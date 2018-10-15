<?php
class adminAction extends YouYaX
{
    public function login()
    {
        if (!empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "index" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $this->assign('token', $_SESSION['token'])->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/login.html");
    }
    public function validate()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $user = addslashes($_POST['user']);
        $pass = md5(addslashes($_POST['pass']));
        $sql  = "select * from " . C('db_prefix') . "admin where binary user='" . $user . "' and binary pass='" . $pass . "' and isAdmin=1";
        $num  = mysql_num_rows(mysql_query($sql));
        if ($num > 0) {
            $_SESSION['youyax_admin'] = $user;
            echo "<script type='text/javascript' src='" . C('SITE') . "/Public/JScript/public.js'></script>
            			<script type='text/javascript' src='" . C('SITE') . "/Public/JScript/Tip2.js'></script>
            			<script>Tip2('登陆成功,3秒后自动跳转',3,1,'parent');
								        for(var i=3;i>=0;i--){
								        (function(){
								          var TipNum=i;
									        t=setTimeout(
									         function(){	         	  
									          	if(TipNum == 0){
									          	 window.clearTimeout(t);
									          	 parent.window.location.href='" . $this->youyax_url . "/admin" . C('default_url') . "index" . C('static_url') . "';
									          	}
									          	parent.document.getElementById('TipMsg').innerHTML='登陆成功,'+TipNum+'秒后自动跳转';          	
									          },(3-TipNum)*1000);
								          })()
								         }</script>";
            //echo '<script>alert("登录成功~~~");</script>';
            //$this->redirect("admin" . C('default_url') . "index" . C('static_url'));
        } else {
            echo "<script type='text/javascript' src='" . C('SITE') . "/Public/JScript/public.js'></script>
            		<script type='text/javascript' src='" . C('SITE') . "/Public/JScript/Tip2.js'></script>
            		<script>Tip2('登陆失败,3秒后自动跳转',2,1,'parent');for(var i=3;i>=0;i--){
								        (function(){
								          var TipNum=i;
									        t=setTimeout(
									         function(){	         	  
									          	if(TipNum == 0){
									          	 window.clearTimeout(t);
									          	 parent.window.location.href='" . $this->youyax_url . "/admin" . C('default_url') . "login" . C('static_url') . "';
									          	}
									          	parent.document.getElementById('TipMsg').innerHTML='登陆失败,'+TipNum+'秒后自动跳转';          	
									          },(3-TipNum)*1000);
								          })()
								         }</script>";
            //echo '<script>alert("登录失败~~~");</script>';
            //$this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
    }
    public function logout()
    {
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        unset($_SESSION['youyax_admin']);
        $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
    }
    public function index()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $this->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/index.html");
    }
    public function secindex()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $this->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/secindex.html");
    }
    public function tophead()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $this->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->assign('admin', $_SESSION['youyax_admin'])->display("admin/tophead.html");
    }
    public function leftbar()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $this->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/leftbar.html");
    }
    public function content()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $reply_num_max  = $this->select("select max(num2) as m,rid,time2  FROM " . C('db_prefix') . "reply group by rid having UNIX_TIMESTAMP(time2) between (UNIX_TIMESTAMP(now())-7*24*3600) and UNIX_TIMESTAMP(now()) order by m desc limit 0,5");
        $scan_num_max   = $this->select("select *  FROM " . C('db_prefix') . "talk where UNIX_TIMESTAMP(time1) between (UNIX_TIMESTAMP(now())-7*24*3600) and UNIX_TIMESTAMP(now()) order by num1 desc limit 0,5");
        $count_arr      = $this->find(C('db_prefix') . "count", "string", "id=1");
        $count_user     = unserialize($count_arr['user_count']);
        $count_user_num = 0;
        if (!empty($count_user)) {
            foreach ($count_user as $v) {
                $count_user_num += $v;
            }
        }
        $count_post     = unserialize($count_arr['post_count']);
        $count_post_num = 0;
        if (!empty($count_post)) {
            foreach ($count_post as $v) {
                $count_post_num += $v;
            }
        }
        $this->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->assign('count_user_num', $count_user_num)->assign('count_post_num', $count_post_num)->assign('reply_num_max', $reply_num_max)->assign('scan_num_max', $scan_num_max)->assign('url_connect', $this->youyax_url)->display("admin/content.html");
    }
    public function block()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $this->assign('site', C('SITE'));
        //$data=$this->select("select * from ".C('db_prefix')."big_block");
        $sql  = "select big.id,big.bzone from " . C('db_prefix') . "big_block big left join (select * from (select * from " . C('db_prefix') . "small_block order by ssort desc,szone desc)smalltmp group by smalltmp.bid ) tmp on big.id=tmp.bid order by tmp.ssort desc,tmp.szone desc";
        $data = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign("data", $data)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/block.html");
    }
    public function block_transform()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $this->assign('site', C('SITE'));
        $sql  = "select * from " . C('db_prefix') . "small_block";
        $data = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign("data", $data)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/block_transform.html");
    }
    public function block_do_transform()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $sql = "update " . C('db_prefix') . "talk set parentid=" . intval($_POST['oto']) . " where parentid=" . intval($_POST['org']);
        mysql_query($sql);
        $sql = "update " . C('db_prefix') . "reply set parentid2=" . intval($_POST['oto']) . " where parentid2=" . intval($_POST['org']);
        mysql_query($sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "block_transform" . C('static_url'));
    }
    public function delblock()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id      = getparam("id");
        $big_sql = "delete from " . C('db_prefix') . "big_block where id=" . $id;
        mysql_query($big_sql);
        $sql   = "select * from " . C('db_prefix') . "small_block where bid=" . $id;
        $query = mysql_query($sql);
        $num   = mysql_num_rows($query);
        if ($num > 0) {
            while ($arr = mysql_fetch_array($query)) {
                $cmall_sql = "delete from " . C('db_prefix') . "small_block where sid=" . $arr['id'];
                mysql_query($cmall_sql);
            }
        }
        $small_sql = "delete from " . C('db_prefix') . "small_block where bid=" . $id;
        mysql_query($small_sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "block" . C('static_url'));
    }
    public function editblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id   = intval($_POST["id"]);
        $name = addslashes(htmlspecialchars($_POST["bzone"], ENT_QUOTES, "UTF-8"));
        if (empty($name)) {
            $_SESSION['youyax_error'] = 2;
        } else {
            $t = T(C('db_prefix') . "big_block");
            $t->find($id);
            $t->bzone                 = $name;
            $_SESSION['youyax_error'] = 1;
            $t->save();
        }
        $this->redirect("admin" . C('default_url') . "block" . C('static_url'));
    }
    public function addblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $name = addslashes(htmlspecialchars($_POST["bzone"], ENT_QUOTES, "UTF-8"));
        if (!empty($name)) {
            $t        = T(C('db_prefix') . "big_block");
            $t->bzone = $name;
            $t->add();
            $_SESSION['youyax_error'] = 1;
        } else {
            $_SESSION['youyax_error'] = 2;
            echo '<script>alert("名称必填项");</script>';
        }
        $this->redirect("admin" . C('default_url') . "block" . C('static_url'));
    }
    public function sblock()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $this->assign('site', C('SITE'));
        $data = $this->select("select * from " . C('db_prefix') . "small_block where bid=" . getparam("id") . " order by ssort desc,szone desc");
        $this->assign("data", $data);
        $data1 = $this->select("select * from " . C('db_prefix') . "big_block");
        $this->assign("data1", $data1);
        $data3 = $this->find(C('db_prefix') . "big_block", "string", getparam("id"));
        $this->assign('token', $_SESSION['token'])->assign("data3", $data3)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/sblock.html");
    }
    public function delsblock()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id        = getparam("id");
        $bid       = getparam("bid");
        $small_sql = "delete from " . C('db_prefix') . "small_block where id=" . $id . " or sid=" . $id;
        mysql_query($small_sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "sblock" . C('default_url') . "id" . C('default_url') . $bid . C('static_url'));
    }
    public function editsblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id       = intval($_POST["id"]);
        $szone    = addslashes(htmlspecialchars($_POST["szone"], ENT_QUOTES, "UTF-8"));
        $mark     = addslashes(htmlspecialchars($_POST["mark"], ENT_QUOTES, "UTF-8"));
        $icon_url = addslashes(htmlspecialchars($_POST["icon_url"], ENT_QUOTES, "UTF-8"));
        $bid      = intval($_POST["bid"]);
        $ssort    = intval($_POST['ssort']);
        $bbid     = intval($_POST["bbid"]);
        //	 if(empty($szone)&&empty($mark)&&empty($bid)){
        //	 	$_SESSION['youyax_error']=2;
        // 	}else{
        $t        = T(C('db_prefix') . "small_block");
        $t->find($id);
        if (!empty($szone))
            $t->szone = $szone;
        if (!empty($mark))
            $t->mark = nl2br($mark);
         $t->icon_url = $icon_url;
        if (!empty($bid))
            $t->bid = $bid;
        if (!empty($ssort))
            $t->ssort = $ssort;
        $_SESSION['youyax_error'] = 1;
        $t->save();
        //	}
        $this->redirect("admin" . C('default_url') . "sblock" . C('default_url') . "id" . C('default_url') . $bbid . C('static_url'));
    }
    public function addsblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $szone    = addslashes(htmlspecialchars($_POST["szone"], ENT_QUOTES, "UTF-8"));
        $mark     = addslashes(htmlspecialchars($_POST["mark"], ENT_QUOTES, "UTF-8"));
        $bid      = intval($_POST["bid"]);
        $icon_url = addslashes(htmlspecialchars($_POST["icon_url"], ENT_QUOTES, "UTF-8"));
        if (!empty($szone) && !empty($bid)) {
            $t           = T(C('db_prefix') . "small_block");
            $t->szone    = $szone;
            $t->mark     = nl2br($mark);
            $t->icon_url = $icon_url;
            $t->bid      = $bid;
            $t->ssort    = 0;
            $t->add();
            $_SESSION['youyax_error'] = 1;
        } else {
            $_SESSION['youyax_error'] = 2;
            echo '<script>alert("名称或隶属必填项");</script>';
        }
        if (!empty($bid)) {
            $this->redirect("admin" . C('default_url') . "sblock" . C('default_url') . "id" . C('default_url') . $bid . C('static_url'));
        } else {
            $this->redirect("admin" . C('default_url') . "block" . C('static_url'));
        }
    }
    public function cblock()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $this->assign('site', C('SITE'));
        $data = $this->select("select * from " . C('db_prefix') . "small_block where sid=" . getparam("id") . " order by ssort desc,szone desc");
        $this->assign("data", $data);
        $data3 = $this->find(C('db_prefix') . "small_block", "string", getparam("id"));
        $data2 = $this->find(C('db_prefix') . "big_block", "string", $data3['bid']);
        $this->assign('token', $_SESSION['token'])->assign("data3", $data3)->assign("data2", $data2)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/cblock.html");
    }
    public function addcblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $szone    = addslashes(htmlspecialchars($_POST["szone"], ENT_QUOTES, "UTF-8"));
        $mark     = addslashes(htmlspecialchars($_POST["mark"], ENT_QUOTES, "UTF-8"));
        $sid      = intval($_POST["sid"]);
        $icon_url = addslashes(htmlspecialchars($_POST["icon_url"], ENT_QUOTES, "UTF-8"));
        if (!empty($szone) && !empty($sid)) {
            $t           = T(C('db_prefix') . "small_block");
            $t->szone    = $szone;
            $t->mark     = nl2br($mark);
            $t->icon_url = $icon_url;
            $t->sid      = $sid;
            $t->ssort    = 0;
            $t->add();
            $_SESSION['youyax_error'] = 1;
        }
        $this->redirect("admin" . C('default_url') . "cblock" . C('default_url') . "id" . C('default_url') . $sid . C('static_url'));
    }
    public function delcblock()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id        = getparam("id");
        $sid       = getparam("sid");
        $small_sql = "delete from " . C('db_prefix') . "small_block where id=" . $id;
        mysql_query($small_sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "cblock" . C('default_url') . "id" . C('default_url') . $sid . C('static_url'));
    }
    public function editcblock()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id       = intval($_POST["id"]);
        $szone    = addslashes(htmlspecialchars($_POST["szone"], ENT_QUOTES, "UTF-8"));
        $mark     = addslashes(htmlspecialchars($_POST["mark"], ENT_QUOTES, "UTF-8"));
        $icon_url = addslashes(htmlspecialchars($_POST["icon_url"], ENT_QUOTES, "UTF-8"));
        $ssort    = intval($_POST['ssort']);
        $sid      = intval($_POST["sid"]);
        $t        = T(C('db_prefix') . "small_block");
        $t->find($id);
        if (!empty($szone))
            $t->szone = $szone;
        if (!empty($mark))
            $t->mark = nl2br($mark);
        $t->icon_url = $icon_url;
        if (!empty($ssort))
            $t->ssort = $ssort;
        $_SESSION['youyax_error'] = 1;
        $t->save();
        $this->redirect("admin" . C('default_url') . "cblock" . C('default_url') . "id" . C('default_url') . $sid . C('static_url'));
    }
    public function admin()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "admin"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "admin order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/admin.html");
    }
    public function purview()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $id       = getparam("id");
        $arr      = $this->find(C('db_prefix') . "admin", "string", "id='" . $id . "'");
        $purviews = unserialize($arr['purview']);
        if (empty($purviews)) {
            $purviews = array();
        }
        $lists = $this->select("select * from " . C('db_prefix') . "small_block order by bid,ssort desc");
        $this->assign('token', $_SESSION['token'])->assign('list', $lists)->assign('arr', $arr)->assign('purviews', $purviews)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/purview.html");
    }
    public function purview2()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $id       = getparam("id");
        $arr      = $this->find(C('db_prefix') . "user_group", "string", "id='" . $id . "'");
        $purviews = unserialize($arr['purview']);
        if (empty($purviews)) {
            $purviews = array();
        }
        $lists = $this->select("select * from " . C('db_prefix') . "small_block order by bid,ssort desc");
        $this->assign('token', $_SESSION['token'])->assign('list', $lists)->assign('arr', $arr)->assign('purviews', $purviews)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/purview2.html");
    }
    public function purview_update()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $array = array();
        for ($k = 0; $k < count($_POST['ck']); $k++) {
            $array[] = intval($_POST['ck'][$k]);
        }
        $data['purview'] = serialize($array);
        $this->save($data, C('db_prefix') . "admin", "id='" . intval($_POST['id']) . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "purview" . C('default_url') . "id" . C('default_url') . intval($_POST['id']) . C('static_url'));
    }
    public function purview_update2()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $array = array();
        for ($k = 0; $k < count($_POST['ck']); $k++) {
            $array[] = intval($_POST['ck'][$k]);
        }
        $data['purview'] = serialize($array);
        $this->save($data, C('db_prefix') . "user_group", "id='" . intval($_POST['id']) . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "purview2" . C('default_url') . "id" . C('default_url') . intval($_POST['id']) . C('static_url'));
    }
    public function admin_add()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $user = addslashes(htmlspecialchars($_POST["admin"], ENT_QUOTES, "UTF-8"));
        $pass = md5(addslashes(htmlspecialchars($_POST["pass"], ENT_QUOTES, "UTF-8")));
        if (empty($user) || empty($pass) || empty($_POST['ac'])) {
            $_SESSION['youyax_error'] = 2;
        } else {
            if ($_POST['ac'] == "add" &&  !($this->find(C('db_prefix') . "admin", "string", "user='" . $user . "'"))) {
                $t          = T(C('db_prefix') . "admin");
                $t->user    = $user;
                $t->pass    = $pass;
                $t->isAdmin = 1;
                $t->add();
                $_SESSION['youyax_error'] = 1;
            } elseif ($_POST['ac'] == "update") {
                if ($this->find(C('db_prefix') . "admin", "string", "user='" . $user . "'")) {
                    $data['pass'] = $pass;
                    $this->save($data, C('db_prefix') . "admin", "user='" . $user . "'");
                    $_SESSION['youyax_error'] = 1;
                } else {
                    $_SESSION['youyax_error'] = 2;
                }
            } else {
                $_SESSION['youyax_error'] = 2;
            }
        }
        $this->redirect("admin" . C('default_url') . "admin" . C('static_url'));
    }
    public function admin_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "admin");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "admin" . C('static_url'));
    }
    public function admin_depose()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "admin");
        $t->find($id);
        $t->delete();
        $data['title'] = '';
        $this->save($data, C('db_prefix') . "user", "user='" . $t->user . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "admin" . C('static_url'));
    }
    public function mark1()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "mark1"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "mark1 order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->assign('site', C('SITE'))->display("admin/mark1.html");
    }
    public function mark1_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "mark1");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "mark1" . C('static_url'));
    }
    public function mark2()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "mark2"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "mark2 order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('shtml', C('static_url'))->assign('url', C('default_url'))->assign('site', C('SITE'))->display("admin/mark2.html");
    }
    public function mark2_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "mark2");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "mark2" . C('static_url'));
    }
    public function reply()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "reply where id2!=1"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "reply  where id2!=1 order by id2 desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('list', $list)->assign('page', $showx)->assign('site', C('SITE'))->assign('db_prefix', C('db_prefix'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/reply.html");
    }
    public function reply_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id2");
        if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
            w("delPostPicWidget")->judge_reply($id);
        }
        $this->delete(C('db_prefix') . "reply", "id2=" . $id);
        $this->delete(C('db_prefix') . "mark2", "rid='" . $id . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "reply" . C('static_url'));
    }
    public function talk()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $_SESSION['talk_query_parentid'] = '';
        $_SESSION['talk_query_user']     = '';
        $mix                             = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "talk"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "talk order by id desc");
        $list   = $this->select($sql);
        $this->assign('list', $list)->assign('page', $showx);
        $data1 = $this->select("select * from " . C('db_prefix') . "small_block");
        $this->assign('token', $_SESSION['token'])->assign("data1", $data1)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/talk.html");
    }
    public function talk_query()
    {
    	if($_SESSION['token']!=$_POST['token'] && ($_GET["page"] == "" || $_GET["page"] == null)){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token'] = md5(microtime(true));
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $parentid = addslashes($_POST['parentid']);
        $user     = addslashes($_POST['user']);
        if (empty($parentid) && empty($user)) {
            $parentid = $_SESSION['talk_query_parentid'];
            $user     = $_SESSION['talk_query_user'];
        } else {
            $_SESSION['talk_query_parentid'] = $parentid;
            $_SESSION['talk_query_user']     = $user;
        }
        if (empty($parentid)) {
            $tmp = " where 1=1";
            if (!empty($user)) {
                $tmp .= " and zuozhe='" . $user . "'";
            }
        } else {
            if ($parentid == 'invalid') {
                $arr = $this->select("select id from " . C('db_prefix') . "small_block", "id");
                foreach ($arr as $v) {
                    $arr[] = intval($v + 10000);
                }
                $tmp = "where parentid not in (" . implode(",", $arr) . ")";
            } else {
                $tmp = "where parentid=" . intval($parentid);
            }
            if (!empty($user)) {
                $tmp .= " and zuozhe='" . $user . "'";
            }
        }
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "talk " . $tmp));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "talk " . $tmp . " order by id desc");
        $list   = $this->select($sql);
        $this->assign('list', $list)->assign('page', $showx);
        $data1 = $this->select("select * from " . C('db_prefix') . "small_block");
        $this->assign('token', $_SESSION['token'])->assign("data1", $data1)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/talk.html");
    }
    public function talk_update()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = intval($_POST['update_txt_hidden']);
        $t  = T(C('db_prefix') . "talk");
        $t->find($id);
        $t->title = addslashes(htmlspecialchars($_POST['update_txt'.$id], ENT_QUOTES, "UTF-8"));
        $t->save();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk" . C('static_url'));
    }
    public function movetalk()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = intval($_POST['id']);
        $t  = T(C('db_prefix') . "talk");
        $t->find($id);
        $t->parentid = intval($_POST['parentid']);
        $t->save();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk" . C('static_url'));
    }
    public function talk_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
            w("delPostPicWidget")->judge($id);
        }
        $t = T(C('db_prefix') . "talk");
        $t->find($id);
        $t->delete();
        $this->delete(C('db_prefix') . "mark1", "tid='" . $id . "'");
        $this->delete(C('db_prefix') . "mark2", "tid='" . $id . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk" . C('static_url'));
    }
    public function talk_del_all()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        for ($k = 0; $k < count($_POST['cb']); $k++) {
            $id = intval($_POST['cb'][$k]);
            if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
                w("delPostPicWidget")->judge($id);
            }
            $t = T(C('db_prefix') . "talk");
            $t->find($id);
            $t->delete();
            $this->delete(C('db_prefix') . "mark1", "tid='" . $id . "'");
            $this->delete(C('db_prefix') . "mark2", "tid='" . $id . "'");
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk_query" . C('static_url'));
    }
    public function user()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx          = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "user"));
        $fenye           = new Fenye($countx['count'], 40);
        $showx           = $fenye->show();
        $showx           = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql             = $fenye->listcon("select * from " . C('db_prefix') . "user order by id desc");
        $list            = $this->select($sql);
        $user_group_data = $this->select("select * from " . C('db_prefix') . "user_group order by id desc");
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('user_group_data', $user_group_data)->assign('page', $showx)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/user.html");
    }
    public function do_s_user()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token'] = md5(microtime(true));
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx          = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "user"));
        $fenye           = new Fenye($countx['count'], 40);
        $showx           = $fenye->show();
        $showx           = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql             = $fenye->listcon("select * from " . C('db_prefix') . "user where user='" . addslashes($_POST['suser']) . "' order by id desc");
        $list            = $this->select($sql);
        $user_group_data = $this->select("select * from " . C('db_prefix') . "user_group order by id desc");
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('user_group_data', $user_group_data)->assign('page', $showx)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/user.html");
    }
    public function user_set_group()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = intval($_POST['id']);
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $t->user_group = intval($_POST['user_group_data']);
        $t->save();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_forbid()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $t->status = 2;
        $t->save();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_appoint()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $t->title = 'ico_title.png';
        $t->save();
        if(!$this->find(C('db_prefix') . "admin","string","user='".$t->user."'")){
	        $at          = T(C('db_prefix') . "admin");
	        $at->user    = $t->user;
	        $at->pass    = $t->pass;
	        $at->isAdmin = 0;
	        $at->add();
	      }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_depose()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $t->title = '';
        $t->save();
        if(!$this->find(C('db_prefix') . "admin","string","user='".$t->user."' and isAdmin=1")){
        	$this->delete(C('db_prefix') . "admin", "user='" . $t->user . "'");
      	}
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_empty()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        if (is_exist_widget("delPostPicWidget") && is_active_widget("delPostPicWidget")) {
            w("delPostPicWidget")->judge($id);
        }
        $t = T(C('db_prefix') . "user");
        $t->find($id);
        $user = $t->user;
        $this->delete(C('db_prefix') . "talk", "zuozhe='" . $user . "'");
        $this->delete(C('db_prefix') . "reply", "zuozhe1='" . $user . "'");
        $this->delete(C('db_prefix') . "mark1", "marker='" . $user . "'");
        $this->delete(C('db_prefix') . "mark2", "marker='" . $user . "'");
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_unfor()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $openid = $t->openid;
        if (!empty($openid)) {
            $t->status = 3;
        } else {
            $t->status = 1;
        }
        $t->save();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function user_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "user");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function setting()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $site_config = require("./Conf/site.config.php");
        $this->assign('token', $_SESSION['token'])->assign('site_config', $site_config)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/setting.html");
    }
    public function dosetting()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $site_config = require("./Conf/site.config.php");
        if (!empty($_POST['title'])) {
            $site_config['site_title']        = htmlspecialchars($_POST['title'], ENT_QUOTES, "UTF-8");
            $site_config['site_title_mobile'] = htmlspecialchars($_POST['title_mobile'], ENT_QUOTES, "UTF-8");
        }
        $site_config['site_keywords']    = htmlspecialchars($_POST['keywords'], ENT_QUOTES, "UTF-8");
        $site_config['site_description'] = htmlspecialchars($_POST['description'], ENT_QUOTES, "UTF-8");
        $site_config['site_logo']        = htmlspecialchars($_POST['logo'], ENT_QUOTES, "UTF-8");
        $site_config['site_foot']        = str_replace(array('"','<script>','</script>'),array("'","&lt;script&gt;","&lt;/script&gt;"),$_POST['foot']);
        $file                            = "<?php return " . var_export($site_config, true) . "; ?>";
        file_put_contents("./Conf/site.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "setting" . C('static_url'));
    }
    public function mailset()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $config = require("./Conf/config.php");
        $this->assign('config', $config);
        $user_group_data = $this->select("select * from " . C('db_prefix') . "user_group order by id desc");
        $mail_config     = require("./Conf/mail.config.php");
        $this->assign('token', $_SESSION['token'])->assign('mail_config', $mail_config)->assign('user_group_data', $user_group_data)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/mailset.html");
    }
    public function domailset()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $config                          = require("./Conf/config.php");
        $config['register_mode']         = intval($_POST['reg']);
        $config['default_user_group']    = intval($_POST['default_user_group']);
        $config['not_log_in_user_group'] = intval($_POST['not_log_in_user_group']);
        $file                            = "<?php return " . var_export($config, true) . "; ?>";
        file_put_contents("./Conf/config.php", $file, LOCK_EX);
        if ($_POST['reg'] == 1) {
            $mail_config                  = require("./Conf/mail.config.php");
            $mail_config['mail_Host']     = htmlspecialchars($_POST['host'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_Username'] = htmlspecialchars($_POST['huser'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_Password'] = htmlspecialchars($_POST['hpass'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_From']     = htmlspecialchars($_POST['ufrom'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_FromName'] = htmlspecialchars($_POST['uname'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_Subject']  = htmlspecialchars($_POST['utitle'], ENT_QUOTES, "UTF-8");
            $mail_config['mail_Body']     = htmlspecialchars($_POST['ucon'], ENT_QUOTES, "UTF-8");
            $file                         = "<?php return " . var_export($mail_config, true) . "; ?>";
            file_put_contents("./Conf/mail.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "mailset" . C('static_url'));
    }
    public function seoset()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $config = require("./Conf/config.php");
        $this->assign('token', $_SESSION['token'])->assign('config', $config)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/seoset.html");
    }
    public function doseoset()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $config            = require("./Conf/config.php");
        $config['seo_set'] = htmlspecialchars($_POST['seostatus'], ENT_QUOTES, "UTF-8");
        $file              = "<?php return " . var_export($config, true) . "; ?>";
        file_put_contents("./Conf/config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "seoset" . C('static_url'));
    }
    public function custom_title()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $config = require("./Conf/custom_title.config.php");
        $this->assign('token', $_SESSION['token'])->assign('config', $config)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/custom_title.html");
    }
    public function docustom_title()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $title = $_POST['title'];
        $min   = $_POST['min'];
        $max   = $_POST['max'];
        $file  = '';
        foreach ($title as $k => $v) {
            $file .= "array('title'=>'" . htmlspecialchars($v, ENT_QUOTES, "UTF-8") . "','min'=>'" . intval($min[$k]) . "','max'=>'" . $max[$k] . "'),";
        }
        $file = "array(" . $file . ")";
        $file = "<?php return " . $file . "; ?>";
        file_put_contents("./Conf/custom_title.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "custom_title" . C('static_url'));
    }
    public function friend_url_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $config = require("./Conf/friend_url.config.php");
        $this->assign('token', $_SESSION['token'])->assign('config', $config)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/friend_url_set.html");
    }
    public function do_friend_url()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $title = $_POST['title'];
        $url   = $_POST['url'];
        $file  = '';
        foreach ($title as $k => $v) {
            $file .= "array('title'=>'" . htmlspecialchars($v, ENT_QUOTES, "UTF-8") . "','url'=>'" . htmlspecialchars($url[$k], ENT_QUOTES, "UTF-8") . "'),";
        }
        $file = "array(" . $file . ")";
        $file = "<?php return " . $file . "; ?>";
        file_put_contents("./Conf/friend_url.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "friend_url_set" . C('static_url'));
    }
    public function talk_vote()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "vote"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "vote order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/talk_vote.html");
    }
    public function talk_vote_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "vote");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk_vote" . C('static_url'));
    }
    public function clear_vote()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $sql = "delete from " . C('db_prefix') . "vote where rid not in(select id from " . C('db_prefix') . "talk)";
        mysql_query($sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "talk_vote" . C('static_url'));
    }
    public function vote_ip()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "vote_ips"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "vote_ips order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/vote_ip.html");
    }
    public function vote_ip_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id = getparam("id");
        $t  = T(C('db_prefix') . "vote_ips");
        $t->find($id);
        $t->delete();
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "vote_ip" . C('static_url'));
    }
    public function menu_cn_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        if (getparam("sp") == 'en') {
            $mc = require("./Conf/menu_en.config.php");
            $this->assign('sp', 'en');
        } else {
            $mc = require("./Conf/menu.config.php");
            $this->assign('sp', 'cn');
        }
        $this->assign('token', $_SESSION['token'])->assign('mc', $mc)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/menu_cn_set.html");
    }
    public function do_menu_cn_set()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $array = array();
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        foreach ($_POST as $k => $v) {
            if ($k == 'x' || $k == 'y') {
                unset($_POST[$k]);
            }
            $num = preg_match_all('/-/', $k, $match);
            if ($num == 1) {
                $tmp = explode("-", $k);
                if (empty($array[$tmp[0]]['seclists'])) {
                    $array[$tmp[0]]['seclists'] = array();
                }
                $array[$tmp[0]][$tmp[1]] = htmlspecialchars($v, ENT_QUOTES, "UTF-8");
            }
            if ($num == 2) {
                $tmp = explode("-", $k);
                if (empty($array[$tmp[0]]['seclists'])) {
                    $array[$tmp[0]]['seclists'] = array();
                }
                $array[$tmp[0]]['seclists'][$tmp[1]][$tmp[2]] = htmlspecialchars($v, ENT_QUOTES, "UTF-8");
            }
        }
        $file = "<?php return " . var_export($array, true) . "; ?>";
        if ($_POST['sp'] == 'cn') {
            file_put_contents("./Conf/menu.config.php", $file, LOCK_EX);
        }
        if ($_POST['sp'] == 'en') {
            file_put_contents("./Conf/menu_en.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "menu_cn_set" . C('default_url') . "sp" . C('default_url') . $_POST['sp'] . C('static_url'));
    }
    public function do_menu_cn_set_delone()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $array = array();
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        if (getparam("sp") == 'cn') {
            $mc = require("./Conf/menu.config.php");
        }
        if (getparam("sp") == 'en') {
            $mc = require("./Conf/menu_en.config.php");
        }
        $oid = getparam("oid");
        foreach ($mc as $k => $v) {
            if ($k == $oid) {
                unset($mc[$k]);
            } else {
                $array[] = $v;
            }
        }
        $file = "<?php return " . var_export($array, true) . "; ?>";
        if (getparam("sp") == 'cn') {
            file_put_contents("./Conf/menu.config.php", $file, LOCK_EX);
        }
        if (getparam("sp") == 'en') {
            file_put_contents("./Conf/menu_en.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $sp                       = getparam("sp");
        $this->redirect("admin" . C('default_url') . "menu_cn_set" . C('default_url') . "sp" . C('default_url') . $sp . C('static_url'));
    }
    public function do_menu_cn_set_deltwo()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $array = array();
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        if (getparam("sp") == 'cn') {
            $mc = require("./Conf/menu.config.php");
        }
        if (getparam("sp") == 'en') {
            $mc = require("./Conf/menu_en.config.php");
        }
        $oid = getparam("oid");
        $tid = getparam("tid");
        foreach ($mc as $k => $v) {
            if (!empty($v['seclists'])) {
                if ($k == $oid) {
                    foreach ($v['seclists'] as $kk => $vv) {
                        if ($kk == $tid) {
                            $array[$k]['title'] = $v['title'];
                            $array[$k]['url']   = $v['url'];
                            unset($mc[$k]['seclists'][$kk]);
                            if (!is_array($array[$k]['seclists'])) {
                                $array[$k]['seclists'] = array();
                            }
                        } else {
                            $array[$k]['title']      = $v['title'];
                            $array[$k]['url']        = $v['url'];
                            $array[$k]['seclists'][] = $vv;
                        }
                    }
                } else {
                    $array[$k] = $v;
                }
            } else {
                $array[$k]['title']    = $v['title'];
                $array[$k]['url']      = $v['url'];
                $array[$k]['seclists'] = array();
            }
        }
        $file = "<?php return " . var_export($array, true) . "; ?>";
        if (getparam("sp") == 'cn') {
            file_put_contents("./Conf/menu.config.php", $file, LOCK_EX);
        }
        if (getparam("sp") == 'en') {
            file_put_contents("./Conf/menu_en.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $sp                       = getparam("sp");
        $this->redirect("admin" . C('default_url') . "menu_cn_set" . C('default_url') . "sp" . C('default_url') . $sp . C('static_url'));
    }
    public function do_menu_cn_set_addone()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $array = array();
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        if ($_POST['sp2'] == 'cn') {
            $mc = require("./Conf/menu.config.php");
        }
        if ($_POST['sp2'] == 'en') {
            $mc = require("./Conf/menu_en.config.php");
        }
        $array['title']    = htmlspecialchars($_POST['oname'], ENT_QUOTES, "UTF-8");
        $array['url']      = htmlspecialchars($_POST['ourl'], ENT_QUOTES, "UTF-8");
        $array['seclists'] = array();
        $mc[]              = $array;
        $file              = "<?php return " . var_export($mc, true) . "; ?>";
        if ($_POST['sp2'] == 'cn') {
            file_put_contents("./Conf/menu.config.php", $file, LOCK_EX);
        }
        if ($_POST['sp2'] == 'en') {
            file_put_contents("./Conf/menu_en.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "menu_cn_set" . C('default_url') . "sp" . C('default_url') . $_POST['sp2'] . C('static_url'));
    }
    public function do_menu_cn_set_addtwo()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        $array = array();
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        if ($_POST['sp2'] == 'cn') {
            $mc = require("./Conf/menu.config.php");
        }
        if ($_POST['sp2'] == 'en') {
            $mc = require("./Conf/menu_en.config.php");
        }
        $array['title']                  = htmlspecialchars($_POST['turl'], ENT_QUOTES, "UTF-8");
        $array['url']                    = htmlspecialchars($_POST['turl'], ENT_QUOTES, "UTF-8");
        $mc[$_POST['oid']]['seclists'][] = $array;
        $file                            = "<?php return " . var_export($mc, true) . "; ?>";
        if ($_POST['sp2'] == 'cn') {
            file_put_contents("./Conf/menu.config.php", $file, LOCK_EX);
        }
        if ($_POST['sp2'] == 'en') {
            file_put_contents("./Conf/menu_en.config.php", $file, LOCK_EX);
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "menu_cn_set" . C('default_url') . "sp" . C('default_url') . $_POST['sp2'] . C('static_url'));
    }
    public function filter_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $config = require("./Conf/filter.config.php");
        $this->assign('token', $_SESSION['token'])->assign('config', var_export($config, true))->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/filter_set.html");
    }
    public function do_filter()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $filter = $_POST['filter_area'];
        $file   = "<?php return " . stripslashes($filter) . "; ?>";
        file_put_contents("./Conf/filter.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "filter_set" . C('static_url'));
    }
    public function ads_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $ads = require("./Conf/ads.config.php");
        $this->assign('token', $_SESSION['token'])->assign('ads', $ads)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/ads_set.html");
    }
    public function do_ads()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $filter = htmlspecialchars($_POST['ads'], ENT_QUOTES, "UTF-8");
        $file   = "<?php return '" . htmlspecialchars($filter, ENT_QUOTES, "UTF-8") . "'; ?>";
        file_put_contents("./Conf/ads.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "ads_set" . C('static_url'));
    }
    public function plugin_view()
    {
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $array = array();
        $dir   = dir("Plugin");
        while (($file = $dir->read()) !== false) {
            if ($file != "." && $file != ".." && preg_match_all("/Widget\.php/", $file, $tmp)) {
                $array[] = $file;
            }
        }
        $dir->close();
        if (count($array) != count(array_unique($array))) {
            $this->assign("Tip", "插件名不能重复!")->display("Public/plugin_error.html");
            exit;
        } else {
            $array2 = array();
            $data   = $this->select("select * from " . C('db_prefix') . "plugin where status=1");
            foreach ($data as $v) {
                $array2[] = $v['name'];
            }
            $this->assign('token', $_SESSION['token'])->assign('plu_arr', $array)->assign('plu_in_arr', $array2)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/plugin_view.html");
        }
    }
    public function mix_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        $this->assign('token', $_SESSION['token'])->assign('mix', $mix)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/mix_set.html");
    }
    public function do_mix()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $config = require("./Conf/mix.config.php");
        if (!is_numeric($_POST['list_per']) || !is_numeric($_POST['admin_count_num'])) {
            $_SESSION['youyax_error'] = 2;
            $this->redirect("admin" . C('default_url') . "mix_set" . C('static_url'));
        }
        if (!empty($_FILES["file"]['tmp_name'])) {
            if (($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/png")) {
                if ($_FILES["file"]["error"] > 0) {
                    $_SESSION['youyax_error'] = 2;
                    $this->redirect("admin" . C('default_url') . "mix_set" . C('static_url'));
                } else {
                    if (file_exists("./Public/images/" . $_FILES["file"]["name"])) {
                        echo '<script>alert("文件名已经存在了");</script>';
                        $_SESSION['youyax_error'] = 2;
                        $this->redirect("admin" . C('default_url') . "mix_set" . C('static_url'));
                    } else {
                        move_uploaded_file($_FILES["file"]["tmp_name"], "./Public/images/" . $_FILES["file"]["name"]);
                        @unlink("." . $config['home_back_bg']);
                    }
                }
            } else {
                echo '<script>alert("无效文件");</script>';
                $_SESSION['youyax_error'] = 2;
                $this->redirect("admin" . C('default_url') . "mix_set" . C('static_url'));
            }
        }
        $config['list_per']        = intval($_POST['list_per']);
        $config['fenye_style']     = intval($_POST['fenye_style']);
        $config['is_prevent_reg']  = ($_POST['is_prevent_reg'] == 1) ? true : false;
        $config['prevent_reg_num'] = intval($_POST['prevent_reg_num']);
        $config['is_limit_time']   = ($_POST['is_limit_time'] == 1) ? true : false;
        $config['limit_time']      = intval($_POST['limit_time']);
        $config['bid_init']      = intval($_POST['bid_init']);
        $config['admin_count_num'] = intval($_POST['admin_count_num']);
        if (!empty($_FILES["file"]['tmp_name'])) {
            $config['home_back_bg'] = "/Public/images/" . $_FILES["file"]["name"];
        }
        $file = "<?php return " . var_export($config, true) . "; ?>";
        file_put_contents("./Conf/mix.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "mix_set" . C('static_url'));
    }
    public function ads_poll_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $poll = require("./Conf/ads_poll.config.php");
        $this->assign('token', $_SESSION['token'])->assign('poll', $poll)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/ads_poll_set.html");
    }
    public function do_ads_poll_set()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $img   = $_POST['img'];
        $title = $_POST['title'];
        $url   = $_POST['url'];
        $ord   = $_POST['ord'];
        $file  = '';
        foreach ($img as $k => $v) {
            $file .= "array('img'=>'" . htmlspecialchars($v, ENT_QUOTES, "UTF-8") . "','title'=>'" . htmlspecialchars($title[$k], ENT_QUOTES, "UTF-8") . "','url'=>'" . htmlspecialchars($url[$k], ENT_QUOTES, "UTF-8") . "','ord'=>'" . intval($ord[$k]) . "'),";
        }
        $file = "array(" . $file . ")";
        $file = "<?php return " . $file . "; ?>";
        file_put_contents("./Conf/ads_poll.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "ads_poll_set" . C('static_url'));
    }
    public function placard_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $placard = require("./Conf/placard_set.config.php");
        $this->assign('token', $_SESSION['token'])->assign('placard', $placard)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/placard_set.html");
    }
    public function do_placard_set()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $title = $_POST['title'];
        $url   = $_POST['url'];
        $ord   = $_POST['ord'];
        $file  = '';
        foreach ($title as $k => $v) {
            $file .= "array('title'=>'" . htmlspecialchars($title[$k], ENT_QUOTES, "UTF-8") . "','url'=>'" . htmlspecialchars($url[$k], ENT_QUOTES, "UTF-8") . "','ord'=>'" . intval($ord[$k]) . "'),";
        }
        $file = "array(" . $file . ")";
        $file = "<?php return " . $file . "; ?>";
        file_put_contents("./Conf/placard_set.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "placard_set" . C('static_url'));
    }
    public function qqset()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $qq = require("./Conf/qq.config.php");
        $this->assign('token', $_SESSION['token'])->assign('qq', $qq)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/qq_set.html");
    }
    public function do_qqset()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $qq               = require("./Conf/qq.config.php");
        $qq['app_id']     = htmlspecialchars($_POST['app_id'], ENT_QUOTES, "UTF-8");
        $qq['app_secret'] = htmlspecialchars($_POST['app_secret'], ENT_QUOTES, "UTF-8");
        $file             = "<?php return " . var_export($qq, true) . "; ?>";
        file_put_contents("./Conf/qq.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "qqset" . C('static_url'));
    }
    public function vertical_set()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $placard = require("./Conf/vertical_set.config.php");
        $this->assign('token', $_SESSION['token'])->assign('placard', $placard)->assign('site', C('SITE'))->assign('shtml', C('static_url'))->assign('url', C('default_url'))->display("admin/vertical_set.html");
    }
    public function do_vertical_set()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $title = $_POST['title'];
        $url   = $_POST['url'];
        $ord   = $_POST['ord'];
        $file  = '';
        foreach ($title as $k => $v) {
            $file .= "array('title'=>'" . htmlspecialchars($title[$k], ENT_QUOTES, "UTF-8") . "','url'=>'" . htmlspecialchars($url[$k], ENT_QUOTES, "UTF-8") . "','ord'=>'" . intval($ord[$k]) . "'),";
        }
        $file = "array(" . $file . ")";
        $file = "<?php return " . $file . "; ?>";
        file_put_contents("./Conf/vertical_set.config.php", $file, LOCK_EX);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "vertical_set" . C('static_url'));
    }
    public function user_group()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "user_group"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "user_group order by id asc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/user_group.html");
    }
    public function user_group_add()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $name = addslashes(htmlspecialchars($_POST["name"], ENT_QUOTES, "UTF-8"));
        if (!empty($name)) {
            $t       = T(C('db_prefix') . "user_group");
            $t->name = $name;
            $t->add();
            $_SESSION['youyax_error'] = 1;
        } else {
            $_SESSION['youyax_error'] = 2;
            echo '<script>alert("名称必填项");</script>';
        }
        $this->redirect("admin" . C('default_url') . "user_group" . C('static_url'));
    }
    public function user_group_mod()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id   = intval($_POST["id"]);
        $name = addslashes(htmlspecialchars($_POST["name"], ENT_QUOTES, "UTF-8"));
        if (empty($name)) {
            $_SESSION['youyax_error'] = 2;
        } else {
            $t = T(C('db_prefix') . "user_group");
            $t->find($id);
            $t->name                  = $name;
            $_SESSION['youyax_error'] = 1;
            $t->save();
        }
        $this->redirect("admin" . C('default_url') . "user_group" . C('static_url'));
    }
    public function user_group_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id  = getparam("id");
        $sql = "delete from " . C('db_prefix') . "user_group where id=" . $id;
        mysql_query($sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user_group" . C('static_url'));
    }
    public function jubao()
    {
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $mix = require("./Conf/mix.config.php");
        require("./ORG/Page/" . $mix['fenye_style'] . "/Fenye.class.php");
        $countx = mysql_fetch_array(mysql_query("select count(*) as count from " . C('db_prefix') . "jubao"));
        $fenye  = new Fenye($countx['count'], 40);
        $showx  = $fenye->show();
        $showx  = implode("<span style='width:2px;display:inline-block;'></span>", $showx);
        $sql    = $fenye->listcon("select * from " . C('db_prefix') . "jubao order by id desc");
        $list   = $this->select($sql);
        $this->assign('token', $_SESSION['token'])->assign('data', $list)->assign('page', $showx)->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/jubao.html");
    }
    public function jubao_del()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
        if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id  = getparam("id");
        $sql = "delete from " . C('db_prefix') . "jubao where id=" . $id;
        mysql_query($sql);
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "jubao" . C('static_url'));
    }
    public function bid_set()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $id  = intval($_POST['id']);
        $arr = $this->find(C('db_prefix') . "user", 'string', "id='" . $id . "'");
        if ($_POST['bid_op'] == 1) {
            mysql_query("update " . C('db_prefix') . "user set bid=bid+" . intval($_POST['bid_text']) . " where  id='" . $id . "'");
            CommonAction::admin_send($arr['user'], '管理员奖励了你 ' . intval($_POST['bid_text']) . ' 金币，操作原因: '.(empty($_POST['bid_reason'])?'无':htmlspecialchars($_POST["bid_reason"], ENT_QUOTES, "UTF-8")));
        }
        if ($_POST['bid_op'] == 2) {
            mysql_query("update " . C('db_prefix') . "user set bid=bid-" . intval($_POST['bid_text']) . " where  id='" . $id . "'");
            CommonAction::admin_send($arr['user'], '管理员处罚了你 ' . intval($_POST['bid_text']) . ' 金币，操作原因: '.(empty($_POST['bid_reason'])?'无':htmlspecialchars($_POST["bid_reason"], ENT_QUOTES, "UTF-8")));
        }
        $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "user" . C('static_url'));
    }
    public function block_set_purview()
    {
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $user_group       = array();
        $user_group_query = mysql_query("select * from " . C('db_prefix') . "user_group");
        while ($user_group_arr = mysql_fetch_array($user_group_query)) {
            $tmp            = array();
            $tmp['id']      = intval($user_group_arr['id']);
            $tmp['name']    = addslashes($user_group_arr['name']);
            $tmp['purview'] = unserialize($user_group_arr['purview']);
            $user_group[]   = $tmp;
        }
        $str = '';
        foreach ($user_group as $v) {
            $str .= '<input type="checkbox" name="purview[]" ' . (in_array(intval($_POST['data']), $v['purview']) == true ? "checked" : "") . ' value="' . $v['id'] . '">' . $v['name'] . '&nbsp;';
        }
        echo $str;
    }
    public function block_set_purview_do()
    {
    	if($_SESSION['token']!=$_POST['token']){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $user_groups      = $_POST['purview'];
        $sid              = intval($_POST['id']);
        $jumpurl          = addslashes(htmlspecialchars($_POST["jumpurl"], ENT_QUOTES, "UTF-8"));
        $user_group_query = mysql_query("select * from " . C('db_prefix') . "user_group");
        while ($user_group_arr = mysql_fetch_array($user_group_query)) {
            $purview = array();
            $arr     = $this->find(C('db_prefix') . "user_group", "string", "id='" . $user_group_arr['id'] . "'");
            if (in_array($user_group_arr['id'], $user_groups)) {
                $purview = unserialize($arr['purview']);
                if (!in_array($sid, $purview)) {
                    $purview[] = $sid;
                }
                $data['purview'] = serialize($purview);
                $this->save($data, C('db_prefix') . "user_group", "id='" . $user_group_arr['id'] . "'");
            } else {
                $purview = unserialize($arr['purview']);
                if (in_array($sid, $purview)) {
                    foreach ($purview as $o => $p) {
                        if ($p == $sid) {
                            unset($purview[$o]);
                        }
                    }
                }
                $data['purview'] = serialize($purview);
                $this->save($data, C('db_prefix') . "user_group", "id='" . $user_group_arr['id'] . "'");
            }
        }
        $_SESSION['youyax_error'] = 1;
        echo '<script>window.location.href="' . $jumpurl . '";</script>';
    }
    public function backupSQL()
    {
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $_SESSION['token'] = md5(microtime(true));
        $conf = require("./Conf/config.php");
    	   $this->assign('conf', $conf)->assign('token', $_SESSION['token'])->assign('shtml', C('static_url'))->assign('site', C('SITE'))->assign('url', C('default_url'))->display("admin/backupSQL.html");	
    }
    public function optimizeSQL()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        mysql_query("optimize table `".getparam("name")."`");
		$_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "backupSQL" . C('static_url'));
    }
    public function dobackupSQL()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $config = require("./Conf/config.php");
		$db_server = $config['db_host']; // your MySQL server - localhost will normally suffice
		$db = $config['db_name']; // your MySQL database name
		$mysql_username = $config['db_user'];  // your MySQL username
		$mysql_password = $config['db_pwd'];  // your MySQL password
		require("./ext_public/phpmysqlautobackup/run.php");
		$_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "backupSQL" . C('static_url'));
    }
    public function download_backup()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $file_name = getparam("file");
        $file_name = str_replace(array("./","../"),"",$file_name);
        $file_dir = "./ext_public/phpmysqlautobackup/backups/";
        if(file_exists($file_dir.$file_name)){
	       header("Content-Type:text/plain");  
		   header("Accept-Ranges:bytes");
		   header("Content-Disposition:attachment;filename=".$file_name);
		   $file = fopen($file_dir.$file_name,"r");
		   echo fread($file,filesize($file_dir.$file_name));
		   fclose($file);
	 	}
    }
    public function delete_backup()
    {
    	if($_SESSION['token']!=getparam("token")){
    		$this->assign("code", "操作错误!")->assign("msg", "可能处于恶意操作中")->display("Public/exception.html");exit;
    	}else{
    		$_SESSION['token']='';	
    	}
    	if (empty($_SESSION['youyax_admin'])) {
            $this->redirect("admin" . C('default_url') . "login" . C('static_url'));
        }
        $file_name = getparam("file");
        $file_name = str_replace(array("./","../"),"",$file_name);
	    $file_dir = "./ext_public/phpmysqlautobackup/backups/";
	   if(file_exists($file_dir.$file_name))
 	   		@unlink($file_dir.$file_name);
 	   $_SESSION['youyax_error'] = 1;
        $this->redirect("admin" . C('default_url') . "backupSQL" . C('static_url'));
    }
}
?>