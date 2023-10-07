<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$template_id = 'ee3637b7-41bc-4032-96f8-96a748e448f4';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact WHERE status_invoice = 'waiting'");

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

        // Data Body


        if ($days <= 3) {
            $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
            $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

            $nomor_hp = $invArray['nomorhp'];
            $nama = $invArray['nama'];
            $no_invoice = $invArray['no_invoice'];
            $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');
            // echo json_encode($sisaHutang);
            if ($sisaHutang > 0) {

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
                            "value": "no_invoice",
                            "value_text": "' . $no_invoice . '"
                        },
                        {
                            "key": "3",
                            "value": "sisa",
                            "value_text": "' . $sisaHutang . '"
                        },
                        {
                            "key": "4",
                            "value": "jatuh_tempo",
                            "value_text": "' . $jatuhTempo . '"
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
                    $response = ["response" => 200, "status" => "ok", "message" => "Success notify customer"];
                    echo json_encode($response);
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed notify customer", "detail" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            }
        } else {
            echo "Belum waktunya";
        }
        echo $jatuhTempo;
    }
}
