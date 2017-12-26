<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module_Config;

class Config extends Module_Config {
	public $configurable = false;

	/**
	 * @return bool
	 * @throws \Model\Core\Exception
	 */
	public function makeCache(){
		$assets = $this->model->_AdminTemplateEditt->getAssets();

		$assets[] = PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'header.php';
		$assets[] = PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'footer.php';

		$md5 = [];
		foreach($assets as $asset){
			if(!file_exists(PATHBASE.$asset))
				$this->model->error('One of the assets file defined by admin template dependencies does not exist!');
			$md5[] = md5(file_get_contents(PATHBASE.$asset));
		}

		return (bool) file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache-key.php', "<?php\n\$cacheKey = '".md5(implode('', $md5))."';");
	}

	/**
	 * Rules for API actions
	 *
	 * @return array
	 */
	public function getRules(){
		$adminConfig = new \Model\Admin\Config($this->model);

		$rules = [];
		$adminRules = $adminConfig->getRules();
		foreach($adminRules['rules'] as $rule){
			$rules[] = $rule.'/sw.js';
		}

		return [
			'rules' => $rules,
			'controllers' => [
				'AdminServiceWorker',
			],
		];
	}
}
