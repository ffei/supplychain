<?php

require_once("DB_config.php");

$sql = "select * from retailer";
$DB_connect = new mysqli($DB_url, $DB_username, $DB_password, $DB_name);
$res = $DB_connect->query($sql);
$DB_connect->close();

$retailer = array();
$temp;

while($temp = $res->fetch_array()) {
	$retailer[] = $temp;
	/*
	if(count($retailer) >= 20) {
		break;
	}
	*/
}

echo json_encode($retailer);

?>