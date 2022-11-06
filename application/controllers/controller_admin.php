<?php

class Controller_Admin extends Controller
{

	function __construct()
	{
		$this->model = new Model_Admin();
		$this->view = new View();
	}

	function action_index()
	{
		$data_arr = ["", "", "", "", ""]; //Результаты поиска пользователей и блогов
		if (isset($_POST['admin-usersubmit'])) {
			$users_query = $this->model->getUsersQuery($_POST['admin-usersearch']);
			$data_arr[0] = $users_query;
		}
		else if (isset($_POST['admin-useraddsubmit'])) {
			$add_error = $this->model->addUser($_POST);
			$data_arr[1] = $add_error;
		}
		else if (isset($_POST['delete-user'])){
			foreach( $_POST['delete-user'] as $key => $value){
				$this->model->deleteUser($key);
			}
		}
		else if (isset($_POST['edit-user'])){
			foreach( $_POST['edit-user'] as $key => $value){
				isset($_POST['admin-useradmin'][$key]) ? $admin = true : $admin = false;
				$data_arr[2] = $this->model->editUser($key, $_POST['admin-username'][$key], $_POST['admin-userlogin'][$key], $_POST['admin-usergroup'][$key], $_POST['admin-userpass'][$key], $admin);
			}
		}
		else if (isset($_POST['delete-survey'])){
			foreach( $_POST['delete-survey'] as $key => $value){
				$this->model->deleteSurvey($key);
			}
		}
		else if (isset($_POST['edit-survey'])){
			foreach( $_POST['edit-survey'] as $key => $value){
				$data_arr[2] = $this->model->editSurvey($key, $_POST['admin-period'][$key], $_POST['admin-surveydesc'][$key], $_POST['admin-startdate'][$key], $_POST['admin-enddate'][$key]);
			}
		}
		else if (isset($_POST['newsurvey-submit'])){
			$newsurvey_msg = $this->model->createSurvey($_POST);
		}
		$data_arr[3] = $this->model->getAdminSurveys();
		$this->view->generate('admin_view.php', 'template_view.php', $this->is_logged(), $data_arr);
	}
}