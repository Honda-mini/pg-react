<?php
header('Content-Type: application/json');
require_once('../../src/utils/pg_services.php');

$stockID = $_POST['stockID'] ?? '';
$filename = $_POST['filename'] ?? '';
$response = ['status' => 'error', 'msg' => 'Invalid request.'];

$imagePath = "../../public/images/cars/{$stockID}/{$filename}";
$thumbPath = "../../public/images/cars/{$stockID}/thumbs/{$filename}";

if (!$stockID || !$filename) {
  $response['msg'] = 'Missing stock ID or filename.';
} elseif (!file_exists($imagePath)) {
  $response['msg'] = 'File not found.';
} elseif (unlink($imagePath)) {
  // Try to delete the thumbnail too (if it exists)
  if (file_exists($thumbPath)) {
    unlink($thumbPath);
  }
  $response['status'] = 'success';
  $response['msg'] = 'Image and thumbnail deleted successfully.';
} else {
  $response['msg'] = 'Error deleting the image.';
}

echo json_encode($response);