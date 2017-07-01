<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 30/06/2017
 * Time: 21:39
 */

namespace Hodor\Controller;


class Hello extends BaseController
{
    public function world()
    {
        return $this->withStatus(400)
            ->respond('hello', 10000);
    }

    public function query()
    {

    }
}