<?php namespace Model\AdminTemplateEditt\Controllers;

use Model\Core\Controller;

class AdminWebAppManifestController extends Controller {
	public function init(){
		header('Content-Type: text/json');
	}

	public function index(){
		require(INCLUDE_PATH.'model'.DIRECTORY_SEPARATOR.'AdminTemplateEditt'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'manifest.json');
		die();
	}
}
