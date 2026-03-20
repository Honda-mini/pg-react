<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../Connections/pg_services.php';
require_once __DIR__ . '/../content/helpers.php';

// Validate ID
$stockID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($stockID <= 0) {
    echo json_encode(["error" => "Invalid vehicle ID"]);
    exit;
}

// Fetch full vehicle row
$stmt = $pg_services->prepare("
    SELECT *
    FROM stock
    WHERE stockID = ?
");
$stmt->bind_param("i", $stockID);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    echo json_encode(["error" => "Vehicle not found"]);
    exit;
}

// Clean yearPlate (remove anything after "/")
$year = trim((string)($car['yearPlate'] ?? ''));
if (strpos($year, '/') !== false) {
    $year = substr($year, 0, strpos($year, '/'));
}

// Build clean title
$nameParts = array_filter([
    $year,
    $car['make'],
    $car['model'],
    $car['trim'],
    $car['additional']
]);
$car['name'] = ucwords(strtolower(trim(implode(' ', $nameParts))));

// Format mileage
if (!empty($car['mileage'])) {
    $car['mileage'] = number_format((int)$car['mileage']) . " mi";
} else {
    $car['mileage'] = "";
}

// Price stays raw integer — React handles formatting
$car['price'] = (int)$car['price'];

// Build image array
$folder = __DIR__ . "/../public/images/cars/" . $stockID . "/";
$publicPath = "/images/cars/" . $stockID . "/";
$images = [];
$extensions = ['jpg', 'jpeg', 'png', 'gif'];

// Use order.json if present
$orderFile = $folder . "order.json";
if (file_exists($orderFile)) {
    $ordered = json_decode(file_get_contents($orderFile), true);
    if (is_array($ordered)) {
        foreach ($ordered as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions) && file_exists($folder . $file)) {
                $images[] = $publicPath . $file;
            }
        }
    }
}

// Fallback: alphabetical scan
if (empty($images) && is_dir($folder)) {
    foreach (scandir($folder) as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions) && is_file($folder . $file)) {
            $images[] = $publicPath . $file;
        }
    }
}

// Attach images to response
$car['images'] = $images;

// Output JSON
echo json_encode($car);
