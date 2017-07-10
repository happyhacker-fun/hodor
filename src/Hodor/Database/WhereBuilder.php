<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 29/06/2017
 * Time: 23:51
 */

namespace Hodor\Database;


use Illuminate\Database\Query\Builder;

/**
 * Build where closure for Laravel Query Builder to ease the work.
 * More complicated where should be build manually.
 */
class WhereBuilder
{

    public static function build(array $conditions)
    {
        return function (Builder $builder) use ($conditions) {
            foreach ($conditions as $condition) {
                call_user_func_array([$builder, 'where'], $condition);
            }
        };
    }

    public static function buildOr(array $conditions)
    {
        return function (Builder $builder) use ($conditions) {
            foreach ($conditions as $key => $condition) {
                call_user_func_array([$builder, 'orWhere'], $condition);
            }
        };
    }
}