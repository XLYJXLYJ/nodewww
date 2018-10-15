<?php
class IndexAction extends YouYaX
{
    public function index()
    {
        header("Content-Type:text/html; charset=utf-8");
        if (empty($_SESSION['youyax_user']) && !stristr($_SERVER['HTTP_USER_AGENT'], 'android') && !stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') && !stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $cache = new Cache(20);
        }
        if(isset($_COOKIE['youyax_data']) && isset($_COOKIE['youyax_user']) && isset($_COOKIE['youyax_bz']) && preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$_COOKIE['youyax_user']) && preg_match("/^[A-Za-z0-9]+$/u",$_COOKIE['youyax_cookieid'])){
        	if($this->find(C('db_prefix') . "user", 'string', "user='" . addslashes($_COOKIE['youyax_user']) . "' and cookieid='".addslashes($_COOKIE['youyax_cookieid'])."'")){
	        	$_SESSION['youyax_data'] = $_COOKIE['youyax_data'];
	        	$_SESSION['youyax_user'] = $_COOKIE['youyax_user'];
	        	$_SESSION['youyax_bz'] = $_COOKIE['youyax_bz'];
        	 }
   		}
        if ($_SESSION['youyax_data'] == 1) {
            $bz   = $_SESSION['youyax_bz'];
            $user = $_SESSION['youyax_user'];
            if ($bz != 1)
                $bz = 0;
            if ($bz == 0) {
                $_SESSION['youyax_user'] = "";
                $user                    = "";
                $_SESSION['youyax_data'] = 0;
            }
        } else {
            $_SESSION['youyax_user'] = "";
            $_SESSION['youyax_bz']   = "";
            $bz                      = 0;
            $user                    = "";
        }
        $this->assign('bz', $bz)->assign('user', $user);
        $tongji = CommonAction::tongji();
        $this->assign('count', $tongji);
        $todaynum = 0;
        $tiezinum = 0;
        $usernum  = 0;
        $sql_user = "select * from " . C('db_prefix') . "user where (status!=2 and status!=0) order by id desc limit 0,1";
        $new_user = mysql_fetch_array(mysql_query($sql_user));
        $this->assign("new_user", $new_user['user']);
        $sql_num = "select count(*) as num from " . C('db_prefix') . "user where (status!=2 and status!=0)";
        $usernum = mysql_fetch_array(mysql_query($sql_num));
        $this->assign("usernum", $usernum['num']);
        $sql_block   = "select * from " . C('db_prefix') . "small_block where bid!=0 order by ssort desc,szone desc";
        $query_block = mysql_query($sql_block);
        $data_block  = array();
        $data_big    = array();
        $time1       = date("Y-m-d");
        $time1 .= " 00:00:00";
        $time2 = date("Y-m-d");
        $time2 .= " 23:59:59";
        while ($arr_block = mysql_fetch_array($query_block)) {
            $data_block[] = $arr_block;
            
            $bsql                        = "select * from " . C('db_prefix') . "big_block where id=" . $arr_block['bid'];
            $barr                        = mysql_fetch_array(mysql_query($bsql));
            $data_big[$arr_block['bid']] = $barr['bzone'];
            
            ${'zhuti' . $arr_block['id']} = mysql_num_rows(mysql_query("select parentid from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
            $this->assign("zhuti" . $arr_block['id'], ${'zhuti' . $arr_block['id']});
            
            ${'tiezi1' . $arr_block['id']} = mysql_num_rows(mysql_query("select parentid from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
            ${'tiezi2' . $arr_block['id']} = mysql_num_rows(mysql_query("select parentid2 from " . C('db_prefix') . "reply where parentid2 in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
            ${'tiezi' . $arr_block['id']}  = ${'tiezi1' . $arr_block['id']} + ${'tiezi2' . $arr_block['id']};
            $this->assign("tiezi" . $arr_block['id'], ${'tiezi' . $arr_block['id']});
            
            ${'arc' . $arr_block['id']} = $this->find(C('db_prefix') . "talk", "string", "(parentid=" . $arr_block['id'] . " or parentid=".($arr_block['id']+10000).") order by timeup desc");
            $this->assign("arc" . $arr_block['id'], ${'arc' . $arr_block['id']});
            
            ${'today1' . $arr_block['id']} = $this->select("select  count(*) as count1 from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")" . " and time1 between '" . $time1 . "' and '" . $time2 . "'");
            ${'today2' . $arr_block['id']} = $this->select("select  count(*) as count2 from " . C('db_prefix') . "reply where parentid2 in" . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")" . " and time2 between '" . $time1 . "' and '" . $time2 . "'");
            /*子版块 start*/
		        $sql_block_child   = "select * from " . C('db_prefix') . "small_block where bid=0 and sid='".$arr_block['id']."'";
		        $query_block_child = mysql_query($sql_block_child);
		        $num_block_child   = mysql_num_rows($query_block_child);
		        if ($num_block_child > 0) {
		            while ($arr_block_child = mysql_fetch_array($query_block_child)) {
		                ${'today1_child' . $arr_block_child['sid']} = $this->select("select  count(*) as count1 from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block_child['id'] . "," . (int) ($arr_block_child['id'] + 10000) . ")" . " and time1 between '" . $time1 . "' and '" . $time2 . "'");
		                ${'today2_child' . $arr_block_child['sid']} = $this->select("select  count(*) as count2 from " . C('db_prefix') . "reply where parentid2 in" . "(" . $arr_block_child['id'] . "," . (int) ($arr_block_child['id'] + 10000) . ")" . " and time2 between '" . $time1 . "' and '" . $time2 . "'");
		                ${'today_child' . $arr_block_child['sid']}  += ${'today1_child' . $arr_block_child['sid']}[0]['count1'] + ${'today2_child' . $arr_block_child['sid']}[0]['count2'];
		            }
		            $todaynum += ${'today_child' . $arr_block_child['sid']};
		        }
        	/*子版块 end*/
            ${'today' . $arr_block['id']}  = ${'today1' . $arr_block['id']}[0]['count1'] + ${'today2' . $arr_block['id']}[0]['count2'] +${'today_child' . $arr_block['id']};
            $this->assign("today" . $arr_block['id'], ${'today' . $arr_block['id']});
            $todaynum += ${'today' . $arr_block['id']};
            $tiezinum += ${'tiezi' . $arr_block['id']};
        }
        $message_result = $this->find(C('db_prefix') . "message_status", 'string', "muser='" . $_SESSION['youyax_user'] . "'");
        if ($message_result['mstatus'] == '1') {
            $this->assign('message_status', 'block');
        } else {
            $this->assign('message_status', 'none');
        }
        $this->assign("todaynum", $todaynum)->assign("tiezinum", $tiezinum);
        if (getparam("l") == 'cn') {
            $menues = require("Conf/menu.config.php");
        } else if (getparam("l") == 'en') {
            $menues = require("Conf/menu_en.config.php");
        } else if ($_COOKIE['youyax_lang'] == 'cn') {
            $menues = require("Conf/menu.config.php");
        } else if ($_COOKIE['youyax_lang'] == 'en') {
            $menues = require("Conf/menu_en.config.php");
        } else {
        		if($this->config['default_language']=='en'){
            		$menues = require("Conf/menu_en.config.php");
            	}else{
            		$menues = require("Conf/menu.config.php");
            	}
        }
        $this->assign('menues', $menues);
        $reply_num_max = $this->select("select max(num2) as m,rid,time2  FROM " . C('db_prefix') . "reply group by rid having UNIX_TIMESTAMP(time2) between (UNIX_TIMESTAMP(now())-7*24*3600) and UNIX_TIMESTAMP(now()) order by m desc limit 0,7");
        $this->assign('reply_num_max', $reply_num_max);
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $qq = require("./Conf/qq.config.php");
        $this->assign('qq', $qq);
        $site_config = require("./Conf/site.config.php");
        $ads         = require("Conf/ads.config.php");
        $this->assign('site_config', $site_config)->assign('ads', $ads)->assign('data_big', $data_big)->assign('data_block', $data_block)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display('home/index.html');
        if (empty($_SESSION['youyax_user']) && !stristr($_SERVER['HTTP_USER_AGENT'], 'android') && !stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') && !stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $cache->endCache();
        }
    }
    public function logout()
    {
        $_SESSION['youyax_data']   = "";
        $_SESSION['youyax_user'] = "";
        $_SESSION['youyax_bz']   = "";
        @setcookie('youyax_data','',time()-3600,"/");
        @setcookie('youyax_user','',time()-3600,"/");
        @setcookie('youyax_bz','',time()-3600,"/");
        @setcookie('youyax_cookieid','',time()-3600,"/");
        echo "<script>window.location.href='" . C('SITE') . "';</script>";
    }
    public function self()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $this->assign('user', $user)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'));
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config)->display("home/self.html");
    }
    public function saveself()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
         if(preg_match("/^\d{2}\.gif$/",$_POST['face'])){
        	$face = addslashes($_POST['face']);
      	} else {
      		$this->assign("code", "操作错误!")->assign("msg", "禁止非法操作")->display("Public/exception.html");
            echo "<script>setTimeout(function(){history.back();},3000);</script>";
            exit;
      	}
        mysql_query("update " . C('db_prefix') . "user set  face='" . $face . "'  where user='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "talk set  face='" . $face . "'  where zuozhe='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "reply set  face1='" . $face . "'  where zuozhe1='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "mark2 set  pic='" . $face . "'  where marker='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "mark1 set  pic='" . $face . "'  where marker='" . $user . "'");
        $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "self" . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '图片更新成功！')->success();
    }
    public function mypub()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        $sql   = "select * from (select id,title,time1,zuozhe from " . C('db_prefix') . "talk where zuozhe='" . $user . "') t left join (select zuozhe1,rid from " . C('db_prefix') . "reply  order by id2 desc) tmp on t.id=tmp.rid group by t.id order by t.time1 desc";
        $count = mysql_num_rows(mysql_query($sql));
        if ($count <= 0) {
            $mix    = require("./Conf/mix.config.php");
            $show   = '';
            $result = '';
        } else {
            $mix = require("./Conf/mix.config.php");
            require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
            $fenye  = new Fenye($count, 50);
            $show   = $fenye->show();
            $show   = implode("<span style='width:2px;display:inline-block;'></span>", $show);
            $sql2   = $fenye->listcon($sql);
            $result = $this->select($sql2);
        }
        $this->assign('mix', $mix);
        $this->assign('page', $show)->assign("mypubinfo", $result)->assign('user', $user)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'));
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config)->display("home/mypub.html");
    }
    public function myrep()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        $sql   = "select * from (select * from (select zuozhe1,num2,rid,time2 from " . C('db_prefix') . "reply where zuozhe1='" . $user . "' order by time2 desc) tmp group by tmp.rid desc) r left join (select id,zuozhe,title from " . C('db_prefix') . "talk) t on r.rid=t.id order by r.time2 desc";
        $count = mysql_num_rows(mysql_query($sql));
        if ($count <= 0) {
            $mix    = require("./Conf/mix.config.php");
            $show   = '';
            $result = '';
        } else {
            $mix = require("./Conf/mix.config.php");
            require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
            $fenye  = new Fenye($count, 50);
            $show   = $fenye->show();
            $show   = implode("<span style='width:2px;display:inline-block;'></span>", $show);
            $sql2   = $fenye->listcon($sql);
            $result = $this->select($sql2);
        }
        $this->assign('mix', $mix);
        $this->assign('page', $show)->assign("myrepinfo", $result)->assign('user', $user)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'));
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config)->display("home/myrep.html");
    }
    public function chpsd()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        $this->assign('site', C('SITE'))->assign('user', $user)->assign('shtml', C('static_url'))->assign('url', C('default_url'));
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config)->display("home/chpsd.html");
    }
    public function dochpsd()
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        if(!preg_match("/^[A-Za-z0-9_]+$/u",$_POST['newpass'])){
	       $this->assign("code", "操作错误!")->assign("msg", "新密码中含有非法字符")->display("Public/exception.html");
            echo "<script>setTimeout(function(){history.back();},3000);</script>";
            exit;
	    }
        if ($this->find(C('db_prefix') . "user", "string", "user='" . $user . "' and pass='" . md5(addslashes($_POST['oldpass'])) . "'")) {
            mysql_query("update " . C('db_prefix') . "user set  pass='" . md5(addslashes($_POST['newpass'])) . "'  where user='" . $user . "'");
            $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "chpsd" . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '密码更新成功！')->success();
        } else {
            $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "chpsd" . C('static_url'))->assign('msgtitle', '操作错误')->assign('message', '原密码输入有误')->error();
        }
    }
    public function resize($filename)
    {
        $user = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        $album       = "./Public/pic/upload";
        $filenameall = $album . "/" . $filename;
        // File and new size
        // Content type
        //		header('Content-type: image/jpeg');
        // Get new sizes
        list($width, $height) = getimagesize($filenameall);
        list($font, $back) = explode(".", $filename); //获取扩展名
        if ($width >= $height && $width > 120) {
            $newwidth  = 120;
            $newheight = $newwidth * $height / $width;
        } else if ($height >= $width && $height > 120) {
            $newheight = 120;
            $newwidth  = $newheight * $width / $height;
        } else {
            $newwidth  = $width;
            $newheight = $height;
        }
        // Load
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        switch (strtolower($back)) {
            case 'gif':
                $source = imagecreatefromgif($filenameall);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                $name = time() . ".gif";
                imagegif($thumb, $album . "/" . $name);
                break;
            case 'jpg':
            case 'jpeg':
                $source = imagecreatefromjpeg($filenameall);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                $name = time() . ".jpg";
                imagejpeg($thumb, $album . "/" . $name);
                break;
            case 'png':
                $source = imagecreatefrompng($filenameall);
                imagesavealpha($source, true);
                imagealphablending($thumb, false);
                imagesavealpha($thumb,true);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                $name = time() . ".png";
                imagepng($thumb, $album . "/" . $name);
                break;
            default:
                break;
        }
        $oldface  = mysql_fetch_array(mysql_query("select * from  " . C('db_prefix') . "user  where user='" . $user . "'"));
        $oldface2 = $oldface['face'];
        mysql_query("update " . C('db_prefix') . "user set  face='upload/" . $name . "'   where user='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "talk set  face='upload/" . $name . "'   where zuozhe='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "reply set  face1='upload/" . $name . "'   where zuozhe1='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "mark2 set  pic='upload/" . $name . "'   where marker='" . $user . "'");
        mysql_query("update " . C('db_prefix') . "mark1 set  pic='upload/" . $name . "'   where marker='" . $user . "'");
        if (preg_match_all("/upload/", $oldface2, $tmp)) {
            @unlink("./Public/pic/$oldface2");
        }
        @unlink("./Public/pic/upload/$filename");
    }
    public function upload()
    {
        $album = "./Public/pic/upload";
        $user  = $_SESSION['youyax_user'];
        if ($user == "" || $user == null)
            $this->redirect("Index" . C('default_url') . "index" . C('static_url'));
        if (is_dir($album) != true) {
            mkdir($album);
        }
        $type  = array(
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/png',
            'image/x-png'
        );
        $type2 = array(
            'jpg',
            'jpeg',
            'gif',
            'png'
        );
        $type3 = "|.jpeg|.gif|.png|.jpg";
        $hz    = substr(strstr($_FILES["file"]["name"], "."), 1);
        if (in_array($_FILES["file"]["type"], $type) && in_array(strtolower($hz), $type2)) {
            $filename = $_FILES["file"]["name"];
            list($font, $back) = explode(".", $filename); //获取扩展名
            if (!preg_match("/^[\x4e00-\x9fa5]+$/", $font)) {
                echo "<script>alert('上传文件不能有中文,空格!');</script>";
                $this->redirect("Index" . C('default_url') . "self" . C('static_url'));
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $album . "/" . $filename)) {
                    $info = getimagesize($album . "/" . $filename);
                    $ext  = image_type_to_extension($info['2']);
                    if (stripos($type3, $ext)) {
                        $this->resize($filename);
                        $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "self" . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '图片更新成功！')->success();
                    } else {
                        @unlink($album . "/" . $filename);
                        $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "self" . C('static_url'))->assign('msgtitle', '操作错误')->assign('message', '非法类型文件！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "self" . C('static_url'))->assign('msgtitle', '操作错误')->assign('message', '文件上传过程出错！')->error();
                }
            }
        } else {
            $this->assign('jumpurl', $this->youyax_url . "/Index" . C('default_url') . "self" . C('static_url'))->assign('msgtitle', '操作错误')->assign('message', '禁止空上传！')->error();
        }
    }
    public function showip()
    {
        $iparr = array();
        $query = mysql_query("select zone from " . C('db_prefix') . "online");
        while ($arr = mysql_fetch_array($query)) {
            $iparr[] = $arr['zone'];
        }
        echo json_encode($iparr);
    }
    public function getsecmenu()
    {
        if (getparam("l") == 'cn') {
            $menues = require("Conf/menu.config.php");
        } else if (getparam("l") == 'en') {
            $menues = require("Conf/menu_en.config.php");
        } else if ($_COOKIE['youyax_lang'] == 'cn') {
            $menues = require("Conf/menu.config.php");
        } else if ($_COOKIE['youyax_lang'] == 'en') {
            $menues = require("Conf/menu_en.config.php");
        } else {
            if($this->config['default_language']=='en'){
            		$menues = require("Conf/menu_en.config.php");
            	}else{
            		$menues = require("Conf/menu.config.php");
            	}
        }
        $this->assign('menues', $menues);
        echo json_encode($menues[$_POST['data']]['seclists']);
    }
}
?>