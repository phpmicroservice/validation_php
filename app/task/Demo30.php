<?php

namespace app\task;

use pms\Task\Task;
use pms\Task\TaskInterface;

/**
 * Created by PhpStorm.
 * User: toplink_php1
 * Date: 2018/7/5
 * Time: 16:28
 */
class Demo30 extends Task implements TaskInterface
{
    public function run()
    {
        $aa = uniqid();
        output([
            __CLASS__,
            __FILE__, $aa
        ]);
        return $aa;
    }

    public function end()
    {
        $aa = uniqid();
        output([
            'end',
            __CLASS__,
            __FUNCTION__, $aa
        ]);
        return $aa;
    }

}