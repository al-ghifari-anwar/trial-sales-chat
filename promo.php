<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_promo WHERE id_promo = '$id'");

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
        $result = mysqli_query($conn, "SELECT * FROM tb_promo ");

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

        $result = mysqli_query($conn, "UPDATE tb_city SET nama_city = '$nama_city', kode_city = '$kode_city' WHERE id_city = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data kota!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data kota!"];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $nama_city = $_POST['nama_city'];
        $kode_city = $_POST['kode_city'];

        $result = mysqli_query($conn, "INSERT INTO tb_city(nama_city, kode_city) VALUES('$nama_city', '$kode_city')");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data kota!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data kota!"];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
