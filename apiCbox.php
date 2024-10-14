<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $arrayData = [
        ''
    ];

    if ($arrayData == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayData));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_dbTime = $_POST['_dbTime'];
    $_terminalTime = $_POST['_terminalTime'];
    $_groupName = $_POST['_groupName'];
    $Nobatch = $_POST['Nobatch'];
    $Keterangan = $_POST['Keterangan'];
    $Scale = $_POST['Scale'];

    $arrayData = [
        '_dbTime' => $_dbTime,
        '_terminalTime' => $_terminalTime,
        '_groupName' => $_groupName,
        'Nobatch' => $Nobatch,
        'Keterangan' => $Keterangan,
        'Scale' => $Scale
    ];

    if ($arrayData == null) {
        echo json_encode(array("status" => "empty", "results" => []));
    } else {
        echo json_encode(array("status" => "ok", "results" => $arrayData));
    }
}
