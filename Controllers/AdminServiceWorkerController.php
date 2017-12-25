<?php namespace Model\AdminTemplateEditt\Controllers;

use Model\Core\Controller;

class AdminServiceWorkerController extends Controller {
	public function init(){
		header('Content-Type: text/javascript');
	}

	public function index(){
		echo '// test';
		die();
	}
}
