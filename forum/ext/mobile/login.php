<?php
session_start();
$config = include('../../Conf/config.php');
$site_config = include("../../Conf/site.config.php");
?>
<?php
if (isset($_POST['sub'])) {
    include('../../ext_public/database_mobile.php');
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
        echo '<script>window.location.href="'.$config['SITE'].'";</script>';
        exit;
    } else {
        $_SESSION['youyax_data'] = 0;
        echo '<script>alert("输入错误 or 尚未激活");</script>';
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
		<meta name="format-detection" content="telephone=no"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $config['SITE'];?>/Public/Css/mobile/public.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $config['SITE'];?>/Public/Css/mobile/login.css">
		<title><?php echo $site_config['site_title_mobile'];?></title>
	</head>
	<body>
	<!--头部开始-->
		<header>
			<div class="top_head white">
	    <div class="box1">
	        <a href="javascript:history.go(-1);" class="back"></a>
	    </div>
	    <div class="box2">
	        <a href="<?php echo $config['SITE'];?>"><?php echo $site_config['site_title_mobile'];?></a></div>
	    <div class="box3"></div>
			</div>
		</header>
	<!--头部结束-->	
	
	<section>
        <div class="loginBox">
        	<aside></aside>
           <form action="" method="post">
            <div class="login">
                <p>
                    <span class="s1">
                        <label>
                            用户名：</label>
                    </span><span class="s2">
                        <input name="user" type="text" value="">
                    </span>
                </p>
                <p>
                    <span class="s1">
                        <label>
                            密&nbsp;&nbsp;&nbsp;&nbsp;码：</label>
                    </span><span class="s2">
                        <input name="pass" type="password" value="">
                    </span>
                </p>
            </div>
                <input type="submit" name="sub" class="but_1" value="登&nbsp;&nbsp;录">
               <?php if("{qq->app_id}"!='' && "{qq->app_secret}"!=''){ ?>
               <p style="text-align:center">
               	或者<br>
        	<input type="button" class="but_1" value="使用QQ登录" onclick="window.location.href='<?php echo $config['SITE'];?>/ext/qq_connect.php';"><br>
               	</p><?php	}	?>
           </form>
        </div>
        <p class="tishi_zc red">
            还没有帐号？点击 <a href="<?php echo $config['SITE'];?>/ext/mobile/register.php">立即注册</a>
        </p>
    </section>
	
	<!--版权开始-->
	<footer>
		<div class="copyright">
   		<a>Powered BY YouYaX</a>
  	</div>
	</footer>
	<!--版权结束-->
	</body>
</html>