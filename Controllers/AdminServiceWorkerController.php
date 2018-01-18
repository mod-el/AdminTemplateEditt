<?php namespace Model\AdminTemplateEditt\Controllers;

use Model\Core\Controller;

class AdminServiceWorkerController extends Controller {
	public function init(){
		header('Content-Type: text/javascript');
	}

	public function index(){
		$assets = $this->model->_AdminTemplateEditt->getAssetsForServiceWorker();
		require(INCLUDE_PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache-key.php');
		if($this->model->isLoaded('Multilang'))
			$cacheKey .= $this->model->_Multilang->lang;
		require(INCLUDE_PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'sw.js');
		die();
	}
}
