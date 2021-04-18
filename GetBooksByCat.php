<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("config.php");
require_once("safemysql.class.php");
/**
* очищает имя доп. поля от символов отсавшихся после сериализации
*/
function clearCatName($name){
	$arr = explode("/",$name);
	if(!empty($arr[0])){
		return $arr[0];
	}
	return $name;
}

/**
* фомирует список книг с описанием доп. полей
*/
function serializeBookList($books,$catFields){
	$result = array();
	for($i=0;$i<count($books);$i++){
		html_entity_decode($books[$i]["fieldsdata"], ENT_QUOTES);
		$bookFields = unserialize($books[$i]["fieldsdata"]);
		$fields_result = array();
		for($j=0;$j<count($bookFields);$j++){
			if(!empty($bookFields[$j])){
				$tempFieldsResult = clearCatName($catFields[$j]).": ".strip_tags(html_entity_decode($bookFields[$j]));
				array_push($fields_result,$tempFieldsResult);
			}
		}
		$host = 'https://elib.skolib.kz';
		$imgpath = '/images/catalog/';
		$imgPreview = "/images/catalog/small/";
		$tempResult  = array(
					"id"=>$books[$i]["id"],
					"title"=>$books[$i]["title"],
					"image"=>$host.$imgpath.$books[$i]["imageurl"],
					"imgPreview"=>$host.$imgPreview.$books[$i]["imageurl"].".jpg",
					"fields"=>$fields_result);
		array_push($result,$tempResult);
	}
	return $result;
}
if($_POST['catId']){
	$db = new SafeMySQL($config);
	$limit = 20;
	$offset = 0;
	$orderby = 'id';
	$direction = 'DESC';
	if(isset($_POST['limit'])){
		$limit = $_POST['limit'];
	}
	if(isset($_POST['offset'])){
		$offset = $_POST['offset'];
	}
	if(isset($_POST['direction'])){
		$direction = $_POST['direction'];
	}
	if(isset($_POST['orderby'])){
		$orderby = $_POST['orderby'];
	}

	$fields = $db->getOne('SELECT fieldsstruct FROM cms_uc_cats WHERE id=?i',$_POST['catId']);
	html_entity_decode($fields, ENT_QUOTES); 
	$catFields = unserialize($fields);
	$order = $db->whiteList($orderby, array('id','title'));
	$dir   = $db->whiteList($direction,   array('ASC','DESC'));
	$books = $db->getAll('SELECT `id`,`title`,`imageurl`,`fieldsdata` FROM `cms_uc_items` WHERE `category_id` = ?i AND `published` = 1 ORDER BY ?p ?p LIMIT ?i OFFSET ?i ',$_POST['catId'],$order,$dir,$limit,$offset);
	$result = serializeBookList($books, $catFields);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
else {
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}
