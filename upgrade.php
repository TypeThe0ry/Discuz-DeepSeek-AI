<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$is_discuzdeepseekai_err = DB::fetch_first("show tables like '" . DB::table('plugin_discuzdeepseekai_err') . "'");
$sql = '';
if (! $is_discuzdeepseekai_err) {
    $sql .= <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_discuzdeepseekai_err` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;
}

if ($sql) runquery($sql);
$finish = TRUE;

?>