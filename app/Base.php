<?php

namespace app;


/**
 * 基类
 * Class Controller
 * @property \pms\Cache\Backend\Redis $gCache
 * @property \Phalcon\Cache\BackendInterface $sessionCache
 * @property \Phalcon\Config $dConfig
 * @property \Phalcon\Validation\Message\Group $message
 * @property \pms\bear\ClientSync $proxyCS
 * @property \swoole_server $swooleServer
 * @package app\controller
 */
class Base extends \Phalcon\Di\Injectable
{
    public $swooleServer;


    /**
     * 设置 $swooleServer
     * @param \Swoole\Server $swooleServer
     */
    public function setSwooleServer(\Swoole\Server $swooleServer)
    {
        $this->swooleServer = $swooleServer;
    }
}