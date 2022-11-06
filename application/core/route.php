<?php

/*
Класс-маршрутизатор для определения запрашиваемой страницы.
> цепляет классы контроллеров и моделей;
> создает экземпляры контролеров страниц и вызывает действия этих контроллеров.
*/
class Route
{

	static function start()
	{
		// контроллер и действие по умолчанию
		$controller_name = 'Main';
		$action_name = 'index';
		
		$routes = explode('/', $_SERVER['REQUEST_URI']);
		$search = explode('?', $_SERVER['REQUEST_URI']);

		if ((!empty($search[1])) && (strpos($search[1], 'search-request=') !== false)){
			$search_str = substr($search[1], 15);
			$search_str = substr($search_str, 0, -8);
			$search_str = str_replace('%2F', "", $search_str);
			echo $search[1];
			header("Location: /search/".$search_str);
		}
		else{
			// получаем имя контроллера
			if ( !empty($routes[1]) )
			{	
				$controller_name = $routes[1];
			}
			
			// получаем имя экшена
			if ( !empty($routes[2]) )
			{
				$action_name = $routes[2];
			}
	
			// добавляем префиксы
			$page_name = $controller_name;
			$model_name = 'Model_'.$controller_name;
			$controller_name = 'Controller_'.$controller_name;
			$link = $action_name; // Ссылка для страниц блога, поиска, редактора и профиля
			$action_name = 'action_'.$action_name;
			
			//Если пользователь вошёл, редирект с регистрации на главную страницу
			if(($page_name == "auth") && (isset($_SESSION['auth'])) && ($_SESSION['auth'] == true)){ 
				header('Location: /main');
				exit();
			}
	
			//Если пользователь не вошёл, при переходе на настройки профиля, создатель блога и страницу админа, редирект на регистрацию
			if((($page_name == "settings") || ($page_name == "surveys") || ($page_name == "admin") || ($page_name == "questions")) && ((!isset($_SESSION['auth'])) || ($_SESSION['auth'] == false))){
				header('Location: /auth');
				exit();
			}

			//Если пользователь без прав админа переходит в панель админа, редирект на главную страницу
			if (($page_name == "admin") && (isset($_SESSION['auth'])) && ($_SESSION['auth'] == true)){
				$link = new mysqli('localhost', 'root', 'admin','sop_db');
		
				$prepared = $link->prepare("SELECT user_admin FROM `users` WHERE user_login = ?");
				$prepared->bind_param('s', $_SESSION['login']);
				$prepared->execute();
				$result = $prepared->get_result();
		
				$user = $result->fetch_assoc();
				if (!empty($user)){
					if ($user['user_admin'] == false){
						(new Route)->ErrorPage404();
						exit();
					}
				}
			}
	
			//Подцепляем файл с классом модели (файла модели может и не быть)
			$model_file = strtolower($model_name).'.php';
			$model_path = "application/models/".$model_file;
			if(file_exists($model_path))
			{
				include "application/models/".$model_file;
			}
	
			//Подцепляем файл с классом контроллера
			$controller_file = strtolower($controller_name).'.php';
			$controller_path = "application/controllers/".$controller_file;
			if(file_exists($controller_path))
			{
				include "application/controllers/".$controller_file;
			}
			else
			{
				echo $controller_file;
				(new Route)->ErrorPage404();
			}
			//Создаем контроллер
			$controller = new $controller_name;
			$action = $action_name;
			
			if(method_exists($controller, $action))
			{
				//Вызываем действие контроллера
				$controller->$action();
			}
			//Иначе если действия нет, это может быть ссылкой
			else if(($page_name = "questions") || ($page_name = "profile")){
				$_SESSION['link'] = $link;
				$controller->action_index();
			}
			else
			{
				(new Route)->ErrorPage404();
			}
		}
	}

	function ErrorPage404()
	{
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:'.$host.'404');
    }
    
}
