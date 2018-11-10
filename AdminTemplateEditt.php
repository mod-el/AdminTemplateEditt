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
		if ($this->model->moduleExists('DraggableOrder'))
			$this->model->load('DraggableOrder');
		if ($this->model->moduleExists('Dashboard'))
			$this->model->load('Dashboard');

		$this->model->load('Popup');
		$this->model->load('Form');
		$this->model->load('ContextMenu');
		$this->model->load('CSRF');

		if ($this->model->isLoaded('Multilang') and isset($_COOKIE['admin-lang']))
			$this->model->_Multilang->setLang($_COOKIE['admin-lang']);

		if (!isset($this->model->_AdminFront->request[1]) and isset($this->model->_AdminFront->request[0], $_COOKIE['model-admin-' . $this->model->_AdminFront->request[0] . '-searchFields'])) { // List request
			$_REQUEST['search-columns'] = $_COOKIE['model-admin-' . $this->model->_AdminFront->request[0] . '-searchFields'];
		}
	}

	/**
	 * Returns a list of the used assets (javascript and stylesheets) for Service Worker caching
	 *
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
			PATH . 'model/AdminTemplateEditt/files/basics.css',
			PATH . 'model/AdminTemplateEditt/files/menu.css',
			PATH . 'model/AdminTemplateEditt/files/style.css',
		]);
	}

	/**
	 * Renders the menu
	 *
	 * @param array $pages
	 */
	public function renderMenu(array $pages)
	{
		foreach ($pages as $pIdx => $p) {
			if ($p['hidden'] ?? false)
				continue;
			if (($p['page'] ?? null) and !$this->model->_Admin->canUser('L', $p['page']))
				continue;

			if (isset($p['rule'])) {
				if (($p['direct'] ?? null) and is_numeric($p['direct'])) {
					$link = $this->model->_AdminFront->getUrlPrefix() . $p['rule'] . '/edit/' . $p['direct'];
					$onclick = 'loadElement(\'' . $p['rule'] . '\', ' . $p['direct'] . '); return false';
				} else {
					$link = $this->model->_AdminFront->getUrlPrefix() . $p['rule'];
					$onclick = 'loadAdminPage([\'' . $p['rule'] . '\']); return false';
				}
			} else {
				$link = '#';
				$onclick = 'switchMenuGroup(\'' . $pIdx . '\'); return false';
			}
			?>
			<a href="<?= $link ?>" class="main-menu-tasto" id="menu-group-<?= $pIdx ?>" onclick="<?= $onclick ?>" data-menu-id="<?= $pIdx ?>">
				<span class="cont-testo-menu"><?= entities($p['name']) ?></span> </a>
			<?php
			if (isset($p['sub']) and $p['sub']) {
				?>
				<div class="main-menu-cont expandible" id="menu-group-<?= $pIdx ?>-cont" style="height: 0px" data-menu-id="<?= $pIdx ?>">
					<div>
						<?php
						$this->renderMenuItems($pIdx, $p['sub']);
						?>
					</div>
				</div>
				<?php
			}
		}
	}

	/**
	 * Recursively renders a left menu items list
	 *
	 * @param string $parent
	 * @param array $items
	 * @param int $lvl
	 */
	protected function renderMenuItems(string $parent, array $items, int $lvl = 1)
	{
		foreach ($items as $pIdx => $p) {
			if (isset($p['rule'])) {
				if (($p['direct'] ?? null) and is_numeric($p['direct'])) {
					$link = $this->model->_AdminFront->getUrlPrefix() . $p['rule'] . '/edit/' . $p['direct'];
					$onclick = 'loadElement(\'' . $p['rule'] . '\', ' . $p['direct'] . '); return false';
				} else {
					$link = $this->model->_AdminFront->getUrlPrefix() . $p['rule'];
					$onclick = 'loadAdminPage([\'' . $p['rule'] . '\']); return false';
				}
			} else {
				$link = '#';
				$onclick = 'switchMenuGroup(\'' . $parent . '-' . $pIdx . '\'); return false';
			}
			?>
			<a href="<?= $link ?>" class="main-menu-sub" id="menu-group-<?= $parent ?>-<?= $pIdx ?>" onclick="<?= $onclick ?>" data-menu-id="<?= $parent . '-' . $pIdx ?>"<?= ($p['hidden'] ?? false) ? ' style="display: none"' : '' ?>>
				<img src="<?= PATH ?>model/<?= $this->getClass() ?>/files/img/page.png" alt=""/>
				<span class="cont-testo-menu"><?= entities($p['name']) ?></span> </a>
			<?php
			if (isset($p['sub']) and $p['sub']) {
				?>
				<div class="main-menu-cont expandible" id="menu-group-<?= $parent ?>-<?= $pIdx ?>-cont" style="height: 0; padding-left: <?= (15 * $lvl) ?>px" data-menu-id="<?= $parent . '-' . $pIdx ?>">
					<div>
						<?php
						$this->renderMenuItems($parent . '-' . $pIdx, $p['sub'], $lvl + 1);
						?>
					</div>
				</div>
				<?php
			}
		}
	}

	/**
	 * Method called via Ajax
	 *
	 * Shows the filters picking template, and saves them if necessary
	 *
	 * @return array
	 * @throws \Model\Core\Exception
	 */
	public function pickFilters(): array
	{
		if ($this->model->_CSRF->checkCsrf()) {
			$filtersPost = json_decode($_POST['filters'], true);
			if ($filtersPost === null)
				die('Errore JSON');

			$filtersArr = [];
			foreach ($filtersPost as $f => $v) {
				$k = substr($f, -4);
				$f = substr($f, 0, -5);
				$filtersArr[$f][$k] = $v;
			}

			$filters = [
				'top' => [],
				'filters' => [],
			];

			foreach ($filtersArr as $f => $fOpt) {
				if (!isset($fOpt['type'], $fOpt['form']))
					continue;
				$filters[$fOpt['form']][$f] = $fOpt['type'];
			}

			foreach ($filters as $form => $fArr) {
				setcookie('model-admin-' . $this->model->_AdminFront->request[0] . '-filters-' . $form, json_encode($fArr), time() + (60 * 60 * 24 * 365), $this->model->prefix() . ($this->model->_AdminFront->url ? $this->model->_AdminFront->url . '/' : ''));
			}
			die('ok');
		}
		return [
			'template-module' => $this->getClass(),
			'template-module-layout' => $this->getClass(),
			'showLayout' => false,
			'template' => 'pick-filters',
			'cacheTemplate' => false,
		];
	}

	/**
	 * Method called via Ajax
	 *
	 * Shows the search fields picking template, and saves them if necessary
	 *
	 * @return array
	 * @throws \Model\Core\Exception
	 */
	public function pickSearchFields(): array
	{
		if ($this->model->_CSRF->checkCsrf()) {
			setcookie('model-admin-' . $this->model->_AdminFront->request[0] . '-searchFields', json_encode(explode(',', $_POST['fields'])), time() + (60 * 60 * 24 * 365), $this->model->prefix() . ($this->model->_AdminFront->url ? $this->model->_AdminFront->url . '/' : ''));
			die('ok');
		}
		return [
			'template-module' => $this->getClass(),
			'template-module-layout' => $this->getClass(),
			'showLayout' => false,
			'template' => 'pick-search-fields',
			'cacheTemplate' => false,
		];
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
