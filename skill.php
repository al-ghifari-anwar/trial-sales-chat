<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_skill WHERE id_skill = '$id'");

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
        $result = mysqli_query($conn, "SELECT * FROM tb_skill ");

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
        $kode_skill = $_POST['kode_skill'];
        $nama_skill = $_POST['nama_skill'];

        $result = mysqli_query($conn, "UPDATE tb_skill SET kode_skill = '$kode_skill', nama_skill = '$nama_skill' WHERE id_skill = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Succes updating skill data!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Failed updating skill!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $kode_skill = $_POST['kode_skill'];
        $nama_skill = $_POST['nama_skill'];

        $result = mysqli_query($conn, "INSERT INTO tb_skill(kode_skill, nama_skill) VALUES('$kode_skill', '$nama_skill')");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Succes insert new skill!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Failed insert new skill!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
        mysqli_close($conn);
    }
}
