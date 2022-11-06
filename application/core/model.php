<?php

class Model
{
	
	public function dbconnect(){ //mysqli-подключение к БД
		$host = 'localhost';
		$user = 'root';
		$password = 'admin';
		$db_name = 'sop_db';

		return $link = new mysqli($host, $user, $password, $db_name);
	}

	public function blobToImage($blob){ //Перевод blob в изображение
		return base64_encode($blob);
	}

	public function imageToBlob($image){ //Перевод изображения в blob (для записи в БД)
		$blob = file_get_contents($image);

		return $blob;
	}

	public function getProfilePic(){ //Получение изображения профиля пользователя
		$link = $this->dbconnect();
		$pfp_image = "";

		$prepared = $link->prepare("SELECT user_image from `users` WHERE user_login = ?");
        $prepared->bind_param('s', $_SESSION['login']);
        $prepared->execute();
		$result = $prepared->get_result();

		$pfp_result = $result->fetch_assoc();
		if(!empty($pfp_result)){
			$pfp = $pfp_result['user_image'];
			if (!empty($pfp)){
				$pfp_image = $this->blobToImage($pfp);
			}
		}
		
		return $pfp_image;
		mysqli_close($link);
	}

	public function ifAdmin(){ //Является ли пользователь администратором (и вывод панели администратора)
		$link = $this->dbconnect();
		$admin_btn = "";

		$prepared = $link->prepare("SELECT user_admin from `users` WHERE user_login = ?");
        $prepared->bind_param('s', $_SESSION['login']);
        $prepared->execute();
		$result = $prepared->get_result();

		$admin_result = $result->fetch_assoc();
		if(!empty($admin_result)){
			$admin = $admin_result['user_admin'];
			if ($admin == true){
				$admin_btn = "<a href='/admin'><div class='user__link admin-btn'>
								Панель администратора
							  </div></a>";
			}
		}
		
		return $admin_btn;
		mysqli_close($link);
	} 

}