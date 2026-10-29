<?php
$pl_title = 'Pharmacy Details';
require_once __DIR__ . '/pharmalink_header.php';

$id       = isset($_GET['id']) ? trim($_GET['id']) : '';
$pharmacy = [];
$error    = '';

if ($id === '') {
    $error = 'No pharmacy selected.';
} else {
    $res = pharmalink_request('GET', '/pharmacies/' . rawurlencode($id));
    if ($res['ok']) {
        $pharmacy = pl_one($res['data']);
    } else {
        $error = $res['error'] !== '' ? $res['error'] : 'Could not load this pharmacy right now.';
    }
}

$name    = pl_pick($pharmacy, ['name', 'pharmacyName', 'pharmacy_name'], 'Pharmacy');
$address = pl_pick($pharmacy, ['address', 'location']);
$phone   = pl_pick($pharmacy, ['phone', 'phoneNumber', 'phone_number', 'contact']);
$lat     = pl_pick($pharmacy, ['latitude', 'lat']);
$lng     = pl_pick($pharmacy, ['longitude', 'lng', 'lon']);

$phoneClean = preg_replace('/[^0-9+]/', '', (string)$phone);

$directionsUrl = '';
if (is_numeric($lat) && is_numeric($lng)) {
    $directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($lat . ',' . $lng);
} elseif ($address !== '') {
    $directionsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode((string)$address);
}
?>
<h2>Pharmacy Details</h2>

<?php if ($error !== ''): ?>
    <div class="pl-msg pl-error"><?= pl_h($error) ?></div>
    <p><a href="find_medicine.php">Back to Find Medicine</a></p>
<?php else: ?>
    <h3><?= pl_h($name) ?></h3>
    <p><strong>Address:</strong> <?= $address !== '' ? pl_h($address) : 'Not available' ?></p>
    <p><strong>Phone:</strong> <?= $phone !== '' ? pl_h($phone) : 'Not available' ?></p>

    <div class="pl-actions">
        <?php if ($phoneClean !== ''): ?>
            <a class="pl-btn" href="tel:<?= pl_h($phoneClean) ?>">Call</a>
        <?php endif; ?>
        <?php if ($directionsUrl !== ''): ?>
            <a class="pl-btn" href="<?= pl_h($directionsUrl) ?>" target="_blank" rel="noopener noreferrer">Directions</a>
        <?php endif; ?>
        <a class="pl-btn" href="pharmacies_map.php">Back to map</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/pharmalink_footer.php'; ?>
