<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '85f17083-255d-4340-af32-5dd22f483960';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getSales = mysqli_query($conn, "SELECT * FROM tb_user WHERE level_user = 'sales'");

    while ($rowSales = $getSales->fetch_array(MYSQLI_ASSOC)) {
        $salesArray[] = $rowSales;
    }

    foreach ($salesArray as $salesArray) {
        $id_city = $salesArray['id_city'];
        $nomor_hp = $salesArray['phone_user'];
        $nama = $salesArray['full_name'];

        
        if($nomor_hp == "6287774436555" || $nomor_hp == "6281808152028"){
            $message = "Laporan rekap piutang: https://order.topmortarindonesia.com/wh-tagihan?c=0";
            // echo "Khusus";
        } else {
            $message = "Laporan rekap piutang: https://order.topmortarindonesia.com/wh-tagihan?c=$id_city";
            // echo "No";
        }

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

        // if ($status == "success") {
        //     $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim laporan tagihan!"];
        //     echo json_encode($response);
        // } else {
        //     $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim laporan tagihan!"];
        //     echo json_encode($response);
        // }
    }
}
