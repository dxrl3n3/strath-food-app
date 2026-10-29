<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/pharmalink_client.php';

function pl_back($type, $message, $medicineId = '') {
    $_SESSION['pl_flash'] = ['type' => $type, 'msg' => $message];
    $url = 'find_medicine.php';
    if ($medicineId !== '') {
        $url .= '?medicineId=' . rawurlencode($medicineId);
    }
    header('Location: ' . $url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: find_medicine.php');
    exit;
}

$medicineId  = isset($_POST['medicine_id']) ? trim($_POST['medicine_id']) : '';
$inventoryId = isset($_POST['inventory_id']) ? trim($_POST['inventory_id']) : '';
$pharmacyId  = isset($_POST['pharmacy_id']) ? trim($_POST['pharmacy_id']) : '';
$quantity    = filter_var(isset($_POST['quantity']) ? $_POST['quantity'] : '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$token       = isset($_POST['csrf']) ? $_POST['csrf'] : '';

if (empty($_SESSION['pl_csrf']) || !hash_equals($_SESSION['pl_csrf'], $token)) {
    pl_back('error', 'Your session expired. Please try again.', $medicineId);
}

$userId = pl_current_user_id();
if ($userId === null) {
    header('Location: login.php');
    exit;
}

if ($inventoryId === '' || $quantity === false) {
    pl_back('error', 'Please choose a valid quantity.', $medicineId);
}

$check = pharmalink_request('GET', '/inventory/' . rawurlencode($inventoryId));
if (!$check['ok']) {
    $msg = $check['error'] !== '' ? $check['error'] : 'Could not confirm stock right now.';
    pl_back('error', $msg, $medicineId);
}
$record    = pl_one($check['data']);
$available = (int)pl_pick($record, ['quantity', 'stock', 'availableQuantity'], 0);
if ($available < $quantity) {
    pl_back('error', 'Not enough stock. Only ' . $available . ' available.', $medicineId);
}

$pharmacyId = (string)pl_pick($record, ['pharmacyId', 'pharmacy_id'], $pharmacyId);
$medicineId = (string)pl_pick($record, ['medicineId', 'medicine_id'], $medicineId);

$payload = [
    'medicineId'  => $medicineId,
    'pharmacyId'  => $pharmacyId,
    'inventoryId' => $inventoryId,
    'quantity'    => $quantity,
    'patientId'   => $userId,
];
$res = pharmalink_request('POST', '/reservations', $payload);

if ($res['ok']) {
    $created = pl_one($res['data']);
    $resId   = pl_pick($created, ['id', 'reservationId', 'reservation_id'], '');
    $text    = 'Reservation created.' . ($resId !== '' ? ' Your reservation number is ' . $resId . '.' : '') . ' Please collect it in person at the pharmacy.';
    pl_back('success', $text, $medicineId);
}

$msg = $res['error'] !== '' ? $res['error'] : 'The reservation could not be created.';
pl_back('error', $msg, $medicineId);
