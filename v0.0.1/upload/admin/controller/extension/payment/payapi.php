<?php

class ControllerExtensionPaymentPayapi extends Controller
{

	private $error    = array();
	private $brand    = 'payapi';
	private $settings = false;
	private $data     = array();
	private $valid    = false;
	private $staging  = false;
	private $values   = array(
		"payapi_public_id",
		"payapi_api_key",
		"payapi_shipping",
		"payapi_shipping",
		"payapi_instantpayments",
		"payapi_mode",
		"payapi_debug",
		"payapi_order",
		"payapi_processing_status_id",
		"payapi_processed_status_id",
		"payapi_canceled_status_id",
		"payapi_failed_status_id",
		"payapi_chargeback_status_id"
	);
	private $statuses  = array(
		"processing" =>  2,
		"processed"  =>  1,
		"failed"     => 10,
		"chargeback" => 13
	);

	public function index()
	{
		$this->language->load('extension/payment/payapi');
		$this->load->model('extension/payment/payapi');
		$this->settings = $this->model_extension_payment_payapi->settings();
		/*
		@TODO NOTE: settings should be called just when demo mode is not active (staging/production)
		 */
		if (isset($this->settings['staging']) !== true || $this->settings['staging'] !== true) {
			if (isset($this->request->post['payapi_mode']) === true && $this->request->post['payapi_mode'] != 0) {
				$staging = true; 
			} else {
				$this->staging = false;
			}
			
		} else {
			$this->staging = true;
		}
		$this->branding();
		$this->load->model('localisation/order_status');
		$this->load->model('extension/extension');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			//-> TODEVELOP: do not record settings
			//->$this->model_setting_setting->editSetting('payapi', $this->request->post);
			$this->data['success'] = $this->language->get('text_success');
		} else {
			$this->data['success'] = false;
		}
		$this->errors();
		$this->wording();
		$this->values();
		$this->status();
		$this->breadcrumbs();
		//-> @NOTE if not available this gets NULL
		$this->data['shippings'] = $this->model_extension_extension->getInstalled('shipping');
		//-> action(s)
		$this->data['action'] = $this->url->link('extension/payment/payapi', 'token=' . $this->session->data['token'], true);
		$this->data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);
		//-> 
		if($this->valid === true) {
			$this->data['account_status'] = $this->language->get('text_verified');
			$this->data['account_status_class'] = 'success';
			$this->data['account_status_tooltip'] = $this->language->get('account_verified');
		} else {
			$this->data['account_status'] = $this->language->get('text_unverified');
			$this->data['account_status_class'] = 'warning';
			$this->data['account_status_tooltip'] = $this->language->get('account_unverified');
		}
		//-> webpage blocks
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		//->var_dump(get_declared_classes()); exit;
		$this->response->setOutput($this->load->view('extension/payment/payapi', $this->data));
	}

	private function validate()
	{
		if (isset($this->request->post['payapi_staging']) !== true) {
			$this->error['warning'] = $this->language->get('error_default');
		} else {
			if($this->request->post['payapi_mode'] === 1) {
				$staging = true;
			} else {
				$staging = false;
			}
			// @NOTE demo mode is mode3, do not seetings if demo
			if (isset($this->request->post['payapi_public_id']) !== true || isset($this->request->post['payapi_api_key']) !== true || is_string($this->request->post['payapi_public_id']) !== true || is_string($this->request->post['payapi_api_key']) !== true) {
				$this->error['account'] = $this->language->get('error_account');
			}
		}
		if (!$this->error) {
			$settings = $this->payapiSdk($this->request->post['payapi_public_id'], $this->request->post['payapi_api_key'], $staging);
			if (isset($settings['code']) !== true) {
				$this->error['warning'] = $this->language->get('error_default');
			} else if ($settings['code'] !== 200) {
				if ($settings['code'] === 400 ) {
					$this->error['account'] = $this->language->get('error_account');
				} else {
					$this->error['account'] = $this->language->get('error_default');
				}
			} else {
				$this->settings = $settings;
				$this->valid = true;
			}
		}
		return !$this->error; 
	}

	private function errors()
	{
		$errors = array(
			"warning",
			"error_account"
		);
		foreach($errors as $key => $error) {
			if (isset($this->error[$error]) === true) {
				$this->data[$error] = $this->error['warning'];
			} else {
				$this->data[$error] = false;
			}
		}
	}

	private function values()
	{
		foreach($this->values as $key => $value) {
			if ($this->valid === true) {
				$this->data[$value] = $this->request->post[$value];
			} else if (is_array($this->settings) !== true){
				$this->data[$value] = $this->config->get($value);
			} else {
				$this->data[$value] = null;
			}
		}
	}

	private function breadcrumbs()
	{
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->data['text_extensions'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->data['text_payments'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->data['heading_title'],
			'href' => $this->url->link('extension/payment/payapi', 'token=' . $this->session->data['token'], true)
		);
		return $this->data['breadcrumbs'];
	}

	private function wording()
	{
		foreach (array(
			"heading_title",
			"tab_general",
			"tab_status",
			"text_payment",
			"text_payments",
			"text_account_status",
			"text_enabled",
			"text_disabled",
			"text_edit",
			"tab_general",
			"tab_status",
			"text_phone",
			"text_email",
			"text_dashboard",
			"text_yes",
			"text_no",
			"text_extensions",
			"label_public_id",
			"label_api_key",
			"label_shipping",
			"label_mode",
			"label_instant_payments",
			"label_debug",
			"label_account_status",
			"label_order",
			"label_status_processing",
			"label_status_processed",
			"label_status_failed",
			"label_status_chargeback",
			"label_status",
			"label_sort_order",
			"select_default",
			"select_shipping",
			"select_mode",
			"input_selected",
			"input_disabled",
			'button_update',
			'button_save',
			'button_cancel',
			"mode_1",
			"mode_2",
			"mode_3",
			"help_public_id",
			"help_api_key",
			"help_shipping",
			"help_debug",
			"help_instantpayments",
			"help_verified",
			"help_unverified",
			"help_account_status"
		) as $key => $word) {
			$this->data[$word] = $this->language->get($word);
		}
	}

	private function branding()
	{
		//$branding = $this->payapiSdk->branding($this->brand);
		$branding = $this->model_extension_payment_payapi->branding();
		if (is_array($branding) === true) {
			$this->data['branding'] = $branding;
			if ($this->staging === false) {
				$this->data['branding']['dashboard'] = $this->branding['partnerBackoffice']['production'];
			} else {
				$this->data['branding']['dashboard'] = $this->branding['partnerBackoffice']['staging'];
			}
			return true;
		}
		return false;
	}

	private function status()
	{
		foreach($this->statuses as $status => $defaultId) {
			$flag = 'payapi_' . $status . '_status_id';
			if ($this->valid === true) {
				$this->data[$flag] = $this->request->post[$flag];
			} else if (is_array($this->settings) !== true){
				$this->data[$flag] = $this->config->get($flag);
			} else {
				$this->data[$flag] = $defaultId;
			}
		}
	}

	private function dependencies()
	{
		if (class_exist('Firebase\JWT\JWT') === true) {
			return true;
		} 
		return false;
	}

	protected function log($info)
	{
		return $this->model_extension_payment_payapi->log($info);
	}



}