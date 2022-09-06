<?php namespace Model\AdminTemplateEditt;

use Model\Multilang\MultilangProviderInterface;

class MultilangProvider implements MultilangProviderInterface
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
