<?php

namespace Demon\AdminThinkPHP\response;

use think\response\Json;
use think\response\Jsonp;

trait View
{
    /**
     * 模板变量赋值
     *
     * @param      $name
     * @param null $value
     *
     * @return $this
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function assign($name, $value = null)
    {
        app('admin.view')->share($name, $value);

        return $this;
    }

    /**
     * 解析和获取模板内容 用于输出
     *
     * @param       $template
     * @param array $vars
     *
     * @return string
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function fetch($template, $vars = [])
    {
        $template = str_replace('@', '::', $template);
        $template = mb_stripos($template, '::') === false ? 'admin::' . $template : $template;

        return app('admin.view')->make($template, $vars)->render();
    }

    /**
     * 返回json
     *
     * @param array $data
     * @param int   $code
     * @param array $header
     * @param array $options
     *
     * @return Json
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function json($data = [], $code = 200, $header = [], $options = [])
    {
        return json($data, $code, $header, $options);
    }

    /**
     * 返回jsonp
     *
     * @param array $data
     * @param int   $code
     * @param array $header
     * @param array $options
     *
     * @return Jsonp
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    protected function jsonp($data = [], $code = 200, $header = [], $options = [])
    {
        return jsonp($data, $code, $header, $options);
    }

}
