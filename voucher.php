<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_vouchers = explode(",", $_POST['no_voucher']);
    $id_contact = $_POST['id_contact'];
    $point_voucher = 1;

    $voucher_bermasalah = '';
    $status = 'good';

    foreach ($no_vouchers as $cekLength) {
        $cekLength = str_replace(' ', '', $cekLength);
        if (strlen($cekLength) != 5) {
            $voucher_bermasalah .= $cekLength . ", ";
            $status = 'problem';
        }
    }

    $no = 0;

    foreach ($no_vouchers as $no_voucher) {
        $no_voucher = str_replace(' ', '', $no_voucher);
        if (strlen($no_voucher) == 5) {
            $cekVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE no_voucher = '$no_voucher'");
            $rowCekVoucher = $cekVoucher->fetch_array(MYSQLI_ASSOC);

            if ($rowCekVoucher == null) {
                $insert = mysqli_query($conn, "INSERT INTO tb_voucher(id_contact,no_voucher,point_voucher) VALUES($id_contact,'$no_voucher',$point_voucher)");
            }
        }
    }

    if ($status == 'good') {
        $response = ["response" => 200, "status" => "success", "message" => "Kode voucher berhasil tersimpan!"];
        echo json_encode($response);
    } else if ($status == 'problem') {
        $response = ["response" => 200, "status" => "success", "message" => "Beberapa kode voucher berhasil tersimpan, kode $voucher_bermasalah tidak tersimpan karena tidak memenuhi syarat 5 karakter!"];
        echo json_encode($response);
    }
}
