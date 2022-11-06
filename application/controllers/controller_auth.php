<?php

class Controller_Auth extends Controller
{

    function __construct()
	{
		$this->model = new Model_Auth();
		$this->view = new View();
	}
	
	function action_index()
	{	
        if (isset($_POST['auth-submit'])){
            if (!isset($_POST['auth-remember'])) $_POST['auth-remember'] = 0;
            //Ошибка при регистрации
            $auth_error = $this->model->authenticate($_POST['auth-login'], $_POST['auth-pass'], $_POST['auth-remember']);
            $this->view->generate('auth_view.php', 'template_view.php', $this->is_logged(), [$auth_error, ""]);
        }
        else $this->view->generate('auth_view.php', 'template_view.php', $this->is_logged());
		
	}
}