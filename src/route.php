<?php

use Demon\AdminThinkPHP\access\middleware\LogSave;
use Demon\AdminThinkPHP\access\middleware\SessionPost;
use Demon\AdminThinkPHP\access\middleware\SessionPre;
use think\exception\FuncNotFoundException;
use think\exception\HttpException;
use think\exception\ErrorException;
use think\Exception;
use think\facade\Route;
use think\middleware\SessionInit;
use think\middleware\LoadLangPack;

Route::group(!app('admin')->multi ? config('admin.path') : '', function() {
    //  路由方法
    $run = function($controller = null, $act = 'index', $mod = 'common', $con = 'index') {
        //  默认参数
        $act = $act ? : 'index';
        $mod = $mod ? : 'common';
        $con = $con ? : 'index';
        try {
            //  指向具体文件
            $controller = $controller ? : "app\\admin\\controller\\" . lcfirst($mod) . "\\" . ucwords($con) . 'Controller';
            //  当前路径
            $path = request()->url(true);
            //  定义参数
            foreach (['app' => 'admin'] + compact('controller', 'act', 'mod', 'con', 'path') as $key => $val)
                request()->setRoute([$key => $val]);
            //  如果文件存在
            if (class_exists($controller)) {
                //  手动设置路由
                request()->setController(sprintf('%s.%s', $mod, ucwords($con)))->setAction($act);

                //  运行返回
                return app()->invoke([$controller, $act]);
            }
            else abort(DEMON_CODE_NONE, admin_error(DEMON_CODE_NONE));
        } catch (HttpException $exception) {
            if (!DEMON_INAJAX && !DEMON_SUBMIT && !app()->isDebug())
                return admin_view('preset.error.general', ['code' => $exception->getStatusCode(), 'message' => $exception->getMessage()]);
            throw $exception;
        } catch (Exception | FuncNotFoundException | ErrorException $exception) {
            if (!DEMON_INAJAX && !DEMON_SUBMIT && !app()->isDebug())
                return admin_view('preset.error.general', ['code' => DEMON_CODE_SERVER, 'message' => env('app.debug') ? $exception->getMessage() : admin_error(DEMON_CODE_SERVER)]);
            throw $exception;
        }
    };
    //  首页
    Route::rule('/', function() use ($run) { return $run(); }, 'GET|POST');
    //  授权
    Route::rule('/auth/[:act]', function($act = 'login') use ($run) { return $run($act == 'login' ? config('admin.authentication') : config('admin.setting'), $act); }, 'GET|POST');
    //  权限
    Route::rule('/admin/access/:con/[:act]', function($con, $act = null) use ($run) { return $run("Demon\\AdminThinkPHP\\access\\controller\\" . ucwords($con) . 'Controller', $act); }, 'GET|POST');
    //  例子
    Route::rule('/example/[:act]', function($act = null) use ($run) { return $run(Demon\AdminThinkPHP\example\Controller::class, $act); }, 'GET|POST');
    //  扩展图片例子
    Route::rule('/extend/image/[:act]', function($act = '') use ($run) { return $run(Demon\AdminThinkPHP\example\Extend::class, $act); }, 'GET|POST');
    //  加载自定义
    if (is_file(admin_path('route.php')))
        include admin_path('route.php');
    else include 'directory/route.php';
    //  自动解析
    Route::any(':mod/[:con]/[:act]', function($mod = '', $con = '', $act = '') use ($run) { return $run(null, $act, $mod, $con); });
})->middleware(array_merge([SessionPre::class, SessionInit::class, LoadLangPack::class, SessionPost::class], config('admin.middleware', []), [LogSave::class]));
