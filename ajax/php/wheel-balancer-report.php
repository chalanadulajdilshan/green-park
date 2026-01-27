<?php
include '../../class/include.php';

header('Content-Type: application/json');

if (isset($_POST['action']) && $_POST['action'] == 'get_invoices') {

    $wheelBalancerId = isset($_POST['id']) ? $_POST['id'] : '';
    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($wheelBalancerId) || empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $invoices = $SALES_INVOICE->getInvoicesByWheelBalancer($wheelBalancerId, $fromDate, $toDate);

    echo json_encode(['status' => 'success', 'data' => $invoices]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'get_totals') {

    $fromDate = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $toDate = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    if (empty($fromDate) || empty($toDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing date range']);
        exit;
    }

    $SALES_INVOICE = new SalesInvoice(NULL);
    $totals = $SALES_INVOICE->getWheelBalancerTotals($fromDate, $toDate);

    echo json_encode(['status' => 'success', 'data' => $totals]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
exit;
