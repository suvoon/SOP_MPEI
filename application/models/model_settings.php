<?php

class Model_Settings extends Model
{
	
    public function changeUname($new_uname){ //Проверка на корректность и изменение имени пользователя
        $link = $this->dbconnect();
        $uname_error = "";

        $prepared = $link->prepare("SELECT user_id FROM `users` WHERE user_name = ?");
        $prepared->bind_param('s', $new_uname);
        $prepared->execute();
        $result = $prepared->get_result(); 

        $isUsernameOccupied = $result->fetch_assoc();
        if (empty($isUsernameOccupied)){
            if (mb_strlen($new_uname, 'utf8') < 30){
                if ((str_replace(" ",'',$new_uname) != "") && (!strpos($new_uname, "/")) && (!strpos($new_uname, "?"))){
                    $prepared = $link->prepare("UPDATE `users` SET user_name = ? WHERE user_login = ?");
                    $prepared->bind_param('ss', $new_uname, $_SESSION['login']);
                    $prepared->execute();
            
                    $_SESSION['username'] = $new_uname;
                }
                else{
                    $uname_error = "Имя пользователя содержит недопустимые символы";
                }
            } 
            else{
                $uname_error = "Имя пользователя слишком длинное";
            }
        } 
        else{
            $uname_error = "Имя пользователя уже занято";
        }
        mysqli_close($link);
        return $uname_error;
    }

    public function changePfp($profilepic){ //Проверка на корректность файла и изменение изображения профиля
        $pfp_error = "";
        $ext = getimagesize($profilepic)['mime'];
        $ext = explode('/', $ext)[0];
        if ($ext == "image"){
            $blob = $this->imageToBlob($profilepic);
            $link = $this->dbconnect();

            $prepared = $link->prepare("UPDATE `users` SET user_image = ? WHERE user_login = ?");
            $prepared->bind_param('ss', $blob, $_SESSION['login']);
            $prepared->execute();

            mysqli_close($link);
        }
        else $pfp_error = "Некорректный формат изображения";
        return $pfp_error;
    }

}
