<?php

class ControllerExtensionPaymentPayapi extends Controller
{

	private $error    = array();
	private $brand    = 'payapi';
	private $settings = false;
	private $data     = array();
	private $valid    = false;
	private $staging  = false;

	public function index()
	{
		$this->language->load('extension/payment/payapi');
		$this->load->model('extension/payment/payapi');
		$this->settings = $this->model_extension_payment_payapi->settings();
		if (isset($this->settings['staging']) !== true || $this->settings['staging'] !== true) {
			$this->staging = false;
		} else {
			$this->staging = true;
		}
		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->branding();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			//-> TODEVELOP: do not record settings
			//->$this->model_setting_setting->editSetting('payapi', $this->request->post);
			$this->data['success'] = $this->language->get('text_success');
		} else {
			$this->data['success'] = false;
		}
		//var_dump($this->data['branding']); exit;
		$this->wording();
		//->var_dump($this->data); exit;
		$this->breadcrumbs();
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
			if($this->request->post['payapi_staging'] === 1) {
				$staging = true;
			} else {
				$staging = false;
			}
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

	private function values()
	{
		if ($this->valid === true) {

		}
		//->
		if ($this->valid === true && isset($this->request->post['payapi_public_id']) === true) {
			$this->data['payapi_public_id'] = $this->request->post['payapi_public_id'];
		} else if ($this->config->has('payapi_public_id') === true) {
			$this->data['payapi_public_id'] = $this->config->get('payapi_public_id');
		} else {
			$this->data['payapi_public_id'] = null;
		}
		if ($this->valid === true && isset($this->request->post['payapi_api_key']) === true) {
			$this->data['payapi_api_key'] = $this->request->post['payapi_api_key'];
		} else if ($this->config->has('payapi_api_key') === true) {
			$this->data['payapi_api_key'] = $this->config->get('payapi_api_key');
		} else {
			$this->data['payapi_api_key'] = null;
		}

	}

	private function breadcrumbs()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);
		$breadcrumbs[] = array(
			'text' => $this->data['text_payments'],
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);
		$breadcrumbs[] = array(
			'text' => $this->data['heading_title'],
			'href' => $this->url->link('extension/payment/payapi', 'token=' . $this->session->data['token'], true)
		);
	}

	private function wording()
	{
		$wording = array(
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
			"status_processing",
			"status_success",
			"status_canceled",
			"status_failed",
			"status_chargeback",
			"label_public_id",
			"label_api_key",
			"label_shipping",
			"label_mode",
			"label_instant_payments",
			"label_debug",
			"label_order",
			"label_processing_status",
			"label_processed_status",
			"",
			"",
			"select_default",
			"select_shipping",
			"select_mode",
			"input_selected",
			"input_disabled",
			"mode_1",
			"mode_2",
			"mode_3",
			"help_public_id",
			"help_api_key",
			"help_instantpayments",
			"help_verified",
			"help_unverified"
		);
		foreach($wording as $key => $word) {
			$this->data[$word] = $this->language->get($word);
		}
	}

	private function branding()
	{
		$branding = $this->payapiSdk->branding($this->brand);
		if (isset($branding['code']) === true && isset($branding['data']) === true && $branding['code'] ===200) {
			$this->data['branding'] = $branding['data'];
			$this->data['branding']['dashboard'] = 'http://input.payapi.io/';
			return true;
		}
		return false;
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