<?php

namespace Demon\AdminThinkPHP\access\controller;

use Demon\AdminThinkPHP\access\model\UserModel;
use Demon\AdminThinkPHP\Controller;
use think\facade\Console;

class SettingController extends Controller
{
    protected $loginExcept = ['locale'];

    protected $accessExcept = ['*'];

    public function __initialize()
    {
        parent::__initialize();
    }

    /**
     * 登出
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function logout()
    {
        //  移除用户信息
        session(['uid' => 0]);
        //  记录标记
        app('admin')->log->setTag('auth.logout');

        //  登出成功
        return $this->api->setMessage(app('admin')->__('base.auth.logout_success'))->send();
    }

    /**
     * 变更设置
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function setting()
    {
        if (DEMON_SUBMIT) {
            //  检查内容
            $change = $this->api->check(UserModel::edit($this->uid, arguer('data', [], 'array')));
            //  记录标记
            app('admin')->log->setTag('auth.setting')->setContent($change);

            return $this->api->setMessage(app('admin')->__('base.auth.setting_success'))->send();
        }
        else
            return admin_view('preset.access.setting', ['access' => app('admin')->access, 'store' => UserModel::fieldStore()]);
    }

    /**
     * 切换语言
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function locale()
    {
        //   获取设置语言
        $locale = arguer('locale', app()->lang->getLangSet(), 'string');
        if (!in_array($locale, array_keys(config('admin.locales'))))
            return $this->api->setError(DEMON_CODE_PARAM)->send();
        //  保存设置语言
        app('admin')->setLang($locale);

        return $this->api->setMessage(app('admin')->__('base.auth.locale_success'))->send();
    }

    /**
     * 清理缓存
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function clear()
    {
        //  清理缓存
        Console::call('clear');
        //  清理Opcache
        if (ini_get('opcache.enable'))
            opcache_reset();

        //  清理成功
        return $this->api->setMessage(app('admin')->__('base.auth.clear_success'))->send();
    }
}
