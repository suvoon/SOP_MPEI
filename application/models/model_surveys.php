<?php

class Model_Surveys extends Model
{
    public function formatDate($date){
        return substr($date, -2).".".substr($date, -5, 2).".".substr($date, 0, 4);
    }

	public function getSurveys(){
        $link = $this->dbconnect();
        $login = $_SESSION['login'];
        $surveys = array();
        $i = 0;

        $prepared = $link->prepare("SELECT survey_period, survey_startdate, survey_enddate, survey_link, survey_status, result_userlist FROM `surveys` LEFT JOIN `results` ON surveys.survey_link = results.result_surveylink ORDER BY survey_id DESC");
        $prepared->execute();
        $result = $prepared->get_result(); 

        $srv_res = $result->fetch_assoc();
        while (!empty($srv_res)){

            $list = $srv_res['result_userlist'];
            $list == null ? $ifpassed = false : $ifpassed = strpos($list, $login.",");

            $ifpassed == false ? $srv_status = "<a href='/questions/".$srv_res['survey_link']."'>Выполнить</a>" : $srv_status = "Опрос завершён, анкета заполнена";
            $surveys[$i] = [$srv_res['survey_period'], $this->formatDate($srv_res['survey_startdate']), $this->formatDate($srv_res['survey_enddate']), $srv_status];

            $srv_res = $result->fetch_assoc();
            $i++;
        }

        mysqli_close($link);
        return $surveys;
    }

}