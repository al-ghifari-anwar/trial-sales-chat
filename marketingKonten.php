<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getMarketingMsg = mysqli_query($conn, "SELECT * FROM tb_marketing_message");

    while ($rowMarketingMsg = $getMarketingMsg->fetch_array(MYSQLI_ASSOC)) {
        $marketingMsgArr[] = $rowMarketingMsg;
    }

    if ($marketingMsgArr != null) {

        foreach ($marketingMsgArr as $marketingMsg) {
            $template_id = $marketingMsg['template_id'];
            $image = "https://order.topmortarindonesia.com/assets/img/content_img/" . $marketingMsg['image_marketing_message'];
            $body = $marketingMsg['body_marketing_message'];
            $week = $marketingMsg['week_marketing_message'];
            $target_status = $marketingMsg['target_status'];
            $id_distributor = $marketingMsg['id_distributor'];

            if ($week == 0) {
                $dateMinusWeek = date("Y-m-d");
            } else if ($week > 0 && $week < 2) {
                $dateMinusWeek = date("Y-m-d", strtotime("-" . $week . " day"));
            } else if ($week >= 2) {
                $dateMinusWeek = date("Y-m-d", strtotime("-" . $week . " days"));
            }

            $getStore = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE tb_city.id_distributor = '$id_distributor' AND DATE(tb_contact.created_at) = '$dateMinusWeek'");

            while ($rowStore = $getStore->fetch_array(MYSQLI_ASSOC)) {
                $storeArray[] = $rowStore;
            }

            $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            $qontak = $getQontak->fetch_array(MYSQLI_ASSOC);


            $integration_id = $qontak['integration_id'];
            $wa_token = $qontak['token'];

            if ($storeArray != null) {
                foreach ($storeArray as $store) {
                    // echo json_encode($store);
                    $id_contact = $store['id_contact'];
                    $nomor_hp = $store['nomorhp'];
                    $nama = $store['nama'];
                    $created_at = $store['created_at'];

                    if (date("Y-m-d", strtotime($created_at)) == $dateMinusWeek) {
                        if ($store['store_status'] != 'blacklist') {
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
                                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim konten marketing!", "details" => 'Contact:' . $id_contact . "|Nama:" . $nama . "|" . $nomor_hp . "| DateMinus: " . $dateMinusWeek . "| CreatedAt: " . $created_at . "|week:" . $week];
                                echo json_encode($response);
                            } else {
                                echo $response;

                                $response2 = ["response" => 400, "status" => "failed", "message" => "Gagal mengirim konten marketing!", "details" => 'Contact:' . $id_contact . "|Nama:" . $nama . "|" . $nomor_hp . "| DateMinus: " . $dateMinusWeek . "| CreatedAt: " . $created_at . "|week:" . $week];
                                echo json_encode($response2);
                                // continue;
                            }
                        }
                    } else {
                        $response2 = ["response" => 400, "status" => "failed", "message" => "Belum waktunya kirim!", "details" => 'Contact:' . $id_contact . "|Nama:" . $nama . "|" . $nomor_hp . "| DateMinus: " . $dateMinusWeek . "| CreatedAt: " . $created_at . "|week:" . $week];
                        echo json_encode($response2);
                    }
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Toko tidak ditemukan!"];
                echo json_encode($response);
            }
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "Konten tidak ditemukan!"];
        echo json_encode($response);
    }
}
