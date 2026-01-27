<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

$WHEEL_BALANCER = new WheelBalancer(NULL);
$balancers = $WHEEL_BALANCER->getActiveWheelBalancers();
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Wheel Balancer Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
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
                                <h4 class="mb-0">Wheel Balancer Report</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Wheel Balancer Report</li>
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

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Total Amount</th>
                                                    <th>Total Commission</th>
                                                    <th>Remark</th>
                                                    <th style="width: 100px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (count($balancers) > 0) {
                                                    foreach ($balancers as $key => $wb) {
                                                ?>
                                                        <tr class="balancer-row" data-id="<?php echo $wb['id']; ?>">
                                                            <td><?php echo $key + 1; ?></td>
                                                            <td><?php echo $wb['code']; ?></td>
                                                            <td><?php echo $wb['name']; ?></td>
                                                            <td class="total-amount" data-id="<?php echo $wb['id']; ?>">0.00</td>
                                                            <td class="total-commission" data-id="<?php echo $wb['id']; ?>">0.00</td>
                                                            <td><?php echo $wb['remark']; ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-primary btn-expand" title="View Invoices">
                                                                    <i class="uil uil-plus"></i> View
                                                                </button>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="5" class="text-center">No Wheel Balancers Found</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
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
    <script src="ajax/js/wheel-balancer-report.js"></script>

</body>

</html>
