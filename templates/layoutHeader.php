<?php
$config = $this->model->_AdminFront->retrieveConfig();
$hideMenu = isset($config['hide-menu']) ? $config['hide-menu'] : 'mobile';
$maxMenuWidth = isset($_COOKIE['menu-width']) ? $_COOKIE['menu-width'] : 220;
$this->languageBound = true;
?>
<!DOCTYPE html>

<html>

<head>
	[:head]
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="manifest" href="<?=$this->model->_AdminFront->getUrlPrefix()?>manifest.json">
    <meta name="theme-color" content="#383837">

    <script>
		var maxMenuWidth = <?=$maxMenuWidth?>;
		var adminPrefix = <?=json_encode($this->model->_AdminFront->getUrlPrefix())?>;
		var elementCallback = null;
    </script>
    <link rel="stylesheet" type="text/css" href="<?=PATH?>model/AdminTemplateEditt/files/basics.css" />
    <style>
        #main-menu {
            max-width: <?=$maxMenuWidth?>px;
        }

        #main-grid{
            display: none;
        }
    </style>
</head>

<body>
    <header id="header">
        <div class="tasti-right">
			<?php
			if($this->model->isLoaded('Multilang')){
				?>
                <div>
                    <select id="admin-language-selector" onchange="this.getValue().then(l => changeAdminLang(l))">
                        <option value=""></option>
						<?php
						foreach($this->model->_Multilang->langs as $l){
							?>
                            <option value="<?=entities($l)?>"><?=entities(ucwords($l))?></option>
							<?php
						}
						?>
                    </select>
                </div>
				<?php
			}
			?>
            <div>
                <a href="<?=$this->model->_AdminFront->getUrlPrefix()?>logout" class="tasto-header">
					<?= entities($this->model->_AdminFront->word('logout')) ?>
                </a>
            </div>
        </div>
        <div>
            <div>
				<?php if(file_exists(INCLUDE_PATH.'app'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'logo.png')){ ?>
                    <a href="<?=$this->model->_AdminFront->getUrlPrefix()?>"><img src="<?=PATH?>app/assets/img/logo.png" alt="" style="max-height: 39px" /></a>
				<?php }else{ ?>
                    <a href="<?=$this->model->_AdminFront->getUrlPrefix()?>" style="font-size: 26px"><?=APP_NAME?></a>
				<?php } ?>
            </div>
            <div style="border-left: solid #FFF 1px">
                <img src="<?=PATH?>model/AdminTemplateEditt/files/img/utente.png" alt="" />
                <span style="padding-left: 5px">
                    <?=$this->model->_User_Admin->username?>
                </span>
            </div>
        </div>
    </header>

    <div id="filtersForm" style="display: none">
        <div class="pad5v no-overflow">
            <div class="right">
                [<a href="#" onclick="switchFiltersForm(false); return false"> <?= entities($this->model->_AdminFront->word('filters-close')) ?> </a>]
            </div>
            [<a href="#" onclick="manageFilters(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage')) ?> </a>]
            [<a href="#" onclick="manageSearchFields(); return false"> <?= entities($this->model->_AdminFront->word('filters-manage-main')) ?> </a>]
            [<a href="#" onclick="filtersReset(); return false"> <?= entities($this->model->_AdminFront->word('filters-reset')) ?> </a>]
        </div>
        <div id="filtersFormCont"></div>
    </div>

    <link rel="stylesheet" type="text/css" href="<?=PATH?>model/AdminTemplateEditt/files/menu.css" />

    <a href="#" onclick="switchMenu(); return false"><img src="<?=PATH?>model/AdminTemplateEditt/files/img/open-menu.png" alt="" id="img-open-menu"<?php if($hideMenu!='always'){ ?> style="opacity: 0"<?php } ?> /></a>

    <div class="grid" id="main-grid">
        <div id="main-menu" data-hide="<?=$hideMenu?>">
			<?php
			$pages = $this->model->_AdminFront->getPages();

			foreach($pages as $pIdx => $p){
			    if(isset($p['rule'])){
					$link = $this->model->_AdminFront->getUrlPrefix().$p['rule'];
					$onclick = 'loadAdminPage([\''.$p['rule'].'\']); return false';
                }else{
					$link = '#';
					$onclick = 'switchMenuGroup(\''.$pIdx.'\'); return false';
                }
                ?>
                <a href="<?=$link?>" class="main-menu-tasto" id="menu-group-<?=$pIdx?>" onclick="<?=$onclick?>" data-menu-id="<?=$pIdx?>">
                    <span class="cont-testo-menu"><?=entities($p['name'])?></span>
                </a>
                <?php
                if(isset($p['sub']) and $p['sub']){
                    ?>
                    <div class="main-menu-cont expandible" id="menu-group-<?=$pIdx?>-cont" style="height: 0px" data-menu-id="<?=$pIdx?>">
                        <div>
                            <?php
                            $this->model->_AdminTemplateEditt->renderMenuItems($pIdx, $p['sub']);
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
			?>
            <div id="main-menu-resize" onmousedown="startMenuResize(event); event.stopPropagation(); event.preventDefault()" ondblclick="switchMenu()"></div>
        </div>

        <div id="main-page-cont" style="width: calc(<?='100% - '.$maxMenuWidth.'px'?>)">
            <div id="toolbar"></div>

            <div id="main-page">
                <div id="breadcrumbs" style="display: none"></div>

                <div id="main-content" style="left: 0px">