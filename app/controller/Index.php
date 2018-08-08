<?php

namespace app\controller;


use app\Controller;

class Index extends Controller
{

    /**
     * 获取验证码类型
     */
    public function index()
    {
        $browser = $this->getData('browser', 'null');
        $ip = $this->getData('ip', '0.0.0.0');
        //初始化一个验证
        $re = $this->lService->verify_type($browser, $ip, $this->sn, $this->operation);
        $this->session->set(md5($this->sn . $this->operation),$re['identifying']);
        return $this->send($re);

    }

    /**
     * 测试验证
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function check_virtual()
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
            $this->gCache->save($this->identifying, 1);
            //验证通过
            $re_56 = $this->lService->check_virtual($type, $this->identifying);
            return $this->send($re_56);
        }
        return $this->send($re);
    }

    public function get_signature()
    {
        return $this->send([
            'time' => RUN_TIME,
            'signature' => sha1(RUN_TIME . 'D2JSfznLKXo7dB30M3pNLeeidbKXddUx')
        ]);
    }


}
