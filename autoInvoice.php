<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $resultSuratJalan = mysqli_query($conn, "SELECT tb_surat_jalan.*, tb_user.full_name AS courier_name, tb_kendaraan.nama_kendaraan, tb_kendaraan.nopol_kendaraan, tb_contact.nama, tb_contact.address, tb_contact.nomorhp FROM tb_surat_jalan JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier JOIN tb_kendaraan ON tb_kendaraan.id_kendaraan = tb_surat_jalan.id_kendaraan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact WHERE is_closing = 1");

    while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
        $rowSuratJalan[] = $row;
    }
    // $rowSuratJalan = $resultSuratJalan->fetch_array(MYSQLI_ASSOC);

    if ($rowSuratJalan == null) {
        $response = ["response" => 200, "status" => "failed", "message" => "Surat jalan not found!"];
        echo json_encode($response);
    } else {
        // echo json_encode($rowSuratJalan);
        foreach ($rowSuratJalan as $rowSuratJalan) {

            $no = $rowSuratJalan['id_surat_jalan'];

            $getSubTotals = mysqli_query($conn, "SELECT SUM(amount) AS subtotal FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");

            $rowSubTotals = $getSubTotals->fetch_array(MYSQLI_ASSOC);

            $id_surat_jalan = $rowSuratJalan['id_surat_jalan'];
            $no_invoice = date("Y") . "/" . "TM" . "/" . "INV" . "/" . $no;
            $date_invoice = date("Y-m-d H:i:s", strtotime($rowSuratJalan['date_closing']));
            $bill_to_name = $rowSuratJalan['nama'];
            $bill_to_address = $rowSuratJalan['address'];
            $bill_to_phone = $rowSuratJalan['nomorhp'];
            // UniqueNumber
            $checkNumber = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE status_invoice = 'waiting' ORDER BY total_invoice DESC");

            while ($rowNumber = $checkNumber->fetch_array(MYSQLI_ASSOC)) {
                $numberArray[] = $rowNumber;
            }
            $nominal = $rowSubTotals['subtotal'];
            $pengurangan = 0;
            if ($numberArray != null) {
                foreach ($numberArray as $numberArray) {
                    // echo "Nominal Skrg: " . ($nominal - $pengurangan) . "\n";
                    // echo "Nominal DB: " . $numberArray['total_invoice'] . "\n";
                    if ($numberArray['total_invoice'] == ($nominal - $pengurangan)) {
                        $pengurangan = $pengurangan + 5;
                    }
                }
            }
            $subtotal_invoice = $nominal - $pengurangan;
            $total_invoice = $subtotal_invoice;

            // echo $no;

            $checkInv = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE no_invoice = '$no_invoice'");
            $rowCheckInv = $checkInv->fetch_array(MYSQLI_ASSOC);

            if ($rowCheckInv) {
                $response = ["response" => 200, "status" => "failed", "message" => "Invoice already exist!"];
                echo json_encode($response);
            } else {
                $resultInvoice = mysqli_query($conn, "INSERT INTO tb_invoice(id_surat_jalan,no_invoice,date_invoice,bill_to_name,bill_to_address,bill_to_phone,subtotal_invoice,total_invoice) VALUES($id_surat_jalan, '$no_invoice', '$date_invoice', '$bill_to_name', '$bill_to_address', '$bill_to_phone', $subtotal_invoice, $total_invoice)");

                if ($resultInvoice) {
                    $response = ["response" => 200, "status" => "success", "message" => "Succes creating invoice!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed creating invoice!"];
                    echo json_encode($response);
                }
            }
        }
    }
}