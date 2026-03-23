
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
if(isset($_POST['submit']))  {

 $dir = "images/cars/".$_POST['stockID'];
 $file = glob($dir . '/*.*');
	//echo "<pre>", 
	//print_r($file);
	//, "</pre>";
	foreach($file as $deleted) {
	unlink($deleted);	}
	$a=rmdir($dir);
    if($a)
      echo "directory is removed ";
	else
      echo "cannot remove directory ";
		

	
}
?>


<form id="form" name="form" method="POST" action="">
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>
    <label for="stockID">folder number</label>
    <input type="text" name="stockID" />
  </p>
  <p>
    <input type="submit" name="submit" id="submit" value="Submit" />
  </p>
</form>
</body>
</html>