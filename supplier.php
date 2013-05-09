<?php

require_once("DB_config.php");

$sql = "select * from supplier";
$DB_connect = new mysqli($DB_url, $DB_username, $DB_password, $DB_name);
$res = $DB_connect->query($sql);
$DB_connect->close();

$supplier = array();
$temp;

while($temp = $res->fetch_array()) {
	$supplier[] = $temp;
	/*
	if(count($retailer) >= 20) {
		break;
	}
	*/
}

echo json_encode($supplier);

?>