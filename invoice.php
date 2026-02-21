<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_contact']) && !isset($_GET['status'])) {
        $id_contact = $_GET['id_contact'];

        $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan WHERE tb_surat_jalan.id_contact = '$id_contact' ORDER BY date_invoice DESC");

        while ($row = $resultInv->fetch_array(MYSQLI_ASSOC)) {
            $invArray[] = $row;
        }

        mysqli_close($conn);

        if ($invArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $invArray));
        }
    } else if (isset($_GET['status'])) {
        $id_contact = $_GET['id_contact'];
        $status_invoice = $_GET['status'];

        $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan WHERE tb_surat_jalan.id_contact = '$id_contact' AND status_invoice = '$status_invoice' ORDER BY date_invoice DESC");

        while ($row = $resultInv->fetch_array(MYSQLI_ASSOC)) {
            $invArray[] = $row;
        }

        mysqli_close($conn);

        if ($invArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $invArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_surat_jalan'])) {

        $id_surat_jalan = $_POST['id_surat_jalan'];

        $resultSuratJalan = mysqli_query($conn, "SELECT tb_surat_jalan.*, tb_user.full_name AS courier_name, tb_kendaraan.nama_kendaraan, tb_kendaraan.nopol_kendaraan, tb_contact.nama, tb_contact.address, tb_contact.nomorhp FROM tb_surat_jalan JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier JOIN tb_kendaraan ON tb_kendaraan.id_kendaraan = tb_surat_jalan.id_kendaraan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact WHERE id_surat_jalan = '$id_surat_jalan' ");

        $rowSuratJalan = $resultSuratJalan->fetch_array(MYSQLI_ASSOC);

        if ($rowSuratJalan == null) {
            $response = ["response" => 200, "status" => "failed", "message" => "Surat jalan not found!"];
            echo json_encode($response);
        } else {
            // $countInvoice = mysqli_query($conn, "SELECT MAX(no_invoice) AS no_invoice, MAX(id_invoice) FROM tb_invoice");

            // $rowInvoice = $countInvoice->fetch_array(MYSQLI_ASSOC);
            // if ($rowInvoice['no_invoice'] != null) {
            //     $no = substr($rowInvoice['no_invoice'], strrpos($rowInvoice['no_invoice'], '/') + 1);
            //     echo $no;
            // } else {
            //     $no = 1;
            // }
            $is_cod = $rowSuratJalan['is_cod'];

            // if ($is_cod != 1) {
            $no = $rowSuratJalan['id_surat_jalan'];

            $getSubTotals = mysqli_query($conn, "SELECT SUM(amount) AS subtotal FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");

            $rowSubTotals = $getSubTotals->fetch_array(MYSQLI_ASSOC);

            $getNotFreeItem = mysqli_query($conn, "SELECT SUM(qty_produk) AS jmlItem FROM tb_detail_surat_jalan JOIN tb_produk ON tb_produk.id_produk = tb_detail_surat_jalan.id_produk JOIN tb_master_produk ON tb_master_produk.id_master_produk = tb_produk.id_master_produk WHERE id_surat_jalan = '$id_surat_jalan' AND is_bonus = 0 AND tb_master_produk.is_default_potongan = 1 GROUP BY id_surat_jalan");

            $rowNotFreeItem = $getNotFreeItem->fetch_array(MYSQLI_ASSOC);

            if ($is_cod == 1) {
                $jmlItemDiskon = $rowNotFreeItem['jmlItem'];
                $potonganCod = 2000 * $jmlItemDiskon;
            } else {
                $potonganCod = 0;
            }

            // Begin Cross Sell Potongan
            $getNotDefaultPotongan = mysqli_query($conn, "SELECT SUM(qty_produk) AS jmlItem, potongan_cod AS total_potongan FROM tb_detail_surat_jalan JOIN tb_produk ON tb_produk.id_produk = tb_detail_surat_jalan.id_produk JOIN tb_master_produk ON tb_master_produk.id_master_produk = tb_produk.id_master_produk WHERE id_surat_jalan = '$id_surat_jalan' AND is_bonus = 0 AND tb_master_produk.is_default_potongan = 0 GROUP BY id_surat_jalan");

            $rowNotDefaultPotongan = $getNotDefaultPotongan->fetch_array(MYSQLI_ASSOC);
            $potonganCrossSelling = $rowNotDefaultPotongan['total_potongan'] * $rowNotDefaultPotongan['total_potongan'];
            // End Cross Sell Potongan

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

            $nominal = $rowSubTotals['subtotal'] - $potonganCod - $potonganCrossSelling;

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

            $checkInv = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE no_invoice = '$no_invoice'");
            $rowCheckInv = $checkInv->fetch_array(MYSQLI_ASSOC);

            if ($rowCheckInv) {
                $response = ["response" => 200, "status" => "failed", "message" => "Invoice already exist!"];
                echo json_encode($response);
            } else {
                $resultInvoice = mysqli_query($conn, "INSERT INTO tb_invoice(id_surat_jalan,no_invoice,date_invoice,bill_to_name,bill_to_address,bill_to_phone,subtotal_invoice,total_invoice) VALUES($id_surat_jalan, '$no_invoice', '$date_invoice', '$bill_to_name', '$bill_to_address', '$bill_to_phone', $subtotal_invoice, $total_invoice)");

                if ($resultInvoice) {
                    $id_invoice = mysqli_insert_id($conn);
                    // Send Notif
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://order.topmortarindonesia.com/notif/invoice',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('id_invoice' => $id_invoice),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $response = ["response" => 200, "status" => "success", "message" => "Succes creating invoice!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed creating invoice!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            }
            // } else {
            //     $response = ["response" => 200, "status" => "success", "message" => "Closing berhasil!"];
            //     echo json_encode($response);
            // }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "No cURL Postfield", "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
} else {
    $response = ["response" => 200, "status" => "failed", "message" => "Method not defined", "detail" => mysqli_error($conn)];
    echo json_encode($response);
}
