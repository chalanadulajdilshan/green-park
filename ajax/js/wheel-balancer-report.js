$(document).ready(function () {
    $(".btn-expand").click(function () {
        const btn = $(this);
        const tr = btn.closest("tr");
        const id = tr.data("id");

        // Check if already expanded
        if (tr.next().hasClass("expanded-row")) {
            tr.next().remove();
            btn.html('<i class="uil uil-plus"></i> View');
            btn.removeClass("btn-danger").addClass("btn-primary");
            return;
        }

        // Expand
        btn.html('<i class="uil uil-minus"></i> Close');
        btn.removeClass("btn-primary").addClass("btn-danger");

        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();

        // Add loading row
        const loadingRow = `<tr class="expanded-row"><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
        tr.after(loadingRow);

        $.ajax({
            url: "ajax/php/wheel-balancer-report.php",
            type: "POST",
            data: {
                action: "get_invoices",
                id: id,
                from_date: fromDate,
                to_date: toDate,
            },
            dataType: "json",
            success: function (response) {
                tr.next().remove(); // Remove loading

                if (response.status === "success" && response.data.length > 0) {
                    let rows = "";
                    response.data.forEach((invoice, index) => {
                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${invoice.invoice_date}</td>
                            <td>${invoice.invoice_no}</td>
                            <td>${invoice.customer_name}</td>
                            <td class="text-end">${parseFloat(invoice.grand_total).toFixed(2)}</td>
                            <td class="text-end">${parseFloat(invoice.wheel_balancer_commission || 0).toFixed(2)}</td>
                            <td>
                                <a href="sales-invoice-view.php?invoice_id=${invoice.id
                            }" target="_blank" class="btn btn-sm btn-info">
                                    <i class="uil uil-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    });

                    const tableHtml = `
                    <tr class="expanded-row bg-light">
                        <td colspan="7">
                            <div class="p-3">
                                <h6 class="mb-3">Invoices for this Wheel Balancer</h6>
                                <table class="table table-sm table-bordered bg-white">
                                    <thead>
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
                                        ${rows}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                `;
                    tr.after(tableHtml);
                } else {
                    tr.after(
                        `<tr class="expanded-row bg-light"><td colspan="7" class="text-center p-3 text-muted">No invoices found for the selected period.</td></tr>`
                    );
                }
            },
            error: function () {
                tr.next().remove();
                tr.after(
                    `<tr class="expanded-row bg-light"><td colspan="7" class="text-center p-3 text-danger">Error loading data.</td></tr>`
                );
            },
        });
    });
    function loadTotals() {
        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();

        // Show loading state (optional, or just wait for update)
        $(".total-amount").text("...");
        $(".total-commission").text("...");

        $.ajax({
            url: "ajax/php/wheel-balancer-report.php",
            type: "POST",
            data: {
                action: "get_totals",
                from_date: fromDate,
                to_date: toDate
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    // Reset first
                    $(".total-amount").text("0.00");
                    $(".total-commission").text("0.00");

                    // Update with data
                    $.each(response.data, function (id, totals) {
                        $(`.total-amount[data-id="${id}"]`).text(parseFloat(totals.total_amount || 0).toFixed(2));
                        $(`.total-commission[data-id="${id}"]`).text(parseFloat(totals.total_commission || 0).toFixed(2));
                    });
                }
            }
        });
    }

    // Load totals on start
    loadTotals();

    // Reload totals when date range changes
    $("#from_date, #to_date").change(function () {
        loadTotals();
    });

});
