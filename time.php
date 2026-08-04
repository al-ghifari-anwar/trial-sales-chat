<?php

include_once("config.php");

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    echo json_encode(array("status" => "ok", "date_time" => date('Y-m-d H:i:s')));
}
