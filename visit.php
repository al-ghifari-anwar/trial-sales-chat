<?php

// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['u'])) {
        if (isset($_GET['s'])) {
            $id_user = $_GET['u'];
            $id_contact = $_GET['s'];

            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' AND tb_visit.id_contact = '$id_contact' ORDER BY date_visit DESC");

            echo mysqli_error($conn);

            while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                $id_visit = $rowVisit['id_visit'];
                $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
                $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
                $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
                $visitArray[] = $rowVisit;
            }

            if ($visitArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $visitArray));
            }
        } else if (isset($_GET['g'])) {
            $id_user = $_GET['u'];
            $id_contact = $_GET['g'];

            $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_gudang ON tb_gudang.id_gudang = tb_visit.id_contact WHERE id_user = '$id_user' AND tb_visit.id_contact = '$id_contact' ORDER BY date_visit DESC");

            echo mysqli_error($conn);

            while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                $id_visit = $rowVisit['id_visit'];
                $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
                $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
                $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
                $visitArray[] = $rowVisit;
            }

            if ($visitArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $visitArray));
            }
        } else {
            if (isset($_GET['cat']) && $_GET['cat'] == 'sales') {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC LIMIT 200");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $id_visit = $rowVisit['id_visit'];
                    $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
                    $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
                    $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            } else if (isset($_GET['cat']) && $_GET['cat'] == 'courier') {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_gudang ON tb_gudang.id_gudang = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $id_visit = $rowVisit['id_visit'];
                    $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
                    $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
                    $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            } else if (!isset($_GET['cat'])) {
                $id_user = $_GET['u'];
                $getVisit = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_contact ON tb_contact.id_contact = tb_visit.id_contact WHERE tb_visit.id_user = '$id_user' ORDER BY date_visit DESC");

                while ($rowVisit = $getVisit->fetch_array(MYSQLI_ASSOC)) {
                    $id_visit = $rowVisit['id_visit'];
                    $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
                    $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
                    $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
                    $visitArray[] = $rowVisit;
                }

                if ($visitArray == null) {
                    echo json_encode(array("status" => "empty", "results" => []));
                } else {
                    echo json_encode(array("status" => "ok", "results" => $visitArray));
                }
            }
        }
    }

    if (isset($_GET['a']) && isset($_GET['s'])) {
        $id_contact = $_GET['s'];
        $getUser = mysqli_query($conn, "SELECT * FROM tb_visit JOIN tb_user ON tb_user.id_user = tb_visit.id_user WHERE id_contact = '$id_contact' GROUP BY tb_visit.id_user");

        while ($rowUser = $getUser->fetch_array(MYSQLI_ASSOC)) {
            $id_visit = $rowVisit['id_visit'];
            $getAnswer = mysqli_query($conn, "SELECT * FROM tb_visit_answer WHERE id_visit = '$id_visit' LIMIT 1");
            $rowAnswer = $getAnswer->fetch_array(MYSQLI_ASSOC);
            $rowVisit['has_checklist'] = $rowAnswer != null ? "1" : "0";
            $userArray[] = $rowUser;
        }

        if ($userArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $userArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_contact'])) {
        $id_contact = $_POST['id_contact'] ? $_POST['id_contact'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $source = $_POST['source'];
        $laporan_visit = "[" . $source . "] " .  $_POST['laporan_visit'];
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;
        $type_renvi = $_POST['type_renvi'];
        $is_pay = isset($_POST['is_pay']) ? $_POST['is_pay'] : '0';


        $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user = '$id_user'");
        $rowUser = $getUser->fetch_array(MYSQLI_ASSOC);

        $insertVisit = false;
        $id_visit = 0;

        if ($is_pay != "0") {
            $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
            // $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
            $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';

            $id_invoice = $_POST['id_invoice'];

            $getInv = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE id_invoice = '$id_invoice'");
            $rowInv = $getInv->fetch_array(MYSQLI_ASSOC);

            $no_inv = $rowInv['no_invoice'];

            $resultContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'");
            $rowContact = $resultContact->fetch_array(MYSQLI_ASSOC);

            $nama = $rowContact['nama'];
            $nomor_hp = $rowContact['nomorhp'];
            $id_distributor = $rowContact['id_distributor'];

            // $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            // $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
            $full_name = "PT Top Mortar Indonesia";
            // $integration_id = $rowQontak['integration_id'];
            // $wa_token = $rowQontak['token'];

            $insertVisit = false;

            if ($is_pay == "pay") {
                $pay_value = $_POST['pay_value'];
                $laporan_visit = "[" . $source . "] " .  $_POST['laporan_visit'] . " - Nominal Pembayaran: Rp. " .  number_format($pay_value, 0, ',', '.');

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay,pay_value,is_approved) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user,'$is_pay',$pay_value,1)");
                $id_visit = mysqli_insert_id($conn);

                $message = "Terimakasih telah melakukan pembayaran sebesar Rp. " . number_format($pay_value, 0, ',', '.') . ". ";

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

                    if ($res['status'] == 'success') {
                        $nomor_hp_admin = "6289636224827";
                        $nama_admin = "April";
                        if ($id_distributor == 6) {
                            $nomor_hp_admin = "628";
                            $nama_admin = "Dea";
                        }
                        $message = "Toko " . $nama . "telah melakukan pembayaran sebesar Rp. " . number_format($pay_value, 0, ',', '.') . ". ";

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
                                $nama_admin,
                                trim(preg_replace('/\s+/', ' ', $message)),
                                $full_name,
                            ],
                            'phone_number' => $nomor_hp_admin,
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
                    }
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

                // echo $response;
            } else if ($is_pay == "pay_later") {
                $pay_date = $_POST['pay_date'];

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay,pay_date,id_invoice) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user,'$is_pay','$pay_date',$id_invoice)");
                $id_visit = mysqli_insert_id($conn);

                if ($insertVisit) {
                    $message = "Hari ini kami belum menerima pembayaran dan janji bayar pada tanggal " . date('d M Y', strtotime($pay_date)) . ". Terimakasih";

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

                        if ($res['status'] == 'success') {
                            $nomor_hp_admin = "6289636224827";
                            $nama_admin = "April";
                            if ($id_distributor == 6) {
                                $nomor_hp_admin = "628";
                                $nama_admin = "Dea";
                            }
                            $message = "Toko " . $nama . " menjanjikan pembayaran pada tanggal " . date("Y-m-d", strtotime($pay_date));

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
                                    $nama_admin,
                                    trim(preg_replace('/\s+/', ' ', $message)),
                                    $full_name,
                                ],
                                'phone_number' => $nomor_hp_admin,
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
                        }
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
                }
            } else if ($is_pay == "not_pay") {
                // $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay,pay_date,id_invoice) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user,'$is_pay','$pay_date',$id_invoice)");

                $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user,is_pay) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user, '$is_pay')");
                $id_visit = mysqli_insert_id($conn);

                if ($insertVisit) {
                    $message = "Hari ini kami belum menerima pembayaran mohon dibantu pembayaran nya. Terimakasih";

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

                        if ($res['status'] == 'success') {
                            $nomor_hp_admin = "6289636224827";
                            $nama_admin = "April";
                            if ($id_distributor == 6) {
                                $nomor_hp_admin = "628";
                                $nama_admin = "Dea";
                            }
                            $message = "Toko " . $nama . " hari ini belum melakukan pembayaran ";

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
                                    $nama_admin,
                                    trim(preg_replace('/\s+/', ' ', $message)),
                                    $full_name,
                                ],
                                'phone_number' => $nomor_hp_admin,
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
                        }
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
                }
            }
        } else {
            $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_contact, $distance_visit, '$laporan_visit','$type_renvi', $id_user)");
            $id_visit = mysqli_insert_id($conn);
        }

        // if ($insertVisit) {
        $visitDate = date("Y-m-d H:i:s");
        if ($type_renvi == 'jatem3') {
            $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'jatem'");

            $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem3'");
        } else if ($type_renvi == 'mg') {
            $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'mg'");
        } else {
            if ($type_renvi == 'jatem2') {
                $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2'");
            } else if ($type_renvi == 'jatem1') {
                $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1'");
            } else if ($type_renvi == 'voucher') {
                $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
                // Update pasif also
                $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'passive'");
            } else if ($type_renvi == 'passive') {
                // Send notif passive ke toko
                $resultContact = mysqli_query($conn, "SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE id_contact = '$id_contact'");
                $rowContact = $resultContact->fetch_array(MYSQLI_ASSOC);

                $nama = $rowContact['nama'];
                $nomor_hp = $rowContact['nomorhp'];

                $id_distributor = $rowUser['id_distributor'];

                $message = "Halo, " . $nama . ". terimakasih terimakasih atas waktu kunjungannya ";

                $full_name = 'PT Top Mortar Indonesia';

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

                echo json_encode($rowHaloai);
                echo $response;

                $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
            } else if ($type_renvi == 'tagih_mingguan' || $type_renvi == 'weekly') {
                $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'tagih_mingguan'");
            } else {
                if ($source == 'renvisales') {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = '$type_renvi'");
                } else {
                    $getRenvis = mysqli_query($conn, "UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact'");
                }
            }
        }

        $id_bid = $rowBid['id_bid'];
        $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim laporan!", "id_visit" => (string)$id_visit];
        echo json_encode($response);
        // } else {
        //     $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan laporan! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
        //     echo json_encode($response);
        // }
    } else if (isset($_POST['id_gudang'])) {
        $id_gudang = $_POST['id_gudang'] ? $_POST['id_gudang'] : 0;
        $distance_visit = $_POST['distance_visit'] ? str_replace(',', '.', $_POST['distance_visit']) : 0;
        $laporan_visit = $_POST['laporan_visit'] ? $_POST['laporan_visit'] : '';
        $id_user = $_POST['id_user'] ? $_POST['id_user'] : 0;
        $source = $_POST['source'] == null ? 'absen_in' : $_POST['source'];

        $insertVisit = mysqli_query($conn, "INSERT INTO tb_visit(id_contact,distance_visit,laporan_visit,source_visit,id_user) VALUES($id_gudang, $distance_visit, '$laporan_visit', '$source', $id_user)");
        $id_visit = mysqli_insert_id($conn);

        if ($insertVisit) {
            $id_bid = $rowBid['id_bid'];
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim absen!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal menyimpan absen! " . mysqli_error($conn), "detail" => mysqli_error($conn)];
            echo json_encode($response);
        }
    }
}
