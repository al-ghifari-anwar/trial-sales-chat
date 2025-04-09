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
            $nominal = 0;
            $is_cod = $rowSuratJalan['is_cod'];

            $no = $rowSuratJalan['id_surat_jalan'];
            $id_surat_jalan = $rowSuratJalan['id_surat_jalan'];

            $getSubTotals = mysqli_query($conn, "SELECT SUM(amount) AS subtotal FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");

            $rowSubTotals = $getSubTotals->fetch_array(MYSQLI_ASSOC);

            $getNotFreeItem = mysqli_query($conn, "SELECT SUM(qty_produk) AS jmlItem FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan' AND is_bonus = 0 GROUP BY id_surat_jalan");

            $rowNotFreeItem = $getNotFreeItem->fetch_array(MYSQLI_ASSOC);

            echo "Nominal awal: " . $rowSubTotals['subtotal'];

            if ($is_cod == 1) {
                $jmlItemDiskon = $rowNotFreeItem['jmlItem'];
                $potonganCod = 2000 * $jmlItemDiskon;
            } else {
                $potonganCod = 0;
            }

            echo "Potongan: " . $potonganCod;

            $id_surat_jalan = $rowSuratJalan['id_surat_jalan'];
            $no_invoice = date("Y") . "/" . "TM" . "/" . "INV" . "/" . $no;
            $date_invoice = date("Y-m-d H:i:s", strtotime($rowSuratJalan['date_closing']));
            $bill_to_name = $rowSuratJalan['nama'];
            $bill_to_address = $rowSuratJalan['address'];
            $bill_to_phone = $rowSuratJalan['nomorhp'];
            // UniqueNumber
            $numberArray = array();

            $checkNumber = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE status_invoice = 'waiting' AND total_invoice > 0 ORDER BY total_invoice DESC");

            while ($rowNumber = $checkNumber->fetch_array(MYSQLI_ASSOC)) {
                $numberArray[] = $rowNumber;
            }

            $nominal = $rowSubTotals['subtotal'] - $potonganCod;

            echo "Nominal after potongan: " . $nominal;

            // echo json_encode($numberArray);
            $pengurangan = 1;
            if ($numberArray != null) {
                foreach ($numberArray as $numberArray) {
                    // echo "Nominal Skrg: " . ($nominal - $pengurangan) . "\n";
                    // echo "Nominal DB: " . $numberArray['total_invoice'] . "\n";
                    if ($numberArray['total_invoice'] == ($nominal - $pengurangan)) {
                        $pengurangan = $pengurangan + 1;
                    }
                }
            }

            $subtotal_invoice = $nominal - $pengurangan;

            if ($subtotal_invoice <= 0) {
                $subtotal_invoice = 0;
                $total_invoice = $subtotal_invoice;
            } else {
                $total_invoice = $subtotal_invoice;
            }

            // echo $no;

            $checkInv = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE id_surat_jalan = '$no'");
            $rowCheckInv = $checkInv->fetch_array(MYSQLI_ASSOC);

            if ($rowCheckInv) {
                $response = ["response" => 200, "status" => "failed", "message" => "Invoice already exist!"];
                echo json_encode($response);
            } else {
                $date_jatem = date('Y-m-d', strtotime("+" . $invArray['termin_payment'] . " days", strtotime($date_invoice)));

                $resultInvoice = mysqli_query($conn, "INSERT INTO tb_invoice(id_surat_jalan,no_invoice,date_invoice,bill_to_name,bill_to_address,bill_to_phone,subtotal_invoice,total_invoice,date_jatem) VALUES($id_surat_jalan, '$no_invoice', '$date_invoice', '$bill_to_name', '$bill_to_address', '$bill_to_phone', $subtotal_invoice, $total_invoice, $date_jatem)");

                if ($resultInvoice) {
                    $response = ["response" => 200, "status" => "success", "message" => "Succes creating invoice!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed creating invoice!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            }
        }
    }
}
