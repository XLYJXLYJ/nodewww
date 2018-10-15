<?php
	session_start();
	$config=include('../Conf/config.php');
	$mix = require("../Conf/mix.config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<script type="text/javascript" src="../ext/validate_ajax.js"></script>
<style type="text/css">
.button{
	font-family:Arial;
	font-size:14px;
	background:url('../Public/images/button2.png');
	width:62px;
	height:28px;
	line-height:28px;
	border:none;
	cursor:pointer;
}
input[type="text"],input[type="password"]{
	border:1px #dfdfdf solid;
	-webkit-border-radius:5px;
	border-radius:5px;
	height:24px
}
input[type="text"]:focus,input[type="password"]:focus{
	transition:border linear .2s,box-shadow linear .5s;
	-moz-transition:border linear .2s,-moz-box-shadow linear .5s;
	-webkit-transition:border linear .2s,-webkit-box-shadow linear .5s;
	-o-transition:border linear .2s,-webkit-box-shadow linear .5s;
	outline:none;
	box-shadow:0 0 10px rgba(124,105,56,.7);
	-moz-box-shadow:0 0 10px rgba(124,105,56,.7);
	-webkit-box-shadow:0 0 10px rgba(124,105,56,.7);
	-o-box-shadow:0 0 10px rgba(124,105,56,.7)
}
td{font-family: 'Microsoft YaHei',微软雅黑,Arial,Helvetica,sans-serif;font-size:14px;color:#666;}
.btn-mark-gray{border: 0;padding: 0;cursor: pointer;display: inline-block;height: 16px;line-height: 16px;font-size: 12px;color: #fff;padding: 0 5px;margin-bottom: 4px;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;background: #333;text-decoration: none;}
</style>
<title>注册</title>
</head>
<body bgcolor="#f5f5f5" style="overflow:hidden;font-size:13px;">
<form name="form" id="form" action="" method=post onsubmit="senddata('ok');return false;">
<div id="msg"></div>
<table width=360 height=130 cellspacing=0 cellpadding=0>
<tr><td>用户名</td><td><input style="width:130px" id="username" type="text" name="user">
<?php if($mix['is_prevent_reg']){	?>
<img title="已开启IP地址防止恶意注册功能&#10;一个IP仅能注册 <?php echo $mix['prevent_reg_num'];?> 次" src="<?php echo $config['SITE'] . '/Public/images/warm.png';?>">
<?php	}	?>
</td></tr>
<tr><td>密码</td><td><input style="width:130px" id="pass"  name="pass"  type=password onBlur="senddata();"  onFocus="senddata();" ></td></tr>
<tr><td>邮箱</td><td><input style="width:130px" id="email" type="text" name="email" onBlur="senddata();"  onFocus="senddata();" ></td></tr>
<?php	if($config['register_mode']==2){	?>
<tr><td>验证码</td><td valign="top"><input style="width:130px;float:left;" id="valicode" type="text" name="valicode" >
<img style="float:left;margin-left:6px;margin-top:3px;" src="./valicode.php" onclick="this.src='./valicode.php?r='+Math.random();">
</td></tr>
<?php	}	?>
<tr><td valign="bottom"><input type="hidden" name="sub" value="1">	
<input type="submit"  class="button" value="注册"></td>
<td valign="bottom"><span class="btn-mark-gray" onclick="window.location.href='./forgot_password.php';">找回密码</span></td></tr>
</table>
</form>
<?php
include('../ext_public/include.php');
include('../ext_public/database.php');
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
	    $mailconf = require("../Conf/mail.config.php");
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
        	   require_once("../ext_public/phpmailer/class.phpmailer.php");
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
</body>
</html>