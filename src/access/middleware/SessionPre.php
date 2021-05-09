<?php

namespace Demon\AdminThinkPHP\access\middleware;

use Closure;
use think\Request;

class SessionPre
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //  AdminSession
        $config = config('session');
        foreach (config('admin.session') as $key => $val)
            $config[$key] = $val;
        //  SetSession
        app('config')->set($config, 'session');
        //  File
        if (!is_dir(config('session.path')))
            bomber()->dirMake(config('session.path'));

        //  Next
        return $next($request);
    }
}
