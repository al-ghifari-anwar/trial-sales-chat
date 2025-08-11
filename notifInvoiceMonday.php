<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

// $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
$wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
$template_id = 'ee3637b7-41bc-4032-96f8-96a748e448f4';
// $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $resultInv = mysqli_query($conn, "SELECT * FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan JOIN tb_contact ON tb_contact.id_contact = tb_surat_jalan.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE status_invoice = 'waiting'");

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


        if ($days == "-2") {
            // if ($invArray['id_surat_jalan'] == '1633') {
            $getTotalPayment = mysqli_query($conn, "SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'");
            $rowPayment = $getTotalPayment->fetch_array(MYSQLI_ASSOC);

            $id_distributor = $invArray['id_distributor'];
            $nomor_hp = $invArray['nomorhp'];
            $nama = $invArray['nama'];
            $no_invoice = $invArray['no_invoice'];
            $sisaHutang = number_format($invArray['total_invoice'] - $rowPayment['amount_total'], 0, '.', ',');

            $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'");
            $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);

            $integration_id = $rowQontak['integration_id'];
            $wa_token = $rowQontak['token'];
            // echo json_encode($sisaHutang);
            if ($sisaHutang > 0) {
                // if ($id_distributor == 2) {
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
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed notify customer. " . mysqli_error($conn), "detail" => mysqli_error($conn), "qontak" => $res];
                    echo json_encode($response);
                }
                // }
            }
            // }
        } else {
            $response = ["message" => "Belum waktunya", "days" => $days, "date_inv" => $invArray['date_invoice']];
            echo json_encode($response);
        }
        // echo $jatuhTempo;
    }
}
