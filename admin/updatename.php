<?php
$oldname=$_POST['oldname'];
$newname=$_POST['newname'];
$folder=$_POST['folder'];


$dir ='../images/cars/'.$folder.'/';
$a= file_exists($dir.$newname);
$a= file_exists($dir.$newname.".jpg");
if($a == 0)
{
rename($dir.$oldname,$dir.$newname.'.jpg');
echo "Name Updated";
}
else
{
	echo "Select Another Name For This File";
}

?>

