<?php
$config = $this->model->_AdminFront->retrieveConfig();
$hideMenu = isset($config['hide-menu']) ? $config['hide-menu'] : 'mobile';
$maxMenuWidth = isset($_COOKIE['menu-width']) ? $_COOKIE['menu-width'] : 220;
$this->languageBound = true;
?>
<!DOCTYPE html>

<html lang="en">

<head>
	[:head]
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="theme-color" content="#383837">
	<script>
		var maxMenuWidth = <?=$maxMenuWidth?>;
		var model_notifications_user_idx = 'Admin';
		var model_notifications_user = '<?=$this->model->_User_Admin->logged()?>';
	</script>
	<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/files/basics.css"/>
	<script type="text/javascript" src="<?= PATH ?>model/AdminTemplateEditt/files/js.js"></script>
	<style>
		#main-menu {
			max-width: <?=$maxMenuWidth?>px;
		}

		#main-grid {
			display: none;
		}
	</style>
</head>

<body>
<header id="header">
	<div class="tasti-right">
		<?php
		if ($this->model->isLoaded('Notifications')) {
			?>
			<div>
				<a href="#" onclick="toggleNotifications(); return false" class="tasto-header" id="campanellina-notifiche">
					<i class="fas fa-bell" style="font-size: 17px"></i>
				</a>
			</div>
			<?php
		}
		?>
		<div>
			<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>logout" class="tasto-header">
				<?= entities($this->model->_AdminFront->word('logout')) ?>
			</a>
		</div>
	</div>
	<div>
		<div class="d-none d-sm-inline-block" style="border-right: solid #FFF 1px">
			<?php if (file_exists(INCLUDE_PATH . 'app' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>"><img src="<?= PATH ?>app/assets/img/logo.png" alt="" style="max-height: 39px"/></a>
			<?php } else { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>" style="font-size: 26px"><?= APP_NAME ?></a>
			<?php } ?>
		</div>
		<div>
			<img src="<?= PATH ?>model/AdminTemplateEditt/files/img/utente.png" alt=""/>
			<span style="padding-left: 5px"><?= $this->model->_User_Admin->username ?></span>
		</div>
	</div>
	<?php
	if ($this->model->isLoaded('Notifications')) {
		?>
		<div id="header-notifications-container" style="display: none"></div>
		<?php
	}
	?>
</header>

<div id="filtersForm" style="display: none">
	<div class="pad5v no-overflow">
		<div class="right">
			[<a href="#" onclick="switchFiltersForm(false); return false"> <?= entities($this->model->_AdminFront->word('filters-close')) ?> </a>]
		</div>
		[<a href="#" onclick="manageFilters(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage')) ?> </a>] [<a href="#" onclick="manageSearchFields(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage-main')) ?> </a>] [<a href="#" onclick="filtersReset(); return false"> <?= entities($this->model->_AdminFront->word('filters-reset')) ?> </a>]
	</div>
	<form id="filtersFormCont" onsubmit="return false"></form>
</div>

<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/files/menu.css"/>

<a href="#" onclick="switchMenu(); return false"><img src="<?= PATH ?>model/AdminTemplateEditt/files/img/open-menu.png" alt="" id="img-open-menu"<?php if ($hideMenu != 'always') { ?> style="opacity: 0"<?php } ?> /></a>

<div class="grid" id="main-grid">
	<div id="main-menu" data-hide="<?= $hideMenu ?>">
		<?php
		$pages = $this->model->_AdminFront->getPages();
		$this->model->_AdminTemplateEditt->renderMenu($pages);
		?>
		<div id="main-menu-resize" onmousedown="startMenuResize(event); event.stopPropagation(); event.preventDefault()" ondblclick="menuResizing = false; switchMenu()"></div>
	</div>

	<div id="main-page-cont" style="width: calc(<?= '100% - ' . $maxMenuWidth . 'px' ?>)">
		<div id="toolbar"></div>

		<div id="main-page">
			<div id="breadcrumbs" style="display: none"></div>
			<div id="main-content" style="left: 0px"></div>
			<div id="main-loading"><img src="<?= PATH ?>model/Output/files/loading.gif" alt=""/></div>
		</div>
	</div>
</div>

<div id="main-loading-bar-cont">
	<div id="main-loading-bar" style="width: 0%"></div>
</div>

</body>

<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/files/style.css"/>

[:foot]

</html>