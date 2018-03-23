<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module_Config;

class Config extends Module_Config
{
	public $configurable = false;

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function makeCache(): bool
	{
		$adminConfig = new \Model\AdminFront\Config($this->model);

		$adminConfig->checkAndInsertWords([
			'logout' => [
				'it' => 'Log out',
				'en' => 'Log out',
			],
			'filters-close' => [
				'it' => 'Chiudi',
				'en' => 'Close',
			],
			'filters-manage' => [
				'it' => 'Gestisci',
				'en' => 'Manage',
			],
			'filters-manage-main' => [
				'it' => 'Gestisci campo generico',
				'en' => 'Manage main search',
			],
			'filters-reset' => [
				'it' => 'Reimposta',
				'en' => 'Reset',
			],
		]);

		return true;
	}
}
