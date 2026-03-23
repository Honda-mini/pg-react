<?php
require_once('../../src/utils/pg_services.php');
session_start();

// Recursive folder delete function (you already have this)
function deleteDir($dirPath) {
    if (!is_dir($dirPath)) return false;

    $items = scandir($dirPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $dirPath . DIRECTORY_SEPARATOR . $item;

        if (is_dir($fullPath)) {
            deleteDir($fullPath);
        } else {
            unlink($fullPath);
        }
    }

    return rmdir($dirPath);
}

// Main deletion logic
if (isset($_GET['stockID']) && is_numeric($_GET['stockID'])) {
    $stockID = (int) $_GET['stockID'];

    // Attempt image directory deletion
    $dir = "../../public/images/cars/$stockID";
    $imagesDeleted = deleteDir($dir);

    // Delete DB record
    $stmt = $pg_services->prepare("DELETE FROM stock WHERE stockID = ?");
    $stmt->bind_param("i", $stockID);
    $stmt->execute();
    $dbDeleted = $stmt->affected_rows > 0;
    $stmt->close();

    // Set success/failure message
    if ($dbDeleted) {
        $_SESSION['uploadMessage'] = "Vehicle #$stockID deleted successfully.";
        $_SESSION['uploadMessageType'] = 'success';
    } else {
        $_SESSION['uploadMessage'] = "Failed to delete vehicle #$stockID.";
        $_SESSION['uploadMessageType'] = 'error';
    }

    header("Location: ../manage_stock.php");
    exit();
} else {
    $_SESSION['uploadMessage'] = "Invalid vehicle ID.";
    $_SESSION['uploadMessageType'] = 'error';
    header("Location: ../manage_stock.php");
    exit();
}
?>
