<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("config.php");
require_once("safemysql.class.php");
/**
* Получает IP адресс клиента
*/
function getIPAddress() {  
    //whether ip is from the share internet  
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
        $ip = $_SERVER['HTTP_CLIENT_IP'];  
    }  
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    }  
	//whether ip is from the remote address  
    else{  
        $ip = $_SERVER['REMOTE_ADDR'];  
    }  
    return $ip;  
} 
function clearCatName($name){
	$arr = explode("/",$name);
	if(!empty($arr[0])){
		return $arr[0];
	}
	return $name;
}
if($_POST['bookId']){
	$db = new SafeMySQL($config);
	$book = $db->getRow('SELECT `id`,`category_id`,`title`,`imageurl`,`fieldsdata` FROM `cms_uc_items` WHERE id = ?i AND `published` = 1',$_POST['bookId']);
	$cat = $db->getOne('SELECT fieldsstruct FROM cms_uc_cats WHERE id=?i',$book["category_id"]);
	html_entity_decode($cat, ENT_QUOTES); 
	$catFields = unserialize($cat);
		
	html_entity_decode($book["fieldsdata"], ENT_QUOTES);
	$bookFields = unserialize($book["fieldsdata"]);
	$fields_result = array();
	for($j=0;$j<count($bookFields);$j++){
		if(!empty($bookFields[$j])){
			$tempFieldsResult = clearCatName($catFields[$j]).": ".strip_tags(html_entity_decode($bookFields[$j]));
			array_push($fields_result,$tempFieldsResult);
		}
	}
	$clientIP = getIPAddress();
	$bookId = $_POST['bookId'];
	$isAlreadySet = $db->getOne('SELECT points FROM cms_uc_ratings WHERE item_id = ?i AND ip = ?s',$bookId,$clientIP);
	if($isAlreadySet){
		$raiting = $isAlreadySet;
		$RaitIsAlreadySet = true;
	}
	else {
		$raiting = 0;
		$RaitIsAlreadySet = false;
	}
	$host = 'https://elib.skolib.kz';
	$imgpath = '/images/catalog/';
	$result  = array(
			"id"=>$book["id"],
			"title"=>$book["title"],
			"image"=>$host.$imgpath.$book["imageurl"],
			"fields"=>$fields_result,
			"rating"=>$raiting,
			"link"=>$host."/catalog/item".$book["id"].".html",
			"RaitIsAlreadySet"=>$RaitIsAlreadySet);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
else {
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}