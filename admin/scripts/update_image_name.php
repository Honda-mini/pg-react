<?php
header('Content-Type: application/json');
require_once('../../Connections/pg_services.php');

$stockID = $_POST['stockID'] ?? '';
$oldname = $_POST['oldname'] ?? '';
$newname = $_POST['newname'] ?? '';

$uploadDir = "../../images/cars/{$stockID}/";
$oldPath = $uploadDir . $oldname;
$newPath = $uploadDir . $newname;

$response = ['status' => 'error', 'msg' => 'Unknown error occurred.'];

if (!$stockID || !$oldname || !$newname) {
  $response['msg'] = 'Missing data in request.';
} elseif (!file_exists($oldPath)) {
  $response['msg'] = 'Original image does not exist.';
} elseif (file_exists($newPath)) {
  $response['msg'] = 'A file with that name already exists.';
} else {
  // Preserve extension
  $ext = pathinfo($oldname, PATHINFO_EXTENSION);
  $newFullPath = $uploadDir . $newname . '.' . $ext;

  if (rename($oldPath, $newFullPath)) {
    $response['status'] = 'success';
    $response['msg'] = 'Image renamed successfully.';
  } else {
    $response['msg'] = 'Failed to rename image.';
  }
}

echo json_encode($response);
