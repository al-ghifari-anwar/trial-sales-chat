<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_city = $_GET['id_city'];
    $id_user = $_GET['id_user'];

    $city = mysqli_query($conn, " SELECT * FROM tb_city WHERE id_city = '$id_city' ")->fetch_array(MYSQLI_ASSOC);

    $nama_city = $city['nama_city'][0] . " X";

    $cityX = mysqli_query($conn, " SELECT * FROM tb_city WHERE nama_city = '$nama_city'")->fetch_array(MYSQLI_ASSOC);

    $id_city_x = $cityX['id_city'];

    $key = isset($_GET['key']) ? $_GET['key'] : '';

    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $getContacts = mysqli_query($conn, "SELECT * FROM tb_contact WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND tb_contact.id_city = '$id_city_x' AND store_status = '$status'");
    } else {
        $getContacts = mysqli_query($conn, "SELECT * FROM tb_contact WHERE (nama LIKE '%$key%' OR nomorhp LIKE '%$key%') AND tb_contact.id_city = '$id_city_x'");
    }

    while ($rowContacts = $getContacts->fetch_array(MYSQLI_ASSOC)) {
        $contacts[] = $rowContacts;
    }

    if ($contacts == null) {
        echo json_encode(array("status" => "empty", "results" => []));
        die;
    } else {
        echo json_encode(array("status" => "ok", "results" => $contacts));
    }
}
