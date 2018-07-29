<?php

namespace apps\verify\controllers;

use core\Sundry\Trace;
use logic\Common\Verification;

class IndexController extends CoreController
{


    /**
     * 获取验证码类型
     */
    public function index()
    {
        //
        $re = $this->lService->verify_type($_SERVER['HTTP_USER_AGENT'],$_SERVER['REMOTE_ADDR'],$this->appid,$this->operation);
        return $this->restful_return($re);

    }

    /**
     * 测试验证
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function check_virtual()
    {
        $value = $this->request->get('value');
        $type = $this->request->get('type');
        # 增加图形验证码验证
        $config = [
            'driver_name' => $type,
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'identifying' => $this->identifying,
            ]
        ];
        $CAPTCHA = new \core\verify\production($config);
        $re = $CAPTCHA->check_virtual($value);
        if ($re === true) {
            $this->session->set($this->identifying,1);
            //验证通过
            $re_56 = $this->lService->check_virtual($type,$this->identifying);
            return $this->restful_return($re_56);
        }
        return $this->restful_return($re);
    }

    /**
     * 真实验证
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function true_check()
    {
        $time = $this->request->get('time', 'int',time());
        $signature = $this->request->get('signature', 'string');

        //验证通过
        $re_56 = $this->lService->true_check($this->identifying,$this->appid,$this->operation,$time,$signature);
        return $this->restful_return($re_56);

    }
    public function get_signature()
    {
        return $this->restful_return([
            'time'=>RUN_TIME,
            'signature'=>sha1(RUN_TIME.'D2JSfznLKXo7dB30M3pNLeeidbKXddUx')
        ]);
    }




}
