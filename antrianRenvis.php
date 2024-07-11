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
            $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_rencana_visit WHERE id_contact = '$id_contact' AND type_rencana = 'passive' AND is_visited = 0");

            // while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
            $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
            // }

            if ($renvisArray == null) {
                $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");

                if ($insertRenvis) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            } else {
                $response = ["message" => "Sudah ada"];
                echo json_encode($response);
            }
        } else {
            $cekRenvis = mysqli_query($conn, "SELECT COUNT(*) AS jml_renvis FROM tb_rencana_visit WHERE id_contact = '$id_contact' AND type_rencana = 'passive'");
            $renvisArray = $cekRenvis->fetch_array(MYSQLI_ASSOC);
            $jmlRenvis = $renvisArray['jml_renvis'];

            $times = $jmlRenvis * $interval_renvis;

            if ($date_renvis == date("Y-m-d", strtotime("-" . $times . " days"))) {
                $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");

                if ($insertRenvis) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menyimpan data rencana visit!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan data rencana visit!", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            } else {
                $response = ["message" => "Belum waktunya interval", "detail" => "JML: " . $jmlRenvis . "| INTERVAL:" . $interval_renvis . "| DATE:" . $date_renvis . "| DATE MINUS:" . date("Y-m-d", strtotime("-" . $times . " days")) . "| ID: " . $id_contact];
                echo json_encode($response);
            }
        }
    }
}
