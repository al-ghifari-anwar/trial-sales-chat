<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $getLastOrder = mysqli_query($conn, "SELECT MAX(date_closing) AS last_order, id_contact  FROM tb_surat_jalan tsj WHERE is_closing = 1 GROUP BY tsj.id_contact ");

    while ($rowLastOrder = $getLastOrder->fetch_array(MYSQLI_ASSOC)) {
        $lastOrderArr[] = $rowLastOrder;
    }

    if ($lastOrderArr != null) {
        $twoMonths = date("Y-m-d H:i:s", strtotime("-45 days"));
        // echo $twoMonths;
        foreach ($lastOrderArr as $lastOrder) {
            $id_contact = $lastOrder['id_contact'];

            if ($lastOrder['last_order'] < $twoMonths) {
                $dateLastOrder = strtotime($lastOrder['last_order']);
                $datePassive = date("Y-m-d H:i:s", strtotime("+45 days", $dateLastOrder));

                $getContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'");
                $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);

                $id_distributor = $rowContact['id_distributor'];

                $cekDate = date("Y-m-d", strtotime($datePassive));
                $cekStatusData = mysqli_query($conn, "SELECT * FROM tb_status_change WHERE id_contact = '$id_contact' AND status_from = 'active' AND status_to = 'passive' AND created_at LIKE '%$cekDate%'");
                $rowStatusData = $cekStatusData->fetch_array(MYSQLI_ASSOC);

                if ($rowStatusData == null) {
                    $statusChange = mysqli_query($conn, "INSERT INTO tb_status_change(id_contact,status_from,status_to, created_at) VALUES($id_contact,'active','passive', '$datePassive')");

                    $setPassive = mysqli_query($conn, "UPDATE tb_contact SET store_status = 'passive' WHERE id_contact = '$id_contact'");

                    if ($setPassive) {
                        $cekRenvis = mysqli_query($conn, "SELECT * FROM tb_rencana_visit WHERE id_contact = '$id_contact' AND type_rencana = 'passive' AND is_visited = 0");

                        while ($rowRenvis = $cekRenvis->fetch_array(MYSQLI_ASSOC)) {
                            $renvisArray[] = $rowRenvis;
                        }

                        if ($renvisArray == null) {
                            $insertRenvis = mysqli_query($conn, "INSERT INTO tb_rencana_visit(id_contact,id_surat_jalan,type_rencana,id_distributor,id_invoice) VALUES($id_contact,0,'passive',$id_distributor,0)");
                        }

                        $response = ["response" => 200, "status" => "success", "message" => "Status changed to passive!"];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Failed changing status!", "detail" => mysqli_error($conn)];
                        echo json_encode($response);
                    }
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed changing status!", "detail" => "Status change already saved"];
                    echo json_encode($response);
                }
            }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "No stores!", "detail" => mysqli_error($conn)];
        echo json_encode($response);
    }
}
