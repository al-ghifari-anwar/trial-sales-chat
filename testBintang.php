<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $_POST;

    $material = $post['material'];
    $jam = date("H:i", strtotime($post['jam']));
    $tanggal = date("Y-m-d", strtotime($post['tanggal']));
    $hasil = $post['hasil'];

    $data = [
        'id' => $id,
        'jam' => date("H:i", strtotime($jam)),
        'tanggal' => date("Y-m-d", strtotime($tanggal)),
        'hasil' => $hasil
    ];

    $insert = mysqli_query($conn, "INSERT INTO tb_penimbangan(material_penimbangan,jam_penimbangan,tgl_penimbangan,hasil_penimbangan) VALUES('$material','$jam','$tanggal','$hasil')");

    if ($insert) {
        $response = ["code" => 200, "status" => "ok", "message" => "Success input data", "data" => $data];
        echo json_encode($response);
    } else {
        $response = ["code" => 400, "status" => "failed", "message" => "Failed input data", "data" => $data];
        echo json_encode($response);
    }
} else {
    $response = ["code" => 404, "status" => "failed", "message" => "Not Found", "detail" => "Wrong request method"];
    echo json_encode($response);
}
