<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

date_default_timezone_set('Asia/Jakarta');


$wa_token = 'EGzGoRR6sw6B5FhpJsG_Y2HB8g9f1U6amBOC9VJHITY';
// $template_id = 'c80d503f-bc62-450e-87e2-b7e794855145';
$template_id = '4a58a270-09a2-4c54-af90-385a61265e2c';
$integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    $checkUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$username'");

    $row = $checkUser->fetch_array(MYSQLI_ASSOC);

    if ($row == null) {
        $response = ["response" => 200, "status" => "failed", "message" => "Username not found, please use the registered username!"];
        echo json_encode($response);
    } else {
        $id_user =  $row['id_user'];
        $username = $row['username'];
        $phone_user = $row['phone_user'];
        $checkOtp = mysqli_query($conn, "SELECT * FROM tb_otp WHERE id_user = '$id_user'");
        $rowOtp = $checkOtp->fetch_array(MYSQLI_ASSOC);

        if ($rowOtp != null) {
            $expDate = date("Y-m-d H:i:s", strtotime($rowOtp['exp_date']));
            $currentDate = date("Y-m-d H:i:s");
            if ($expDate <= $currentDate) {
                $expDate = date("Y-m-d H:i:s", strtotime(" +5 minutes"));
                $otp = rand(100000, 999999);
                $createOtp = mysqli_query($conn, "INSERT INTO tb_otp(id_user,otp,exp_date) VALUES($id_user, $otp, '$expDate')");

                if ($createOtp) {
                    $message = "Request for password reset comfirmed, please insert this OTP: " . $otp . ", This OTP will valid until 5 minutes";

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
                        CURLOPT_POSTFIELDS => '{
                            "to_number": "' . $phone_user . '",
                            "to_name": "' . $username . '",
                            "message_template_id": "' . $template_id . '",
                            "channel_integration_id": "' . $integration_id . '",
                            "language": {
                                "code": "id"
                            },
                            "parameters": {
                                "body": [
                                    {
                                        "key": "1",
                                        "value": "nama",
                                        "value_text": "' . $username . '"
                                    },
                                    {
                                        "key": "2",
                                        "value": "message",
                                        "value_text": "' . $message . '"
                                    },
                                    {
                                        "key": "3",
                                        "value": "sender",
                                        "value_text": "Admin Top Mortar"
                                    }
                                ]
                            }
                        }',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $wa_token,
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $res = json_decode($response, true);

                    if ($res['status'] == 'success') {
                        $response = ["response" => 200, "status" => "ok", "message" => "Success creating new OTP code!"];
                        echo json_encode($response);
                    } else {
                        $response = ["response" => 200, "status" => "failed", "message" => "Failed creating new OTP code!", "detail" => $res];
                        echo json_encode($response);
                    }
                } else {
                    $response = ["response" => 200, "status" => "failed", "message" => "Failed to create OTP!", "error" => mysqli_error($conn)];
                    echo json_encode($response);
                }
            } else {
                $response = ["response" => 200, "status" => "active", "message" => "Your OTP is still active!"];
                echo json_encode($response);
            }
        } else {
            $expDate = date("Y-m-d H:i:s", strtotime(" +5 minutes"));
            $otp = rand(100000, 999999);
            $createOtp = mysqli_query($conn, "INSERT INTO tb_otp(id_user,otp,exp_date) VALUES($id_user, $otp, '$expDate')");

            if ($createOtp) {
                $message = "Request for password reset comfirmed, please insert this OTP: " . $otp . " , This OTP will valid until 5 minutes.";

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
                    CURLOPT_POSTFIELDS => '{
                "to_number": "' . $phone_user . '",
                "to_name": "' . $username . '",
                "message_template_id": "' . $template_id . '",
                "channel_integration_id": "' . $integration_id . '",
                "language": {
                    "code": "id"
                },
                "parameters": {
                    "body": [
                    {
                        "key": "1",
                        "value": "nama",
                        "value_text": "' . $username . '"
                    },
                    {
                        "key": "2",
                        "value": "message",
                        "value_text": "' . $message . '"
                    }
                    ]
                }
                }',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $wa_token,
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = ["response" => 200, "status" => "ok", "message" => "Success creating new OTP code!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Failed to create OTP!", "error" => mysqli_error($conn)];
                echo json_encode($response);
            }
        }
    }

    mysqli_close($conn);
} else {
    $response = ["response" => 404, "status" => "not-found", "message" => "Request not found!"];
    echo json_encode($response);
}
