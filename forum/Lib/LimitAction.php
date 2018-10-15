<?php
class LimitAction extends YouYaX
{
    static function limit_time($limit_time)
    {
        $arr = self::find(C('db_prefix') . "topic_limit", "string", "user='" . $_SESSION['youyax_user'] . "'");
        if ($arr) {
            if (time() - $arr['time'] > $limit_time) {
                $data['time'] = time();
                self::save($data, C('db_prefix') . "topic_limit", "user='" . $_SESSION['youyax_user'] . "'");
                return true;
            } else {
                $data['time'] = time();
                self::save($data, C('db_prefix') . "topic_limit", "user='" . $_SESSION['youyax_user'] . "'");
                return false;
            }
        } else {
            $data['user'] = $_SESSION['youyax_user'];
            $data['time'] = time();
            self::add($data, C('db_prefix') . "topic_limit");
            return true;
        }
    }
}
?>