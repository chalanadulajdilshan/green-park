$(document).ready(function () {

    function loadSalesData() {
        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();

        $("#reportTable tbody").html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $("#grandTotal").text("...");

        $.ajax({
            url: "ajax/php/brand-sales-report.php",
            type: "POST",
            data: {
                action: "get_sales",
                from_date: fromDate,
                to_date: toDate
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let rows = "";
                    if (response.data.length > 0) {
                        response.data.forEach((item, index) => {
                            rows += `
                                <tr data-id="${item.brand_id}">
                                    <td>${index + 1}</td>
                                    <td>${item.brand_name}</td>
                                    <td class="text-end">${parseFloat(item.total_sales).toFixed(2)}</td>
                                    <td class="text-end">${item.percentage}%</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-expand" title="View Invoices">
                                            <i class="uil uil-plus"></i> View
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $("#grandTotal").text(parseFloat(response.grand_total).toFixed(2));
                    } else {
                        rows = '<tr><td colspan="5" class="text-center text-muted">No sales found for this period.</td></tr>';
                        $("#grandTotal").text("0.00");
                    }
                    $("#reportTable tbody").html(rows);
                } else {
                    $("#reportTable tbody").html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
                }
            },
            error: function () {
                $("#reportTable tbody").html('<tr><td colspan="5" class="text-center text-danger">Server error.</td></tr>');
            }
        });
    }

    // Load data on start
    loadSalesData();

    // Reload on date change
    $("#from_date, #to_date").change(function () {
        loadSalesData();
    });

    // Handle Expand Click
    $(document).on("click", ".btn-expand", function () {
        const btn = $(this);
        const tr = btn.closest("tr");
        const brandId = tr.data("id");

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
        const loadingRow = `<tr class="expanded-row"><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
        tr.after(loadingRow);

        $.ajax({
            url: "ajax/php/brand-sales-report.php",
            type: "POST",
            data: {
                action: "get_brand_invoices",
                brand_id: brandId,
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
                            <td class="text-end">${parseFloat(invoice.brand_total).toFixed(2)}</td>
                            <td>
                                <a href="sales-invoice-view.php?invoice_id=${invoice.id}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="uil uil-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    });

                    const tableHtml = `
                    <tr class="expanded-row bg-light">
                        <td colspan="5">
                            <div class="p-3">
                                <h6 class="mb-3">Invoices for this Brand</h6>
                                <table class="table table-sm table-bordered bg-white">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>Customer</th>
                                            <th class="text-end">Sales Amount</th>
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
                        `<tr class="expanded-row bg-light"><td colspan="5" class="text-center p-3 text-muted">No invoices found for this brand in the selected period.</td></tr>`
                    );
                }
            },
            error: function () {
                tr.next().remove();
                tr.after(
                    `<tr class="expanded-row bg-light"><td colspan="5" class="text-center p-3 text-danger">Error loading data.</td></tr>`
                );
            },
        });
    });

});
