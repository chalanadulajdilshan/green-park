<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

if (!isset($_SESSION)) {
    session_start();
}

$US = new User($_SESSION['id']);
$COMPANY_PROFILE = new CompanyProfile($US->company_id);
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Service Invoice | <?php echo $COMPANY_PROFILE->name ?></title>
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
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3 no-print gap-2">
            <div>
                <h4 class="mb-1">Service Invoice</h4>
                <small class="text-muted">Enter Service Code or Tracking ID to generate printable invoice</small>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="service_lookup" class="form-control" placeholder="Service Code / Tracking ID" style="min-width:260px;">
                <button class="btn btn-primary" id="fetchServiceBtn"><i class="uil uil-search"></i> Fetch</button>
                <button class="btn btn-success" id="printBtn" disabled><i class="uil uil-print"></i> Print</button>
            </div>
        </div>

        <div class="alert alert-warning d-none" id="infoMessage"></div>

        <div class="card d-none" id="invoice-card">
            <div class="card-body" id="invoice-content">
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
                                    <td id="svc_code">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tracking Code:</td>
                                    <td id="svc_tracking">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created:</td>
                                    <td id="svc_created">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="invoice-border-box mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted">Customer</h6>
                            <p class="mb-1 fw-semibold" id="cust_name">-</p>
                            <p class="mb-1 text-muted" id="cust_address">-</p>
                            <p class="mb-0 text-muted" id="cust_phone">-</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-uppercase text-muted">Vehicle</h6>
                            <p class="mb-1 fw-semibold" id="vehicle_no">-</p>
                            <p class="mb-1 text-muted" id="vehicle_details">-</p>
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
                        <tbody id="jobs_body" style="font-size:13px;"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold" id="total_amount">0.00</td>
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
        let currentService = null;

        function showMessage(type, text) {
            const $msg = $('#infoMessage');
            $msg.removeClass('d-none alert-warning alert-danger alert-success').addClass(`alert-${type}`).text(text);
        }

        function clearMessage() {
            $('#infoMessage').addClass('d-none').text('');
        }

        function renderInvoice(data) {
            currentService = data;
            $('#invoice-card').removeClass('d-none');
            $('#printBtn').prop('disabled', false);

            $('#svc_code').text(data.code || '-');
            $('#svc_tracking').text(data.tracking_code || '-');
            $('#svc_created').text(data.created_at ? new Date(data.created_at).toLocaleString() : '-');

            $('#cust_name').text(data.customer_name || '-');
            $('#cust_address').text(data.customer_address || '-');
            $('#cust_phone').text(data.customer_phone || '-');

            $('#vehicle_no').text(data.vehicle_no || '-');
            $('#vehicle_details').text([data.brand_name, data.model_name].filter(Boolean).join(' ') || '-');

            const jobs = data.jobs || [];
            let rows = '';
            let total = 0;
            jobs.forEach((job, idx) => {
                const price = parseFloat(job.price) || 0;
                total += price;
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
            const totalFromApi = typeof data.total_amount === 'number' ? data.total_amount : parseFloat(data.total_amount);
            const finalTotal = !isNaN(totalFromApi) && totalFromApi > 0 ? totalFromApi : total;
            $('#total_amount').text(finalTotal.toFixed(2));
        }

        function fetchService() {
            const val = $('#service_lookup').val().trim();
            clearMessage();
            if (!val) {
                showMessage('warning', 'Please enter a Service Code or Tracking ID.');
                return;
            }
            $('#fetchServiceBtn').prop('disabled', true).text('Fetching...');
            $.getJSON('ajax/php/vehicle-service.php', { service_lookup: val })
                .done(res => {
                    if (res.status === 'success' && res.data) {
                        renderInvoice(res.data);
                    } else {
                        $('#invoice-card').addClass('d-none');
                        $('#printBtn').prop('disabled', true);
                        showMessage('danger', res.message || 'Service not found');
                    }
                })
                .fail(() => {
                    showMessage('danger', 'Error fetching service');
                })
                .always(() => {
                    $('#fetchServiceBtn').prop('disabled', false).text('Fetch');
                });
        }

        $(document).ready(function () {
            $('#fetchServiceBtn').on('click', fetchService);
            $('#service_lookup').on('keyup', function (e) {
                if (e.key === 'Enter') fetchService();
            });
            $('#printBtn').on('click', function () {
                if (!currentService) return;
                window.print();
            });
        });
    </script>
</body>

</html>
