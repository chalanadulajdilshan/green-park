<?php
include '../../class/include.php';

header('Content-Type: application/json');

if (isset($_POST['action']) && $_POST['action'] == 'get_sales') {

    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing date range']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $salesData = $SALES_INVOICE->getBrandWiseSales($fromDate, $toDate);

    $totalSales = 0;
    foreach ($salesData as $row) {
        $totalSales += $row['total_sales'];
    }

    $responseData = [];
    foreach ($salesData as $row) {
        $percentage = ($totalSales > 0) ? ($row['total_sales'] / $totalSales) * 100 : 0;
        $row['percentage'] = number_format($percentage, 2);
        $responseData[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $responseData, 'grand_total' => $totalSales]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'get_brand_invoices') {

    $brandId = isset($_POST['brand_id']) ? $_POST['brand_id'] : '';
    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($brandId) || empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $invoices = $SALES_INVOICE->getInvoicesByBrand($brandId, $fromDate, $toDate);

    echo json_encode(['status' => 'success', 'data' => $invoices]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;
