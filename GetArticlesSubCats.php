<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("config.php");
require_once("safemysql.class.php");
function GetSubCats($catID,$db){
	$sql = "SELECT `id`,`parent_id`,`title`,`description` FROM `cms_category` WHERE `parent_id`=?i AND `published`=1";
	$Cats = $db->getAll($sql,$catID);
	if($Cats){
		return $Cats;
	}
	return false;
}
$db = new SafeMySQL($config);
if(isset($_POST['catId'])){
	$db = new SafeMySQL($config);
	$result = GetSubCats($_POST['catId'],$db);
	if($result){
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	else{
		echo json_encode(array(), JSON_UNESCAPED_UNICODE);
	}
}
else{
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}