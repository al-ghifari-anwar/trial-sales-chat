<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status = 'passive'");

    while ($rowContact = $resultContact->fetch_array(MYSQLI_ASSOC)) {
        $contactArr[] = $rowContact;
    }

    foreach ($contactArr as $contact) {
        $id_contact = $contact['id_contact'];
        $id_distributor = $contact['id_distributor'];
        $dateMin30 = date("Y-m-d", strtotime('-1 month'));

        $resultLastVisit = mysqli_query($conn, "SELECT COUNT(*) AS jml_visit FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit = 'passive' AND DATE(date_visit) >= '$dateMin30'");

        $lastVisitArray = $resultLastVisit->fetch_array(MYSQLI_ASSOC);

        if ($lastVisitArray != null) {

            if ($lastVisitArray['jml_visit'] >= 3) {
                if ($renvisArray == null) {
                    $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'mg',$id_distributor,0)");

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
            }
        }
    }
}
