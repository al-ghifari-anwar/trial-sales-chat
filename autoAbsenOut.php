<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getKurir = mysqli_query($conn, "SELECT * FROM tb_user WHERE level_user = 'courier'");
    while ($rowKurir = $getKurir->fetch_array(MYSQLI_ASSOC)) {
        $arrayKurir[] = $rowKurir;
    }

    foreach ($arrayKurir as $kurir) {
        // $id_gudang = $_POST['id_gudang'] ? $_POST['id_gudang'] : 0;
        // $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        // $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
        $id_user = $kurir['id_user'];
        $dateNow = date("Y-m-d");
        // $source = $_POST['source'] == null ? 'absen_in' : $_POST['source'];

        $getAbsenIn = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_user = '$id_user' AND DATE(date_visit) = '$dateNow' AND source_visit = 'absen_in' ");
        $rowAbsenIn = $getAbsenIn->fetch_array(MYSQLI_ASSOC);

        if ($rowAbsenIn) {
            $getAbsenOut =  mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_user = '$id_user' AND DATE(date_visit) = '$dateNow' AND source_visit = 'absen_out' ");
            $rowAbsenOut = $getAbsenOut->fetch_array(MYSQLI_ASSOC);

            if ($rowAbsenOut == null) {
                $id_gudang = $rowAbsenIn['id_contact'];
                $distance_visit = 0.001;
                $laporan_visit = "Absen Pulang By System";
                $source = "absen_out";

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_gudang, $distance_visit, '$laporan_visit', '$source', $id_user)");

                if ($insertVisit) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim absen pulang!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan absen pulang! ", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Sudah absen pulang"];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Tidak ada absen masuk"];
            echo json_encode($response);
        }
    }
}
