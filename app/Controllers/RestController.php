<?php

namespace App\Controllers;

class RestController extends Controller {

	protected $view;

	public function __construct($container) {
		// parent::__construct($container);

		// $this->view = $this->container->view;
	}

	public function index($request, $response) {

		// return $this->view->render('login'); // loads login form

		// return $response->write('Home controller');

	}

	public function __invoke($request, $response) {
		
		if ($request->isGet()) {
			return $response->write('REST: ' . $request->getMethod());
		} elseif ($request->isPost()) {
			return $response->write('REST: ' . $request->getMethod());
		} elseif ($request->isPut()) {
			
		} elseif ($request->isDelete()) {
			
		}	
		
	}
}