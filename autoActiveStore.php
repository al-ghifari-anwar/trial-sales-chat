<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active, tb_contact.id_city, id_distributor FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status = 'active' GROUP BY tb_contact.id_city");

    while ($rowCountActive = $countActive->fetch_array(MYSQLI_ASSOC)) {
        $arrayCountActive[] = $rowCountActive;
    }

    foreach ($arrayCountActive as $active) {
        // $month = date('m');
        // $month = 9;
        $month = date('n');
        $jml_active = $active['jml_active'];
        $id_city = $active['id_city'];
        $id_distributor = $active['id_distributor'];
        $updated_at = date("Y-m-d H:i:s");
        $year = date('Y');

        $getActive = mysqli_query($conn, "SELECT * FROM tb_active_store WHERE month_active = '$month' AND id_city = '$id_city' AND YEAR(created_at) = '$year' ")->fetch_array(MYSQLI_ASSOC);

        if ($getActive) {
            $updateActive = mysqli_query($conn, " UPDATE tb_active_store SET jml_active = '$jml_active', updated_at = '$updated_at' WHERE month_active = '$month' AND id_city = '$id_city' AND YEAR(created_at) = '$year' ");

            if ($updateActive) {
                echo json_encode(array("status" => "ok", "results" => "Sukses"));
            } else {
                echo json_encode(array("status" => "failed", "results" => mysqli_error($conn)));
            }
        } else {
            $insertActive = mysqli_query($conn, "INSERT INTO tb_active_store(month_active,jml_active,id_city,id_distributor,updated_at) VALUES('$month',$jml_active,$id_city,$id_distributor,'$updated_at')");

            if ($insertActive) {
                echo json_encode(array("status" => "ok", "results" => "Sukses"));
            } else {
                echo json_encode(array("status" => "failed", "results" => mysqli_error($conn)));
            }
        }
    }
}
