<?php

namespace app\controller;


use app\Controller;

class Imgbase64 extends Controller
{
    public function initialize()
    {


    }

    /**
     * 获取图形验证码 dragging
     */
    public function getinfo()
    {

        $config = [
            'driver_name' => 'img_base64',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 70,
                'height' =>25,
            ]
        ];
        $CAPTCHA = new \app\logic\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->send($base64);
    }



}
