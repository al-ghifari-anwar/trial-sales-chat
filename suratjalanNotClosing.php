<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // if (isset($_GET['c'])) {
    //     $id_city = $_GET['c'];

    //     $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact WHERE tb_contact.id_city = '$id_city' AND tb_surat_jalan.is_closing = 0 ORDER BY id_surat_jalan DESC");

    //     while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
    //         $suratJalanArray[] = $row;
    //     }

    //     if ($suratJalanArray == null) {
    //         echo json_encode(array("status" => "empty", "results" => []));
    //     } else {
    //         echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
    //     }
    // }
    if (isset($_GET['dst']) && isset($_GET['c'])) {
        $id_distributor = $_GET['dst'];
        $id_city = $_GET['c'];

        $city = mysqli_query($conn, "SELECT * FROM tb_city WHERE id_city = '$id_city'");
        $rowCity = $city->fetch_array(MYSQLI_ASSOC);

        $id_distributor = $rowCity['id_distributor'];
        $nama_city = preg_replace('/[0-9]+/', '', $rowCity['nama_city']);

        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_contact.id_city = tb_city.id_city JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier WHERE tb_city.id_distributor = '$id_distributor' AND tb_surat_jalan.is_closing = 0 AND tb_city.nama_city LIKE '%$nama_city%' AND tb_city.id_distributor = '$id_distributor' ORDER BY id_surat_jalan DESC");

        while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    } else if (isset($_GET['dst']) && !isset($_GET['c'])) {
        $id_distributor = $_GET['dst'];
        $id_city = $_GET['c'];

        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_contact.id_city = tb_city.id_city JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier WHERE tb_city.id_distributor = '$id_distributor' AND tb_surat_jalan.is_closing = 0 ORDER BY id_surat_jalan DESC");

        while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    } else {
        $resultSuratJalan = mysqli_query($conn, "SELECT * FROM tb_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_user ON tb_user.id_user = tb_surat_jalan.id_courier WHERE tb_surat_jalan.is_closing = 0 ORDER BY id_surat_jalan DESC");

        while ($row = $resultSuratJalan->fetch_array(MYSQLI_ASSOC)) {
            $suratJalanArray[] = $row;
        }

        if ($suratJalanArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $suratJalanArray));
        }
    }
}
