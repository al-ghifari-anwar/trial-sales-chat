<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['p'] == 1) {
        $id_courier = $_GET['cr'];

        // $resultStore = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact IN (SELECT id_contact FROM tb_surat_jalan WHERE id_courier = '$id_courier' AND tb_surat_jalan.is_closing = 0)");
        $resultStore = mysqli_query($conn, "SELECT * id_surat_jalan FROM tb_contact JOIN tb_surat_jalan ON tb_surat_jalan.id_contact = tb_contact.id_contact WHERE tb_surat_jalan.id_courier = '$id_courier' AND tb_surat_jalan.is_closing = 0");

        while ($row = $resultStore->fetch_array(MYSQLI_ASSOC)) {
            // $id_surat_jalan = $row['id_surat_jalan'];
            // $getSj = mysqli_query($conn, "SELECT * FROM tb_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");
            // $rowSj = $getSj->fetch_array(MYSQLI_ASSOC);

            // $row['sj'] = $rowSj;

            $storeArray[] = $row;
        }

        if ($storeArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $storeArray));
        }
    } else if ($_GET['p'] == 2) {
        $id_contact = $_GET['str'];

        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan WHERE id_contact = '$id_contact' ORDER BY id_surat_jalan DESC");

        while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    } else if ($_GET['p'] == 3) {
        $id_surat_jalan = $_GET['sj'];

        $resultSuratJalan = mysqli_query($conn, "SELECT tb_surat_jalan.*, tb_user.full_name AS courier_name, tb_kendaraan.nama_kendaraan, tb_kendaraan.nopol_kendaraan FROM tb_surat_jalan JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier JOIN tb_kendaraan ON tb_kendaraan.id_kendaraan = tb_surat_jalan.id_kendaraan WHERE id_surat_jalan = '$id_surat_jalan' ");

        $resultDetail = mysqli_query($conn, "SELECT * FROM tb_detail_surat_jalan JOIN tb_produk ON tb_produk.id_produk = tb_detail_surat_jalan.id_produk WHERE id_surat_jalan = '$id_surat_jalan'");

        while ($row = $resultDetail->fetch_array(MYSQLI_ASSOC)) {
            $detailArray[] = $row;
        }

        while ($row = $resultSuratJalan->fetch_object()) {
            $row->details = $detailArray;
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['command'] == 'print') {
        $id_surat_jalan = $_POST['id_surat_jalan'];
        $date = date("Y-m-d H:i:s");

        $resultPrint = mysqli_query($conn, "UPDATE tb_surat_jalan SET is_printed = 1, date_printed = '$date' WHERE id_surat_jalan = '$id_surat_jalan'");

        if ($resultPrint) {
            $resultSuratJalan = mysqli_query($conn, "SELECT tb_surat_jalan.*, tb_user.full_name AS courier_name, tb_kendaraan.nama_kendaraan, tb_kendaraan.nopol_kendaraan FROM tb_surat_jalan JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier JOIN tb_kendaraan ON tb_kendaraan.id_kendaraan = tb_surat_jalan.id_kendaraan WHERE id_surat_jalan = '$id_surat_jalan' ");

            $resultDetail = mysqli_query($conn, "SELECT * FROM tb_detail_surat_jalan JOIN tb_produk ON tb_produk.id_produk = tb_detail_surat_jalan.id_produk WHERE id_surat_jalan = '$id_surat_jalan'");

            while ($row = $resultDetail->fetch_array(MYSQLI_ASSOC)) {
                $detailArray[] = $row;
            }

            while ($row = $resultSuratJalan->fetch_object()) {
                $row->details = $detailArray;
                $suratJalanArray[] = $row;
            }

            if ($suratJalanArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Failed to change status!"];
            echo json_encode($response);
        }
    } else if ($_POST['command'] == 'closing') {
        $id_surat_jalan = $_POST['id_surat_jalan'];
        $proof_closing = $_FILES['pic']['name'];
        $date = date("Y-m-d H:i:s");
        $dateFile = date("Y-m-d-H-i-s");
        $distance = $_POST['distance'];

        if (move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $dateFile . $_FILES['pic']['name'])) {
            $sourceImage = 'img/' . $dateFile . $_FILES['pic']['name'];
            $imageDestination = 'img/min-' . $dateFile . $_FILES['pic']['name'];
            $createImage = imagecreatefromjpeg($sourceImage);
            imagejpeg($createImage, $imageDestination, 60);
        }

        $imgNewName = $dateFile . $_FILES['pic']['name'];

        $resultPrint = mysqli_query($conn, "UPDATE tb_surat_jalan SET is_closing = 1, date_closing = '$date', proof_closing = 'min-$imgNewName', distance = '$distance' WHERE id_surat_jalan = '$id_surat_jalan'");

        if ($resultPrint) {
            $getSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");
            $rowSuratJalan = $getSuratJalan->fetch_array(MYSQLI_ASSOC);
            $id_contact = $rowSuratJalan['id_contact'];
            $id_surat_jalan = $rowSuratJalan['id_surat_jalan'];

            // Save record change status
            $store_status = "";
            $getContact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id_contact'");
            $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);
            $store_status = $rowContact['store_status'];
            $statusChange = mysqli_query($conn, "INSERT INTO tb_status_change(id_contact,status_from,status_to) VALUES($id_contact,'$store_status','active')");

            // Transfer Test
            $id_city = $rowContact['id_city'];
            $getCity = mysqli_query($conn, "SELECT * FROM tb_city WHERE id_city = '$id_city'");
            $rowCity = $getCity->fetch_array(MYSQLI_ASSOC);

            $getTotalQty = mysqli_query($conn, "SELECT id_surat_jalan, SUM(qty_produk) AS qty_produk FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan'");
            $rowTotalQty = $getTotalQty->fetch_array(MYSQLI_ASSOC);
            $qty = $rowTotalQty['qty_produk'];

            // if ($rowCity['norek_city'] != '' && $rowCity['bank_code'] == '') {
            //     $to_account = $rowCity['norek_city'];
            //     $curl = curl_init();

            //     curl_setopt_array($curl, array(
            //         CURLOPT_URL => "https://apibca.topmortarindonesia.com/snapIntrabank.php?qty=$qty&to=$to_account&city=$id_city&sj=$id_surat_jalan",
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => '',
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 0,
            //         CURLOPT_FOLLOWLOCATION => true,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => 'GET',
            //     ));

            //     $response = curl_exec($curl);

            //     curl_close($curl);
            // } else if ($rowCity['norek_city'] != '' && $rowCity['bank_code'] != '') {
            //     $to_account = $rowCity['norek_city'];
            //     $bank_code = $rowCity['bank_code'];
            //     $curl = curl_init();

            //     curl_setopt_array($curl, array(
            //         CURLOPT_URL => "https://apibca.topmortarindonesia.com/snapInterbankTest.php?qty=$qty&to=$to_account&city=$id_city&sj=$id_surat_jalan&to_name=Eram%20Prabowo&bank_code=$bank_code",
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => '',
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 0,
            //         CURLOPT_FOLLOWLOCATION => true,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => 'GET',
            //     ));

            //     $response = curl_exec($curl);

            //     curl_close($curl);
            // }

            if ($statusChange) {
                $changeStoreStatus = mysqli_query($conn, "UPDATE tb_contact SET store_status = 'active' WHERE id_contact = '$id_contact'");
                $date = date("Y-m-d H:i:s");

                if ($changeStoreStatus) {
                    $removeRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$date' WHERE id_contact = '$id_contact' AND type_rencana = 'passive'");

                    $response = ["response" => 200, "status" => "success", "message" => "Succes to closing!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed to change store status!"];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Status record not saved!", "detail" => mysqli_error($conn)];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Failed to closing!"];
            echo json_encode($response);
        }
    }
}
