<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Create a new Service Type
if (isset($_POST['create'])) {

    $SERVICE = new ServiceType(NULL);

    $SERVICE->code = $_POST['code'];
    $SERVICE->name = $_POST['name'];
    $SERVICE->price = $_POST['price'];

    $res = $SERVICE->create();

    if ($res) {
        $result = [
            "status" => 'success'
        ];
        echo json_encode($result);
        exit();
    } else {
        $result = [
            "status" => 'error'
        ];
        echo json_encode($result);
        exit();
    }
}

// Update Service Type details
if (isset($_POST['update'])) {

    $SERVICE = new ServiceType($_POST['id']);

    $SERVICE->code = $_POST['code'];
    $SERVICE->name = $_POST['name'];
    $SERVICE->price = $_POST['price'];

    $result = $SERVICE->update();

    if ($result) {
        $result = [
            "status" => 'success'
        ];
        echo json_encode($result);
        exit();
    } else {
        $result = [
            "status" => 'error'
        ];
        echo json_encode($result);
        exit();
    }
}

// Delete Service Type
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $SERVICE = new ServiceType($_POST['id']);
    $result = $SERVICE->delete();

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

// Get all service types
if (isset($_GET['get_all'])) {
    $SERVICE = new ServiceType();
    $services = $SERVICE->all();
    echo json_encode(['status' => 'success', 'data' => $services]);
}

?>
