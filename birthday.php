<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
// putenv('GDFONTPATH=' . realpath('.'));

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = '27286af8-304e-4543-af66-307f453054f7';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

$result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE tgl_lahir IS NOT NULL");

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $transArray[] = $row;
}

$img = imagecreatefrompng("img/bday.png");

foreach ($transArray as $arr) {
    $tgl_lahir = date("m-d", strtotime($arr['tgl_lahir']));
    $tgl_skrg = date("m-d");
    $nomor_hp = $arr['nomorhp'];
    $nama = $arr['store_owner'];
    $toko = $arr['nama'];

    if ($tgl_lahir == $tgl_skrg) {
        // (B) WRITE TEXT
        $txt = $nama . "\n" . $toko;
        $fontFile = __DIR__ . "/font/CoffeCake.ttf"; // CHANGE TO YOUR OWN!
        $fontSize = 35;
        $fontColor = imagecolorallocate($img, 255, 255, 255);
        $posX = 212;
        $posY = 875;
        $angle = 0;
        // (C) CALCULATE TEXT BOX POSITION
        // (C1) GET IMAGE DIMENSIONS
        $iWidth = imagesx($img);
        $iHeight = imagesy($img);

        // (C2) GET TEXT BOX DIMENSIONS
        $tSize = imagettfbbox($fontSize, $angle, $fontFile, $txt);
        $tWidth = max([$tSize[2], $tSize[4]]) - min([$tSize[0], $tSize[6]]);
        $tHeight = max([$tSize[5], $tSize[7]]) - min([$tSize[1], $tSize[3]]);
        // (C3) CENTER THE TEXT BLOCK
        $centerX = ceil(($iWidth - $tWidth) / 2);
        $centerX = $centerX < 0 ? 0 : $centerX;

        imagettftext($img, $fontSize, $angle, $centerX, $posY, $fontColor, $fontFile, $txt);

        // (C2) OR SAVE TO A FILE
        $quality = 80; // 0 to 100
        imagejpeg($img, "img/bday_" . $nomor_hp . ".jpg", $quality);

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
                                "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                            },
                            {
                                "key":"filename",
                                "value":"bday.jpg"
                            }
                        ]
                    },
                    "body": [
                    {
                        "key": "1",
                        "value": "nama",
                        "value_text": "' . $nama . '"
                    },
                    {
                        "key": "2",
                        "value": "nama_toko",
                        "value_text": "' . $toko . '"
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

        // echo $response;
        // die;

        if ($status == 'success') {
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
                    "to_number": "' . "6287757904850" . '",
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
                                    "value":"https://saleswa.topmortarindonesia.com/img/bday_' . $nomor_hp . '.jpg"
                                },
                                {
                                    "key":"filename",
                                    "value":"bday.jpg"
                                }
                            ]
                        },
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "Forwarding from - ' . $nama . '"
                        },
                        {
                            "key": "2",
                            "value": "nama_toko",
                            "value_text": "' . $toko . '"
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
                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim ucapan ultah!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim ucapan ultah!"];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim ucapan ultah!"];
            echo json_encode($response);
        }
    }
}
