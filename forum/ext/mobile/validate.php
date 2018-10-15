<?php
header('Content-type: text/html;charset=utf-8');
include('../../ext_public/database_mobile.php');
$config = include('../../Conf/config.php');
mysql_query("SET NAMES 'utf8'");
$user = $_POST['username'];
$pass = $_POST['password'];
if ($user == "" || $user == null) {
    echo "1";
    exit;
}
if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$user)){
    	echo "5";
    	exit;
}if(!preg_match("/^[A-Za-z0-9_]+$/u",$pass)){
    	echo "6";
    	exit;
}
$sql    = "select * from " . $config['db_prefix'] . "user where user='" . addslashes($user) . "'";
$result = mysql_query($sql);
$num    = mysql_num_rows($result);
if ($num > 0) {
    echo "2";
} else if ($num == 0) {
	  echo "3";
} else {
    echo "4";
}
?>