<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_pg_services = "localhost";
$database_pg_services = "causeway_pgServices";
$username_pg_services = "causeway_nidba";
$password_pg_services = "PurPle23!";
$pg_services = @mysql_pconnect($hostname_pg_services, $username_pg_services, $password_pg_services) or trigger_error(mysql_error(),E_USER_ERROR); 
?>