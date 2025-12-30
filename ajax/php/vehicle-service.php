<?php

include '../../class/include.php';

// Function to send SMS via OzoneDesk API
function sendSMS($phone, $message) {
    $user_id = "105974";
    $api_key = "8evcpzsicpx7zvo4y";
    $sender_id = "ozoneDEMO"; // Use approved sender ID in production
    
    // Format phone number (ensure it starts with 94 for Sri Lanka)
    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
    if (substr($phone, 0, 1) === '0') {
        $phone = '94' . substr($phone, 1); // Convert 07X to 947X
    } elseif (substr($phone, 0, 2) !== '94') {
        $phone = '94' . $phone;
    }
    
    // Build query parameters
    $params = http_build_query([
        "user_id"   => $user_id,
        "api_key"   => $api_key,
        "sender_id" => $sender_id,
        "to"        => $phone,
        "message"   => $message,
    ]);
    
    // API URL
    $url = "http://send.ozonedesk.com/api/v2/send.php?" . $params;
    
    // Make HTTP request using cURL for better error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check for cURL errors
    if ($curl_error) {
        error_log("OzoneDesk SMS cURL error: {$curl_error}");
        return [
            'success' => false,
            'reason' => 'Connection error: ' . $curl_error
        ];
    }
    
    // Decode JSON response
    $result = json_decode($response, true);
    if ($result === null) {
        error_log("OzoneDesk SMS invalid JSON. HTTP {$http_code}. Raw response: {$response}");
    } else {
        error_log("OzoneDesk SMS response: HTTP {$http_code} " . json_encode($result));
    }
    
    if ($result && isset($result["status"]) && $result["status"] === "success") {
        return [
            'success' => true,
            'reason' => 'SMS sent successfully'
        ];
    } else {
        $error_msg = 'Unknown error';
        if ($result && isset($result['result'])) {
            $error_msg = $result['result'];
        } elseif ($result && isset($result['error'])) {
            $error_msg = $result['error'];
        } elseif (!$result) {
            $error_msg = 'Invalid API response';
        }
        return [
            'success' => false,
            'reason' => $error_msg
        ];
    }
}
header('Content-Type: application/json; charset=UTF8');

// Create a new Vehicle Service
if (isset($_POST['create'])) {
    $db = Database::getInstance();
    
    $SERVICE = new VehicleService(NULL);

    $SERVICE->code = $db->escapeString($_POST['code']);
    $SERVICE->customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $SERVICE->customer_name = $db->escapeString($_POST['customer_name']);
    $SERVICE->customer_address = $db->escapeString($_POST['customer_address']);
    $SERVICE->customer_phone = $db->escapeString($_POST['customer_phone']);
    $SERVICE->vehicle_no = $db->escapeString($_POST['vehicle_no']);
    $SERVICE->vehicle_brand_id = (int)$_POST['vehicle_brand_id'];
    $SERVICE->vehicle_model_id = (int)$_POST['vehicle_model_id'];
    $SERVICE->expected_completion = $db->escapeString($_POST['expected_completion']);
    $SERVICE->remarks = $db->escapeString($_POST['remarks'] ?? '');
    $SERVICE->created_by = $_SESSION['user_id'] ?? 0;

    $res = $SERVICE->create();

    if ($res) {
        // Add service jobs
        if (isset($_POST['service_jobs']) && is_array($_POST['service_jobs'])) {
            $SERVICE->id = $res;
            foreach ($_POST['service_jobs'] as $job) {
                $SERVICE->addServiceJob($job['service_type_id'], $job['price'], $job['notes'] ?? '');
            }
        }
        
        // Get the created service for tracking code
        $createdService = new VehicleService($res);
        
        echo json_encode([
            "status" => 'success',
            "id" => $res,
            "tracking_code" => $createdService->tracking_code
        ]);
        exit();
    } else {
        echo json_encode(["status" => 'error']);
        exit();
    }
}

// Update Vehicle Service details
if (isset($_POST['update'])) {
    $db = Database::getInstance();
    
    $SERVICE = new VehicleService($_POST['id']);

    $SERVICE->customer_name = $db->escapeString($_POST['customer_name']);
    $SERVICE->customer_address = $db->escapeString($_POST['customer_address']);
    $SERVICE->customer_phone = $db->escapeString($_POST['customer_phone']);
    $SERVICE->vehicle_no = $db->escapeString($_POST['vehicle_no']);
    $SERVICE->vehicle_brand_id = (int)$_POST['vehicle_brand_id'];
    $SERVICE->vehicle_model_id = (int)$_POST['vehicle_model_id'];
    $SERVICE->expected_completion = $db->escapeString($_POST['expected_completion']);
    $SERVICE->remarks = $db->escapeString($_POST['remarks'] ?? '');

    $result = $SERVICE->update();

    if ($result) {
        // Append new service jobs if provided
        if (isset($_POST['service_jobs']) && is_array($_POST['service_jobs'])) {
            foreach ($_POST['service_jobs'] as $job) {
                $SERVICE->addServiceJob($job['service_type_id'], $job['price'], $job['notes'] ?? '');
            }
        }

        echo json_encode(["status" => 'success']);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Update Service Stage
if (isset($_POST['update_stage'])) {
    $SERVICE = new VehicleService($_POST['id']);
    $stage = (int)$_POST['stage'];
    $notes = $_POST['notes'] ?? '';
    
    // Block manual switch to Delivered (stage 7) - only through payment
    if ($stage == 7) {
        echo json_encode(["status" => 'error', "message" => 'Delivered status can only be set through payment']);
        exit();
    }
    
    // Block if already delivered
    if ($SERVICE->current_stage == 7 || $SERVICE->status == 'delivered') {
        echo json_encode(["status" => 'error', "message" => 'This service has already been delivered']);
        exit();
    }
    
    $result = $SERVICE->updateStage($stage, $notes);

    if ($result) {
        $response = [
            "status" => 'success',
            "stage" => $stage,
            "stage_name" => VehicleService::STAGES[$stage]['name'] ?? 'Unknown'
        ];
        
        // Send SMS notification only when stage is "Ready for Pickup" (stage 6)
        if ($stage == 6 && !empty($SERVICE->customer_phone)) {
            $sms_message = "Dear " . $SERVICE->customer_name . ", your vehicle (" . $SERVICE->vehicle_no . ") is now Ready for Pickup. Tracking: " . $SERVICE->tracking_code . ". Thank you!";
            
            $sms_result = sendSMS($SERVICE->customer_phone, $sms_message);
            
            $response['sms_sent'] = $sms_result['success'];
            $response['sms_message'] = $sms_result['reason'];
        }
        
        echo json_encode($response);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Add Service Job
if (isset($_POST['add_job'])) {
    $SERVICE = new VehicleService($_POST['service_id']);
    
    $result = $SERVICE->addServiceJob(
        $_POST['service_type_id'],
        $_POST['price'],
        $_POST['notes'] ?? ''
    );

    if ($result) {
        echo json_encode([
            "status" => 'success',
            "total" => number_format($SERVICE->getTotalAmount(), 2)
        ]);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Remove Service Job
if (isset($_POST['remove_job'])) {
    $SERVICE = new VehicleService($_POST['service_id']);
    $result = $SERVICE->removeServiceJob($_POST['job_id']);

    if ($result) {
        echo json_encode([
            "status" => 'success',
            "total" => number_format($SERVICE->getTotalAmount(), 2)
        ]);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Get Service Details
if (isset($_GET['get_service'])) {
    $SERVICE = new VehicleService($_GET['id']);
    
    if ($SERVICE->id) {
        $jobs = $SERVICE->getServiceJobs();
        $logs = $SERVICE->getServiceLogs();
        
        $BRAND = new VehicleBrand($SERVICE->vehicle_brand_id);
        $MODEL = new VehicleModel($SERVICE->vehicle_model_id);
        
        echo json_encode([
            "status" => 'success',
            "data" => [
                "id" => $SERVICE->id,
                "code" => $SERVICE->code,
                "tracking_code" => $SERVICE->tracking_code,
                "customer_id" => $SERVICE->customer_id,
                "customer_name" => $SERVICE->customer_name,
                "customer_address" => $SERVICE->customer_address,
                "customer_phone" => $SERVICE->customer_phone,
                "vehicle_no" => $SERVICE->vehicle_no,
                "vehicle_brand_id" => $SERVICE->vehicle_brand_id,
                "vehicle_model_id" => $SERVICE->vehicle_model_id,
                "brand_name" => $BRAND->name,
                "model_name" => $MODEL->name,
                "status" => $SERVICE->status,
                "current_stage" => $SERVICE->current_stage,
                "expected_completion" => $SERVICE->expected_completion,
                "remarks" => $SERVICE->remarks,
                "created_at" => $SERVICE->created_at,
                "jobs" => $jobs,
                "logs" => $logs,
                "total_amount" => number_format($SERVICE->getTotalAmount(), 2)
            ]
        ]);
    } else {
        echo json_encode(["status" => 'error', "message" => 'Service not found']);
    }
    exit();
}

// Lookup Service by code or tracking code (for invoice)
if (isset($_GET['service_lookup'])) {
    $db = Database::getInstance();
    $lookup = $db->escapeString($_GET['service_lookup']);

    $query = "SELECT vs.*, vb.name as brand_name, vm.name as model_name 
              FROM `vehicle_service` vs
              LEFT JOIN `vehicle_brand` vb ON vs.vehicle_brand_id = vb.id 
              LEFT JOIN `vehicle_model` vm ON vs.vehicle_model_id = vm.id 
              WHERE vs.code = '$lookup' OR vs.tracking_code = '$lookup' LIMIT 1";

    $result = mysqli_fetch_assoc($db->readQuery($query));

    if ($result) {
        // Check if already delivered
        if ($result['current_stage'] == 7 || $result['status'] == 'delivered') {
            echo json_encode(["status" => 'error', "message" => 'This service has already been delivered and paid']);
            exit();
        }
        
        $SERVICE = new VehicleService($result['id']);
        $jobs = $SERVICE->getServiceJobs();

        echo json_encode([
            "status" => 'success',
            "data" => [
                "id" => $SERVICE->id,
                "code" => $SERVICE->code,
                "tracking_code" => $SERVICE->tracking_code,
                "customer_name" => $SERVICE->customer_name,
                "customer_address" => $SERVICE->customer_address,
                "customer_phone" => $SERVICE->customer_phone,
                "vehicle_no" => $SERVICE->vehicle_no,
                "brand_name" => $result['brand_name'] ?? '',
                "model_name" => $result['model_name'] ?? '',
                "created_at" => $SERVICE->created_at,
                "current_stage" => $result['current_stage'],
                "status" => $result['status'],
                "jobs" => $jobs,
                "total_amount" => (float)$SERVICE->getTotalAmount()
            ]
        ]);
    } else {
        echo json_encode(["status" => 'error', "message" => 'Service not found']);
    }
    exit();
}

// Get Service by Tracking Code (for public view)
if (isset($_GET['track'])) {
    $trackingCode = $_GET['track'];
    $service = VehicleService::getByTrackingCode($trackingCode);
    
    if ($service) {
        $SERVICE = new VehicleService($service['id']);
        $jobs = $SERVICE->getServiceJobs();
        $logs = $SERVICE->getServiceLogs();
        
        echo json_encode([
            "status" => 'success',
            "data" => [
                "tracking_code" => $service['tracking_code'],
                "customer_name" => $service['customer_name'],
                "vehicle_no" => $service['vehicle_no'],
                "brand_name" => $service['brand_name'],
                "model_name" => $service['model_name'],
                "status" => $service['status'],
                "current_stage" => $service['current_stage'],
                "stage_info" => VehicleService::STAGES[$service['current_stage']] ?? null,
                "expected_completion" => $service['expected_completion'],
                "created_at" => $service['created_at'],
                "jobs" => $jobs,
                "logs" => $logs,
                "stages" => VehicleService::STAGES
            ]
        ]);
    } else {
        echo json_encode(["status" => 'error', "message" => 'Invalid tracking code']);
    }
    exit();
}

// DataTable fetch
if (isset($_POST['fetch_datatable'])) {
    $SERVICE = new VehicleService();
    $result = $SERVICE->fetchForDataTable($_POST);
    echo json_encode($result);
    exit();
}

// Delete Vehicle Service
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $SERVICE = new VehicleService($_POST['id']);
    $result = $SERVICE->delete();

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// Get models by brand
if (isset($_GET['get_models_by_brand'])) {
    $db = Database::getInstance();
    $brandId = (int)$_GET['brand_id'];
    
    $query = "SELECT * FROM `vehicle_model` WHERE `brand_id` = '$brandId' ORDER BY name ASC";
    $result = $db->readQuery($query);
    
    $models = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $models[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $models]);
    exit();
}

?>
