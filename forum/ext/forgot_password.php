<?php
	session_start();
	$config=include('../Conf/config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<style type="text/css">
#msg{color:red}
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
<title>找回密码</title>
<script>
function check(){
	if(document.getElementById("email").value=='' || document.getElementById("email").value==null){
		document.getElementById("msg").innerHTML='邮箱必填';
		return false;
	}else if(document.getElementById("pass").value=='' || document.getElementById("pass").value==null){
		document.getElementById("msg").innerHTML='新密码必填';
		return false;
	}else{
		document.getElementById("msg").innerHTML='';
		return true;	
	}
}
</script>
</head>
<body bgcolor="#f5f5f5" style="overflow:hidden;font-size:13px;">
<form name="form" id="form" action="" method="post" onsubmit="return check()">
<div id="msg"></div>
<table width=360 height=130 cellspacing=0 cellpadding=0>
<tr><td>邮箱</td><td><input style="width:130px" id="email" type="text" name="email" ></td></tr>
<tr><td>新密码</td><td><input style="width:130px" id="pass" type="text" name="pass" ></td></tr>
<tr><td valign="bottom"><input type="hidden" name="sub" value="1">	
<input type="submit"  class="button" value="发送"></td>
<td valign="bottom"><span class="btn-mark-gray" onclick="window.location.href='./register.php';">注册</span></td></tr>
</table>
</form>
<?php
include('../ext_public/include.php');
include('../ext_public/database.php');
if (isset($_POST['sub'])) {
	if(empty($config['default_user_group'])||empty($config['not_log_in_user_group'])){
    	echo "<script>alert('请至后台[注册激活管理-配置]设置注册和未登陆默认用户组');</script>";
    	exit;
    }
    $_POST['email'] = addslashes($_POST['email']);
    $_POST['pass'] = addslashes($_POST['pass']);
    if (empty($_POST['email']) || empty($_POST['pass'])) {
        echo "<script>alert('邮箱名或新密码必填');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
        exit;
    }
    $mailconf = require("../Conf/mail.config.php");
    if(empty($mailconf['mail_Host'])&&empty($mailconf['mail_Username'])&&empty($mailconf['mail_Password'])){
    	echo "<script>alert('管理员后台未配置邮件服务器，无法邮件找回密码。');</script>";
    	exit;
    }
    $sql    = "select * from " . $config['db_prefix'] . "user where email='" . $_POST['email'] . "'";
    $result = mysql_query($sql);
    $num    = mysql_num_rows($result);
    if ($num <= 0) {
        echo "<script>alert('邮箱不存在');</script>";
        echo "<script>window.parent.location.href='" . url_site . "';</script>";
    } else {
    		$arr = mysql_fetch_array($result);
    	    $code = $arr['codes'];
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
            $mail->Subject  = "论坛重置密码邮件";
            $mail->Body     = "下面的链接点击重置论坛密码，如果不是您本人操作，请忽略。<br><a href='" . url_site . "/ext/forgot_password_active.php?email=" . addslashes($_POST['email']) . "&pass=". addslashes($_POST['pass']) . "&code=" . $code . "'>点此重置</a>";
            if (!$mail->Send()) {
                exit;
            }
            echo "<script>alert('密码重置成功，请至邮箱确认激活!');</script>";
    }
}
?>
</body>
</html>