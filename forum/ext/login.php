<?php
session_start();
$config = include('../Conf/config.php');
?>
<?php
if (isset($_POST['sub'])) {
    include('../ext_public/database.php');
    mysql_query("SET NAMES 'utf8'");
    $sql    = "select* from " . $config['db_prefix'] . "user where binary user='" . addslashes($_POST['user']) . "'  and binary pass='" . md5(addslashes($_POST['pass'])) . "' and status=1 and complete=0";
    $user   = $_POST['user'];
    $result = mysql_query($sql);
    $num    = mysql_num_rows($result);
    $cookieid = md5(microtime(true));
    if ($num == 1) {
        $_SESSION['youyax_data'] = 1;
        $_SESSION['youyax_user'] = $user;
        $_SESSION['youyax_bz']   = 1;
        mysql_query("update " . $config['db_prefix'] . "user set cookieid='".$cookieid."' where user='".addslashes($_POST['user'])."'");
        @setcookie('youyax_data',1,time()+(60*60*24*30),"/");
        @setcookie('youyax_user',$user,time()+(60*60*24*30),"/");
        @setcookie('youyax_bz',1,time()+(60*60*24*30),"/");
        @setcookie('youyax_cookieid',$cookieid,time()+(60*60*24*30),"/");
        echo '1';exit;
    } else {
        $_SESSION['youyax_data'] = 0;
        echo '2';exit;
    }
}
?>