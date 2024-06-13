<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_contact = $_GET['c'];

    $getStoreVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = $id_contact ORDER BY id_voucher DESC");

    while ($rowGetStoreVoucher = $getStoreVoucher->fetch_array(MYSQLI_ASSOC)) {
        $getStoreVoucherArray[] = $rowGetStoreVoucher;
    }

    if ($getStoreVoucherArray == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $getStoreVoucherArray));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_voucher'])) {
        $id_voucher = $_POST['id_voucher'];
        $no_fisik = $_POST['no_fisik'];
        $id_user = $_POST['id_user'];

        // $cekNoFisik = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE no_fisik = '$cekNoFisik'");
        // $rowCekNoFisik = $cekNoFisik->fetch_array(MYSQLI_ASSOC);

        // if ($rowCekNoFisik == null) {
        $updateVoucher = mysqli_query($conn, "UPDATE tb_voucher SET no_fisik = '$no_fisik' WHERE id_voucher = '$id_voucher'");

        if ($updateVoucher) {
            $getVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_voucher = '$id_voucher'");
            $voucherArr = $getVoucher->fetch_array(MYSQLI_ASSOC);

            $id_contact = $voucherArr['id_contact'];
            $visitDate = date("Y-m-d H:i:s");

            $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'voucher'");

            $getAllVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0");
            $voucherCodes = '';
            $distance_visit = '0.001';
            while ($rowAllVoucher = $getAllVoucher->fetch_array(MYSQLI_ASSOC)) {
                $allVoucherArray[] = $rowAllVoucher;
            }


            if ($allVoucherArray != null) {
                foreach ($allVoucherArray as $vc_array) {
                    $voucherCodes .= $vc_array['no_voucher'] . ",";
                }

                $laporan_visit = '[renvisales] Kirim voucher fisik ' . $voucherCodes;

                $cekVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE laporan_visit = '$laporan_visit' AND id_contact = '$id_contact'");
                $rowVisit = $cekVisit->fetch_array(MYSQLI_ASSOC);

                if ($rowVisit == null) {
                    $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_contact, $distance_visit, '$laporan_visit','renvisales', $id_user)");
                }
            }

            $response = ["response" => 200, "status" => "success", "message" => "Berhasil konfirmasi voucher!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal konfirmasi voucher!", "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
        // } else {
        //     $response = ["response" => 200, "status" => "failed", "message" => "Kode voucher fisik sudah terdaftar!"];
        //     echo json_encode($response);
        // }
    } else {
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
}
