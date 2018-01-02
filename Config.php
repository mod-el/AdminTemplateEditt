<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module_Config;

class Config extends Module_Config {
	public $configurable = false;

	/**
	 * @return bool
	 * @throws \Model\Core\Exception
	 */
	public function makeCache(){
		if($this->model->moduleExists('WebAppManifest')){
			$adminConfig = new \Model\Admin\Config($this->model);
			$adminRules = $adminConfig->getRules();
			foreach($adminRules['rules'] as $rule){
				if($rule)
					$rule .= '/';

				$manifestData = [
					'name' => APP_NAME,
					'theme_color' => '#383837',
					'background_color' => '#f2f2f2',
				];

				$currentManifest = $this->model->_WebAppManifest->getManifest($rule.'manifest.json');
				if($currentManifest)
					$manifestData = array_merge($manifestData, $currentManifest);

				$manifestData['start_url'] = PATH.$rule;

				$this->model->_WebAppManifest->setManifest($rule.'manifest.json', $manifestData);

				$iconsFolder = str_replace(['/', '\\'], '-', $rule.'manifest.json');
				$iconFormats = ['32', '192', '512'];
				foreach($iconFormats as $format){
					$iconPath = INCLUDE_PATH.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'WebAppManifest'.DIRECTORY_SEPARATOR.'icons'.DIRECTORY_SEPARATOR.$iconsFolder.DIRECTORY_SEPARATOR.$format.'.png';
					if(!file_exists($iconPath))
						copy(__DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'icons'.DIRECTORY_SEPARATOR.$format.'.png', $iconPath);
				}
			}
		}

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
			$rules[] = ($rule ? $rule.'/' : $rule).'sw.js';
		}

		return [
			'rules' => $rules,
			'controllers' => [
				'AdminServiceWorker',
			],
		];
	}
}
