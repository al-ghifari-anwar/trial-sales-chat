<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['command'] == 'closing') {
        $id_surat_jalan = $_POST['id_surat_jalan'];
        $proof_closing = $_FILES['pic']['name'];
        $date = date("Y-m-d H:i:s");
        $dateFile = date("Y-m-d-H-i-s");
        $distance = $_POST['distance'];

        $suratjalan = mysqli_query($conn, " SELECT * FROM tb_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan' ")->fetch_array(MYSQLI_ASSOC);
        // Delivery
        $endDateTime = $_POST['endDateTime'];
        $endLat = $_POST['endLat'];
        $endLng = $_POST['endLng'];
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $startDateTime = $_POST['startDateTime'];
        $startLat = $_POST['startLat'];
        $startLng = $_POST['startLng'];
        $id_courier = $suratjalan['id_courier'];
        $id_contact = $suratjalan['id_contact'];

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

            if ($statusChange) {
                $changeStoreStatus = mysqli_query($conn, "UPDATE tb_contact SET store_status = 'active' WHERE id_contact = '$id_contact'");
                $date = date("Y-m-d H:i:s");

                if ($changeStoreStatus) {
                    $removeRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$date' WHERE id_contact = '$id_contact' AND type_rencana = 'passive'");

                    $result = mysqli_query($conn, "INSERT INTO tb_delivery(endDateTime, endLat, endLng, lat, lng, id_courier, id_contact, startDateTime, startLat, startLng, id_surat_jalan) VALUES('$endDateTime', '$endLat', '$endLng', '$lat', '$lng', $id_courier, $id_contact, '$startDateTime', '$startLat', '$startLng', '$id_surat_jalan')");

                    if ($result) {
                        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil closing & menambah data delivery!"];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Gagal closing menambah data delivery!", "detail" => mysqli_error($conn)];
                        echo json_encode($response);
                    }
                    // $response = ["response" => 200, "status" => "success", "message" => "Succes to closing!"];
                    // echo json_encode($response);
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
