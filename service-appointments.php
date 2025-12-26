<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

$APPOINTMENT = new ServiceAppointment();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Service Appointments | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
    
    <style>
        .appointment-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 16px;
            overflow: hidden;
        }
        
        .appointment-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-code {
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-confirmed {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #059669;
        }
        
        .appointment-body {
            padding: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        
        .services-list {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
        }
        
        .services-title {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .service-tag {
            display: inline-block;
            padding: 4px 10px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 13px;
            margin: 2px 4px 2px 0;
            color: #475569;
        }
        
        .appointment-footer {
            padding: 16px 20px;
            background: #f8fafc;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-confirm {
            background: #2563eb;
            color: white;
        }
        
        .btn-confirm:hover {
            background: #1d4ed8;
        }
        
        .btn-complete {
            background: #059669;
            color: white;
        }
        
        .btn-complete:hover {
            background: #047857;
        }
        
        .btn-cancel {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .btn-cancel:hover {
            background: #fecaca;
        }
        
        .filters-bar {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }
        
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            min-width: 150px;
        }
        
        .total-estimate {
            font-weight: 700;
            color: #059669;
        }
        
        .datetime-info {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 13px;
        }
        
        .notes-section {
            margin-top: 12px;
            padding: 12px;
            background: #fffbeb;
            border-radius: 8px;
            font-size: 13px;
            color: #92400e;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Service Appointments</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Service Appointments</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filters-bar">
                        <div class="filter-group">
                            <span class="filter-label">Status:</span>
                            <select class="filter-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Date:</span>
                            <input type="date" class="filter-select" id="dateFilter">
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="loadAppointments()">
                            <i class="uil uil-filter me-1"></i> Filter
                        </button>
                        <button class="btn btn-light btn-sm" onclick="clearFilters()">
                            <i class="uil uil-times me-1"></i> Clear
                        </button>
                    </div>

                    <!-- Appointments List -->
                    <div id="appointments-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php include './footer.php' ?>
        </div>
    </div>

    <?php include 'main-js.php' ?>
    
    <script>
        $(document).ready(function() {
            loadAppointments();
        });

        function loadAppointments() {
            const status = $('#statusFilter').val();
            const date = $('#dateFilter').val();
            
            $('#appointments-container').html(`
                <div class="text-center py-5">
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
                    status: status,
                    date: date,
                    start: 0,
                    length: 100,
                    draw: 1,
                    search: { value: '' }
                },
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        renderAppointments(response.data);
                    } else {
                        $('#appointments-container').html(`
                            <div class="empty-state">
                                <i class="uil uil-calendar-slash"></i>
                                <h5>No Appointments Found</h5>
                                <p>There are no appointments matching your criteria.</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#appointments-container').html(`
                        <div class="empty-state">
                            <i class="uil uil-exclamation-triangle"></i>
                            <h5>Error Loading Appointments</h5>
                            <p>Please try again later.</p>
                        </div>
                    `);
                }
            });
        }

        function renderAppointments(appointments) {
            let html = '';
            
            appointments.forEach(apt => {
                const statusClass = `status-${apt.status}`;
                const statusText = apt.status_badge.text;
                
                html += `
                    <div class="appointment-card" id="apt-${apt.id}">
                        <div class="appointment-header">
                            <div>
                                <span class="booking-code">${apt.booking_code}</span>
                                <div class="datetime-info mt-1">
                                    <i class="uil uil-calendar-alt"></i> ${apt.preferred_date}
                                    <i class="uil uil-clock ms-2"></i> ${apt.preferred_time}
                                </div>
                            </div>
                            <span class="status-badge ${statusClass}">${statusText}</span>
                        </div>
                        <div class="appointment-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Customer</span>
                                    <span class="info-value">${apt.customer_name}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Phone</span>
                                    <span class="info-value">${apt.customer_phone}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Vehicle</span>
                                    <span class="info-value">${apt.vehicle_no}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Vehicle Details</span>
                                    <span class="info-value">${apt.brand_name || '-'} ${apt.model_name || ''}</span>
                                </div>
                            </div>
                            <div class="services-list" id="services-${apt.id}">
                                <span class="services-title">Services</span>
                                <div class="services-tags">Loading...</div>
                            </div>
                        </div>
                        <div class="appointment-footer">
                            <span class="datetime-info">
                                <i class="uil uil-clock-three"></i> Booked: ${apt.created_at}
                            </span>
                            <div class="action-buttons">
                                ${getActionButtons(apt)}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#appointments-container').html(html);
            
            // Load services for each appointment
            appointments.forEach(apt => {
                loadAppointmentServices(apt.id);
            });
        }

        function getActionButtons(apt) {
            let buttons = '';
            
            if (apt.status === 'pending') {
                buttons += `<button class="btn-action btn-confirm" onclick="updateStatus(${apt.id}, 'confirmed')">
                    <i class="uil uil-check"></i> Confirm
                </button>`;
                buttons += `<button class="btn-action btn-cancel" onclick="updateStatus(${apt.id}, 'cancelled')">
                    <i class="uil uil-times"></i> Cancel
                </button>`;
            } else if (apt.status === 'confirmed') {
                buttons += `<button class="btn-action btn-complete" onclick="updateStatus(${apt.id}, 'completed')">
                    <i class="uil uil-check-circle"></i> Complete
                </button>`;
                buttons += `<button class="btn-action btn-cancel" onclick="updateStatus(${apt.id}, 'cancelled')">
                    <i class="uil uil-times"></i> Cancel
                </button>`;
            }
            
            return buttons;
        }

        function loadAppointmentServices(id) {
            $.get('ajax/php/service-appointment.php', { get_appointment: true, id: id }, function(response) {
                if (response.status === 'success' && response.data.services) {
                    let serviceHtml = '';
                    response.data.services.forEach(s => {
                        serviceHtml += `<span class="service-tag">${s.name} - LKR ${parseFloat(s.price).toFixed(2)}</span>`;
                    });
                    serviceHtml += `<div class="mt-2"><strong class="total-estimate">Total: LKR ${response.data.total_estimate}</strong></div>`;
                    $(`#services-${id} .services-tags`).html(serviceHtml);
                    
                    // Add notes if present
                    if (response.data.notes) {
                        $(`#services-${id}`).after(`
                            <div class="notes-section">
                                <i class="uil uil-comment-notes me-1"></i>
                                <strong>Notes:</strong> ${response.data.notes}
                            </div>
                        `);
                    }
                }
            }, 'json');
        }

        function updateStatus(id, status) {
            const statusLabels = {
                'confirmed': 'confirm',
                'cancelled': 'cancel',
                'completed': 'mark as completed'
            };
            
            swal({
                title: 'Are you sure?',
                text: `Do you want to ${statusLabels[status]} this appointment?`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'cancelled' ? '#dc2626' : '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, ' + statusLabels[status],
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.post('ajax/php/service-appointment.php', {
                        update_status: true,
                        id: id,
                        status: status
                    }, function(response) {
                        if (response.status === 'success') {
                            swal({
                                title: 'Updated!',
                                text: 'Appointment status has been updated.',
                                type: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadAppointments();
                        } else {
                            swal('Error', 'Failed to update status', 'error');
                        }
                    }, 'json');
                }
            });
        }

        function clearFilters() {
            $('#statusFilter').val('');
            $('#dateFilter').val('');
            loadAppointments();
        }
    </script>

</body>

</html>
