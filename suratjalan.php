<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['p'] == 1) {
        $id_courier = $_GET['cr'];

        $resultStore = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact IN (SELECT id_contact FROM tb_surat_jalan WHERE id_courier = '$id_courier')");

        while ($row = $resultStore->fetch_array(MYSQLI_ASSOC)) {
            $storeArray[] = $row;
        }

        if ($storeArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $storeArray));
        }
    } else if ($_GET['p'] == 2) {
        $id_contact = $_GET['str'];

        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan WHERE id_contact = '$id_contact'");

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

        $resultSuratJalan = mysqli_query($conn, "SELECT tb_surat_jalan.*, tb_user.full_name AS courier_name, tb_kendaraan.nama_kendaraan, tb_kendaraan.nopol_kendaraan FROM tb_surat_jalan JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier JOIN tb_kendaraan ON tb_kendaraan.id_courier = tb_surat_jalan.id_courier WHERE id_surat_jalan = '$id_surat_jalan' ");

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
}