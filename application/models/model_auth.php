<?php
class Model_Auth extends Model
{

    public function generateSalt(){ //Генерация соли для пароля и куки
        $salt = '';
        $saltLength = 5;
        for ($i = 0; $i < $saltLength; $i++){
            $salt .= chr(mt_rand(33,126));
        }
        return $salt;
    }

    public function authenticate($login, $password, $remember){ //Проверка полей и авторизация пользователя
        $link = $this->dbconnect();
        $auth_error = "";

        $prepared = $link->prepare("SELECT user_login, user_password, user_salt, user_name FROM `users` WHERE user_login = ?");
        $prepared->bind_param('s', $login);
        $prepared->execute();
        $result = $prepared->get_result();

        $user = $result->fetch_assoc();
        if (!empty($user)){
            $salt = $user['user_salt'];
            $saltPassword = md5($password.$salt);
            if($user['user_password'] == $saltPassword){
                session_start();
                $_SESSION['auth'] = true;
                $_SESSION['login'] = $user['user_login'];
                $_SESSION['username'] = $user['user_name'];
                if(!empty($remember) && ($remember == "on")){
                    $key = $this->generateSalt();
                    setcookie('login', $user['user_login'], time()+2592000);
                    setcookie('key', $key, time()+2592000);

                    $prepared = $link->prepare("UPDATE `users` SET user_cookie = ? WHERE user_login = ?");
                    $prepared->bind_param('ss', $key, $login);
                    $prepared->execute();
                }
                mysqli_close($link);
                header("Location: /main");
                exit();
            }
            else $auth_error = "Неправильный пароль";
        }
        else $auth_error = "Неправильный логин";

        mysqli_close($link);
        return $auth_error;
    }

}
