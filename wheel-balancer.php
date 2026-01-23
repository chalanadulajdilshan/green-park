<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';

$WHEEL_BALANCER = new WheelBalancer(NULL);

// Get the last inserted id for next code generation
$lastId = $WHEEL_BALANCER->getLastID();
$next_code = 'WB/0' . ($lastId + 1);

?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Wheel Balancer Master | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <a href="#" class="btn btn-success" id="new">
                                <i class="uil uil-plus me-1"></i> New
                            </a>

                            <?php if ($PERMISSIONS['add_page']): ?>
                            <a href="#" class="btn btn-primary" id="create">
                                <i class="uil uil-save me-1"></i> Save
                            </a>
                            <?php endif; ?>

                            <?php if ($PERMISSIONS['edit_page']): ?>
                            <a href="#" class="btn btn-warning" id="update" style="display:none;">
                                <i class="uil uil-edit me-1"></i> Update
                            </a>
                            <?php endif; ?>

                            <?php if ($PERMISSIONS['delete_page']): ?>
                            <a href="#" class="btn btn-danger delete-wheel-balancer">
                                <i class="uil uil-trash-alt me-1"></i> Delete
                            </a>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Wheel Balancer Master</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    WB
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">Wheel Balancer Master</h5>
                                            <p class="text-muted text-truncate mb-0">Manage Wheel Balancers information</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <form id="form-data" autocomplete="off">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label" for="code">Code</label>
                                                <div class="input-group mb-3">
                                                    <input id="code" name="code" type="text" class="form-control"
                                                        placeholder="Code" readonly
                                                        value="<?php echo $next_code ?>">
                                                    <button class="btn btn-info" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#wheelBalancerModal">
                                                        <i class="uil uil-search me-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input id="name" name="name" type="text" class="form-control"
                                                    placeholder="Enter Wheel Balancer Name" onkeyup="toUpperCaseInput(this)">
                                            </div>

                                            <div class="col-md-1 d-flex justify-content-center align-items-center mt-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                                    <label class="form-check-label" for="is_active">Active</label>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="remark" class="form-label">Remark Note</label>
                                                <textarea id="remark" name="remark" class="form-control" rows="3"
                                                    placeholder="Enter any remarks..."></textarea>
                                            </div>
                                            <input type="hidden" name="id" id="wheel_balancer_id">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="wheelBalancerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Wheel Balancers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="datatable table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Remark</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $WHEEL_BALANCER = new WheelBalancer(null);
                                foreach ($WHEEL_BALANCER->all() as $key => $wb) {
                                ?>
                                <tr class="select-wheel-balancer" 
                                    data-id="<?php echo $wb['id']; ?>"
                                    data-code="<?php echo htmlspecialchars($wb['code']); ?>"
                                    data-name="<?php echo htmlspecialchars($wb['name']); ?>"
                                    data-remark="<?php echo htmlspecialchars($wb['remark']); ?>"
                                    data-status="<?php echo $wb['is_active']; ?>">

                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo htmlspecialchars($wb['code']); ?></td>
                                    <td><?php echo htmlspecialchars($wb['name']); ?></td>
                                    <td><?php echo htmlspecialchars($wb['remark']); ?></td>
                                    <td>
                                        <?php if ($wb['is_active'] == 1): ?>
                                            <span class="badge bg-soft-success font-size-12">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-soft-danger font-size-12">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="ajax/js/wheel-balancer.js"></script>
    <?php include 'main-js.php' ?>
</body>
</html>
