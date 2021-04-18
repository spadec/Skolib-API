<?php
require_once("config.php");
require_once("safemysql.class.php");
$db = new SafeMySQL($config);
	$sql = "SELECT `id`,`parent_id`,`title`,`description` FROM `cms_category` WHERE `parent_id`=1 AND `published`=1";
	$ParentCats = $db->getAll($sql);
	$result = $ParentCats; //GetResults($ParentCats);
	
function GetSubCats($catID){
	$sql = "SELECT `id`,`parent_id`,`title`,`description` FROM `cms_category` WHERE `parent_id`=?i AND `published`=1";
	$Cats = $db->getAll($sql,$catID);
	if($Cats){
		return $Cats;
	}
	return false;
}
function GetResults($arr){
	$result = array();
	if($arr){
		for($i=0;$i<count($arr);$i++){
			$subcat = GetSubCats($arr[$i]["id"]);
			$subcatResult = GetResults($subcat);
			array_push($result,array("id"=>$arr[$i]["id"],
									 "parent_id"=>$arr[$i]["parent_id"],
									 "title"=>$arr[$i]["title"],
									 "description"=>$arr[$i]["description"],
									 "child"=>$subcatResult));
		}
	}
	return $result;
}
	




print_r($result);

