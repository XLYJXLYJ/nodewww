<?php
class ListAction extends YouYaX
{
    public function transform($txt)
    {
    	$txt = trim($txt);
        $txt = addslashes(htmlspecialchars($txt, ENT_QUOTES, "UTF-8"));
        if (preg_match_all("/\[quote](.+?)\[\/quote]/is", $txt, $match)) {
            $txt = preg_replace('/\s*\r\n\s*/', '', $txt, 1);
        }
        $huanhang = array(
            "\r\n",
            "\n",
            "\r"
        );
        $txt      = str_replace($huanhang, '<br>', $txt);
        /*字体替换*/
        if (preg_match_all("/\[face=(.*?)](.+?)\[\/face]/is", $txt, $face)) {
            for ($i = 0; $i < sizeof($face[0]); $i++) {
                $tmp = preg_replace("/\[face=(.*?)](.+?)\[\/face]/is", '<span style="font-family:' . $face[1][$i] . '">' . $face[2][$i] . '</span>', $face[0][$i]);
                $txt = str_replace($face[0][$i], $tmp, $txt);
            }
        }
        /*--字体替换*/
        
        /*大小替换*/
        if (preg_match_all("/\[size=(.*?)](.+?)\[\/size]/is", $txt, $size)) {
            for ($i = 0; $i < sizeof($size[0]); $i++) {
                if (substr($size[1][$i], 0, 2) > 24)
                    $size[1][$i] = "25px";
                $tmp = preg_replace("/\[size=(.*?)](.+?)\[\/size]/is", '<span style="font-size:' . $size[1][$i] . '">' . $size[2][$i] . '</span>', $size[0][$i]);
                $txt = str_replace($size[0][$i], $tmp, $txt);
            }
        }
        /*--大小替换*/
        
        /*加粗替换*/
        if (preg_match_all("/\[b](.+?)\[\/b]/is", $txt, $bold)) {
            for ($i = 0; $i < sizeof($bold[0]); $i++) {
                $tmp = preg_replace("/\[b](.+?)\[\/b]/is", '<span style="font-weight:bold">' . $bold[1][$i] . '</span>', $bold[0][$i]);
                $txt = str_replace($bold[0][$i], $tmp, $txt);
            }
        }
        /*--加粗替换*/
        
        /*倾斜替换*/
        if (preg_match_all("/\[i](.+?)\[\/i]/is", $txt, $tilt)) {
            for ($i = 0; $i < sizeof($tilt[0]); $i++) {
                $tmp = preg_replace("/\[i](.+?)\[\/i]/is", '<span style="font-style:italic">' . $tilt[1][$i] . '</span>', $tilt[0][$i]);
                $txt = str_replace($tilt[0][$i], $tmp, $txt);
            }
        }
        /*--倾斜替换*/
        
        /*下划线替换*/
        if (preg_match_all("/\[u](.+?)\[\/u]/is", $txt, $under)) {
            for ($i = 0; $i < sizeof($under[0]); $i++) {
                $tmp = preg_replace("/\[u](.+?)\[\/u]/is", '<span style="text-decoration:underline">' . $under[1][$i] . '</span>', $under[0][$i]);
                $txt = str_replace($under[0][$i], $tmp, $txt);
            }
        }
        /*--下划线替换*/
        
        /*颜色替换*/
        if (preg_match_all("/\[color=(.*?)](.+?)\[\/color]/is", $txt, $color)) {
            for ($i = 0; $i < sizeof($color[0]); $i++) {
                $tmp = preg_replace("/\[color=(.*?)](.+?)\[\/color]/is", '<span style="color:' . $color[1][$i] . '">' . $color[2][$i] . '</span>', $color[0][$i]);
                $txt = str_replace($color[0][$i], $tmp, $txt);
            }
        }
        /*--颜色替换*/
        
        /*超链接替换*/
        if (preg_match_all("/\[url=(.*?)](.+?)\[\/url]/is", $txt, $Hyperlink)) {
            for ($i = 0; $i < sizeof($Hyperlink[0]); $i++) {
                if (!preg_match_all("/javascript:/is", strtolower($Hyperlink[1][$i]), $tmp)) {
                    $tmp = preg_replace("/\[url=(.*?)](.+?)\[\/url]/is", '<a target="_blank" style="text-decoration:underline" href="' . $Hyperlink[1][$i] . '">' . $Hyperlink[2][$i] . '</a>', $Hyperlink[0][$i]);
                    $txt = str_replace($Hyperlink[0][$i], $tmp, $txt);
                }
            }
        }
        /*--超链接替换*/
        
        /*本地图片替换*/
        if (preg_match_all("/\[img=(.*?)]\[\/img]/is", $txt, $localimg)) {
            for ($i = 0; $i < sizeof($localimg[0]); $i++) {
                $tmp_img = substr($localimg[1][$i], strrpos($localimg[1][$i], ".") + 1);
                if (in_array($tmp_img, array(
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'JPEG',
                    'JPG',
                    'PNG',
                    'GIF'
                ))) {
                    $tmp = preg_replace("/\[img=(.*?)]\[\/img]/is", '<img src="' . $localimg[1][$i] . '">', $localimg[0][$i]);
                    $txt = str_replace($localimg[0][$i], $tmp, $txt);
                }
            }
        }
        /*--本地图片替换*/
        
        /*音乐替换*/
        if (preg_match_all("/\[music=(.*?)]\[\/music]/is", $txt, $music)) {
            for ($i = 0; $i < sizeof($music[0]); $i++) {
                $tmp_mus = substr($music[1][$i], strrpos($music[1][$i], ".") + 1);
                if (in_array($tmp_mus, array(
                    'mp3',
                    'MP3'
                ))) {
                    $tmp = preg_replace("/\[music=(.*?)]\[\/music]/is", '<audio src="' . $music[1][$i] . '" preload="auto"></audio>', $music[0][$i]);
                    $txt = str_replace($music[0][$i], $tmp, $txt);
                }
            }
        }
        /*--音乐替换*/
        
        /*视频替换*/
        if (preg_match_all("/\[video=(.*?) width=(\d*?) height=(\d*?)]\[\/video]/is", $txt, $video)) {
            for ($i = 0; $i < sizeof($video[0]); $i++) {
                /*	$tmp=preg_replace("/\[video=(.*?)]\[\/video]/is",'<object type="application/x-shockwave-flash" data="'.$video[1][$i].'" width="450" height="300" >'.
                '<param name="movie" value="'.$video[1][$i].'"  />'.
                '<param name="quality" value="high" />'.
                '<param name="play" value="false" />'.
                '<param name="loop" value="false" />'.
                '<param name="menu" value="false" />'.
                '<param name="scale" value="default" />'.
                '<param name="wmode" value="transparent">',$video[0][$i]);
                */
                $tmp_vid = substr($video[1][$i], strrpos($video[1][$i], ".") + 1);
                if (in_array($tmp_vid, array(
                    'swf',
                    'SWF'
                ))) {
                    $tmp = preg_replace("/\[video=(.*?)]\[\/video]/is", '<param name="wmode" value="transparent"><embed allowfullscreen="true" wmode="transparent" src="' . $video[1][$i] . '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' . ($video[2][$i] > 600 ? 600 : $video[2][$i]) . '" height="' . ($video[3][$i] > 400 ? 400 : $video[3][$i]) . '"></embed>', $video[0][$i]);
                    $txt = str_replace($video[0][$i], $tmp, $txt);
                }
            }
        }
        /*--视频替换*/
        
        /*代码替换*/
        if (preg_match_all("/\[code=([^\[]*)](.+?)\[\/code]/is", $txt, $cod)) {
            for ($i = 0; $i < sizeof($cod[0]); $i++) {
                $huanhang   = array(
                    "<br>",
                    "<br/>",
                    "<br />"
                );
                $cod[2][$i] = str_replace($huanhang, '\r\n', $cod[2][$i]);
                $txt        = str_replace($cod[0][$i], '<pre class="brush: ' . ($cod[1][$i]=='html' ? 'xml' : $cod[1][$i]) . '">' . $cod[2][$i] . '</pre>', $txt);
            }
        }
        /*--代码替换*/
        return $txt;
    }
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
        if (!is_numeric(getparam("f"))) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "列表序号不为非数字!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['youyax_f'] = getparam("f");
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
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
        
        $_SESSION['youyax_id'] = 0; //用于记录浏览次数
        $tiezi                 = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "talk where parentid in " . "(" . $_SESSION['youyax_f'] . "," . (int) ($_SESSION['youyax_f'] + 10000) . ")"));
        $this->assign("tiezi", $tiezi);
        $time1 = date("Y-m-d");
        $time1 .= " 00:00:00";
        $time2 = date("Y-m-d");
        $time2 .= " 23:59:59";
        $today1 = $this->select("select  count(*) as count1 from " . C('db_prefix') . "talk where parentid in " . "(" . $_SESSION['youyax_f'] . "," . (int) ($_SESSION['youyax_f'] + 10000) . ")" . " and time1 between '" . $time1 . "' and '" . $time2 . "'");
        $today2 = $this->select("select  count(*) as count2 from " . C('db_prefix') . "reply where parentid2 in" . "(" . $_SESSION['youyax_f'] . "," . (int) ($_SESSION['youyax_f'] + 10000) . ")" . " and time2 between '" . $time1 . "' and '" . $time2 . "'");
        $today  = $today1[0]['count1'] + $today2[0]['count2'];
        $this->assign("today", $today);
        
        $zd_sql  = "select *  from " . C('db_prefix') . "talk left join (select * from " . C('db_prefix') . "reply order by num2 desc) " . C('db_prefix') . "reply  on " . C('db_prefix') . "talk.id=" . C('db_prefix') . "reply.rid and " . C('db_prefix') . "talk.parentid=" . C('db_prefix') . "reply.parentid2 where " . C('db_prefix') . "talk.parentid=" . (int) ($_SESSION['youyax_f'] + 10000) . "  group by id order by " . C('db_prefix') . "talk.timeup desc";
        $zd_data = $this->select($zd_sql);
        $this->assign('zd_data', $zd_data);
        /* 用于列表页Tab start*/
        if (getparam("type") == null) {
            $type = "1=1";
        }
        if (getparam("type") == 1) {
            $type = "is_question=0";
        }
        if (getparam("type") == 2) {
            $type = "is_question=1";
        }
        /* 用于列表页Tab end*/
        $count = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "talk where parentid=" . $_SESSION['youyax_f'] . " and " . $type));
        $mix   = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $fenye = new Fenye($count, $mix['list_per']);
        $show  = $fenye->show();
        $show  = implode("<span style='width:2px;display:inline-block;'></span>", $show);
        $sql   = $fenye->listcon("select *  from " . C('db_prefix') . "talk left join (select * from " . C('db_prefix') . "reply order by num2 desc) " . C('db_prefix') . "reply  on " . C('db_prefix') . "talk.id=" . C('db_prefix') . "reply.rid and " . C('db_prefix') . "talk.parentid=" . C('db_prefix') . "reply.parentid2 where " . C('db_prefix') . "talk.parentid=" . $_SESSION['youyax_f'] . "  and " . $type . " group by id order by " . C('db_prefix') . "talk.timeup desc");
        $data  = $this->select($sql);
        if (empty($data)) {
            $show = "";
        }
        $fsql     = "select szone,mark,sid from " . C('db_prefix') . "small_block where id=" . $_SESSION['youyax_f'];
        $f        = mysql_fetch_array(mysql_query($fsql));
        $f_parent = array();
        if (!empty($f['sid']) && $f['sid'] != 0) {
            $fsql_parent = "select szone,mark,id from " . C('db_prefix') . "small_block where id=" . $f['sid'];
            $f_parent    = mysql_fetch_array(mysql_query($fsql_parent));
        }
        $tongji     = CommonAction::tongji();
        $small_sql  = "select id,szone from " . C('db_prefix') . "small_block";
        $small_data = $this->select($small_sql);
        $this->assign('small_data', $small_data)->assign('count', $tongji);
        $message_result = $this->find(C('db_prefix') . "message_status", 'string', "muser='" . $_SESSION['youyax_user'] . "'");
        if ($message_result['mstatus'] == '1') {
            $this->assign('message_status', 'block');
        } else {
            $this->assign('message_status', 'none');
        }
        /* 子版块统计显示 start*/
        $sql_block   = "select * from " . C('db_prefix') . "small_block where sid=" . $_SESSION['youyax_f'] . " order by ssort desc,szone desc";
        $query_block = mysql_query($sql_block);
        $data_block  = array();
        $time1       = date("Y-m-d");
        $time1 .= " 00:00:00";
        $time2 = date("Y-m-d");
        $time2 .= " 23:59:59";
        $num_block = mysql_num_rows($query_block);
        if ($num_block > 0) {
            while ($arr_block = mysql_fetch_array($query_block)) {
                $data_block[] = $arr_block;
                
                ${'zhuti_child' . $arr_block['id']} = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
                $this->assign("zhuti_child" . $arr_block['id'], ${'zhuti_child' . $arr_block['id']});
                
                ${'tiezi1_child' . $arr_block['id']} = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
                ${'tiezi2_child' . $arr_block['id']} = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "reply where parentid2 in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")"));
                ${'tiezi_child' . $arr_block['id']}  = ${'tiezi1_child' . $arr_block['id']} + ${'tiezi2_child' . $arr_block['id']};
                $this->assign("tiezi_child" . $arr_block['id'], ${'tiezi_child' . $arr_block['id']});
                
                ${'arc_child' . $arr_block['id']} = $this->find(C('db_prefix') . "talk", "string", "parentid=" . $arr_block['id'] . " order by timeup desc");
                $this->assign("arc_child" . $arr_block['id'], ${'arc_child' . $arr_block['id']});
                
                ${'today1_child' . $arr_block['id']} = $this->select("select  count(*) as count1 from " . C('db_prefix') . "talk where parentid in " . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")" . " and time1 between '" . $time1 . "' and '" . $time2 . "'");
                ${'today2_child' . $arr_block['id']} = $this->select("select  count(*) as count2 from " . C('db_prefix') . "reply where parentid2 in" . "(" . $arr_block['id'] . "," . (int) ($arr_block['id'] + 10000) . ")" . " and time2 between '" . $time1 . "' and '" . $time2 . "'");
                ${'today_child' . $arr_block['id']}  = ${'today1_child' . $arr_block['id']}[0]['count1'] + ${'today2_child' . $arr_block['id']}[0]['count2'];
                $this->assign("today_child" . $arr_block['id'], ${'today_child' . $arr_block['id']});
            }
        }
        $this->assign('data_block_child', $data_block);
        /* 子版块统计显示 end*/
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $qq = require("./Conf/qq.config.php");
        $this->assign('qq', $qq);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config)->assign('f', $f)->assign('f_parent', $f_parent)->assign('data', $data)->assign('page', $show)->assign('bz', $bz)->assign('user', $user)->assign('site', C('SITE'))->assign('seo_status', C('seo_set'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display('list/index.html');
        if (empty($_SESSION['youyax_user']) && !stristr($_SERVER['HTTP_USER_AGENT'], 'android') && !stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') && !stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $cache->endCache();
        }
    }
    public function fatie()
    {
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['token'] = md5(microtime(true));
        $user = $_SESSION['youyax_user'];
        $mix  = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config);
        $fsql      = "select szone,mark,sid from " . C('db_prefix') . "small_block where id=" . $_SESSION['youyax_f'];
        $fb        = mysql_fetch_array(mysql_query($fsql));
        $fb_parent = array();
        if (!empty($fb['sid']) && $fb['sid'] != 0) {
            $fsql_parent = "select szone,mark,id from " . C('db_prefix') . "small_block where id=" . $fb['sid'];
            $fb_parent   = mysql_fetch_array(mysql_query($fsql_parent));
        }
        $this->assign('token', $_SESSION['token'])->assign('user', $user)->assign('f', $_SESSION['youyax_f'])->assign('fb', $fb)->assign('fb_parent', $fb_parent)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display('list/fatie.html');
    }
    public function vote()
    {
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['token'] = md5(microtime(true));
        $user = $_SESSION['youyax_user'];
        $mix  = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config);
        $fsql      = "select szone,mark,sid from " . C('db_prefix') . "small_block where id=" . $_SESSION['youyax_f'];
        $fb        = mysql_fetch_array(mysql_query($fsql));
        $fb_parent = array();
        if (!empty($fb['sid']) && $fb['sid'] != 0) {
            $fsql_parent = "select szone,mark,id from " . C('db_prefix') . "small_block where id=" . $fb['sid'];
            $fb_parent   = mysql_fetch_array(mysql_query($fsql_parent));
        }
        $this->assign('token', $_SESSION['token'])->assign('user', $user)->assign('f', $_SESSION['youyax_f'])->assign('fb', $fb)->assign('fb_parent', $fb_parent)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display('list/vote.html');
    }
    public function insert1()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $mix = require("./Conf/mix.config.php");
        if ($mix['is_limit_time']) {
            if (!LimitAction::limit_time($mix['limit_time'])) {
                $this->assign("msgtitle", "操作限制!")->assign("message", "在" . $mix['limit_time'] . "秒内不能发帖和回帖！")->assign("jumpurl", C('SITE'))->error();
            }
        }
        if (empty($_SESSION['youyax_f'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "没有版块标识，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['youyax_f'] = intval($_POST['f']);
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        if (intval($_POST['cat']) == 2 && (intval($_POST['bid']) > 0 && is_numeric($_POST['bid']))) {
            if (!BidAction::query_bid($_SESSION['youyax_user'], intval($_POST['bid']))) {
                $this->assign("msgtitle", "操作错误!")->assign("message", "您的金币数不足!")->assign("jumpurl", C('SITE'))->error();
            }
        }
        $small_id_arr = $this->select("select * from " . C('db_prefix') . "small_block", "id");
        if (!in_array($_POST['f'], $small_id_arr)) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "版块ID异常，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        if (match($_SESSION['youyax_user'], "session_user")) {
            if (($_SESSION['youyax_user'] == addslashes($_POST['zuozhe'])) && CommonAction::competence($_POST['zuozhe'])) {
                $t1          = addslashes($_POST['zuozhe']);
                $isvisible   = ($_POST['isvisible'] == 1) ? 1 : 0;
                $isquestion  = ($_POST['cat'] == 2 && ($_POST['bid'] > 0 && is_numeric($_POST['bid']))) ? 1 : 0;
                $questionbid = ($_POST['bid'] > 0 && is_numeric($_POST['bid'])) ? intval($_POST['bid']) : 0;
                $t2          = mb_substr(addslashes(htmlspecialchars($_POST['title'], ENT_QUOTES, "UTF-8")), 0, 40, 'utf-8');
                $t2          = filter_var($t2, FILTER_CALLBACK, array(
                    "options" => "filter_function"
                ));
                $t3 = filter_var($_POST['content'], FILTER_CALLBACK, array(
                    "options" => "filter_function"
                ));
                if (match($t3, "content")) {
                    if (!empty($_POST['lival'])) {
                        $lival    = $_POST['lival'];
                        $li_count = count($lival);
                        if (match($li_count, "lival")) {
                            $vote_tmp = array();
                            $vote_arr = array();
                            for ($m = 0; $m < $li_count; $m++) {
                                $vote_tmp['options'] = mb_substr(addslashes(htmlspecialchars($lival[$m], ENT_QUOTES, "UTF-8")), 0, 20, 'utf-8');
                                $vote_tmp['nums']    = 0;
                                $vote_arr[]          = $vote_tmp;
                            }
                            mysql_query("insert into " . C('db_prefix') . "vote(rid,comb) values(0,'" . serialize($vote_arr) . "')");
                            $voteid = mysql_insert_id();
                        }
                    }
                    $t3 = $this->transform($t3);
                    if (preg_match_all("/<embed[^>]*?\/\s*>/", $t3, $arr)) {
                        $val = "";
                        foreach ($arr[0] as $v) {
                            if (!preg_match_all("/wmode\s*=\s*\'\s*transparent\s*\'/", $v, $arr2)) {
                                $v1 = preg_replace("/\/>/", " wmode='transparent' />", $v);
                                $v2 = preg_replace("/<embed src/", "<param name='wmode' value='transparent' /><embed src", $v1);
                                $t3 = str_replace($v, $v2, $t3);
                            }
                        }
                    }
                    $user = $this->find(C('db_prefix') . "user", "string", "user='" . $t1 . "'");
                    $t4   = $user['face'];
                    $t5   = $user['time'];
                    $t6   = $user['fatieshu'];
                    $t6   = $t6 + 1;
                    /*添加手机设备识别 start*/
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
                        $t3 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自Android设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
                        $t3 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPhone设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
                        $t3 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPad设备</div>";
                    }
                    /*添加手机设备识别 end*/
                    mysql_query("insert into " . C('db_prefix') . "talk(fatieshu1,timezc1,face,zuozhe,title,content,time1,timeup,parentid,is_visible,is_question,question_bid) values(" . $t6 . ",'" . $t5 . "','" . $t4 . "','" . $t1 . "','" . $t2 . "','" . $t3 . "',now(),now()," . addslashes($_POST['f']) . "," . $isvisible . "," . $isquestion . "," . $questionbid . ")");
                    if (!empty($_POST['lival'])) {
                        $talkid = mysql_insert_id();
                        mysql_query("update " . C('db_prefix') . "vote set rid=" . $talkid . " where id='" . $voteid . "'");
                    }
                    mysql_query("update " . C('db_prefix') . "talk set face='" . $t4 . "',timezc1='" . $t5 . "', fatieshu1='" . $t6 . "'  where  zuozhe='" . $t1 . "'");
                    mysql_query("update " . C('db_prefix') . "reply set fatieshu2=" . $t6 . " where  zuozhe1='" . $t1 . "'");
                    if (intval($_POST['cat']) == 2 && (intval($_POST['bid']) > 0 && is_numeric($_POST['bid']))) {
                        mysql_query("update " . C('db_prefix') . "user set fatieshu=" . $t6 . ",bid=bid-" . intval($_POST['bid']) . " where  user='" . $t1 . "'");
                    } else {
                        mysql_query("update " . C('db_prefix') . "user set fatieshu=" . $t6 . " where  user='" . $t1 . "'");
                    }
                    CountAction::doPostCount();
                    $this->redirect("List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . intval($_POST['f']) . C('static_url'));
                }
            } else {
                $_SESSION['youyax_user'] = "";
                $_SESSION['youyax_data'] = 0;
                $this->assign("msgtitle", "操作错误!")->assign("message", "非法操作，请重新登陆!")->assign("jumpurl", C('SITE'))->error();
            }
        }
    }
    public function insert2()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $mix = require("./Conf/mix.config.php");
        if ($mix['is_limit_time']) {
            if (!LimitAction::limit_time($mix['limit_time'])) {
                $this->assign("msgtitle", "操作限制!")->assign("message", "在" . $mix['limit_time'] . "秒内不能发帖和回帖！")->assign("jumpurl", C('SITE'))->error();
            }
        }
        if (empty($_SESSION['youyax_f'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "没有版块标识，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        //ajax验证判断，防止被禁用Javascript或其他源码恶意操作
        if (empty($_SESSION['youyax_ajax_bz'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "回复帖子没有经过Ajax处理!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['youyax_ajax_bz'] = '';
        if (match($_SESSION['youyax_user'], "session_user")) {
            if (($_SESSION['youyax_user'] == addslashes($_POST['zuozhe1'])) && CommonAction::competence($_POST['zuozhe1']) && CommonAction::lock($_SESSION['youyax_talk_id'])) {
                $t1 = addslashes($_POST['zuozhe1']);
                $t2 = filter_var($_POST['content'], FILTER_CALLBACK, array(
                    "options" => "filter_function"
                ));
                if (match($t2, "content")) {
                    $t2 = $this->transform($t2);
                    $bz = false;
                    if (preg_match_all("/\[quote](.+?)\[\/quote]/is", $t2, $quo)) {
                    	if (match(preg_replace("/\[quote](.+?)\[\/quote]/is",'',$t2),"content")){
                        $quote_id2    = trim($_SESSION['youyax_id2']);
                        $bz           = true;
                        $quote_result = array();
                        if (!empty($quote_id2)) {
                            $quote_result = $this->find(C('db_prefix') . "reply", "string", "id2=" . intval($quote_id2));
                            preg_match_all("/<fieldset.*>.*<\/fieldset>/", $quote_result['content1'], $quo_result_tmp);
                            $quote_result['content1'] = $quo_result_tmp[0][0];
                        } else {
                            $quote_result['content1'] = '';
                            $quote_result['zuozhe1']  = $_SESSION['youyax_id_zuozhe'];
                        }
                        for ($i = 0; $i < sizeof($quo[0]); $i++) {
                            $tmp = preg_replace("/\[quote](.+?)\[\/quote]/is", '<fieldset style="font-size: 12px;border: 1px solid #CCC;padding: 0 10px 10px;margin: 10px 10px 0px 10px;overflow-x: hidden;word-wrap: break-word;"><legend>' . htmlspecialchars(trim($_SESSION['youyax_cite']), ENT_QUOTES, "UTF-8") . '</legend>' . $quote_result['content1'] . strip_tags($quo[1][$i]) . '</fieldset>', strip_tags($quo[0][$i]));
                            $t2  = str_replace($quo[0][$i], $tmp, $t2);
                        }
                        $huanhang = array(
                            "\r\n",
                            "\n",
                            "\r"
                        );
                        $t2       = str_replace($huanhang, '<br>', $t2);
                      }
                    }
                    if (preg_match_all("/<embed[^>]*?\/\s*>/", $t2, $arr)) {
                        $val = "";
                        foreach ($arr[0] as $v) {
                            if (!preg_match_all("/wmode\s*=\s*\'\s*transparent\s*\'/", $v, $arr2)) {
                                $v1 = preg_replace("/\/>/", " wmode='transparent' />", $v);
                                $v2 = preg_replace("/<embed src/", "<param name='wmode' value='transparent' /><embed src", $v1);
                                $t2 = str_replace($v, $v2, $t2);
                            }
                        }
                    }
                    //$t2=str_replace("'",'"',$t2);
                    $t3            = $_SESSION['youyax_talk_id'];
                    $recount_query = mysql_query("select num2,rid,id2 from " . C('db_prefix') . "reply where rid=" . $t3 . " order by id2 desc");
                    if ($recount_query) {
                        $recount_arr = mysql_fetch_array($recount_query);
                        $t4          = $recount_arr['num2'] + 1;
                    } else {
                        $t4 = 1;
                    }
                    if ($t4 >= 4) {
                        if (CommonAction::continue_post($t3, $t4)) {
                            $this->assign("msgtitle", "操作限制!")->assign("message", "一个用户只允许连续回复3次!")->assign("jumpurl", C('SITE'))->error();
                        }
                    }
                    $user = $this->find(C('db_prefix') . "user", "string", "user='" . $t1 . "'");
                    $t5   = $user['face'];
                    $t6   = $user['time'];
                    $t7   = $user['fatieshu'];
                    /*添加手机设备识别 start*/
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
                        $t2 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自Android设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
                        $t2 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPhone设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
                        $t2 .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPad设备</div>";
                    }
                    /*添加手机设备识别 end*/
                    mysql_query("insert into " . C('db_prefix') . "reply(fatieshu2,timezc2,face1,zuozhe1,content1,rid,num2,time2,parentid2) values(" . $t7 . ",'" . $t6 . "','" . $t5 . "','" . $t1 . "','" . $t2 . "'," . $t3 . "," . $t4 . ",now()," . $_SESSION['youyax_f'] . ")");
                    if ($bz) {
                        CommonAction::callSend($t4, $quote_result['zuozhe1'], $t3, $this->youyax_url);
                    }
                    mysql_query("update " . C('db_prefix') . "talk set timeup=now() where id=$t3");
                    mysql_query("update " . C('db_prefix') . "reply set face1='" . $t5 . "',timezc2='" . $t6 . "', fatieshu2='" . $t7 . "'  where  zuozhe1='" . $t1 . "'");
                    $bid_arr = $this->find(C('db_prefix') . "talk", "string", "id='" . intval($t3) . "'");
                    if ($bid_arr['zuozhe'] != $t1) {
                        mysql_query("update " . C('db_prefix') . "user set bid=bid+1 where  user='" . $t1 . "'");
                    }
                    CountAction::doPostCount();
                    if ($_COOKIE['record'] == 'yellow2')
                        $page_tmp = 10;
                    if ($_COOKIE['record'] == 'blue')
                        $page_tmp = 20;
                    if (empty($_COOKIE['record']) || $_COOKIE['record'] == 'shallow')
                        $page_tmp = 15;
                    if ($t4 <= $page_tmp) {
                        $this->redirect("Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $t3 . C('static_url') . "#p" . $t4);
                    } else {
                        $this->redirect("Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $t3 . C('static_url') . "?page=" . ceil($t4 / $page_tmp) . "#p" . $t4);
                    }
                }
            } else {
                $_SESSION['youyax_user'] = "";
                $_SESSION['youyax_data'] = 0;
                $this->assign("msgtitle", "操作错误!")->assign("message", "非法操作，请重新登陆!")->assign("jumpurl", C('SITE'))->error();
            }
        }
    }
    public function Quick()
    {
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        switch ($_POST['action']) {
            case 0:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser0']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass0']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $colors  = addslashes($_POST['colors']);
                        $topicid = intval($_POST['topicid']);
                        $arr     = $this->find(C('db_prefix') . "talk", 'string', $topicid);
                        if ($arr['parentid'] > 10000) {
                            $arr['title'] = preg_replace("/\[[^]]*]/", "", strip_tags($arr['title']));
                            $title        = "<span class=\'top\'>[置顶]</span>" . "<font color=" . $colors . ">" . $arr['title'] . "</font>";
                        } else {
                            $title = "<font color=" . $colors . ">" . strip_tags($arr['title']) . "</font>";
                        }
                        $data['title'] = addslashes($title);
                        $this->save($data, C('db_prefix') . "talk", $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '主题颜色设置成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
            case 1:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser1']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass1']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $topicid          = intval($_POST['topicid']);
                        $arr              = $this->find(C('db_prefix') . "talk", 'string', $topicid);
                        $arr['title']     = preg_replace("/\[[^]]*]/", "", strip_tags($arr['title']));
                        $title            = "<span class=\'top\'>[置顶]</span>" . $arr['title'];
                        $arr['parentid']  = $arr['parentid'] > 10000 ? $arr['parentid'] : ($arr['parentid'] + 10000);
                        $data['parentid'] = (int) ($arr['parentid']);
                        $data['title']    = $title;
                        $this->save($data, C('db_prefix') . "talk", $topicid);
                        $data['parentid2'] = (int) ($arr['parentid']);
                        $this->save($data, C('db_prefix') . "reply", "rid=" . $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '主题置顶成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
            case 2:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser2']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass2']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $topicid          = intval($_POST['topicid']);
                        $arr              = $this->find(C('db_prefix') . "talk", 'string', $topicid);
                        $data['parentid'] = $_POST['small_zone'];
                        $data['title']    = preg_replace("/\[[^]]*]/", "", strip_tags($arr['title']));
                        $this->save($data, C('db_prefix') . "talk", $topicid);
                        $data['parentid2'] = $_POST['small_zone'];
                        $this->save($data, C('db_prefix') . "reply", "rid=" . $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '主题转移成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
            case 3:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser3']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass3']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $topicid = intval($_POST['topicid']);
                        if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
                            w("delPostPicWidget")->judge($topicid);
                        }
                        $this->delete(C('db_prefix') . "talk", $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '主题删除成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
            case 4:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser4']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass4']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $topicid = intval($_POST['topicid']);
                        $data    = $this->find(C('db_prefix') . "talk", 'string', $topicid);
                        if ($data['lock_status'] == 1) {
                            $dat['lock_status'] = 0;
                        } else {
                            $dat['lock_status'] = 1;
                        }
                        $this->save($dat, C('db_prefix') . "talk", $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '主题锁定/解锁成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
            case 5:
                if (!empty($_SESSION['youyax_user'])) {
                    $user = addslashes($_POST['auser5']);
                    if ($_SESSION['youyax_user'] != $user) {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '当前登录用户名必须与版主/管理员名称匹配')->error();
                    }
                    $pass     = md5(addslashes($_POST['apass5']));
                    $sql      = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "'";
                    $query    = mysql_query($sql);
                    $arr      = mysql_fetch_array($query);
                    $purviews = unserialize($arr['purview']);
                    if (empty($purviews)) {
                        $purviews = array();
                    }
                    $num = mysql_num_rows($query);
                    if ($num > 0 && in_array($_SESSION['youyax_f'], $purviews)) {
                        $topicid = intval($_POST['topicid']);
                        $data    = $this->find(C('db_prefix') . "talk", 'string', $topicid);
                        if ($data['is_grap'] == 1) {
                            $dat['is_grap'] = 0;
                        } else {
                            $dat['is_grap'] = 1;
                        }
                        $this->save($dat, C('db_prefix') . "talk", $topicid);
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作成功')->assign('message', '精华设置/取消成功！')->success();
                    } else {
                        $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '您没有管理员权限！')->error();
                    }
                } else {
                    $this->assign('jumpurl', $this->youyax_url . "/List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $_SESSION['youyax_f'] . C('static_url'))->assign('msgtitle', '操作失败')->assign('message', '未登录用户没有权限！')->error();
                }
                break;
        }
    }
    public function editupdate()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_f'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "没有版块标识，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        if ($_SESSION['youyax_user'] == addslashes($_POST['zuozhe']) && CommonAction::competence($_POST['zuozhe'])) {
            $id = !empty($_POST['id']) ? intval($_POST['id']) : intval($_POST['id2']);
            if (!is_numeric($id)) {
                $this->assign("msgtitle", "操作错误!")->assign("message", "编辑序号不为非数字!")->assign("jumpurl", C('SITE'))->error();
            }
            if (match($_POST['content'], "content")) {
                $content = filter_var($this->transform($_POST['content']), FILTER_CALLBACK, array(
                    "options" => "filter_function"
                ));
                /*添加手机设备识别 start*/
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
                        $content .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自Android设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
                        $content .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPhone设备</div>";
                    }
                    if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
                        $content .= "<div style=\'clear:both;\'></div><div style=\'float:right;margin-right:10px;margin-top:20px;background:#9b9b9b;color:#fff;border-radius:10px;padding:10px;line-height:10px;\'>来自iPad设备</div>";
                    }
                    /*添加手机设备识别 end*/
                if ($this->find(C('db_prefix') . "talk", "string", "zuozhe='" . addslashes($_POST['zuozhe']) . "'  and id=" . $id) && !empty($_POST['id'])) {
                    $f                    = $this->find(C('db_prefix') . "talk", "string", "zuozhe='" . addslashes($_POST['zuozhe']) . "'  and id=" . $id);
                    $f['parentid']        = $f['parentid'] > 10000 ? $f['parentid'] - 10000 : $f['parentid'];
                    $data['content']      = $content;
                    $data['last_modify1'] = date('Y-m-d H:i:s', time());
                    $this->save($data, C('db_prefix') . "talk", "zuozhe='" . addslashes($_POST['zuozhe']) . "'  and id=" . $id);
                    $this->redirect("List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $f['parentid'] . C('static_url'));
                } else if ($this->find(C('db_prefix') . "reply", "string", "zuozhe1='" . addslashes($_POST['zuozhe']) . "'  and id2=" . $id) && !empty($_POST['id2'])) {
                    $f                    = $this->find(C('db_prefix') . "reply", "string", "zuozhe1='" . addslashes($_POST['zuozhe']) . "'  and id2=" . $id);
                    $f['parentid2']       = $f['parentid2'] > 10000 ? $f['parentid2'] - 10000 : $f['parentid2'];
                    $data['content1']     = $content;
                    $data['last_modify2'] = date('Y-m-d H:i:s', time());
                    $this->save($data, C('db_prefix') . "reply", "zuozhe1='" . addslashes($_POST['zuozhe']) . "'  and id2=" . $id);
                    $this->redirect("List" . C('default_url') . "index" . C('default_url') . "f" . C('default_url') . $f['parentid2'] . C('static_url'));
                } else {
                   $this->assign("code", "操作错误!")->assign("msg", "您似乎越权了!")->display("Public/exception.html");
                    echo "<script>setTimeout(function(){history.back();},3000);</script>";
                    exit;
                }
            }
        }
    }
}
?>