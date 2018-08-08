<?php

namespace app\controller;

use app\Controller;

/**
 * 中文图片点击 imgcn
 * Class ImgcnController
 * @package apps\verify\controllers
 */
class Imgcn extends Controller
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
            'driver_name' => 'img_cn',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 200,
                'height' => 200,
            ]
        ];
        $CAPTCHA = new \app\logic\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->send($base64);
    }


}
