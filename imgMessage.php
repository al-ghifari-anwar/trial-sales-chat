<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
// $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
// $template_id = '85f17083-255d-4340-af32-5dd22f483960';
// $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_contact = $_POST['id_contact'];
    $nomorhp = $_POST['nomorhp'];
    $message = $_POST['message'];

    $contact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'")->fetch_array(MYSQLI_ASSOC);

    $id_distributor = $contact['id_distributor'];

    $qontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'")->fetch_array(MYSQLI_ASSOC);

    $wa_token = $qontak['token'];
    $integration_id = $qontak['integration_id'];
    $template_id = '7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841';

    if (isset($_FILES['img_message'])) {
        $img_message = $_FILES['img_message']['name'];
        $dateFile = date("Y-m-d-H-i-s");

        $fileName = $dateFile . $_FILES['img_message']['name'];
        if (move_uploaded_file($_FILES['img_message']['tmp_name'], 'img/img_konten_msg/' . $fileName)) {
            $sourceImage = 'img/img_konten_msg/' . $fileName;
            $imageDestination = 'img/img_konten_msg/min-' . $fileName;
            $createImage = imagecreatefromjpeg($sourceImage);
            imagejpeg($createImage, $imageDestination, 60);
        }

        $imgNewName = 'min-' . $fileName;

        $message = 'Halo ' . $contact['nama'] . ' ' . $message;

        // Send message
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
                            "to_number": "' . $nomorhp . '",
                            "to_name": "' . $contact['nama'] . '",
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
                                            "value":"https://devsaleswa.topmortarindonesia.com/img/img_konten_msg/' . $imgNewName . '"
                                        },
                                        {
                                            "key":"filename",
                                            "value":"konten.png"
                                        }
                                    ]
                                },
                                "body": [
                                {
                                    "key": "1",
                                    "value": "message",
                                    "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
                                },
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

        if ($res['status'] == 'success') {
            $resData = $res['data'];
            $id_msg = $resData['id'];

            // Cek Log 5f70dd63-7959-4a1c-8e52-e65a1eb40487
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $id_msg . '/whatsapp/log',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
                ),
            ));

            $responseLog = curl_exec($curl);

            curl_close($curl);

            $resLog = json_decode($responseLog, true);
            $logData = $resLog['data'][0];

            if ($logData['status'] == 'failed') {
                // Gagal
                $saveLog = mysqli_query($conn, "INSERT INTO tb_msg_konten(id_contact,id_distributor,id_msg,nomorhp_msg_konten,message_msg_konten,img_msg_konten,is_sent) VALUES($id_contact,$id_distributor,'$id_msg','$nomorhp','$message','$imgNewName',0)");

                if ($saveLog) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Pesan telah disimpan."];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 400, "status" => "failed", "message" => "Pesan gagal tersimpan."];
                    echo json_encode($response);
                }
            } else {
                $saveLog = mysqli_query($conn, "INSERT INTO tb_msg_konten(id_contact,id_distributor,id_msg,nomorhp_msg_konten,message_msg_konten,img_msg_konten,is_sent) VALUES($id_contact,$id_distributor,'$id_msg','$nomorhp','$message','$imgNewName',1)");

                if ($saveLog) {
                    $response = ["response" => 200, "status" => "ok", "message" => "Pesan berhasil terkirim."];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 400, "status" => "failed", "message" => "Pesan gagal terkirim."];
                    echo json_encode($response);
                }
            }
        } else {
            // Gagal
            $saveLog = mysqli_query($conn, "INSERT INTO tb_msg_konten(id_contact,id_distributor,id_msg,nomorhp_msg_konten,message_msg_konten,img_msg_konten,is_sent) VALUES($id_contact,$id_distributor,'-','$nomorhp','$message','$imgNewName',0)");

            if ($saveLog) {
                $response = ["response" => 200, "status" => "ok", "message" => "Pesan telah disimpan"];
                echo json_encode($response);
            } else {
                $response = ["response" => 400, "status" => "failed", "message" => "Pesan gagal tersimpan"];
                echo json_encode($response);
            }
        }
    } else {
        $response = ["response" => 400, "status" => "failed", "message" => "Anda belum memilih foto"];
        echo json_encode($response);
    }
}
