<?php
class ModelExtensionPaymentPayapi extends Model {

	private $vqmod    = 'payapi';

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

	public function settings()
	{
		$settings = $this->payapiSdk->settings();
		if(isset($settings['code']) === true && isset($settings['data']) === true && $settings['code'] === 200) {
			return $settings['data'];
		} 
		return false;
	}

	public function info()
	{
		return $this->payapiSdk->info();
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