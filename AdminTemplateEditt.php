<?php namespace Model\AdminTemplateEditt;

use Model\Core\Autoloader;
use Model\Core\Module;
use Model\Form\Form;

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
		if ($this->model->moduleExists('CkEditor'))
			$this->model->load('CkEditor');
		if ($this->model->moduleExists('InstantSearch'))
			$this->model->load('InstantSearch');
		if ($this->model->moduleExists('Dashboard'))
			$this->model->load('Dashboard');

		$this->model->addCSS('model/AdminTemplateEditt/assets/css/basics.css', ['head' => true]);

		if ($this->model->isLoaded('Multilang') and isset($_COOKIE['admin-lang']))
			$this->model->_Multilang->setLang($_COOKIE['admin-lang']);

		if (!isset($this->model->_AdminFront->request[1]) and isset($this->model->_AdminFront->request[0], $_COOKIE['model-admin-' . $this->model->_AdminFront->request[0] . '-searchFields'])) { // List request
			$_REQUEST['search-columns'] = $_COOKIE['model-admin-' . $this->model->_AdminFront->request[0] . '-searchFields'];
		}
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
			if (substr($url, 0, 4) === 'http') {
				if ($excludeExternal)
					return false;
				return $url;
			} else {
				return PATH . $url;
			}
		}, array_merge($this->model->_Output->getJsList(true), $this->model->_Output->getCSSList(true))))), [
			PATH . 'model/AdminTemplateEditt/assets/css/basics.css',
			PATH . 'model/AdminTemplateEditt/assets/css/menu.css',
			PATH . 'model/AdminTemplateEditt/assets/css/style.css',
		]);
	}

	/**
	 * Renders a group of tabs
	 *
	 * @param string $name
	 * @param array $tabs
	 * @param array $options
	 */
	public function renderTabs(string $name, array $tabs, array $options = [])
	{
		$options = array_merge([
			'before' => false,
			'after' => false,
			'default' => null,
		], $options);

		$totK = $this->model->_AdminFront->request[0] . '-' . $this->model->element['id'] . '-' . $name;

		if ($options['default'] !== null and !isset($tabs[$options['default']]))
			$options['default'] = null;

		echo '<div class="admin-tabs" data-tabs="' . entities($totK) . '" data-name="' . entities($name) . '"' . ($options['default'] !== null ? ' data-default="' . entities($options['default']) . '"' : '') . '>';
		if ($options['before'])
			echo '<div>' . $options['before'] . '</div>';
		foreach ($tabs as $k => $t) {
			if (!is_array($t))
				$t = ['label' => $t];
			$t = array_merge([
				'label' => '',
				'onclick' => '',
			], $t);

			echo '<a class="admin-tab" data-tab="' . entities($k) . '" data-onclick="' . ($t['onclick'] ? entities($t['onclick']) . ';' : '') . '">' . entities($t['label']) . '</a>';
		}
		if ($options['after'])
			echo '<div>' . $options['after'] . '</div>';
		echo '</div>';
	}
}
