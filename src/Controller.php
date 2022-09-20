<?php

namespace Demon\AdminThinkPHP;

use Demon\AdminThinkPHP\access\model\UserModel;
use Demon\AdminThinkPHP\response\View;
use Illuminate\Support\Collection;
use think\App;
use think\Request;

abstract class Controller
{
    use View;

    /**
     * @var Api 接口服务
     */
    protected $api;

    /**
     * @var Request Request实例
     */
    protected $request;

    /**
     * @var App 应用实例
     */
    protected $app;

    /**
     * @var Log 记录服务
     */
    protected $log;

    /**
     * @var int 登录用户
     */
    protected $uid = 0;

    /**
     * @var null|Collection 登录用户
     */
    protected $user = null;

    /**
     * @var array 无需登录
     */
    protected $loginExcept = [];

    /**
     * @var array 无需权限
     */
    protected $accessExcept = [];

    /**
     * @var array 权限分配
     */
    protected $accessAssign = [];

    /**
     * @var array 控制器中间件
     */
    protected $middleware = [];

    /**
     * Controller constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        //  接口服务
        $this->api = $this->api ? : app('admin')->api;
        //  应用实例
        $this->app = $app;
        //  请求实例
        $this->request = $this->app->request;
        //  记录服务
        $this->log = $this->log ? : app('admin')->log;
        //  登录用户
        $this->uid = $this->request->route('uid');
        //  Action
        $action = $this->request->action();
        //  Access
        if (config('admin.access')) {
            //  User
            $this->__user();
            //  Auth
            if (get_called_class() != config('admin.authentication')) {
                //  Login
                if ($this->__login($action) !== true)
                    $this->__access($action);
            }
        }
        //  Badge
        if (config('admin.badge'))
            app()->invoke([app()->make(config('admin.badge')), 'setBadge'], ['uid' => $this->uid]);
        //  Notification
        if (config('admin.notification'))
            app()->invoke([app()->make(config('admin.notification')), 'setNotification'], ['uid' => $this->uid]);
        //  Tabs
        $this->__tabs($action);
        // 控制器初始化
        $this->__initialize();
    }

    /**
     * 初始化
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function __initialize()
    {
    }

    /**
     * 检查用户
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function __user()
    {
        //  检查用户
        if (config('admin.access')) {
            //  如果已经登录
            if ($this->uid) {
                //  获取信息并更新
                if ($this->user = UserModel::findAndRids($this->uid)) {
                    app('admin')->setUser($this->user);
                    app('admin')->view->share('user', $this->user);
                    UserModel::where('uid', $this->uid)->update(['activeTime' => mstime()]);
                }
                //  如果数据有误或状态不正确
                if (!$this->user || $this->user->status < 1 || ((session('time') ? : 0) < ($this->user->passwordTime ?? 0))) {
                    $this->uid = 0;
                    $this->user = null;
                }
            }
        }
    }

    /**
     * 检查登录
     *
     * @param string $action
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function __login($action)
    {
        //  检查登录
        if (config('admin.access')) {
            //  不需要登录
            if (in_array('*', $this->loginExcept) || in_array($action, $this->loginExcept))
                return true;
            //  需要登录
            if (!$this->uid) {
                if (!DEMON_SUBMIT && !DEMON_INJSON) {
                    session('admin.login', request()->url(true));
                    abort(response($this->app->make(config('admin.authentication'))->login()));
                }
                else
                    abort(json(['code' => DEMON_CODE_AUTH, 'message' => admin_error(DEMON_CODE_AUTH)], DEMON_CODE_AUTH));
            }
        }
    }

    /**
     * 检查权限
     *
     * @param string $action
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function __access($action)
    {
        //  检查权限
        if (config('admin.access')) {
            //  需要权限
            if (!app('admin')->access->intercept($action, $this->accessExcept, $this->accessAssign)) {
                abort((DEMON_SUBMIT || DEMON_INJSON) ?
                    json(['code' => DEMON_CODE_ACCESS, 'message' => admin_error(DEMON_CODE_ACCESS)], DEMON_CODE_ACCESS) :
                    response(admin_view('preset.error.general', ['code' => DEMON_CODE_ACCESS, 'message' => admin_error(DEMON_CODE_ACCESS), 'data' => ['javascript' => 'window.location.href=window.location.href;window.location.reload;']]), DEMON_CODE_ACCESS));
            }
        }
    }

    /**
     * 标签页
     *
     * @param string $action
     *
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function __tabs($action)
    {
        //  开启判断
        if (config('admin.tabs') && !DEMON_SUBMIT && !DEMON_INAJAX && !DEMON_INJSON) {
            //  携带框架标记并且不是授权相关功能，或者是首页但没有携带框架标记
            if ((admin_tabs('frame') || (request()->baseUrl(true) == admin_url() && !admin_tabs(null))) && get_called_class() != config('admin.authentication')) {
                //  获取用户菜单信息
                $menu = collect(app('admin')->access->getUserMenuFormat());
                //  寻找首页信息
                $index = $menu->where('path', admin_url())->first();
                //  定义基础内容
                $frames = [$index ? $index : ['mid' => 0, 'title' => app('admin')->__('base.menu.dashboard'), 'path' => admin_url()]];
                //  搜寻当前页
                if (admin_url() != request()->baseUrl(true) && $current = $menu->where('path', request()->baseUrl(true))->first())
                    $frames[] = $current;
                //  输出框架视图
                abort(response(admin_view('layout.vessel.tabs', ['frames' => $frames])));
            }
        }
    }
}
