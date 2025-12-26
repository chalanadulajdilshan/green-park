<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Create a new Appointment Booking
if (isset($_POST['create'])) {
    $db = Database::getInstance();
    
    $APPOINTMENT = new ServiceAppointment(NULL);

    $APPOINTMENT->customer_name = $db->escapeString($_POST['customer_name']);
    $APPOINTMENT->customer_phone = $db->escapeString($_POST['customer_phone']);
    $APPOINTMENT->customer_email = $db->escapeString($_POST['customer_email'] ?? '');
    $APPOINTMENT->vehicle_no = $db->escapeString($_POST['vehicle_no']);
    $APPOINTMENT->vehicle_brand_id = (int)$_POST['vehicle_brand_id'];
    $APPOINTMENT->vehicle_model_id = (int)$_POST['vehicle_model_id'];
    
    // Encode service type IDs as JSON
    $serviceTypeIds = isset($_POST['service_type_ids']) ? $_POST['service_type_ids'] : [];
    if (is_array($serviceTypeIds)) {
        $APPOINTMENT->service_type_ids = json_encode(array_map('intval', $serviceTypeIds));
    } else {
        $APPOINTMENT->service_type_ids = json_encode([]);
    }
    
    $APPOINTMENT->preferred_date = $db->escapeString($_POST['preferred_date']);
    $APPOINTMENT->preferred_time = $db->escapeString($_POST['preferred_time']);
    $APPOINTMENT->notes = $db->escapeString($_POST['notes'] ?? '');

    $res = $APPOINTMENT->create();

    if ($res) {
        // Get the created appointment for booking code
        $createdAppointment = new ServiceAppointment($res);
        
        echo json_encode([
            "status" => 'success',
            "id" => $res,
            "booking_code" => $createdAppointment->booking_code
        ]);
        exit();
    } else {
        echo json_encode(["status" => 'error', "message" => 'Failed to create appointment']);
        exit();
    }
}

// Update Appointment Status
if (isset($_POST['update_status'])) {
    $APPOINTMENT = new ServiceAppointment($_POST['id']);
    $status = $_POST['status'];
    
    $result = $APPOINTMENT->updateStatus($status);

    if ($result) {
        echo json_encode([
            "status" => 'success',
            "new_status" => $status
        ]);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

// Get Appointment Details
if (isset($_GET['get_appointment'])) {
    $APPOINTMENT = new ServiceAppointment($_GET['id']);
    
    if ($APPOINTMENT->id) {
        $services = $APPOINTMENT->getServiceTypes();
        
        $BRAND = new VehicleBrand($APPOINTMENT->vehicle_brand_id);
        $MODEL = new VehicleModel($APPOINTMENT->vehicle_model_id);
        
        echo json_encode([
            "status" => 'success',
            "data" => [
                "id" => $APPOINTMENT->id,
                "booking_code" => $APPOINTMENT->booking_code,
                "customer_name" => $APPOINTMENT->customer_name,
                "customer_phone" => $APPOINTMENT->customer_phone,
                "customer_email" => $APPOINTMENT->customer_email,
                "vehicle_no" => $APPOINTMENT->vehicle_no,
                "vehicle_brand_id" => $APPOINTMENT->vehicle_brand_id,
                "vehicle_model_id" => $APPOINTMENT->vehicle_model_id,
                "brand_name" => $BRAND->name,
                "model_name" => $MODEL->name,
                "preferred_date" => $APPOINTMENT->preferred_date,
                "preferred_time" => $APPOINTMENT->preferred_time,
                "notes" => $APPOINTMENT->notes,
                "status" => $APPOINTMENT->status,
                "services" => $services,
                "total_estimate" => number_format($APPOINTMENT->getTotalEstimate(), 2),
                "created_at" => $APPOINTMENT->created_at
            ]
        ]);
    } else {
        echo json_encode(["status" => 'error', "message" => 'Appointment not found']);
    }
    exit();
}

// Get Appointment by Booking Code (for public view)
if (isset($_GET['track'])) {
    $bookingCode = $_GET['track'];
    $appointment = ServiceAppointment::getByBookingCode($bookingCode);
    
    if ($appointment) {
        $APPOINTMENT = new ServiceAppointment($appointment['id']);
        $services = $APPOINTMENT->getServiceTypes();
        
        echo json_encode([
            "status" => 'success',
            "data" => [
                "booking_code" => $appointment['booking_code'],
                "customer_name" => $appointment['customer_name'],
                "vehicle_no" => $appointment['vehicle_no'],
                "brand_name" => $appointment['brand_name'],
                "model_name" => $appointment['model_name'],
                "preferred_date" => $appointment['preferred_date'],
                "preferred_time" => $appointment['preferred_time'],
                "status" => $appointment['status'],
                "services" => $services,
                "total_estimate" => number_format($APPOINTMENT->getTotalEstimate(), 2),
                "created_at" => $appointment['created_at']
            ]
        ]);
    } else {
        echo json_encode(["status" => 'error', "message" => 'Invalid booking code']);
    }
    exit();
}

// DataTable fetch
if (isset($_POST['fetch_datatable'])) {
    $APPOINTMENT = new ServiceAppointment();
    $result = $APPOINTMENT->fetchForDataTable($_POST);
    echo json_encode($result);
    exit();
}

// Delete Appointment
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $APPOINTMENT = new ServiceAppointment($_POST['id']);
    $result = $APPOINTMENT->delete();

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// Get models by brand (for brand dropdown change)
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

// Get all service types
if (isset($_GET['get_service_types'])) {
    $SERVICE_TYPE = new ServiceType();
    $services = $SERVICE_TYPE->all();
    
    echo json_encode(['status' => 'success', 'data' => $services]);
    exit();
}

// Get all vehicle brands
if (isset($_GET['get_brands'])) {
    $BRAND = new VehicleBrand();
    $brands = $BRAND->all();
    
    echo json_encode(['status' => 'success', 'data' => $brands]);
    exit();
}

?>
