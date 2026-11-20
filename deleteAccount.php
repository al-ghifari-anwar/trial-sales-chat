<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id_user'])) {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data user!. "];
        echo json_encode($response);
        die;
    }

    $id_user = $_POST['id_user'];

    $result = mysqli_query($conn, "DELETE FROM tb_user WHERE id_user = $id_user");

    if ($result) {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menghapus data anda!"];
        echo json_encode($response);
        die;
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal menghapus data anda!. "];
        echo json_encode($response);
        die;
    }
}
