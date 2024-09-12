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
            $date_voucher = date("Y-m-d", strtotime($rowContactVoucher['date_voucher']));
            $exp_voucher = date("Y-m-d", strtotime($rowContactVoucher['exp_date']));

            if ($exp_voucher <= date("Y-m-d")) {
                // Remove Renvi
                $removeRenvi = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1 WHERE id_contact = '$id_contact' AND type_rencana = 'voucher'");

                if ($removeRenvi) {
                    // Send
                    $total_point = $rowCountVoucher['total_point'];
                    $detail = [
                        'id_contact' => $id_contact,
                        'exp_voucher' => $exp_voucher,
                        'date_voucher' => $date_voucher,
                        'id_voucher' => $rowContactVoucher['id_voucher']
                    ];
                    $response = ["response" => 200, "status" => "ok", "message" => "Renvi expired removed!", "detail" => $detail];
                    echo json_encode($response);
                } else {
                    $detail = [
                        'id_contact' => $id_contact,
                        'date_voucher' => $date_voucher
                    ];

                    $response = ["response" => 200, "status" => "failed", "message" => "Failed to remove renvi!", "detail" => $detail];
                    echo json_encode($response);
                }
            } else if ($is_claimed == 1) {
                // Remove Renvi
                $removeRenvi = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1 WHERE id_contact = '$id_contact' AND type_rencana = 'voucher'");

                if ($removeRenvi) {
                    // Send
                    $total_point = $rowCountVoucher['total_point'];
                    $detail = [
                        'id_contact' => $id_contact,
                        'exp_voucher' => $exp_voucher,
                        'date_voucher' => $date_voucher,
                        'id_voucher' => $rowContactVoucher['id_voucher']
                    ];
                    $response = ["response" => 200, "status" => "ok", "message" => "Renvi claimed removed!", "detail" => $detail];
                    echo json_encode($response);
                } else {
                    $detail = [
                        'id_contact' => $id_contact,
                        'date_voucher' => $date_voucher
                    ];

                    $response = ["response" => 200, "status" => "failed", "message" => "Failed to remove renvi!", "detail" => $detail];
                    echo json_encode($response);
                }
            } else {
                $detail = [
                    'id_contact' => $id_contact,
                    'exp_voucher' => $exp_voucher,
                    'date_voucher' => $date_voucher,
                    'id_voucher' => $rowContactVoucher['id_voucher']
                ];

                $response = ["response" => 200, "status" => "failed", "message" => "Still active!", "detail" => $detail];
                echo json_encode($response);
            }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "No voucher!", "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
}
