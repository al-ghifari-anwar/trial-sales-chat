<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'jatem') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_rencana_visit.id_invoice, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'jatem' AND tb_contact.id_city = '$id_city' AND is_visited = 0");


            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                // $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_rencana_visit.id_invoice, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'jatem' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'voucher') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'voucher' AND tb_contact.id_city = '$id_city' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE type_rencana = 'voucher' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'passive') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'passive' AND tb_contact.id_city = '$id_city' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE type_rencana = 'passive' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    }
}
