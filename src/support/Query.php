<?php

namespace Demon\AdminThinkPHP\support;

use Illuminate\Support\Collection;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\helper\Arr;
use think\Model;

/**
 * Class DbManager
 * @package think
 * @mixin BaseQuery
 * @mixin Query
 */
class Query extends \think\db\Query
{
    /**
     * 获取数据
     *
     * @param array|string $columns
     *
     * @return Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function first($columns = ['*'])
    {
        $data = $columns && !$this->getOptions('field') ? $this->field(Arr::wrap($columns))->find() : $this->find();

        return $data ? : null;
    }

    /**
     * 获取列表
     *
     * @param array|string $columns
     *
     * @return \think\Collection|Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function get($columns = ['*'])
    {
        $list = $columns && !$this->getOptions('field') ? $this->field(Arr::wrap($columns))->select() : $this->select();

        return !$list->isEmpty() ? $list : collect([]);
    }

    /**
     * 定义表名
     *
     * @param $table
     * @param $alias
     *
     * @return Query
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function from($table, $alias)
    {
        return $this->table([$table => $alias]);
    }

    /**
     * 清空表
     *
     * @return mixed
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function truncate()
    {
        return $this->batchQuery(["truncate {$this->getTable()}"]);
    }

    /**
     * 获取字段列表
     *
     * @return array
     * @author    ComingDemon
     * @copyright 魔网天创信息科技
     */
    public function getColumnListing()
    {
        return array_keys($this->getFields($this->getTable()));
    }
}
