<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_gudang'])) {
        $id_gudang = $_POST['id_gudang'] ? $_POST['id_gudang'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;
        $source = $_POST['source'] == null ? 'absen_in_bc' : $_POST['source']  . "_bc";

        $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_gudang, $distance_visit, '$laporan_visit', '$source', $id_user)");
        $id_visit = mysqli_insert_id($conn);

        if ($insertVisit) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim absen!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan absen! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    } else if (isset($_POST['id_contact'])) {
        $id_contact = $_POST['id_contact'] ? $_POST['id_contact'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;
        $source = $_POST['source'] == null ? 'absen_in_store' : $_POST['source'] . "_store";

        $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_contact, $distance_visit, '$laporan_visit', '$source', $id_user)");
        $id_visit = mysqli_insert_id($conn);

        if ($insertVisit) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim absen!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan absen! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    }
}
