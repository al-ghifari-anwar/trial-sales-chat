<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['c'])) {
        $id_city = $_GET['c'];

        $result = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id_city = '$id_city'");

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

        $result = mysqli_query($conn, "SELECT * FROM tb_produk JOIN tb_city ON tb_city.id_city = tb_produk.id_city WHERE id_distributor = '$id_distributor'");

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
    $response = ["response" => 404, "status" => "failed", "message" => "Not found!"];
    echo json_encode($response);
}
