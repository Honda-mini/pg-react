<?php require_once('../Connections/pg_services.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_pg_services, $pg_services);
$query_Recordset1 = "SELECT stockID, make, model, regNumber FROM stock";
$Recordset1 = mysql_query($query_Recordset1, $pg_services) or die(mysql_error());
//$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="../scripts/boilerplate.css" rel="stylesheet" type="text/css">
<link href="../css/pgLayout.css" rel="stylesheet" type="text/css">
<script src="../scripts/respond.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

</head>

<body>
<script>
$(document).ready(function(){
						   				$("#confirm").hide();
										var name,name2,name3;  
						$(".imagename").each(function(){
										$(this).hide();
														});
						$('.btnedit').click(function(){
										name=$(this).attr('data_id');
					    				name2=$(this).attr('data_folder');
										name3=$(this).attr('data_name');
										$("#"+name3).toggle("slidedown").slow();
						 								 })
						$('.btnupdate').click(function(event){
										event.preventDefault();
										//var formdata = $("#form_"+name2).serialize();
										var imagename=$("#txtedit_"+name3).val();
										var oldname=name;
										$.ajax({
										type: "POST",
										url:"updatename.php",
										data: {oldname:oldname,newname:imagename,folder:name2}
										}).done(function( result ) {
										$("#confirm").show();
										$("#confirm").html(result);
										$("#"+name3).toggle("slideUp").fast();
										location.reload();
										});
															});
						$('.btndelete').click(function(event){
										event.preventDefault();
										var imagename=$(this).attr('data_id');
										var foldername=$(this).attr('data_name');
										
										$.ajax({
										type: "POST",
										url:"delete_image.php",
										data: {name:imagename,folder:foldername}
										}).done(function( result ) {
											$("#confirm").html(result);
											location.reload();
										
										});	
									 });
						
 });
</script>
<div class="gridContainer clearfix">
  <div id="header">
<?php include("../content/header2.txt"); ?>    <div id="admin" align=right>ADMIN AREA</div> 
    </div>
  
  <div id="nav">
  <?php include("../content/nav2.txt"); ?>
  </div>
  <div id="content">
    <p><h1> Image Editor </h1>
    <p><a href="index.php">Admin Menu</a>
      </p>
    </p>
    <p>
      <div id="confirm">
      </div>
         <select name="list1" id="list1" onChange='window.location="" + this.value;'>
        <?php 
  		while ($line = mysql_fetch_array($Recordset1)) { 
		if($line['stockID']==$_GET['stockID']){	?> 
        
        <option value="pics_test.php?stockID=<?php echo $line['stockID'];?>" selected="selected"> <?php echo $line['model'];?> - <?php echo $line['regNumber'];?></option>
        <?php 
		}
		else
		{
			?>
            <option value="pics_test.php?stockID=<?php echo $line['stockID'];?>" > <?php echo $line['model'];?> - <?php echo $line['regNumber'];?></option>
        
            <?php
			
		}
		  } 
  ?>
      </select>
    </p>
    </div>
    <?php
	if(isset($_GET["stockID"]))
	{
		$foldername=$_GET["stockID"];
		$files = glob('../images/cars/'.$foldername.'/*');
		// Do a natural case insensitive sort, usually 1.jpg and 10.jpg would come next to each other with a regular sort
		natcasesort($files);
		// Display images
		foreach($files as $file)
		{
			
			$name=explode("/",$file);
			$name2=explode(".",$name[4]);
			
			 if (strtoupper(substr($name[4],-4)) == '.JPG'  || strtoupper(substr($name[4],-4)) == '.BMP'  || strtoupper(substr($name[4],-4)) == '.GIF'  || strtoupper(substr($name[4],-4)) == '.PNG'  || strtoupper(substr($name[4],-5)) == '.JPEG'  ) {
       			
 		  echo '<div style="float:left"><img src="'.$file .'" height=160px width=200px; style="margin-left:10px; margin-right:10px; margin-top:10px; margin-bottom:10px;"/><div>'.$name[4].'&nbsp;&nbsp;<a style="text-decoration:none"  name="btnedit" id="btnedit" class="btnedit" data_id="'.$name[4].'" data_name="'.$name2[0].'"data_folder="'.$name[3].'"> <img src="../images/profile_edit.png" /></a>&nbsp;&nbsp;<a style="text-decoration:none" name="btnedit" id="btndelete" class="btndelete" data_id="'.$name[4].'" data_name="'.$name[3].'">  <img src="../images/DeleteRed.png" /></a>
		  <form name="form1" id="form_'.$name2[0].' method="post" style="margin-left:0px;"><div style="float:left"  class="imagename" id="'.$name2[0].'" ><span>New Name:</span><input type="text" name="txtedit" id="txtedit_'.$name2[0].'" /><input type="submit" value="Update" class="btnupdate"/> </div></form>
		  </div></div>';
			}
			
			
		 	
			}

	}
	
	?>
     
  <div id="footer1">
    <p>VIEWING BY APPOINTMENT , ALL VEHICLES VALETED WITH AUTOGLYM PRODUCTS TO A VERY HIGH STANDARD, CARDS ACCEPTED, PX POSS</p>
    <p>Phone : 01736 369940 or Mobile : 07887653155</p>
  </div>
  <div id="footer2" >
    <div>
    
      <p><a href="<?php echo $logoutAction ?>">Log out</a> |
<button class="button" onclick="history.go(-1);">Back </button></p>
      <p style="font-size: 0.6em">©2013 Honda-Mini Designs <a href="http://www.honda-mini.co.uk">Site</a> • <a href="mailto='martyn@honda-mini.co.uk'">Contact</a></p>
    </div>
  </div>
</div>

</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
