<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$stockID = intval($data['stockID'] ?? 0);
$filename = $data['filename'] ?? '';

if (!$stockID || !$filename) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'msg' => 'Invalid data']);
  exit;
}

$dir = dirname(__DIR__, 2) . "/images/cars/{$stockID}/";
$orderFile = $dir . "order.json";

if (!is_dir($dir)) {
  http_response_code(404);
  echo json_encode(['status' => 'error', 'msg' => 'Image folder not found']);
  exit;
}

$order = [];
if (file_exists($orderFile)) {
  $decoded = json_decode(file_get_contents($orderFile), true);
  if (is_array($decoded)) {
    $order = $decoded;
  }
}

// Ensure filename is in order; if not, add it
$order = array_values(array_unique($order));
if (!in_array($filename, $order)) {
  $order[] = $filename;
}

// Move filename to front
$order = array_values(array_filter($order, fn($f) => $f !== $filename));
array_unshift($order, $filename);

file_put_contents($orderFile, json_encode($order, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success', 'msg' => 'Primary image updated']);
