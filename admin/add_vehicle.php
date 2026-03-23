<?php
require_once('../src/utils/pg_services.php');
session_start();
ob_start(); // Start output buffering

// Debug switch (set to true to show POST and FILES)
$debug = true;

// Logout logic
if (isset($_GET['doLogout']) && $_GET['doLogout'] === 'true') {
    $_SESSION = [];
    session_destroy();
    header("Location: ../stock.php");
    exit;
}

$logoutAction = htmlspecialchars($_SERVER['PHP_SELF']) . "?doLogout=true" . 
    (!empty($_SERVER['QUERY_STRING']) ? '&' . htmlentities($_SERVER['QUERY_STRING']) : '');

if (empty($_SESSION['username'])) {
    header("Location: login.php?accesscheck=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Create thumbnail
function createThumbnail($sourcePath, $thumbPath, $thumbWidth = 200) {
    $imgInfo = getimagesize($sourcePath);
    if (!$imgInfo) return false;

    list($width, $height, $type) = $imgInfo;

    switch ($type) {
        case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($sourcePath); break;
        case IMAGETYPE_PNG:  $srcImg = imagecreatefrompng($sourcePath); break;
        case IMAGETYPE_GIF:  $srcImg = imagecreatefromgif($sourcePath); break;
        default: return false;
    }

    $thumbHeight = floor($height * ($thumbWidth / $width));
    $thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
    imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG: imagejpeg($thumbImg, $thumbPath); break;
        case IMAGETYPE_PNG:  imagepng($thumbImg, $thumbPath); break;
        case IMAGETYPE_GIF:  imagegif($thumbImg, $thumbPath); break;
    }

    imagedestroy($srcImg);
    imagedestroy($thumbImg);
    return true;
}

$success = false;
$error = '';

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["MM_insert"]) && $_POST["MM_insert"] === "form1") {
    $mysqli = new mysqli($hostname_pg_services, $username_pg_services, $password_pg_services, $database_pg_services);
    if ($mysqli->connect_error) {
        $error = "DB Connection failed: " . $mysqli->connect_error;
    } else {
        $sql = "INSERT INTO stock (make, model, `trim`, additional, yearPlate, regNumber, fuelType, engineSize, mileage, transmission, bodyType, powerBhp, doorsNo, colour, `description`, price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            $error = "Prepare failed: " . $mysqli->error;
        } else {
          $make = $_POST['make'];
          $model = $_POST['model'];
          $trim = $_POST['trim'];
          $additional = $_POST['additional'];
          $yearPlate = $_POST['yearPlate'];
          $regNumber = $_POST['regNumber'];
          $fuelType = $_POST['fuelType'];
          $engineSize = (int)$_POST['engineSize'];
          $mileage = (int)$_POST['mileage'];
          $transmission = $_POST['transmission'];
          $bodyType = $_POST['bodyType'];
          $powerBhp = (int)$_POST['powerBhp'];
          $doorsNo = (int)$_POST['doorsNo'];
          $colour = $_POST['colour'];
          $description = $_POST['description'];
          $price = $_POST['price'];

          // Bind parameters
          if (!$stmt->bind_param(
              "sssssssiissiisss",
              $make, $model, $trim, $additional, $yearPlate, $regNumber, $fuelType,
              $engineSize, $mileage, $transmission, $bodyType,
              $powerBhp, $doorsNo, $colour, $description, $price
          )) {
              $error = "Bind failed: " . $stmt->error;
          }

          // Execute the statement
          if ($stmt->execute()) {
              $stockID = $stmt->insert_id;
              $uploadDir = "../images/cars/$stockID/";
              $thumbDir = $uploadDir . "thumbs/";

              // Create the upload and thumbnail directories if they don't exist
              @mkdir($thumbDir, 0777, true);

              $files = $_FILES['pix'];
              $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

if (!empty($files['name'][0])) {

    // Config (portable for React)
    $MAX_MAIN_SIZE = 1920;
    $THUMB_WIDTH = 400;
    $JPG_QUALITY = 80;
    $WEBP_QUALITY = 80;

    // Utility: load image
    function loadImageUpload($path, $mime) {
        switch ($mime) {
            case 'image/jpeg': return @imagecreatefromjpeg($path);
            case 'image/png':  return @imagecreatefrompng($path);
            default: return false;
        }
    }

    // Utility: fix EXIF rotation
    function fixOrientationUpload($img, $path) {
        if (!function_exists('exif_read_data')) return $img;
        $exif = @exif_read_data($path);
        if (!$exif || !isset($exif['Orientation'])) return $img;

        switch ($exif['Orientation']) {
            case 3: return imagerotate($img, 180, 0);
            case 6: return imagerotate($img, -90, 0);
            case 8: return imagerotate($img, 90, 0);
            default: return $img;
        }
    }

    // Resize + save JPG
    function resizeAndSaveJPGUpload($srcPath, $destPath, $maxSize, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        list($width, $height) = $info;
        $mime = $info['mime'];

        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        $src = fixOrientationUpload($src, $srcPath);

        $ratio = $width / $height;

        if ($ratio > 1) {
            $newWidth = $maxSize;
            $newHeight = (int)($maxSize / $ratio);
        } else {
            $newHeight = $maxSize;
            $newWidth = (int)($maxSize * $ratio);
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($dst, $destPath, $quality);

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    // Create thumbnail
    function createThumbnailUpload($srcPath, $thumbPath, $thumbWidth, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        list($width, $height) = $info;
        $mime = $info['mime'];

        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        $src = fixOrientationUpload($src, $srcPath);

        $ratio = $thumbWidth / $width;
        $thumbHeight = (int)($height * $ratio);

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        imagejpeg($thumb, $thumbPath, $quality);

        imagedestroy($src);
        imagedestroy($thumb);

        return true;
    }

    // Create WebP
    function createWebPUpload($srcPath, $destPath, $quality) {
        $info = @getimagesize($srcPath);
        if (!$info) return false;

        $mime = $info['mime'];
        $src = loadImageUpload($srcPath, $mime);
        if (!$src) return false;

        imagewebp($src, $destPath, $quality);
        imagedestroy($src);

        return true;
    }

    // Loop through uploaded files
    for ($i = 0; $i < count($files['name']); $i++) {

        $file_name = $files['name'][$i];
        $file_tmp  = $files['tmp_name'][$i];
        $file_size = $files['size'][$i];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
            continue;
        }

        // Always save as JPG
        $newName = ($i + 1) . ".jpg";

$dest = $uploadDir . $newName;
$thumbDest = $thumbDir . $newName;

// Resize + save main JPG
resizeAndSaveJPGUpload($file_tmp, $dest, $MAX_MAIN_SIZE, $JPG_QUALITY);

// Create thumbnail
createThumbnailUpload($dest, $thumbDest, $THUMB_WIDTH, $JPG_QUALITY);

// Create WebP versions
createWebPUpload($dest, $uploadDir . ($i + 1) . ".webp", $WEBP_QUALITY);
createWebPUpload($thumbDest, $thumbDir . ($i + 1) . ".webp", $WEBP_QUALITY);

// Delete original upload
@unlink($file_tmp);
}   // ← closes the upload loop ONLY

// ❌ REMOVE THIS ONE
// }   ← this was the extra brace causing the syntax error

}   // ← closes the "if (!empty(files))" block
              // Set the success message and redirect
              $_SESSION['uploadMessage'] = "✔ Vehicle successfully added!";
              $_SESSION['uploadMessageType'] = "success";
                            header("Location: manage_stock.php");
              exit;

          } else {
              $error = "Insert failed: " . $stmt->error;
          }

          $stmt->close();
          $mysqli->close();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/boilerplate.css" rel="stylesheet" type="text/css">
    <link href="styles/pgLayout.css?v=<?=filemtime('styles/pgLayout.css')?>" rel="stylesheet" type="text/css">
</head>
<body>
<div class="adminGridContainer clearfix">
    <div id="header">
        <?php include("includes/header2.txt"); ?>
        <div id="admin" align="right">ADMIN AREA</div> 
    </div>

<div id="nav">      
    <a href="index.php" class="btn btn-secondary nav-back">← Back to Admin Menu</a>

    <button id="nav-toggle" aria-label="Open navigation">
  <span class="hamburger"></span>
  <span class="hamburger"></span>
  <span class="hamburger"></span>
</button>
<?php include("includes/nav2.txt"); ?>
</div>

    <div id="admin-content">
<div id="admin-content">
    <h1>Add Vehicle</h1>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">

        <p>
            <label>Make:</label>
            <input type="text" name="make" required placeholder="e.g. Ford">
        </p>

        <p>
            <label>Model:</label>
            <input type="text" name="model" required placeholder="e.g. Fiesta">
        </p>

        <p>
            <label>Trim:</label>
            <input type="text" name="trim" placeholder="e.g. Zetec, Titanium, Sport">
        </p>

        <p>
            <label>Extra info:</label>
            <textarea name="additional" rows="3" cols="30" placeholder="Optional notes (spec, condition, features). This will go to make up the title"></textarea>
        </p>

        <p>
            <label>Year/Plate:</label>
            <input type="text" name="yearPlate" placeholder="e.g. 2016 / 66 plate">
        </p>

        <p>
            <label>Registration Number:</label>
            <input type="text" name="regNumber" placeholder="e.g. AB16 XYZ">
        </p>

        <p>
            <label>Fuel Type:</label>
            <input type="text" name="fuelType" placeholder="e.g. Petrol, Diesel, Hybrid">
        </p>

        <p>
            <label>Engine Size:</label>
            <input type="text" name="engineSize" placeholder="e.g. 1600">
        </p>

        <p>
            <label>Mileage:</label>
            <input type="text" name="mileage" placeholder="e.g. 54000">
        </p>

        <p>
            <label>Transmission:</label>
            <input type="text" name="transmission" placeholder="e.g. Manual / Automatic">
        </p>

        <p>
            <label>Body Type:</label>
            <input type="text" name="bodyType" placeholder="e.g. Hatchback, SUV, Estate">
        </p>

        <p>
            <label>Power (BHP):</label>
            <input type="text" name="powerBhp" placeholder="e.g. 120">
        </p>

        <p>
            <label>Doors No.:</label>
            <input type="text" name="doorsNo" placeholder="e.g. 5">
        </p>

        <p>
            <label>Colour:</label>
            <input type="text" name="colour" placeholder="e.g. Blue">
        </p>

        <p>
            <label>Description:</label>
            <textarea name="description" rows="5" cols="50" placeholder="Full description of the vehicle..."></textarea>
        </p>

        <p>
            <label>Price:</label>
            <input type="text" name="price" placeholder="e.g. 5995">
        </p>

        <p>
            <label>Upload Images:</label>
            <input type="file" name="pix[]" multiple>
        </p>

        <input type="hidden" name="MM_insert" value="form1">

        <p>
            <input type="submit" value="Add to stock">
        </p>

    </form>
</div>

    <div id="footer1">
        <p>VIEWING BY APPOINTMENT , ALL VEHICLES VALETED WITH AUTOGLYM PRODUCTS TO A VERY HIGH STANDARD</p>
    </div>
    <div id="footer2">
        <p><a href="<?= $logoutAction ?>" class="btn btn-secondary">Log out</a></p>
    </div>
</div>
<script>
document.getElementById('nav-toggle').addEventListener('click', function() {
  var navList = document.querySelector('#navbar');
  navList.classList.toggle('open');
});
</script>

</body>
</html>
