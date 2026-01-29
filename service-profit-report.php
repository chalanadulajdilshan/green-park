<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

?>

<html lang="en">

<head>

    <meta charset="utf-8" />
    <title> Service Profit Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <!-- include main CSS -->
    <?php include 'main-css.php' ?>

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
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                           
                            <a href="#" class="btn btn-primary" id="view_report">
                                <i class="uil uil-search me-1"></i> View Report
                            </a>

                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active"> Service Profit Report </li>
                            </ol>
                        </div>
                    </div>
                    <!--- Hidden Values -->


                    <!-- end page title -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    01
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">Service Profit Report </h5>
                                            <p class="text-muted text-truncate mb-0">Fill all information below to
                                                view Service Profits </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <form id="form-data" autocomplete="off">
                                            <div class="row">

                                                <div class="col-md-3">
                                                    <label for="from_date" class="form-label">From Date</label>
                                                    <div class="input-group" id="datepicker2">
                                                        <input type="text" class="form-control date-picker"
                                                            id="from_date" name="from_date"> <span
                                                            class="input-group-text"><i
                                                                class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="to_date" class="form-label">To Date</label>
                                                    <div class="input-group" id="datepicker2">
                                                        <input type="text" class="form-control date-picker"
                                                            id="to_date" name="to_date"> <span
                                                            class="input-group-text"><i
                                                                class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>


                                        <hr class="my-4">

                                        <div id="reportDateRange" class="mb-3"></div>

                                        <!-- Table -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="serviceProfitTable">
                                                <thead class="table-light">
                                                    <tr>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Service Name</th>
                                                        <th class="text-center">Total Quantity</th>
                                                        <th class="text-end">Total Earned</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No records found</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="3" class="text-end">Grand Total</th>
                                                        <th class="text-end" id="grandTotal">0.00</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="ajax/js/service-profit-report.js"></script>

    <!-- include main js  -->
    <?php include 'main-js.php' ?>

    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(function() {
            // Initialize the datepicker
            $(".date-picker").datepicker({
                dateFormat: 'yy-mm-dd'
            });

            // Set today's date as default value
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            $(".date-picker").val(today);
        });
    </script>

</body>

</html>
