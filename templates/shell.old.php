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
		var adminApiPath = '<?=$this->model->_Admin->getApiPath()?>';
		var maxMenuWidth = <?=$maxMenuWidth?>;
		var model_notifications_user_idx = 'Admin';
		var model_notifications_user = null;
	</script>
	<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/assets/css/basics.css"/>
	<script type="text/javascript" src="<?= PATH ?>model/AdminTemplateEditt/assets/js/js.js"></script>
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

</header>

<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/assets/css/menu.css"/>

<a href="#" onclick="switchMenu(); return false"><img src="<?= PATH ?>model/AdminTemplateEditt/assets/img/open-menu.png" alt="" id="img-open-menu"<?php if ($hideMenu != 'always') { ?> style="opacity: 0"<?php } ?> /></a>

<div class="grid" id="main-grid" style="display: none">
	<div id="main-menu" data-hide="<?= $hideMenu ?>">
		<div class="d-block d-sm-none text-center p-2">
			<?php if (file_exists(INCLUDE_PATH . 'app' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png')) { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>"><img src="<?= PATH ?>app/assets/img/logo.png" alt="" style="max-width: 95%"/></a>
			<?php } else { ?>
				<a href="<?= $this->model->_AdminFront->getUrlPrefix() ?>" style="font-size: 20px"><?= APP_NAME ?></a>
			<?php } ?>
		</div>

		<div id="main-menu-ajaxcont"></div>
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

</body>

<link rel="stylesheet" type="text/css" href="<?= PATH ?>model/AdminTemplateEditt/assets/css/style.css"/>

[:foot]

</html>