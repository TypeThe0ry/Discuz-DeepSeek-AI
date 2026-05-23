<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_discuzdeepseekai_err` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;

runquery($sql);

$finish = TRUE;

?>