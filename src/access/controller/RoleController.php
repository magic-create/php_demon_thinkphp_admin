<?php

namespace Demon\AdminThinkPHP\access\controller;

use Demon\AdminThinkPHP\access\model\RoleModel;
use Demon\AdminThinkPHP\access\table\RoleTable;
use Demon\AdminThinkPHP\Controller;

class RoleController extends Controller
{
    public function __initialize()
    {
        parent::__initialize();
    }

    public function index(RoleTable $table)
    {
        return $table->render('preset.access.role', ['access' => app('admin')->access]);
    }

    public function add()
    {
        if (DEMON_SUBMIT) {
            $this->api->check(RoleModel::add(arguer('data', [], 'array')));

            return $this->api->send();
        }
        else {
            $store = RoleModel::fieldStore();
            $tree = app('admin')->access->getRoleTree($store['menu']);

            return admin_view('preset.access.role_info', ['access' => app('admin')->access, 'tree' => $tree]);
        }
    }

    public function edit()
    {
        $info = RoleModel::find(arguer('rid', 0, 'abs'));
        if (!$info)
            abort(DEMON_CODE_PARAM);
        if (DEMON_SUBMIT) {
            $data = arguer('data', [], 'array');
            $this->api->check(RoleModel::edit($info->rid, $data));
            //  设置记录
            $this->log->setTag('admin.access.role')->setContent(bomber()->arrayDiffer($data, $info));

            return $this->api->send();
        }
        else {
            $store = RoleModel::fieldStore();
            $tree = app('admin')->access->getRoleTree($store['menu'], $info->rid);

            return admin_view('preset.access.role_info', ['info' => $info, 'access' => app('admin')->access, 'tree' => $tree]);
        }
    }

    public function status()
    {
        return response()->json(['code' => DEMON_CODE_ACCESS, 'message' => __('admin::base.error.' . DEMON_CODE_ACCESS)], DEMON_CODE_ACCESS);
        $this->api->check(RoleModel::updateStatus(arguer('rid'), arguer('status', false, 'bool')));

        return $this->api->send();
    }

    public function del()
    {
        $this->api->check(RoleModel::del(arguer('rid')));

        return $this->api->send();
    }
}
