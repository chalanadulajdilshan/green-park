<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

$VEHICLE_SERVICE = new VehicleService();
$lastId = $VEHICLE_SERVICE->getLastID();
$service_code = 'VS/' . str_pad(($lastId + 1), 5, '0', STR_PAD_LEFT);

// Get existing service if editing
$editMode = false;
$serviceData = null;
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $serviceData = new VehicleService($_GET['id']);
    if ($serviceData->id) {
        $editMode = true;
        $service_code = $serviceData->code;
    }
}

// Get all brands and service types
$BRAND = new VehicleBrand();
$brands = $BRAND->all();

$SERVICE_TYPE = new ServiceType();
$serviceTypes = $SERVICE_TYPE->all();

$CUSTOMER = new CustomerMaster();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Vehicle Service Tracking | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
    <link rel="stylesheet" href="assets/libs/jquery-ui-dist/jquery-ui.min.css">
    
    <style>
        :root {
            --vs-primary: #5b8def;
            --vs-primary-soft: #e9f0ff;
            --vs-success: #4ccba3;
            --vs-success-soft: #e7f7f1;
            --vs-warning: #f5b55b;
            --vs-warning-soft: #fff4e3;
            --vs-danger: #f08a84;
            --vs-danger-soft: #ffeceb;
            --vs-purple: #8c7ee6;
            --vs-purple-soft: #f1edff;
            --vs-pink: #f18acb;
            --vs-cyan: #63c5dd;
            --vs-cyan-soft: #e7f7fb;
        }

        /* Datepicker styling */
        .ui-datepicker {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(17, 24, 39, 0.1);
            padding: 8px 10px 12px;
            z-index: 1050 !important;
        }

        .ui-datepicker-header {
            background: #f8fafc;
            border: none;
            border-radius: 10px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .ui-datepicker-title {
            font-weight: 600;
            color: #111827;
        }

        .ui-datepicker-prev, .ui-datepicker-next {
            top: 9px !important;
            background: #eef2ff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background: #e0e7ff;
        }

        .ui-datepicker table {
            width: 100%;
            margin: 0;
            font-size: 13px;
        }

        .ui-datepicker th {
            padding: 6px 0;
            color: #6b7280;
            font-weight: 600;
        }

        .ui-datepicker td {
            padding: 2px;
        }

        .ui-state-default {
            text-align: center;
            border-radius: 8px;
            padding: 6px 0;
            color: #111827;
            border: none;
            background: #f9fafb;
        }

        .ui-state-highlight {
            background: #eef2ff;
            color: #4338ca;
        }

        .ui-state-active {
            background: linear-gradient(135deg, var(--vs-primary) 0%, #1d4ed8 100%);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.35);
        }

        .ui-datepicker-buttonpane {
            display: none !important;
        }

        /* History list */
        #serviceHistory {
            max-height: 260px;
            overflow-y: auto;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .vs-history-item {
            display: flex;
            gap: 12px;
            padding: 10px 12px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 8px;
            box-shadow: 0 6px 16px rgba(17, 24, 39, 0.04);
        }

        .vs-history-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .vs-history-title {
            margin: 0;
            font-weight: 600;
            color: #111827;
            font-size: 15px;
        }

        .vs-history-notes {
            margin: 2px 0 4px;
            color: #4b5563;
            font-size: 13px;
        }

        .vs-history-meta {
            color: #6b7280;
            font-size: 12px;
        }

        .vs-section {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 30px rgba(24, 39, 75, 0.06);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .vs-section.locked {
            opacity: 0.6;
            pointer-events: none;
        }

        .vs-section.locked .vs-section-header {
            background: #f3f4f6;
        }

        .vs-section-header {
            padding: 18px 22px;
            background: linear-gradient(135deg, #f9fbff 0%, var(--vs-primary-soft) 100%);
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
        }

        .vs-section-header .vs-section-number {
            background: var(--vs-primary-soft);
            color: #1d4ed8;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .vs-section-header.completed {
            background: linear-gradient(135deg, #f6fffb 0%, var(--vs-success-soft) 100%);
            color: #0f5132;
        }

        .vs-section-header.completed .vs-section-number {
            background: var(--vs-success-soft);
            color: #0f5132;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .vs-section-header.active {
            background: linear-gradient(135deg, #fff9f0 0%, var(--vs-warning-soft) 100%);
            color: #92400e;
        }

        .vs-section-header.active .vs-section-number {
            background: var(--vs-warning-soft);
            color: #b45309;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .vs-section-header-qr {
            background: linear-gradient(135deg, #f8f6ff 0%, var(--vs-purple-soft) 100%);
            color: #3b2f73;
        }

        .vs-section-header-qr .vs-section-number {
            background: var(--vs-purple-soft);
            color: #5b47c4;
            border-color: rgba(92, 70, 196, 0.25);
        }

        .vs-section-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .vs-section-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .vs-section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .vs-section-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        .vs-section-body {
            padding: 24px;
            display: none;
        }

        .vs-section.expanded .vs-section-body {
            display: block;
        }

        .vs-section-toggle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .vs-section.expanded .vs-section-toggle {
            transform: rotate(180deg);
        }

        .vs-form-group {
            margin-bottom: 20px;
        }

        .vs-form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .vs-form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .vs-form-control:focus {
            border-color: var(--vs-primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .vs-btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .vs-btn-primary {
            background: linear-gradient(135deg, var(--vs-primary) 0%, #1d4ed8 100%);
            color: white;
        }

        .vs-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .vs-btn-success {
            background: linear-gradient(135deg, var(--vs-success) 0%, #059669 100%);
            color: white;
        }

        .vs-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .vs-btn-outline {
            background: white;
            border: 2px solid #e5e7eb;
            color: #374151;
        }

        .vs-btn-outline:hover {
            border-color: var(--vs-primary);
            color: var(--vs-primary);
        }

        /* Service Jobs Table */
        .vs-jobs-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .vs-jobs-table th {
            background: #f9fafb;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .vs-jobs-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .vs-jobs-table tr:hover td {
            background: #f9fafb;
        }

        /* Stage Timeline */
        .vs-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 20px 0;
        }

        .vs-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e5e7eb;
            transform: translateY(-50%);
            z-index: 1;
        }

        .vs-timeline-progress {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--vs-success), var(--vs-primary));
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.5s ease;
        }

        .vs-stage {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 3;
            cursor: pointer;
        }

        .vs-stage-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #9ca3af;
            transition: all 0.3s ease;
            border: 4px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .vs-stage.completed .vs-stage-icon {
            background: var(--vs-success);
            color: white;
        }

        .vs-stage.current .vs-stage-icon {
            background: var(--vs-primary);
            color: white;
            animation: pulse 2s infinite;
        }

        .vs-stage-label {
            margin-top: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-align: center;
            max-width: 80px;
        }

        .vs-stage.completed .vs-stage-label,
        .vs-stage.current .vs-stage-label {
            color: #374151;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
        }

        /* QR Code Section */
        .vs-qr-container {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            margin-top: 20px;
        }

        .vs-qr-code {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .vs-tracking-code {
            font-size: 24px;
            font-weight: 700;
            color: var(--vs-primary);
            letter-spacing: 2px;
            margin: 16px 0;
        }

        .vs-share-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 8px;
        }

        .vs-share-btn:hover {
            border-color: var(--vs-primary);
            color: var(--vs-primary);
        }

        .vs-share-btn.whatsapp:hover {
            border-color: #25d366;
            color: #25d366;
        }

        /* Customer Search */
        .vs-customer-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .vs-customer-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }

        .vs-customer-item:hover {
            background: #f9fafb;
        }

        .vs-customer-item strong {
            color: #374151;
        }

        .vs-customer-item small {
            color: #6b7280;
        }

        /* Service Item Card */
        .vs-service-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .vs-service-card:hover {
            border-color: var(--vs-primary);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .vs-service-card-info h5 {
            margin: 0 0 4px 0;
            color: #374151;
        }

        .vs-service-card-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--vs-success);
        }

        .vs-remove-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #fee2e2;
            color: var(--vs-danger);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .vs-remove-btn:hover {
            background: var(--vs-danger);
            color: white;
        }

        /* Total Section */
        .vs-total-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .vs-total-label {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
        }

        .vs-total-amount {
            font-size: 28px;
            font-weight: 700;
            color: var(--vs-success);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .vs-timeline {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .vs-timeline::before {
                width: 4px;
                height: 100%;
                left: 25px;
                top: 0;
                transform: none;
            }

            .vs-timeline-progress {
                width: 4px !important;
                left: 25px;
                top: 0;
                transform: none;
            }

            .vs-stage {
                flex-direction: row;
                gap: 16px;
            }

            .vs-stage-label {
                margin-top: 0;
                text-align: left;
                max-width: none;
            }
        }

        /* Service List Modal */
        .vs-service-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .vs-service-select-item {
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .vs-service-select-item:hover {
            border-color: var(--vs-primary);
            background: #f0f9ff;
        }

        .vs-service-select-item.selected {
            border-color: var(--vs-success);
            background: #f0fdf4;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">
        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <a href="vehicle-service-tracking.php<?php echo isset($page_id) ? ('?page_id=' . (int)$page_id) : ''; ?>" class="btn btn-success" id="newService">
                                <i class="uil uil-plus me-1"></i> New Service
                            </a>
                            <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#appointmentsModal">
                                <i class="uil uil-calendar-alt me-1"></i> Load from Appointment
                            </a>
                            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#serviceListModal">
                                <i class="uil uil-search me-1"></i> View All Services
                            </a>
                        </div>
                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Vehicle Service Tracking</li>
                            </ol>
                        </div>
                    </div>

                    <form id="service-form" autocomplete="off">
                        <input type="hidden" id="service_id" name="id" value="<?php echo $editMode ? $serviceData->id : 0; ?>">
                        <input type="hidden" id="tracking_code" value="<?php echo $editMode ? $serviceData->tracking_code : ''; ?>">

                        <!-- Section 1: Customer Information -->
                        <div class="vs-section expanded" id="section-customer">
                            <div class="vs-section-header active" onclick="toggleSection('customer')">
                                <div class="vs-section-header-left">
                                    <div class="vs-section-number">1</div>
                                    <div>
                                        <h4 class="vs-section-title">Customer Information</h4>
                                        <p class="vs-section-subtitle">Enter customer details</p>
                                    </div>
                                </div>
                                <div class="vs-section-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            </div>
                            <div class="vs-section-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Service Code</label>
                                            <input type="text" id="code" name="code" class="vs-form-control" 
                                                value="<?php echo $service_code; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="vs-form-group position-relative">
                                            <label class="vs-form-label">Customer Name *</label>
                                            <input type="text" id="customer_name" name="customer_name" class="vs-form-control" 
                                                placeholder="Search or enter customer name"
                                                value="<?php echo $editMode ? htmlspecialchars($serviceData->customer_name) : ''; ?>">
                                            <input type="hidden" id="customer_id" name="customer_id" 
                                                value="<?php echo $editMode ? $serviceData->customer_id : 0; ?>">
                                            <div class="vs-customer-search-results" id="customerSearchResults"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Phone Number *</label>
                                            <input type="text" id="customer_phone" name="customer_phone" class="vs-form-control" 
                                                placeholder="Enter phone number"
                                                value="<?php echo $editMode ? htmlspecialchars($serviceData->customer_phone) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Address</label>
                                            <textarea id="customer_address" name="customer_address" class="vs-form-control" 
                                                rows="2" placeholder="Enter customer address"><?php echo $editMode ? htmlspecialchars($serviceData->customer_address) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="vs-btn vs-btn-primary" onclick="completeSection('customer', 'vehicle')">
                                        Continue <i class="uil uil-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Vehicle Information -->
                        <div class="vs-section <?php echo !$editMode ? 'locked' : 'expanded'; ?>" id="section-vehicle">
                            <div class="vs-section-header" onclick="toggleSection('vehicle')">
                                <div class="vs-section-header-left">
                                    <div class="vs-section-number">2</div>
                                    <div>
                                        <h4 class="vs-section-title">Vehicle Information</h4>
                                        <p class="vs-section-subtitle">Enter vehicle details</p>
                                    </div>
                                </div>
                                <div class="vs-section-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            </div>
                            <div class="vs-section-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Vehicle Number *</label>
                                            <input type="text" id="vehicle_no" name="vehicle_no" class="vs-form-control" 
                                                placeholder="e.g., ABC-1234"
                                                value="<?php echo $editMode ? htmlspecialchars($serviceData->vehicle_no) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Vehicle Brand *</label>
                                            <select id="vehicle_brand_id" name="vehicle_brand_id" class="vs-form-control">
                                                <option value="">Select Brand</option>
                                                <?php foreach ($brands as $brand): ?>
                                                <option value="<?php echo $brand['id']; ?>" 
                                                    <?php echo ($editMode && $serviceData->vehicle_brand_id == $brand['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($brand['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Vehicle Model *</label>
                                            <select id="vehicle_model_id" name="vehicle_model_id" class="vs-form-control">
                                                <option value="">Select Model</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="vs-btn vs-btn-outline me-2" onclick="goToSection('customer')">
                                        <i class="uil uil-arrow-left"></i> Back
                                    </button>
                                    <button type="button" class="vs-btn vs-btn-primary" onclick="completeSection('vehicle', 'services')">
                                        Continue <i class="uil uil-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Service Selection -->
                        <div class="vs-section <?php echo !$editMode ? 'locked' : 'expanded'; ?>" id="section-services">
                            <div class="vs-section-header" onclick="toggleSection('services')">
                                <div class="vs-section-header-left">
                                    <div class="vs-section-number">3</div>
                                    <div>
                                        <h4 class="vs-section-title">Service Selection</h4>
                                        <p class="vs-section-subtitle">Select services to perform</p>
                                    </div>
                                </div>
                                <div class="vs-section-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            </div>
                            <div class="vs-section-body">
                                <div class="row mb-4">
                                    <div class="col-md-5">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Select Service</label>
                                            <select id="service_type_select" class="vs-form-control">
                                                <option value="">Choose a service...</option>
                                                <?php foreach ($serviceTypes as $st): ?>
                                                <option value="<?php echo $st['id']; ?>" 
                                                    data-price="<?php echo $st['price']; ?>"
                                                    data-name="<?php echo htmlspecialchars($st['name']); ?>">
                                                    <?php echo htmlspecialchars($st['name']); ?> - LKR <?php echo number_format($st['price'], 2); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Price (LKR)</label>
                                            <input type="number" id="service_price" class="vs-form-control" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Notes</label>
                                            <input type="text" id="service_notes" class="vs-form-control" placeholder="Optional notes">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="vs-btn vs-btn-success w-100 mb-3" onclick="addServiceJob()">
                                            <i class="uil uil-plus"></i>
                                            <span class="ms-1">Add</span>
                                        </button>
                                    </div>
                                </div>

                                <div id="selected-services">
                                    <!-- Selected services will be added here -->
                                </div>

                                <div class="vs-total-section">
                                    <span class="vs-total-label">Total Amount</span>
                                    <span class="vs-total-amount">LKR <span id="total-amount">0.00</span></span>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Expected Completion</label>
                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <input type="text" id="expected_completion_date" name="expected_completion_date" 
                                                        class="vs-form-control date-picker"
                                                        placeholder="YYYY-MM-DD"
                                                        value="<?php echo $editMode && $serviceData->expected_completion ? date('Y-m-d', strtotime($serviceData->expected_completion)) : ''; ?>">
                                                </div>
                                                <div class="col-5">
                                                    <input type="time" id="expected_completion_time" name="expected_completion_time" 
                                                        class="vs-form-control"
                                                        value="<?php echo $editMode && $serviceData->expected_completion ? date('H:i', strtotime($serviceData->expected_completion)) : ''; ?>">
                                                </div>
                                            </div>
                                            <input type="hidden" id="expected_completion" name="expected_completion" 
                                                value="<?php echo $editMode && $serviceData->expected_completion ? $serviceData->expected_completion : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" class="vs-form-control" 
                                                placeholder="Any additional notes"
                                                value="<?php echo $editMode ? htmlspecialchars($serviceData->remarks) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="vs-btn vs-btn-outline me-2" onclick="goToSection('vehicle')">
                                        <i class="uil uil-arrow-left"></i> Back
                                    </button>
                                    <?php if (!$editMode): ?>
                                    <button type="button" class="vs-btn vs-btn-success" id="saveServiceBtn" onclick="saveService()">
                                        <i class="uil uil-check"></i> Create Service
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="vs-btn vs-btn-primary" onclick="updateService()">
                                        <i class="uil uil-edit"></i> Update Service
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($editMode): ?>
                        <!-- Section 4: Service Status (Only shown in edit mode) -->
                        <div class="vs-section expanded" id="section-status">
                            <div class="vs-section-header completed" onclick="toggleSection('status')">
                                <div class="vs-section-header-left">
                                    <div class="vs-section-number">4</div>
                                    <div>
                                        <h4 class="vs-section-title">Service Status</h4>
                                        <p class="vs-section-subtitle">Track and update service progress</p>
                                    </div>
                                </div>
                                <div class="vs-section-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            </div>
                            <div class="vs-section-body">
                                <div class="vs-timeline" id="stageTimeline">
                                    <div class="vs-timeline-progress" id="timelineProgress" style="width: 0%;"></div>
                                    <?php 
                                    $currentStage = $serviceData->current_stage ?? 1;
                                    foreach (VehicleService::STAGES as $num => $stage): 
                                        $stageClass = '';
                                        if ($num < $currentStage) $stageClass = 'completed';
                                        elseif ($num == $currentStage) $stageClass = 'current';
                                    ?>
                                    <div class="vs-stage <?php echo $stageClass; ?>" data-stage="<?php echo $num; ?>" onclick="updateStage(<?php echo $num; ?>)">
                                        <div class="vs-stage-icon" style="<?php echo $num <= $currentStage ? 'background: ' . $stage['color'] . '; color: white;' : ''; ?>">
                                            <i class="uil <?php echo $stage['icon']; ?>"></i>
                                        </div>
                                        <span class="vs-stage-label"><?php echo $stage['name']; ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Update Notes</label>
                                            <input type="text" id="stage_notes" class="vs-form-control" placeholder="Add notes for this update">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="vs-form-group">
                                            <label class="vs-form-label">Current Stage</label>
                                            <select id="stage_select" class="vs-form-control">
                                                <?php foreach (VehicleService::STAGES as $num => $stage): ?>
                                                <option value="<?php echo $num; ?>" <?php echo $num == $currentStage ? 'selected' : ''; ?>>
                                                    <?php echo $stage['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="vs-btn vs-btn-primary w-100 mb-3" onclick="updateStageFromSelect()">
                                            <i class="uil uil-sync"></i> Update Stage
                                        </button>
                                    </div>
                                </div>

                                <!-- Service History -->
                                <div class="mt-4">
                                    <h5 class="mb-3"><i class="uil uil-history me-2"></i>Service History</h5>
                                    <div id="serviceHistory">
                                        <!-- History will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: QR Code & Sharing -->
                        <div class="vs-section expanded" id="section-qr">
                            <div class="vs-section-header vs-section-header-qr" onclick="toggleSection('qr')">
                                <div class="vs-section-header-left">
                                    <div class="vs-section-number">5</div>
                                    <div>
                                        <h4 class="vs-section-title">QR Code & Sharing</h4>
                                        <p class="vs-section-subtitle">Share tracking link with customer</p>
                                    </div>
                                </div>
                                <div class="vs-section-toggle">
                                    <i class="uil uil-angle-down"></i>
                                </div>
                            </div>
                            <div class="vs-section-body">
                                <div class="vs-qr-container">
                                    <div class="vs-qr-code">
                                        <div id="qrcode"></div>
                                    </div>
                                    <div class="vs-tracking-code"><?php echo $serviceData->tracking_code; ?></div>
                                    <p class="text-muted mb-4">Customer can scan this QR code or use the tracking code to check service status</p>
                                    
                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                        <button type="button" class="vs-share-btn" onclick="copyLink()">
                                            <i class="uil uil-copy"></i> Copy Link
                                        </button>
                                        <button type="button" class="vs-share-btn whatsapp" onclick="shareWhatsApp()">
                                            <i class="uil uil-whatsapp"></i> WhatsApp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </form>

                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Service List Modal -->
    <div class="modal fade" id="serviceListModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">All Vehicle Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table id="servicesTable" class="table table-bordered dt-responsive nowrap" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Tracking</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Modal -->
    <div class="modal fade" id="appointmentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="uil uil-calendar-alt me-2"></i>Load from Confirmed Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="appointmentsList">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/jquery-ui-dist/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        // Configuration
        const editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
        const serviceId = <?php echo $editMode ? $serviceData->id : 0; ?>;
        const trackingCode = '<?php echo $editMode ? $serviceData->tracking_code : ''; ?>';
        const currentStage = <?php echo $editMode ? $serviceData->current_stage : 1; ?>;
        const baseUrl = window.location.origin + '/green-park/';
        
        let selectedServices = []; // newly added jobs (client-side)
        let serviceJobs = []; // existing jobs from DB

        $(document).ready(function() {
            // Default phone prefix
            if (!editMode && !$('#customer_phone').val()) {
                $('#customer_phone').val('+94');
            }

            // Keep +94 prefix and allow only digits after it
            $('#customer_phone').on('input', function() {
                let v = $(this).val();
                if (!v.startsWith('+94')) {
                    v = '+94' + v.replace(/[^0-9]/g, '');
                } else {
                    v = '+94' + v.substring(3).replace(/[^0-9]/g, '');
                }
                // Limit to +94 plus 9 digits
                v = v.slice(0, 3 + 9);
                $(this).val(v);
            });

            // Datepicker for expected completion (jQuery UI)
            $('.date-picker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            });

            // Initialize
            if (editMode) {
                loadServiceJobs();
                loadServiceHistory();
                generateQRCode();
                updateTimelineProgress();
            }

            // Brand change - load models
            $('#vehicle_brand_id').change(function() {
                const brandId = $(this).val();
                loadModelsByBrand(brandId);
            });

            // Load models if brand is selected (edit mode)
            <?php if ($editMode && $serviceData->vehicle_brand_id): ?>
            loadModelsByBrand(<?php echo $serviceData->vehicle_brand_id; ?>, <?php echo $serviceData->vehicle_model_id; ?>);
            <?php endif; ?>

            // Service type change
            $('#service_type_select').change(function() {
                const option = $(this).find('option:selected');
                $('#service_price').val(option.data('price') || '');
            });

            // Customer search
            let searchTimeout;
            $('#customer_name').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => searchCustomers(query), 300);
                } else {
                    $('#customerSearchResults').hide();
                }
            });

            // Close search results on click outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.vs-form-group').length) {
                    $('#customerSearchResults').hide();
                }
            });

            // Initialize DataTable for services list
            initServicesTable();

            // Keep expected completion combined value updated
            $('#expected_completion_date, #expected_completion_time').on('change keyup', updateExpectedCompletionField);
            updateExpectedCompletionField();

            // Load confirmed appointments dropdown
            loadConfirmedAppointments();
            
            // Load appointments when modal opens
            $('#appointmentsModal').on('show.bs.modal', function() {
                loadConfirmedAppointments();
            });
        });

        function toggleSection(section) {
            const $section = $(`#section-${section}`);
            if (!$section.hasClass('locked')) {
                $section.toggleClass('expanded');
            }
        }

        function goToSection(section) {
            $('.vs-section').removeClass('expanded');
            $(`#section-${section}`).addClass('expanded').removeClass('locked');
            
            $('html, body').animate({
                scrollTop: $(`#section-${section}`).offset().top - 100
            }, 300);
        }

        function completeSection(current, next) {
            // Validate current section
            if (!validateSection(current)) return;

            // Mark current as completed
            $(`#section-${current} .vs-section-header`).addClass('completed').removeClass('active');
            
            // Unlock and expand next section
            $(`#section-${next}`).removeClass('locked').addClass('expanded');
            $(`#section-${next} .vs-section-header`).addClass('active');
            
            // Collapse current
            $(`#section-${current}`).removeClass('expanded');
            
            // Scroll to next section
            $('html, body').animate({
                scrollTop: $(`#section-${next}`).offset().top - 100
            }, 300);
        }

        function validateSection(section) {
            if (section === 'customer') {
                if (!$('#customer_name').val()) {
                    showError('Please enter customer name');
                    return false;
                }
                if (!$('#customer_phone').val()) {
                    showError('Please enter phone number');
                    return false;
                }
            } else if (section === 'vehicle') {
                if (!$('#vehicle_no').val()) {
                    showError('Please enter vehicle number');
                    return false;
                }
                if (!$('#vehicle_brand_id').val()) {
                    showError('Please select vehicle brand');
                    return false;
                }
                if (!$('#vehicle_model_id').val()) {
                    showError('Please select vehicle model');
                    return false;
                }
            }
            return true;
        }

        function updateExpectedCompletionField() {
            const date = $('#expected_completion_date').val();
            const time = $('#expected_completion_time').val();

            if (date && time) {
                $('#expected_completion').val(`${date} ${time}:00`);
            } else if (date) {
                $('#expected_completion').val(date);
            } else {
                $('#expected_completion').val('');
            }
        }

        function loadModelsByBrand(brandId, selectedModelId = null) {
            if (!brandId) {
                $('#vehicle_model_id').html('<option value="">Select Model</option>');
                return;
            }

            $.get('ajax/php/vehicle-service.php', { get_models_by_brand: true, brand_id: brandId }, function(response) {
                let options = '<option value="">Select Model</option>';
                if (response.data) {
                    response.data.forEach(model => {
                        const selected = selectedModelId == model.id ? 'selected' : '';
                        options += `<option value="${model.id}" ${selected}>${model.name}</option>`;
                    });
                }
                $('#vehicle_model_id').html(options);
            }, 'json');
        }

        function searchCustomers(query) {
            $.get('ajax/php/customer.php', { search: query }, function(response) {
                if (response.data && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(customer => {
                        html += `
                            <div class="vs-customer-item" onclick="selectCustomer(${customer.id}, '${customer.name}', '${customer.mobile_number}', '${customer.address || ''}')">
                                <strong>${customer.name}</strong><br>
                                <small>${customer.mobile_number} | ${customer.address || 'No address'}</small>
                            </div>
                        `;
                    });
                    $('#customerSearchResults').html(html).show();
                } else {
                    $('#customerSearchResults').hide();
                }
            }, 'json');
        }

        function selectCustomer(id, name, phone, address) {
            $('#customer_id').val(id);
            $('#customer_name').val(name);
            $('#customer_phone').val(phone);
            $('#customer_address').val(address);
            $('#customerSearchResults').hide();
        }

        function addServiceJob() {
            const select = $('#service_type_select');
            const serviceTypeId = select.val();
            const serviceName = select.find('option:selected').data('name');
            const price = parseFloat($('#service_price').val()) || 0;
            const notes = $('#service_notes').val();

            if (!serviceTypeId) {
                showError('Please select a service');
                return;
            }

            if (price <= 0) {
                showError('Please enter a valid price');
                return;
            }

            // Check if already added
            if (selectedServices.find(s => s.service_type_id == serviceTypeId)) {
                showError('This service is already added');
                return;
            }

            const job = {
                service_type_id: serviceTypeId,
                name: serviceName,
                price: price,
                notes: notes
            };

            selectedServices.push(job);
            renderServices();
            
            // Reset inputs
            select.val('');
            $('#service_price').val('');
            $('#service_notes').val('');
        }

        function removeServiceJob(index) {
            selectedServices.splice(index, 1);
            renderServices();
        }

        function renderServices() {
            let html = '';
            let total = 0;

            // existing jobs
            serviceJobs.forEach(job => {
                const price = parseFloat(job.price) || 0;
                total += price;
                html += `
                    <div class="vs-service-card">
                        <div class="vs-service-card-info">
                            <h5>${job.service_name}</h5>
                            <small class="text-muted">${job.notes || 'No notes'}</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="vs-service-card-price">LKR ${price.toFixed(2)}</span>
                            <span class="badge bg-light text-muted">Existing</span>
                        </div>
                    </div>
                `;
            });

            // newly added jobs (client-side)
            selectedServices.forEach((job, index) => {
                const price = parseFloat(job.price) || 0;
                total += price;
                html += `
                    <div class="vs-service-card">
                        <div class="vs-service-card-info">
                            <h5>${job.name}</h5>
                            <small class="text-muted">${job.notes || 'No notes'}</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="vs-service-card-price">LKR ${price.toFixed(2)}</span>
                            <button type="button" class="vs-remove-btn" onclick="removeServiceJob(${index})">
                                <i class="uil uil-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            $('#selected-services').html(html || '<p class="text-muted text-center py-4">No services selected yet</p>');
            $('#total-amount').text(total.toFixed(2));
        }

        function saveService() {
            if (!validateSection('customer') || !validateSection('vehicle')) {
                return;
            }

            updateExpectedCompletionField();

            if (selectedServices.length === 0) {
                showError('Please add at least one service');
                return;
            }

            $('.someBlock').preloader();

            const formData = new FormData($('#service-form')[0]);
            formData.append('create', true);
            
            selectedServices.forEach((job, index) => {
                formData.append(`service_jobs[${index}][service_type_id]`, job.service_type_id);
                formData.append(`service_jobs[${index}][price]`, job.price);
                formData.append(`service_jobs[${index}][notes]`, job.notes || '');
            });

            $.ajax({
                url: 'ajax/php/vehicle-service.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('.someBlock').preloader('remove');
                    
                    if (response.status === 'success') {
                        swal({
                            title: 'Success!',
                            text: `Service created! Tracking Code: ${response.tracking_code}`,
                            type: 'success',
                            showConfirmButton: true
                        }, function() {
                            window.location.href = `vehicle-service-tracking.php?id=${response.id}`;
                        });
                    } else {
                        showError('Failed to create service');
                    }
                },
                error: function() {
                    $('.someBlock').preloader('remove');
                    showError('An error occurred');
                }
            });
        }

        function updateService() {
            if (!validateSection('customer') || !validateSection('vehicle')) {
                return;
            }

            updateExpectedCompletionField();

            $('.someBlock').preloader();

            const formData = new FormData($('#service-form')[0]);
            formData.append('update', true);

            // append newly added jobs
            selectedServices.forEach((job, index) => {
                formData.append(`service_jobs[${index}][service_type_id]`, job.service_type_id);
                formData.append(`service_jobs[${index}][price]`, job.price);
                formData.append(`service_jobs[${index}][notes]`, job.notes || '');
            });

            $.ajax({
                url: 'ajax/php/vehicle-service.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('.someBlock').preloader('remove');
                    
                    if (response.status === 'success') {
                        swal({
                            title: 'Success!',
                            text: 'Service updated successfully!',
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        showError('Failed to update service');
                    }
                }
            });
        }

        function updateStage(stage) {
            if (stage > currentStage + 1) {
                showError('Please complete stages in order');
                return;
            }
            
            $('#stage_select').val(stage);
            updateStageFromSelect();
        }

        function updateStageFromSelect() {
            const stage = $('#stage_select').val();
            const notes = $('#stage_notes').val();

            $('.someBlock').preloader();

            $.post('ajax/php/vehicle-service.php', {
                update_stage: true,
                id: serviceId,
                stage: stage,
                notes: notes
            }, function(response) {
                $('.someBlock').preloader('remove');
                
                if (response.status === 'success') {
                    swal({
                        title: 'Success!',
                        text: `Stage updated to: ${response.stage_name}`,
                        type: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showError('Failed to update stage');
                }
            }, 'json');
        }

        function loadServiceJobs() {
            $.get('ajax/php/vehicle-service.php', { get_service: true, id: serviceId }, function(response) {
                if (response.status === 'success' && response.data.jobs) {
                    serviceJobs = response.data.jobs;
                    renderServices();
                }
            }, 'json');
        }

        function loadServiceHistory() {
            $.get('ajax/php/vehicle-service.php', { get_service: true, id: serviceId }, function(response) {
                if (response.status === 'success' && response.data.logs) {
                    let html = '';
                    
                    response.data.logs.forEach(log => {
                        const stageInfo = getStageInfo(log.stage);
                        const date = new Date(log.created_at).toLocaleString();
                        
                        html += `
                            <div class="vs-history-item">
                                <div class="vs-history-icon" style="background:${stageInfo.color};">
                                    <i class="uil uil-history"></i>
                                </div>
                                <div>
                                    <p class="vs-history-title">${stageInfo.name}</p>
                                    <p class="vs-history-notes">${log.notes || 'No notes'}</p>
                                    <div class="vs-history-meta">${date} ${log.user_name ? '- ' + log.user_name : ''}</div>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#serviceHistory').html(html || '<p class="text-muted">No history yet</p>');
                }
            }, 'json');
        }

        function getStageInfo(stage) {
            const stages = {
                1: { name: 'Vehicle Received', color: '#3b82f6' },
                2: { name: 'Inspection', color: '#8b5cf6' },
                3: { name: 'Service Started', color: '#f59e0b' },
                4: { name: 'In Progress', color: '#ec4899' },
                5: { name: 'Quality Check', color: '#10b981' },
                6: { name: 'Ready for Pickup', color: '#06b6d4' },
                7: { name: 'Delivered', color: '#22c55e' }
            };
            return stages[stage] || { name: 'Unknown', color: '#6b7280' };
        }

        function updateTimelineProgress() {
            const totalStages = 7;
            const progress = ((currentStage - 1) / (totalStages - 1)) * 100;
            $('#timelineProgress').css('width', progress + '%');
        }

        function generateQRCode() {
            if (!trackingCode) return;
            
            const trackUrl = baseUrl + 'service-track.php?code=' + trackingCode;
            
            new QRCode(document.getElementById("qrcode"), {
                text: trackUrl,
                width: 180,
                height: 180,
                colorDark: "#1e40af",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        function copyLink() {
            const trackUrl = baseUrl + 'service-track.php?code=' + trackingCode;
            navigator.clipboard.writeText(trackUrl).then(() => {
                swal({
                    title: 'Copied!',
                    text: 'Tracking link copied to clipboard',
                    type: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        function shareWhatsApp() {
            const trackUrl = baseUrl + 'service-track.php?code=' + trackingCode;
            const message = `Track your vehicle service status:\n\nTracking Code: ${trackingCode}\nLink: ${trackUrl}`;
            const phone = $('#customer_phone').val().replace(/\D/g, '');
            
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
        }

        function shareSMS() {
            const trackUrl = baseUrl + 'service-track.php?code=' + trackingCode;
            const message = `Track your vehicle service: ${trackUrl} (Code: ${trackingCode})`;
            const phone = $('#customer_phone').val();
            
            window.open(`sms:${phone}?body=${encodeURIComponent(message)}`, '_blank');
        }

        function initServicesTable() {
            $('#servicesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'ajax/php/vehicle-service.php',
                    type: 'POST',
                    data: function(d) {
                        d.fetch_datatable = true;
                    }
                },
                columns: [
                    { data: 'code' },
                    { data: 'tracking_code' },
                    { data: 'customer_name' },
                    { 
                        data: null,
                        render: function(data) {
                            return `${data.vehicle_no}<br><small class="text-muted">${data.brand_name} ${data.model_name}</small>`;
                        }
                    },
                    { 
                        data: null,
                        render: function(data) {
                            return `<span class="badge" style="background: ${data.stage_color}">${data.stage_name}</span>`;
                        }
                    },
                    { data: 'created_at' },
                    {
                        data: null,
                        render: function(data) {
                            const pageId = <?php echo isset($page_id) ? (int)$page_id : 0; ?>;
                            const pageQuery = pageId ? `&page_id=${pageId}` : '';
                            return `<a href="vehicle-service-tracking.php?id=${data.id}${pageQuery}" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye"></i> View
                            </a>`;
                        }
                    }
                ],
                order: [[5, 'desc']]
            });
        }

        // Load confirmed appointments into modal
        function loadConfirmedAppointments() {
            $('#appointmentsList').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
            
            $.ajax({
                url: 'ajax/php/service-appointment.php',
                type: 'POST',
                data: {
                    fetch_datatable: true,
                    status: 'confirmed',
                    start: 0,
                    length: 50,
                    draw: 1,
                    search: { value: '' }
                },
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(apt => {
                            html += `
                                <div class="appointment-card-item" onclick="selectAppointment(${apt.id})" style="cursor: pointer; padding: 16px; border: 2px solid #e5e7eb; border-radius: 12px; margin-bottom: 12px; transition: all 0.2s;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-primary">${apt.booking_code}</span>
                                            <span class="badge bg-success ms-1">Confirmed</span>
                                        </div>
                                        <small class="text-muted">${apt.preferred_date} at ${apt.preferred_time}</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>${apt.customer_name}</strong><br>
                                            <small class="text-muted">${apt.customer_phone}</small>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <strong>${apt.vehicle_no}</strong><br>
                                            <small class="text-muted">${apt.brand_name || ''} ${apt.model_name || ''}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#appointmentsList').html(html);
                        
                        // Add hover effect
                        $('.appointment-card-item').hover(
                            function() { $(this).css({'border-color': '#f59e0b', 'background': '#fffbeb'}); },
                            function() { $(this).css({'border-color': '#e5e7eb', 'background': 'transparent'}); }
                        );
                    } else {
                        $('#appointmentsList').html(`
                            <div class="text-center py-4 text-muted">
                                <i class="uil uil-calendar-slash" style="font-size: 48px;"></i>
                                <p class="mt-2">No confirmed appointments found.</p>
                            </div>
                        `);
                    }
                }
            });
        }
        
        // Select appointment from modal
        function selectAppointment(appointmentId) {
            $('#appointmentsModal').modal('hide');
            loadAppointmentData(appointmentId);
        }

        // Load appointment data and populate form
        function loadAppointmentData(appointmentId) {
            $.get('ajax/php/service-appointment.php', { get_appointment: true, id: appointmentId }, function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // Populate customer info
                    $('#customer_name').val(data.customer_name);
                    $('#customer_phone').val(data.customer_phone);
                    $('#customer_id').val(0);
                    
                    // Populate vehicle info
                    $('#vehicle_no').val(data.vehicle_no);
                    $('#vehicle_brand_id').val(data.vehicle_brand_id);
                    
                    // Load models and select the right one
                    loadModelsByBrand(data.vehicle_brand_id, data.vehicle_model_id);
                    
                    // Store appointment ID for reference
                    $('#service-form').data('appointment_id', data.id);
                    
                    // Populate remarks with notes
                    if (data.notes) {
                        $('#remarks').val('Appointment Notes: ' + data.notes);
                    }
                    
                    // Unlock all sections for editing
                    $('.vs-section').removeClass('locked').addClass('expanded');
                    $('.vs-section-header').addClass('active');
                    
                    // Add selected services
                    if (data.services && data.services.length > 0) {
                        // Clear existing jobs first (for new service)
                        if ($('#service_id').val() == 0) {
                            selectedServiceJobs = [];
                            data.services.forEach(service => {
                                selectedServiceJobs.push({
                                    service_type_id: service.id,
                                    name: service.name,
                                    price: service.price,
                                    notes: ''
                                });
                            });
                            renderSelectedJobs();
                            updateTotalAmount();
                        }
                    }
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Appointment Loaded',
                        text: 'Form populated with appointment data. You can now create the service.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }, 'json');
        }

        function showError(message) {
            swal({
                title: 'Error!',
                text: message,
                type: 'error',
                timer: 2500,
                showConfirmButton: false
            });
        }
    </script>

    <?php include 'main-js.php' ?>

</body>

</html>
