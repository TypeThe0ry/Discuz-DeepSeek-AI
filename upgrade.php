<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: AIDeepSeek自动回帖 商业版V1.9.0
 *      下载地址: https://addon.dismall.com/plugins/apoyl_deepseekaipost.html
 *      应用开发者: 凹凸曼
 *      开发者QQ: 3489214354
 *      更新日期: 202605120727
 *      授权域名: zwwx.club
 *      授权码: 2026051119cxxquXxUk1
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */

/**
 *      [liyuanchao] (C)2019-2099 http://www.apoyl.com
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: upgrade.php  2025-4  liyuanchao（凹凸曼） $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$table = DB::table('plugin_apoyl_deepseekaipost_articleerr');
$isapoyl_deepseekaipost_article= DB::fetch_first("show tables like '" . $table . "'");
$sql='';
if (! $isapoyl_deepseekaipost_article) {
    $sql.= <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_apoyl_deepseekaipost_articleerr` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `articleid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
EOF;
}


$isapoyl_apoyl_deepseekaipost_role= DB::fetch_first("show tables like '" . DB::table('plugin_apoyl_deepseekaipost_role') . "'");
$sql='';
if (! $isapoyl_apoyl_deepseekaipost_role) {
    $sql.= <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_apoyl_deepseekaipost_role` (
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rolename` text NOT NULL,
   UNIQUE INDEX `uid`(`uid`)
) ENGINE=MyISAM;
EOF;
}

$isapoyl_apoyl_deepseekaipost_limit= DB::fetch_first("show tables like '" . DB::table('plugin_apoyl_deepseekaipost_limit') . "'");
$sql='';
if (! $isapoyl_apoyl_deepseekaipost_limit) {
    $sql.= <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_apoyl_deepseekaipost_limit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `tid`(`tid`)
) ENGINE=MyISAM;
EOF;
}
$isapoyl_apoyl_deepseekaipost_limitarticle= DB::fetch_first("show tables like '" . DB::table('plugin_apoyl_deepseekaipost_limitarticle') . "'");
$sql='';
if (! $isapoyl_apoyl_deepseekaipost_limitarticle) {
    $sql.= <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_apoyl_deepseekaipost_limitarticle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `aid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   KEY `aid`(`aid`)
) ENGINE=MyISAM;
EOF;
}

if($sql) runquery($sql);
$finish = TRUE;

?>