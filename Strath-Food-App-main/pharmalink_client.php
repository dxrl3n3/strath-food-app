<?php
const PHARMALINK_USE_MOCK = true;

const PHARMALINK_BASE_URL = 'http://localhost/pharmalink/api';

const PHARMALINK_TIMEOUT = 5;

function pl_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function pl_pick($row, $keys, $default = '') {
    if (!is_array($row)) {
        return $default;
    }
    foreach ($keys as $k) {
        if (isset($row[$k]) && $row[$k] !== '') {
            return $row[$k];
        }
    }
    return $default;
}

function pl_list($data) {
    if (!is_array($data)) {
        return [];
    }
    foreach (['data', 'items', 'results'] as $k) {
        if (isset($data[$k]) && is_array($data[$k])) {
            return $data[$k];
        }
    }
    return $data;
}

function pl_one($data) {
    if (!is_array($data)) {
        return [];
    }
    if (isset($data['data']) && is_array($data['data'])) {
        return $data['data'];
    }
    return $data;
}

function pl_current_user_id() {
    foreach (['user_id', 'id', 'userId', 'userid'] as $k) {
        if (!empty($_SESSION[$k])) {
            return $_SESSION[$k];
        }
    }
    return null;
}

function pl_csrf_token() {
    if (empty($_SESSION['pl_csrf'])) {
        $_SESSION['pl_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['pl_csrf'];
}

function pl_result($status, $data, $error = '') {
    return [
        'ok'     => ($status >= 200 && $status < 300),
        'status' => $status,
        'data'   => $data,
        'error'  => $error,
    ];
}

function pharmalink_request($method, $path, $body = null) {
    if (PHARMALINK_USE_MOCK) {
        return pl_mock_request($method, $path, $body);
    }

    if (!function_exists('curl_init')) {
        return pl_result(0, null, 'The PHP curl extension is not enabled.');
    }

    $ch = curl_init(rtrim(PHARMALINK_BASE_URL, '/') . $path);
    $headers = ['Accept: application/json'];
    $options = [
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => PHARMALINK_TIMEOUT,
    ];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        $options[CURLOPT_POSTFIELDS] = json_encode($body);
    }
    $options[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $options);

    $raw    = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err    = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        return pl_result(0, null, 'Could not reach PharmaLink: ' . $err);
    }

    $data = json_decode($raw, true);
    $result = pl_result($status, $data);
    if (!$result['ok']) {
        $msg = pl_pick($data, ['error', 'message'], 'PharmaLink returned status ' . $status . '.');
        $result['error'] = is_string($msg) ? $msg : 'PharmaLink returned status ' . $status . '.';
    }
    return $result;
}

function pl_mock_request($method, $path, $body) {
    $method = strtoupper($method);

    $medicines = [
        ['id' => 1, 'name' => 'Paracetamol 500mg'],
        ['id' => 2, 'name' => 'Amoxicillin 250mg'],
        ['id' => 3, 'name' => 'Ibuprofen 400mg'],
    ];

    $pharmacies = [
        ['id' => 1, 'name' => 'Campus Pharmacy',     'address' => 'Ole Sangale Road, Nairobi', 'phone' => '+254700000001', 'latitude' => -1.3101, 'longitude' => 36.8125],
        ['id' => 2, 'name' => 'Madaraka Chemist',    'address' => 'Madaraka Estate, Nairobi',  'phone' => '+254700000002', 'latitude' => -1.3050, 'longitude' => 36.8200],
        ['id' => 3, 'name' => 'Langata Road Pharmacy', 'address' => 'Langata Road, Nairobi',   'phone' => '+254700000003', 'latitude' => -1.3200, 'longitude' => 36.8050],
    ];

    $inventory = [
        ['id' => 1, 'medicineId' => 1, 'pharmacyId' => 1, 'pharmacyName' => 'Campus Pharmacy',       'price' => 50,  'quantity' => 20],
        ['id' => 2, 'medicineId' => 1, 'pharmacyId' => 2, 'pharmacyName' => 'Madaraka Chemist',      'price' => 40,  'quantity' => 5],
        ['id' => 3, 'medicineId' => 1, 'pharmacyId' => 3, 'pharmacyName' => 'Langata Road Pharmacy', 'price' => 65,  'quantity' => 0],
        ['id' => 4, 'medicineId' => 2, 'pharmacyId' => 1, 'pharmacyName' => 'Campus Pharmacy',       'price' => 300, 'quantity' => 8],
        ['id' => 5, 'medicineId' => 3, 'pharmacyId' => 2, 'pharmacyName' => 'Madaraka Chemist',      'price' => 120, 'quantity' => 12],
    ];

    $parts = parse_url($path);
    $query = [];
    parse_str(isset($parts['query']) ? $parts['query'] : '', $query);
    $route    = trim(isset($parts['path']) ? $parts['path'] : '', '/');
    $segments = ($route === '') ? [] : explode('/', $route);
    $resource = isset($segments[0]) ? $segments[0] : '';
    $id       = isset($segments[1]) ? $segments[1] : null;

    $findById = function ($list, $wanted) {
        foreach ($list as $row) {
            if ((string)$row['id'] === (string)$wanted) {
                return $row;
            }
        }
        return null;
    };

    if ($method === 'GET' && $resource === 'medicines') {
        if ($id !== null) {
            $row = $findById($medicines, $id);
            return $row ? pl_result(200, $row) : pl_result(404, null, 'Medicine not found.');
        }
        $name = isset($query['name']) ? trim($query['name']) : '';
        $out = [];
        foreach ($medicines as $m) {
            if ($name === '' || stripos($m['name'], $name) !== false) {
                $out[] = $m;
            }
        }
        return pl_result(200, $out);
    }

    if ($method === 'GET' && $resource === 'inventory') {
        if ($id !== null) {
            $row = $findById($inventory, $id);
            return $row ? pl_result(200, $row) : pl_result(404, null, 'Inventory record not found.');
        }
        $out = [];
        foreach ($inventory as $row) {
            if (!isset($query['medicineId']) || (string)$row['medicineId'] === (string)$query['medicineId']) {
                $out[] = $row;
            }
        }
        if (isset($query['sortBy']) && $query['sortBy'] === 'price') {
            usort($out, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });
        }
        return pl_result(200, $out);
    }

    if ($method === 'GET' && $resource === 'pharmacies') {
        if ($id !== null) {
            $row = $findById($pharmacies, $id);
            return $row ? pl_result(200, $row) : pl_result(404, null, 'Pharmacy not found.');
        }
        return pl_result(200, $pharmacies);
    }

    if ($method === 'POST' && $resource === 'reservations') {
        if (!is_array($body) || empty($body['inventoryId'])) {
            return pl_result(400, null, 'inventoryId is required.');
        }
        return pl_result(201, ['id' => rand(1000, 9999), 'status' => 'reserved']);
    }

    return pl_result(404, null, 'Unknown sample endpoint: ' . $method . ' ' . $path);
}
