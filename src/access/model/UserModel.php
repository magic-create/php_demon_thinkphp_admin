<?php

namespace Demon\AdminThinkPHP\access\model;


use Demon\AdminThinkPHP\DB;

class UserModel extends BaseModel
{
    public function __initialize()
    {
        $this->name = 'admin_user';
        $this->pk = 'uid';
        parent::__initialize();
    }

    public static function fieldStore($field = '')
    {
        $store = [
            'role' => app('admin')->access->getRoleList(true),
            'status' => [0 => app('admin')->access->getLang('status_0'), 1 => app('admin')->access->getLang('status_1')],
        ];

        return $field ? ($store[$field] ?? null) : $store;
    }

    public static function findAndRids($uid)
    {
        return self::from((new UserModel())->getTable(), 'a')
                   ->leftJoin([AllotModel::field(DB::raw('GROUP_CONCAT(rid) as role,uid'))->group('uid')->buildSql() => 'b'], 'b.uid = a.uid')
                   ->where('a.uid', $uid)
                   ->field(['a.*', 'b.role'])
                   ->first();
    }

    public static function updateStatus($uid, $status)
    {
        if (!$uid)
            return DEMON_CODE_PARAM;

        $update = ['status' => (int)$status, 'updateTime' => mstime()];
        if ((int)$status == -1)
            $update += ['username' => null];

        return self::where('system', 0)->whereIn('uid', is_array($uid) ? $uid : explode(',', $uid))->update($update) ? true : DEMON_CODE_COND;
    }

    public static function checkInfo($data = [], $info = [])
    {
        $reData = app('admin')->api->validator([
            'username' => ['rule' => 'require|min:2|max:32'],
        ], [], [], $data);
        if (!error_check($reData))
            return $reData;
        $reData['avatar'] = ($reData['avatar'] ?? '') ? : null;
        $reData['nickname'] = ($reData['nickname'] ?? '') ? : $reData['username'];
        $reData['remark'] = ($reData['remark'] ?? '') ? : null;
        if ($reData['password'] ?? null) {
            $reData['password'] = bomber()->password(['content' => $reData['password'], 'action' => 'hash']);
            $reData['passwordTime'] = mstime();
        }
        else unset($reData['password']);
        if ($reData['username']) {
            $uniqueUsername = self::where('username', $reData['username'])->where('uid', '<>', $info['uid'] ?? 0)->first();
            if ($uniqueUsername)
                return error_build(DEMON_CODE_PARAM, app('admin')->access->getLang('error_username_unique'));
        }
        $field = self::getColumnListing();
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

        $reData['uid'] = self::insertGetId($reData);
        if ($data['role'] ?? [])
            AllotModel::updateList($reData['uid'], $data['role']);

        return $reData['uid'] ? $reData : DEMON_CODE_DATA;
    }

    public static function password($type, $account, $password)
    {
        if (!$account || !$password)
            return error_build(DEMON_CODE_PARAM);
        $user = self::where($type, $account)->first();
        if (!$user || $user->{$type} != $account || $user->status < 1)
            return error_build(DEMON_CODE_PARAM, app('admin')->__('base.auth.error_account'));
        if (!bomber()->password(['action' => 'verify', 'hash' => $user->password, 'content' => $password]))
            return error_build(DEMON_CODE_COND, app('admin')->__('base.auth.error_password'));

        return $user;
    }

    public static function reset($uid, $password)
    {
        return self::where('uid', $uid)->update([
            'password' => bomber()->password(['content' => $password, 'action' => 'hash']),
            'updateTime' => mstime()
        ]) ? true : DEMON_CODE_DATA;
    }

    public static function edit($uid, $data = [])
    {
        $info = self::find($uid);
        if (!$info)
            return DEMON_CODE_PARAM;
        $reData = self::checkInfo($data, $info);
        if (!error_check($reData))
            return $reData;
        foreach ($reData as $key => $val) {
            if ($val == $info->{$key})
                unset($reData[$key]);
        }
        if (isset($data['role']))
            AllotModel::updateList($uid, $data['role']);

        return self::where('uid', $uid)->update($reData + ['updateTime' => mstime()]) ? $reData : DEMON_CODE_DATA;
    }
}
