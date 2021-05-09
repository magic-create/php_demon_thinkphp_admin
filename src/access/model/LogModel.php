<?php

namespace Demon\AdminThinkPHP\access\model;

class LogModel extends BaseModel
{
    public function __initialize()
    {
        $this->table = 'admin_log';
        $this->primaryKey = 'lid';
        parent::__initialize();
    }

    public static function findAndFormat($lid)
    {
        return self::from((new LogModel())->getTable(), 'a')
                   ->leftJoin([(new UserModel())->getTable() => 'b'], 'b.uid = a.uid')
                   ->where('a.lid', $lid)
                   ->field('a.*,INET_NTOA(a.ip) as ip,b.username,b.remark as userRemark')
                   ->first();
    }

    public static function del($lid)
    {
        self::whereIn('lid', is_array($lid) ? $lid : [$lid])->delete();

        return true;
    }

    public static function clear()
    {
        self::truncate();

        return true;
    }
}
