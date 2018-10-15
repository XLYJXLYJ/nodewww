<?php
	session_start();
	$config=include('../Conf/config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<script language="javascript">
			function show(n){
				document.form.t.value=""+n.options[n.selectedIndex].value;
			}
		</script>
<style>
.button{-moz-border-radius: 4px;-webkit-border-radius: 4px;border-radius: 4px;font-family: Verdana, Arial, sans-serif;
display: inline-block;
background: #459300 url('<?php echo $config['SITE']; ?>/Public/resources/images/bg-button-green.gif') top left repeat-x !important;
border: 1px solid #459300 !important;
padding: 4px 7px 4px 7px !important;
color: #fff !important;
font-size: 11px !important;
text-align: center;
cursor: pointer;}	
</style>
	</head>
	<body bgcolor="#ebe8e3">
	<?php
		if($_SESSION['youyax_user']==""){
			echo "<b>未登陆用户不能点评!</b>";
		}else{
	?>
		<h3 style="color:#0099cc;margin-bottom:20px;">点评</h3>
			<form name="form" target="_parent" action="<?php echo $config['SITE']; ?>/index.php/Content<?php echo $config['default_url']; ?>mark<?php echo $config['static_url']; ?>" method="post">
		<b>操作说明</b>&nbsp;&nbsp;<select name=sel onchange="show(this)">
												<option value="文不对题">文不对题</option>
												<option value="违规内容">违规内容</option>												
												<option value="我很赞同">我很赞同</option>
												<option value="精品文章">精品文章</option>
										 </select>
		<br>
		<textarea name=t cols=15 rows=5 style="width:160px"></textarea><br>
		<input type="hidden" name="id2" value="<?php echo empty($_GET['id2'])?'':intval($_GET['id2']);?>">
		<input type="hidden" name="id" value="<?php echo intval($_GET['id']);?>">
		<input type="hidden" name="mid" value="<?php echo intval($_GET['mid']);?>">
		<input type="hidden" name="user" value="<?php echo $_SESSION['youyax_user'];?>">
		<input type="hidden" name="reply_u" value="<?php echo addslashes($_GET['reply_u']);?>">
		<input class="button" type="submit" value="确定">
	</form>
	<?php }	?>
</body>
</html>
		