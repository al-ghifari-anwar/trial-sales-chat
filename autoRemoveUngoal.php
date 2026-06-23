<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getContacts = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status IN ('data') ");

    while ($rowContact = $getContacts->fetch_array(MYSQLI_ASSOC)) {
        $id_contact = $rowContact['id_contact'];
        $id_city = $rowContact['id_city'];
        $id_distributor = $rowContact['id_distributor'];
        $nama_city_buangan = trim(preg_replace("/\\d+/", "", $rowContact['nama_city'])) . " X";

        $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_contact' ORDER BY date_visit DESC")->fetch_array(MYSQLI_ASSOC);

        $id_user = $lastVisit['id_user'];

        $cutoffVisit = mysqli_query($conn, "SELECT * FROM tb_cutoff_visit WHERE id_contact = '$id_contact' ORDER BY created_at DESC")->fetch_array(MYSQLI_ASSOC);

        $visit = array();
        $dateCutoffVisit = null;

        if ($cutoffVisit) {
            $dateCutoffVisit = date('Y-m-d', strtotime($cutoffVisit['date_cutoff_visit']));

            $visit = mysqli_query($conn, " SELECT COUNT(*) AS jml_visit FROM tb_visit WHERE id_contact = $id_contact AND id_user = '$id_user' AND DATE(date_visit) > $dateCutoffVisit ")->fetch_array(MYSQLI_ASSOC);
        } else {
            $visit = mysqli_query($conn, " SELECT COUNT(*) AS jml_visit FROM tb_visit WHERE id_contact = $id_contact AND id_user = '$id_user'")->fetch_array(MYSQLI_ASSOC);
        }

        $cityBuangan = mysqli_query($conn, " SELECT * FROM tb_city WHERE nama_city = '$nama_city_buangan' AND id_distributor = '$id_distributor' ")->fetch_array(MYSQLI_ASSOC);

        if ($visit['jml_visit'] >= 4) {
            if ($cityBuangan) {
                $id_city_buangan = $cityBuangan['id_city'];

                $checkBuangan = mysqli_query($conn, "SELECT * FROM tb_transit_toko WHERE id_contact = '$id_contact'")->fetch_array(MYSQLI_ASSOC);

                if (!$checkBuangan) {
                    $insertTransit = mysqli_query($conn, "INSERT INTO tb_transit_toko(id_contact,id_city_from,id_city_to) VALUES($id_contact,$id_city,$id_city_buangan)");

                    if (!$insertTransit) {
                        $response = ["response" => 400, "status" => "failed", "message" => "Gagal pindah toko!"];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "ok", "message" => "Toko " . $rowContact['nama'] . " Berhasil dipindah! "];
                        echo json_encode($response);
                    }
                }
            }
        }
    }
}
