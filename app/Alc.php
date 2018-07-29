<?php

namespace app;

use pms\Dispatcher;


/**
 * Class Alc
 * @package app
 */
class Alc extends Base
{
    public $user_id;
    public $serverTask = [
        'server', 'index', 'transaction'
    ];

    /**
     *
     * beforeDispatch 在调度之前
     * @param \Phalcon\Events\Event $Event
     * @param \Phalcon\Mvc\Dispatcher $Dispatcher
     * @return
     */
    public function beforeDispatch(\Phalcon\Events\Event $Event, \pms\Dispatcher $dispatcher)
    {
        if ($dispatcher->getTaskName() == 'demo') {
            return true;
        }
        if (in_array($dispatcher->getTaskName(), $this->serverTask)) {
            # 进行服务间鉴权
            return $this->server_auth($dispatcher);
        }
        if (empty($dispatcher->session)) {
            $dispatcher->connect->send_error('没有初始化session!!', [], 500);
            return false;
        }
        # 进行rbac鉴权
        if ($dispatcher->session->get('user_id') > 0) {
            # 登录即可访问
            return true;
        }

        $dispatcher->connect->send_error('没有权限!!', [$dispatcher->session->get('user_id')], 401);
        return false;
    }

    /**
     * 服务间的鉴权
     * @return bool
     */
    private function server_auth(Dispatcher $dispatcher)
    {
        $key = $dispatcher->connect->accessKey??'';
        output([APP_SECRET_KEY, $dispatcher->connect->getData(), $dispatcher->connect->f], 'verify_access');
        if (!verify_access($key, APP_SECRET_KEY, $dispatcher->connect->getData(), $dispatcher->connect->f)) {
            $dispatcher->connect->send_error('accessKey-error', [], 412);
            return false;
        }
        return true;
    }

}