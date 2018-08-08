<?php

namespace app\controller;


use app\Controller;

class Imgclick extends Controller
{
    public function initialize()
    {


    }

    /**
     * 获取图形验证码
     */
    public function getinfo()
    {

        $config = [
            'driver_name' => 'img_click',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 100,
                'height' => 100,
            ]
        ];
        $CAPTCHA = new \app\logic\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->send($base64);
    }



}
