<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

// $conn = new mysqli("185.201.8.148", "user_bintang", "123123!", "admin_dev_saleschat");
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
//     return;
// } else {
//     echo "Koneksi berhasil";
// }

set_time_limit(30);
header('Content-Type: application/json');

$curlClickUpChance = 10;
// $clickUpToken = "pk_66658751_Z5F1B52LQLC4XMA0CKNZRQ3FZ6DO3NH4"; // My Workspace
$clickUpToken = "pk_66658751_PQ0WLFO995BF10U4L598N5C96TQDGXMX"; // Pt Top Mortar's Workspace
$curlWaChance = 5;
$templateReminderToday = "58db1978-17d9-4ad8-9ec1-226b2d99e1b2";
$templateReminderTomorrow = "56fd1f7d-0d23-4e64-a489-ca1f1af4d620";
$templateReminderOverdue = "37dba89e-2745-4ad1-8458-5ef3442fbf76";
$templateCreated = "b654d032-02d6-41f7-b1d8-66c5d28211e8";
$templateStatusUpdated = "089cc73a-2bcb-45e2-9f87-3ff65abcea4c";
$listUsers = [
    [
        "email" => "it@topmortar.com",
        "username" => "Admin Top Mortar",
        "phone" => "-",
    ],
    [
        "email" => "hartawansudihardjo@gmail.com",
        "username" => "hartawan sudiharjo",
        "phone" => "6281808152028",
    ],
    [
        "email" => "tinidiss88@gmail.com",
        "username" => "tini",
        "phone" => "6287774436555",
    ],
    [
        "email" => "tasia.hrd59@gmail.com",
        "username" => "tasia anastasia",
        "phone" => "6281230305227",
    ],
    [
        "email" => "hendrioktriz@gmail.com",
        "username" => "Hendri Oktriz",
        "phone" => "628988430185",
    ],
    [
        "email" => "keerouk.ink@gmail.com",
        "username" => "M Rafli Ramadani",
        "phone" => "62895636998639",
    ],
    [
        "email" => "mochammadrafliramadani@gmail.com",
        "username" => "Mochammad Rafli Ramadani",
        "phone" => "62895636998639",
    ],
    [
        "email" => "diahnurkhasanah5@gmail.com",
        "username" => "Diah Nur Khasanah",
        "phone" => "6285770348227",
    ],
    [
        "email" => "alghifari.anwar2002@gmail.com",
        "username" => "Al Ghifari Anwar",
        "phone" => "6285546112267",
    ],
    [
        "email" => "hart.jessica.jh@gmail.com",
        "username" => "Jessica Hart",
        "phone" => "6287771736555",
    ],
    [
        "email" => "nunsafitri@gmail.com",
        "username" => "Nun Safitri",
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
    } else if (isset($_GET['reminder'])) {

        $reminder = $_GET['reminder'];
        
        if ($reminder == 'today') {
            $query = "SELECT * 
                FROM tb_clickup_webhook 
                WHERE cw_date_done IS NULL
                AND DATE(cw_due_date) LIKE CONCAT(CURDATE(), '%')
                AND (cw_task_id, cw_event) IN (
                    SELECT cw_task_id, MAX(cw_event) 
                    FROM tb_clickup_webhook 
                    WHERE cw_date_done IS NULL
                    AND DATE(cw_due_date) LIKE CONCAT(CURDATE(), '%')
                    GROUP BY cw_task_id
                )
            ";
        } else if ($reminder == 'tomorrow') {
            $query = "SELECT * 
                    FROM tb_clickup_webhook 
                    WHERE cw_date_done IS NULL
                    AND DATE(cw_due_date) LIKE CONCAT(CURDATE() + INTERVAL 1 DAY, '%')
                    AND (cw_task_id, cw_event) IN (
                        SELECT cw_task_id, MAX(cw_event) 
                        FROM tb_clickup_webhook 
                        WHERE cw_date_done IS NULL
                        AND DATE(cw_due_date) LIKE CONCAT(CURDATE() + INTERVAL 1 DAY, '%')
                        GROUP BY cw_task_id
                    )
                ";
        } else if ($reminder == 'overdue') {
            $query = "SELECT * 
                    FROM tb_clickup_webhook 
                    WHERE cw_date_done IS NULL
                    AND DATE(cw_due_date) < CURDATE()
                    AND (cw_task_id, cw_event) IN (
                        SELECT cw_task_id, MAX(cw_event) 
                        FROM tb_clickup_webhook 
                        WHERE cw_date_done IS NULL
                        AND DATE(cw_due_date) < CURDATE()
                        GROUP BY cw_task_id
                    )
                ";
        } else {
            echo json_encode(array("status" => "failed", "error" => 'Not found'));
            return;
        }

        $result = mysqli_query($conn, $query);

    } else{
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
                if ($cwMessageToString != null) {
                    $cwMessageToObject = json_decode($cwMessageToString, true);
                    $cwMessageTo = json_decode($cwMessageToObject, true);
                    $row['cw_message_to'] = $cwMessageTo;
                }

                $cwTaskDetailString = $row['cw_task_detail'];
                if ($cwTaskDetailString != null) {
                    $cwTaskDetailObject = json_decode($cwTaskDetailString, true);
                    $cwTaskDetail = json_decode($cwTaskDetailObject, true);
                    $row['cw_task_detail'] = $cwTaskDetail;
                }

                $cwErrorlString = $row['cw_error'];
                if ($cwErrorlString != null) {
                    $cwErrorObject = json_decode($cwErrorlString, true);
                    $cwError = json_decode($cwErrorObject, true);
                    $row['cw_error'] = $cwError;
                }
                
                $transArray[] = $row;
            }

            if (json_last_error() === JSON_ERROR_NONE) {

                if (isset($reminder)) {

                    $getQontak = mysqli_query($conn, "SELECT * FROM tb_qontak WHERE id_distributor = 1");
                    $rowQontak = $getQontak->fetch_array(MYSQLI_ASSOC);
                    $waToken = $rowQontak['token'];
                    $integrationId = $rowQontak['integration_id'];
                    
                    foreach ($transArray as $key => $value) {

                        $cwId = $value['cw_id'];
                        $listEmail = $value['cw_message_to'];
                        $taskName = $value['cw_task_detail']['name'];
                        $taskUrl = $value['cw_task_detail']['url'];
                        $query = "UPDATE tb_clickup_webhook SET remindered = '$reminder' WHERE cw_id = '$cwId'";

                        if (mysqli_query($conn, $query)) {

                            $cwIdArray[] = $cwId;
                            foreach ($listEmail as $email) {
                                $user = getUserByEmail($email);

                                if ($user != null) {
                                    $targetPhone = $user['phone'];
                                    $targetName = $user['username'];
                                    tryNotifToWhatsapp($targetPhone, $targetName, $reminder);
                                    // echo $responseWa;
                                    // echo json_encode(array('target' => $targetName . '-' . $targetPhone));
                                }
                            }
                        }
                    }

                    $cwIdArray = json_encode($cwIdArray);
                    echo json_encode(array("status" => "ok", "message" => "Data '$cwIdArray' updated reminder successfully"));

                } else {
                    echo json_encode(array("status" => "ok", "results" => $transArray));
                }

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
        $dateNow = date('Y-m-d H:i:s');
        $errorMessage = null;

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
        } else if ($event = 'taskDeleted') {

            $query = "DELETE FROM tb_clickup_webhook WHERE cw_task_id = '$taskId'";

            if (mysqli_query($conn, $query)) {
                echo json_encode(array("status" => "ok", "message" => "Deleted data successfully"));
            } else {
                echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
            }

            return;
        }

        // Check available task
        $availableTask = mysqli_query($conn, "SELECT * FROM tb_clickup_webhook WHERE cw_task_id = '$taskId' AND cw_event = '$event'");

        $webhookData = mysqli_real_escape_string($conn, json_encode($input));
        $data = json_encode($webhookData, JSON_PRETTY_PRINT);

        $taskDetailResponse = tryToGetTaskDetail($taskId);
        $taskDetailResponseJson = json_decode($taskDetailResponse, true);

        if ($taskDetailResponseJson['status'] == 'failed') {

            $errorMessage = json_encode(mysqli_real_escape_string($conn, $taskDetailResponseJson['error']));

            if ($availableTask && mysqli_num_rows($availableTask) > 0) {

                $query = "UPDATE tb_clickup_webhook SET cw_error = '$errorMessage', cw_due_date = null WHERE cw_task_id = '$taskId' AND cw_event = '$event'";

                if (mysqli_query($conn, $query)) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Data updated successfully with err.",
                            "error" => json_decode($taskDetailResponseJson['error'], true),
                        )
                    );
                    return;
                }
    
            } else {

                $query = "INSERT INTO tb_clickup_webhook (cw_task_id, cw_webhook_id, cw_event, cw_data, cw_error, cw_due_date) VALUES ('$taskId', '$webhookId', '$event', '$data', '$errorMessage', null)";

                if (mysqli_query($conn, $query)) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Data stored successfully with err.",
                            "error" => json_decode($taskDetailResponseJson['error'], true),
                        )
                    );
                    return;
                }

            }

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
        $cwDueDate = convertDateTime($taskDetail['due_date'], "Y-m-d h:i:s");
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
        // $waToken = '123';
        $waToken = $rowQontak['token'];
        $integrationId = $rowQontak['integration_id'];

        $emailArray = array_map(function($item) {

            // global $availableTask, $conn, $errorMessage;

            $user = getUserByEmail($item['email']);

            if ($user != null) {

                $targetPhone = $user['phone'];
                $targetName = $item['username'];

                // $notifWaResponse = tryNotifToWhatsapp($targetPhone, $targetName);
                // $notifWaResponseJson = json_decode($notifWaResponse, true);

                // if ($notifWaResponseJson['status'] == 'failed' && $availableTask && mysqli_num_rows($availableTask) > 0) {

                //     $errorMessage = json_encode($notifWaResponseJson['error']);
                //     $query = "UPDATE tb_clickup_webhook SET cw_error = null WHERE cw_task_id = '$taskId' AND cw_event = '$event'";

                //     if (mysqli_query($conn, $query)) {
                //         echo $errorMessage;
                //         echo json_encode(
                //             array(
                //                 "status" => "failed",
                //                 "message" => "Data updated successfully with err.",
                //                 "error" => $notifWaResponseJson['error'],
                //             )
                //         );
                //         return $item['email'];

                //     }

                // }

                tryNotifToWhatsapp($targetPhone, $targetName);
                return $item['email'];
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

        if ($availableTask) {

            if (mysqli_num_rows($availableTask) > 0) {

                if ($cwDateDone != null) {
                    $dateNow = date('Y-m-d H:i:s');
                    $query = "UPDATE tb_clickup_webhook SET cw_message_to = '$emailArrayEncoded', cw_message_text = '$messageText', cw_error = null, cw_date_done = '$cwDateDone', cw_data = '$data', cw_task_detail = '$taskDetailEncoded', cw_due_date = '$cwDueDate' WHERE cw_task_id = '$taskId'";
                } else {
                    $query = "UPDATE tb_clickup_webhook SET cw_message_to = '$emailArrayEncoded', cw_message_text = '$messageText', cw_error = null, cw_date_done = null, cw_data = '$data', cw_task_detail = '$taskDetailEncoded', cw_due_date = '$cwDueDate' WHERE cw_task_id = '$taskId' AND cw_event = '$event'";
                }

                if (mysqli_query($conn, $query)) {
                    echo json_encode(array("status" => "ok", "message" => "Data updated successfully"));
                } else {
                    echo json_encode(array("status" => "failed", "error" => mysqli_error($conn)));
                }

            } else {

                $query = "INSERT INTO tb_clickup_webhook (cw_task_id, cw_webhook_id, cw_event, cw_message_to, cw_message_text, cw_error, cw_data, cw_task_detail, cw_due_date) VALUES ('$taskId', '$webhookId', '$event', '$emailArrayEncoded', '$messageText', null, '$data', '$taskDetailEncoded', '$cwDueDate')";

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

function notifToWhatsapp($targetPhone, $targetName, $reminder) {

    global $waToken, $integrationId, $event, $templateReminderToday, $templateReminderTomorrow, $templateReminderOverdue, $templateCreated, $templateStatusUpdated, $taskName, $taskStatusBefore, $taskStatusAfter, $taskTriggerBy, $taskPriority, $taskDueDate, $taskUrl;

    if ($reminder != null) {

        if ($reminder == 'today') $templateReminder = $templateReminderToday;
        else if ($reminder == 'tomorrow') $templateReminder = $templateReminderTomorrow;
        else $templateReminder = $templateReminderOverdue;

        $postFields = '{
            "to_number": "' . $targetPhone . '",
            "to_name": "' . $targetName . ' - ClickUp Reminder",
            "message_template_id": "' . $templateReminder . '",
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
                        "value": "url",
                        "value_text": "' . $taskUrl . '"
                    }
                ]
            }
        }';

    } else {

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
    $responseObject = json_decode($response, true);

    if (curl_errno($curl)) {
        return json_encode(array("status" => "failed", "error" => curl_error($curl)));
    } else if (isset($responseObject['status']) && $responseObject['status'] == 'error') {
        return json_encode(array("status" => "failed", "error" => $responseObject['error']));
    }

    curl_close($curl);

    return json_encode(array("status" => "ok", "data" => $response));
}

function tryNotifToWhatsapp($targetPhone, $targetName, $reminder = null) {
    $response = notifToWhatsapp($targetPhone, $targetName, $reminder);
    $responseObject = json_decode($response, true);

    global $curlWaChance;

    if ($responseObject['status'] == 'error' && $curlWaChance > 0) {
        $curlWaChance --;
        return tryNotifToWhatsapp($targetPhone, $targetName, $reminder);
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
