<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getMaxVoucher = mysqli_query($conn, "SELECT MAX(id_voucher) as id_voucher FROM tb_voucher GROUP BY id_contact");
    while ($rowMaxVoucher = $getMaxVoucher->fetch_array(MYSQLI_ASSOC)) {
        $arrMaxVouhcer[] = $rowMaxVoucher;
    }

    foreach ($arrMaxVouhcer as $maxVoucher) {
        $id_voucher = $maxVoucher['id_voucher'];
        $getVouhcer = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_voucher = '$id_voucher'");
        $rowVoucher = $getVouhcer->fetch_array(MYSQLI_ASSOC);

        $id_contact = $rowVoucher['id_contact'];

        $getContact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id_contact'");
        $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);

        if (date("Y-m-d", strtotime($rowVoucher['exp_date'])) < date("Y-m-d")) {

            $removeRenvi = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1 WHERE id_contact = '$id' AND type_rencana = 'voucher'");

            if ($removeRenvi) {
                $response = ["response" => 200, "status" => "ok", "message" => "Vouhcer telah expired!", "detail" => "ID:" . $id_contact . "| NAME:" . $rowContact['nama']];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit!", "detail" => mysqli_error($conn)];
                echo json_encode($response);
            }
        } else {
            if ($rowVoucher['is_claimed'] == 1) {
                $removeRenvi = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1 WHERE id_contact = '$id' AND type_rencana = 'voucher'");

                if ($removeRenvi) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Vouhcer telah claim!", "detail" => "ID:" . $id_contact . "| NAME:" . $rowContact['nama']];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "ok", "message" => "Vouhcer masih renvi!"];
                echo json_encode($response);
            }
        }
    }
}
