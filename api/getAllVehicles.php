<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../Connections/pg_services.php';
require_once __DIR__ . '/../content/helpers.php';

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

    // Clean yearPlate
    $year = trim((string)($car['yearPlate'] ?? ''));
    if (strpos($year, '/') !== false) {
        $year = substr($year, 0, strpos($year, '/'));
    }

    // Build name
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

    // Price as integer
    $car['price'] = (int)$car['price'];

    // Convert flags to booleans
    $car['sold'] = $car['sold'] == 1;
    $car['featured'] = $car['featured'] == 1;
    $car['reserved'] = $car['reserved'] == 1;

    // Build first image path
    
    $folder = __DIR__ . "/../public/images/cars/" . $car['stockID'] . "/thumbs/";
    $publicPath = "/images/cars/" . $car['stockID'] . "/thumbs/";
    $extensions = ['jpg', 'jpeg', 'png', 'gif'];

    $image = "/images/no-image.svg";
// 🔍 DEBUG START — this will NOT break anything
error_log("---- DEBUG FOR STOCKID {$car['stockID']} ----");
error_log("Folder path: $folder");
error_log("Folder exists? " . (is_dir($folder) ? "YES" : "NO"));
error_log("Folder readable? " . (is_readable($folder) ? "YES" : "NO"));
error_log("Files: " . json_encode(@scandir($folder)));
// 🔍 DEBUG END
    if (is_dir($folder)) {
        foreach (scandir($folder) as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $image = $publicPath . $file;
                break;
            }
        }
    }

    $car['image'] = $image;

    $vehicles[] = $car;
}

echo json_encode($vehicles);
