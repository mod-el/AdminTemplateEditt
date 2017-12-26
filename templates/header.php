<?php
$config = $this->model->_Admin->retrieveConfig();
$hideMenu = isset($config['hide-menu']) ? $config['hide-menu'] : 'mobile';
$maxMenuWidth = isset($_COOKIE['menu-width']) ? $_COOKIE['menu-width'] : 220;
?>
<!DOCTYPE html>

<html>

<head>
	[:head]
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="manifest" href="<?=$this->model->_Admin->getUrlPrefix()?>manifest.json">
    <meta name="theme-color" content="#383837">

    <script>
		var maxMenuWidth = <?=$maxMenuWidth?>;
		var adminPrefix = <?=json_encode($this->model->_Admin->getUrlPrefix())?>;
    </script>
    <style>
        body{
            background-color: #f2f2f2;
            color: #333;
            font-family: 'Lato', sans-serif;
        }

        #header{
            width: 100%;
            height: 45px;
            padding: 3px 0;
            line-height: 39px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            background: #000 url('<?=PATH?>model/AdminTemplateEditt/files/img/bg-header.png') center repeat-x;
            white-space: nowrap;
        }

        #header a:link, #header a:visited{
            color: #FFF;
        }

        #header > div > div{
            display: inline-block;
            color: #FFF;
            padding: 0 10px;
            font-family: 'Raleway', sans-serif;
        }

        #main-menu {
            max-width: <?=$maxMenuWidth?>px;
        }
    </style>
</head>

<body>
    <header id="header">
        <div class="tasti-right">
            <div>
                <a href="<?=$this->model->_Admin->getUrlPrefix()?>logout" class="tasto-header">
                    Log out
                </a>
            </div>
        </div>
        <div>
            <div>
				<?php if(file_exists(INCLUDE_PATH.'img/logo.png')){ ?>
                    <a href="<?=$this->model->_Admin->getUrlPrefix()?>"><img src="<?=PATH?>img/logo.png" alt="" style="max-height: 39px" /></a>
				<?php }else{ ?>
                    <a href="<?=$this->model->_Admin->getUrlPrefix()?>" style="font-size: 26px"><?=APP_NAME?></a>
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

    <a href="#" onclick="switchMenu(); return false"><img src="<?=PATH?>model/AdminTemplateEditt/files/img/open-menu.png" alt="" id="img-open-menu"<?php if($hideMenu!='always'){ ?> style="opacity: 0"<?php } ?> /></a>

    <div id="filtersForm" style="display: none">
        <div class="pad5v no-overflow">
            <div class="right">
                [<a href="#" onclick="switchFiltersForm(false); return false"> chiudi </a>]
            </div>
            [<a href="#" onclick="manageFilters(); return false"> gestisci </a>]
            [<a href="#" onclick="manageSearchFields(); return false"> gestisci campo generico </a>]
            [<a href="#" onclick="filtersReset(); return false"> resetta </a>]
        </div>
        <div id="filtersFormCont"></div>
    </div>

    <div class="grid" id="main-grid">
        <div id="main-menu" data-hide="<?=$hideMenu?>">
			<?php
			$pages = $this->model->_Admin->getPages();

			foreach($pages as $pIdx => $p){
			    if(isset($p['rule'])){
					$link = $this->model->_Admin->getUrlPrefix().$p['rule'];
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