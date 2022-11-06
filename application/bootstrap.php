<?php

// подключаем файлы ядра
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';

require_once 'core/route.php';

session_start();
if ((empty($_SESSION['auth'])) || ($_SESSION['auth'] == false)){
	if((!empty($_COOKIE['login'])) && (!empty($_COOKIE['key']))){

		$link = new mysqli('localhost', 'root', 'admin','sop_db');
		$login = $_COOKIE['login'];
		$key = $_COOKIE['key'];

		$prepared = $link->prepare("SELECT user_login, user_name FROM `users` WHERE user_login = ? AND user_cookie = ?");
        $prepared->bind_param('ss', $login, $key);
        $prepared->execute();
        $result = $prepared->get_result();

        $user = $result->fetch_assoc();
		if (!empty($user)){
			$_SESSION['auth'] = true;
			$_SESSION['login'] = $user['user_login'];
			$_SESSION['username'] = $user['user_name'];
		}

		mysqli_close($link);
	}
}

Route::start(); // запускаем маршрутизатор
