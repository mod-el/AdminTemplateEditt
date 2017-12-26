<?php namespace Model\AdminTemplateEditt\Controllers;

use Model\Core\Controller;

class AdminServiceWorkerController extends Controller {
	public function init(){
		header('Content-Type: text/javascript');
	}

	public function index(){
		$assets = $this->model->_AdminTemplateEditt->getAssets();
		require(INCLUDE_PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache-key.php');
		require(INCLUDE_PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'sw.js');
		die();
	}
}
