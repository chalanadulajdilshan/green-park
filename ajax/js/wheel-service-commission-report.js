$(document).ready(function () {

    function loadTotals() {
        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();

        // Show loading state
        $("#total-commission-display").text("Loading...");

        $.ajax({
            url: "ajax/php/wheel-service-commission-report.php",
            type: "POST",
            data: {
                action: "get_totals",
                from_date: fromDate,
                to_date: toDate
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#total-commission-display").text(parseFloat(response.total_commission || 0).toFixed(2));
                } else {
                    $("#total-commission-display").text("Error");
                }
            },
            error: function () {
                $("#total-commission-display").text("Error");
            }
        });
    }

    function loadInvoices() {
        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();

        const tbody = $("#invoice-table tbody");
        tbody.html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: "ajax/php/wheel-service-commission-report.php",
            type: "POST",
            data: {
                action: "get_invoices",
                from_date: fromDate,
                to_date: toDate
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let rows = "";
                    if (response.data.length > 0) {
                        response.data.forEach((invoice, index) => {
                            rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${invoice.invoice_date}</td>
                                <td>${invoice.invoice_no}</td>
                                <td>${invoice.customer_name || '-'}</td>
                                <td class="text-end">${parseFloat(invoice.grand_total).toFixed(2)}</td>
                                <td class="text-end">${parseFloat(invoice.wheel_service_commission).toFixed(2)}</td>
                                <td>
                                    <a href="sales-invoice-view.php?invoice_id=${invoice.id}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="uil uil-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                        });
                        tbody.html(rows);
                    } else {
                        tbody.html('<tr><td colspan="7" class="text-center">No invoices found.</td></tr>');
                    }
                } else {
                    tbody.html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>');
                }
            },
            error: function () {
                tbody.html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>');
            }
        });
    }

    // Load totals and invoices on start
    loadTotals();
    loadInvoices();

    // Reload totals and invoices when date range changes
    $("#from_date, #to_date").change(function () {
        loadTotals();
        loadInvoices();
    });

});
