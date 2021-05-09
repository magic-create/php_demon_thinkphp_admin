<?php

namespace Demon\AdminThinkPHP\access\controller;

use Demon\AdminThinkPHP\access\model\LogModel;
use Demon\AdminThinkPHP\access\table\LogTable;
use Demon\AdminThinkPHP\Controller;

class LogController extends Controller
{
    public function __initialize()
    {
        parent::__initialize();
    }

    public function index(LogTable $table)
    {
        switch (arguer($table->config->actionName)) {
            case 'export':
                return $table->export();
                break;
            default:
                return $table->render('preset.access.log', ['access' => app('admin')->access]);
                break;
        }
    }

    public function info()
    {
        $info = LogModel::findAndFormat(arguer('lid', 0, 'abs'));
        if (!$info)
            abort(DEMON_CODE_PARAM);

        return admin_view('preset.access.log_info', ['info' => $info, 'access' => app('admin')->access]);
    }

    public function del()
    {
        app('admin')->log->setBreak(true);
        $this->api->check(LogModel::del(arguer('lid')));

        return $this->api->send();
    }

    public function clear()
    {
        $this->api->check(LogModel::clear());

        return $this->api->send();
    }
}
