<!doctype html>
<?php
include 'class/include.php';
include './auth.php';
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Service Billing | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body, html {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">
        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row mb-4 no-print">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <button class="btn btn-success" id="newBtn">
                                <i class="uil uil-plus me-1"></i> New
                            </button>
                            <button class="btn btn-primary" id="paymentBtn" style="display:none;" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="uil uil-credit-card me-1"></i> Save Payment
                            </button>
                        </div>
                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Service Billing</li>
                            </ol>
                        </div>
                    </div>

                    <input type="hidden" id="service_id" name="service_id">
                    <input type="hidden" id="payment_id" name="payment_id">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="p-4 no-print">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    01
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-16 mb-1">Service Billing</h5>
                                            <p class="text-muted text-truncate mb-0">Enter Service Code or Tracking ID to load service details</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <form id="billing-form">
                                        <div class="row no-print">
                                            <div class="col-md-4">
                                                <label class="form-label">Service Code / Tracking ID</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" id="service_lookup" class="form-control" placeholder="Enter Code or Tracking ID">
                                                    <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#serviceSelectModal" title="Select Finished Service">
                                                        <i class="uil uil-search"></i>
                                                    </button>
                                                    <button class="btn btn-primary" type="button" id="fetchServiceBtn">
                                                        <i class="uil uil-arrow-right me-1"></i> Fetch
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning d-none no-print" id="infoMessage"></div>

                                        <div id="service-details" style="display:none;">
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase text-muted mb-3">Service Information</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td class="text-muted" style="width:150px;">Service Code:</td>
                                                            <td class="fw-semibold" id="svc_code">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Tracking Code:</td>
                                                            <td class="fw-semibold" id="svc_tracking">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Created Date:</td>
                                                            <td id="svc_created">-</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase text-muted mb-3">Customer & Vehicle</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td class="text-muted" style="width:150px;">Customer:</td>
                                                            <td class="fw-semibold" id="cust_name">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Phone:</td>
                                                            <td id="cust_phone">-</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Vehicle:</td>
                                                            <td class="fw-semibold" id="vehicle_info">-</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:60px;">No.</th>
                                                            <th>Service</th>
                                                            <th class="text-end" style="width:160px;">Price (LKR)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="jobs_body"></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="2" class="text-end fw-bold">Total Amount</td>
                                                            <td class="text-end fw-bold" id="total_amount">0.00</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>


                                            <div class="row mt-3 no-print">
                                                <div class="col-12">
                                                    <h6 class="mb-3">Payment History</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Payment Type</th>
                                                                    <th>Amount</th>
                                                                    <th>Notes</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="payment_history_body">
                                                                <tr>
                                                                    <td colspan="4" class="text-center text-muted">No payments recorded yet</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
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

    <!-- Service Selection Modal -->
    <div class="modal fade" id="serviceSelectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="uil uil-car me-2"></i> Select Finished Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="finishedServicesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Service Code</th>
                                    <th>Tracking Code</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="finishedServicesBody">
                                <tr><td colspan="5" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="uil uil-credit-card me-2"></i> Finalize Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body bg-light">
                    <form id="paymentForm">
                        <div class="mb-3">
                            <label class="fw-bold">Total Amount</label>
                            <input type="text" id="modalFinalTotal" class="form-control form-control-lg text-end fw-bold border-primary" readonly>
                        </div>

                        <div id="paymentRows" class="mb-3"></div>

                        <button type="button" class="btn btn-outline-primary w-100 mb-4" id="addPaymentRow">
                            <i class="uil uil-plus-circle me-2"></i> Add Payment Method
                        </button>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold">Total Paid</label>
                                <input type="text" id="totalPaid" class="form-control text-end bg-white fw-bold" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">Balance</label>
                                <input type="text" id="balanceAmount" class="form-control text-end bg-white fw-bold" readonly>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="fw-bold">Note</label>
                            <textarea id="paymentNote" rows="2" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="uil uil-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="savePaymentBtn">
                        <i class="uil uil-check me-1"></i> Save Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <?php include 'main-js.php' ?>
    <script>
        let currentService = null;
        let totalAmount = 0;
        let rowId = 0;
        let paymentTypes = [];

        function showMessage(type, text) {
            const $msg = $('#infoMessage');
            $msg.removeClass('d-none alert-warning alert-danger alert-success').addClass(`alert-${type}`).text(text);
        }

        function clearMessage() {
            $('#infoMessage').addClass('d-none').text('');
        }

        function createPaymentRow() {
            rowId++;
            const row = $(`
                <div class="payment-row card shadow-sm mb-3" data-id="${rowId}">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="fw-semibold">Payment Type</label>
                                <select name="paymentType[]" class="form-select paymentType" required>
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-semibold">Amount</label>
                                <input type="number" name="amount[]" class="form-control paymentAmount text-end fw-bold" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-4 chequeDetails d-none">
                                <label class="fw-semibold">Cheque No</label>
                                <input type="text" name="chequeNumber[]" class="form-control mb-2">
                                <label class="fw-semibold">Bank</label>
                                <input type="text" name="chequeBank[]" class="form-control mb-2">
                                <label class="fw-semibold">Date</label>
                                <input type="date" name="chequeDate[]" class="form-control">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                    <i class="uil uil-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Populate payment types
            const select = row.find('.paymentType');
            paymentTypes.forEach(type => {
                select.append(`<option value="${type.id}">${type.name}</option>`);
            });

            $('#paymentRows').append(row);

            row.find('.paymentType').on('change', function() {
                const selectedText = $(this).find('option:selected').text().toLowerCase();
                const chequeDetails = row.find('.chequeDetails');
                if (selectedText.includes('cheque')) {
                    chequeDetails.removeClass('d-none');
                } else {
                    chequeDetails.addClass('d-none');
                }
            });

            row.find('.paymentAmount').on('input', updatePaymentTotals);
            row.find('.removeRow').on('click', function() {
                row.remove();
                updatePaymentTotals();
            });
        }

        function updatePaymentTotals() {
            let total = 0;
            $('.paymentAmount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#totalPaid').val(total.toFixed(2));
            $('#balanceAmount').val((totalAmount - total).toFixed(2));
        }

        function loadPaymentTypes() {
            $.getJSON('ajax/php/service-payment.php', { get_payment_types: true })
                .done(res => {
                    if (res.status === 'success') {
                        paymentTypes = res.data;
                    }
                });
        }

        function loadFinishedServices() {
            $('#finishedServicesBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
            $.getJSON('ajax/php/service-payment.php', { get_finished_services: true })
                .done(res => {
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        let rows = '';
                        res.data.forEach(svc => {
                            const vehicle = [svc.vehicle_no, svc.brand_name, svc.model_name].filter(Boolean).join(' - ');
                            const date = svc.created_at ? new Date(svc.created_at).toLocaleDateString() : '-';
                            rows += `
                                <tr style="cursor:pointer;" data-code="${svc.code}" data-id="${svc.id}">
                                    <td>${svc.code || '-'}</td>
                                    <td>${svc.tracking_code || '-'}</td>
                                    <td>${svc.customer_name || '-'}</td>
                                    <td>${vehicle || '-'}</td>
                                    <td>${date}</td>
                                </tr>
                            `;
                        });
                        $('#finishedServicesBody').html(rows);
                    } else {
                        $('#finishedServicesBody').html('<tr><td colspan="5" class="text-center text-muted">No finished services found</td></tr>');
                    }
                })
                .fail(() => {
                    $('#finishedServicesBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading services</td></tr>');
                });
        }

        function loadPaymentHistory(serviceId) {
            $.getJSON('ajax/php/service-payment.php', { get_payments: true, service_id: serviceId })
                .done(res => {
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        let rows = '';
                        res.data.forEach(payment => {
                            rows += `
                                <tr>
                                    <td>${new Date(payment.created_at).toLocaleString()}</td>
                                    <td>${payment.payment_type_name || '-'}</td>
                                    <td>LKR ${parseFloat(payment.amount).toFixed(2)}</td>
                                    <td>${payment.notes || '-'}</td>
                                </tr>
                            `;
                        });
                        $('#payment_history_body').html(rows);
                    } else {
                        $('#payment_history_body').html('<tr><td colspan="4" class="text-center text-muted">No payments recorded yet</td></tr>');
                    }
                });
        }

        function renderService(data) {
            currentService = data;
            $('#service-details').show();
            $('#paymentBtn').show();

            $('#service_id').val(data.id);
            $('#svc_code').text(data.code || '-');
            $('#svc_tracking').text(data.tracking_code || '-');
            $('#svc_created').text(data.created_at ? new Date(data.created_at).toLocaleString() : '-');

            $('#cust_name').text(data.customer_name || '-');
            $('#cust_phone').text(data.customer_phone || '-');
            $('#vehicle_info').text(`${data.vehicle_no || '-'} (${[data.brand_name, data.model_name].filter(Boolean).join(' ') || '-'})`);

            const jobs = data.jobs || [];
            let rows = '';
            totalAmount = 0;
            jobs.forEach((job, idx) => {
                const price = parseFloat(job.price) || 0;
                totalAmount += price;
                rows += `
                    <tr>
                        <td>${String(idx + 1).padStart(2, '0')}</td>
                        <td>${job.service_name || 'Service'}</td>
                        <td class="text-end">${price.toFixed(2)}</td>
                    </tr>
                `;
            });
            if (!rows) {
                rows = '<tr><td colspan="3" class="text-center text-muted">No service items found</td></tr>';
            }
            $('#jobs_body').html(rows);
            $('#total_amount').text(totalAmount.toFixed(2));
            $('#modalFinalTotal').val(totalAmount.toFixed(2));
            
            loadPaymentHistory(data.id);
        }

        function fetchService() {
            const val = $('#service_lookup').val().trim();
            clearMessage();
            if (!val) {
                showMessage('warning', 'Please enter a Service Code or Tracking ID.');
                return;
            }
            $('#fetchServiceBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> Fetching...');
            
            $.getJSON('ajax/php/vehicle-service.php', { service_lookup: val })
                .done(res => {
                    if (res.status === 'success' && res.data) {
                        renderService(res.data);
                    } else {
                        $('#service-details').hide();
                        $('#paymentBtn').hide();
                        showMessage('danger', res.message || 'Service not found');
                    }
                })
                .fail(() => {
                    showMessage('danger', 'Error fetching service');
                })
                .always(() => {
                    $('#fetchServiceBtn').prop('disabled', false).html('<i class="uil uil-search me-1"></i> Fetch');
                });
        }

        function savePayment() {
            const serviceId = $('#service_id').val();
            const note = $('#paymentNote').val();

            if (!serviceId) {
                showMessage('danger', 'Please fetch a service first');
                return;
            }

            const payments = [];
            let isValid = true;

            $('.payment-row').each(function() {
                const paymentType = $(this).find('.paymentType').val();
                const amount = parseFloat($(this).find('.paymentAmount').val()) || 0;
                const chequeNo = $(this).find('input[name="chequeNumber[]"]').val();
                const chequeBank = $(this).find('input[name="chequeBank[]"]').val();
                const chequeDate = $(this).find('input[name="chequeDate[]"]').val();

                if (!paymentType || amount <= 0) {
                    isValid = false;
                    return false;
                }

                payments.push({
                    payment_type_id: paymentType,
                    amount: amount,
                    cheque_no: chequeNo,
                    cheque_bank: chequeBank,
                    cheque_date: chequeDate
                });
            });

            if (!isValid || payments.length === 0) {
                showMessage('danger', 'Please add at least one valid payment');
                return;
            }

            $('.someBlock').preloader();

            $.post('ajax/php/service-payment.php', {
                add_payments: true,
                service_id: serviceId,
                payments: JSON.stringify(payments),
                notes: note
            }, function(response) {
                $('.someBlock').preloader('remove');
                
                if (response.status === 'success') {
                    $('#paymentModal').modal('hide');
                    swal({
                        title: 'Success!',
                        text: 'Payment recorded successfully',
                        type: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    setTimeout(function() {
                        window.location.href = 'service-billing-print.php?service_id=' + serviceId;
                    }, 1500);
                } else {
                    showMessage('danger', response.message || 'Failed to save payment');
                }
            }, 'json').fail(() => {
                $('.someBlock').preloader('remove');
                showMessage('danger', 'Error saving payment');
            });
        }

        $(document).ready(function() {
            loadPaymentTypes();

            $('#fetchServiceBtn').on('click', fetchService);
            $('#service_lookup').on('keyup', function(e) {
                if (e.key === 'Enter') fetchService();
            });

            $('#addPaymentRow').on('click', createPaymentRow);
            $('#savePaymentBtn').on('click', savePayment);

            $('#paymentModal').on('show.bs.modal', function() {
                $('#paymentRows').empty();
                $('#paymentNote').val('');
                rowId = 0;
                createPaymentRow();
                // Auto-fill with total amount
                setTimeout(function() {
                    $('.paymentAmount').first().val(totalAmount.toFixed(2));
                    updatePaymentTotals();
                }, 100);
            });

            // Service selection modal
            $('#serviceSelectModal').on('show.bs.modal', function() {
                loadFinishedServices();
            });

            $(document).on('click', '#finishedServicesBody tr', function() {
                const code = $(this).data('code');
                if (code) {
                    $('#service_lookup').val(code);
                    $('#serviceSelectModal').modal('hide');
                    fetchService();
                }
            });

            $('#newBtn').on('click', function() {
                $('#service_lookup').val('');
                $('#service-details').hide();
                $('#paymentBtn').hide();
                currentService = null;
                clearMessage();
            });
        });
    </script>
</body>

</html>
