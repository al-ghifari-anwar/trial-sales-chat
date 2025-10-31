<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $countActive = mysqli_query($conn, "SELECT COUNT(*) AS jml_active, tb_contact.id_city, id_distributor FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE store_status = 'active' GROUP BY tb_contact.id_city");

    while ($rowCountActive = $countActive->fetch_array(MYSQLI_ASSOC)) {
        $arrayCountActive[] = $rowCountActive;
    }

    foreach ($arrayCountActive as $active) {
        // $month = date('m');
        // $month = 9;
        $month = date('n');
        $jml_active = $active['jml_active'];
        $id_city = $active['id_city'];
        $id_distributor = $active['id_distributor'];
        $updated_at = date("Y-m-d H:i:s");

        $insertActive = mysqli_query($conn, "INSERT INTO tb_active_store(month_active,jml_active,id_city,id_distributor,updated_at) VALUES('$month',$jml_active,$id_city,$id_distributor,'$updated_at')");

        if ($insertActive) {
            $nomor_hp = '6281808152028';
            $nama = 'Pak hartawan';
            $message = 'Toko aktif berhasil disimpan';
            $full_name = 'PT Top Mortar Indonesia';

            $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
            $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';
            $wa_token = '8EU-_tfPuTuqHMHpJHh2CycWEKbxAszXAxXs-qmL59Y';

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
                                    "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
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

            $status = isset($res['status']) ? $res['status'] : 'empty';

            echo json_encode(array("status" => "ok", "results" => "Sukses"));
        } else {
            echo json_encode(array("status" => "failed", "results" => mysqli_error($conn)));
        }
    }
}
