<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM tb_user JOIN tb_city ON tb_user.id_city = tb_city.id_city WHERE username = '$username' ");

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $transArray[] = $row;
    }

    mysqli_close($conn);

    if ($transArray == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        if ($password == $transArray[0]['password']) {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        } else {
            $response = ["response" => 200, "status" => "fail", "message" => "Wrong Password!"];
            echo json_encode($response);
        }
    }
} else {
    $response = ["response" => 200, "status" => "fail", "message" => "Wrong Request Method!"];
    echo json_encode($response);
}