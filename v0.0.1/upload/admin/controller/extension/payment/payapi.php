<?php

class ControllerExtensionPaymentPayapi extends Controller
{

	private $error = array();
	private $brand = 'payapi';
	private $settings = false;
	private $data = array();
	private $valid = false;

	public function index()
	{
		$this->language->load('extension/payment/payapi');
		$this->load->model('extension/payment/payapi');
		$this->settings = $this->model_extension_payment_payapi->settings();

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->wording();
		$this->breadcrumbs();
		//var_dump(get_declared_classes()); exit;
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
			"help_public_id",
			"help_api_key",
			"help_verified",
			"help_unverified"
		);
		foreach($wording as $key => $word) {
			$this->data[$word] = $this->language->get($word);
		}
	}

	private function branding()
	{
		return $this->payapaiSdk->branding($this->brand);
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