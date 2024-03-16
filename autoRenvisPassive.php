<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status = 'active'");

    while ($rowContact = $resultContact->fetch_array(MYSQLI_ASSOC)) {
        $contactArr[] = $rowContact;
    }

    foreach ($contactArr as $contact) {
        $id_contact = $contact['id_contact'];
        $id_distributor = $contact['id_distributor'];

        $resultOrder = mysqli_query($conn, "SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact");

        $orderArr = $resultOrder->fetch_array(MYSQLI_ASSOC);

        if ($orderArr != null) {
            $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
            $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
            $dateLastOrder = date("Y-m-d", strtotime($orderArr['date_closing']));

            if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) {
                $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_rencana_visit WHERE id_contact = '$id_contact' AND type_rencana = 'passive' AND is_visited = 0");

                while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
                    $renvisArray[] = $rowRenvis;
                }

                if ($renvisArray == null) {
                    $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");

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
