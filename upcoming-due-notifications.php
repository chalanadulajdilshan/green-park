<!doctype html>
<?php
include 'class/include.php';
include './auth.php';
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Upcoming Due Notifications | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="ARN Due Date Notifications" name="description" />
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

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">Upcoming Due Notifications (Next 7 Days)</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Notifications</li>
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
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ARN No</th>
                                                <th>Supplier</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Total Amount</th>
                                                <th class="text-end">Paid Amount</th>
                                                <th class="text-end">Balance</th>
                                                <th>Days Left</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $ARN_MASTER = new ArnMaster();
                                            $upcomingArns = $ARN_MASTER->getUpcomingDueArns(7);
                                            
                                            $today = new DateTime();
                                            
                                            foreach ($upcomingArns as $key => $arn) {
                                                $dueDate = new DateTime($arn['due_date']);
                                                $interval = $today->diff($dueDate);
                                                $daysLeft = $interval->format('%r%a');
                                                $balance = $arn['total_arn_value'] - $arn['paid_amount'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $key + 1; ?></td>
                                                    <td><?php echo $arn['arn_no']; ?></td>
                                                    <td><?php echo $arn['supplier_code'] . ' - ' . $arn['supplier_name']; ?></td>
                                                    <td><?php echo $arn['invoice_date']; ?></td>
                                                    <td><span class="badge bg-danger font-size-12"><?php echo $arn['due_date']; ?></span></td>
                                                    <td class="text-end"><?php echo number_format($arn['total_arn_value'], 2); ?></td>
                                                    <td class="text-end"><?php echo number_format($arn['paid_amount'], 2); ?></td>
                                                    <td class="text-end fw-bold"><?php echo number_format($balance, 2); ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($daysLeft < 0) {
                                                            echo '<span class="text-danger fw-bold">Overdue ' . abs($daysLeft) . ' days</span>';
                                                        } elseif ($daysLeft == 0) {
                                                            echo '<span class="text-danger fw-bold">Due Today</span>';
                                                        } else {
                                                            echo '<span class="text-warning fw-bold">' . $daysLeft . ' days left</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <!-- end row -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include 'footer.php' ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php include 'main-js.php' ?>
    
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>

</body>

</html>
