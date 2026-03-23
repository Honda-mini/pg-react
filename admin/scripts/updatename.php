<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stockID = intval($_POST['stockID']);
    $oldname = basename($_POST['oldname']); // sanitize filename
    $newname = basename($_POST['newname']); // sanitize filename

    // Ensure that the new filename doesn't contain unwanted characters
    if (preg_match('/[^a-zA-Z0-9_-]/', $newname)) {
        $_SESSION['uploadMessage'] = "Invalid file name: only letters, numbers, dashes, and underscores are allowed.";
        $_SESSION['uploadMessageType'] = 'error';
        header("Location: image_manager.php?stockID=$stockID");
        exit;
    }

    $dir = "../../images/cars/$stockID/"; // Updated path
    $oldPath = $dir . $oldname;
    $ext = pathinfo($oldname, PATHINFO_EXTENSION);
    $newPath = $dir . $newname . '.' . $ext;

    if (file_exists($oldPath)) {
        if (rename($oldPath, $newPath)) {
            // Set success message
            $_SESSION['uploadMessage'] = "Image renamed successfully!";
            $_SESSION['uploadMessageType'] = 'success';
            header("Location: image_manager.php?stockID=$stockID");
            exit;
        } else {
            // Set error message if renaming fails
            $_SESSION['uploadMessage'] = "Failed to rename file.";
            $_SESSION['uploadMessageType'] = 'error';
            header("Location: image_manager.php?stockID=$stockID");
            exit;
        }
    } else {
        // File not found error
        $_SESSION['uploadMessage'] = "Original file not found.";
        $_SESSION['uploadMessageType'] = 'error';
        header("Location: image_manager.php?stockID=$stockID");
        exit;
    }
} else {
    echo "Invalid request.";
}
?>
