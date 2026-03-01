<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = 'cef1a14c-0441-4927-941c-5d785ed81f76';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getVoucher = mysqli_query($conn, "SELECT * FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE is_claimed = 0 GROUP BY tb_voucher.id_contact");
    while ($rowGetVoucher = $getVoucher->fetch_array(MYSQLI_ASSOC)) {
        $getVoucherArr[] = $rowGetVoucher;
    }

    foreach ($getVoucherArr as $getVoucherArr) {
        $oneWeek = date("Y-m-d", strtotime("-1 week"));
        $dateVoucher = date("Y-m-d", strtotime($getVoucherArr['date_voucher']));

        $codeArr = array();
        if ($oneWeek == $dateVoucher) {
            $id_contact = $getVoucherArr['id_contact'];
            $nomor_hp = $getVoucherArr['nomorhp'];
            $nama = $getVoucherArr['nama'];
            $getVoucherCode = mysqli_query($conn, "SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND DATE(date_voucher) = '$dateVoucher'");
            while ($rowVoucherCode = $getVoucherCode->fetch_array(MYSQLI_ASSOC)) {
                $codeArr[] = $rowVoucherCode;
            }
            $vouchers = "";
            $jml_voucher = count($codeArr);
            foreach ($codeArr as $codeArr) {
                $vouchers .= $codeArr['no_voucher'] . ",";
            }

            // echo "send message";
            $message = "Anda masih memiliki kode voucher yang belum ditukarkan. Tukarkan voucher anda dengan produk-produk unggulan kami sebelum tanggal " . date("d M, Y", strtotime($getVoucherArr['exp_date'])) . ". Kode voucher: " . $vouchers;

            $id_city = $getVoucherArr['id_city'];

            $getCity = mysqli_query($conn, "SELECT * FROM tb_city WHERE id_city = '$id_city'");
            $rowCity = $getCity->fetch_array(MYSQLI_ASSOC);
            $id_distributor = $rowCity['id_distributor'];

            // $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            // $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
            // $integration_id = $rowQontak['integration_id'];
            // $wa_token = $rowQontak['token'];
            // $template_id = "9241bf86-ae94-4aa8-8975-551409af90b9";

            $getHaloai = mysqli_query($conn, "SELECT * FROM tb_haloai WHERE id_distributor = '$id_distributor'");
            $rowHaloai = $getHaloai->fetch_array(MYSQLI_ASSOC);
            $wa_token = $rowHaloai['token_haloai'];
            $business_id = $rowHaloai['business_id_haloai'];
            $channel_id = $rowHaloai['channel_id_haloai'];
            $template = 'notif_materi_vid2';

            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                'fallback_template_message' => $template,
                'fallback_template_header' => [
                    "filename" => "video.mp4",
                    'type' => 'video',
                    'url' => 'https://saleswa.topmortarindonesia.com/vids/reminder_1.mp4',
                ],
                'fallback_template_variables' => [
                    trim(preg_replace('/\s+/', ' ', $message)),
                ],
                'media' => [
                    "filename" => "video.mp4",
                    'type' => 'video',
                    'url' => 'https://saleswa.topmortarindonesia.com/vids/reminder_1.mp4',
                ],
                'phone_number' => $nomor_hp,
                'text' => trim(preg_replace('/\s+/', ' ', $message)),
            ];

            // Send Message
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
            //         "to_number": "' . $nomor_hp . '",
            //         "to_name": "' . $nama . '",
            //         "message_template_id": "' . $template_id . '",
            //         "channel_integration_id": "' . $integration_id . '",
            //         "language": {
            //             "code": "id"
            //         },
            //         "parameters": {
            //             "header":{
            //                 "format":"VIDEO",
            //                 "params": [
            //                     {
            //                         "key":"url",
            //                         "value":"https://saleswa.topmortarindonesia.com/vids/reminder_1.mp4"
            //                     },
            //                     {
            //                         "key":"filename",
            //                         "value":"bday.mp4"
            //                     }
            //                 ]
            //             },
            //             "body": [
            //             {
            //                 "key": "1",
            //                 "value": "nama",
            //                 "value_text": "' . $nama . '"
            //             },
            //             {
            //                 "key": "2",
            //                 "value": "jml_voucher",
            //                 "value_text": "' . $jml_voucher . '"
            //             },
            //             {
            //                 "key": "3",
            //                 "value": "tgl",
            //                 "value_text": "' . date("d M, Y", strtotime($getVoucherArr['exp_date'])) . '"
            //             }
            //             ]
            //         }
            //         }',
            //     CURLOPT_HTTPHEADER => array(
            //         'Authorization: Bearer ' . $wa_token,
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);

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

            if ($status == "success") {
                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengirim reminder voucher!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengirim reminder voucher!", 'detail' => $res];
                echo json_encode($response);
            }
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "No need to send message!"];
            echo json_encode($response);
        }
    }
}
