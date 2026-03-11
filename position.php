<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $id_contact = $_POST['id_contact'];
    $type_position = $_POST['type_position'];
    $lat_position = $_POST['lat_position'];
    $long_position = $_POST['long_position'];

    $save = mysqli_query($conn, " INSERT INTO tb_position(id_user,id_contact,type_position,lat_position,long_position) ");

    if ($save) {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan lokasi!"];
        return json_encode($response);
    } else {
        $response = ["response" => 400, "status" => "failed", "message" => "Gagal menyimpan lokasi!"];
        return json_encode($response);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
}
