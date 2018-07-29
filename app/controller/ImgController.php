<?php

namespace apps\verify\controllers;

use core\Sundry\Trace;
use logic\Common\Verification;

class ImgController extends CoreController
{
    public function initialize()
    {


    }

    /**
     * 获取图形验证码 dragging
     */
    public function getimg()
    {

        $config = [
            'driver_name' => 'img_base64',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 100,
                'height' => 30,
            ]
        ];
        $CAPTCHA = new \core\verify\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->restful_success($base64);
    }



}
