<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../Connections/pg_services.php';


$query = "SELECT stockID, make, model, trim, additional, yearPlate, mileage,  price, dateAdded, featured, reserved, sold
          FROM stock 
          ORDER BY stockID DESC limit 12";

$result = $pg_services->query($query);

$cars = [];

while ($row = $result->fetch_assoc()) {

// Extract clean year (remove anything after "/")
$year = trim((string)($row['yearPlate'] ?? ''));
if (strpos($year, '/') !== false) {
    $year = substr($year, 0, strpos($year, '/'));
}

// Build clean title parts
$nameParts = array_filter([
    $year,
    $row['make'],
    $row['model'],
    $row['trim'],
    $row['additional']
]);

// Normalise casing and spacing
$row['name'] = ucwords(strtolower(trim(implode(' ', $nameParts))));


// Handle NULL mileage
if (!empty($row['mileage'])) {
    $row['mileage'] = number_format((int)$row['mileage']) . " mi";
} else {
    $row['mileage'] = "";
}

// price format
$rawPrice = $row['price'];
$isNumeric = is_numeric($rawPrice);

if ($isNumeric) {
    // Format numeric price (e.g., £12,995)
   $row['price'] = "£" . number_format((int)$rawPrice);
} else {
    // Normalise status text
    $status = strtolower(trim($rawPrice));

    $labels = [
        'poa' => 'Price on Application',
        'deposit taken' => 'Deposit Taken',
        'sold' => 'Sorry, Sold',
    ];

    // Use mapped label or fallback to capitalised text
    $row['price'] = $labels[$status] ?? ucwords($status);
}

    // Build image path
    $row['image'] = "/images/cars/" . $row['stockID'] . "/1_400.webp";

    $cars[] = $row;
}

echo json_encode($cars);
?>