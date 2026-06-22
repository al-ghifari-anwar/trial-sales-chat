<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $getUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE phone_user != '0' AND password != '0' AND level_user IN ('sales','courier')");

    while ($user = $getUser->fetch_array(MYSQLI_ASSOC)) {
        $id_user = $user['id_user'];
        $date = date('Y-m-d');
        $full_name = $user['full_name'];

        $absenMasuk = mysqli_query($conn, " SELECT * FROM tb_visit WHERE id_user = $id_user AND DATE(date_visit) = '$date' AND source_visit LIKE '%absen_in%' ")->fetch_array(MYSQLI_ASSOC);

        if (!$absenMasuk) {
            $curl = curl_init();

            $message = "Pengguna Belum Absen\n\nNama: " . $full_name . "";

            $telegramPayload = [
                'chat_id' => -5138489487,
                'text' => $message,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.telegram.org/bot8494834740:AAEZLYfkzUhrY6GroazEJOf876oToo2-qIw/sendMessage',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($telegramPayload),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        }
    }
}
