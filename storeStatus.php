<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['c'])) {
        $id_city = $_GET['c'];
        $statusCount = mysqli_query($conn, "SELECT COUNT(*) AS jml_store, store_status FROM tb_contact WHERE id_city = '$id_city' GROUP BY store_status");
    } else {
        $statusCount = mysqli_query($conn, "SELECT COUNT(*) AS jml_store, store_status FROM tb_contact GROUP BY store_status");
    }

    while ($rowStatusCount = $statusCount->fetch_array(MYSQLI_ASSOC)) {
        $statusCountArray[] = $rowStatusCount;
    }

    if ($statusCountArray == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $statusCountArray));
    }
}
