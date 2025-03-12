<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'];
    $id_md5 = $_POST['id_md5'];

    $voucherTukang = mysqli_query($conn, "SELECT * FROM tb_voucher_tukang WHERE id_md5 = '$id_md5'");
    $rowVoucherTukang = $voucherTukang->fetch_array(MYSQLI_ASSOC);

    $id_tukang = $rowVoucherTukang['id_tukang'];

    $result = mysqli_query($conn, "UPDATE tb_tukang SET id_user_post = '$id_user' WHERE id_tukang = '$id_tukang'");

    if ($result) {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil assign data tukang!"];
        echo json_encode($response);
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Gagal assign data tukang!"];
        echo json_encode($response);
    }
}
