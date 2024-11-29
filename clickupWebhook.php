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

set_time_limit(30);
header('Content-Type: application/json');

$curlChance = 5;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['cw_id'])) {
        $id = $_GET['cw_id'];
        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_id = '$id'");
    } else if (isset($_GET['cw_event'])) {
        $event = $_GET['cw_event'];
        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_event = '$event' ORDER BY created_at DESC");
    } else {
        $result = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook ORDER BY created_at DESC");
    }

    if ($result) {

        if (mysqli_num_rows($result) > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $cwDataString = $row['cw_data'];
                $cwDataObject = json_decode($cwDataString, true);
                $cwData = json_decode($cwDataObject, true);
                $row['cw_data'] = $cwData;
                $cwMessageToString = $row['cw_message_to'];
                $cwMessageToObject = json_decode($cwMessageToString, true);
                $cwMessageTo = json_decode($cwMessageToObject, true);
                $row['cw_message_to'] = $cwMessageTo;
                $cwTaskDetailString = $row['cw_task_detail'];
                $cwTaskDetailObject = json_decode($cwTaskDetailString, true);
                $cwTaskDetail = json_decode($cwTaskDetailObject, true);
                $row['cw_task_detail'] = $cwTaskDetail;
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

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $input = json_decode(file_get_contents("php://input"), true);

    if ($input !== null) {

        $taskId = $input['task_id'];
        $webhookId = $input['webhook_id'];
        $event = $input['event'];

        if ($event == 'taskCreated') {
            $historyItems = $input['history_items'][count($input['history_items'])-1];
        } else if ($event == 'taskStatusUpdated') {
            $historyItems = $input['history_items'][0];
        }

        $webhookData = mysqli_real_escape_string($conn, json_encode($input));
        $data = json_encode($webhookData, JSON_PRETTY_PRINT);

        $taskDetailResponse = tryToGetTaskDetail($taskId);
        $taskDetailResponseJson = json_decode($taskDetailResponse, true);

        if ($taskDetailResponseJson['status'] == 'failed') {
            echo $taskDetailResponse;
            return;
        } else {
            $taskDetailString = $taskDetailResponseJson['data'];
            $taskDetail = json_decode($taskDetailString, true);
        }

        $taskName = $taskDetail['name'];

        if ($event == 'taskStatusUpdated') {
            $taskStatusBefore = $historyItems['before']['status'];
            $taskStatusAfter = $historyItems['after']['status'];
        }

        $taskTriggerBy = $historyItems['user']['username'];
        $taskNotifTo = json_encode($taskDetail['assignees']);
        $taskPriority = $taskDetail['priority']['priority'];
        $taskDueDate = convertDateTime($taskDetail['due_date']);
        $taskUrl = $taskDetail['url'];

        $messageTo = $taskNotifTo;

        if ($event == 'taskCreated') {
            $messageText = "Task baru level $taskPriority. $taskName baru saja ditambahkan oleh $taskTriggerBy dan ditujukan kepada anda, segera tinjau sebelum $taskDueDate. Cek task mu di ClickUp untuk melihat detailnya.. $taskUrl";
        } else if($event == 'taskStatusUpdated') {
            $messageText = "Status $taskName telah berubah dari $taskStatusBefore menjadi $taskStatusAfter. $taskTriggerBy baru saja menyerahkan task kepada anda, segera tinjau sebelum $taskDueDate. Cek lebih detail mengenai task tersebut di ClickUp.. $taskUrl";
        }

        $emailArray = array_map(function($item) {
            return $item['email'];
        }, json_decode($taskNotifTo, true));

        $emailArrayEncoded = json_encode(mysqli_real_escape_string($conn, json_encode($emailArray)));
        $taskDetailEncoded = json_encode(mysqli_real_escape_string($conn, json_encode($taskDetail)));

        // echo json_encode([
        //     "message_to" => $emailArray,
        //     "task_detail" => $taskDetail,
        //     "message_text" => $messageText,
        // ]);
        // return;

        $query = "INSERT INTO tb_clickup_webhook (cw_task_id, cw_webhook_id, cw_event, cw_message_to, cw_message_text, cw_data, cw_task_detail) VALUES ('$taskId', '$webhookId', '$event', '$emailArrayEncoded', '$messageText', '$data', '$taskDetailEncoded')";

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

/*
** Usable Function
*/

function getClickUpTask($taskId) {
    $url = "https://api.clickup.com/api/v2/task/$taskId";
    $apiToken = "pk_66658751_Z5F1B52LQLC4XMA0CKNZRQ3FZ6DO3NH4";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $apiToken,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $responseObject = json_decode($response, true);

    if (curl_errno($ch)) {
        return json_encode(array("status" => "failed", "error" => curl_error($ch)));
    } else if (isset($responseObject['err'])) {
        return json_encode(array("status" => "failed", "error" => $response));
    }

    curl_close($ch);

    return json_encode(array("status" => "ok", "data" => $response));
}

function tryToGetTaskDetail($taskId) {
    $taskDetailResponse = getClickUpTask($taskId);
    $taskDetailResponseJson = json_decode($taskDetailResponse, true);

    global $curlChance;

    if ($taskDetailResponseJson['status'] == 'failed') {
        if ($curlChance > 0) {
            $curlChance --;
            return tryToGetTaskDetail($taskId);
        }
    }

    return json_encode($taskDetailResponseJson);
}

function convertDateTime($timestampMs, $dateOutFormat = "d M Y") {

    $timestampSec = $timestampMs / 1000;
    $formattedDate = date($dateOutFormat, $timestampSec);

    return $formattedDate;

}
