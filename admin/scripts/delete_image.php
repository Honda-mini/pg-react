<?php
header('Content-Type: application/json');

$stockID  = $_POST['stockID']  ?? '';
$filename = $_POST['filename'] ?? '';

$response = ['status' => 'error', 'msg' => 'Invalid request.'];

if (!$stockID || !$filename) {
    $response['msg'] = 'Missing stock ID or filename.';
    echo json_encode($response);
    exit;
}

$dir = "../../public/images/cars/{$stockID}/";
$origDir = $dir . "orig/";

$mainFile = $dir . $filename;
$baseName = pathinfo($filename, PATHINFO_FILENAME);
$ext      = 'webp';

$variants = [
    $mainFile,
    $dir . "{$baseName}_800.{$ext}",
    $dir . "{$baseName}_400.{$ext}",
];

// original: orig/{n}_orig.*
if (is_dir($origDir)) {
    $origPattern = $origDir . "{$baseName}_orig.*";
    foreach (glob($origPattern) as $origFile) {
        $variants[] = $origFile;
    }
}

$deletedAny = false;

foreach ($variants as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            $deletedAny = true;
        }
    }
}

if (!$deletedAny) {
    $response['msg'] = 'No matching files found to delete.';
    echo json_encode($response);
    exit;
}

$orderFile = $dir . "order.json";
if (file_exists($orderFile)) {
    $order = json_decode(file_get_contents($orderFile), true);
    if (is_array($order)) {
        $order = array_values(array_filter($order, fn($img) => $img !== $filename));
        file_put_contents($orderFile, json_encode($order, JSON_PRETTY_PRINT));
    }
}

// Clean up orig/ if empty
if (is_dir($origDir)) {
    $remainingOrig = array_diff(scandir($origDir), ['.', '..']);
    if (empty($remainingOrig)) {
        rmdir($origDir);
    }
}

// Clean up main folder if empty
$remaining = array_diff(scandir($dir), ['.', '..']);
if (empty($remaining)) {
    rmdir($dir);
}

$response['status'] = 'success';
$response['msg']    = 'Image deleted successfully.';

echo json_encode($response);
