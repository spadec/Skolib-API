<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once("config.php");
require_once("safemysql.class.php");
if($_POST){
	$db = new SafeMySQL($config);
	$limit = 25;
	$data = $db->getAll("SELECT id,title FROM cms_uc_cats WHERE published=1 AND parent_id = 1001 ORDER BY title LIMIT ?i",$limit);
	echo json_encode($data,JSON_UNESCAPED_UNICODE);
}
else {
	echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
}
?>

