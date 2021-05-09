<?php

namespace Demon\AdminThinkPHP\access\model;

use think\Model;

class BaseModel extends Model
{
    public function __construct($data = [])
    {
        $this->connection = config('admin.connection', 'admin');
        $this->autoWriteTimestamp = false;
        $this->__initialize();
        parent::__construct($data);
    }

    public function __initialize()
    {
    }
}
