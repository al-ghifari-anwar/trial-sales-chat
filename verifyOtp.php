<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];

    $checkOtp = mysqli_query($conn, "SELECT * FROM tb_otp WHERE otp = '$otp'");
    $row = $checkOtp->fetch_array(MYSQLI_ASSOC);

    if ($row != null) {
        $expDate = date("Y-m-d H:i:s", strtotime($row['exp_date']));
        $currentDate = date("Y-m-d H:i:s");

        if ($expDate <= $currentDate) {
            $response = ["response" => 200, "status" => "failed", "message" => "Your OTP code is expired, please request another!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "ok", "message" => "OTP code verified, please insert your new password!", "user_id" => $row['id_user']];
            echo json_encode($response);
        }
    } else {
        $response = ["response" => 200, "status" => "failed", "message" => "OTP code invalid, please try again!"];
        echo json_encode($response);
    }
} else {
    $response = ["response" => 404, "status" => "not-found", "message" => "Request not found!"];
    echo json_encode($response);
}
