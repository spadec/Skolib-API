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
if(isset($_POST['bookId']) && isset($_POST['raiting'])){
	$db = new SafeMySQL($config);
	$clientIP = getIPAddress();
	$bookId = $_POST['bookId'];
	$isAlreadySet = $db->getOne('SELECT points FROM cms_uc_ratings WHERE item_id = ?i AND ip = ?s',$bookId,$clientIP);
	if(!$isAlreadySet){
		$sql = "INSERT INTO cms_uc_ratings SET item_id = ?i,points=?i,ip=?s";
		$result = $db->query($sql,$_POST['bookId'],$_POST['raiting'],$clientIP);
		echo json_encode(Array("result"=>$result,"isAlreadySet"=>false), JSON_UNESCAPED_UNICODE);
	}
	else {
		echo json_encode(Array("isAlreadySet"=>true, "value"=>$isAlreadySet), JSON_UNESCAPED_UNICODE);
	}
}
else {
	echo json_encode($_POST, JSON_UNESCAPED_UNICODE);
}