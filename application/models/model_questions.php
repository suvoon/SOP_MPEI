<?php

class Model_Questions extends Model
{
	public function getQuestions(){ //Получение изображения, имени пользователя, описания и блогов одного пользователя
        if(isset($_SESSION['link'])){
            $link = $this->dbconnect();
            $srv_link = $_SESSION['link'];
            $srv_login = $_SESSION['login'];
            $questions = array();
    
            $prepared = $link->prepare("SELECT survey_period, survey_desc, survey_content, result_userlist FROM `surveys` LEFT JOIN `results` ON surveys.survey_link = results.result_surveylink WHERE survey_link = ?;");
            $prepared->bind_param('s', $srv_link);
            $prepared->execute();
            $result = $prepared->get_result();
    
            $survey_result = $result->fetch_assoc();
            if (($survey_result['result_userlist'] == null) || (strpos($survey_result['result_userlist'], $srv_login.",") == false)){
                if (!empty($survey_result)){
                    $questions[0] = $survey_result['survey_period'];
                    $questions[1] = $survey_result['survey_content'];
                    $questions[2] = $survey_result['survey_desc'];
                }
                else{
                    mysqli_close($link);
                    header("Location: /404");
                    exit();
                }
            }
            else{
                mysqli_close($link);
                header("Location: /surveys");
                exit();
            }
        }
        else{
            mysqli_close($link);
            header("Location: /404");
            exit();
        }

        mysqli_close($link);
        return $questions;
    }

    public function submitResult($result_array){
        if(isset($_SESSION['link'])){
            $link = $this->dbconnect();
            $srv_link = $_SESSION['link'];
            $srv_login = $_SESSION['login'];
    
            $prepared = $link->prepare("SELECT * FROM `results` WHERE result_surveylink = ?;");
            $prepared->bind_param('s', $srv_link);
            $prepared->execute();
            $result = $prepared->get_result();
    
            $survey_result = $result->fetch_assoc();
            if (!empty($survey_result)){

                $count = count($result_array);
                $result_text = $srv_login."{\n";
                foreach($result_array as $key => $item){
                    if (--$count <= 0) break;
                    $result_text = $result_text."    ".$key.":".$item.";\n";
                }
                $result_text = $result_text."}\n";

                $new_temp = $survey_result['result_temp'].$result_text;
                $new_users = $survey_result['result_users']+1;
                $new_userlist = " ".$survey_result['result_userlist'].$srv_login.",";
                $prepared = $link->prepare("UPDATE results SET result_temp = ?, result_users = ?, result_userlist = ? WHERE result_surveylink = ?");
                $prepared->bind_param('ssss', $new_temp, $new_users, $new_userlist, $srv_link);
                $prepared->execute();
            }
            else{
                echo "НЕТ РЕЗУЛЬТАТА";
            }
        }
        else{
            header("Location: /404");
            exit();
        }

        mysqli_close($link);
        header("Location: /surveys");
        exit();
    }
}