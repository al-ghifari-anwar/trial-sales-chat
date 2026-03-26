<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getContacts = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status IN ('data') ");

    while ($rowContact = $getContacts->fetch_array(MYSQLI_ASSOC)) {
        $id_contact = $rowContact['id_contact'];
        $id_distributor = $rowContact['id_distributor'];
        $nama_city_buangan = trim(preg_replace("/\\d+/", "", $rowContact['nama_city'])) . " X";
        $visit = mysqli_query($conn, " SELECT COUNT(*) AS jml_visit FROM tb_visit WHERE id_contact = $id_contact ")->fetch_array(MYSQLI_ASSOC);

        $cityBuangan = mysqli_query($conn, " SELECT * FROM tb_city WHERE nama_city = '$nama_city_buangan' AND id_distributor = '$id_distributor' ")->fetch_array(MYSQLI_ASSOC);

        if ($visit['jml_visit'] >= 8) {
            if ($cityBuangan) {
                $id_city_buangan = $cityBuangan['id_city'];
                $updateContact = mysqli_query($conn, "UPDATE tb_contact SET id_city = '$id_city_buangan' WHERE id_contact = '$id_contact'");

                if (!$updateContact) {
                    $response = ["response" => 4200, "status" => "failed", "message" => "Gagal pindah toko!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "ok", "message" => "Toko " . $rowContact['nama'] . " Berhasil dipindah! "];
                    echo json_encode($response);
                }
            }
        }
    }
}
