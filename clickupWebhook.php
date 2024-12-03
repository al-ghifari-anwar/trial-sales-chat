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

$curlClickUpChance = 5;
$clickUpToken = "pk_66658751_Z5F1B52LQLC4XMA0CKNZRQ3FZ6DO3NH4";
$curlWaChance = 2;
$templateCreated = "b654d032-02d6-41f7-b1d8-66c5d28211e8";
$templateStatusUpdated = "089cc73a-2bcb-45e2-9f87-3ff65abcea4c";
$listUsers = [
    [
        "email" => "keerouk.ink@gmail.com",
        "phone" => "62895636998639",
    ],
    [
        "email" => "mochammadrafliramadani@gmail.com",
        "phone" => "62895636998639",
    ],
    [
        "email" => "diahnurkhasanah5@gmail.com",
        "phone" => "6285770348227",
    ],
    [
        "email" => "alghifari.anwar2002@gmail.com",
        "phone" => "6285546112267",
    ],
    [
        "email" => "hart.jessica.jh@gmail.com",
        "phone" => "6287771736555",
    ],
    [
        "email" => "nunsafitri@gmail.com",
        "phone" => "6281802358317",
    ],
];

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
            if (isset($historyItems['before']['status'])) {
                if ($historyItems['before']['status'] == null) {
                    echo json_encode(array("status" => "ok", "results" => "This is a new data"));
                    return;
                }
            } else {
                echo json_encode(array("status" => "ok", "results" => "This is a new data"));
                return;
            }
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
        $dateDone = $taskDetail['date_done'];

        if ($event == 'taskStatusUpdated') {
            $taskStatusBefore = $historyItems['before']['status'];
            $taskStatusAfter = $historyItems['after']['status'];
        }

        $taskTriggerBy = $historyItems['user']['username'];
        $taskNotifTo = json_encode($taskDetail['assignees']);
        $taskPriority = $taskDetail['priority']['priority'];
        $taskDueDate = convertDateTime($taskDetail['due_date']);
        $taskUrl = $taskDetail['url'];

        if (isset($dateDone) && $dateDone != null) {
            $cwDateDone = convertDateTime($dateDone, "Y-m-d h:i:s");
        } else {
            $cwDateDone = null;
        }

        $messageTo = $taskNotifTo;

        if ($event == 'taskCreated') {
            $messageText = "Task baru level $taskPriority. $taskName baru saja ditambahkan oleh $taskTriggerBy dan ditujukan kepada anda, segera tinjau sebelum $taskDueDate. Cek task mu di ClickUp untuk melihat detailnya.. $taskUrl";
        } else if($event == 'taskStatusUpdated') {
            $messageText = "Status $taskName telah berubah dari $taskStatusBefore menjadi $taskStatusAfter. $taskTriggerBy baru saja menyerahkan task kepada anda, segera tinjau sebelum $taskDueDate. Cek lebih detail mengenai task tersebut di ClickUp.. $taskUrl";
        }

        $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = 1");
        $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
        $waToken = $rowQontak['token'];
        $integrationId = $rowQontak['integration_id'];

        $emailArray = array_map(function($item) {

            $user = getUserByEmail($item['email']);

            if ($user != null) {

                $targetPhone = $user['phone'];
                $targetName = $item['username'];

                // tryNotifToWhatsapp($targetPhone, $targetName);
                return $item['email'];
                // echo json_encode($user);
                // $responseNotif = tryNotifToWhatsapp($targetPhone, $targetName);
                // echo $responseNotif;
            }

        }, json_decode($taskNotifTo, true));

        $emailArrayEncoded = json_encode(mysqli_real_escape_string($conn, json_encode($emailArray)));
        $taskDetailEncoded = json_encode(mysqli_real_escape_string($conn, json_encode($taskDetail)));

        // echo json_encode([
        //     "message_to" => $emailArray,
        //     // "task_detail" => $taskDetail,
        //     // "response_notif" => json_decode($responseNotif, true),
        //     "message_text" => $messageText,
        // ]);
        // return;

        $availableTask = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_task_id = '$taskId'");

        if ($availableTask) {

            if (mysqli_num_rows($availableTask) > 0) {

                if ($cwDateDone != null) {
                    $query = "UPDATE tb_clickup_webhook SET cw_event = '$event', cw_message_to = '$emailArrayEncoded', cw_message_text = '$messageText', cw_date_done = '$cwDateDone', cw_data = '$data', cw_task_detail = '$taskDetailEncoded' WHERE cw_task_id = '$taskId'";
                } else {
                    $query = "UPDATE tb_clickup_webhook SET cw_event = '$event', cw_message_to = '$emailArrayEncoded', cw_message_text = '$messageText', cw_date_done = null, cw_data = '$data', cw_task_detail = '$taskDetailEncoded' WHERE cw_task_id = '$taskId'";
                }

                if (mysqli_query($conn, $query)) {
                    echo json_encode(array("status" => "ok", "message" => "Data updated successfully"));
                } else {
                    echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
                }

            } else {

                $query = "INSERT INTO tb_clickup_webhook (cw_task_id, cw_webhook_id, cw_event, cw_message_to, cw_message_text, cw_data, cw_task_detail) VALUES ('$taskId', '$webhookId', '$event', '$emailArrayEncoded', '$messageText', '$data', '$taskDetailEncoded')";

                if (mysqli_query($conn, $query)) {
                    echo json_encode(array("status" => "ok", "message" => "Data stored successfully"));
                } else {
                    echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
                }

            }

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
    global $clickUpToken;
    $url = "https://api.clickup.com/api/v2/task/$taskId";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $clickUpToken,
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

    global $curlClickUpChance;

    if ($taskDetailResponseJson['status'] == 'failed') {
        if ($curlClickUpChance > 0) {
            $curlClickUpChance --;
            return tryToGetTaskDetail($taskId);
        }
    }

    return json_encode($taskDetailResponseJson);
}

function notifToWhatsapp($targetPhone, $targetName) {

    global $waToken, $event, $templateCreated, $templateStatusUpdated, $integrationId, $taskName, $taskStatusBefore, $taskStatusAfter, $taskTriggerBy, $taskPriority, $taskDueDate, $taskUrl;

    if ($event == 'taskCreated') {

        $postFields = '{
            "to_number": "' . $targetPhone . '",
            "to_name": "' . $targetName . ' - ClickUp Created",
            "message_template_id": "' . $templateCreated . '",
            "channel_integration_id": "' . $integrationId . '",
            "language": {
                "code": "id"
            },
            "parameters": {
                "body": [
                    {
                        "key": "1",
                        "value": "priority",
                        "value_text": "' . $taskPriority . '"
                    },
                    {
                        "key": "2",
                        "value": "taskname",
                        "value_text": "' . $taskName . '"
                    },
                    {
                        "key": "3",
                        "value": "triggerby",
                        "value_text": "' . $taskTriggerBy . '"
                    },
                    {
                        "key": "4",
                        "value": "duedate",
                        "value_text": "' . $taskDueDate . '"
                    },
                    {
                        "key": "5",
                        "value": "url",
                        "value_text": "' . $taskUrl . '"
                    }
                ]
            }
        }';

    } else if ($event == 'taskStatusUpdated') {

        $postFields = '{
            "to_number": "' . $targetPhone . '",
            "to_name": "' . $targetName . ' - ClickUp Status Updated",
            "message_template_id": "' . $templateStatusUpdated . '",
            "channel_integration_id": "' . $integrationId . '",
            "language": {
                "code": "id"
            },
            "parameters": {
                "body": [
                    {
                        "key": "1",
                        "value": "taskname",
                        "value_text": "' . $taskName . '"
                    },
                    {
                        "key": "2",
                        "value": "statusbefore",
                        "value_text": "' . $taskStatusBefore . '"
                    },
                    {
                        "key": "3",
                        "value": "statusafter",
                        "value_text": "' . $taskStatusAfter . '"
                    },
                    {
                        "key": "4",
                        "value": "triggerby",
                        "value_text": "' . $taskTriggerBy . '"
                    },
                    {
                        "key": "5",
                        "value": "priority",
                        "value_text": "' . $taskPriority . '"
                    },
                    {
                        "key": "6",
                        "value": "duedate",
                        "value_text": "' . $taskDueDate . '"
                    },
                    {
                        "key": "7",
                        "value": "url",
                        "value_text": "' . $taskUrl . '"
                    }
                ]
            }
        }';

    }
    
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
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $waToken,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;;
}

function tryNotifToWhatsapp($targetPhone, $targetName) {
    $response = notifToWhatsapp($targetPhone, $targetName);
    $responseObject = json_decode($response, true);

    global $curlWaChance;

    if ($responseObject['status'] == 'error' && $curlWaChance > 0) {
        $curlWaChance --;
        return tryNotifToWhatsapp($targetPhone, $targetName);
    }

    return json_encode($responseObject);
}

function convertDateTime($timestampMs, $dateOutFormat = "d F Y") {

    $timestampSec = $timestampMs / 1000;
    $formattedDate = date($dateOutFormat, $timestampSec);

    return $formattedDate;

}

function getUserByEmail($email) {
    global $listUsers;
    foreach ($listUsers as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}
