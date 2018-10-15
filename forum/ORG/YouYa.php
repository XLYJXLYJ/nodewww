<?php
/**
 * File Name        :YouYa.php
 * Author             :xujinliang
 * Website           :http://www.youyax.com
 * Description       :The core of the YouYaX for PHP framework class files 
 * 
 数据库连接，选择数据库，类，类方法,loop,list,一维数组解析错，二维数组错，一维数组空，二维数组空,model
 
 */
class YouYaX
{
    public $array; //普通替换
    public $array_array; //一维数组
    public $array_two; //二维数组
    public $config;
    public $lang;
    public $array_url; //url地址栏参数
    public $youyax_url;
    function __construct()
    {
        $this->array       = array();
        $this->array_array = array(
            array()
        );
        $this->array_two   = array();
        $this->array_url   = array();
        $this->config      = require("Conf/config.php");
        $this->config      = array_change_key_case($this->config);
        if ((!empty($this->config['db_host'])) && (!empty($this->config['db_user'])) && (!empty($this->config['db_name']))) {
            $db_host = $this->config['db_host'];
            $db_user = $this->config['db_user'];
            $db_pwd  = $this->config['db_pwd'];
            $db_name = $this->config['db_name'];
            try {
                $con = @mysql_connect($db_host, $db_user, $db_pwd);
                if (!$con)
                    throw new Exception('数据库连接失败！', "300");
            }
            catch (Exception $e) {
                $this->exception($e);
            }
            try {
                $selectdb = @mysql_select_db($db_name, $con);
                if (!$selectdb)
                    throw new Exception('选择数据库失败！', "301");
            }
            catch (Exception $e) {
                $this->exception($e);
            }
            mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
            mysql_query("SET sql_mode=''");
            date_default_timezone_set('Asia/Shanghai');
        }
        if ($this->config['seo_set'] == 'on') {
            $script_name      = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], "/index.php"));
            $this->youyax_url = "http://" . $_SERVER['HTTP_HOST'] . $script_name;
        } else {
            $this->youyax_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        }
        $this->path_info = !empty($_SERVER['PATH_INFO']) ? @$_SERVER['PATH_INFO'] : @$_SERVER['ORIG_PATH_INFO'];
        if (!empty($this->path_info) && ($this->path_info != '/index.php')) {
            $youyax_del_length  = strlen($this->path_info) - strlen($this->config['static_url']) - 1;
            $youyax_url_address = substr($this->path_info, 1, $youyax_del_length);
            $youyax_str         = preg_split("#" . $this->config['default_url'] . "#", $youyax_url_address, -1, PREG_SPLIT_NO_EMPTY);
            $youyax_array_param = array_slice($youyax_str, 2);
            if (sizeof($youyax_array_param) % 2 == 1) {
                $this->exception(null);
            }
            for ($i = 0; $i < sizeof($youyax_array_param); $i = $i + 2) {
                if (is_string($youyax_array_param[$i + 1])) {
                    $this->array_url[$youyax_array_param[$i]] = addslashes($youyax_array_param[$i + 1]);
                }
                if (is_numeric($youyax_array_param[$i + 1])) {
                    $this->array_url[$youyax_array_param[$i]] = intval($youyax_array_param[$i + 1]);
                }
            }
        }
    }
    public function exception($e)
    {
        if (empty($e)) {
            echo file_get_contents("Tpl/Public/exception_url.html");
            exit;
        } else {
            $info = new YouYaX();
            $info->assign('code', "系统异常编号: " . $e->getCode())->assign('msg', $e->getMessage());
            $info->display("Public/exception.html");
            exit;
        }
    }
    public function assign($obj, $quo)
    {
        if (is_array($quo)) {
            if (@is_array($quo[0])) {
                $this->array_two[$obj] = $quo;
            } else {
                $this->array_array[$obj] = $quo;
            }
        } else {
            $key               = $obj;
            $obj               = $quo;
            $this->array[$key] = $obj;
        }
        return $this;
    }
    private function deal($tp)
    {
        if (isset($_COOKIE['youyax_lang']) && in_array($_COOKIE['youyax_lang'],array('en','cn'))) {
            $this->lang                = require("lang/" . $_COOKIE['youyax_lang'] . "/lang.php");
            $this->array_array['lang'] = $this->lang;
        } else {
            if (!empty($this->config['default_language'])) {
                $this->lang                = require("lang/" . $this->config['default_language'] . "/lang.php");
                $this->array_array['lang'] = $this->lang;
            } else {
                $this->lang                = require("lang/cn/lang.php");
                $this->array_array['lang'] = $this->lang;
            }
        }
        if (getparam("l") != "" && getparam("l") != null  && in_array(getparam("l"),array('en','cn'))) {
            $this->lang                = require("lang/" . getparam("l") . "/lang.php");
            $this->array_array['lang'] = $this->lang;
            setcookie("youyax_lang", getparam("l"), time() + 3600 * 24 * 7, "/");
        }
        $txt = file_get_contents($tp);
        //include替换
     $include_bz = true;
     while($include_bz){
        if (preg_match_all("/<\s*include\s+file=\"([^>]*?)\"\s*\/??>/", $txt, $inc)) {
            foreach ($inc[1] as $v) {
                if (file_exists($v)) {
                    $sub = file_get_contents($v);
                    foreach ($inc[0] as $v1) {
                        //区分大小写匹配
                        if (preg_match_all("#" . $v . "#", $v1, $tmp)) {
                            $txt = str_replace($v1, $sub, $txt);
                        }
                    }
                } else {
                    $this->assign("code", "操作错误!")->assign("msg", "include标签解析出错!")->display("Public/exception.html");
                    exit;
                }
            }
        }else{
        		$include_bz = false;	
        }
      }
        //--include替换
        //原样替换
        if (preg_match_all("/<\s*original\s*>\s*(.+?)\s*<\s*\/original\s*>/s", $txt, $match)) {
            $ori = 0;
            foreach ($match[0] as $o0) {
                $match[1][$ori] = htmlspecialchars($match[1][$ori]);
                $match[1][$ori] = str_replace("|", "&#124;", $match[1][$ori]);
                $match[1][$ori] = str_replace("{", "&#123;", $match[1][$ori]);
                $match[1][$ori] = str_replace("}", "&#125;", $match[1][$ori]);
                $txt            = str_replace($match[0][$ori], $match[1][$ori], $txt);
                $ori++;
            }
        }
        //----原样替换
        //注释替换
        if (preg_match_all("/<\s*comments\s*>\s*(.+?)\s*<\s*\/comments\s*>/s", $txt, $match)) {
            foreach ($match[0] as $v) {
                $txt = str_replace($v, '', $txt);
            }
        }
        //----注释替换
        //PHP代码块执行
        /*if(preg_match_all("/<\s*php\s*>\s*(.+?)\s*<\s*\/php\s*>/s", $txt, $p)){
	        $pnum = -1;
	        foreach ($p[1] as $v) {
		        ob_start();
		        $pnum++;
		        eval($v);
		        $out = ob_get_clean();
		        $txt = str_replace($p[0][$pnum], $out, $txt);
	        }
        }*/
        //---PHP代码块执行
        //普通替换
        if (preg_match_all('/\{([^{]*?)}/', $txt, $match)) {
            foreach ($match[0] as $v) {
                $tmp = substr($v, 1, strlen($v) - 2);
                if (array_key_exists($tmp, $this->array)) {
                    $txt = str_replace("{" . $tmp . "}", $this->array[$tmp], $txt);
                }
            }
        }
        //——普通替换
        //单个输出数组值
        if (preg_match_all('/\{[^{$\s]*->[^{}\s]*}/', $txt, $single)) {
            foreach ($single[0] as $v) {
                $tmp = preg_split('/->/', substr($v, 1, strlen($v) - 2), -1, PREG_SPLIT_NO_EMPTY);
                $txt = str_replace($v, $this->array_array[$tmp[0]][$tmp[1]], $txt);
            }
        }
        //数组替换
        if (preg_match_all("/<\s*loop\s*>\s*(.+?)\s*<\s*\/loop\s*>/s", $txt, $match)) {
            try {
                foreach ($match[1] as $n => $r_no_loop) {
                    if (preg_match_all("/<\s*loop\s*>/", $r_no_loop, $tmp)) {
                        throw new Exception(htmlspecialchars("<loop>标签不能嵌套<loop>！"), "304");
                        break;
                    }
                    preg_match_all("/\{(.*)\}/", $r_no_loop, $match2);
                    $real_array = $this->array_array[$match2[1][0]];
                    try {
                        if (!is_array($real_array))
                            throw new Exception(htmlspecialchars("<loop>标签解析出错，仅支持一维数组！"), "306");
                    }
                    catch (Exception $e) {
                        $this->exception($e);
                    }
                    $real_data = '';
                    foreach ($real_array as $v) {
                        $real_data .= str_replace("{" . $match2[1][0] . "}", $v, $r_no_loop);
                    }
                    $r_with_loop = $match[0][$n];
                    if (preg_match_all("#" . $match2[0][0] . "#", $r_with_loop, $tmp))
                        $txt = str_replace($r_with_loop, $real_data, $txt);
                }
            }
            catch (Exception $e) {
                $this->exception($e);
            }
        }
        //--数组替换
        $txt = str_replace("__APP__", $this->youyax_url, $txt);
        //二维数组
        if (preg_match_all("/<\s*list\s*>\s*(.+?)\s*<\s*\/list\s*>/s", $txt, $match)) {
            try {
                foreach ($match[1] as $r_no_list) {
                    if (preg_match_all("/<\s*list\s*>/", $r_no_list, $tmp)) {
                        throw new Exception(htmlspecialchars("<list>标签不能嵌套<list>！"), "305");
                        break;
                    }
                }
            }
            catch (Exception $e) {
                $this->exception($e);
            }
            foreach ($match[1] as $n => $r_no_list) {
                $real_data = '';
                //获取模板标记名称	start
                preg_match_all("/\{(.+?)}/s", $r_no_list, $r_no_list_tmp);
                try {
                    foreach ($r_no_list_tmp[1] as $tmp) {
                        if (!preg_match_all("/\./", $tmp, $tmpp))
                            throw new Exception(htmlspecialchars("<list>标签解析出错，仅支持二维数组！"), "307");
                    }
                }
                catch (Exception $e) {
                    $this->exception($e);
                }
                $str = preg_split('/\./', $r_no_list_tmp[1][0], -1, PREG_SPLIT_NO_EMPTY);
                //获取模板标记名称 end
                if (!empty($this->array_two[$str[0]])) {
                    foreach ($this->array_two[$str[0]] as $real_array) {
                        $final = $r_no_list;
                        foreach ($r_no_list_tmp[1] as $v) {
                            $str1 = preg_split('/\./', $v, -1, PREG_SPLIT_NO_EMPTY);
                            if (preg_match_all("/\|/", $str1[1], $tmp)) {
                                $str1_tmp = explode("|", $str1[1]);
                                $final    = str_replace("{" . $str1[0] . "." . $str1[1] . "}", $str1_tmp[1]($real_array[$str1_tmp[0]]), $final);
                            } else {
                                $final = str_replace("{" . $str1[0] . "." . $str1[1] . "}", $real_array[$str1[1]], $final);
                            }
                        }
                        $real_data .= $final;
                    }
                }
                $txt = str_replace($match[0][$n], $real_data, $txt);
            }
        }
        //--二维数组结束
        //链接替换可以选择性开启，和或符号"|"有干扰
        /*
        $txt=preg_replace("/\<\s*link\s*>/","<a href=",$txt);
        $txt=preg_replace("/\<\s*link\s+target=\'?_blank\'?>/","<a target='_blank' href=",$txt);
        $txt=preg_replace("/(?<!\|)\|(?!\|)/", '>', $txt);
        $txt=preg_replace("/<\s*\/link\s*>/","</a>",$txt);	
        */
        eval("?>" . $txt);
    }
    public function display($tp)
    {
        $tpw = "Tpl/" . $tp;
        $tpm = "Tpl/mobile/" . $tp;
        if (file_exists($tpw)) {
            if (stristr($_SERVER['HTTP_USER_AGENT'], 'android') || stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') || stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
                if (file_exists($tpm)) {
                    $this->deal($tpm);
                } else {
                    $this->assign("Tip", "调用的模板不存在!")->display("Public/no_template.html");
                    exit;
                }
            } else {
                $this->deal($tpw);
            }
        } else {
            $this->assign("Tip", "调用的模板不存在!")->display("Public/no_template.html");
            exit;
        }
    }
    public function redirect($control)
    {
echo <<<EOF
<script>
    	function redirect(url) {
	    if (/MSIE (\d+\.\d+)/.test(navigator.userAgent)){
	        var referLink = document.createElement('a');
	        referLink.href = url;
	        document.body.appendChild(referLink);
	        referLink.click();
	    }
	    else {
	        location.href = url;
	    }
	}
</script>
EOF;
        if ($this->config['seo_set'] == 'on') {
            $script_name = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], "/index.php"));
        } else {
            $script_name = $_SERVER['SCRIPT_NAME'];
        }
        $url = "http://" . $_SERVER['HTTP_HOST'] . $script_name . "/" . $control;
        echo "<script>redirect('" . $url . "');</script>";
        exit;
    }
    /*-----------------------------------------数据库常规查询封装-------------------------------*/
    public function select($sql, $parameter = "")
    {
        $array_param = array();
        $result      = @mysql_query($sql);
        if (empty($parameter)) {
            while ($ar = @mysql_fetch_array($result)) {
                array_push($array_param, $ar);
            }
            return $array_param;
        } else {
            while ($ar = @mysql_fetch_array($result)) {
                array_push($array_param, $ar[$parameter]);
            }
            return $array_param;
        }
    }
    public function add($data = array(), $table)
    {
        mysql_query("set names utf8");
        $sql = "insert into " . $table . "(" . implode(",", array_keys($data)) . ") values('" . implode("','", array_values($data)) . "')";
        mysql_query($sql) or die(mysql_error());
    }
    public function find($table, $ext = "string", $param)
    {
        //在 param 中寻找与给定的正则表达式 pattern 所匹配的子串
        if (preg_match_all("/=/", $param, $tmp)) {
            $sql = "select * from " . $table . " where " . $param;
        } else {
            $param = "id=".intval($param);
            $sql   = "select * from " . $table . " where " . $param;
        }
        $result = mysql_query($sql);
        $num    = mysql_num_rows($result);
        if ($num <= 0) {
            return false;
        } else {
            $arr = mysql_fetch_array($result);
            switch ($ext) {
                case "number":
                    foreach ($arr as $k => $v) {
                        if (is_string($k)) {
                            unset($arr[$k]);
                        }
                    }
                    break;
                case "string":
                    foreach ($arr as $k => $v) {
                        if (is_numeric($k)) {
                            unset($arr[$k]);
                        }
                    }
                    break;
            }
            return $arr;
        }
    }
    public function save($data, $table, $param)
    {
        if (!preg_match_all("/[=><!]/", $param, $tmp))
            $param = "id=".intval($param);
        foreach ($data as $k => $v) {
            $sql = "update " . $table . " set " . $k . "='" . $v . "' where " . $param;
            mysql_query($sql);
        }
    }
    public function delete($table, $param)
    {
        if (!preg_match_all("/[=><!]/", $param, $tmp))
            $param = "id=$param";
        $sql = "delete from " . $table . " where " . $param;
        mysql_query($sql);
    }
    /*-----------------------------------------数据库常规查询封装 end-------------------------------*/
    public function error($param = '')
    {
        $this->display("Public/error.html");
        exit;
    }
    public function success($param = '')
    {
        $this->display("Public/success.html");
        exit;
    }
    public function getparam($param)
    {
        if (in_array($param, array_keys($this->array_url))) {
            return $this->array_url[$param];
        } else {
            return null;
        }
    }
    public function validateTip($param, $_this)
    {
        $_this->assign('Tip', $param);
        $_this->display("Public/validation.html");
        exit;
    }
}
function getparam($param)
{
    $pa = new YouYaX();
    return $pa->getparam($param);
}
function dump($value)
{
    ob_start();
    var_dump($value);
    $out = ob_get_clean();
    echo "<pre>" . $out . "</pre>";
}

/*-----------------------------------------控制器入口类-------------------------------*/
class App
{
    static function run()
    {
        $config    = require("Conf/config.php");
        $path_info = !empty($_SERVER['PATH_INFO']) ? @$_SERVER['PATH_INFO'] : @$_SERVER['ORIG_PATH_INFO'];
        if (empty($path_info) || ($path_info == '/index.php')) {
            if (empty($config['default_action'])) {
                try {
                    if (class_exists("IndexAction"))
                        $app = new IndexAction();
                    else
                        throw new Exception('无法加载模块Index!', "302");
                }
                catch (Exception $e) {
                    call_user_func(array(
                        "YouYaX",
                        'exception'
                    ), $e);
                }
            } else {
                try {
                    if (class_exists($config['default_action'] . "Action")) {
                        $class = $config['default_action'] . "Action";
                        $app   = new $class();
                    } else
                        throw new Exception('无法加载模块' . $config['default_action'], "302");
                }
                catch (Exception $e) {
                    call_user_func(array(
                        "YouYaX",
                        'exception'
                    ), $e);
                }
            }
            try {
                if (method_exists($app, 'index'))
                    $app->index();
                else
                    throw new Exception('无法加载方法index!', "303");
            }
            catch (Exception $e) {
                call_user_func(array(
                    "YouYaX",
                    'exception'
                ), $e);
            }
        } else {
            $str   = preg_split("#" . $config['default_url'] . "#", substr($path_info, 1, strlen($path_info) - strlen($config['static_url']) - 1), -1, PREG_SPLIT_NO_EMPTY);
            $class = $str[0] . "Action";
            try {
                if (class_exists($class))
                    $app = new $class();
                else
                    throw new Exception('无法加载模块' . $str[0] . '!', "302");
            }
            catch (Exception $e) {
                call_user_func(array(
                    "YouYaX",
                    'exception'
                ), $e);
            }
            try {
                if (method_exists($app, $str[1]))
                    $app->$str[1]();
                else
                    throw new Exception('无法加载方法' . $str[1], "303");
            }
            catch (Exception $e) {
                call_user_func(array(
                    "YouYaX",
                    'exception'
                ), $e);
            }
        }
    }
}
/*-----------------------------------------控制器入口类 end-------------------------------*/
function match($value, $field)
{
    $modelvalidation = new validation();
    return $modelvalidation->match($value, $field);
}
/*-----------------------------------------验证模型类-------------------------------*/
class Model
{
    public function match($val, $field)
    {
        header("Content-type: text/html; charset=utf-8");
        if (array_key_exists($field, $this->validation['rules']))
            $rules = $this->validation['rules'][$field];
        try {
            if (empty($rules))
                throw new Exception(htmlspecialchars("验证模型加载异常!"), "310");
        }
        catch (Exception $e) {
            call_user_func(array(
                "YouYaX",
                'exception'
            ), $e);
        }
        foreach ($rules as $k => $v) {
            if ($k == 'required' && $v == 'true') {
                $val = preg_replace("/\s*/", "", $val);
                $val = preg_replace("/&nbsp;*/", "", $val);
                if (empty($val) && $val != '0') {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'maxlength') {
                if (mb_strlen($val, 'utf8') > $v) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'minlength') {
                if (mb_strlen($val, 'utf8') < $v) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'max') {
                if ($val > $v) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'min') {
                if ($val < $v) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'email' && $v == 'true') {
                if (!preg_match('/^\w+@[a-zA-Z]+\.[a-zA-Z]{2,4}$/', $val)) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'digital' && $v == 'true') {
                if (!preg_match('/^\d+$/', $val)) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'letter' && $v == 'true') {
                if (!preg_match('/^[a-zA-Z]+$/', $val)) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
            if ($k == 'alpha' && $v == 'true') {
                if (!preg_match('/^\w+$/', $val)) {
                    call_user_func(array(
                        "YouYaX",
                        'validateTip'
                    ), $this->validation['messages'][$field][$k], new YouYaX());
                }
            }
        }
        return true;
    }
}
/*-----------------------------------------验证模型类 end-------------------------------*/
/*-----------------------------------------数据库对象查询封装-------------------------------*/
class ActiveRecord
{
    public $table;
    public $data;
    public $obj;
    function __construct($table)
    {
        $this->table = $table;
        $this->data  = array();
        $this->obj   = '';
        $this->connect();
    }
    public function connect()
    {
        $config = array_change_key_case(require("Conf/config.php"));
        if ((!empty($config['db_host'])) && (!empty($config['db_user'])) && (!empty($config['db_name']))) {
            $db_host = $config['db_host'];
            $db_user = $config['db_user'];
            $db_pwd  = $config['db_pwd'];
            $db_name = $config['db_name'];
            $con     = mysql_connect($db_host, $db_user, $db_pwd);
            mysql_select_db($db_name, $con);
            mysql_query("SET NAMES UTF8");
        }
    }
    function __set($name, $value)
    {
        $this->data[$name] = $value;
        if (is_object($this->obj)) {
            $this->obj->$name = $value;
        }
    }
    function __get($name)
    {
        if (is_object($this->obj)) {
            return $this->obj->$name;
        }
    }
    public function add()
    {
        $sql = "insert into " . $this->table . "(" . implode(",", array_keys($this->data)) . ") values('" . implode("','", array_values($this->data)) . "')";
        mysql_query($sql);
    }
    public function find($id)
    {
        $data = mysql_query("select * from $this->table where id=" . $id);
        $num  = mysql_num_rows($data);
        if ($num) {
            $this->obj = mysql_fetch_object($data);
            return true;
        } else {
            return false;
        }
    }
    public function select($param = '')
    {
        $array = array();
        if ($param == '') {
            $res = mysql_query("select * from $this->table");
            while ($arr = mysql_fetch_array($res)) {
                $array[] = $arr;
            }
            return $array;
        } else {
            $data = split(",", $param);
            foreach ($data as $v) {
                $res = mysql_query("select * from $this->table where id=" . $v);
                $num = mysql_num_rows($res);
                if ($num) {
                    $arr     = mysql_fetch_array($res);
                    $array[] = $arr;
                }
            }
            return $array;
        }
    }
    public function save()
    {
        foreach ($this->obj as $k => $v) {
            $sql = "update " . $this->table . " set " . $k . "='" . $v . "' where id=" . $this->obj->id;
            mysql_query($sql);
        }
    }
    public function delete()
    {
        foreach ($this->obj as $k => $v) {
            $sql = "delete from " . $this->table . " where id=" . $this->obj->id;
            mysql_query($sql);
        }
    }
}

function T($table)
{
    $t = new ActiveRecord($table);
    return $t;
}
/*-----------------------------------------数据库对象查询封装 end-------------------------------*/
function w($plu_name)
{
    if (file_exists("./Plugin/" . $plu_name . ".php")) {
        $o = new $plu_name();
        return $o;
    }
}
function is_exist_widget($plu_name)
{
    if (file_exists("./Plugin/" . $plu_name . ".php")) {
        return true;
    } else {
        return false;
    }
}
function is_active_widget($plu_name)
{
    $sql = "select * from " . C('db_prefix') . "plugin where name='" . $plu_name . "'";
    $arr = mysql_fetch_array(mysql_query($sql));
    if ($arr['status'] == 1) {
        return true;
    } else {
        return false;
    }
}
function C($param)
{
    $config = require("Conf/config.php");
    return $config[$param];
}
class Cache
{
    public $fileName;
    function __construct($time)
    {
        ob_start();
        $this->path = 'cache';
        if (!file_exists($this->path)) {
            mkdir($this->path);
        }
        $this->time     = $time;
        $this->fileType = 'php';
        $this->config   = require("Conf/config.php");
        if (isset($_COOKIE['youyax_lang'])) {
            $suffix = $_COOKIE['youyax_lang'];
        } else {
            if (!empty($this->config['default_language'])) {
                $suffix = $this->config['default_language'];
            } else {
                $suffix = 'cn';
            }
        }
        if (getparam("l") != "" && getparam("l") != null) {
            $suffix = getparam("l");
            setcookie("youyax_lang", getparam("l"), time() + 3600 * 24 * 7, "/");
        }
        $this->fileName = "./" . $this->path . "/" . md5($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]) . '_' . $suffix . '.' . $this->fileType;
        if (file_exists($this->fileName) && ((filemtime($this->fileName) + $this->time) > time())) {
            $fp = fopen($this->fileName, "r");
            echo fread($fp, filesize($this->fileName));
            fclose($fp);
            ob_end_flush();
            exit;
        }
    }
    public function endCache()
    {
        $fp = fopen($this->fileName, "w");
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, ob_get_contents());
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        ob_end_flush();
    }
}
require("Common/common.php");
require("Common/common_ext.php");
?>