<?php
if (! defined('IN_DISCUZ') || ! defined('IN_ADMINCP')) {
    exit('Access Denied');
}
if ($_GET['go'] == 'del' && $_GET['formhash'] == FORMHASH) {
    $delid=intval($_GET['delid']);
    if ($delid>0) {
        C::t('#discuzdeepseekai#discuzdeepseekai_error')->delete($delid);
    }
}
showtableheader();

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$prepage = 20;
$start = ($page - 1) * $prepage;
$num = C::t('#discuzdeepseekai#discuzdeepseekai_error')->count();
$multipage = multi($num, $prepage, $page, ADMINSCRIPT . '?action=plugins&operation=config&do=' . $pluginid . '&identifier=discuzdeepseekai&pmod=admin');
$arr = C::t('#discuzdeepseekai#discuzdeepseekai_error')->range($start, $prepage, 'addtime desc');
showsubtitle(array(
    'ID',
    lang('plugin/discuzdeepseekai', 'tid'),
    
    lang('plugin/discuzdeepseekai', 'err_msg'),
    lang('plugin/discuzdeepseekai', 'addtime'),
    lang('plugin/discuzdeepseekai', 'ac')
));
foreach ($arr as $v) {
    if ($v['addtime'])
        $addtime = dgmdate($v['addtime'], 'u', '9999', getglobal('setting/dateformat') . ' H:i:s');
    $delurl = '<a href="' . ADMINSCRIPT . '?action=plugins&operation=config&do=' . $pluginid . '&identifier=discuzdeepseekai&pmod=admin&page=' . $page . '&go=del&delid=' . $v['id'] . '&formhash=' . formhash() . '" onclick="javascript:if(!confirm(\'' . lang('plugin/discuzdeepseekai', 'del_msg') . '\')){return false}">' . lang('plugin/discuzdeepseekai', 'del') . '</a>';
    
    showtablerow('', array('width="60"', 'width="60"', 'width="160"',  'width="60"',  'width="60"'), array(
        $v['id'],
        '<a target="_blank" href="forum.php?mod=viewthread&tid=' . $v['tid'] . '">' . $v['tid'] . '</a>',
        '<font color="#e4862f">' . $v['message'] . '</font>',
        $addtime,
        $delurl
    ));
}
showtablefooter();
echo '<div class="cuspages right">' . $multipage . '</div>';
?>