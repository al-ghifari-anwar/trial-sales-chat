<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = 'ee3637b7-41bc-4032-96f8-96a748e448f4';
// $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_surat_jalan = $_GET['id_surat_jalan'];

    $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE status_invoice = 'waiting' AND tb_invoice.id_surat_jalan = '$id_surat_jalan'");

    while ($rowInv = $resultInv->fetch_array(MYSQLI_ASSOC)) {
        $invArray[] = $rowInv;
    }

    foreach ($invArray as $invArray) {
        $id_invoice = $invArray['id_invoice'];
        // Calculate sisa hari jatuh tempo
        $jatuhTempo = date('d M Y', strtotime("+" . $invArray['termin_payment'] . " days", strtotime($invArray['date_invoice'])));
        $date1 = new DateTime(date("Y-m-d"));
        $date2 = new DateTime($jatuhTempo);
        $days  = $date2->diff($date1)->format('%a');
        $operan = "";
        if ($date1 < $date2) {
            $operan = "-";
        }
        $days = $operan . $days;

        // Data Body


        if ($days == "-3") {
            // if ($invArray['id_surat_jalan'] == '1633') {
            $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
            $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

            $id_distributor = $invArray['id_distributor'];
            $nomor_hp = $invArray['nomorhp'];
            $nama = $invArray['nama'];
            $no_invoice = $invArray['no_invoice'];
            $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');

            // $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            // $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);

            // $integration_id = $rowQontak['integration_id'];
            // $wa_token = $rowQontak['token'];
            // echo json_encode($sisaHutang);
            if ($sisaHutang > 0) {
                $getHaloai = mysqli_query($conn, "SELECT * FROM tb_haloai WHERE id_distributor = '$id_distributor'");
                $rowHaloai = $getHaloai->fetch_array(MYSQLI_ASSOC);
                $wa_token = $rowHaloai['token_haloai'];
                $business_id = $rowHaloai['business_id_haloai'];
                $channel_id = $rowHaloai['channel_id_haloai'];
                $template = 'tagihan_min_3';
                $messageText = "Salam toko *$nama* Dengan ini kami memberitahukan bahwa tagihan Bapak/Ibu dengan nomor invoice *$no_invoice* Sebesar *$sisaHutang* akan jatuh pada *$jatuhTempo* (3 hari sebelum jatuh tempo) Mohon kerjasamanya untuk melakukan pembayaran tepat waktu. (_*Jika Bapak/Ibu sudah melakukan pembayaran maka bisa abaikan pesan ini*_) Kami tunggu orderan anda selanjutnya, Terima Kasih. Salam Hangat, PT Top Mortar Indonesia";

                $haloaiPayload = [
                    'activate_ai_after_send' => false,
                    'channel_id' => $channel_id,
                    'fallback_template_message' => $template,
                    'fallback_template_variables' => [
                        $nama,
                        $no_invoice,
                        $sisaHutang,
                        $jatuhTempo,
                    ],
                    'phone_number' => $nomor_hp,
                    'text' => trim(preg_replace('/\s+/', ' ', $messageText)),
                ];

                // if ($id_distributor == 2) {
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
                    $response = ["response" => 200, "status" => "ok", "message" => "Success notify customer", "inv" => $invArray['no_invoice']];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed notify customer. ", "mysql" => mysqli_error($conn), "inv" => $invArray['no_invoice'], "qontak" => $res];
                    echo json_encode($response);
                }
                // }
            }
            // }
        } else {
            $response = ["message" => "Belum waktunya", "days" => $days, "date_inv" => $invArray['date_invoice'], "inv" => $invArray['no_invoice']];
            echo json_encode($response);
        }
        // echo $jatuhTempo;
    }
}
