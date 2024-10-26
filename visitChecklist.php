<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['v'])) {
        $id_visit = $_GET['v'];

        $getVisitAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' GROUP BY text_answer");

        // echo mysqli_error($conn);

        while ($rowVisitAnswer = $getVisitAnswer->fetch_array(MYSQLI_ASSOC)) {
            $text_question = $rowVisitAnswer['text_question'];
            $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' AND text_question = '$text_question'");
            $answerArray = array();
            while ($rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC)) {
                $answerArray[] = $rowAnswer['text_answer'];
            }
            $rowVisitAnswer['answers'] = $answerArray;
            $visitAnswerArray[] = $rowVisitAnswer;
        }

        if ($visitAnswerArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $visitAnswerArray));
        }
    }
}
