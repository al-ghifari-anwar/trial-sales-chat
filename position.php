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

    $save = mysqli_query($conn, " INSERT INTO tb_position(id_user,id_contact,type_position,lat_position,long_position) VALUES($id_user,$id_contact,'$type_position','$lat_position','$long_position') ");

    if ($save) {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan lokasi!"];
        echo json_encode($response);
        die;
    } else {
        $response = ["response" => 400, "status" => "failed", "message" => "Gagal menyimpan lokasi!"];
        echo json_encode($response);
        die;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_user'])) {
        $id_user = $_GET['id_user'];
        $date = isset($_GET['date']) ? $_GET['date'] : null;

        $user = mysqli_query($conn, " SELECT * FROM tb_user WHERE id_user = '$id_user' ")->fetch_array(MYSQLI_ASSOC);

        $lastPosition = mysqli_query($conn, " SELECT * FROM tb_position WHERE id_user = '$id_user' ORDER BY created_at DESC ")->fetch_array(MYSQLI_ASSOC);

        if ($date = null) {
            $date = date('Y-m-d', strtotime($lastPosition['created_at']));
        }

        $getPositions = mysqli_query($conn, " SELECT * FROM tb_position WHERE id_user = '$id_user' AND DATE(created_at) = '$date' ")->fetch_array(MYSQLI_ASSOC);

        $positions = array();

        // while ($rowPosition = $getPositions->fetch_array(MYSQLI_ASSOC)) {
        //     $id_contact = $rowPosition['id_contact'];
        //     $contact = mysqli_query($conn, " SELECT * FROM tb_contact WHERE id_contact = '$id_contact' ")->fetch_array(MYSQLI_ASSOC);

        //     echo json_encode($rowPosition);
        //     echo "<br>";

        //     $rowPosition['toko'] = $contact ? $contact['nama'] : '';

        //     $positions[] = $rowPosition;
        // }

        $user['positions'] = $date;

        // 

        if ($user == null) {
            echo json_encode(["code" => 400, "status" => "failed", "msg" => "User not found"]);
            die;
        } else {
            echo json_encode(["code" => 200, "status" => "ok", "msg" => "Success get data", "data" => $user]);
            die;
        }
    }
}
