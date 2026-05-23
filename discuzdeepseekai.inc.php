<?php
if (! defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;
$cache = $_G['cache']['plugin']['discuzdeepseekai'];
$tid = intval($_GET['tid']);
isset($_GET['ac'])?$ac = $_GET['ac']:$ac='';
isset($_GET['come'])?$come = $_GET['come']:$come='';
if (! $tid) {
    exit();
}


if (! $cache['openai']) {
    $msg = lang('plugin/discuzdeepseekai', 'err_close');
    _debugLog($cache['opendebug'],$tid, $msg);
    exit();
}

if (! in_array($_G['groupid'], unserialize($cache['groups']))) {
    $msg = lang('plugin/discuzdeepseekai', 'err_groupid');
    _debugLog($cache['opendebug'],$tid, $msg);
    exit();
}
if ($_GET['formhash'] != FORMHASH) {
    $msg = lang('plugin/discuzdeepseekai', 'err_formhash');
    _debugLog($cache['opendebug'],$tid, $msg);
    exit();
}
if($cache['limitnums']>0){
    $postnum=C::t('forum_post')->count_visiblepost_by_tid($tid);

    if($cache['limitnums']<=$postnum) {
        $msg = lang('plugin/discuzdeepseekai', 'err_postnum');
        _debugLog($cache['opendebug'],$tid, $msg);
        exit();
    }
}
if($cache['opentime']||$cache['opendelayreply']){
    $threadrow=C::t('forum_thread')->fetch($tid);
}

if($cache['opentime']&&$threadrow){
    if($cache['limittime']&&$threadrow['dateline']<=strtotime($cache['limittime'])){
        $msg = lang('plugin/discuzdeepseekai', 'err_time');
        _debugLog($cache['opendebug'],$tid, $msg);
        exit();
    }
}
if($cache['opendelayreply']&&$threadrow){
    $delaytime=getRandNums($cache['delaytime']);
    if($delaytime&&$threadrow['lastpost']+$delaytime>=TIMESTAMP){
        $msg = lang('plugin/discuzdeepseekai', 'err_delay');
        _debugLog($cache['opendebug'],$tid, $msg);
        exit();
    }
}
if ($cache['users']) {
    $userarr = explode(',', $cache['users']);
    $i = array_rand($userarr);
    $postuid = $userarr[$i];
    if (! $postuid) {
        $msg = lang('plugin/discuzdeepseekai', 'err_uid');
        _debugLog($cache['opendebug'],$tid, $msg);
        exit();
    }
    $row = C::t('common_member')->fetch($postuid);
    if (! $row) {

        $msg = lang('plugin/discuzdeepseekai', 'err_username');
        _debugLog($cache['opendebug'],$tid, $msg);
        exit();
    }
    $postusername = $row['username'];
}




$rolename='';

$quotemessage='';
if ($tid) {
    if($cache['openautoreply']){
            $post = C::t('#discuzdeepseekai#forum_postext')->fetch_last_new($tid, array(
                0,
                - 2
            ));

            if (! $post) {
                exit();
            }

            if (in_array($post['authorid'], $userarr)) {
                exit();
            }

            if($cache['openattach']&&$post['attachment']>0){
                exit();
            }

            require_once libfile('function/post');
            if($post['first']){
                $text=selectInput($cache,$post);
            }else{
                if (stripos($post['message'], '[/quote]') !== false) {
                    $a = explode('[/quote]', $post['message']);
                    $text = $a[1];
                } else {
                    $text = $post['message'];
                }
                $text=trim(messagecutstr($text,2000));

            }

            if ($cache['openquote']) {
                $time = dgmdate($post['dateline']);
                $quotemessage = messagecutstr($text, 100);
                $quotemessage = implode("\n", array_slice(explode("\n", $quotemessage), 0, 3));
                $post_reply_quote = lang('forum/misc', 'post_reply_quote', array(
                    'author' => $post['author'],
                    'time' => $time
                ));

                if (! defined('IN_MOBILE')) {
                    $quotemessage = "\n\n[quote][size=2][url=forum.php?mod=redirect&goto=findpost&pid=" . $post['pid'] . "&ptid=" . $post['pid'] . "][color=#999999]" . $post_reply_quote . "[/color][/url][/size]\n" . $quotemessage . "[/quote]";
                } else {
                    $quotemessage = "\n\n[quote][color=#999999]" . $post_reply_quote . "[/color]\n[color=#999999]" . $quotemessage . "[/color][/quote]";
                }
            }
        }else{
            $modpost = C::t('#discuzdeepseekai#forum_postext')->fetch_threadpost_by_tid_invisible_new($tid, -2);

            if($modpost)
                exit();
            $post = C::t('forum_post')->fetch_threadpost_by_tid_invisible($tid, 0);
            $thread = C::t('forum_thread')->fetch($tid, 0);
            if ($thread['replies'] > 0) {
                exit();
            }
            if (! $post) {
                exit();
            }

            $text=selectInput($cache,$post);
        }


    if($come!='group'){
        if(!in_array($post['fid'], unserialize($cache['forums']))){
            exit();
        }
    }
  
}


if (! $text) {
    $msg = lang('plugin/discuzdeepseekai', 'err_text');
    _debugLog($cache['opendebug'],$tid, $msg);
    exit();
}
if (! function_exists('curl_init')) {
    $msg = lang('plugin/discuzdeepseekai', 'err_curl');
    _debugLog($cache['opendebug'],$tid, $msg);
    exit();
}
$openlimit=$cache['openlimit'];
$limitword='';
if($openlimit>0){
    $limitword=lang('plugin/discuzdeepseekai', 'aiclimit'.$openlimit);
}

require_once dirname(__FILE__) . '/api/DiscuzDeepseekaiPostComm.class.php';
$discuzdeepseekaipostcomm=new DiscuzDeepseekaiPostComm();

// Build context for custom prompt variable replacement.
// Only used when $cache['custom_prompt'] is non-empty; otherwise legacy behavior is preserved.
$promptctx = array(
    'title'   => isset($post['subject']) ? $post['subject'] : '',
    'content' => isset($post['message']) ? $post['message'] : $text,
    'forum'   => isset($_G['forum']['name']) ? $_G['forum']['name'] : '',
    'author'  => isset($post['author']) ? $post['author'] : '',
);
if (!$promptctx['forum'] && isset($post['fid'])) {
    $forumrow = C::t('forum_forum')->fetch($post['fid']);
    if ($forumrow && isset($forumrow['name'])) {
        $promptctx['forum'] = $forumrow['name'];
    }
}

list($isnewcontent,$newcontent,$reobj)=$discuzdeepseekaipostcomm->factoryAotu($text.$limitword,$rolename,$cache,$promptctx);

if ($isnewcontent) {
	    $invisible = 0;
	    if ($cache['openinvisible']&&!in_array($_G['groupid'], unserialize($cache['mgroups']))) {
	        $invisible = - 2;
	    }
	    $status = (defined('IN_MOBILE') ? 8 : 0);
	    require_once libfile('function/forum');
	    $form='';
        if($cache['openfrom']){
            $form="    \n\n". $cache['from'];
        }

	    $pid = insertpost(array(
	        'fid' => $post['fid'],
	        'tid' => $post['tid'],
	        'first' => '0',
	        'author' => $postusername,
	        'authorid' => $postuid,
	        'subject' => '',
	        'dateline' => TIMESTAMP,
            'message' => $quotemessage._convGbk($newcontent,CHARSET) .$form,
	        'useip' => '',
	        'invisible' => $invisible,
	        'anonymous' => '0',
	        'usesig' => '0',
	        'htmlon' => 0,
	        'bbcodeoff' => 0,
	        'smileyoff' => 0,
	        'parseurloff' => 0,
	        'attachment' => '0',
	        'status' => $status
	    ));
	    
	    if ($pid) {
	        if ($invisible == - 2) {
	            C::t('common_moderate')->insert('pid', array(
	                'id' => $pid,
	                'status' => 0,
	                'dateline' => TIMESTAMP
	            ), false, true);
	        }
            C::t('forum_thread')->update($post['tid'], array('lastposter' => $postusername,'lastpost'=>TIMESTAMP), true);
	        $lastpost = $post['tid'] . "\t" . $post['subject'] . "\t" . TIMESTAMP . "\t" . $_G['username'];
	        C::t('forum_forum')->update($post['fid'], array(
	            'lastpost' => $lastpost
	        ));
	        C::t('forum_forum')->update_forum_counter($post['fid'], 0, 1, 1);
	        if ($invisible == 0) {
	            C::t('forum_thread')->increase($post['tid'], array(
	                'replies' => 1
	            ));
	        }
	    }
	}

_debugLog($cache['opendebug'],$tid, $reobj);

exit();

function _debugLog($opendebug,$tid, $message)
{
   
    if ($opendebug) {
        C::t('#discuzdeepseekai#discuzdeepseekai_error')->insert(array(
            'tid' => $tid,
            'message' => $message,
            'addtime' => TIMESTAMP
        ));
    }
}
function _convGbk($var, $charset)
{
    if ($charset == 'gbk') {
        $var = diconv($var, 'utf-8', $charset);
    }
    return $var;
}

function selectInput($cache,$post)
{
    require_once libfile('function/post');
    if($cache['selectfirst']==2){
        $text = trim($post['subject']).trim(messagecutstr($post['message'],3000));
    }elseif ($cache['selectfirst']==3){
        $text = trim($post['message']);
    }else{
        $text = trim($post['subject']);
    }

    return $text;
}
function getRandNums($autonums){
    $nums=0;
    if($autonums && strpos($autonums, '~')!==false){
        $tmp=explode('~', $autonums);
        $nums=rand($tmp[0], $tmp[1]);
    }
    return $nums;
}
?>