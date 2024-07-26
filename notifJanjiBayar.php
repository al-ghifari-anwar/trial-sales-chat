<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = '85f17083-255d-4340-af32-5dd22f483960';

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
            $getContact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE id_contact = '$id_contact'");
            $rowContact = $getContact->fetch_array(MYSQLI_ASSOC);

            $id_distributor = $rowContact['id_distributor'];

            $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
            $full_name = "PT Top Mortar Indonesia";
            $integration_id = $rowQontak['integration_id'];

            $getInvoice = mysqli_query($conn, "SELECT * FROM tb_invoice WHERE id_invoice = '$id_invoice'");
            $rowInvoice = $getInvoice->fetch_array(MYSQLI_ASSOC);

            $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE '$id_user'");
            $rowUser = $getUser->fetch_array(MYSQLI_ASSOC);

            // Send Message To Customer
            $message = "Harap melakukan pembayaran untuk invoice: " . $rowInvoice['no_invoice'] . ". Terimakasih";

            $nomor_hp = $rowContact['nomor_hp'];
            $nama = $rowContact['nama'];

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
                // Send Message To Sales
                $message = "Waktunya untuk melakukan tagihan kepada toko " . $rowContact['nama'] . " dengan invoice : " . $rowInvoice['no_invoice'] . " yang telah dijanjikan pada tanggal " . date("d F Y", strtotime($pay_date));

                $nomor_hp = $rowUser['phone_user'];
                $nama = $rowUser['full_name'];

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
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim notif pada customer dan sales!"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim notif pada customer tapi gagal kirim notif sales!"];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim notif pada customer maupun sales!"];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Belum waktunya!"];
            echo json_encode($response);
        }
    }
}
