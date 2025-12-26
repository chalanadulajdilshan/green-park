<!doctype html>
<?php
include 'class/include.php';

if (!isset($_SESSION)) {
    session_start();
}

$service_param = $_GET['service_id'] ?? null;

if (!$service_param) {
    die('Service ID required');
}

$US = new User($_SESSION['id']);
$COMPANY_PROFILE = new CompanyProfile($US->company_id);
$SERVICE = new VehicleService($service_param);

if (!$SERVICE->id) {
    die('Service not found');
}
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Service Invoice - <?php echo $SERVICE->code ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'main-css.php' ?>
    <link href="https://unicons.iconscout.com/release/v4.0.8/css/line.css" rel="stylesheet">

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body,
            html {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            #invoice-content,
            .card {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            @page {
                size: auto;
                margin: 10mm;
            }
        }

        #invoice-content table,
        #invoice-content th,
        #invoice-content td {
            padding: 6px 8px !important;
            margin: 0 !important;
            border-spacing: 0 !important;
            border-collapse: collapse !important;
        }

        #invoice-content th,
        #invoice-content td {
            vertical-align: middle !important;
        }

        #invoice-content .table {
            width: 100%;
            border-top-width: 0 !important;
            border-style: none !important;
        }

        .invoice-meta table td {
            padding: 4px 6px !important;
        }

        .invoice-border-box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <h4>Service Invoice</h4>
            <div>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="uil uil-print me-1"></i> Print
                </button>
                <a href="service-billing.php" class="btn btn-secondary">
                    <i class="uil uil-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card" id="invoice-content">
            <div class="card-body">
                <div class="invoice-title mb-4">
                    <div class="row">
                        <div class="col-md-6 text-muted">
                            <p class="mb-1 fw-bold fs-5"><?php echo $COMPANY_PROFILE->name ?></p>
                            <p class="mb-1" style="font-size:13px;"><?php echo $COMPANY_PROFILE->address ?></p>
                            <p class="mb-1" style="font-size:13px;"><?php echo $COMPANY_PROFILE->email ?> | <?php echo $COMPANY_PROFILE->mobile_number_1; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end invoice-meta">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="text-muted">Service Code:</td>
                                    <td><?php echo $SERVICE->code ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tracking Code:</td>
                                    <td><?php echo $SERVICE->tracking_code ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created:</td>
                                    <td><?php echo date('d M, Y h:i A', strtotime($SERVICE->created_at)) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="invoice-border-box mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Customer</h6>
                            <p class="mb-1 fw-semibold"><?php echo $SERVICE->customer_name ?></p>
                            <p class="mb-1 text-muted"><?php echo $SERVICE->customer_address ?></p>
                            <p class="mb-0 text-muted"><?php echo $SERVICE->customer_phone ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-uppercase text-muted">Vehicle</h6>
                            <p class="mb-1 fw-semibold"><?php echo $SERVICE->vehicle_no ?></p>
                            <?php
                            $brand = new VehicleBrand($SERVICE->vehicle_brand_id);
                            $model = new VehicleModel($SERVICE->vehicle_model_id);
                            ?>
                            <p class="mb-1 text-muted"><?php echo $brand->name . ' ' . $model->name ?></p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered">
                        <thead>
                            <tr>
                                <th style="width:60px;">No.</th>
                                <th>Service</th>
                                <th class="text-end" style="width:160px;">Price (LKR)</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:13px;">
                            <?php
                            $jobs = $SERVICE->getServiceJobs();
                            $total = 0;
                            foreach ($jobs as $idx => $job) {
                                $price = (float)$job['price'];
                                $total += $price;
                            ?>
                                <tr>
                                    <td><?php echo str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></td>
                                    <td><?php echo $job['service_name'] ?></td>
                                    <td class="text-end"><?php echo number_format($price, 2) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold"><?php echo number_format($total, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <?php include 'main-js.php' ?>
    <script>
        // Auto print on load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
