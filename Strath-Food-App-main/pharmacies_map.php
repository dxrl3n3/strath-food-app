<?php
$pl_title = 'Pharmacy Map';
$pl_head_extra = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
require_once __DIR__ . '/pharmalink_header.php';

$pharmacies = [];
$markers    = [];
$error      = '';

$res = pharmalink_request('GET', '/pharmacies');
if ($res['ok']) {
    $pharmacies = pl_list($res['data']);
    foreach ($pharmacies as $p) {
        $lat = pl_pick($p, ['latitude', 'lat']);
        $lng = pl_pick($p, ['longitude', 'lng', 'lon']);
        if (is_numeric($lat) && is_numeric($lng)) {
            $markers[] = [
                'id'      => (string)pl_pick($p, ['id', 'pharmacyId', 'pharmacy_id']),
                'name'    => (string)pl_pick($p, ['name', 'pharmacyName', 'pharmacy_name'], 'Pharmacy'),
                'address' => (string)pl_pick($p, ['address', 'location']),
                'lat'     => (float)$lat,
                'lng'     => (float)$lng,
            ];
        }
    }
} else {
    $error = $res['error'] !== '' ? $res['error'] : 'Could not load pharmacies right now.';
}
?>
<h2>Pharmacy Map</h2>

<?php if ($error !== ''): ?>
    <div class="pl-msg pl-error"><?= pl_h($error) ?></div>
<?php elseif (count($markers) === 0): ?>
    <p>No pharmacies with map coordinates were found.</p>
<?php else: ?>
    <div id="pl-map" class="pl-map"></div>

    <h3>Pharmacies</h3>
    <ul>
        <?php foreach ($markers as $m): ?>
            <li>
                <?= pl_h($m['name']) ?>
                <?php if ($m['id'] !== ''): ?>
                    - <a href="pharmacy_details.php?id=<?= rawurlencode($m['id']) ?>">Call / Directions</a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var pharmacies = <?= json_encode($markers, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        var map = L.map('pl-map').setView([pharmacies[0].lat, pharmacies[0].lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var bounds = [];
        pharmacies.forEach(function (p) {
            var box = document.createElement('div');

            var title = document.createElement('strong');
            title.textContent = p.name;
            box.appendChild(title);

            if (p.address) {
                box.appendChild(document.createElement('br'));
                box.appendChild(document.createTextNode(p.address));
            }
            if (p.id) {
                box.appendChild(document.createElement('br'));
                var link = document.createElement('a');
                link.href = 'pharmacy_details.php?id=' + encodeURIComponent(p.id);
                link.textContent = 'Call / Directions';
                box.appendChild(link);
            }

            L.marker([p.lat, p.lng]).addTo(map).bindPopup(box);
            bounds.push([p.lat, p.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/pharmalink_footer.php'; ?>
