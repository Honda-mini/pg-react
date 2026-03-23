<?php
$imagename=$_POST['name'];
$folder=$_POST['folder'];
  unlink("../images/cars/".$folder.'/'.$imagename);
  echo "Image Deleted";
?>