<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active, id_city FROM tb_contact WHERE store_status = 'active' GROUP BY tb_contact.id_city");

    while ($rowCountActive = $countActive->fetch_array(MYSQLI_ASSOC)) {
        $arrayCountActive[] = $rowCountActive;
    }

    if ($arrayCountActive == null) {
        echo json_encode(array("status" => "failed", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayCountActive));
    }
}
