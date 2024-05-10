<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['contact'])) {
        $id_contact = $_GET['contact'];

        $getSales = mysqli_query($conn, "SELECT * FROM tb_bid JOIN tb_contact ON tb_contact.id_contact = tb_bid.id_contact JOIN tb_user ON tb_user.id_user = tb_bid.id_user JOIN tb_action_bid ON tb_action_bid.id_bid = tb_bid.id_bid WHERE tb_bid.id_contact = '$id_contact' AND field_action_bid = 'Send new message' ORDER BY tb_bid.id_bid LIMIT 1");

        // while ($rowSales = $getSales->fetch_array(MYSQLI_ASSOC)) {
        //     $salesArray[] = $rowSales;
        // }
        $salesArray = $getSales->fetch_array(MYSQLI_ASSOC);

        if ($salesArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $salesArray));
        }
    }
}
