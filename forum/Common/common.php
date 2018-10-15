<?php
function fname($param){
    $sql = "select szone from " . C('db_prefix') . "small_block where id='" . $param . "' or id='" . ($param - 10000) . "'";
    $arr = mysql_fetch_array(mysql_query($sql));
    return $arr['szone'];
}
function user_title($param){
    $sql = "select title from " . C('db_prefix') . "user where user='" . $param . "'";
    $arr = mysql_fetch_array(mysql_query($sql));
    if(!empty($arr['title'])){
    	return '<img style="float:left;margin-left:10px;" src="'.C("SITE").'/Public/images/'.$arr['title'].'">';
  	}
}
function getTitle($id){
		$sql = "select title from " . C('db_prefix') . "talk where id='" . $id . "'";
		$arr = mysql_fetch_array(mysql_query($sql));
		return strip_tags($arr['title']);
}
function getUserGroup($id){
	if(!empty($id)){
		$sql = "select name from " . C('db_prefix') . "user_group where id='" . $id . "'";
		$arr = mysql_fetch_array(mysql_query($sql));
		return $arr['name'];
	}else{
		return 'NULL';	
	}
}
function getUserGroup_by_name($param){
    $sql = "select user_group from " . C('db_prefix') . "user where user='" . $param . "'";
    $arr = mysql_fetch_array(mysql_query($sql));
    return getUserGroup($arr['user_group']);
}
function getBid_by_name($param){
    $sql = "select bid from " . C('db_prefix') . "user where user='" . $param . "'";
    $arr = mysql_fetch_array(mysql_query($sql));
    return $arr['bid'];
}
function setdate($param){
    return date('Y-m-d H:i:s', $param);
}
function filter_function($param){
		$filters=require("Conf/filter.config.php");
		return strtr($param,$filters);
}
function directory_size($directory){
	$directorysize=0;
	if(is_dir($directory)){
		$dir=opendir($directory);
			while($filename=readdir($dir)){
			   if($filename!="." && $filename!=".."){
			    if(is_file($directory."/".$filename)){
			     $directorysize+=filesize($directory."/".$filename);
			    }
			   }
			}
		@closedir($dir);
	}
	return $directorysize;
}
?>