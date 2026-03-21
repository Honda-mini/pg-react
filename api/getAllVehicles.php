<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once dirname(__DIR__) . '/src/utils/pg_services.php';
require_once dirname(__DIR__) . '/src/utils/helpers.php';
// Fetch ALL vehicles
$stmt = $pg_services->prepare("
    SELECT *
    FROM stock
    ORDER BY stockID DESC
");
$stmt->execute();
$result = $stmt->get_result();

$vehicles = [];

while ($car = $result->fetch_assoc()) {

    // --- Clean yearPlate ---
    $year = trim((string)($car['yearPlate'] ?? ''));
    if (strpos($year, '/') !== false) {
        $year = substr($year, 0, strpos($year, '/'));
    }

    // --- Build name ---
    $nameParts = array_filter([
        $year,
        $car['make'],
        $car['model'],
        $car['trim'],
        $car['additional']
    ]);
    $car['name'] = ucwords(strtolower(trim(implode(' ', $nameParts))));

    // --- Format mileage ---
    if (!empty($car['mileage'])) {
        $car['mileage'] = number_format((int)$car['mileage']) . " mi";
    } else {
        $car['mileage'] = "";
    }

    // --- Price as integer ---
    $car['price'] = (int)$car['price'];

    // --- Convert flags to booleans ---
    $car['sold'] = $car['sold'] == 1;
    $car['featured'] = $car['featured'] == 1;
    $car['reserved'] = $car['reserved'] == 1;

    // --- Image handling ---
    $folder = __DIR__ . "/../public/images/cars/" . $car['stockID'] . "/";
    $publicPath = "/images/cars/" . $car['stockID'] . "/";

    // Default to no-image
    $thumb400 = "/images/no-image.svg";
    $thumb800 = "/images/no-image.svg";
    $full = "/images/no-image.svg";
    $hasImage = false;

    // Check if 400px thumbnail exists
    if (file_exists($folder . "1_400.webp")) {
        $thumb400 = $publicPath . "1_400.webp";
        $thumb800 = $publicPath . "1_800.webp"; // assume 800px exists
        $full = $publicPath . "1.webp";        // full-size image
        $hasImage = true;
    }

    $car['images'] = [
        'thumb400' => $thumb400,
        'thumb800' => $thumb800,
        'full' => $full,
        'hasImage' => $hasImage
    ];

    $vehicles[] = $car;
}

echo json_encode($vehicles, JSON_UNESCAPED_SLASHES);