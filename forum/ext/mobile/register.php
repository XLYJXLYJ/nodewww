<?php
session_start();
$config = include('../../Conf/config.php');
$mix = include("../../Conf/mix.config.php");
$site_config = include("../../Conf/site.config.php");
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
		<script type="text/javascript" src="../../ext/validate_ajax.js"></script>
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
           <form name="form" action="" method="post" onsubmit="senddata('ok');return false;">
            <div id="msg"></div>
            <div class="login">
                <p>
                    <span class="s1">
                        <label>
                            用户名：</label>
                    </span><span class="s2">
                        <input name="user" id="username" type="text" value="" onBlur="senddata();"  onFocus="senddata();">
                    </span>
                </p>
                <p>
                    <span class="s1">
                        <label>
                            密&nbsp;&nbsp;&nbsp;&nbsp;码：</label>
                    </span><span class="s2">
                        <input name="pass" id="pass" type="password" value="" onBlur="senddata();"  onFocus="senddata();">
                    </span>
                </p>
                <p>
                    <span class="s1">
                        <label>
                            邮&nbsp;&nbsp;&nbsp;&nbsp;箱：</label>
                    </span><span class="s2">
                        <input name="email" id="email" type="text" value="" onBlur="senddata();"  onFocus="senddata();">
                    </span>
                </p>
               <?php	if($config['register_mode']==2){	?>
               <p>
                    <span class="s1">
                        <label>
                            验证码：</label>
                    </span><span class="s2">
                        <input name="valicode" type="text" value="">
                    </span>
                    </span><span class="s3">
                        <img style="float:left;margin-left:6px;margin-top:3px;" src="../valicode.php" onclick="this.src='../valicode.php?r='+Math.random();">
                    </span>
                </p>
                <?php	}	?>
            </div>
            <input type="hidden" name="sub" value="1">
						<input type="submit" name="sub" class="but_1" value="注&nbsp;&nbsp;册">
           </form>
        </div>
        <p class="tishi_zc red">
            已有帐号？点击 <a href="<?php echo $config['SITE'];?>/ext/mobile/login.php">立即登录</a>
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
<?php
include('../../ext_public/include_mobile.php');
include('../../ext_public/database_mobile.php');
function doUserCount($param){
	$count_arr = mysql_fetch_array(mysql_query("select * from " . $param . "count where id=1"));
	$data=unserialize($count_arr['user_count']);
	$date=date('w', time());
	$date2=date('W',time());
	if($date2 != $count_arr['week_order']){
  	mysql_query("update " . $param . "count set user_count='',post_count='',week_order='".$date2."' where id=1");
  	$count_arr = mysql_fetch_array(mysql_query("select * from " . $param . "count where id=1"));
		$data=unserialize($count_arr['user_count']);
	}
		switch($date){
			case 0:
				@$data['g']++;
				break;
			case 1:
				@$data['a']++;
				break;
			case 2:
				@$data['b']++;
				break;
			case 3:
				@$data['c']++;
				break;
			case 4:
				@$data['d']++;
				break;
			case 5:
				@$data['e']++;
				break;
			case 6:
				@$data['f']++;
				break;
		}
  	mysql_query("update " . $param . "count set user_count='".serialize($data)."' where id=1");
}
if (isset($_POST['sub'])) {
	if(empty($config['default_user_group'])||empty($config['not_log_in_user_group'])){
    	echo "<script>alert('请至后台[注册激活管理-配置]设置注册和未登陆默认用户组');</script>";
    	exit;
    }
    $user = addslashes(htmlspecialchars(trim($_POST['user']), ENT_QUOTES, "UTF-8"));
    if (empty($_POST['user'])) {
        echo "<script>alert('用户名必填');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
        exit;
    }
    if(mb_strlen($user,'utf8')>7 || mb_strlen($user,'utf8')<2){
    	echo "<script>alert('用户名长度必须在2~7个字符之间');</script>";
      	echo "<script>window.parent.location.href='" . url_site . "';</script>";
      	exit;
    }
    if(mb_substr($_POST['user'],0,1,'utf-8')==' '){
    	echo "<script>alert('用户名首字符不能为空');</script>";
      	echo "<script>window.parent.location.href='" . url_site . "';</script>";
      	exit;
    }
    if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$_POST['user'])){
    	echo "<script>alert('用户名中含有非法字符');</script>";
      	echo "<script>window.parent.location.href='" . url_site . "';</script>";
      	exit;
    }
    if (empty($_POST['pass'])) {
        echo "<script>alert('密码必填');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
        exit;
    }
    if(!preg_match("/^[A-Za-z0-9_]+$/u",$_POST['pass'])){
    	    echo "<script>alert('密码中含有非法字符');</script>";
      	echo "<script>window.parent.location.href='" . url_site . "';</script>";
      	exit;
    }
    $pass = md5(addslashes($_POST['pass']));
    $_POST['email']=addslashes($_POST['email']);
    if (empty($_POST['email'])) {
        echo "<script>alert('邮箱名必填');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
        exit;
    }
	if($mix['is_prevent_reg']){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $myIp = $_SERVER['HTTP_CLIENT_IP'];
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $myIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        $myIp = $_SERVER['REMOTE_ADDR'];
    if(!filter_var($myIp, FILTER_VALIDATE_IP)){
    		echo "<script>alert('此IP地址是无效的!');</script>";
	    echo "<script>window.parent.location.href='" . url_site . "';</script>";
	    exit;
    }
    $sql    = "select * from " . $config['db_prefix'] . "user where ip_addr='" . $myIp . "'";
    $result = mysql_query($sql);
    $num    = mysql_num_rows($result);
	    if($num >= $mix['prevent_reg_num']){
	      echo "<script>alert('注册失败,此IP地址已经使用超过 ".$mix['prevent_reg_num']." 次了');</script>";
	      echo "<script>window.parent.location.href='" . url_site . "';</script>";
	      exit;
	    }
	}
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= mt_rand(0, 9);
    }
    if($config['register_mode']==1){
	    $mailconf = require("../../Conf/mail.config.php");
	    if(empty($mailconf['mail_Host'])&&empty($mailconf['mail_Username'])&&empty($mailconf['mail_Password'])){
	    	echo "<script>alert('管理员后台未配置邮件服务器');</script>";
	    	exit;
	    }
    }
    mysql_query("SET NAMES 'utf8'");
    mysql_query("SET sql_mode=''");
    date_default_timezone_set('Asia/Shanghai');
    $sql    = "select * from " . $config['db_prefix'] . "user where user='" . $user . "'";
    $result = mysql_query($sql);
    $num    = mysql_num_rows($result);
    if ($num > 0) {
        echo "<script>alert('该用户名已被注册');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
    } else {
        $sql2    = "select * from " . $config['db_prefix'] . "user where email='" . $_POST['email'] . "'";
        $result2 = mysql_query($sql2);
        $num2    = mysql_num_rows($result2);
        if ($num2 > 0) {
            echo "<script>alert('邮箱名已被注册');</script>";
            echo "<script>window.parent.location.href='" . url_site . "';</script>";
            exit;
        } else {
        if($config['register_mode']==1){
        	   require_once("../../ext_public/phpmailer/class.phpmailer.php");
            $mail     = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host     = $mailconf['mail_Host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailconf['mail_Username'];
            $mail->Password = $mailconf['mail_Password'];
            $mail->From     = $mailconf['mail_From'];
            $mail->FromName = $mailconf['mail_FromName'];
            $mail->AddAddress($_POST['email']);
            $mail->IsHTML(true);
            $mail->CharSet  = "UTF-8";
            $mail->Encoding = "base64";
            $mail->Subject  = $mailconf['mail_Subject'];
            $mail->Body     = $mailconf['mail_Body'] . "<br><a href='" . url_site . "/ext/mail_active.php?user=" . $user . "&pass=" . $pass . "&email=" . $_POST['email'] . "&code=" . $code . "'>点此激活</a>";
            if (!$mail->Send()) {
                exit;
            }
            if($mix['is_prevent_reg']){
            	$sql = "insert into " . $config['db_prefix'] . "user(user,pass,status,email,complete,face,time,fatieshu,bid,codes,ip_addr,user_group) values('" . $user . "','" . $pass . "',0,'" . $_POST['email'] . "','0','00.gif',now(),0,'".$mix['bid_init']."','" . $code . "','".$myIp."','".$config['default_user_group']."')";
            }else{
            	$sql = "insert into " . $config['db_prefix'] . "user(user,pass,status,email,complete,face,time,fatieshu,bid,codes,user_group) values('" . $user . "','" . $pass . "',0,'" . $_POST['email'] . "','0','00.gif',now(),0,'".$mix['bid_init']."','" . $code . "','".$config['default_user_group']."')";
            }
            mysql_query($sql);
            doUserCount($config['db_prefix']);
            echo "<script>alert('注册成功,请至邮箱激活!');</script>";
            echo "<script>window.parent.location.href='" . url_site . "';</script>";
        }else{
	      	if(addslashes($_POST['valicode'])!=$_SESSION['verify']){
	      		echo "<script>alert('输入的验证码不正确!');</script>";
	      		exit;
	      	}else{
	      		if($mix['is_prevent_reg']){
	      			$sql = "insert into " . $config['db_prefix'] . "user(user,pass,status,email,complete,face,time,fatieshu,bid,codes,ip_addr,user_group) values('" . $user . "','" . $pass . "',1,'" . $_POST['email'] . "','0','00.gif',now(),0,'".$mix['bid_init']."','". $code . "','".$myIp."','".$config['default_user_group']."')";
	      		}else{
	          	$sql = "insert into " . $config['db_prefix'] . "user(user,pass,status,email,complete,face,time,fatieshu,bid,codes,user_group) values('" . $user . "','" . $pass . "',1,'" . $_POST['email'] . "','0','00.gif',now(),0,'".$mix['bid_init']."','". $code . "','".$config['default_user_group']."')";
	        	}
	          mysql_query($sql);
	          doUserCount($config['db_prefix']);
	          echo "<script>alert('注册成功!');</script>";
	          $_SESSION['youyax_data'] = 1;
	          $_SESSION['youyax_user'] = $user;
	          $_SESSION['youyax_bz']   = 1;
	          echo "<script>window.parent.location.href='" . url_site . "';</script>";
	      	}
      	 }
      }
    }
}
?>