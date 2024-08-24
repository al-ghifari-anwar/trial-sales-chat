<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getVouchers = mysqli_query($conn, "SELECT id_contact, MAX(date_voucher) AS date_voucher, MAX(exp_date) AS exp_date, no_fisik FROM tb_voucher tv WHERE exp_date <= NOW() GROUP BY id_contact");

    while ($rowVouchers = $getVouchers->fetch_array(MYSQLI_ASSOC)) {
        $vouchersArray[] = $rowVouchers;
    }

    if ($vouchersArray != null) {
        foreach ($vouchersArray as $voucher) {
            $id_contact = $voucher['id_contact'];

            $getContactVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher tv WHERE id_contact = '$id_contact' ORDER BY id_voucher DESC LIMIT 1");
            $rowContactVoucher = $getContactVoucher->fetch_array(MYSQLI_ASSOC);

            $is_claimed = $rowContactVoucher['is_claimed'];
            $date_voucher = $rowContactVoucher['date_voucher'];

            if ($is_claimed == 0) {
                // Send again
                $getCountVoucher = mysqli_query($conn, "SELECT SUM(*) as total_point FROM tb_voucher WHERE DATE(date_voucher) = '$date_voucher' AND id_contact = '$id_contact'");
                $rowCountVoucher = $getContactVoucher->fetch_array(MYSQLI_ASSOC);

                if ($rowCountVoucher != null) {
                    // Send
                    $total_point = $rowCountVoucher['total_point'];
                    $detail = [
                        'id_contact' => $id_contact,
                        'point_voucher' => $total_point
                    ];
                    $response = ["response" => 200, "status" => "ok", "message" => "Sent new voucher!", "detail" => $detail];
                    echo json_encode($response);
                } else {
                    $detail = [
                        'id_contact' => $id_contact
                    ];

                    $response = ["response" => 200, "status" => "failed", "message" => "Null!", "detail" => $detail];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Already claimed!"];
                echo json_encode($response);
            }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "No voucher!", "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
}
