<?php
class CommonAction extends YouYaX
{
    static function tongji()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            $myIp = $_SERVER['HTTP_CLIENT_IP'];
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $myIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $myIp = $_SERVER['REMOTE_ADDR'];
        if(!filter_var($myIp, FILTER_VALIDATE_IP)){
           	self::delete(C('db_prefix') . "online", "lasttime<" . $lastTime);
        		$num = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "online"));
        		return $num;
        		exit;
        	}
        $myTime   = time();
        $data       = array();
        $lastTime = $myTime - 600;
        $query    = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "online where ip='" . $myIp . "'"));
        if ($query < 1) //无此IP，就增加一条在线记录
            {
            $c   = curl_init();
            $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $myIp;
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            $iper = curl_exec($c);
            curl_close($c);
            $ips              = json_decode($iper);
            $str1             = $myIp;
            $str2             = $ips->country;
            $str3             = $ips->province;
            $str4             = $ips->city;
            $str5             = $ips->district;
            $str6             = $ips->isp;
            $zone             = $str1 . " ," . $str2 . $str3 . " ,(" . $str4 . ")" . $str5 . $str6;
            $data['ip']       = $myIp;
            $data['lasttime'] = $myTime;
            $data['user']     = $_SESSION['youyax_user'];
            $data['zone']     = $zone;
            self::add($data, C('db_prefix') . "online");
        } else //有此用户，就更改此用户的最后活动时间
            {
            $data['lasttime'] = $myTime;
            $data['user']     = $_SESSION['youyax_user'];
            self::save($data, C('db_prefix') . "online", "ip='" . $myIp . "'");
            $data	= array();
            $data['lasttime'] = time();
             self::save($data, C('db_prefix') . "user", "user='" . $_SESSION['youyax_user'] . "'");
        }
        self::delete(C('db_prefix') . "online", "lasttime<" . $lastTime);
        $num = mysql_num_rows(mysql_query("select * from " . C('db_prefix') . "online"));
        return $num;
    }
    static function callSend($pos, $check, $tid, $url)
    {
        if ($_SESSION['youyax_user'] != $check) {
            $pos           = empty($pos) ? '楼主位' : $pos . '楼';
            $data          = array();
            $data['mfrom'] = '系统提示:';
            $data['mto']   = $check;
            $data['mcon']  = $pos . " 有新信息动态，<a href=\'" . $url . "/Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $tid . C('static_url') . "\'>查看</a>";
            $data['time']  = time();
            self::add($data, C('db_prefix') . "message");
            $result_mess = self::find(C('db_prefix') . "message_status", 'string', "muser='" . $check . "'");
            if ($result_mess) {
                $data2            = array();
                $data2['mstatus'] = 1;
                self::save($data2, C('db_prefix') . "message_status", "muser='" . $check . "'");
            } else {
                $data2            = array();
                $data2['muser']   = $check;
                $data2['mstatus'] = 1;
                self::add($data2, C('db_prefix') . "message_status");
            }
        }
    }
    static function admin_send($mto, $mcon)
    {
        $data          = array();
        $data['mfrom'] = '系统提示:';
        $data['mto']   = $mto;
        $data['mcon']  = htmlspecialchars($mcon, ENT_QUOTES);
        $data['time']  = time();
        self::add($data, C('db_prefix') . "message");
        $result = self::find(C('db_prefix') . "message_status", 'string', "muser='" . $mto . "'");
        if ($result) {
            $data2            = array();
            $data2['mstatus'] = 1;
            self::save($data2, C('db_prefix') . "message_status", "muser='" . $mto . "'");
        } else {
            $data2            = array();
            $data2['muser']   = $mto;
            $data2['mstatus'] = 1;
            self::add($data2, C('db_prefix') . "message_status");
        }
    }
    static function competence($user)
    {
        if (self::find(C('db_prefix') . "user", "string", "user='" . $user . "' and status!=2")) {
            return true;
        } else {
            return false;
        }
    }
    static function lock($id)
    {
        $arr = self::find(C('db_prefix') . "talk", "string", "id=" . $id);
        if ($arr['lock_status'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    static function user_group_visit()
    {
    	   if(empty($_SESSION['youyax_f'])){
    	   	  return false;
    	   	  exit;	
    	   }
        $youyax_f = $_SESSION['youyax_f'] > 10000 ? $_SESSION['youyax_f'] - 10000 : $_SESSION['youyax_f'];
        if (!empty($_SESSION['youyax_user'])) {
            $user_arr           = self::find(C('db_prefix') . "user", "string", "user='" . $_SESSION['youyax_user'] . "'");
            $user_group         = $user_arr['user_group'];
            $user_group_arr     = self::find(C('db_prefix') . "user_group", "string", "id='" . $user_group . "'");
            $user_group_purview = unserialize($user_group_arr['purview']);
            if (empty($user_group_purview)) {
                $user_group_purview = array();
            }
            if (!in_array($youyax_f, $user_group_purview)) {
                return false;
            } else {
                return true;
            }
        } else {
            $config = require("./Conf/config.php");
            if (empty($config['not_log_in_user_group'])) {
                return false;
                exit;
            }
            $user_group_arr     = self::find(C('db_prefix') . "user_group", "string", "id='" . $config['not_log_in_user_group'] . "'");
            $user_group_purview = unserialize($user_group_arr['purview']);
            if (empty($user_group_purview)) {
                $user_group_purview = array();
            }
            if (!in_array($youyax_f, $user_group_purview)) {
                return false;
            } else {
                return true;
            }
        }
    }
    static function continue_post($t3, $t4)
    {
        for ($i = 1; $i < 4; $i++) {
            $query = mysql_query("select zuozhe1 from " . C('db_prefix') . "reply where rid=" . $t3 . " and num2=" . intval($t4 - $i));
            $num   = mysql_num_rows($query);
            if ($num <= 0) {
                return false;
                break;
            } else {
                $arr = mysql_fetch_array($query);
                if ($arr['zuozhe1'] != $_SESSION['youyax_user']) {
                    return false;
                    break;
                }
            }
        }
        return true;
    }
}
?>