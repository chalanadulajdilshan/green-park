<!doctype html>
<?php
include 'class/include.php';
include './auth.php';
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Wheel Service Commission Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <!-- include main CSS -->
    <?php include 'main-css.php' ?>
    <link href="assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
</head>

<body data-layout="horizontal" data-topbar="colored">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">Wheel Service Commission Report</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Wheel Service Commission Report</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Date Range</label>
                                            <div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                                <input type="text" class="form-control" name="start" id="from_date" value="<?php echo date('Y-m-01') ?>" placeholder="Start Date" />
                                                <input type="text" class="form-control" name="end" id="to_date" value="<?php echo date('Y-m-d') ?>" placeholder="End Date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-primary text-white-50">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start">
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <h5 class="text-white font-size-15 text-truncate">Total Commission</h5>
                                                            <h3 class="text-white mb-0" id="total-commission-display">0.00</h3>
                                                        </div>
                                                        <div class="flex-shrink-0 align-self-end">
                                                            <i class="uil uil-money-bill text-white font-size-24"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped mb-0" id="invoice-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Date</th>
                                                            <th>Invoice No</th>
                                                            <th>Customer</th>
                                                            <th class="text-end">Amount</th>
                                                            <th class="text-end">Commission</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Data will be populated via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include 'footer.php' ?>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right button to go top -->
    <!-- <?php include 'right-bar.php' ?> -->

    <!-- Standard Scripts -->
    <?php include 'main-js.php' ?>
    <script src="assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="ajax/js/wheel-service-commission-report.js"></script>

</body>

</html>
