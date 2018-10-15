<?php
class ContentAction extends YouYaX
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
        $id                         = getparam("id");
        $_SESSION['youyax_talk_id'] = $id;
        if (!is_numeric($id)) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "内容序号不为非数字!")->assign("jumpurl", C('SITE'))->error();
        }
        $talk = T(C('db_prefix') . "talk");
        if (!($talk->find($id))) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "内容不存在!")->assign("jumpurl", C('SITE'))->error();
        }
        $parentid             = $talk->parentid;
        $parentid_tmp         = $parentid > 10000 ? $parentid - 10000 : $parentid;
        $_SESSION['youyax_f'] = $parentid_tmp;
        $vtitle               = $talk->title;
        $user                 = $_SESSION['youyax_user'];
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        if ($_SESSION['youyax_id'] == 0) {
            mysql_query("update " . C('db_prefix') . "talk set num1=num1+1 where id=$id");
        }
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "talk left join " . C('db_prefix') . "reply on " . C('db_prefix') . "talk.id=$id and " . C('db_prefix') . "reply.rid=$id where " . C('db_prefix') . "reply.rid is not null"));
        if ($_COOKIE['record'] == 'yellow2') {
            $fenye = new Fenye($countx['count'], 10);
        } else if ($_COOKIE['record'] == 'blue') {
            $fenye = new Fenye($countx['count'], 20);
        } else {
            $fenye = new Fenye($countx['count'], 15);
        }
        $showx   = $fenye->show();
        $showx   = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql     = $fenye->listcon("select * from " . C('db_prefix') . "talk left join " . C('db_prefix') . "reply on " . C('db_prefix') . "talk.id=$id and " . C('db_prefix') . "reply.rid=$id where " . C('db_prefix') . "reply.rid is not null order by time2");
        $results = $this->select($sql);
        if (empty($results)) {
            $results = $this->select("select *  from " . C('db_prefix') . "talk," . C('db_prefix') . "reply  where " . C('db_prefix') . "talk.id=$id and " . C('db_prefix') . "reply.fuzhuid=1");
            $showx   = "";
        }
        $result_data    = array();
        $result_visible = array();
        foreach ($results as $v) {
            $num = preg_match_all("/<fieldset/", $v['content1'], $tmp);
            if ($num > 5) {
                $v['content1'] = preg_replace("/(<fieldset[^<]+<legend>[^<]+<\/legend>\s*)(<fieldset[^<]+<legend>[^<]+<\/legend>\s*)(<fieldset[^<]+<legend>[^<]+<\/legend>\s*)(<fieldset[^<]+<legend>[^<]+<\/legend>\s*)(<fieldset[^<]+<legend>[^<]+<\/legend>\s*){1,}(<fieldset[^<]+<legend>[^<]+<\/legend>[^<]+)(<\/fieldset>[^<]+)(<\/fieldset>[^<]+){1,}(<\/fieldset>[^<]+)(<\/fieldset>[^<]+)(<\/fieldset>[^<]+)(<\/fieldset>)/is", "\\1\\2\\3\\4<span style=\"color:#388600;position:relative;top:8px;left:10px;\">部分楼层已被隐藏</span>\\6\\8\\9\\10\\11\\12", $v['content1']);
            }
            $result_data[] = $v;
            if (!empty($v['zuozhe1'])) {
                $result_visible[] = $v['zuozhe1'];
            }
            $result_visible[] = $v['zuozhe'];
        }
        $this->assign('showx', $showx)->assign('data1', $result_data)->assign('data_visible', array_unique($result_visible))->assign('data1_0', strip_tags($results[0]['title']))->assign('user', $user);
        $_SESSION['youyax_id'] = 1;
        
        require("./ORG/Page/Fenye_comments.class.php");
        
        $vote_nums = 0;
        if ($vote_arr = $this->find(C('db_prefix') . "vote", "string", "rid=" . $id)) {
            $vote_options = unserialize($vote_arr['comb']);
            $this->assign('vote_options', $vote_options);
            foreach ($vote_options as $vot) {
                $vote_nums += $vot['nums'];
            }
            $this->assign('vote_nums', $vote_nums)->assign('vid', $vote_arr['id']);
        }
        $message_result = $this->find(C('db_prefix') . "message_status", 'string', "muser='" . $_SESSION['youyax_user'] . "'");
        if ($message_result['mstatus'] == '1') {
            $this->assign('message_status', 'block');
        } else {
            $this->assign('message_status', 'none');
        }
        $fsql      = "select szone,sid from " . C('db_prefix') . "small_block where id=" . $parentid_tmp;
        $fq        = mysql_fetch_array(mysql_query($fsql));
        $fq_parent = array();
        if (!empty($fq['sid']) && $fq['sid'] != 0) {
            $fsql_parent = "select szone,mark,id from " . C('db_prefix') . "small_block where id=" . $fq['sid'];
            $fq_parent   = mysql_fetch_array(mysql_query($fsql_parent));
        }
        $this->assign('cid', $id)->assign('fq', $fq)->assign('fq_parent', $fq_parent)->assign('fqlink', $parentid_tmp);
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $qq = require("./Conf/qq.config.php");
        $this->assign('qq', $qq);
        $custom_title_config = require("./Conf/custom_title.config.php");
        $this->assign('custom_title_config', $custom_title_config);
        $_SESSION['token'] = md5(microtime(true));
        $site_config = require("./Conf/site.config.php");
        $this->assign('token', $_SESSION['token'])->assign('site_config', $site_config)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display('content/index.html');
        if (empty($_SESSION['youyax_user']) && !stristr($_SERVER['HTTP_USER_AGENT'], 'android') && !stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') && !stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $cache->endCache();
        }
    }
    public function setParentID()
    {
        $_SESSION['youyax_talk_id'] = intval($_POST['talk_id']);
        $talk                       = T(C('db_prefix') . "talk");
        $talk->find($_SESSION['youyax_talk_id']);
        $parentid                   = $talk->parentid;
        $_SESSION['youyax_f']       = intval($parentid);
        $_SESSION['youyax_ajax_bz'] = 1;
    }
    public function mark()
    {
    	$mix = require("./Conf/mix.config.php");
        if ($mix['is_limit_time']) {
            if (!LimitAction::limit_time($mix['limit_time'])) {
            	$this->assign("code", "操作限制!")->assign("msg", "在" . ($mix['limit_time']*2) . "秒内不能连续点评!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
        }
        $tid = intval($_POST['id']);
        if (!is_numeric($tid)) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "点评序号不为非数字!")->assign("jumpurl", C('SITE'))->error();
        }
        $rid     = intval($_POST['id2']);
        $mid     = intval($_POST['mid']);
        $content = filter_var($_POST['t'], FILTER_CALLBACK, array(
            "options" => "filter_function"
        ));
        $content = nl2br(addslashes(htmlspecialchars($content, ENT_QUOTES,"UTF-8")));
        $reply_u = addslashes($_POST['reply_u']);
        if (match($content, "content")) {
            $marker = addslashes($_POST['user']);
            if ($_SESSION['youyax_user'] == $marker && CommonAction::competence($marker)) {
                if (empty($rid)) {
                    $checkuser = T(C('db_prefix') . 'talk');
                    $checkuser->find($tid);
                    $check = $checkuser->zuozhe;
                } else {
                    $checkuser = $this->find(C('db_prefix') . "reply", "string", "id2='" . $rid . "'");
                    $check     = $checkuser['zuozhe1'];
                    $num2      = $checkuser['num2'];
                }
                if ($marker == "") {
                    $this->assign("code", "操作错误!")->assign("msg", "未登陆用户不能点评")->display("Public/exception.html");
                    echo "<script>setTimeout(function(){window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';},3000)</script>";
                    exit;
                } elseif (($marker == $check) && empty($reply_u)) {
                    $this->assign("code", "操作错误!")->assign("msg", "您不能给自己点评")->display("Public/exception.html");
                    echo "<script>setTimeout(function(){window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';},3000)</script>";
                    exit;
                } elseif ($marker == $reply_u) {
                    $this->assign("code", "操作错误!")->assign("msg", "您不能给自己回复")->display("Public/exception.html");
                    echo "<script>setTimeout(function(){window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';},3000)</script>";
                    exit;
                } else {
                    if (empty($rid)) {
                        $user = $this->find(C('db_prefix') . "user", "string", "user='" . $marker . "'");
                        $pic  = addslashes($user['face']);
                        /*$result = $this->find(C('db_prefix') . "mark1", "string", "tid=" . $tid . " and marker='" . $marker . "'");
                        if ($result) {
                        echo "<script>alert('您不能重复点评');</script>";
                        } else {*/
                        if (!empty($reply_u)) {
                            $res = $this->find(C('db_prefix') . "mark1", "string", "marker='" . $reply_u . "' and id=" . $mid);
                            if ($res) {
                                $content = "<span style=\'vertical-align:top;display:inline-block;*display:inline;\'>" . $_SESSION['youyax_user'] . " @ " . $reply_u . " ：</span><span style=\'display:inline-block;*display:inline;\'>" . $content . "</span>";
                            } else {
                                $this->assign("code", "操作错误!")->assign("msg", "非法操作")->display("Public/exception.html");
                                echo "<script>setTimeout(function(){window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';},3000)</script>";
                                exit;
                            }
                        }
                        mysql_query("insert into " . C('db_prefix') . "mark1(tid,marker,pic,count,content,time) values(" . $tid . ",'" . $marker . "','" . $pic . "',1,'" . $content . "',now())");
                        if (!empty($reply_u)) {
                            CommonAction::callSend('', $reply_u, $tid, $this->youyax_url);
                        } else {
                            CommonAction::callSend('', $check, $tid, $this->youyax_url);
                        }
                        //}
                    } else {
                        $user = $this->find(C('db_prefix') . "user", "string", "user='" . $marker . "'");
                        $pic  = $user['face'];
                        /*$result = $this->find(C('db_prefix') . "mark2", "string", "rid=" . $rid . "  and marker='" . $marker . "'");
                        if ($result) {
                        echo "<script>alert('您不能重复点评');</script>";
                        } else {*/
                        if (!empty($reply_u)) {
                            $res = $this->find(C('db_prefix') . "mark2", "string", "marker='" . $reply_u . "' and id=" . $mid);
                            if ($res) {
                                $content = "<span style=\'vertical-align:top;display:inline-block;*display:inline;\'>" . $_SESSION['youyax_user'] . " @ " . $reply_u . " ：</span><span>" . $content . "</span>";
                            } else {
                                $this->assign("code", "操作错误!")->assign("msg", "非法操作")->display("Public/exception.html");
                                echo "<script>setTimeout(function(){window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';},3000)</script>";
                                exit;
                            }
                        }
                        mysql_query("insert into " . C('db_prefix') . "mark2(tid,rid,marker,pic,count,content,time) values(" . $tid . "," . $rid . ",'" . $marker . "','" . $pic . "',1,'" . $content . "',now())");
                        if (!empty($reply_u)) {
                            CommonAction::callSend($num2, $reply_u, $tid, $this->youyax_url);
                        } else {
                            CommonAction::callSend($num2, $check, $tid, $this->youyax_url);
                        }
                        //}
                    }
                }
                if (empty($num2)) {
                    $num2 = '';
                }
                echo "<script>window.parent.location.href='" . $this->youyax_url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "#p" . $num2 . "';</script>";
            }
        }
    }
    public function edit()
    {
        if (empty($_SESSION['youyax_f'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "没有版块标识，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['token'] = md5(microtime(true));
        $article = getparam("id");
        $bz      = 1;
        if (empty($article)) {
            $article = getparam("id2");
            $bz      = 2;
        }
        if (!is_numeric($article)) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "编辑序号不为非数字!")->assign("jumpurl", C('SITE'))->error();
        }
        $user = $_SESSION['youyax_user'];
        if ($bz == 1) {
            $result            = $this->find(C('db_prefix') . "talk", "string", $article);
            $result['content'] = preg_replace("/<fieldset.*>.*<\/fieldset>/", '', $result['content']);
            if ($user != $result['zuozhe']) {
                $this->assign("msgtitle", "操作错误!")->assign("message", "您似乎越权了!")->assign("jumpurl", C('SITE'))->error();
            }
        }
        if ($bz == 2) {
            $result             = $this->find(C('db_prefix') . "reply", "string", "id2=" . $article);
            $result['content1'] = preg_replace("/<fieldset.*>.*<\/fieldset>/", '', $result['content1']);
            if ($user != $result['zuozhe1']) {
                $this->assign("msgtitle", "操作错误!")->assign("message", "您似乎越权了!")->assign("jumpurl", C('SITE'))->error();
            }
        }
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config);
        $this->assign('token', $_SESSION['token'])->assign('user', $user)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->assign('result', $result)->assign('bz', $bz)->display("content/edit.html");
    }
    public function Quote()
    {
        if (empty($_SESSION['youyax_f'])) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "没有版块标识，非法操作!")->assign("jumpurl", C('SITE'))->error();
        }
        if (!CommonAction::user_group_visit()) {
            $this->assign("msgtitle", "操作错误!")->assign("message", "您所在的用户组没有权限访问!")->assign("jumpurl", C('SITE'))->error();
        }
        $_SESSION['token'] = md5(microtime(true));
        $article1 = getparam("id");
        $article2 = getparam("id2");
        $bz       = 2;
        if (empty($article2)) {
            $result            = $this->find(C('db_prefix') . "talk", "string", $article1);
            $result['content'] = preg_replace("/<fieldset.*>.*<\/fieldset>/", '', $result['content']);
            if ($result['is_visible'] == 1) {
                $result['content'] = '隐藏内容无法显示';
            }
            $bz = 1;
            if (!empty($result)) {
                $_SESSION['youyax_cite']      = $result['zuozhe'] . '  发表于  ' . $result['time1'];
                $_SESSION['youyax_id2']       = '';
                $_SESSION['youyax_id_zuozhe'] = $result['zuozhe'];
            }
        } else {
            $result             = $this->find(C('db_prefix') . "reply", "string", "id2=" . $article2);
            $result['content1'] = preg_replace("/<fieldset.*>.*<\/fieldset>/", '', $result['content1']);
            if (!empty($result)) {
                $_SESSION['youyax_cite']      = $result['zuozhe1'] . '  发表于  ' . $result['time2'];
                $_SESSION['youyax_id2']       = $result['id2'];
                $_SESSION['youyax_id_zuozhe'] = '';
            }
        }
        $this->assign('result', $result);
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $site_config = require("./Conf/site.config.php");
        $this->assign('site_config', $site_config);
        $user = $_SESSION['youyax_user'];
        $this->assign('user', $user);
        $this->assign('token', $_SESSION['token'])->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->assign('bz', $bz)->display("content/quote.html");
    }
    public function delreply()
    {
        if (!empty($_SESSION['youyax_user'])) {
            $rid = !empty($_POST['rid']) ? intval($_POST['rid']) : getparam("rid");
            if (!is_numeric($rid)) {
                $this->assign("code", "操作错误!")->assign("msg", "禁止非法操作！!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
            $radmin = !empty($_POST['radmin']) ? addslashes($_POST['radmin']) : getparam("radmin");
            if ($_SESSION['youyax_user'] != $radmin) {
                $this->assign("code", "操作错误!")->assign("msg", "当前登录用户名必须与版主/管理员名称匹配!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
            $rpass = !empty($_POST['rpass']) ? addslashes($_POST['rpass']) : getparam("rpass");
            $sql   = "select * from " . C('db_prefix') . "admin where binary user='" . $radmin . "' and binary pass='" . md5($rpass) . "'";
            $query = mysql_query($sql);
            $num   = mysql_num_rows($query);
            if ($num <= 0) {
                $this->assign("code", "操作错误!")->assign("msg", "管理员帐号或密码不匹配!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
            $arr      = mysql_fetch_array($query);
            $purviews = unserialize($arr['purview']);
            if (empty($purviews)) {
                $purviews = array();
            }
            $reply = $this->find(C('db_prefix') . "reply", "string", "id2='" . intval($rid) . "'");
            if (!$reply) {
                $this->assign("code", "操作错误!")->assign("msg", "当前回复帖不存在!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
            $tmp = $reply['parentid2'] > 10000 ? ($reply['parentid2'] - 10000) : $reply['parentid2'];
            if (in_array($tmp, $purviews)) {
                if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
                    w("delPostPicWidget")->judge_reply($rid);
                }
                $this->delete(C('db_prefix') . "reply", "id2=" . $rid);
                $this->delete(C('db_prefix') . "mark2", "rid='" . $rid . "'");
                $this->assign("code", "操作成功!")->assign("msg", "删除成功!")->display("Public/success_no_jump.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            } else {
                $this->assign("code", "操作错误!")->assign("msg", "您没有管理员权限!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
        } else {
            $this->assign("msgtitle", "操作错误!")->assign("message", "未登录用户没有权限!")->assign("jumpurl", C('SITE'))->error();
        }
    }
    public function userinfo()
    {
        header("Content-Type:text/html; charset=utf-8");
        $username      = addslashes($_POST["quser"]);
        if(empty($username)){
        	   $this->assign("code", "操作错误!")->assign("msg", "用户资料读取失败!")->display("Public/exception.html");
            exit;
        }
        $user_result   = $this->find(C('db_prefix') . "user", "string", "user='" . $username . "'");
        $sql_talk      = "select  title,id  from " . C('db_prefix') . "talk where zuozhe='" . $username . "' order by id desc limit 0,10";
        $talk_result   = $this->select($sql_talk);
        $online_result = $this->find(C('db_prefix') . "online", "string", "user='" . $username . "'");
        if ($online_result) {
            $this->assign("onlineinfo", "当前在线")->assign("ipinfo", $online_result['zone']);
        } else {
            $this->assign("onlineinfo", "当前离线")->assign("ipinfo", '————');
        }
        $admin_result = $this->find(C('db_prefix') . "admin", "string", "user='" . $username . "'");
        if ($admin_result) {
            $purviews = unserialize($admin_result['purview']);
            if (empty($purviews)) {
                $purviews = array();
            }
            $lists = $this->select("select * from " . C('db_prefix') . "small_block where id in (" . implode(",", $purviews) . ") order by bid,ssort desc");
        }
        $mix = require("./Conf/mix.config.php");
        $this->assign('mix', $mix);
        $this->assign("uinfo", $user_result)->assign("tinfo", $talk_result)->assign("lists", $lists)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("content/userinfo.html");
    }
    public function doJubao()
    {
        if (!empty($_SESSION['youyax_user'])) {
            $mix = require("./Conf/mix.config.php");
            if ($mix['is_limit_time']) {
                if (!LimitAction::limit_time($mix['limit_time']*6)) {
                    $this->assign('code', "操作限制")->assign('msg', ($mix['limit_time']*6) . "秒内不能操作");
                    $this->display("Public/exception.html");
                    echo "<script>setTimeout(function(){history.back();},3000);</script>";
                    exit;
                }
            }
            if (empty($_POST['jbyy']) || (($_POST['jbyy'] == '其他') && (empty($_POST['hbyy_qt'])))) {
                $this->assign("code", "操作错误!")->assign("msg", "请选择举报原因!")->display("Public/exception.html");
                echo "<script>setTimeout(function(){history.back();},3000);</script>";
                exit;
            }
            $data             = array();
            $data['jubao_to'] = addslashes(htmlspecialchars($_POST['jbyh_hd'], ENT_QUOTES));
            if (empty($_POST['hbyy_qt'])) {
                $data['jubao_reason'] = addslashes(htmlspecialchars($_POST['jbyy'], ENT_QUOTES));
            } else {
                $data['jubao_reason'] = addslashes(htmlspecialchars($_POST['jbyy'] . ":" . $_POST['hbyy_qt'], ENT_QUOTES));
            }
            $data['jubao_from'] = $_SESSION['youyax_user'];
            $data['jubao_url']  = addslashes(htmlspecialchars($_POST['jburl'], ENT_QUOTES));
            $data['time']       = time();
            $this->add($data, C('db_prefix') . "jubao");
            $this->assign("code", "操作成功!")->assign("msg", "举报已经成功提交到后台!")->display("Public/success_no_jump.html");
            echo "<script>setTimeout(function(){history.back();},3000);</script>";
            exit;
        } else {
            $this->assign("msgtitle", "操作错误!")->assign("message", "未登录用户没有权限!")->assign("jumpurl", C('SITE'))->error();
        }
    }
}
?>