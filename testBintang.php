<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jam = $_POST['jam'];
    $tanggal = $_POST['tanggal'];
    $berat = $_POST['berat'];

    $data = [
        'id' => $id,
        'jam' => $jam,
        'tanggal' => $tanggal,
        'berat' => $berat
    ];

    $response = ["code" => 200, "status" => "ok", "message" => "Success", "data" => $data];
    echo json_encode($response);
} else {
    $response = ["code" => 404, "status" => "failed", "message" => "Not Found", "detail" => "Wrong request method"];
    echo json_encode($response);
}
