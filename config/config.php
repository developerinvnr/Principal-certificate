<?php

/*
define('HOST','localhost'); 
define('USER','vnrseed2_agrimat'); 
define('PASS','ajay_scpms'); 
define('DATABASE1','vnrseed2_pcfa'); 
	
$link=mysql_connect(HOST,USER,PASS);
if(!$link) die("Failed to connect to database!");
$db=mysql_select_db(DATABASE1,$link);
if(!$db) die("Failed to select database!");
*/

define('HOST','localhost');
define('USER','cashbook_user');  
define('PASSWORD','cashbook@192'); 
define('DATABASE','pcfa');
define("PAGING","30");

$link = mysql_connect(HOST,USER,PASSWORD);
$dblink5 = mysql_select_db(DATABASE,$link);


mysql_query("SET SESSION sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

?>


