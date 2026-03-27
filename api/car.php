<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../Connections/pg_services.php';


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

// Build WebP-only image array
$folder = __DIR__ . "/../public/images/cars/" . $stockID . "/";
$publicPath = "/images/cars/" . $stockID . "/";
$images = [];

// Only process if folder exists
if (is_dir($folder)) {

    // Scan for full-size WebP images only (1.webp, 2.webp, 3.webp...)
    foreach (scandir($folder) as $file) {
        if (preg_match('/^(\d+)\.webp$/', $file, $match)) {
            $images[(int)$match[1]] = $publicPath . $file;
        }
    }

    // Sort numerically by image number
    ksort($images);

    // Re-index to a clean array
    $images = array_values($images);
}

// Fallback if no images found
if (empty($images)) {
    $images[] = "/../public/images/no-image.svg";
}

$car['images'] = $images;

// Output JSON
echo json_encode($car);
