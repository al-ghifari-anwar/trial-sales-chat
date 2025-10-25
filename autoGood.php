<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getContact = mysqli_query($conn, "SELECT * FROM tb_contact WHERE reputation = 'bad'");

    while ($rowContact = $getContact->fetch_array(MYSQLI_ASSOC)) {
        $id_contact = $rowContact['id_contact'];
        $nama = $rowContact['nama'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://order.topmortarindonesia.com/scoring/combine/' . $id_contact,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Cookie: ci_session=qpce73v2r0rrmnnmo30shsnl7j3cvidf'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resScoring = json_decode($response, true);

        if (isset($resScoring['payment'])) {
            if ($resScoring['payment'] >= '85') {
                $setReputation = mysqli_query($conn, "UPDATE tb_contact SET reputation = 'good' WHERE id_contact = '$id_contact'");

                $response = ["response" => 200, "status" => "ok", "message" => "Store is good", "id_contact" => $id_contact, "toko" => $nama, "payment" => $resScoring['payment']];
                echo json_encode($response);
            } else {
                $response = ["response" => 400, "status" => "failed", "message" => "Store is bad", "id_contact" => $id_contact, "toko" => $nama, "payment" => $resScoring['payment']];
                echo json_encode($response);
            }
        }
    }
}
