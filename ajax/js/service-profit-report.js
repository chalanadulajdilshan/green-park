$(document).ready(function () {

    // Initial load? Optional. Maybe wait for user click.

    $('#view_report').on('click', function (e) {
        e.preventDefault();
        loadServiceProfitReport();
    });

    function loadServiceProfitReport() {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        if (!fromDate || !toDate) {
            swal("Error", "Please select both start and end dates", "error");
            return;
        }

        // Show loading state
        $('#serviceProfitTable tbody').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        $('#grandTotal').html('0.00');

        $.ajax({
            url: 'ajax/php/report.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_service_profit_report',
                from_date: fromDate,
                to_date: toDate
            },
            success: function (response) {
                let tbody = '';
                let grandTotal = 0;

                // Debug check if response is array or has error
                if (response.error) {
                    $('#serviceProfitTable tbody').html(`<tr><td colspan="4" class="text-center text-danger">${response.error}</td></tr>`);
                    return;
                }

                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function (index, row) {
                        index++;
                        const earned = parseFloat(row.total_earned) || 0;
                        const qty = parseFloat(row.total_qty) || 0;
                        grandTotal += earned;

                        // Main Row
                        tbody += `<tr class="service-row" data-id="${row.service_item_id}" data-index="${index}" style="cursor: pointer;">
                            <td>${index} <i class="mdi mdi-chevron-down float-end expand-icon"></i></td>
                            <td>${row.item_name}</td>
                            <!-- Item Code Removed -->
                            <td class="text-center">${qty}</td>
                            <td class="text-end">${earned.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        </tr>
                        <!-- Detail Row (Hidden) -->
                        <tr class="detail-row d-none" id="detail-${row.service_item_id}">
                            <td colspan="4" class="p-3 bg-light">
                                <div class="text-center p-2 loading-details"><i class="mdi mdi-spin mdi-loading me-2"></i> Loading details...</div>
                                <table class="table table-sm table-bordered mb-0 bg-white d-none detail-table">
                                    <thead>
                                        <tr>
                                            <th>Invoice No</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="detail-tbody"></tbody>
                                </table>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody = '<tr><td colspan="4" class="text-center text-muted">No records found for this period</td></tr>';
                }

                $('#serviceProfitTable tbody').html(tbody);
                $('#grandTotal').text(grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                $('#reportDateRange').html(`<h6>Service Profit Report from <strong>${fromDate}</strong> to <strong>${toDate}</strong></h6>`);
            },
            error: function (xhr, status, error) {
                console.error("Error loading report:", error);
                $('#serviceProfitTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Error loading data. See console for details.</td></tr>');
            }
        });
    }

    // Handle row expansion
    $(document).on('click', '.service-row', function () {
        const serviceId = $(this).data('id');
        const detailRow = $(`#detail-${serviceId}`);
        const icon = $(this).find('.expand-icon');

        if (detailRow.hasClass('d-none')) {
            // Expand
            detailRow.removeClass('d-none');
            icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');

            // Load data if not already loaded
            const detailBody = detailRow.find('.detail-tbody');
            if (detailBody.children().length === 0) {
                loadServiceDetails(serviceId, detailRow);
            }
        } else {
            // Collapse
            detailRow.addClass('d-none');
            icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
        }
    });

    function loadServiceDetails(serviceId, detailRow) {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        $.ajax({
            url: 'ajax/php/report.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_service_invoice_details',
                service_item_id: serviceId,
                from_date: fromDate,
                to_date: toDate
            },
            success: function (response) {
                const detailBody = detailRow.find('.detail-tbody');
                const loadingDiv = detailRow.find('.loading-details');
                const detailTable = detailRow.find('.detail-table');

                let rows = '';

                if (Array.isArray(response) && response.length > 0) {
                    $.each(response, function (i, inv) {
                        rows += `<tr>
                            <td><a href="sales-invoice-view.php?invoice_id=${inv.id}" target="_blank">${inv.invoice_no}</a></td>
                            <td>${inv.invoice_date}</td>
                            <td>${inv.customer_name}</td>
                            <td class="text-center">${inv.quantity}</td>
                            <td class="text-end">${parseFloat(inv.total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center text-muted">No details found</td></tr>';
                }

                detailBody.html(rows);
                loadingDiv.addClass('d-none');
                detailTable.removeClass('d-none');
            },
            error: function () {
                detailRow.find('.loading-details').html('<span class="text-danger">Failed to load details</span>');
            }
        });
    }

});
