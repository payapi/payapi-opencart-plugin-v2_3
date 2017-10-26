<?php
class ModelExtensionPaymentPayapi extends Model {

	private $vqmod    = 'payapi';
	private $brand    = 'payapi';

	public function install()
	{
		$this->enable();
	}

	public function uninstall()
	{
		$this->disable();
	}

	private function enable()
	{
		if (is_file($this->vqmod() . '_') === true) {
			rename ($this->vqmod() . '_', $this->vqmod());
			return is_file($this->vqmod());
		}
		return false;
	}

	private function disable()
	{
		if (is_file($this->vqmod()) === true) {
			rename ($this->vqmod(), $this->vqmod() . '_');
			return is_file($this->vqmod() . '_');
		}
		return false;
	}

	private function vqmod()
	{
		return str_replace('system', 'vqmod', DIR_SYSTEM) . 'xml' . DIRECTORY_SEPARATOR . $this->vqmod . $this->vqmod . '.' . 'xml';
	}

	public function branding()
	{
		$branding = $this->checkResponse($this->payapiSdk->branding($this->brand));
		return $branding;
	}

	public function settings()
	{
		return $this->checkResponse($this->payapiSdk->settings());
	}

	public function info()
	{
		return $this->checkResponse($this->payapiSdk->info());
	}

	private function checkResponse($response)
	{
		if (isset($response['code']) === true) {
			if ($response['code'] === 200 && isset ($response['data']) === true) {
				return $response['data'];
			} else {
				$code = $response['code'];
				if(isset($response['error']) === true) {
					$error = $response['error'];
				} else {
					$error = 'undefined';
				}
			}
		} else{
			$code = 'undefined';
			$error = 'undefined';
		}
		$this->log('[error][' . $code . '] ' . $error);
		return false;
	}

	private function defaultSettings()
	{
		return array(
			"payapi_public_id" => null,
			"payapi_api_key"   => null,
			"payapi_demo"      => 1,
			"payapi_test"      => 1,
			"payapi_enabled"   => 1
		);
	}

	public function log($info)
	{
		if ($this->config->has('payapi_debug') !== true || $this->config->get('payapi_debug') !== 1){
			return true;
		} 
		if ($this->log === false) {
			$this->log = new log('payapi' . '.' . date('Ymd', time()));
		}
		return $this->log->add($info);
	}


}