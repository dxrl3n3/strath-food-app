<?php
$pl_title = 'Find Medicine';
require_once __DIR__ . '/pharmalink_header.php';

$q          = isset($_GET['q']) ? trim($_GET['q']) : '';
$medicineId = isset($_GET['medicineId']) ? trim($_GET['medicineId']) : '';
$medicines  = [];
$inventory  = [];
$error      = '';

if ($medicineId !== '') {
    $res = pharmalink_request('GET', '/inventory?medicineId=' . rawurlencode($medicineId) . '&sortBy=price');
    if ($res['ok']) {
        $inventory = pl_list($res['data']);

        usort($inventory, function ($a, $b) {
            return (float)pl_pick($a, ['price'], 0) <=> (float)pl_pick($b, ['price'], 0);
        });
    } else {
        $error = $res['error'] !== '' ? $res['error'] : 'Could not load stock right now.';
    }
} elseif ($q !== '') {
    $res = pharmalink_request('GET', '/medicines?name=' . rawurlencode($q));
    if ($res['ok']) {
        $medicines = pl_list($res['data']);
    } else {
        $error = $res['error'] !== '' ? $res['error'] : 'Could not search medicines right now.';
    }
}
?>
<h2>Find Medicine</h2>

<form method="GET" action="find_medicine.php">
    <label for="q">Medicine name</label>
    <input type="text" id="q" name="q" value="<?= pl_h($q) ?>" placeholder="e.g. Paracetamol" required>
    <button type="submit" class="pl-btn">Search</button>
</form>

<?php if ($error !== ''): ?>
    <div class="pl-msg pl-error"><?= pl_h($error) ?></div>
<?php endif; ?>

<?php if ($medicineId === '' && $q !== '' && $error === ''): ?>
    <?php if (count($medicines) === 0): ?>
        <p>No medicines found for "<?= pl_h($q) ?>".</p>
    <?php else: ?>
        <h3>Results</h3>
        <ul>
            <?php foreach ($medicines as $m): ?>
                <li>
                    <?= pl_h(pl_pick($m, ['name', 'medicineName', 'medicine_name'], 'Medicine')) ?>
                    - <a href="find_medicine.php?medicineId=<?= rawurlencode((string)pl_pick($m, ['id', 'medicineId', 'medicine_id'])) ?>">View prices and stock</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<?php if ($medicineId !== '' && $error === ''): ?>
    <h3>Pharmacies with this medicine (lowest price first)</h3>
    <p><a href="find_medicine.php">Back to search</a></p>
    <?php if (count($inventory) === 0): ?>
        <p>No pharmacies currently list this medicine.</p>
    <?php else: ?>
        <table class="pl-table">
            <thead>
                <tr>
                    <th>Pharmacy</th>
                    <th>Price (KES)</th>
                    <th>In stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($inventory as $row): ?>
                <?php
                $invId      = pl_pick($row, ['id', 'inventoryId', 'inventory_id']);
                $pharmacyId = pl_pick($row, ['pharmacyId', 'pharmacy_id']);
                $pharmName  = pl_pick($row, ['pharmacyName', 'pharmacy_name', 'name'], 'Pharmacy');
                $price      = pl_pick($row, ['price', 'unitPrice', 'unit_price'], '-');
                $qty        = (int)pl_pick($row, ['quantity', 'stock', 'availableQuantity'], 0);
                ?>
                <tr>
                    <td><?= pl_h($pharmName) ?></td>
                    <td><?= pl_h($price) ?></td>
                    <td><?= $qty > 0 ? pl_h($qty) : 'Out of stock' ?></td>
                    <td>
                        <?php if ($pharmacyId !== ''): ?>
                            <a class="pl-btn" href="pharmacy_details.php?id=<?= rawurlencode((string)$pharmacyId) ?>">Details</a>
                        <?php endif; ?>
                        <?php if ($qty > 0 && $invId !== ''): ?>
                            <form method="POST" action="reserve_medicine.php" style="display:inline-block;">
                                <input type="hidden" name="csrf" value="<?= pl_h(pl_csrf_token()) ?>">
                                <input type="hidden" name="inventory_id" value="<?= pl_h($invId) ?>">
                                <input type="hidden" name="medicine_id" value="<?= pl_h($medicineId) ?>">
                                <input type="hidden" name="pharmacy_id" value="<?= pl_h($pharmacyId) ?>">
                                <input type="number" class="pl-qty" name="quantity" value="1" min="1" max="<?= pl_h($qty) ?>" required>
                                <button type="submit" class="pl-btn">Reserve for pickup</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/pharmalink_footer.php'; ?>