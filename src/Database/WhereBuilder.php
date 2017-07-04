<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 29/06/2017
 * Time: 23:51
 */

namespace Hodor\Database;


use Illuminate\Database\Query\Builder;

class WhereBuilder
{
    /**
     * Build where closure for Laravel Query Builder to ease the work
     *
     * @param array $conditions
     * @param array $ors
     * @return \Closure
     */
    public static function build(array $conditions, array $ors = [])
    {
        $where = function (Builder $builder) use ($conditions, $ors) {
            foreach ($conditions as $condition) {
                call_user_func_array([$builder, 'where'], $condition);
            }

            if (empty($ors)) {
                return;
            }
            foreach ($ors as $key => $or) {
                call_user_func_array([$builder, 'orWhere'], $or);
            }
        };

        return $where;
    }
}