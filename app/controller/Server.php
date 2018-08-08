<?php

namespace app\controller;

use app\Controller;

/**
 * 服务间的使用
 * Class Service
 * @package app\controller
 */
class Server extends Controller
{


    /**
     * 真实验证
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function true_check()
    {
        $value = $this->getData('value');
        $type = $this->getData('type');
        # 增加图形验证码验证
        $config = [
            'driver_name' => $type,
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'identifying' => $this->identifying,
            ]
        ];
        $CAPTCHA = new \app\logic\production($config);
        $re = $CAPTCHA->check_virtual($value);
        if ($re === true) {
            //验证通过
            $re_56 = $this->lService->true_check($this->identifying);
            return $this->send($re_56);
        }
        return $this->send(false);



    }


}