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

    if (isset($_GET['cw_id'])) {

        $id = $_GET['cw_id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_id = '$id'");

        if ($result) {

            if (mysqli_num_rows($result) > 0) {

                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $jsonString = $row['cw_data'];
                    $jsonObject = json_decode($jsonString, true);
                    $row['cw_data'] = json_decode($jsonObject, true);
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

    } else if (isset($_GET['cw_event'])) {

        $event = $_GET['cw_event'];

        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_event = '$event'");

        if ($result) {

            if (mysqli_num_rows($result) > 0) {

                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $jsonString = $row['cw_data'];
                    $jsonObject = json_decode($jsonString, true);
                    $row['cw_data'] = json_decode($jsonObject, true);
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
                    $jsonString = $row['cw_data'];
                    $jsonObject = json_decode($jsonString, true);
                    $row['cw_data'] = json_decode($jsonObject, true);
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

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $input = json_decode(file_get_contents("php://input"), true);

    if ($input !== null) {

        $event = $input['event'];
        $webhookData = mysqli_real_escape_string($conn, json_encode($input));
        $jsonString = json_encode($webhookData, JSON_PRETTY_PRINT);

        $query = "INSERT INTO tb_clickup_webhook (cw_data, cw_event) VALUES ('$jsonString', '$event')";

        if (mysqli_query($conn, $query)) {
            echo json_encode(array("status" => "ok", "message" => "Data stored successfully"));
        } else {
            echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
        }

    } else {
        echo json_encode(array("status" => "failed", "error" => "Invalid JSON input"));
    }

    mysqli_close($conn);

} else {
    echo json_encode(array("status" => "failed", "error" => "Invalid request method"));
}
