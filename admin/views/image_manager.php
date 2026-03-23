<?php
require_once('../../Connections/pg_services.php');

$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';

if (!isset($_GET['stockID'])) {
    echo "<p>No stock ID provided.</p>";
    exit;
}

$stockID = intval($_GET['stockID']);
$imageDir = "../../images/cars/$stockID/";
$webImagePath = "../images/cars/$stockID/";

if (!is_dir($imageDir)) {
    echo "<p>No images found for vehicle ID $stockID.</p>";
    exit;
}

function getImages($stockID, $imageDir, $webImagePath) {
    $allFiles = array_diff(scandir($imageDir), ['.', '..', 'thumbs', '.DS_Store']);
    $images = array_filter($allFiles, function($file) use ($imageDir) {
        return is_file($imageDir . $file);
    });

    // Start output buffering here, before the HTML content
    ob_start(); 

    // HTML structure for displaying the images
    ?>
    <div id="image-manager-content">
        <?php
        foreach ($images as $image): 
            // Ensure you get the correct image name
            $oldImageName = $image;
            $filenameNoExt = pathinfo($image, PATHINFO_FILENAME); ?>
            
        <div class="image-card">
            <!-- Lightbox Image -->
            <a href="<?= $webImagePath . $image ?>" data-lightbox="vehicle-<?= $stockID ?>">
                <img src="<?= $webImagePath . $image ?>">
            </a>
            
            <!-- Rename form -->
  <form action="scripts/update_image_name.php" method="POST" class="rename-form">
        <input type="hidden" name="stockID" value="<?php echo $stockID; ?>">
        <input type="hidden" name="oldname" value="<?php echo $oldImageName; ?>">
        
        <label for="newname">New Image Name:</label>
        <input type="text" name="newname" id="newname" value="<?= htmlspecialchars($filenameNoExt) ?>" required>
        
        <button type="submit">Rename Image</button>
    </form>            
            <!-- Delete form -->
            <form class="delete-form" method="post" action="scripts/delete_image.php" style="margin-top: 5px;">
                <input type="hidden" name="stockID" value="<?= $stockID ?>">
                <input type="hidden" name="filename" value="<?= $image ?>">
                <button type="submit" style="color: red;">Delete</button>
            </form>
        </div>
            
        <?php endforeach; ?>
    </div>
    <?php

    // Return the buffered output
    return ob_get_clean();
}

?>
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Manager</title>
    <link href="../scripts/boilerplate.css" rel="stylesheet">
    <link href="../css/pgLayout.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
</head>
<body>

<h2 style="margin-bottom: 15px;">Images for Vehicle ID <?= $stockID ?></h2>

<div>
    <?= getImages($stockID, $imageDir, $webImagePath); ?>
</div>

</body>
</html>