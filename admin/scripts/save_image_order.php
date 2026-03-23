<?php
// scripts/save_image_order.php

// Get POSTed JSON data
$data = json_decode(file_get_contents('php://input'), true);
$stockID = intval($data['stockID'] ?? 0);
$order = $data['order'] ?? [];

if (!$stockID || !is_array($order)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid data']);
    exit;
}

$orderFile = "../../images/cars/$stockID/order.json";
if (file_put_contents($orderFile, json_encode($order))) {
    echo json_encode(['status' => 'success', 'msg' => 'Order saved!']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Failed to save order']);
}
?>