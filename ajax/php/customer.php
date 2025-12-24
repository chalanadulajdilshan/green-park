<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF8');

// Search customers (for autocomplete)
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    
    $CUSTOMER_MASTER = new CustomerMaster();
    $customers = $CUSTOMER_MASTER->searchCustomers($search);
    
    if ($customers) {
        echo json_encode(['status' => 'success', 'data' => $customers]);
    } else {
        echo json_encode(['status' => 'success', 'data' => []]);
    }
    exit;
}

// Get customer by ID
if (isset($_GET['id'])) {
    $CUSTOMER = new CustomerMaster($_GET['id']);
    
    if ($CUSTOMER->id) {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $CUSTOMER->id,
                'code' => $CUSTOMER->code,
                'name' => $CUSTOMER->name,
                'address' => $CUSTOMER->address,
                'mobile_number' => $CUSTOMER->mobile_number,
                'mobile_number_2' => $CUSTOMER->mobile_number_2,
                'email' => $CUSTOMER->email
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
    }
    exit;
}

?>
