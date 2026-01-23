<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

// Create a new wheel balancer
if (isset($_POST['create'])) {

    $WHEEL_BALANCER = new WheelBalancer(NULL);

    $WHEEL_BALANCER->code = $_POST['code'];
    $WHEEL_BALANCER->name = $_POST['name'];
    $WHEEL_BALANCER->remark = $_POST['remark'];
    $WHEEL_BALANCER->is_active = isset($_POST['is_active']) ? 1 : 0;

    $res = $WHEEL_BALANCER->create();

    if ($res) {
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error']);
        exit();
    }
}

// Update wheel balancer
if (isset($_POST['update'])) {

    $WHEEL_BALANCER = new WheelBalancer($_POST['id']);

    $WHEEL_BALANCER->code = $_POST['code'];
    $WHEEL_BALANCER->name = $_POST['name'];
    $WHEEL_BALANCER->remark = $_POST['remark'];
    $WHEEL_BALANCER->is_active = isset($_POST['is_active']) ? 1 : 0;

    $result = $WHEEL_BALANCER->update();

    if ($result) {
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error']);
        exit();
    }
}

// Delete wheel balancer
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $WHEEL_BALANCER = new WheelBalancer($_POST['id']);
    $result = $WHEEL_BALANCER->delete();

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}
?>
