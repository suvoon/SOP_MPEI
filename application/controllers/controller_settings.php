<?php

class Controller_Settings extends Controller
{
	function __construct()
	{
		$this->model = new Model_Settings();
		$this->view = new View();
	}

	function action_index()
	{
		//Описание, ошибка загрузки аватара, ошибка изменения имени профиля, ошибка изменения описания
		$data_arr = ["", ""]; 

		if ((isset($_POST['pfp-change-submit'])) && ($_FILES)){ //Если картинка загружена
			if ($_FILES["profile-picture"]["error"]== UPLOAD_ERR_OK){
				$pfp_change_error = $this->model->changePfp($_FILES['profile-picture']['tmp_name']);
			}
			else{
				$pfp_change_error = "Размер изображения превышает 2МБ";
			}
			$data_arr[0] = $pfp_change_error;
		}
		else if (isset($_POST['uname-change-submit'])) {
			$uname_change_error = $this->model->changeUname($_POST['username']);
			$data_arr[1] = $uname_change_error;
		}
		$this->view->generate('settings_view.php', 'template_view.php', $this->is_logged(), $data_arr);
		
	}
}
