<?php
$adminFrontConfig = $this->model->_AdminFront->retrieveConfig();
$hideMenu = $adminFrontConfig['hide-menu'] ?? 'mobile';

$config = $this->model->_AdminTemplateEditt->retrieveConfig();
?>
<!DOCTYPE html>

<html lang="en">

<head>
	[:head]
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="theme-color" content="#383837">
	<script>
		var adminApiPath = '<?=$this->model->_Admin->getApiPath()?>';
		var model_notifications_user_idx = 'Admin';
		var model_notifications_user = null;
		var enableHistoryNavigation = <?=json_encode($adminFrontConfig['enableHistoryNavigation'])?>;
	</script>

	<style>
		html {
			--background-header: <?=$config['background-header']?>;
			--text-header: <?=$config['text-header']?>;
			--background-menu-primary-off: <?=$config['background-menu-primary-off']?>;
			--background-menu-primary-on: <?=$config['background-menu-primary-on']?>;
			--text-menu-primary-off: <?=$config['text-menu-primary-off']?>;
			--text-menu-primary-on: <?=$config['text-menu-primary-on']?>;
			--background-menu-secondary-off: <?=$config['background-menu-secondary-off']?>;
			--background-menu-secondary-on: <?=$config['background-menu-secondary-on']?>;
			--text-menu-secondary-off: <?=$config['text-menu-secondary-off']?>;
			--text-menu-secondary-on: <?=$config['text-menu-secondary-on']?>;
		}
	</style>
</head>

<body>

<a href="#" onclick="switchMenu(); return false"><img src="<?= PATH ?>model/AdminTemplateEditt/assets/img/open-menu.png" alt="" id="img-open-menu"<?php if ($hideMenu != 'always') { ?> style="opacity: 0"<?php } ?> /></a>

<div id="main-container">
	<header id="header">
		<div id="header-right" style="display: none">
			<?php
			if ($this->model->isLoaded('Notifications')) {
				?>
				<div>
					<a href="#" onclick="toggleNotifications(); return false" class="tasto-header" id="notifications-bell">
						<i class="fas fa-bell" style="font-size: 17px"></i>
						<span id="notifications-counter" style="display: none"></span>
					</a>
				</div>
				<?php
			}
			?>
			<div>
				<a href="#" onclick="logout(); return false" class="tasto-header">
					<?= entities($this->model->_AdminFront->word('logout')) ?>
				</a>
			</div>
		</div>
		<div id="header-left">
			<div class="d-none d-sm-inline-block" style="border-right: solid var(--text-header) 1px">
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>" style="font-size: 26px" onclick="loadAdminPage('', {}, {}, false); return false">
					<?php
					if (file_exists(INCLUDE_PATH . 'app' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png'))
						echo '<img src="' . PATH . 'app/assets/img/logo.png" alt="' . APP_NAME . ' logo" style="max-height: 39px"/>';
					else
						echo APP_NAME;
					?>
				</a>
			</div>
			<div id="header-user-cont" style="display: none">
				<i class="fas fa-user"></i>
				<span id="header-username"></span>
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
			[<a href="#" onclick="manageFilters(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage')) ?> </a>]
			[<a href="#" onclick="manageSearchFields(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage-main')) ?> </a>]
			[<a href="#" onclick="filtersReset(); return false"> Reset valori </a>]
		</div>
		<form id="filtersFormCont" onsubmit="return false"></form>
	</div>

	<aside id="main-menu-cont" data-hide="<?= $hideMenu ?>">
		<div class="d-block d-sm-none text-center p-2">
			<?php if (file_exists(INCLUDE_PATH . 'app' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>"><img src="<?= PATH ?>app/assets/img/logo.png" alt="" style="max-width: 95%"/></a>
			<?php } else { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>" style="font-size: 20px"><?= APP_NAME ?></a>
			<?php } ?>
		</div>

		<div id="main-menu"></div>
		<div id="main-menu-resize" onmousedown="startMenuResize(event); event.stopPropagation(); event.preventDefault()" ondblclick="menuResizing = false; switchMenu()"></div>
	</aside>

	<main id="main-page" class="no-toolbar no-breadcrumbs">
		<div id="toolbar" class="d-none"></div>
		<div id="breadcrumbs" class="d-none"></div>
		<div id="main-loading"><img src="<?= PATH ?>model/Output/files/loading.gif" alt=""/></div>
		<div id="main-content"></div>
	</main>
</div>

<div id="main-loading-bar-cont">
	<div id="main-loading-bar" style="width: 0%"></div>
</div>

</body>

[:foot]

</html>
