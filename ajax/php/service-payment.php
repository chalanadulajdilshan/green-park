<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Add multiple payments for a service
if (isset($_POST['add_payments'])) {
    $db = Database::getInstance();
    
    $serviceId = (int)$_POST['service_id'];
    $payments = json_decode($_POST['payments'], true);
    $notes = $db->escapeString($_POST['notes'] ?? '');
    $createdBy = $_SESSION['user_id'] ?? 0;
    
    if ($serviceId <= 0 || empty($payments)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid service or payments']);
        exit();
    }
    
    // Check if service is already delivered - block payment
    $checkQuery = "SELECT current_stage, status FROM `vehicle_service` WHERE id = '$serviceId'";
    $checkResult = mysqli_fetch_assoc($db->readQuery($checkQuery));
    if ($checkResult && ($checkResult['current_stage'] == 7 || $checkResult['status'] == 'delivered')) {
        echo json_encode(['status' => 'error', 'message' => 'This service has already been delivered and paid']);
        exit();
    }
    
    $success = true;
    foreach ($payments as $payment) {
        $paymentTypeId = (int)$payment['payment_type_id'];
        $amount = (float)$payment['amount'];
        $chequeNo = $db->escapeString($payment['cheque_no'] ?? '');
        $chequeBank = $db->escapeString($payment['cheque_bank'] ?? '');
        $chequeDate = $db->escapeString($payment['cheque_date'] ?? '');
        
        if ($paymentTypeId <= 0 || $amount <= 0) {
            continue;
        }
        
        $query = "INSERT INTO `vehicle_service_payments` 
            (`service_id`, `payment_type_id`, `amount`, `cheque_no`, `cheque_bank`, `cheque_date`, `notes`, `created_by`, `created_at`)
            VALUES ('$serviceId', '$paymentTypeId', '$amount', '$chequeNo', '$chequeBank', '$chequeDate', '$notes', '$createdBy', NOW())";
        
        $result = $db->readQuery($query);
        if (!$result) {
            $success = false;
        }
    }
    
    if ($success) {
        // Auto-switch service to Delivered (stage 7) after payment
        $updateQuery = "UPDATE `vehicle_service` SET 
            `current_stage` = 7, 
            `status` = 'delivered',
            `completed_at` = NOW(),
            `updated_at` = NOW()
            WHERE `id` = '$serviceId'";
        $db->readQuery($updateQuery);
        
        // Log the stage change
        $userId = $_SESSION['user_id'] ?? 0;
        $logQuery = "INSERT INTO `vehicle_service_logs` 
            (`service_id`, `stage`, `notes`, `created_by`, `created_at`)
            VALUES ('$serviceId', 7, 'Payment received - Service delivered', '$userId', NOW())";
        $db->readQuery($logQuery);
        
        echo json_encode(['status' => 'success', 'message' => 'Payments recorded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to record some payments']);
    }
    exit();
}

// Get payments for a service
if (isset($_GET['get_payments'])) {
    $db = Database::getInstance();
    $serviceId = (int)$_GET['service_id'];
    
    $query = "SELECT vsp.*, pt.name as payment_type_name, u.name as user_name 
              FROM `vehicle_service_payments` vsp 
              LEFT JOIN `payment_type` pt ON vsp.payment_type_id = pt.id
              LEFT JOIN `user` u ON vsp.created_by = u.id 
              WHERE vsp.service_id = '$serviceId' 
              ORDER BY vsp.created_at DESC";
    
    $result = $db->readQuery($query);
    $payments = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $payments[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $payments]);
    exit();
}

// Get total paid amount for a service
if (isset($_GET['get_total_paid'])) {
    $db = Database::getInstance();
    $serviceId = (int)$_GET['service_id'];
    
    $query = "SELECT SUM(amount) as total_paid FROM `vehicle_service_payments` WHERE `service_id` = '$serviceId'";
    $result = mysqli_fetch_assoc($db->readQuery($query));
    
    echo json_encode([
        'status' => 'success', 
        'total_paid' => (float)($result['total_paid'] ?? 0)
    ]);
    exit();
}

// Get active payment types
if (isset($_GET['get_payment_types'])) {
    $db = Database::getInstance();
    
    $query = "SELECT id, name FROM `payment_type` WHERE `is_active` = 1 ORDER BY `queue` ASC";
    $result = $db->readQuery($query);
    $types = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $types[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $types]);
    exit();
}

// Get finished services for selection modal (Ready for Pickup = stage 6)
if (isset($_GET['get_finished_services'])) {
    $db = Database::getInstance();
    
    $query = "SELECT vs.id, vs.code, vs.tracking_code, vs.customer_name, vs.vehicle_no, 
                     vs.created_at, vb.name as brand_name, vm.name as model_name
              FROM `vehicle_service` vs
              LEFT JOIN `vehicle_brand` vb ON vs.vehicle_brand_id = vb.id
              LEFT JOIN `vehicle_model` vm ON vs.vehicle_model_id = vm.id
              WHERE vs.current_stage = 6
              ORDER BY vs.created_at DESC
              LIMIT 100";
    
    $result = $db->readQuery($query);
    $services = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $services]);
    exit();
}

?>
