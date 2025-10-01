<?php
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

        $insertActive = mysqli_query($conn, "INSERT INTO tb_active_store(month_active,jml_active,id_city,id_distributor,updated_at) VALUES('$month',$jml_active,$id_city,$id_distributor,'$updated_at')");

        if ($insertActive) {
            echo json_encode(array("status" => "ok", "results" => "Sukses"));
        } else {
            echo json_encode(array("status" => "failed", "results" => mysqli_error($conn)));
        }
    }
}
