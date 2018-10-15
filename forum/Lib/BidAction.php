<?php
class BidAction extends YouYaX
{
    static function query_bid($user, $bid)
    {
        $arr = self::find(C('db_prefix') . "user", "string", "user='" . $user . "' and status!=2");
        if ($arr['bid'] >= $bid) {
            return true;
        } else {
            return false;
        }
    }
    public function accept()
    {
        $id    = getparam("id");
        $id2   = getparam("id2");
        $reply = $this->find(C('db_prefix') . "reply", "string", "id2='" . $id2 . "' and rid='" . $id . "'");
        if ($reply) {
            $talk = $this->find(C('db_prefix') . "talk", "string", "id='" . $id . "'");
            if ($talk) {
                if (($talk['zuozhe'] == $_SESSION['youyax_user']) && ($talk['is_question'] == 1)) {
                    mysql_query("update " . C('db_prefix') . "reply set content1=CONCAT('<div style=\'border: 1px solid #ff999a;background: #fbeded;padding: 10px;\'><h3 style=\'background-image: url(" . C('SITE') . "/Public/images/medals.gif);background-repeat: no-repeat;padding-left: 50px;border: 0;height: 60px;line-height: 60px;\'>采纳的答案</h3>',content1,'</div>') where id2=" . $id2);
                    mysql_query("update " . C('db_prefix') . "talk set lock_status=1 where id=$id");
                    mysql_query("update " . C('db_prefix') . "user set bid=bid+" . $talk['question_bid'] . " where user='" . $reply['zuozhe1'] . "'");
                    $this->redirect("Content" . C('default_url') . "index" . C('default_url') . "id" . C('default_url') . $id . C('static_url'));
                } else {
                    $this->error();
                }
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
    }
    public function error()
    {
        $this->assign("msgtitle", "操作错误!")->assign("message", "您可能处于非法操作中!")->assign("jumpurl", C('SITE'))->error();
    }
}
?>