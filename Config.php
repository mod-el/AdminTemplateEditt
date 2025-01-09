<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module_Config;

class Config extends Module_Config
{
	public bool $configurable = true;

	/**
	 * @throws \Model\Core\Exception
	 */
	protected function assetsList(): void
	{
		$this->addAsset('config', 'config.php', function () {
			return '<?php
$config = ' . var_export([
					'background-header' => '#333333',
					'text-header' => '#ffffff',
					'background-menu-primary-off' => '#ffffff',
					'background-menu-primary-on' => '#71af71',
					'text-menu-primary-off' => '#000000',
					'text-menu-primary-on' => '#ffffff',
					'background-menu-secondary-off' => '#f2f2f2',
					'background-menu-secondary-on' => '#e5ebe5',
					'text-menu-secondary-off' => '#000000',
					'text-menu-secondary-on' => '#000000',
				], true) . ";\n";
		});
	}

	/**
	 * Returns the config template
	 *
	 * @param string $type
	 * @return string|null
	 */
	public function getTemplate(string $type): ?string
	{
		return $type === 'config' ? 'config' : null;
	}

	/**
	 * @return array
	 */
	public function retrieveConfig(): array
	{
		$config = parent::retrieveConfig();
		return array_merge([
			'background-header' => '#333333',
			'text-header' => '#ffffff',
			'background-menu-primary-off' => '#ffffff',
			'background-menu-primary-on' => '#71af71',
			'text-menu-primary-off' => '#000000',
			'text-menu-primary-on' => '#ffffff',
			'background-menu-secondary-off' => '#f2f2f2',
			'background-menu-secondary-on' => '#e5ebe5',
			'text-menu-secondary-off' => '#000000',
			'text-menu-secondary-on' => '#000000',
		], $config);
	}

	public function getConfigData(): ?array
	{
		return [];
	}
}
