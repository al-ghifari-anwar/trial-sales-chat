<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include_once("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $result = mysqli_query($conn, "SELECT * FROM tb_user JOIN tb_city ON tb_user.id_city = tb_city.id_city WHERE id_user = '$id' ");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    } else {
        $id_distributor = $_GET['dst'];

        $result = mysqli_query($conn, "SELECT * FROM tb_user JOIN tb_city ON tb_user.id_city = tb_city.id_city WHERE tb_city.id_distributor = '$id_distributor'");

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $transArray[] = $row;
        }

        mysqli_close($conn);

        if ($transArray == null) {
            echo json_encode(array("status" => "empty", "results" => []));
        } else {
            echo json_encode(array("status" => "ok", "results" => $transArray));
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $level_user = $_POST['level_user'];
        $id_city = $_POST['id_city'];
        $phone_user = $_POST['phone_user'];
        $full_name = $_POST['full_name'];

        $result = mysqli_query($conn, "UPDATE tb_user SET username = '$username', level_user = '$level_user', id_city = '$id_city', phone_user = '$phone_user', full_name = '$full_name' WHERE id_user = '$id'");

        if ($result) {
            $response = ["response" => 200, "status" => "ok", "message" => "Berhasil mengubah data user!"];
            echo json_encode($response);
        } else {
            $response = ["response" => 200, "status" => "failed", "message" => "Gagal mengubah data user!. "];
            echo json_encode($response);
        }

        mysqli_close($conn);
    } else {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $level_user = $_POST['level_user'];
        $id_city = $_POST['id_city'];
        $phone_user = $_POST['phone_user'];
        $full_name = $_POST['full_name'];

        $checkUser = mysqli_query($conn, "SELECT * FROM tb_user WHERE username = '$username'");

        $row = $checkUser->fetch_array(MYSQLI_ASSOC);

        if ($row != null) {
            $response = ["response" => 200, "status" => "failed", "message" => "Username already taken, please use another username!"];
            echo json_encode($response);
        } else {
            $result = mysqli_query($conn, "INSERT INTO tb_user(full_name,username, password, level_user, id_city, phone_user) VALUES('$full_name','$username', '$password', '$level_user', '$id_city','$phone_user')");

            if ($result) {
                $response = ["response" => 200, "status" => "ok", "message" => "Berhasil menambah data user!"];
                echo json_encode($response);
            } else {
                $response = ["response" => 200, "status" => "failed", "message" => "Gagal menambah data user!"];
                echo json_encode($response);
            }
        }

        mysqli_close($conn);
    }
}
