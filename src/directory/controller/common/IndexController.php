<?php

namespace app\admin\controller\common;

use app\admin\controller\Controller;

class IndexController extends Controller
{
    protected $accessExcept = ['index'];

    function __initialize()
    {
        parent::__initialize();
    }

    /**
     * 首页
     *
     * @return mixed
     *
     * @copyright 魔网天创信息科技
     * @author    ComingDemon
     */
    public function index()
    {
        return admin_view('index');
    }
}
