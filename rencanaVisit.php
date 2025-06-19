<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'jatem') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_rencana_visit.id_invoice, tb_contact.reputation, status_invoice, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'jatem' AND tb_contact.id_city = '$id_city' AND is_visited = 0  AND status_invoice = 'waiting'");


            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_invoice = '$id_inv'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $rowRenvis['id_contact'];
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive') ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];

                $getBadScore = mysqli_query($conn, "SELECT * FROM tb_bad_score WHERE id_contact = '$id_con'");
                $rowBadscore = $getBadScore->fetch_array(MYSQLI_ASSOC);

                if ($rowBadscore['is_approved'] != 1) {
                    $renvisArray[] = $rowRenvis;
                }
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, date_invoice, termin_payment, tb_rencana_visit.id_invoice, tb_contact.reputation, status_invoice, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'jatem' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0 AND status_invoice = 'waiting'");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_invoice = '$id_inv'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $rowRenvis['id_contact'];
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive') ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];

                $getBadScore = mysqli_query($conn, "SELECT * FROM tb_bad_score WHERE id_contact = '$id_con'");
                $rowBadscore = $getBadScore->fetch_array(MYSQLI_ASSOC);

                if ($rowBadscore['is_approved'] != 1) {
                    $renvisArray[] = $rowRenvis;
                }
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
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'voucher' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND store_status != 'blacklist'");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                // $id_inv = $rowRenvis['id_invoice'];
                // $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_invoice = '$id_inv'");
                // $resCount = $count->fetch_array(MYSQLI_ASSOC);
                // $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? 1 : 0;
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE type_rencana = 'voucher' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0 AND store_status != 'blacklist'");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "count" => count($renvisArray), "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'passive') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.id_contact, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'passive' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND store_status != 'blacklist' GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_con = $rowRenvis['id_contact'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'passive'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $date_margin = date("Y-m-d", strtotime("-1 month"));
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.id_contact, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE type_rencana = 'passive' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0 AND store_status != 'blacklist' GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_con = $rowRenvis['id_contact'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'passive'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $date_margin = date("Y-m-d", strtotime("-1 month"));
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'tagih_mingguan') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, date_invoice, termin_payment, tb_invoice.id_invoice, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'tagih_mingguan' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND tb_invoice.status_invoice = 'waiting' GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_invoice = '$id_inv'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $rowRenvis['id_contact'];
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, date_invoice, termin_payment, tb_invoice.id_invoice, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city JOIN tb_invoice ON tb_invoice.id_invoice = tb_rencana_visit.id_invoice WHERE type_rencana = 'tagih_mingguan' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0  AND tb_invoice.status_invoice = 'waiting'  GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_inv = $rowRenvis['id_invoice'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_invoice = '$id_inv'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $jatuhTempo = date('d M Y', strtotime("+" . $rowRenvis['termin_payment'] . " days", strtotime($rowRenvis['date_invoice'])));
                $rowRenvis['jatuh_tempo'] = $jatuhTempo;
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $rowRenvis['id_contact'];
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive') ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        }
    } else if ($_GET['type'] == 'mg') {
        if (isset($_GET['c'])) {
            $id_city = $_GET['c'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.id_contact, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact WHERE type_rencana = 'mg' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND store_status != 'blacklist' AND reputation = 'good' GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_con = $rowRenvis['id_contact'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'mg'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $date_margin = date("Y-m-d", strtotime("-1 month"));
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
                $renvisArray[] = $rowRenvis;
            }

            if ($renvisArray == null) {
                echo json_encode(array("status" => "empty", "results" => []));
            } else {
                echo json_encode(array("status" => "ok", "results" => $renvisArray));
            }
        } else {
            $id_distributor = $_GET['dst'];
            $getRenvis = mysqli_query($conn, "SELECT tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_city.*, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_contact.id_contact, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_rencana_visit JOIN tb_contact ON tb_contact.id_contact = tb_rencana_visit.id_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE type_rencana = 'mg' AND tb_city.id_distributor = '$id_distributor' AND is_visited = 0 AND store_status != 'blacklist' AND reputation = 'good' GROUP BY tb_rencana_visit.id_contact");

            while ($rowRenvis = $getRenvis->fetch_array(MYSQLI_ASSOC)) {
                $id_con = $rowRenvis['id_contact'];
                $count = mysqli_query($conn, "SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'mg'");
                $resCount = $count->fetch_array(MYSQLI_ASSOC);
                $date_margin = date("Y-m-d", strtotime("-1 month"));
                $lastVisit = mysqli_query($conn, "SELECT * FROM tb_visit WHERE id_contact = '$id_con'  AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1");
                $resLastVisit = $lastVisit->fetch_array(MYSQLI_ASSOC);
                $rowRenvis['last_visit'] = $resLastVisit == null ? '0000-00-00' : $resLastVisit['date_visit'];
                $created_at = $rowRenvis['created_at'];
                $rowRenvis['created_at'] = $resLastVisit == null ? $created_at : $resLastVisit['date_visit'];
                $rowRenvis['is_new'] = $resCount['jmlRenvis'] == 1 ? "1" : "0";
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
