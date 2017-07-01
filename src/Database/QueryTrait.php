<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 29/06/2017
 * Time: 23:57
 */

namespace Hodor\Database;


/**
 * Common query functionality
 *
 * @package Hodor\Database
 */
trait QueryTrait
{
    public function query($where, $page = 0, $perPage = 10)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->where($where)
            ->orderByDesc('id')
            ->forPage($page, $perPage)
            ->get();
    }

    public function numOfRows($where)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->where($where)
            ->count();
    }

    public function queryOne($where)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->where($where)
            ->orderByDesc('id')
            ->get()
            ->first();
    }

    public function create($row)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->insert($row);
    }

    public function createOrUpdate($row)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->insertOrUpdate($row);
    }

    public function update($where, $row)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->where($where)
            ->update($row);
    }

    public function delete($where)
    {
        return $this->getConnection()
            ->table(static::TABLE)
            ->delete($where);
    }
}