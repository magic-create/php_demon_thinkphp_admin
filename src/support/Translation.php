<?php

namespace Demon\AdminThinkPHP\support;

use Illuminate\Support\Traits\Macroable;
use think\Lang;

class Translation extends Lang
{
    use Macroable;

    /**
     * @var array AdminLang
     */
    public $loaded = [];


    /**
     * 翻译内容
     *
     * @param string|null $name
     * @param array       $vars
     * @param string      $range
     *
     * @return array|float|int|mixed|object|string|string[]
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function __($name = null, $vars = [], $range = '')
    {
        $range = $range ? : $this->getLangSet();
        $loaded = app('admin.translation')->loaded[$range] ?? [];
        // 空参数返回所有定义
        if (is_null($name))
            return $loaded;
        $value = strpos($name, '.') !== false ? arguer($name, $name, 'string', $loaded) : ($loaded[strtolower($name)] ?? $name);
        // 变量解析
        if (!empty($vars) && is_array($vars)) {
            // 数字索引解析
            if (key($vars) === 0) {
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            }
            // 关联索引解析
            else {
                $replace = array_keys($vars);
                foreach ($replace as &$v)
                    $v = "{:{$v}}";
                $value = str_replace($replace, $vars, $value);
            }
        }

        return $value;
    }


    /**
     * 继承父类读取文件
     *
     * @param string $file
     *
     * @return array
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function parse(string $file):array
    {
        return parent::parse($file);
    }

    /**
     * 将后置调整为大写
     *
     * @param string $name
     *
     * @return array
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function upper($name = '')
    {
        $name = $name ? : $this->getLangSet();
        $list = explode('-', $name);

        return $list[0] . (count($list) > 1 ? '-' . strtoupper($list[1]) : '');
    }
}
