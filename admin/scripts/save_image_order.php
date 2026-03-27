<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$stockID = intval($data['stockID'] ?? 0);
$order   = $data['order'] ?? [];

if (!$stockID || !is_array($order)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid data']);
    exit;
}

$dir = "../../images/cars/{$stockID}/";
$orderFile = $dir . "order.json";

if (!is_dir($dir)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Image folder not found']);
    exit;
}

if (file_put_contents($orderFile, json_encode($order, JSON_PRETTY_PRINT))) {
    echo json_encode(['status' => 'success', 'msg' => 'Order saved!']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Failed to save order']);
}
