<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (date('D') != 'Sun') {
        $getUser = mysqli_query($conn, "SELECT * FROM tb_user JOIN tb_city ON tb_city.id_city = tb_user.id_city WHERE password != '0' AND level_user IN ('sales','courier') AND tb_user.id_distributor = 1");

        while ($user = $getUser->fetch_array(MYSQLI_ASSOC)) {
            $id_user = $user['id_user'];
            $date = date('Y-m-d');
            $full_name = $user['full_name'];
            $level_user = $user['level_user'];
            $nama_city = $user['nama_city'];

            $absenMasuk = mysqli_query($conn, " SELECT * FROM tb_visit WHERE id_user = $id_user AND DATE(date_visit) = '$date' AND source_visit LIKE '%absen_in%' ")->fetch_array(MYSQLI_ASSOC);

            if (!$absenMasuk) {
                $curl = curl_init();

                $message = "Pengguna Belum Absen\n\nNama: " . $full_name . "\nRole: " . $level_user . "\nKota: " . $nama_city;

                $telegramPayload = [
                    'chat_id' => -5138489487,
                    'text' => $message,
                ];

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.telegram.org/bot8494834740:AAGU-lTH1_9mWAwIAIgICkn3mn9unb83nGk/sendMessage',
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
}
