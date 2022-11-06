<?php

class Controller_Surveys extends Controller
{
	function __construct()
	{
		$this->model = new Model_Surveys();
		$this->view = new View();
	}

	function action_index()
	{	
        $surveys_data = $this->model->getSurveys();
		$this->view->generate('surveys_view.php', 'template_view.php', $this->is_logged(), $surveys_data);
	}
}