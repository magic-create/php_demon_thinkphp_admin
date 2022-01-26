<?php

namespace Demon\AdminThinkPHP;

use Demon\AdminThinkPHP\support\Publish;
use Demon\AdminThinkPHP\support\Taglib;
use Demon\AdminThinkPHP\support\Translation;
use Demon\AdminThinkPHP\support\blade\BladeInstance;
use think\helper\Str;
use think\Service;
use Demon\AdminThinkPHP\command;

class AdminService extends Service
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //  合并配置
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'admin.php', 'admin');
        $this->mergeConfigFrom(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'dbtable.php', 'dbtable');
        //  动态生成最终有效前端资源路径（CDN优先，如果未配置则拼接本地路径）
        $this->app->config->set(['assets' => (config('admin.cdn.status') ? config('admin.cdn.url') : config('admin.static', '/static/admin') . '/libs') . '/'], 'admin');
        //  Query继承类
        $connection = $this->app->config->get('admin.connection');
        $config = $this->app->config->get('database.connections');
        $config[$connection] = $config[$connection] ?? [];
        $config[$connection]['query'] = 'Demon\AdminThinkPHP\support\Query';
        $this->app->config->set(['connections' => $config], 'database');
        //  加载路由
        if (!$this->app->runningInConsole() && strpos($this->app->request->server('REQUEST_URI'), '/' . config('admin.path')) === 0)
            $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'route.php');
        //  加载命令
        $this->commands([command\Publish::class, command\Database::class, command\Reset::class]);
        $this->app->bind('admin.publish', Publish::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //  加载语言包
        $this->app->lang->setLangSet(cookie('admin_lang') ? : $this->app->lang->getLangSet());
        $langPath = admin_path('lang');
        $localePath = $langPath . DIRECTORY_SEPARATOR . $this->app->lang->getLangSet();
        $localePreset = __DIR__ . DIRECTORY_SEPARATOR . 'directory' . DIRECTORY_SEPARATOR . 'lang';
        $this->loadTranslationsFrom(is_dir($localePath) && bomber()->dirList($localePath) ? $langPath : $localePreset);
        //  过滤无效的语言
        $locales = config('admin.locales', []);
        foreach ($locales as $locale => $name) {
            $localePath = $langPath . DIRECTORY_SEPARATOR . $locale;
            if (!(is_dir($localePath) && bomber()->dirList($localePath)) && !is_dir($localePreset . DIRECTORY_SEPARATOR . $locale))
                unset($locales[$locale]);
        }
        $this->app->config->set(['locales' => $locales], 'admin');
        //  检查是否正确安装
        if (!is_dir(admin_path('controller')) && !$this->app->runningInConsole())
            throw new \ErrorException('Please install it (AdminService) correctly, Read the README.md first', DEMON_CODE_SERVICE);
        //  加载视图
        $this->loadViewsFrom([admin_path('view'), __DIR__ . DIRECTORY_SEPARATOR . 'view'], 'admin');
        //  实例化更多
        $this->app->bind('admin', function() { return new Admin($this->app->config); });
        if ($this->app->runningInConsole()) {
            //  初始化安装
            $this->publishes([
                __DIR__ . DIRECTORY_SEPARATOR . 'directory' => admin_path(),
                root_path('vendor' . DIRECTORY_SEPARATOR . 'comingdemon' . DIRECTORY_SEPARATOR . 'admin-asset') . 'static' => public_path(trim(config('admin.static', '/static/admin'), '/')),
            ], 'admin-all');
            //  只安装资源
            $this->publishes([
                root_path('vendor' . DIRECTORY_SEPARATOR . 'comingdemon' . DIRECTORY_SEPARATOR . 'admin-asset') . 'static' => public_path(trim(config('admin.static', '/static/admin'), '/')),
            ], 'admin-assets');
        }
    }

    /**
     * 合并配置
     *
     * @param $path
     * @param $key
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app->config;
        $config->set(array_merge(require $path, $config->get($key, [])), $key);
    }

    /**
     * 加载多语言
     *
     * @param $path
     *
     * @return array
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function loadTranslationsFrom($path)
    {
        $this->app->bind('admin.translation', Translation::class);
        if (!is_dir($path))
            return [];
        $list = glob(realpath($path) . DIRECTORY_SEPARATOR . $this->app->lang->getLangSet() . DIRECTORY_SEPARATOR . '*.php');
        $admin = [];
        foreach ($list as $val)
            $admin = array_merge($admin, [basename($val, substr(strrchr($val, '.'), 0)) => array_change_key_case((new Translation($this->app))->parse($val))]);
        app('admin.translation')->setLangSet($this->app->lang->getLangSet());
        app('admin.translation')->loaded[$this->app->lang->getLangSet()] = $admin;

        return app('admin.translation')->loaded[$this->app->lang->getLangSet()];
    }

    /**
     * 注册视图
     *
     * @param $path
     * @param $namespace
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function loadViewsFrom($path, $namespace)
    {
        $this->app->bind('admin.view', function() { return new BladeInstance(root_path('view/admin'), root_path('runtime/admin/view')); });
        app('admin.view')->addNamespace($namespace, $path);
    }

    /**
     * 设置推送
     *
     * @param array $paths
     * @param null  $groups
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function publishes(array $paths, $groups = null)
    {
        app('admin.publish')->publishes(static::class, $paths, $groups);
    }
}
