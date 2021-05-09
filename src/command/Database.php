<?php

namespace Demon\AdminThinkPHP\command;

use Demon\AdminThinkPHP\DB;
use Demon\AdminThinkPHP\example\Service;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Database extends Command
{
    /**
     * The provider to publish.
     *
     * @var string
     */
    protected $provider = null;

    /**
     * The tags to publish.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * @var string Mysql version
     */
    protected $version = '5.7';

    /**
     * @var DB DB query
     */
    protected $db = null;

    protected function configure()
    {
        $this
            ->setName('admin:table')
            ->setDescription('Create some table for the admin database');
    }

    /**
     * Execute the console command.
     *
     * @param Input $input
     * @param Output $output
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->db = DB::connect(config('admin.connection'));
        $this->version = substr($this->db->query('SELECT VERSION() as version')[0]['version'], 0, 3);
        $this->query(Service::MasterModel);
        $this->query(Service::SettingModel);
        $this->query(Service::SlaveModel);
        $this->query('admin_allot');
        $this->query('admin_log');
        $this->query('admin_menu');
        $this->query('admin_role');
        $this->query('admin_user');
        $output->info('Tables created successfully.');
    }

    private function query($table)
    {
        switch ($table) {
            case Service::MasterModel:
                $data = $this->version < '5.7' ? "`data` text COLLATE utf8mb4_unicode_ci COMMENT '特殊数据'" : "`data` json DEFAULT NULL COMMENT '特殊数据'";
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `example_master` (
  `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `phone` char(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机号',
  `nickname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
  `code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户码(邀请码)',
  `inviteUid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邀请人UID',
  `sex` enum('-1','0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '性别(-1:女,0:其他,1:男)',
  `avatar` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像图片地址',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  $data,
  `intro` text COLLATE utf8mb4_unicode_ci COMMENT '简介(富文本)',
  `birthday` date NOT NULL DEFAULT '1800-01-01' COMMENT '生日',
  `hobby` text COLLATE utf8mb4_unicode_ci COMMENT '爱好(用,分隔)',
  `type` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '三级分类',
  `activeTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '活动时间',
  `signDate` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '签到日期',
  `loginTime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginIpv4i` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录IPV4_整数',
  `loginIpv4s` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IPV4_字符串',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删,0:隐藏,1:正常)',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                break;
            case Service::SettingModel:
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `example_setting` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `module` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属模块',
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '主键名称',
  `title` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `tips` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '帮助提示',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '当前内容',
  `before` text COLLATE utf8mb4_unicode_ci COMMENT '上次内容',
  `hidden` tinyint(4) NOT NULL DEFAULT '0' COMMENT '隐藏类型(-1:显示但只读,0:显示,1:隐藏)',
  `must` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否必填(0:非必填,1:必填)',
  `reorder` int(10) UNSIGNED NOT NULL DEFAULT '1000' COMMENT '显示顺序',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT '控件类型',
  `filter` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT '过滤类型',
  `validate` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'validate.js规则名称',
  `tag` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '自定义标识(用来注入DATA或其他处理)',
  `data` text COLLATE utf8mb4_unicode_ci COMMENT '数据来源',
  `initialize` text COLLATE utf8mb4_unicode_ci COMMENT '初始内容',
  PRIMARY KEY (`id`),
  UNIQUE KEY `example_setting_module_name_unique` (`module`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                $this->db->table($table)
                  ->insertAll([
                      ['id' => 1, 'module' => 'test', 'name' => 'checkbox', 'title' => 'Checkbox Title', 'tips' => 'Checkbox Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 1, 'type' => 'checkbox', 'filter' => 'bool', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '0',],
                      [
                          'id' => 2, 'module' => 'test', 'name' => 'checkboxs', 'title' => 'Checkboxs Title', 'tips' => 'Checkboxs Tips', 'value' => '[]', 'before' => '[]', 'hidden' => 0, 'must' => 1, 'reorder' => 2, 'type' => 'checkboxs', 'filter' => 'array', 'validate' => null, 'tag' => null, 'data' => '["Test1","Test2","Test3","Test4","Test5"]',
                          'initialize' => '[]',
                      ],
                      ['id' => 3, 'module' => 'test', 'name' => 'radio', 'title' => 'Radio Title', 'tips' => 'Radio Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 3, 'type' => 'radio', 'filter' => 'int', 'validate' => null, 'tag' => null, 'data' => '["Test1","Test2","Test3","Test4","Test5"]', 'initialize' => '0',],
                      ['id' => 4, 'module' => 'test', 'name' => 'select', 'title' => 'Select Title', 'tips' => 'Select Tips', 'value' => '[]', 'before' => '[]', 'hidden' => 0, 'must' => 1, 'reorder' => 4, 'type' => 'select', 'filter' => 'int', 'validate' => null, 'tag' => null, 'data' => '["Test1","Test2","Test3","Test4","Test5"]', 'initialize' => '[]',],
                      [
                          'id' => 5, 'module' => 'test', 'name' => 'selects', 'title' => 'Selects Title', 'tips' => 'Selects Tips', 'value' => '[0]', 'before' => '[0]', 'hidden' => 0, 'must' => 1, 'reorder' => 5, 'type' => 'selects', 'filter' => 'array', 'validate' => null, 'tag' => null, 'data' => '["Test1","Test2","Test3","Test4","Test5"]',
                          'initialize' => '[0]',
                      ],
                      ['id' => 6, 'module' => 'test', 'name' => 'range', 'title' => 'Range Title', 'tips' => 'Range Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 6, 'type' => 'range', 'filter' => 'int', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '0',],
                      ['id' => 7, 'module' => 'test', 'name' => 'ranges', 'title' => 'Ranges Title', 'tips' => 'Ranges Tips', 'value' => '0,0', 'before' => '0,0', 'hidden' => 0, 'must' => 1, 'reorder' => 7, 'type' => 'range', 'filter' => 'array', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '0,0',],
                      ['id' => 8, 'module' => 'test', 'name' => 'custom', 'title' => 'Custom Title', 'tips' => 'Custom Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 8, 'type' => 'select', 'filter' => 'int', 'validate' => null, 'tag' => 'custom', 'data' => null, 'initialize' => '0',],
                      ['id' => 9, 'module' => 'test', 'name' => 'object', 'title' => 'Object Title', 'tips' => 'Object Tips', 'value' => 'credit', 'before' => 'credit', 'hidden' => 0, 'must' => 1, 'reorder' => 9, 'type' => 'select', 'filter' => 'string', 'validate' => null, 'tag' => 'object', 'data' => null, 'initialize' => 'credit',],
                      [
                          'id' => 10, 'module' => 'test', 'name' => 'image', 'title' => 'Image Title', 'tips' => 'Image Tips',
                          'value' => 'data:image/jpeg;base64,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',
                          'before' => 'data:image/jpeg;base64,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',
                          'hidden' => 0, 'must' => 1, 'reorder' => 10, 'type' => 'image', 'filter' => 'string', 'validate' => null, 'tag' => '', 'data' => null,
                          'initialize' => 'data:image/jpeg;base64,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',
                      ],
                      ['id' => 11, 'module' => 'test', 'name' => 'color', 'title' => 'Color Title', 'tips' => 'Color Tips', 'value' => '#000000', 'before' => '#000000', 'hidden' => 0, 'must' => 1, 'reorder' => 11, 'type' => 'color', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '#000000',],
                      ['id' => 12, 'module' => 'test', 'name' => 'time', 'title' => 'Time Title', 'tips' => 'Time Tips', 'value' => '00:00', 'before' => '00:00', 'hidden' => 0, 'must' => 1, 'reorder' => 12, 'type' => 'time', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '00:00',],
                      ['id' => 13, 'module' => 'test', 'name' => 'date', 'title' => 'Date Title', 'tips' => 'Date Tips', 'value' => '2021-02-11', 'before' => '2021-02-11', 'hidden' => 0, 'must' => 1, 'reorder' => 13, 'type' => 'date', 'filter' => 'string', 'validate' => '{"date":true}', 'tag' => null, 'data' => null, 'initialize' => '2021-02-11',],
                      [
                          'id' => 14, 'module' => 'test', 'name' => 'datetime', 'title' => 'Datetime Title', 'tips' => 'Datetime Tips', 'value' => '2021-02-11 20:00', 'before' => '2021-02-11 20:00', 'hidden' => 0, 'must' => 1, 'reorder' => 14, 'type' => 'datetime', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null,
                          'initialize' => '2021-02-11 20:00',
                      ],
                      ['id' => 15, 'module' => 'test', 'name' => 'env', 'title' => 'Env Title', 'tips' => 'Env Tips', 'value' => 'env', 'before' => 'env', 'hidden' => 1, 'must' => 1, 'reorder' => 15, 'type' => 'text', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => 'env',],
                      ['id' => 16, 'module' => 'test', 'name' => 'hidden', 'title' => 'Hidden Title', 'tips' => 'Hidden Tips', 'value' => 'hidden', 'before' => 'hidden', 'hidden' => -1, 'must' => 1, 'reorder' => 16, 'type' => 'text', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => 'hidden',],
                      ['id' => 17, 'module' => 'test', 'name' => 'text', 'title' => 'Text Title', 'tips' => 'Text Tips', 'value' => 'text', 'before' => 'text', 'hidden' => 0, 'must' => 1, 'reorder' => 17, 'type' => 'text', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => 'text',],
                      ['id' => 18, 'module' => 'test', 'name' => 'number', 'title' => 'Number Title', 'tips' => 'Number Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 18, 'type' => 'number', 'filter' => 'int', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '0',],
                      [
                          'id' => 19, 'module' => 'test', 'name' => 'url', 'title' => 'Url Title', 'tips' => 'Url Tips', 'value' => 'http://www.m-create.cn', 'before' => 'http://www.m-create.cn', 'hidden' => 0, 'must' => 1, 'reorder' => 19, 'type' => 'url', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null,
                          'initialize' => 'http://www.m-create.cn',
                      ],
                      ['id' => 20, 'module' => 'test', 'name' => 'password', 'title' => 'Password Title', 'tips' => 'Password Tips', 'value' => '0', 'before' => '0', 'hidden' => 0, 'must' => 1, 'reorder' => 20, 'type' => 'password', 'filter' => 'int', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '0',],
                      [
                          'id' => 21, 'module' => 'test', 'name' => 'email', 'title' => 'Email Title', 'tips' => 'Email Tips', 'value' => 'comingdemon@sina.com', 'before' => 'comingdemon@sina.com', 'hidden' => 0, 'must' => 1, 'reorder' => 21, 'type' => 'email', 'filter' => 'string', 'validate' => 'email', 'tag' => null, 'data' => null,
                          'initialize' => 'comingdemon@sina.com',
                      ],
                      ['id' => 22, 'module' => 'test', 'name' => 'textarea', 'title' => 'Textarea Title', 'tips' => 'Textarea Tips', 'value' => 'textarea', 'before' => 'textarea', 'hidden' => 0, 'must' => 1, 'reorder' => 22, 'type' => 'textarea', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => 'textarea',],
                      [
                          'id' => 23, 'module' => 'test', 'name' => 'editor', 'title' => 'Editor Title', 'tips' => 'Editor Tips', 'value' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'before' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'hidden' => 0, 'must' => 1, 'reorder' => 23, 'type' => 'editor',
                          'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>',
                      ],
                      [
                          'id' => 24, 'module' => 'test', 'name' => 'markdown', 'title' => 'Markdown Title', 'tips' => 'Markdown Tips', 'value' => '# 1' . PHP_EOL . '## 2' . PHP_EOL . '### 3' . PHP_EOL . '#### 4' . PHP_EOL . '##### 5' . PHP_EOL . '###### 6',
                          'before' => '# 1' . PHP_EOL . '## 2' . PHP_EOL . '### 3' . PHP_EOL . '#### 4' . PHP_EOL . '##### 5' . PHP_EOL . '###### 6', 'hidden' => 0, 'must' => 1, 'reorder' => 24, 'type' => 'markdown', 'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null,
                          'initialize' => '# 1' . PHP_EOL . '## 2' . PHP_EOL . '### 3' . PHP_EOL . '#### 4' . PHP_EOL . '##### 5' . PHP_EOL . '###### 6',
                      ],
                      [
                          'id' => 50, 'module' => 'debug', 'name' => 'editor', 'title' => 'Editor Title', 'tips' => 'Editor Tips', 'value' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'before' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'hidden' => -1, 'must' => 1, 'reorder' => 50, 'type' => 'editor',
                          'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>',
                      ],
                      [
                          'id' => 51, 'module' => 'debug', 'name' => 'editor2', 'title' => 'Editor2 Title', 'tips' => 'Editor2 Tips', 'value' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'before' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>', 'hidden' => 0, 'must' => 1, 'reorder' => 51, 'type' => 'editor',
                          'filter' => 'string', 'validate' => null, 'tag' => null, 'data' => null, 'initialize' => '<h1>1</h1><h2>2</h2><h3>3</h3><h4>4</h4><h5>5</h5><h6>6</h6>',
                      ],
                  ]);
                $this->db->execute("ALTER TABLE {$table} AUTO_INCREMENT = 1000;");
                break;
            case Service::SlaveModel:
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `example_slave` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT 'UID',
  `type` tinyint(3) UNSIGNED NOT NULL COMMENT '类型',
  `value` decimal(16,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '数值',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `example_slave_uid_type_unique` (`uid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                break;
            case 'admin_allot':
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `admin_allot` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '管理员UID',
  `rid` int(10) UNSIGNED NOT NULL COMMENT '角色RID',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  UNIQUE KEY `admin_allot_uid_rid_unique` (`uid`,`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                $this->db->table($table)->insert(['uid' => 1, 'rid' => 1, 'createTime' => mstime()]);
                break;
            case 'admin_log':
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `admin_log` (
  `lid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理员UID',
  `tag` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标记',
  `method` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '路径',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `content` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `arguments` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '参数',
  `ip` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IP',
  `userAgent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'UA标识',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `createDate` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建日期',
  `soleCode` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '唯一标识码',
  PRIMARY KEY (`lid`),
  KEY `admin_log_uid_index` (`uid`),
  KEY `admin_log_tag_index` (`tag`),
  KEY `admin_log_ip_index` (`ip`),
  KEY `admin_log_createtime_index` (`createTime`),
  KEY `admin_log_createdate_index` (`createDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                break;
            case 'admin_menu':
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `admin_menu` (
  `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色RID',
  `type` enum('menu','page','action') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型(menu:菜单,page:页面,action:操作)',
  `upId` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标题',
  `path` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '路径',
  `icon` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图标',
  `badgeColor` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '统计颜色(#开头的16进制颜色或样式名称)',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `weight` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权重(越大越优先)',
  `system` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统内置无法操作',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态(0:关闭,1:启用)',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`mid`),
  UNIQUE KEY `admin_menu_path_unique` (`path`),
  KEY `admin_menu_upid_index` (`upId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                $this->db->table($table)
                  ->insertAll([
                      ['mid' => 1, 'type' => 'page', 'upId' => 0, 'title' => '__base.menu.dashboard', 'path' => '/', 'icon' => 'fa fa-tachometer-alt', 'weight' => 10000, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 2, 'type' => 'menu', 'upId' => 0, 'title' => '__base.menu.admin', 'path' => null, 'icon' => 'fa fa-server', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 3, 'type' => 'menu', 'upId' => 2, 'title' => '__base.menu.admin_access', 'path' => null, 'icon' => 'fa fa-memory', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 4, 'type' => 'page', 'upId' => 3, 'title' => '__base.menu.admin_access_user', 'path' => 'admin/access/user', 'icon' => 'fa fa-user-tie', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 5, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/add', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 6, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/status', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 7, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/edit', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 8, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/del', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 9, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/info', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 10, 'type' => 'action', 'upId' => 4, 'title' => null, 'path' => 'admin/access/user/batch', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 11, 'type' => 'page', 'upId' => 3, 'title' => '__base.menu.admin_access_menu', 'path' => 'admin/access/menu', 'icon' => 'fa fa-network-wired', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 12, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/weight', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 13, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/del', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 14, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/edit', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 15, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/status', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 16, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/add', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 17, 'type' => 'action', 'upId' => 11, 'title' => null, 'path' => 'admin/access/menu/batch', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 18, 'type' => 'page', 'upId' => 3, 'title' => '__base.menu.admin_access_role', 'path' => 'admin/access/role', 'icon' => 'fa fa-map-signs', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 19, 'type' => 'action', 'upId' => 18, 'title' => null, 'path' => 'admin/access/role/status', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 20, 'type' => 'action', 'upId' => 18, 'title' => null, 'path' => 'admin/access/role/del', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 21, 'type' => 'action', 'upId' => 18, 'title' => null, 'path' => 'admin/access/role/edit', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 22, 'type' => 'action', 'upId' => 18, 'title' => null, 'path' => 'admin/access/role/add', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 23, 'type' => 'action', 'upId' => 18, 'title' => null, 'path' => 'admin/access/role/batch', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 24, 'type' => 'page', 'upId' => 3, 'title' => '__base.menu.admin_access_log', 'path' => 'admin/access/log', 'icon' => 'fa fa-list-alt', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 25, 'type' => 'action', 'upId' => 24, 'title' => null, 'path' => 'admin/access/log/del', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 26, 'type' => 'action', 'upId' => 24, 'title' => null, 'path' => 'admin/access/log/info', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 27, 'type' => 'action', 'upId' => 24, 'title' => null, 'path' => 'admin/access/log/clear', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 28, 'type' => 'action', 'upId' => 24, 'title' => null, 'path' => 'admin/access/log/export', 'icon' => null, 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1000, 'type' => 'menu', 'upId' => 2, 'title' => '__base.menu.example', 'path' => null, 'icon' => 'fa fa-gifts', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1001, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_index', 'path' => 'example/index', 'icon' => 'fa fa-tachometer-alt', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1002, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_form', 'path' => 'example/form', 'icon' => 'fa fa-building', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1003, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_layer', 'path' => 'example/layer', 'icon' => 'fa fa-layer-group', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1004, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_table', 'path' => 'example/table', 'icon' => 'fa fa-table', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1005, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_widget', 'path' => 'example/widget', 'icon' => 'fa fa-magic', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1006, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_editor', 'path' => 'example/editor', 'icon' => 'fa fa-pen-fancy', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1007, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_markdown', 'path' => 'example/markdown', 'icon' => 'fa fa-marker', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1008, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_login', 'path' => 'example/login', 'icon' => 'fa fa-user-check', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                      ['mid' => 1009, 'type' => 'page', 'upId' => 1000, 'title' => '__base.menu.example_setting', 'path' => 'example/setting', 'icon' => 'fa fa-cogs', 'weight' => 0, 'system' => 1, 'createTime' => mstime()],
                  ]);
                $this->db->execute("ALTER TABLE {$table} AUTO_INCREMENT = 10000;");
                break;
            case 'admin_role':
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `admin_role` (
  `rid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色RID',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `system` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统内置无法操作',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态(0:关闭,1:启用)',
  `mids` text COLLATE utf8mb4_unicode_ci COMMENT '菜单IDs',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                $this->db->table($table)->insert(['rid' => 1, 'name' => '__base.access.admin', 'system' => 1, 'mids' => '*', 'createTime' => mstime()]);
                break;
            case 'admin_user':
                $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `admin_user` (
  `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员UID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '账号名',
  `nickname` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '昵称',
  `avatar` text COLLATE utf8mb4_unicode_ci COMMENT '头像数据(base64)',
  `password` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录密码',
  `system` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系统内置无法操作',
  `remark` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(-1:已删,0:禁用,1:正常)',
  `createTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `loginTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `activeTime` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后活跃时间',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `admin_user_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
                $this->db->query($sql);
                $this->db->table($table)->insert(['uid' => 1, 'username' => 'admin', 'nickname' => 'Administrator', 'password' => bomber()->password(['action' => 'hash', 'content' => 'demon']), 'system' => 1, 'createTime' => mstime()]);
                break;
        }
    }
}
