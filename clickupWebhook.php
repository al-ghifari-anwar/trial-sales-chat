<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

// $conn = new mysqli("185.201.8.148", "user_bintang", "123123!", "admin_dev_saleschat");
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
//     return;
// } else {
//     echo "Koneksi berhasil";
// }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE id = '$id'");

        if ($result) {

            if (mysqli_num_rows($result) > 0) {

                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $jsonString = $row['webhook'];
                    $jsonObject = json_decode($jsonString, true);
                    $row['webhook'] = json_decode($jsonObject, true);
                    $transArray[] = $row;
                }

                if (json_last_error() === JSON_ERROR_NONE) {
                    echo json_encode(array("status" => "ok", "results" => $transArray));
                } else {
                    echo json_encode(array("status" => "empty", "message" => "Error decoding JSON: " . json_last_error_msg(), "results" => []));;
                }

            } else {
                echo json_encode(array("status" => "empty", "results" => []));
            }

        } else {
            echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
        }
        
        mysqli_close($conn);

    } else {

        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook");

        if ($result) {

            if (mysqli_num_rows($result) > 0) {

                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $jsonString = $row['webhook'];
                    $jsonObject = json_decode($jsonString, true);
                    $row['webhook'] = json_decode($jsonObject, true);
                    $transArray[] = $row;
                }

                if (json_last_error() === JSON_ERROR_NONE) {
                    echo json_encode(array("status" => "ok", "results" => $transArray));
                } else {
                    echo json_encode(array("status" => "empty", "message" => "Error decoding JSON: " . json_last_error_msg(), "results" => []));;
                }

            } else {
                echo json_encode(array("status" => "empty", "results" => []));
            }

        } else {
            echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
        }
        
        mysqli_close($conn);

    }

}
