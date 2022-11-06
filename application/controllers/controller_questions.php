<?php

class Controller_Questions extends Controller
{
	function __construct()
	{
		$this->model = new Model_Questions();
		$this->view = new View();
	}

	function action_index()
	{	
		$questions_data = $this->model->getQuestions();
		if (isset($_POST['quiz-submit'])){
            $this->model->submitResult($_POST);
        }
        $this->view->generate('questions_view.php', 'template_view.php', $this->is_logged(), $questions_data);
	}
}