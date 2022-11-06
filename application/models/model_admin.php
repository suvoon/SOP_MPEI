<?php

class Model_Admin extends Model
{

    public function generateSalt(){ //Генерация соли для пароля и куки
        $salt = '';
        $saltLength = 5;
        for ($i = 0; $i < $saltLength; $i++){
            $salt .= chr(mt_rand(33,126));
        }
        return $salt;
    }

    public function formatDate($date){
        return substr($date, -2).".".substr($date, -5, 2).".".substr($date, 0, 4);
    }

    public function getUsersQuery($query){ //Вывести пользователей по запросу
        $link = $this->dbconnect();
        $users_arr = array();
        $query = "%".$query."%";

        $prepared = $link->prepare("SELECT * FROM `users` WHERE user_name LIKE ? OR user_group LIKE ? OR user_login LIKE ? ORDER BY user_id;");
        $prepared->bind_param('sss', $query, $query, $query);
        $prepared->execute();
        $result = $prepared->get_result();
        $user_result = $result->fetch_assoc();
        while (!empty($user_result)){
            $user_result['user_admin'] == true ? $user_admin = "checked" : $user_admin = "";
            $user_html = "<div class='admin-query__info-block'>
                            <div class='info-block__name underline'>"
                                .$user_result['user_name']." (".$user_result['user_login'].")
                            </div>
                            <div class='info-block__delete'>
                                <form action='' method='post'>
                                    <input type='submit' value='Удалить' name='delete-user[".$user_result['user_id']."]' class='info-block__button'>
                                    <input type='button' value='Редактировать' name='edituser' class='info-block__button info-block__edit'>
                                </form>
                            </div>
                            <div class='info-block__hidden' id='hidden-".$user_result['user_id']."'>
                                <div class='hidden__content'>
                                    <form method='post'>
                                        <div class='add-field first-field'>
                                            <label>Имя: </label><input type='text' value='".$user_result['user_name']."' name='admin-username[".$user_result['user_id']."]' class='admin-input' required>
                                        </div>
                                        <div class='add-field sec-field'>
                                            <label>Логин:</label><input type='text' value='".$user_result['user_login']."' name='admin-userlogin[".$user_result['user_id']."]' class='admin-input' required>
                                        </div>
                                        <div class='add-field fourth-field'>
                                            <label>Группа:</label><input type='text' value='".$user_result['user_group']."' name='admin-usergroup[".$user_result['user_id']."]' class='admin-input'>
                                        </div>
                                        <div class='add-field'>
                                            <input type='button'  value='Новый пароль' class='admin-querybtn newpass-btn'>
                                            <label class='newpass-lbl'>Новый пароль:</label><input type='text' name='admin-userpass[".$user_result['user_id']."]' class='admin-input newpass-field'>
                                        </div>
                                        <div class='add-field'>
                                            <label>Права администратора:</label><input type='checkbox' ".$user_admin." name='admin-useradmin[".$user_result['user_id']."]'>
                                        </div>
                                        <input type='submit' name='edit-user[".$user_result['user_id']."]' value='Изменить' class='admin-querybtn'>
                                    </form>
                                </div>
                            </div>
                        </div>";
            $users_arr[] = $user_html;
            $user_result = $result->fetch_assoc();
        }

        if (empty($users_arr)) $users_arr[] = "Ничего не найдено";
        mysqli_close($link);
        return $users_arr;
    }

    public function addUser($userdata){ //Проверка полей и регистрация пользователя
        $link = $this->dbconnect();
        $add_error = "";
        $userlogin = strip_tags($userdata['admin-userlogin']);
        $username = strip_tags($userdata['admin-username']);
        $userpass = strip_tags($userdata['admin-userpass']);
        $usergroup = strip_tags($userdata['admin-usergroup']);

        if(mb_strlen($userpass, 'utf8') < 30){
            $prepared = $link->prepare("SELECT user_id FROM `users` WHERE user_login = ?");
            $prepared->bind_param('s', $userlogin);
            $prepared->execute();

            $result = $prepared->get_result();
            $isLoginOccupied = $result->fetch_assoc();
            if ((empty($isLoginOccupied)) && (str_replace(" ",'',$userlogin) != "") && (mb_strlen($userlogin, 'utf8') < 30)){
                if (mb_strlen($username, 'utf8') < 60){
                    if ((str_replace(" ",'',$username) != "") && (!strpos($username, "/")) && (!strpos($username, "?"))){
                        $salt = $this->generateSalt();
                        $saltedPassword = md5($userpass.$salt);
                        if ((!empty($userdata['admin-useradmin'])) && ($userdata['admin-useradmin'] == true)){
                            $admin = true;
                        }
                        else $admin = false;
                        
                        $prepared = $link->prepare("INSERT INTO `users` (`user_name`, `user_login`, `user_password`, `user_salt`, `user_group`, `user_admin`) VALUES (?, ?, ?, ?, ?, ?);");
                        $prepared->bind_param('ssssss', $username, $userlogin, $saltedPassword, $salt, $usergroup, $admin);
                        $prepared->execute();
                        $add_error = "Пользователь успешно создан!";
                    }
                    else{
                        $add_error = "Имя пользователя содержит недопустимые символы";
                    }
                } 
                else{
                    $add_error = "Имя пользователя слишком длинное";
                }
            }
            else{
                $add_error = "Логин уже занят/недоступен";
            }
        }
        else{
            $add_error = "Слишком длинный пароль";
        }

        mysqli_close($link);
        return $add_error;
    }

    public function editUser($user_id, $new_name, $new_login, $new_group, $new_pass, $new_admin){ //Проверка полей и регистрация пользователя
        $link = $this->dbconnect();
        $edit_error = "";
        $new_name = strip_tags($new_name);
        $new_login = strip_tags($new_login);
        $new_group = strip_tags($new_group);
        $new_pass = strip_tags($new_pass);

        if ((!empty($new_pass)) || (str_replace(" ",'',$new_pass) != "")){
            if (mb_strlen($new_pass, 'utf8') < 30){
                $salt = $this->generateSalt();
                $saltedPassword = md5($new_pass.$salt);

                $prepared = $link->prepare("UPDATE `users` SET user_password = ?, user_salt = ? WHERE user_id = ?;");
                $prepared->bind_param('sss', $saltedPassword, $salt, $user_id);
                $prepared->execute();
            }
            else $edit_error = "Пароль слишком длинный";
        }

        $prepared = $link->prepare("SELECT user_id FROM `users` WHERE user_login = ? AND user_id != ?");
        $prepared->bind_param('ss', $new_login, $user_id);
        $prepared->execute();

        $result = $prepared->get_result();
        $isLoginOccupied = $result->fetch_assoc();
        if ((empty($isLoginOccupied)) && (str_replace(" ",'',$new_login) != "") && (mb_strlen($new_login, 'utf8') < 30)){
            if (mb_strlen($new_name, 'utf8') < 60){
                if ((str_replace(" ",'',$new_name) != "") && (!strpos($new_name, "/")) && (!strpos($new_name, "?"))){
                    $prepared = $link->prepare("UPDATE `users` SET user_name = ?, user_login = ?, user_group = ?, user_admin = ? WHERE user_id = ?;");
                    $prepared->bind_param('sssss', $new_name, $new_login, $new_group, $new_admin, $user_id);
                    $prepared->execute();
                    $edit_error = "Пользователь ".$new_name." успешно изменён!";
                }
                else{
                    $edit_error = "Имя пользователя содержит недопустимые символы";
                }
            } 
            else{
                $edit_error = "Имя пользователя слишком длинное";
            }
        }
        else{
            $edit_error = "Логин уже занят/недоступен";
        }

        mysqli_close($link);
        return $edit_error;
    }

    public function deleteUser($user_id){ //Удалить пользователя
        $link = $this->dbconnect();
        $login = $_SESSION['login'];

        $prepared = $link->prepare("SELECT user_login FROM `users` WHERE user_id = ?;");
        $prepared->bind_param('s', $user_id);
        $prepared->execute();
        $result = $prepared->get_result();
        $user_result = $result->fetch_assoc();
        if ($user_result['user_login'] != $login){
            $prepared = $link->prepare("DELETE FROM `users` where user_id = ?;");
            $prepared->bind_param('s', $user_id);
            $prepared->execute();
        }

        mysqli_close($link);
    }

    public function getAdminSurveys(){
        $link = $this->dbconnect();
        $login = $_SESSION['login'];
        $surveys = array();
        $i = 0;

        $prepared = $link->prepare("SELECT survey_id, survey_period, survey_desc, survey_startdate, survey_enddate, survey_link, survey_status FROM `surveys` ORDER BY survey_id DESC");
        $prepared->execute();
        $result = $prepared->get_result(); 

        $srv_res = $result->fetch_assoc();
        while (!empty($srv_res)){
            $buttons = "<div class='info-block__delete'>
                            <form action='' method='post'>
                                <input type='submit' value='Удалить' name='delete-survey[".$srv_res['survey_id']."]' class='info-block__survey-button'>
                                <input type='button' value='Редактировать' name='editsurvey' class='info-block__survey-button info-block__edit'>
                            </form>
                        </div>
                        <div class='info-block__hidden' id='srv-hidden-".$srv_res['survey_id']."'>
                                <div class='hidden__content'>
                                    <form method='post'>
                                        <div class='add-field'>
                                            <label>Период: </label><input type='text' value='".$srv_res['survey_period']."' name='admin-period[".$srv_res['survey_id']."]' class='admin-input' required>
                                        </div>
                                        <div class='add-field'>
                                            <label>Описание:</label><textarea cols='25' rows='5' name='admin-surveydesc[".$srv_res['survey_id']."]' class='admin-input'>".$srv_res['survey_desc']."</textarea>
                                        </div>
                                        <div class='add-field first-field'>
                                            <label>Дата начала:</label><input type='date' value='".$srv_res['survey_startdate']."' name='admin-startdate[".$srv_res['survey_id']."]' class='admin-input'>
                                        </div>
                                        <div class='add-field'>
                                            <label>Дата окончания:</label><input type='date' value='".$srv_res['survey_enddate']."' name='admin-enddate[".$srv_res['survey_id']."]' class='admin-input'>
                                        </div>
                                        <input type='submit' name='edit-survey[".$srv_res['survey_id']."]' value='Изменить' class='admin-querybtn'>
                                    </form>
                                </div>
                            </div>
                        </div>";
            $surveys[$i] = [$srv_res['survey_period'], $this->formatDate($srv_res['survey_startdate']), $this->formatDate($srv_res['survey_enddate']), $buttons];

            $srv_res = $result->fetch_assoc();
            $i++;
        }

        mysqli_close($link);
        return $surveys;
    }

    public function editSurvey($survey_id, $new_period, $new_desc, $new_startdate, $new_enddate){ //Проверка полей и регистрация пользователя
        $link = $this->dbconnect();
        $edit_error = "";
        $new_period = strip_tags($new_period);
        $new_desc = strip_tags($new_desc);

        if ((mb_strlen($new_period, 'utf8') < 60) && (str_replace(" ",'',$new_period) != "")){
                $prepared = $link->prepare("UPDATE `surveys` SET survey_period = ?, survey_desc = ?, survey_startdate = ?, survey_enddate = ? WHERE survey_id = ?;");
                $prepared->bind_param('sssss', $new_period, $new_desc, $new_startdate, $new_enddate, $survey_id);
                $prepared->execute();
                $edit_error = "Опрос ".$new_period." успешно изменён!";
        }
        else{
            $edit_error = "Слишком длинный/пустой заголовок";
        }

        mysqli_close($link);
        return $edit_error;
    }

    public function deleteSurvey($survey_id){ //Удалить пользователя
        $link = $this->dbconnect();

        $prepared = $link->prepare("DELETE FROM `surveys` where survey_id = ?;");
        $prepared->bind_param('s', $survey_id);
        $prepared->execute();

        mysqli_close($link);
    }

    public function createSurvey($questions){
        $link = $this->dbconnect();
        $content = "";
        $add_error = "";
        empty($questions['desc-default']) ? $desc = strip_tags($questions['description']) : $desc = "Укажите Вашу оценку учебного курса и преподавателя по указанным характеристикам. Каждая характеристика оценивается по 5-балльной шкале, где 1 — характеристика на очень низком уровне, 5 — характеристика на очень высоком уровне. Если Вы не посещали занятия по курсу или не можете оценить характеристику, отметьте «Затрудняюсь ответить».<br>
        Вы можете оставить любые комментарии и пожелания касательно учебного курса или преподавателя в текстовых полях. Заполнение текстовых полей не является обязательным, но Ваши впечатления и комментарии очень помогут преподавателям сделать учебные курсы лучше в дальнейшем.";

        foreach($questions['question'] as $block_n=>$block){
            $content = $content."<h3 class='quiz__title'>".strip_tags($questions['title'][$block_n])."</h3>";
            foreach($block as $question_n=>$question_type){
                switch ($question_type){
                    case "rate":
                        $content = $content."<div class='quiz__block'>
                                                <div class='block__header'>
                                                    <span>1</span>
                                                    <span>2</span>
                                                    <span>3</span>
                                                    <span>4</span>
                                                    <span>5</span>
                                                    <span>Затрудняюсь ответить</span>
                                                </div>
                                            </div>
                                            <div class='quiz__block'>
                                                <fieldset>
                                                    <input type='radio' value='idk' name='question-".$block_n."-".$question_n."' checked>
                                                    <input type='radio' value='5' name='question-".$block_n."-".$question_n."'>
                                                    <input type='radio' value='4' name='question-".$block_n."-".$question_n."'>
                                                    <input type='radio' value='3' name='question-".$block_n."-".$question_n."'>
                                                    <input type='radio' value='2' name='question-".$block_n."-".$question_n."'>
                                                    <input type='radio' value='1' name='question-".$block_n."-".$question_n."'>
                                                </fieldset>
                                                <span>".strip_tags($questions['qfield'][$block_n][$question_n])."</span>
                                            </div>";
                        break;
                    case "important":
                        $content = $content."<div class='quiz__block'>
                                                <h3>".strip_tags($questions['qfield'][$block_n][$question_n])." (заполнение обязательно)</h3>
                                            </div>
                                            <div class='quiz__block quiz__note'>
                                                <input type='text' class='text'name='question-".$block_n."-".$question_n."' required>
                                            </div>";
                        break;
                    case "unimportant":
                        $content = $content."<div class='quiz__block'>
                                                <h3>".strip_tags($questions['qfield'][$block_n][$question_n])." (заполнение необязательно)</h3>
                                            </div>
                                            <div class='quiz__block quiz__note'>
                                                <input type='text' class='text'name='question-".$block_n."-".$question_n."'>
                                            </div>";
                        break;
                }
            }
        }

        $status = true;
        $temp_url = "temp";
        $prepared = $link->prepare("INSERT INTO `surveys` (survey_period, survey_desc, survey_startdate, survey_enddate, survey_content, survey_link, survey_status) VALUES (?, ?, ?, ?, ?, ?, ?);");
        $prepared->bind_param('sssssss', $questions['period'], $desc, $questions['startdate'], $questions['enddate'], $content, $temp_url, $status);
        $prepared->execute();
        
        $inserted_id = $link->insert_id;
        $url = urlencode(base64_encode($inserted_id));

        $prepared = $link->prepare("UPDATE `surveys` SET survey_link = ? WHERE survey_id = ?;");
        $prepared->bind_param('ss', $url, $inserted_id);
        $prepared->execute();

        $prepared = $link->prepare("INSERT INTO `results` (result_surveylink) VALUES  (?);");
        $prepared->bind_param('s', $url);
        $prepared->execute();

        mysqli_close($link);
        return $add_error;
    }

}
