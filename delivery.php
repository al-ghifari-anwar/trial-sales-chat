<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        // 
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier JOIN tb_contact ON tb_contact.id_contact = tb_delivery.id_contact WHERE id_delivery = '$id'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else if (isset($_GET['id_courier'])) {
        $id_courier = $_GET['id_courier'];
        $dateNow = date("Y-m-d");

        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier JOIN tb_contact ON tb_contact.id_contact = tb_delivery.id_contact WHERE tb_delivery.id_courier = '$id_courier' AND DATE(startDatetime) = '$dateNow'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else if (isset($_GET['c'])) {
        $id_city = $_GET['c'];
        $dateNow = date("Y-m-d");

        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier JOIN tb_contact ON tb_contact.id_contact = tb_delivery.id_contact WHERE tb_user.id_city = '$id_city' AND DATE(startDatetime) = '$dateNow'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else if (isset($_GET['dst'])) {
        $id_distributor = $_GET['dst'];
        $dateNow = date("Y-m-d");

        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier JOIN tb_contact ON tb_contact.id_contact = tb_delivery.id_contact WHERE tb_user.id_distributor = '$id_distributor' AND DATE(startDatetime) = '$dateNow'");

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
        // echo "AWDA";
        $result = mysqli_query($conn, "SELECT * FROM tb_delivery JOIN tb_user ON tb_user.id_user = tb_delivery.id_courier JOIN tb_contact ON tb_contact.id_contact = tb_delivery.id_contact");

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
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data delivery!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
