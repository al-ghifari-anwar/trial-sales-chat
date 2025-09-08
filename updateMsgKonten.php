<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
// $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
// $template_id = '85f17083-255d-4340-af32-5dd22f483960';
// $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // $rowMsgKonten = array();

    $getMsgKonten = mysqli_query($conn, "SELECT * FROM tb_msg_konten");

    while ($rowMsgKonten = $getMsgKonten->fetch_array($getMsgKonten)) {
        $updated_at = date("Y-m-d H:i:s");
        $id_msg_konten = $rowMsgKonten['id_msg_konten'];
        $id_msg = $rowMsgKonten['id_msg'];
        $id_distributor = $rowMsgKonten['id_distributor'];

        $qontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'")->fetch_array(MYSQLI_ASSOC);

        $wa_token = $qontak['token'];

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
            $saveLog = mysqli_query($conn, "UPDATE tb_msg_log SET is_sent = 0, updated_at = '$updated_at' WHERE id_msg_konten = '$id_msg_konten'");

            if ($saveLog) {
                $response = ["response" => 200, "status" => "ok", "message" => "Pesan telah disimpan."];
                echo json_encode($response);
            } else {
                $response = ["response" => 400, "status" => "failed", "message" => "Pesan gagal tersimpan."];
                echo json_encode($response);
            }
        }
    }
}
