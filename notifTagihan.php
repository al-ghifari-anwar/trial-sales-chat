<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
// $template_id = '85f17083-255d-4340-af32-5dd22f483960';
// $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getSales = mysqli_query($conn, "SELECT * FROM tb_user WHERE level_user = 'sales' AND is_notify = 1");

    while ($rowSales = $getSales->fetch_array(MYSQLI_ASSOC)) {
        $salesArray[] = $rowSales;
    }

    foreach ($salesArray as $salesArray) {

        $id_city = $salesArray['id_city'];
        $nomor_hp = $salesArray['phone_user'];
        $nama = $salesArray['full_name'];
        $id_distributor = $salesArray['id_distributor'];

        $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
        $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);

        $integration_id = $rowQontak['integration_id'];
        $wa_token = $rowQontak['token'];

        if ($nomor_hp == "6287774436555" || $nomor_hp == "6281808152028") {
            $message = "Laporan rekap piutang: https://order.topmortarindonesia.com/wh-tagihan?c=0&dst=$id_distributor";
            $msgStatus = "All City";
            // echo $message;
        } else {
            $message = "Laporan rekap piutang: https://order.topmortarindonesia.com/wh-tagihan?c=$id_city&dst=$id_distributor";
            $msgStatus = "Specific City";
            // echo $message;
        }

        if ($nomor_hp != "6287774436555" || $nomor_hp != "6281808152028" || $nomor_hp != "6281235834111" || $nomor_hp != "6281952581199" || $nomor_hp != "6285335631783" || $nomor_hp != "6281808152028" || $nomor_hp != "6287757904850") {

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => '{
            //         "to_number": "' . $nomor_hp . '",
            //         "to_name": "' . $nama . '",
            //         "message_template_id": "' . $template_id . '",
            //         "channel_integration_id": "' . $integration_id . '",
            //         "language": {
            //             "code": "id"
            //         },
            //         "parameters": {
            //             "body": [
            //             {
            //                 "key": "1",
            //                 "value": "nama",
            //                 "value_text": "' . $nama . '"
            //             },
            //             {
            //                 "key": "2",
            //                 "value": "message",
            //                 "value_text": "' . $message . '"
            //             },
            //             {
            //                 "key": "3",
            //                 "value": "sales",
            //                 "value_text": "Automated Message"
            //             }
            //             ]
            //         }
            //         }',
            //     CURLOPT_HTTPHEADER => array(
            //         'Authorization: Bearer ' . $wa_token,
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);

            // $res = json_decode($response, true);
            // $status = $res['status'];
            // $id = $data['data']['id'];

            // if ($status == "success") {
            //     $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim laporan tagihan!", "detail" => $msgStatus, "body" => $message];
            //     echo json_encode($response);
            // } else {
            //     $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim laporan tagihan!", "id" => $id, "target" => $salesArray['username'] . " - " . $nomor_hp];
            //     echo json_encode($response);
            // }
        }
    }
}
