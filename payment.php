<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_invoice'])) {
        $id_invoice = $_GET['id_invoice'];

        $resultPayment = mysqli_query($conn, "SELECT * FROM tb_payment WHERE id_invoice = '$id_invoice' ORDER BY date_payment DESC");

        while ($row = $resultPayment->fetch_array(MYSQLI_ASSOC)) {
            $paymentArray[] = $row;
        }

        mysqli_close($conn);

        if ($paymentArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $paymentArray));
        }
    }
}
