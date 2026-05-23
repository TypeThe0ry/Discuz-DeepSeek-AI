# Discuz-DeepSeek-AI

Discuz! X3.5 插件：发帖后自动调用 **DeepSeek** 大模型进行 AI 回帖。完全开源（MIT），无付费组件、无授权校验、无远程回调。

- 作者：TypeThe0ry
- 协议：[MIT](LICENSE)
- 适配：Discuz! X3.5

## 功能

- 新主题发布后自动触发 AI 回帖
- 可配置触发用户组、回帖用户池、是否引用原文、是否免审核
- 支持随机延迟、时间窗口限制、单帖回复数上限
- 支持自定义 Prompt（`{title} {content} {forum} {author}` 占位）
- 后台「帖子调试信息」页面查看失败原因

## 安装

1. 将本目录上传到 Discuz 站点的 `source/plugin/discuzdeepseekai/`。  
   **目录名必须是 `discuzdeepseekai`**，否则 Discuz 无法识别。
2. 进入后台 → 应用 → 插件，找到「AIDeepSeek自动回帖」并安装。
3. 安装会创建一张表：`pre_plugin_discuzdeepseekai_err`（用于调试日志）。
4. 在插件设置里填入 DeepSeek API Key（[申请地址](https://platform.deepseek.com/api_keys)），保存即可。

## 升级 / 卸载

- 升级：覆盖文件后，后台执行插件「升级」即可（`upgrade.php` 仅做幂等的表结构补齐）。
- 卸载：会删除 `pre_plugin_discuzdeepseekai_err` 表。其他配置由 Discuz 自动清理。

> 若你从旧版（旧表前缀 `pre_plugin_apoyl_deepseekaipost_*`）切过来，请先在后台卸载旧版插件，再安装本版；或手工 `DROP` 那些旧表。

## 目录结构

```
discuzdeepseekai/
├── admin.inc.php                          后台「帖子调试信息」
├── check.php                              安装检查（no-op）
├── deepseekaipost.class.php               帖子钩子类
├── mbdeepseekaipost.class.php             移动端钩子类
├── discuzdeepseekai.inc.php               主入口（回帖逻辑）
├── install.php / uninstall.php / upgrade.php
├── discuz_plugin_discuzdeepseekai_SC_UTF8.xml   简体中文插件清单
├── discuz_plugin_discuzdeepseekai_TC_UTF8.xml   繁体中文插件清单
├── api/
│   ├── DiscuzDeepseekaiPost.class.php     DeepSeek HTTP 调用
│   └── DiscuzDeepseekaiPostComm.class.php 统一入口
├── table/
│   ├── table_discuzdeepseekai_error.php   调试日志表
│   └── table_forum_postext.php            forum_post 扩展查询
└── template/
    ├── auto.htm                           PC 端自动触发模板
    └── touch/auto.htm                     移动端自动触发模板
```

## 开发说明

- 数据库表通过 `C::t('#discuzdeepseekai#discuzdeepseekai_error')` 访问。
- 主流程入口：`discuzdeepseekai.inc.php`。  
  自定义 Prompt 留空时保留原默认逻辑（向后兼容）。
- API 调用：`api/DiscuzDeepseekaiPost.class.php::getTextDavinci()`。

## License

[MIT](LICENSE) © TypeThe0ry
