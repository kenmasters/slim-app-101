<?php

namespace App\Controllers;

class RestController extends Controller {

	public function __construct($container) {

		parent::__construct($container);

	}

	public function index($request, $response) {

		return $this->view->render('login'); // loads login form

		// return $response->write('Home controller');

	}


	public function show($request, $response) {
		echo $request->getAttribute('id');
	}

	public function __invoke() {
		
	// 	if ($request->isGet()) {
	// 		return $response->write('REST: ' . $request->getMethod());
	// 	} elseif ($request->isPost()) {
	// 		return $response->write('REST: ' . $request->getMethod());
	// 	} elseif ($request->isPut()) {
			
	// 	} elseif ($request->isDelete()) {
			
	// 	}	
		
	}
}