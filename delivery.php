<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier WHERE id_delivery = '$id'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else {
        $id_distributor = $_GET['dst'];

        $result = mysqli_query($conn, "SELECT * FROM tb_city WHERE id_distributor = '$id_distributor'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $nama_city = $_POST['nama_city'];
        $kode_city = $_POST['kode_city'];
        $id_distributor = $_POST['id_distributor'];

        $result = mysqli_query($conn, "UPDATE tb_city SET nama_city = '$nama_city', kode_city = '$kode_city', id_distributor = $id_distributor WHERE id_city = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data kota!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data kota!"];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $endDateTime = $_POST['endDateTime'];
        $endLat = $_POST['endLat'];
        $endLng = $_POST['endLng'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $id_courier = $_POST['id_courier'];
        $id_contact = $_POST['id_contact'];
        $startDateTime = $_POST['startDateTime'];
        $startLat = $_POST['startLat'];
        $startLng = $_POST['startLng'];

        $result = mysqli_query($conn, "INSERT INTO tb_delivery(endDateTime, endLat, endLng, lat, lng, id_courier, id_contact, startDateTime, startLat, startLng) VALUES('$endDateTime', '$endLat', '$endLng', '$lat', '$lng', $id_courier, $id_contact, '$startDateTime', '$startLat', '$startLng')");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data delivery!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data delivery!"];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
