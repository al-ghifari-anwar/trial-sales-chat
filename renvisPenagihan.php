<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'jatem1') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_renvis_jatem.id_invoice, tb_contact.reputation, tb_invoice.status_invoice FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem1' AND tb_contact.id_city = '$id_city' AND is_visited = 0");


            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_renvis_jatem.id_invoice, tb_contact.reputation, tb_invoice.status_invoice FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem1' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'jatem2') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem2' AND tb_contact.id_city = '$id_city' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem2' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'jatem3') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem3' AND tb_contact.id_city = '$id_city' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem3' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    }

    // Untuk Update Payment
    //     UPDATE tb_renvis_jatem SET is_visited = 1 WHERE id_renvis_jatem IN 
    // (SELECT id_renvis_jatem FROM tb_renvis_jatem JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE status_invoice = 'paid')
}
