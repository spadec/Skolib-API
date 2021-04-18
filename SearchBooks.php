<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
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
function serializeBookList($books, $db){
	$result = array();
	for($i=0;$i<count($books);$i++){
		$cat = $db->getOne('SELECT fieldsstruct FROM cms_uc_cats WHERE id=?i',$books[$i]["category_id"]);
		html_entity_decode($cat, ENT_QUOTES); 
		$catFields = unserialize($cat);
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
if($_POST['mode']){
	$result = array();
	require_once("config.php");
	require_once("safemysql.class.php");
	$db = new SafeMySQL($config);
	$limit = 20;
	$offset = 0;
	if(isset($_POST['limit'])){
		$limit = $_POST['limit'];
	}
	if(isset($_POST['offset'])){
		$offset = $_POST['offset'];
	}
	if($_POST['mode']=='basic'){
		$query = '%'.$_POST['queryString'].'%';
		$sql = "SELECT `id`,`category_id`,`title`,`imageurl`,`fieldsdata` FROM `cms_uc_items` WHERE title LIKE ?s OR fieldsdata LIKE ?s LIMIT ?i OFFSET ?i";
		$books = $db->getAll($sql, $query, $query,$limit,$offset);
		$result = serializeBookList($books, $db);
	}
	if($_POST['mode']=='advanced'){
		$sql = "SELECT `id`,`category_id`,`title`,`imageurl`,`fieldsdata` FROM `cms_uc_items` WHERE fieldsdata ";
		$addOR = array();
		if(isset($_POST['author'])){
			array_push($addOR,$_POST['author']);
		}
		if(isset($_POST['title'])){
			array_push($addOR,$_POST['title']);
		}
		if(isset($_POST['pubyear'])){
			array_push($addOR,$_POST['pubyear']);
		}
		if(count($addOR) > 1){
			for($i=0;$i<count($addOR);$i++){
				if($i+1!=count($addOR)){
					$sql .="LIKE '%".$addOR[$i]."%' AND `fieldsdata` ";
				}
				else {
					$sql .="LIKE '%".$addOR[$i]."%'";
				}
			}
			$sql .= " LIMIT ?i OFFSET ?i";
			$books = $db->getAll($sql,$limit,$offset);
			$result = serializeBookList($books, $db);
		}
		elseif(count($addOR) == 1) {
			$sql .="LIKE '%".$addOR[0]."%'";
			$sql .= " LIMIT ?i OFFSET ?i";
			$books = $db->getAll($sql,$limit,$offset);
			$result = serializeBookList($books, $db);
		}
		else {
			$result =array("error"=>"Для расширенного поиска нужно передать хотя бы один параметр");
		}
	}
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
else {
	
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}