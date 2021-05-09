<?php

namespace Demon\AdminThinkPHP\access\middleware;

use Closure;
use think\Request;

class SessionPost
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
        //  Locale
        $locale = cookie('admin_lang') ? : app()->lang->getLangSet();
        if (in_array($locale, array_keys(config('admin.locales'))))
            app()->lang->setLangSet($locale);
        //  Access
        if (config('admin.access')) {
            //  Uid
            $uid = session('uid') ? : 0;
            if ($uid)
                app('admin')->setUid($uid);
            //  Attribute
            $request->setRoute(['uid' => $uid]);
        }

        return $next($request);
    }
}
