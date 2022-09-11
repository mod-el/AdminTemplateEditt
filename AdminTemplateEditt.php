<?php namespace Model\AdminTemplateEditt;

use Model\Core\Module;

class AdminTemplateEditt extends Module
{
	/**
	 * @param array $options
	 * @throws \Exception
	 */
	public function init(array $options)
	{
		$this->model->load('FrontEnd');

		if ($this->model->moduleExists('DatePicker'))
			$this->model->load('DatePicker');

		// TODO
//		$this->model->addCSS('model/AdminTemplateEditt/assets/css/basics.css', ['head' => true]);
	}

	/**
	 * Returns a list of the used assets (javascript and stylesheets) for Service Worker caching
	 *
	 * @param bool $excludeExternal
	 * @return array
	 */
	public function getAssetsForServiceWorker(bool $excludeExternal = true): array
	{
		return array_merge(array_values(array_filter(array_map(function ($url) use ($excludeExternal) {
			if (str_starts_with($url, 'http')) {
				if ($excludeExternal)
					return false;
				return $url;
			} else {
				return PATH . $url;
			}
		}, array_keys(\Model\Assets\Assets::getList([], true))))), [
			PATH . 'model/AdminTemplateEditt/assets/css/basics.css',
			PATH . 'model/AdminTemplateEditt/assets/css/style.css',
		]);
	}
}
