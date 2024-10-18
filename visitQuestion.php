<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getQuestion = mysqli_query($conn, "SELECT * FROM tb_visit_question");

    while ($rowQuestion = $getQuestion->fetch_array(MYSQLI_ASSOC)) {
        $options = $rowQuestion['answer_option'];
        if ($options != null) {
            $options = explode(",", $options);
        }
        $rowQuestion['answer_option'] = $options;
        $arrayQuestion[] = $rowQuestion;
    }

    if ($arrayQuestion == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayQuestion));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_visit = $_POST['id_visit'];
    $answers = $_POST['array_answer'];
    $answers = json_decode($answers);

    // echo $answers;
    // die;

    foreach ($answers as $answer) {
        $id_question = $answer['id_visit_question'];
        echo $id_question;
        die;

        $getQuestion = mysqli_query($conn, "SELECT * FROM tb_visit_question WHERE id_visit_question = '$id_question'");
        $rowQuestion = $getQuestion->fetch_array(MYSQLI_ASSOC);

        $answer_type = $rowQuestion['answer_type'];
        $text_question = $rowQuestion['text_question'];

        if ($answer_type == 'text' || $answer_type == 'radio' || $answer_type == 'date') {
            $text_answer = $answer['text_answer'];

            $insertAnswer = mysqli_query($conn, "INSERT INTO tb_visit_answer(id_question,text_question,text_answer,id_visit) VALUES($id_question,'$text_question','$text_answer',$id_visit)");

            if ($insertAnswer) {
                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim jawaban!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim jawaban!", "detail" => mysqli_error($conn)];
                echo json_encode($response);
            }
        } else if ($answer_type == 'checkbox') {
            $selected_answers = $answer['selected_answer'];

            foreach ($selected_answers as $selected_answer) {

                $insertAnswer = mysqli_query($conn, "INSERT INTO tb_visit_answer(id_question,text_question,text_answer,id_visit) VALUES($id_question,'$text_question','$selected_answer',$id_visit)");

                if ($insertAnswer) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim jawaban!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim jawaban!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim jawaban!", "detail" => "Tipe jawaban tidak ada"];
            echo json_encode($response);
        }
    }

    // $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim jawaban!"];
    // echo json_encode($response);
}
