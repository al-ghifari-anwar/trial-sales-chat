<?php
// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_kontenmsg'])) {
        $id_kontenmsg = $_GET['id_kontenmsg'];
        $kontenmsg = mysqli_query($conn, "SELECT * FROM tb_kontenmsg WHERE id_kontenmsg = '$id_kontenmsg'")->fetch_array(MYSQLI_ASSOC);

        if ($kontenmsg == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            $kontenmsg['link_thumbnail'] = 'https://order.topmortarindonesia.com/assets/img/kontenmsg_img/' . $kontenmsg['thumbnail_kontenmsg'];
            echo json_encode(array("status" => "ok", "results" => $kontenmsg));
        }
    } else {
        $kontenmsgs = array();

        $getKontenMsg = mysqli_query($conn, "SELECT * FROM tb_kontenmsg ORDER BY created_at DESC");

        while ($rowKontenMsg = $getKontenMsg->fetch_array(MYSQLI_ASSOC)) {
            $rowKontenMsg['link_thumbnail'] = 'https://order.topmortarindonesia.com/assets/img/kontenmsg_img/' . $rowKontenMsg['thumbnail_kontenmsg'];

            $kontenmsgs[] = $rowKontenMsg;
        }

        if ($kontenmsgs == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $kontenmsgs));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kontenmsg = $_POST['id_kontenmsg'];
    $id_contact = $_POST['id_contact'];

    $kontenmsg = mysqli_query($conn, "SELECT * FROM tb_kontenmsg WHERE id_kontenmsg = '$id_kontenmsg'")->fetch_array(MYSQLI_ASSOC);
    $kontenmsg['link_thumbnail'] = 'https://order.topmortarindonesia.com/assets/img/kontenmsg_img/' . $kontenmsg['thumbnail_kontenmsg'];

    $contact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'")->fetch_array(MYSQLI_ASSOC);

    $id_distributor = $contact['id_distributor'];

    // $qontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'")->fetch_array(MYSQLI_ASSOC);

    // $wa_token = $qontak['token'];
    // $integration_id = $qontak['integration_id'];
    // $template_id = '7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841';

    $getHaloai = mysqli_query($conn, "SELECT * FROM tb_haloai WHERE id_distributor = '$id_distributor'");
    $rowHaloai = $getHaloai->fetch_array(MYSQLI_ASSOC);
    $wa_token = $rowHaloai['token_haloai'];
    $business_id = $rowHaloai['business_id_haloai'];
    $channel_id = $rowHaloai['channel_id_haloai'];
    $template = 'notif_materi_img';

    $message = $kontenmsg['body_kontenmsg'] . " " . $kontenmsg['link_kontenmsg'];
    $thumbnail_kontenmsg = "https://order.topmortarindonesia.com/assets/img/kontenmsg_img/" . $kontenmsg['thumbnail_kontenmsg'];

    // Send message
    $haloaiPayload = [
        'activate_ai_after_send' => false,
        'channel_id' => $channel_id,
        "fallback_template_header" => [
            'filename' => $kontenmsg['thumbnail_kontenmsg'],
            'type' => 'image',
            'url' => $thumbnail_kontenmsg,
        ],
        'fallback_template_message' => $template,
        'fallback_template_variables' => [
            trim(preg_replace('/\s+/', ' ', $message)),
        ],
        "media" => [
            'filename' => $kontenmsg['thumbnail_kontenmsg'],
            'type' => 'image',
            'url' => $thumbnail_kontenmsg,
        ],
        'phone_number' => $contact['nomorhp'],
        'text' => trim(preg_replace('/\s+/', ' ', $message)),
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.haloai.co.id/api/open/channel/whatsapp/v1/sendMessageByPhoneSync',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($haloaiPayload),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $wa_token,
            'X-HaloAI-Business-Id: ' . $business_id,
            'Content-Type: application/json'
        ),
    ));
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
    //                         "to_number": "' . $contact['nomorhp'] . '",
    //                         "to_name": "' . $contact['nama'] . '",
    //                         "message_template_id": "' . $template_id . '",
    //                         "channel_integration_id": "' . $integration_id . '",
    //                         "language": {
    //                             "code": "id"
    //                         },
    //                         "parameters": {
    //                             "header":{
    //                                 "format":"IMAGE",
    //                                 "params": [
    //                                     {
    //                                         "key":"url",
    //                                         "value":"https://order.topmortarindonesia.com/assets/img/kontenmsg_img/' . $kontenmsg['thumbnail_kontenmsg'] . '"
    //                                     },
    //                                     {
    //                                         "key":"filename",
    //                                         "value":"konten.png"
    //                                     }
    //                                 ]
    //                             },
    //                             "body": [
    //                                 {
    //                                     "key": "1",
    //                                     "value": "message",
    //                                     "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
    //                                 }
    //                             ]
    //                         }
    //                         }',
    //     CURLOPT_HTTPHEADER => array(
    //         'Authorization: Bearer ' . $wa_token,
    //         'Content-Type: application/json'
    //     ),
    // ));

    $response = curl_exec($curl);

    curl_close($curl);

    $res = json_decode($response, true);

    if ($res['status'] == 'success') {
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim pesan", "detail" => $res];
        echo json_encode($response);
    } else {
        $response = ["response" => 400, "status" => "failed", "message" => "Pesan gagal terkirim", "detail" => $res];
        echo json_encode($response);
    }
}
