<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = 'f9b24650-1029-4eec-9f47-878d2b3e232b';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

$result = mysqli_query($conn, "SELECT * FROM tb_contact WHERE tgl_lahir IS NOT NULL");

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $transArray[] = $row;
}

foreach($transArray as $arr){
    $tgl_lahir = date("m-d", strtotime($arr['tgl_lahir']));
    $tgl_skrg = date("m-d");
    $nomor_hp = $arr['nomorhp'];
    $nama = $arr['store_owner'];

    if($tgl_lahir == $tgl_skrg){
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
                    "body": [
                    {
                        "key": "1",
                        "value": "nama",
                        "value_text": "' . $nama . '"
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

        if($status == 'success'){
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
                    "to_number": "' . "6287771736555" . '",
                    "to_name": "' . $nama . '",
                    "message_template_id": "' . $template_id . '",
                    "channel_integration_id": "' . $integration_id . '",
                    "language": {
                        "code": "id"
                    },
                    "parameters": {
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "Forwarding from - ' . $nama . '"
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

            if($status == "success"){
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



