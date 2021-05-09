<?php

namespace Demon\AdminThinkPHP;

use think\DbManager;
use think\Facade;

/**
 * Class DB
 * @see     DbManager
 * @mixin DbManager
 * @package Demon\AdminThinkPHP
 */
class DB extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'think\DbManager';
    }
}
