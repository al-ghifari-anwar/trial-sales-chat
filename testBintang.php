<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $_POST;
    $id = $post['id'];
    $jam = $post['jam'];
    $tanggal = $post['tanggal'];
    $berat = $post['berat'];

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
