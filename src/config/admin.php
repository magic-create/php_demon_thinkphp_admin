<?php

return [
    //  标题
    'title' => env('ADMIN.ADMIN_TITLE', 'Admin Management'),
    //  自定义路由入口
    'path' => env('ADMIN.ADMIN_PATH', 'admin'),
    //  CDN配置
    'cdn' => [
        //  开启CDN后将读取CDN远程资源
        'status' => (bool)env('ADMIN.ADMIN_CDN_STATUS', true),
        //  请设置CDN的地址（部分强制使用CDN的地方会用到）
        'url' => env('ADMIN.ADMIN_CDN_URL', 'https://cdn.bootcdn.net/ajax/libs'),
    ],
    //  静态资源释放目录（位于public目录下）
    'static' => env('ADMIN.ADMIN_STATIC', '/static/admin'),
    //  布局（horizontal/vertical）
    'layout' => env('ADMIN.ADMIN_LAYOUT', 'vertical'),
    //  主题（light/dark）
    'theme' => env('ADMIN.ADMIN_THEME', 'light'),
    //  是否开启Tabs-Frame（启用时为URL参数标记）
    'tabs' => env('ADMIN.ADMIN_TABS', false),
    //  数据库连接
    'connection' => env('ADMIN.ADMIN_CONNECTION', 'admin'),
    //  权限验证
    'access' => env('ADMIN.ADMIN_ACCESS', false),
    //  授权控制器
    'authentication' => env('ADMIN.ADMIN_AUTHENTICATION', Demon\AdminThinkPHP\access\controller\AuthController::class),
    //  设置控制器(包含退出登录,设置,清理缓存,切换语言等用户操作)
    'setting' => env('ADMIN.ADMIN_SETTING', Demon\AdminThinkPHP\access\controller\SettingController::class),
    //  菜单统计
    'badge' => env('ADMIN.ADMIN_BADGE', Demon\AdminThinkPHP\example\Service::class),
    //  通知内容
    'notification' => env('ADMIN.ADMIN_NOTIFICATION', Demon\AdminThinkPHP\example\Service::class),
    //  提交日志
    'log' => env('ADMIN.ADMIN_LOG', Demon\AdminThinkPHP\example\Service::class),
    //  使用语言
    'locales' => [
        'en' => 'English',
        'zh-cn' => '简体中文',
        'zh-tw' => '繁體中文',
    ],
    //  Session
    'session' => [
        // session name
        'name' => env('ADMIN.ADMIN_SESSION_NAME', 'ADMINSESSID'),
        // 驱动方式 支持file cache
        'type' => env('ADMIN.ADMIN_SESSION_TYPE', 'file'),
        // 存储连接标识 当type使用cache的时候有效
        'store' => env('ADMIN.ADMIN_SESSION_STORE', null),
        // 过期时间
        'expire' => env('ADMIN.ADMIN_SESSION_EXPIRE', 7200),
        // 前缀
        'prefix' => env('ADMIN.ADMIN_SESSION_PREFIX', 'admin'),
        // 路径
        'path' => runtime_path('session'),
    ],
    //  构建元素模板
    'element' => [
        //  面包屑导航
        'breadcrumb' => env('ADMIN.ADMIN_ELEMENT_BREADCRUMB', 'admin::preset.element.breadcrumb'),
        //  底部
        'footer' => env('ADMIN.ADMIN_ELEMENT_FOOTER', 'admin::preset.element.footer'),
        //  侧边导航
        'slidebar' => env('ADMIN.ADMIN_ELEMENT_SLIDEBAR', 'admin::preset.element.slidebar'),
        //  顶部导航
        'topbar' => env('ADMIN.ADMIN_ELEMENT_TOPBAR', 'admin::preset.element.topbar')
    ],
    //  附加全局JS
    'js' => [],
    //  附加全局CSS
    'css' => [],
    //  背景图片
    'background' => [
        //  切换模式（random/daily）
        'mode' => env('ADMIN.ADMIN_BACKGROUND_MODE', 'random'),
        //  自定义图片列表
        'list' => explode(',', env('ADMIN.ADMIN_BACKGROUND_LIST', ''))
    ],
    //  验证码
    'captcha' => [
        //  字数
        'length' => env('ADMIN.ADMIN_CAPTCHA_LENGTH', 4),
        //  字符内容
        'charset' => env('ADMIN.ADMIN_CAPTCHA_CHARSET', '0123456789'),
        //  宽度
        'width' => env('ADMIN.ADMIN_CAPTCHA_WIDTH', 192),
        //  高度
        'height' => env('ADMIN.ADMIN_CAPTCHA_HEIGHT', 64),
    ]
];
