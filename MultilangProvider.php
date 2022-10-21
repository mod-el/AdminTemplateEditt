<?php namespace Model\AdminTemplateEditt;

use Model\Multilang\AbstractMultilangProvider;

class MultilangProvider extends AbstractMultilangProvider
{
	public static function dictionary(): array
	{
		return [
			'admin' => [
				'accessLevel' => 'root',
				'words' => require INCLUDE_PATH . 'model' . DIRECTORY_SEPARATOR . 'AdminFront' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'dictionary.php',
			],
		];
	}
}
