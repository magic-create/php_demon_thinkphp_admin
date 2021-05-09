<?php

namespace Demon\AdminThinkPHP\access\model;

use Demon\AdminThinkPHP\DB;

class RoleModel extends BaseModel
{
    public function __initialize()
    {
        $this->table = 'admin_role';
        $this->primaryKey = 'rid';
        parent::__initialize();
    }

    public static function fieldStore($field = '')
    {
        $store = [
            'status' => [0 => app('admin')->access->getLang('status_0'), 1 => app('admin')->access->getLang('status_1')],
            'menu' => app('admin')->access->getAccessTreeDeep(MenuModel::get())
        ];

        return $field ? ($store[$field] ?? null) : $store;
    }

    public static function fieldList()
    {
        $model = new self();

        return DB::connect($model->getConnectionName())->getColumnListing($model->getTable());
    }

    public static function updateStatus($rid, $status)
    {
        if (!$rid)
            return DEMON_CODE_PARAM;

        return self::where('system', 0)->whereIn('rid', is_array($rid) ? $rid : explode(',', $rid))->update(['status' => (int)$status, 'updateTime' => mstime()]) ? true : DEMON_CODE_COND;
    }

    public static function checkInfo($data = [], $info = [])
    {
        $store = self::fieldStore();
        $reData = app('admin')->api->validator([
            'name' => ['rule' => 'required|string|min:2|max:64'],
        ], [], [], $data);
        if (!error_check($reData))
            return $reData;
        $reData['mids'] = $reData['mids'] ?? '';
        if ($reData['mids']) {
            $mids = array_unique(explode(',', $reData['mids']));
            $reData['mids'] = count($store['menu']) == count($mids) ? '*' : implode(',', $mids);
        }
        $reData['mids'] = $reData['mids'] ? : null;
        $reData['remark'] = $reData['remark'] ? : null;
        $field = self::fieldList();
        foreach ($reData as $key => $val) {
            if (!in_array($key, $field))
                unset($reData[$key]);
        }

        return $reData;
    }

    public static function add($data = [])
    {
        $reData = self::checkInfo($data);
        if (!error_check($reData))
            return $reData;
        $reData += ['createTime' => mstime()];

        $reData['rid'] = self::insertGetId($reData);

        return $reData['rid'] ? $reData : DEMON_CODE_DATA;
    }

    public static function edit($rid, $data = [])
    {
        $info = self::find($rid);
        if (!$info)
            return DEMON_CODE_PARAM;
        $reData = self::checkInfo($data, $info);
        if (!error_check($reData))
            return $reData;
        $reData += ['updateTime' => mstime()];
        foreach ($reData as $key => $val) {
            if ($val == $info->{$key})
                unset($reData[$key]);
        }

        return self::where('rid', $rid)->update($reData) ? true : DEMON_CODE_DATA;
    }

    public static function del($rid)
    {
        if (!$rid)
            return DEMON_CODE_PARAM;

        return self::whereIn('rid', $rid)->where('system', 0)->delete() ? true : DEMON_CODE_DATA;
    }
}
