<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getStore = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status != 'blacklist'");

    while ($rowStore = $getStore->fetch_array(MYSQLI_ASSOC)) {
        $storeArray[] = $rowStore;
    }

    foreach ($storeArray as $store) {
        $nomor_hp = $store['nomorhp'];
        $nama = $store['nama'];
        $id_distributor = $store['id_distributor'];
        $created_at = $store['created_at'];

        $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
        $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);

        $integration_id = $rowQontak['integration_id'];

        $getMarketingKonten = mysqli_query($conn, "SELECT * FROM tb_marketing_message WHERE id_distributor = '$id_distributor'");
        while ($rowMarketingKonten = $getMarketingKonten->fetch_array(MYSQLI_ASSOC)) {
            $marketingKontenArray[] = $rowMarketingKonten;
        }

        foreach ($marketingKontenArray as $marketingKonten) {
            $template_id = $marketingKonten['template_id'];
            $image = "https://order.topmortarindonesia.com/assets/img/content_img/" . $marketingKonten['image_marketing_message'];
            $body = $marketingKonten['body_marketing_message'];
            $week = $marketingKonten['week_marketing_message'];
            $target_status = $marketingKonten['target_status'];
            $id_distributor = $marketingKonten['id_distributor'];

            // if ($week == 1) {
            $dateMinusWeek = date("Y-m-d", strtotime("-" . $week . " days"));
            // } else if ($week > 2) {
            //     $dateMinusWeek = date("Y-m-d", strtotime("-" . $week . " day"));
            // } else if ($week == 0) {
            //     $dateMinusWeek = date("Y-m-d");
            // }
            if ($dateMinusWeek == $created_at) {
                // Send Message
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                                "to_number": "' . $nomor_hp . '",
                                "to_name": "' . $nama . '",
                                "message_template_id": "' . $template_id . '",
                                "channel_integration_id": "' . $integration_id . '",
                                "language": {
                                    "code": "id"
                                },
                                "parameters": {
                                    "header":{
                                        "format":"IMAGE",
                                        "params": [
                                            {
                                                "key":"url",
                                                "value":"' . $image . '"
                                            },
                                            {
                                                "key":"filename",
                                                "value":"content.jpg"
                                            }
                                        ]
                                    },
                                    "body": [
                                    {
                                        "key": "1",
                                        "value": "body_msg",
                                        "value_text": "' . $body . '"
                                    }
                                    ]
                                }
                                }',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $wa_token,
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);

                $status = $res['status'];

                if ($status == "success") {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim konten marketing!", "details" => 'Contact:' . $id_contact . "|Nama:" . $nama . "|" . $nomor_hp . "| DateMinus: " . $dateMinusWeek . "| CreatedAt: " . $created_at];
                    echo json_encode($response);
                } else {
                    // $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim konten marketing!"];
                    // echo json_encode($response);
                    echo $response;
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Tidak kirim konten karna bukan waktunya!", "details" => 'Contact:' . $id_contact . "|Nama:" . $nama . "|" . $nomor_hp . "| DateMinus: " . $dateMinusWeek . "| CreatedAt: " . $created_at . "| week:" . $week];
                echo json_encode($response);
            }
        }
    }
}
