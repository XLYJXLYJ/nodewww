<?php
class CountAction extends YouYaX
{
    static function doPostCount()
    {
        if (match($_SESSION['youyax_user'], "session_user")) {
            $count_arr = self::find(C('db_prefix') . "count", "string", "id=1");
            $data      = unserialize($count_arr['post_count']);
            $date      = date('w', time());
            $date2     = date('W', time());
            if ($date2 != $count_arr['week_order']) {
                $array               = array();
                $array['user_count'] = '';
                $array['post_count'] = '';
                $array['week_order'] = $date2;
                self::save($array, C('db_prefix') . "count", "id=1");
                $count_arr = self::find(C('db_prefix') . "count", "string", "id=1");
                $data      = unserialize($count_arr['post_count']);
            }
            switch ($date) {
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
            $array               = array();
            $array['post_count'] = serialize($data);
            self::save($array, C('db_prefix') . "count", "id=1");
        }
    }
}
?>