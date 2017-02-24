<?php

namespace App\Controllers;

class HomeController extends Controller {

	protected $view;

	public function __construct($container) {
		// parent::__construct($container);

		$this->view = $this->container->view;
	}

	public function index($request, $response) {

		return $this->view->render('login'); // loads login form

		// return $response->write('Home controller');

	}
}