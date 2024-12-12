<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $year = date("Y");

    if (isset($_GET['city'])) {
        $id_city = $_GET['city'];

        $resultActive = mysqli_query($conn, "SELECT MONTH(tb_status_change.created_at) AS month_active FROM tb_status_change JOIN tb_contact ON tb_contact.id_contact = tb_status_change.id_contact WHERE status_to = 'active' AND id_city = '$id_city' AND YEAR(tb_status_change.created_at) = '$year' GROUP BY MONTH(tb_status_change.created_at)");


        while ($rowActive = $resultActive->fetch_array(MYSQLI_ASSOC)) {
            $month = $rowActive['month_active'];
            $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active FROM (SELECT tb_status_change.id_contact FROM tb_status_change JOIN tb_contact ON tb_contact.id_contact = tb_status_change.id_contact WHERE status_to = 'active' AND id_city = '$id_city' AND YEAR(tb_status_change.created_at) = '$year' AND MONTH(tb_status_change.created_at) = '$month' GROUP BY id_contact) t");
            $rowCount = $countActive->fetch_array(MYSQLI_ASSOC);
            $rowActive['jml_active'] = $rowCount['jml_active'];
            $arrayActive[] = $rowActive;
        }

        if ($arrayActive == null) {
            echo json_encode(array("status" => "failed", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $arrayActive));
        }
    } else {
        $id_dist = $_GET['dst'];

        $resultActive = mysqli_query($conn, "SELECT MONTH(tb_status_change.created_at) AS month_active FROM tb_status_change JOIN tb_contact ON tb_contact.id_contact = tb_status_change.id_contact JOIN tb_city ON tb_contact.id_city = tb_city.id_city WHERE status_to = 'active' AND id_distributor = '$id_dist' AND YEAR(tb_status_change.created_at) = '$year' GROUP BY MONTH(tb_status_change.created_at)");

        while ($rowActive = $resultActive->fetch_array(MYSQLI_ASSOC)) {
            $month = $rowActive['month_active'];
            $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active FROM (SELECT id_contact FROM tb_status_change JOIN tb_contact ON tb_contact.id_contact = tb_status_change.id_contact JOIN tb_city ON tb_contact.id_city = tb_city.id_city WHERE status_to = 'active' AND id_distributor = '$id_dist' AND YEAR(tb_status_change.created_at) = '$year' AND MONTH(tb_status_change.created_at) = '$month' GROUP BY tb_status_change.id_contact) t");
            $rowCount = $countActive->fetch_array(MYSQLI_ASSOC);
            $rowActive['jml_active'] = $rowCount['jml_active'];
            $arrayActive[] = $rowActive;
        }

        if ($arrayActive == null) {
            echo json_encode(array("status" => "failed", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $arrayActive));
        }
    }
}
