<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultAntrian = mysqli_query($conn, "SELECT * FROM tb_antrian_renvis");

    while ($rowAntrian = $resultAntrian->fetch_array(MYSQLI_ASSOC)) {
        $antrianArray[] = $rowAntrian;
    }

    foreach ($antrianArray as $antrianArray) {
        $id_contact = $antrianArray['id_contact'];
        $id_distributor = $antrianArray['id_distributor'];
        $date_renvis = $antrianArray['date_renvis'];
        $interval_renvis = $antrianArray['interval_renvis'];

        if ($date_renvis == date("Y-m-d")) {
            $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_rencana_visit WHERE id_contact = '$id_contact' AND type_rencana = 'jatem' AND is_visited = 0");

            // while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
            $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
            // }

            if ($renvisArray == null) {
                $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,$id_surat_jalan,'jatem',$id_distributor,$id_invoice)");

                if ($insertRenvis) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit!"];
                    echo json_encode($response);
                }
            } else {
                $response = ["message" => "Sudah ada"];
                echo json_encode($response);
            }
        } else {
            $response = ["message" => "Belum waktunya"];
            echo json_encode($response);
        }
    }
}
