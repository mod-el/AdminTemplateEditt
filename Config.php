<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module_Config;

class Config extends Module_Config {
	public $configurable = false;

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
