<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['mo'])) {
        $month = $_GET['mo'];

        $resultActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active FROM tb_status_change WHERE status_from = 'passive' AND status_to = 'active' AND month(created_at) = '$month'");
        $rowActive = $resultActive->fetch_array(MYSQLI_ASSOC);

        if ($rowActive == null) {
            echo json_encode(array("status" => "failed", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $rowActive));
        }
    } else {
        $resultActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active FROM tb_status_change WHERE status_from = 'passive' AND status_to = 'active'");
        $rowActive = $resultActive->fetch_array(MYSQLI_ASSOC);

        if ($rowActive == null) {
            echo json_encode(array("status" => "failed", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $rowActive));
        }
    }
}
