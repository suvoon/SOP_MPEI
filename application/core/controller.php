<?php

class Controller {
	
	public $model;
	public $view;
	
	function __construct()
	{
		$this->view = new View();
		$this->model = new Model();
	}
	
	// действие (action), вызываемое по умолчанию
	function action_index()
	{
		
	}

	function action_logout(){
		session_destroy();

		unset($_COOKIE['login']);
		setcookie('login', null, -1, '/');
		unset($_COOKIE['key']);
		setcookie('key', null, -1, '/');

		header("Location: /main");
        exit();
	}

	function is_logged(){
		if((isset($_SESSION['auth'])) && ($_SESSION['auth'] == 1)){ // 
			return ['logged_view.php', $this->model->getProfilePic(), $this->model->ifAdmin()];	
		}
		else{
			return ['unlogged_view.php', ""];
		} 
	}

}
