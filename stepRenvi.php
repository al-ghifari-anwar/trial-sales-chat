<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'tagihan') {
        $id_city = $_GET['id_city'];
        $dateNow = date('Y-m-d');

        $getStepRenvis = mysqli_query($conn, " SELECT * FROM tb_step_renvi WHERE date_step_renvi = '$dateNow' AND number_step_renvi IN(1,2,3) AND id_city = '$id_city' ORDER BY number_step_renvi ASC ");

        $renvis = array();

        while ($rowStepRenvi = $getStepRenvis->fetch_array(MYSQLI_ASSOC)) {
            $id_contact = $rowStepRenvi['id_contact'];

            $renvis[] = $rowStepRenvi;
        }

        if ($renvis == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $renvis));
        }
    } else if ($_GET['type'] == 'selected') {
        $id_city = $_GET['id_city'];
        $dateNow = date('Y-m-d');

        $renvi = mysqli_query($conn, " SELECT * FROM tb_step_renvi WHERE date_step_renvi = '$dateNow' AND number_step_renvi NOT IN(1,2,3) AND is_active = 1 AND is_visited = 1 AND id_city = '$id_city' ")->fetch_array(MYSQLI_ASSOC);

        if ($renvi == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $renvi));
        }
    } else if ($_GET['type'] == 'suggestion') {
        $id_city = $_GET['id_city'];
        $long = $_GET['long'];
        $lat = $_GET['lat'];

        $dateNow = date('Y-m-d');

        $getStepRenvis = mysqli_query($conn, " SELECT * FROM tb_step_renvi JOIN tb_contact ON tb_contact.id_contact = tb_step_renvi.id_contact WHERE date_step_renvi = '$dateNow' AND number_step_renvi NOT IN(1,2,3) AND is_active = 0 AND tb_step_renvi.id_city = '$id_city' ORDER BY number_step_renvi ASC LIMIT 10");

        $renvis = array();
        $coordinates = array();

        $coordinates[] = $long . "," . $lat;

        while ($rowStepRenvi = $getStepRenvis->fetch_array(MYSQLI_ASSOC)) {
            $id_contact = $rowStepRenvi['id_contact'];

            // koordinat toko
            $store_coordinate = explode(",", $rowStepRenvi['maps_url']);

            $coordinates[] = $store_coordinate[1] . ',' . $store_coordinate[0];

            $renvis[] = $rowStepRenvi;
        }

        if ($renvis == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            // Hit OSRM untuk jarak
            $coordinateString = implode(';', $coordinates);

            $url = 'http://router.project-osrm.org/table/v1/driving/' . $coordinateString . '?sources=0&destinations=' . urlencode(implode(';', range(1, count($renvis)))) . '&annotations=duration,distance';

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,

                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

                CURLOPT_USERAGENT => 'Mozilla/5.0',

                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                ],
            ]);

            $response = curl_exec($curl);

            $info = curl_getinfo($curl);
            $errorNo = curl_errno($curl);
            $error = curl_error($curl);

            if ($response === false) {
                // curl_close($curl);

                // echo json_encode([
                //     "status" => "error",
                //     "message" => "Failed to connect OSRM"
                // ]);

                // exit;
                echo '<pre>';
                var_dump([
                    'url' => $url,
                    'response' => $response,
                    'curl_errno' => $errorNo,
                    'curl_error' => $error,
                    'http_code' => $info['http_code'] ?? null,
                    'content_type' => $info['content_type'] ?? null,
                    'total_time' => $info['total_time'] ?? null,
                ]);
                echo '</pre>';

                exit;
            }

            curl_close($curl);

            $osrm = json_decode($response, true);

            if (!isset($osrm['code']) || $osrm['code'] !== 'Ok') {

                // echo json_encode([
                //     "status" => "error",
                //     "message" => "OSRM error",
                //     "osrm" => $osrm
                // ]);

                // exit;
                echo '<pre>';
                var_dump([
                    'url' => $url,
                    'response' => $response,
                    'curl_errno' => $errorNo,
                    'curl_error' => $error,
                    'http_code' => $info['http_code'] ?? null,
                    'content_type' => $info['content_type'] ?? null,
                    'total_time' => $info['total_time'] ?? null,
                ]);
                echo '</pre>';

                exit;
            }

            // Assign koordinat dan durasi
            foreach ($renvis as $index => &$renvi) {
                // Hitung estimasi durasi
                $rawDuration = $osrm['distances'][0][$index] / 35000;
                $duration = $rawDuration * 60;

                // Karena index 0 adalah source
                $osrmIndex = $index + 1;

                $renvi['distance'] = $osrm['distances'][0][$index];
                $renvi['duration'] = $duration;
            }

            unset($renvi);

            // Sorting array
            usort($renvis, function ($a, $b) {

                return $a['distance'] <=> $b['distance'];
            });

            echo json_encode(array("status" => "ok", "results" => $renvis));
        }
    }
}
