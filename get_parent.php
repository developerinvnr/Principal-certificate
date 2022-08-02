<?php
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	echo '<select name="parentName" id="parentName" style="width:265px"><option value="0">-- Select --</option>';
	$sql_parent = mysql_query("SELECT dealer_id, dealer_name FROM dealer WHERE category='".$_POST['cgr']."' AND ai_id=".$_POST['id']." ORDER BY dealer_name",$link);
	while($row_parent = mysql_fetch_array($sql_parent)){
		echo '<option value="'.$row_parent['dealer_id'].'">'.$row_parent['dealer_name'].'</option>';
	}
	echo '</select>';
}?>