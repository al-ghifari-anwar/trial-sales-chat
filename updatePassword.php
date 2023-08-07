<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $password = md5($_POST['password']);

    $result = mysqli_query($conn, "UPDATE tb_user SET password = '$password' WHERE id_user = '$id_user'");

    if ($result) {
        $response = ["response" => 200, "status" => "ok", "message" => "Success change password. Please login again!"];
        echo json_encode($response);
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Failed to change password. Please login again!", "error" => mysqli_error($conn)];
        echo json_encode($response);
    }
} else {
    $response = ["response" => 404, "status" => "not-found", "message" => "Request not found!"];
    echo json_encode($response);
}
