<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_sj = $_POST['id_sj'];

    $dateNow = date('Y-m-d H:i:s');

    $udpatePrint = mysqli_query($conn, "UPDATE tb_surat_jalan SET is_printed_inv = '$dateNow'");

    if ($udpatePrint) {
        $response = ["response" => 200, "status" => "failed", "message" => "Failed to print inv!"];
        echo json_encode($response);
        die;
    } else {
        $response = ["response" => 200, "status" => "success", "message" => "Succes to print inv!"];
        echo json_encode($response);
        die;
    }
}
