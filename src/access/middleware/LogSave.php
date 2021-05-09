<?php

namespace Demon\AdminThinkPHP\access\middleware;

use Closure;
use think\Request;

class LogSave
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
        //  Response
        $response = $next($request);
        //  Log
        if (config('admin.access') && config('admin.log'))
            app()->invoke([config('admin.log'), 'saveLog'], ['response' => $response]);

        //  Return
        return $response;
    }
}
