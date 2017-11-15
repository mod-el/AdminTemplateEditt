<?php
namespace Model;

class AdminTemplateEditt extends Module {
	/**
	 * @param mixed $options
	 */
	public function init($options){
		if($this->model->moduleExists('DatePicker'))
			$this->model->load('DatePicker');
		if($this->model->moduleExists('CkEditor'))
			$this->model->load('CkEditor');
		if($this->model->moduleExists('InstantSearch'))
			$this->model->load('InstantSearch');

		$this->model->load('FrontEnd');
		$this->model->load('Popup');
		$this->model->load('Form');
		$this->model->load('ContextMenu');

		if(!isset($this->model->_Admin->request[1]) and isset($this->model->_Admin->request[0], $_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-searchFields'])){ // List request
			$_REQUEST['search-columns'] = $_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-searchFields'];
		}
	}

	/**
	 * @param array $config
	 * @return array
	 */
	public function getViewOptions(array $config){
		if(isset($this->model->_Admin->request[0]) and $this->model->_Admin->request[0]=='login'){
			return [
				'showLayout' => false,
				'template' => INCLUDE_PATH.'model/'.$this->getClass().'/templates/login',
			];
		}else{
			$options = [
				'header' => [INCLUDE_PATH.'model/'.$this->getClass().'/templates/header'],
				'footer' => [INCLUDE_PATH.'model/'.$this->getClass().'/templates/footer'],
				'template' => null,
			];

			if(isset($_GET['ajax']))
				$options['showLayout'] = false;

			if(isset($this->model->_Admin->request[1])){
				switch($this->model->_Admin->request[1]){
					case 'edit':
						if(isset($_GET['getData'])){
							$arr = $this->model->_Admin->getEditArray();
							$this->model->sendJSON($arr);
						}else{
							$dir = $this->model->_Admin->url ? $this->model->_Admin->url.'/' : '';

							if($this->model->element){
								$this->model->_Admin->form->reset();

								if(file_exists(INCLUDE_PATH.'app/templates/'.$dir.$this->model->_Admin->request[0].'.php'))
									$options['template'] = $dir.$this->model->_Admin->request[0];
								else
									$options['template'] = INCLUDE_PATH.'model/'.$this->getClass().'/templates/form-template';

								$options['cacheTemplate'] = false;

								if(isset($_GET['ajax'])){
									$options['showLayout'] = true;
									$options['header'] = [INCLUDE_PATH.'model/'.$this->getClass().'/templates/form-header'];
									$options['footer'] = [INCLUDE_PATH.'model/'.$this->getClass().'/templates/form-footer'];
								}else{
									$options['header'][] = INCLUDE_PATH.'model/'.$this->getClass().'/templates/form-header';
									array_unshift($options['footer'], INCLUDE_PATH.'model/'.$this->getClass().'/templates/form-footer');
								}

								if(isset($_GET['duplicated']))
									$options['messages'] = ['Succesfully duplicated!'];
							}

							if(isset($this->model->_Admin->request[3])){
								$options['showLayout'] = false;
								$options['template'] = $dir.$this->model->_Admin->request[0].'/'.$this->model->_Admin->request[3];
							}
						}
						break;
				}
			}

			return $options;
		}
	}

	/**
	 * Recursively renders a left menu items list
	 *
	 * @param int $parent
	 * @param array $items
	 * @param int $lvl
	 */
	public function renderMenuItems($parent, array $items, $lvl=1){
		foreach($items as $pIdx => $p){
			if(isset($p['rule'])){
				$link = $this->model->_Admin->getUrlPrefix().$p['rule'];
				$onclick = 'loadAdminPage([\''.$p['rule'].'\']); return false';
			}else{
				$link = '#';
				$onclick = 'switchMenuGroup(\''.$parent.'-'.$pIdx.'\'); return false';
			}
			?>
            <a href="<?=$link?>" class="main-menu-sub" id="menu-group-<?=$parent?>-<?=$pIdx?>" onclick="<?=$onclick?>" data-menu-id="<?=$parent.'-'.$pIdx?>">
                <img src="<?=PATH?>model/<?=$this->getClass()?>/files/img/page.png" alt="" />
                <span class="cont-testo-menu"><?=entities($p['name'])?></span>
            </a>
			<?php
			if(isset($p['sub']) and $p['sub']){
				?>
                <div class="main-menu-cont expandible" id="menu-group-<?=$parent?>-<?=$pIdx?>-cont" style="height: 0px; padding-left: <?=(15*$lvl)?>px" data-menu-id="<?=$parent.'-'.$pIdx?>">
                    <div>
						<?php
						$this->renderMenuItems($parent.'-'.$pIdx, $p['sub'], $lvl+1);
						?>
                    </div>
                </div>
				<?php
			}
		}
	}

	/**
	 * Called by AdminController, should send the correct data to the template for rendering content appropriate to the request
	 * If returns an array, this will be merged into the viewOptions (so it can return a template, for instance)
	 *
	 * @param string $request
	 * @param array $data
	 * @return array
	 */
	public function respond($request, array $data){
		switch($request[1]){
			case '':
				$this->loadResizeModule($data['columns']);

				$backgroundRule = isset($this->model->_Admin->options['background']) ? $this->model->_Admin->options['background'] : false;
				$colorRule = isset($this->model->_Admin->options['color']) ? $this->model->_Admin->options['color'] : false;

				foreach($data['elements'] as &$el){
					if(!is_string($backgroundRule) and is_callable($backgroundRule)){
						$el['background'] = call_user_func($backgroundRule, $el['element']);
					}else{
						$el['background'] = $backgroundRule;
					}
					foreach($el['columns'] as $column_id => $c){
						if(isset($data['columns'][$column_id]['background']) and $data['columns'][$column_id]['background']){
							if(!is_string($data['columns'][$column_id]['background']) and is_callable($data['columns'][$column_id]['background'])){
								$el['columns'][$column_id]['background'] = call_user_func($data['columns'][$column_id]['background'], $el['element']);
							}else{
								$el['columns'][$column_id]['background'] = $data['columns'][$column_id]['background'];
							}
						}else{
							$el['columns'][$column_id]['background'] = false;
						}
					}

					if(!is_string($colorRule) and is_callable($colorRule)){
						$el['color'] = call_user_func($colorRule, $el['element']);
					}else{
						$el['color'] = $colorRule;
					}
					foreach($el['columns'] as $column_id => $c){
						if(isset($data['columns'][$column_id]['color']) and $data['columns'][$column_id]['color']){
							if(!is_string($data['columns'][$column_id]['color']) and is_callable($data['columns'][$column_id]['color'])){
								$el['columns'][$column_id]['color'] = call_user_func($data['columns'][$column_id]['color'], $el['element']);
							}else{
								$el['columns'][$column_id]['color'] = $data['columns'][$column_id]['color'];
							}
						}else{
							$el['columns'][$column_id]['color'] = false;
						}
					}
				}
				unset($el);

				return [
					'template' => INCLUDE_PATH.'model/'.$this->getClass().'/templates/table',
					'cacheTemplate' => false,
					'data' => $data,
				];
				break;
		}

		return [];
	}

	/**
	 * Loads ResizeTable module with the appropriate options
	 *
	 * @param array $columns
	 * @return bool
	 */
	private function loadResizeModule(array $columns = []){
		if($this->model->isLoaded('ResizeTable'))
			return true;

		$this->model->load('ResizeTable', [
			'table'=>$this->model->_User_Admin->options['table'],
			'page' => $this->model->_Admin->request[0],
			'user' => $this->model->_User_Admin->logged(),
			'columns' => array_keys($columns),
		]);

		$this->model->_ResizeTable->load();

		return true;
	}

	/**
	 * Sends a JSON with the page aids for the current page
	 */
	public function pageAids(){
		$request = $this->model->_Admin->request[0];

		$actions = $this->model->_Admin->getActions([
			$request,
			isset($_GET['action']) ? $_GET['action'] : null,
			isset($_GET['id']) ? $_GET['id'] : null,
		]);

		$parsedActions = [];
		foreach($actions as $actId => $act){
			$action = [
				'id' => $actId,
				'text' => $act['text'],
				'icon' => false,
				'url' => '#',
				'action' => 'return false',
			];

			$iconPath = PATH.'model/'.$this->getClass().'/files/img/toolbar/'.$actId.'.png';
			if(file_exists(PATHBASE.$iconPath))
				$action['icon'] = $iconPath;

			switch($act['action']){
				case 'new':
					$action['action'] = 'newElement(); return false';
					break;
				case 'delete':
					if(!isset($_GET['action'])) {
						$action['action'] = 'deleteRows(); return false';
					}else{
						if(isset($_GET['id']) and $_GET['id'])
							$action['action'] = 'deleteRows(['.$_GET['id'].']); return false';
						else
							continue 2;
					}
					break;
				case 'save':
					$action['action'] = 'save(); return false';
					break;
				case 'duplicate':
					$action['action'] = 'duplicate(); return false';
					break;
			}

			$parsedActions[] = $action;
		}

		if(!isset($_GET['action'])){ // We're in the table page
			$parsedActions[] = [
				'id' => 'filters',
				'text' => 'Filtri',
				'icon' => PATH.'model/'.$this->getClass().'/files/img/toolbar/filters.png',
				'url' => '#',
				'action' => 'switchFiltersForm(this); return false',
			];
		}

		$adminPages = $this->model->_Admin->getPages();
		$breadcrumbs = [
			[
				'name' => 'Home',
				'url' => '',
			],
		];
		$this->searchBreadcrumbs($adminPages, $request, $breadcrumbs);

		$breadcrumbsHtml = [];
		$prefix = $this->model->_Admin->getUrlPrefix();
		foreach($breadcrumbs as $b){
			$breadcrumbsHtml[] = $b['url']!==null ? '<a href="'.$prefix.$b['url'].'" onclick="loadAdminPage([\''.$b['url'].'\']); return false">'.entities($b['name']).'</a>' : '<a>'.entities($b['name']).'</a>';
		}
		$breadcrumbsHtml = implode(' -&gt; ', $breadcrumbsHtml);

		$filterForms = $this->getFiltersForms();

		ob_start();
		$filterForms['top']->render([
			'one-row' => true,
			'labels-as-placeholders' => true,
		]);
		$topFormHtml = ob_get_clean();

		ob_start();
		$filterForms['filters']->render();
		$filtersFormHtml = ob_get_clean();

		$this->model->sendJSON([
			'sId' => $this->model->_Admin->getSessionId(),
			'actions' => $parsedActions,
			'breadcrumbs' => $breadcrumbsHtml,
			'topForm' => $topFormHtml,
			'filtersForm' => $filtersFormHtml,
		]);
	}

	/**
	 * Reconstructs page hierarchy in an array, for breadcrumbs building
	 *
	 * @param array $pages
	 * @param string $request
	 * @param array $breadcrumbs
	 * @return bool
	 */
	private function searchBreadcrumbs(array $pages, $request, array &$breadcrumbs){
		foreach($pages as $p){
			if(isset($p['rule']) and $p['rule']==$request){
				$breadcrumbs[] = [
					'name' => $p['name'],
					'url' => $p['rule'],
				];
				return true;
			}
			if(isset($p['sub'])){
				$temp = $breadcrumbs;
				$temp[] = [
					'name' => $p['name'],
					'url' => isset($p['rule']) ? $p['rule'] : null,
				];
				if($this->searchBreadcrumbs($p['sub'], $request, $temp)){
					$breadcrumbs = $temp;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the filter forms (the top one and the extended one)
	 * If $arr is true, it will not return the Form objects but just an array with the fields
	 *
	 * @param bool $arr
	 * @return array
	 */
	public function getFiltersForms($arr = false){
		$defaults = [
			'top' => [
				'all' => '=',
			],
			'filters' => [],
		];

		$customFilters = $this->model->_Admin->getCustomFiltersForm();
		foreach($customFilters->getDataset() as $k => $f){
			$form = isset($f->options['admin-form']) ? $f->options['admin-form'] : 'filters';

			if(isset($f->options['admin-type'])){
				$defaults[$form][$k] = $f->options['admin-type'];
			}else{
				switch($f->options['type']){
					case 'date':
					case 'time':
					case 'datetime':
						$defaults[$form][$k] = 'range';
						break;
					default:
						$defaults[$form][$k] = '=';
						break;
				}
			}
		}

		$adminListOptions = $this->model->_Admin->getListOptions();

		$forms = [];
		foreach($defaults as $form => $defaultFilters){
			$forms[$form] = $this->getFiltersForm($form, $defaultFilters, $adminListOptions['filters'], $arr);
		}

		return $forms;
	}

	/**
	 * Returns the requested filter form
	 *
	 * @param string $name
	 * @param array $default
	 * @param array $filtersSet
	 * @param bool $arr
	 * @return Form|array
	 */
	private function getFiltersForm($name, array $default = [], array $filtersSet = [], $arr = false){
		$filtersArr = null;
		if(isset($_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-filters-'.$name]))
			$filtersArr = json_decode($_COOKIE['model-admin-'.$this->model->_Admin->request[0].'-filters-'.$name], true);
		if($filtersArr===null)
			$filtersArr = $default;

		if($arr)
			return $filtersArr;

		$form = new Form([
			'table' => $this->model->_Admin->options['table'],
			'model' => $this->model,
		]);

		$customFilters = $this->model->_Admin->getCustomFiltersForm();

		$values = [];
		foreach($filtersSet as $f){
			if(isset($filtersArr[$f[0]])){
				switch(count($f)){
					case 2: // Custom filter
						$values[$f[0]] = $f[1];
						break;
					case 3: // Normal filter
						if($f[1]!=$filtersArr[$f[0]]) // Different operator
							continue 2;
						$values[$f[0]] = $f[2];
						break;
					case 4: // Range filter
						$values[$f[0]] = [$f[1], $f[2]];
						break;
				}
			}
		}

		foreach($filtersArr as $k=>$t){
			if(isset($customFilters[$k])){
				$datum = $form->add($customFilters[$k]);
				$datum->options['attributes']['data-filter'] = isset($datum->options['admin-type']) ? $datum->options['admin-type'] : 'custom';
				$datum->options['attributes']['data-default'] = (string) $datum->options['default'];
				if(isset($values[$k]))
					$datum->setValue($values[$k]);
                elseif($filtersSet) // If at least one filter is set, but not this one, that means the default value was erased by the user
					$datum->setValue(null);
			}else{
				$fieldOptions = [
					'attributes' => [
						'data-filter' => $t,
						'data-default' => '',
					],
					'default' => null,
					'admin-type' => $t,
				];

				if($k==='all'){
					$fieldOptions['label'] = 'Ricerca generale';
					$fieldOptions['attributes']['data-filter'] = 'custom';
				}

				if($t==='range'){

				}else{
					$datum = $form->add($k, $fieldOptions);
					if(isset($values[$k]))
						$datum->setValue($values[$k]);
				}
			}
		}

		return $form;
	}

	/**
	 * Method called via Ajax
	 *
	 * Saves the width of a column, called via an ajax request
	 */
	public function saveWidth(){
		if(checkCsrf() and isset($_GET['k'], $_POST['w']) and is_numeric($_POST['w'])){
			$this->loadResizeModule();
			$this->model->_ResizeTable->set($_GET['k'], $_POST['w']);
		}
		die();
	}

	/**
	 * Method called via Ajax
	 *
	 * Shows the filters picking template, and saves them if necessary
	 *
	 * @return array
	 */
	public function pickFilters(){
		if(checkCsrf()){
			$filtersPost = json_decode($_POST['filters'], true);
			if($filtersPost===null)
				die('Errore JSON');

			$filtersArr = [];
			foreach($filtersPost as $f=>$v){
				$k = substr($f, -4);
				$f = substr($f, 0, -5);
				$filtersArr[$f][$k] = $v;
			}

			$filters = [
				'top' => [],
				'filters' => [],
			];

			foreach($filtersArr as $f=>$fOpt){
				if(!isset($fOpt['type'], $fOpt['form']))
					continue;
				$filters[$fOpt['form']][$f] = $fOpt['type'];
			}

			foreach($filters as $form => $fArr){
				setcookie('model-admin-'.$this->model->_Admin->request[0].'-filters-'.$form, json_encode($fArr), time()+(60*60*24*365), $this->model->prefix().($this->model->_Admin->url ? $this->model->_Admin->url.'/' : ''));
			}
			die('ok');
		}
		return [
			'showLayout' => false,
			'template' => INCLUDE_PATH.'model/'.$this->getClass().'/templates/pick-filters',
			'cacheTemplate' => false,
		];
	}

	/**
	 * Method called via Ajax
	 *
	 * Shows the search fields picking template, and saves them if necessary
	 *
	 * @return array
	 */
	public function pickSearchFields(){
		if(checkCsrf()){
			setcookie('model-admin-'.$this->model->_Admin->request[0].'-searchFields', json_encode(explode(',', $_POST['fields'])), time()+(60*60*24*365), $this->model->prefix().($this->model->_Admin->url ? $this->model->_Admin->url.'/' : ''));
			die('ok');
		}
		return [
			'showLayout' => false,
			'template' => INCLUDE_PATH.'model/'.$this->getClass().'/templates/pick-search-fields',
			'cacheTemplate' => false,
		];
	}

	/**
	 * Renders a sublist
	 * $name has to be a declared children-set of the current element
	 *
	 * @param string $name
	 * @param array $options
	 */
	public function renderSublist($name, array $options = []){
		$options = array_merge([
			'type' => 'row',
			'fields' => [],
			'cont' => $name,
			'class' => 'rob-field-cont sublist-row',
			'template' => null,
			'add' => true,
			'add-inside' => false,
		], $options);

		echo '<div id="cont-ch-'.entities($options['cont']).'" data-rows-class="'.$options['class'].'">';

		$dummy = $this->model->element->create($name, '[n]');
		$form = $this->model->_Admin->getSublistRowForm($dummy, $options);

		if($options['type']=='row'){
			echo '<div class="rob-field-cont">';
			echo '<div class="rob-field" style="width: 5%"></div>';
			echo '<div class="rob-field" style="width: 95%">';
			echo '<div class="rob-field-cont sublist-row">';
			$template = $form->getTemplate(['one-row'=>true]);
			foreach($template as $f){
				echo '<div class="rob-field" style="width: '.$f['w'].'%">'.entities($form[$f['field']]->getLabel()).'</div>';
			}
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		if(!$options['add-inside'])
			echo '</div>';

		if($options['add']){
			if($options['add']===true){
				?>
                <div class="rob-field-cont sublist-row" style="cursor: pointer" onclick="sublistAddRow('<?=entities($name)?>', '<?=entities($options['cont'])?>')">
                    <div class="rob-field" style="width: 5%"></div>
                    <div class="rob-field" style="width: 95%">
                        <img src="<?=PATH?>model/<?=$this->getClass()?>/files/img/toolbar/new.png" alt="" /> Aggiungi
                    </div>
                </div>
				<?php
			}else{
				echo $options['add'];
			}
		}

		if($options['add-inside'])
			echo '</div>';
		?>
        <div id="sublist-template-<?=entities($options['cont'])?>" class="sublist-template">
			<?php
			if(($options['type']==='inner-template' or $options['type']==='outer-template') and $options['template']===null)
				$options['template'] = $name;

			if($options['template']){
				$dir = $this->model->_Admin->url ? $this->model->_Admin->url.'/' : '';
				$template_path = INCLUDE_PATH.'app'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$dir.$this->model->_Admin->request[0].DIRECTORY_SEPARATOR.$options['template'].'.php';
				if(!file_exists($template_path))
					$options['template'] = null;
			}

			if($options['template'] and $options['type']==='outer-template'){
				include($template_path);
			}else{
				?>
                <div class="rob-field" style="width: 5%; text-align: center">
                    <a href="#" onclick="if(confirm('Sicuro di voler eliminare questa riga?')) sublistDeleteRow('<?=entities($name)?>', '<?=entities($options['cont'])?>', '[n]'); return false"><img src="<?=PATH?>model/<?=$this->getClass()?>/files/img/toolbar/delete.png" alt="" /></a>
                    <input type="hidden" name="ch-<?=entities($name)?>-[n]" value="1" />
                </div>
                <div class="rob-field" style="width: 95%">
					<?php
					if($options['template'] and $options['type']==='inner-template'){
						include($template_path);
					}else{
						$form->render([
							'one-row' => $options['type']==='row',
							'show-labels' => $options['type']==='form',
						]);
					}
					?>
                </div>
				<?php
			}
			?>
        </div>
		<?php
	}

	/**
	 * Renders a group of tabs
	 *
	 * @param string $name
	 * @param array $tabs
	 * @param array $options
	 */
	public function renderTabs($name, array $tabs, array $options = []){
		$options = array_merge([
			'before' => false,
			'after' => false,
			'default' => null,
		], $options);

		$totK = $this->model->_Admin->request[0].'-'.$this->model->element['id'].'-'.$name;

		if($options['default']!==null and !isset($tabs[$options['default']]))
			$options['default'] = null;

		echo '<div class="admin-tabs" data-tabs="'.entities($totK).'" data-name="'.entities($name).'"'.($options['default']!==null ? ' data-default="'.entities($options['default']).'"' : '').'>';
		if($options['before'])
			echo '<div>'.$options['before'].'</div>';
		foreach($tabs as $k=>$t) {
			if(!is_array($t))
				$t = ['label'=>$t];
			$t = array_merge([
				'label'=>'',
				'onclick'=>'',
			], $t);

			echo '<a class="admin-tab" data-tab="'.entities($k).'" data-onclick="'.($t['onclick'] ? entities($t['onclick']).';' : '').'">'.entities($t['label']).'</a>';
		}
		if($options['after'])
			echo '<div>'.$options['after'].'</div>';
		echo '</div>';
	}
}
