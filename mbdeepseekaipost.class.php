<?php
if (! defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class mobileplugin_discuzdeepseekai
{

}

class mobileplugin_discuzdeepseekai_forum extends mobileplugin_discuzdeepseekai
{

    public function viewthread_bottom_mobile_output($a)
    {
        global $_G;
        $cache = $_G['cache']['plugin']['discuzdeepseekai'];
        $return='';
        if ($cache['openai'] && $_G['thread']['displayorder'] >= 0) {
            if($cache['openattach']&&$_G['thread']['attachment']>0)  return $return;
            if (in_array($_G['groupid'], unserialize($cache['groups']))&&in_array($_G['fid'], unserialize($cache['forums']))) {     
                $tid = intval($_GET['tid']);
                $discuzdeepseekai_url = 'plugin.php?id=discuzdeepseekai&tid=' . $tid . '&formhash=' . FORMHASH;
                $openonload=$cache['openonload'];
                include template('discuzdeepseekai:auto');
            }
        }

        return $return;
    }

}
class mobileplugin_discuzdeepseekai_group extends mobileplugin_discuzdeepseekai
{

    public function viewthread_bottom_mobile_output($a)
    {
        global $_G;
        $cache = $_G['cache']['plugin']['discuzdeepseekai'];
        $return='';
  
        if($cache['opengroup']&&$cache['openai']){
            if ($cache['apikey'] && $_G['thread']['displayorder'] >= 0 && ! ($cache['openattach'] && $_G['thread']['attachment'] > 0)) {

                if (in_array($_G['groupid'], unserialize($cache['groups']))) {

                    $tid = intval($_GET['tid']);
                    $discuzdeepseekai_url = 'plugin.php?id=discuzdeepseekai&come=group&tid=' . $tid . '&formhash=' . FORMHASH;
                    $openonload=$cache['openonload'];
                    include template('discuzdeepseekai:auto');
                }
            }
        }

        return $return;
    }

}
class mobileplugin_discuzdeepseekai_portal extends mobileplugin_discuzdeepseekai
{

    public function view_article_content_mobile_output($a)
    {
        global $_G;
        $cache = $_G['cache']['plugin']['discuzdeepseekai'];
        $return='';
        if($cache['openarticle']&&$cache['openai']){
            $aid = intval($_GET['aid']);
            if ($aid) {
                if (in_array($_G['groupid'], unserialize($cache['groups']))) {
                    $discuzdeepseekai_url = 'plugin.php?id=discuzdeepseekai:article&articleid=' . $aid . '&formhash=' . FORMHASH;
                    $openonload=$cache['openonload'];
                    include template('discuzdeepseekai:auto');
                }
            }
        }

        return $return;
    }

}
?>