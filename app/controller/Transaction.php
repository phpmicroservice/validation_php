<?php

namespace app\controller;

/**
 * 事务控制器
 * Class Transaction
 * @package app\controller
 */
class Transaction extends \app\Controller
{
    public function create()
    {
        var_dump(1414.3);
        $name = $this->getData('name');
        $data = $this->getData('data');
        $xid = $this->getData('xid');
        $class_name='app\\task\\'.ucfirst($name).'Tx';
        var_dump($class_name);
        if(!class_exists($class_name)){
            $this->send('class_not_exists');
        }
        $task_data=[
            'xid'=>$xid,
            'name'=>ucfirst($name).'Tx',
            'data'=>$data
        ];
        $re = $this->swoole_server->task($task_data, -1);
        var_dump($re);
        $this->send(true);
    }

}