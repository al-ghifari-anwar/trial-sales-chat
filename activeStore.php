<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $year = date("Y");

    $resultActive = mysqli_query($conn, "SELECT MONTH(created_at) AS month_active FROM tb_status_change WHERE status_from != 'active' AND status_to = 'active' AND YEAR(created_at) = '$year' GROUP BY MONTH(created_at)");

    while ($rowActive = $resultActive->fetch_array(MYSQLI_ASSOC)) {
        $month = $rowActive['month_active'];
        $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active FROM (SELECT id_contact FROM tb_status_change WHERE status_from != 'active' AND status_to = 'active' AND YEAR(created_at) = '$year' AND MONTH(created_at) = '$month' GROUP BY id_contact) t");
        $rowActive['jml_active'] = $month;
        $arrayActive[] = $rowActive;
    }

    if ($arrayActive == null) {
        echo json_encode(array("status" => "failed", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayActive));
    }
}
