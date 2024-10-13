<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getQuestion = mysqli_query($conn, "SELECT * FROM tb_visit_question");

    while ($rowQuestion = $getQuestion->fetch_array(MYSQLI_ASSOC)) {
        $arrayQuestion[] = $rowQuestion;
    }

    if ($arrayQuestion == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayQuestion));
    }
}
