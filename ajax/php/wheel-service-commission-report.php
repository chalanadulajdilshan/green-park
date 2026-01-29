<?php
include '../../class/include.php';

header('Content-Type: application/json');

if (isset($_POST['action']) && $_POST['action'] == 'get_totals') {

    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing date range']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $total = $SALES_INVOICE->getWheelServiceCommissionTotals($fromDate, $toDate);

    echo json_encode(['status' => 'success', 'total_commission' => $total]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'get_invoices') {

    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing date range']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $invoices = $SALES_INVOICE->getInvoicesWithServiceCommission($fromDate, $toDate);

    echo json_encode(['status' => 'success', 'data' => $invoices]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;
