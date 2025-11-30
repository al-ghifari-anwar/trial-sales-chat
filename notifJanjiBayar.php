<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE is_pay = 'pay_later'");
    while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
        $visitArray[] = $rowVisit;
    }

    foreach ($visitArray as $visit) {
        $pay_date = $visit['pay_date'];
        $id_contact = $visit['id_contact'];
        $id_user = $visit['id_user'];
        $id_invoice = $visit['id_invoice'];

        if (date("Y-m-d") == $pay_date) {
            $getContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'");
            $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);

            $id_distributor = $rowContact['id_distributor'];

            // $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            // $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
            // $integration_id = $rowQontak['integration_id'];
            // $wa_token = $rowQontak['token'];
            $full_name = "PT Top Mortar Indonesia";

            $getInvoice = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE id_invoice = '$id_invoice'");
            $rowInvoice = $getInvoice->fetch_array(MYSQLI_ASSOC);

            $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
            $rowUser = $getUser->fetch_array(MYSQLI_ASSOC);

            // Send Message To Customer
            $message = "Hari ini kami menunggu pambayaran bapak / ibu sesuai janji pada tanggal " . date("d F Y", strtotime($pay_date)) . " terimakasih";

            $nomor_hp = $rowContact['nomorhp'];
            $nama = $rowContact['nama'];

            // if ($nomor_hp == '6281808152028') {

            if ($rowInvoice['status_invoice'] == 'waiting') {

                if ($id_distributor != 8) {

                    $getHaloai = mysqli_query($conn, "SELECT * FROM tb_haloai WHERE id_distributor = '$id_distributor'");
                    $rowHaloai = $getHaloai->fetch_array(MYSQLI_ASSOC);
                    $wa_token = $rowHaloai['token_haloai'];
                    $business_id = $rowHaloai['business_id_haloai'];
                    $channel_id = $rowHaloai['channel_id_haloai'];
                    $template = 'info_meeting_baru';

                    $haloaiPayload = [
                        'activate_ai_after_send' => false,
                        'channel_id' => $channel_id,
                        'fallback_template_message' => $template,
                        'fallback_template_variables' => [
                            $nama,
                            trim(preg_replace('/\s+/', ' ', $message)),
                            $full_name,
                        ],
                        'phone_number' => $nomor_hp,
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

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $res = json_decode($response, true);

                    $status = $res['status'];
                } else {
                    $getMaxchat = mysqli_query($conn, "SELECT * FROM tb_maxchat WHERE id_distributor = 1");
                    $maxchat = $getMaxchat->fetch_array(MYSQLI_ASSOC);
                    $endpoint = "https://app.maxchat.id/api/messages/push";

                    $data = [
                        'to' => $nomor_hp,
                        'msgType' => 'text',
                        'templateId' => 'b75d51f9-c925-4a62-8b93-dd072600b95b',
                        'values' => [
                            'body' => [
                                [
                                    'index' => 1,
                                    'type' => 'text',
                                    'text' => $nama
                                ],
                                [
                                    'index' => 2,
                                    'type' => 'text',
                                    'text' => trim(preg_replace('/\s+/', ' ', $message))
                                ]
                            ],
                        ]
                    ];

                    $headers = [
                        'Authorization: Bearer ' . $maxchat['token_maxchat'],
                        'Content-Type: application/json',
                    ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $endpoint,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($data),
                        CURLOPT_HTTPHEADER => $headers,
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    $res = json_decode($response, true);

                    curl_close($curl);

                    $status = isset($res['content']) ? 'success' : 'empty';
                }

                if ($status == "success") {
                    // Send Message To Sales
                    $message = "Waktunya untuk melakukan tagihan kepada toko *" . $nama .  "*, yang telah dijanjikan pada tanggal " . date("d F Y", strtotime($pay_date));

                    $nomor_hp_sales = $rowUser['phone_user'];
                    $nama_sales = $rowUser['full_name'];

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
                            "to_number": "' . $nomor_hp_sales . '",
                            "to_name": "' . $nama_sales . '",
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
                                    "value_text": "' . $nama_sales . '"
                                },
                                {
                                    "key": "2",
                                    "value": "message",
                                    "value_text": "' . $message . '"
                                },
                                {
                                    "key": "3",
                                    "value": "sales",
                                    "value_text": "' . $full_name . '"
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
                        $data = [
                            'nomor' => $nomor_hp_sales,
                            'nama' => $nama_sales,
                            'id_user' => $rowUser['id_user']
                        ];
                        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim notif pada customer dan sales!", "detail" => $data];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim notif pada customer tapi gagal kirim notif sales!", "details" => $res];
                        echo json_encode($response);
                    }
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim notif pada customer maupun sales!", "details" => $res];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Invoice sudah terbayar"];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Belum waktunya! Pay Date:" . $pay_date . "|Date:" . date("Y-m-d")];
            echo json_encode($response);
        }
    }
}
