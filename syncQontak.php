<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

$token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';

$curl = curl_init();

for ($i = 1; $i < 50; $i++) {
    // echo $i;
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/rooms?offset=' . $i,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);

    $response = json_decode($response, true);
    // $response = json_encode($response);

    $data = json_encode($response['data']);

    curl_close($curl);
    if ($data == "[]") {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        foreach (json_decode($data, true) as $res) {
            $nomor_hp = $res['account_uniq_id'];
            $nama = $res['name'];
            $checkKontak = mysqli_query($conn, "SELECT * FROM tb_contact WHERE nomorhp = '$nomor_hp'");

            $row = $checkKontak->fetch_array(MYSQLI_ASSOC);
            if ($row == null) {
                $result = mysqli_query($conn, "INSERT INTO tb_contact(nama, nomorhp) VALUES('$nama', '$nomor_hp')");
            } else {
                $result = mysqli_query($conn, "UPDATE tb_contact SET nama = '$nama' WHERE nomorhp = '$nomor_hp'");
            }
        }
    }
}

echo json_encode(array("status" => "ok", "results" => ["message" => "Sync process success"]));
