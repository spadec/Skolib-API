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
function GetResults($arr,$db){
	$result = array();
	if($arr){
		for($i=0;$i<count($arr);$i++){
			$subcat = GetSubCats($arr[$i]["id"],$db);
			array_push($result,array("id"=>$arr[$i]["id"],
									 "parent_id"=>$arr[$i]["parent_id"],
									 "title"=>$arr[$i]["title"],
									 "description"=>$arr[$i]["description"],
									 "child"=>GetResults($subcat,$db)));
			
		}
	}
	return $result;
	//$result = array(""=>"")
}
if($_POST){
	$db = new SafeMySQL($config);
	$sql = "SELECT `id`,`parent_id`,`title`,`description` FROM `cms_category` WHERE `parent_id`=1 AND `published`=1";
	$ParentCats = $db->getAll($sql);
	//$result = GetResults($ParentCats,$db);
	echo json_encode($ParentCats, JSON_UNESCAPED_UNICODE);
}
else {
	echo json_encode(array("error"=>"Please, use post method"), JSON_UNESCAPED_UNICODE);
}