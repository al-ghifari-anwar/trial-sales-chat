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
        $twoMonths = date("Y-m-d H:i:s", strtotime("-2 month"));
        // echo $twoMonths;
        foreach ($lastOrderArr as $lastOrder) {
            $id_contact = $lastOrder['id_contact'];

            if ($lastOrder['last_order'] < $twoMonths) {
                $dateLastOrder = strtotime($lastOrder['last_order']);
                $datePassive = date("Y-m-d H:i:s", strtotime("+2 month", $dateLastOrder));

                $getContact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id_contact'");
                $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);

                $cekDate = date("Y-m-d", strtotime($datePassive));
                $cekStatusData = mysqli_query($conn, "SELECT * FROM tb_status_change WHERE id_contact = '$id_contact' AND status_from = 'active' AND status_to = 'passive' AND created_at LIKE '%$cekDate%'");
                $rowStatusData = $cekStatusData->fetch_array(MYSQLI_ASSOC);

                if ($rowStatusData == null) {
                    $statusChange = mysqli_query($conn, "INSERT INTO tb_status_change(id_contact,status_from,status_to, created_at) VALUES($id_contact,'active','passive', '$datePassive')");

                    $setPassive = mysqli_query($conn, "UPDATE tb_contact SET store_status = 'passive' WHERE id_contact = '$id_contact'");

                    if ($setPassive) {

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
