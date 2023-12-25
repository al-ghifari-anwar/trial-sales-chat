<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $jmlVoucher = $_GET['j'];
    $id_contact = $_GET['s'];

    for ($i = 0; $i < $jmlVoucher; $i++) {
        $no_voucher = rand(00001, 99999);

        $cekVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE no_voucher = '$no_voucher'");
        $rowVoucher = $cekVoucher->fetch_array(MYSQLI_ASSOC);
        $exp_date = date("Y-m-d", strtotime("+30 days"));

        if (!$rowVoucher) {
            $insert = mysqli_query($conn, "INSERT INTO tb_voucher(id_contact,no_voucher,point_voucher,exp_date) VALUES($id_contact,'$no_voucher',1,'$exp_date')");
        }
    }

    $response = ["response" => 200, "status" => "ok", "message" => $jmlVoucher . " voucher telah berhasil diinput"];
    echo json_encode($response);
}
