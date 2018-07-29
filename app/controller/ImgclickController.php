<?php

namespace apps\verify\controllers;

use core\Sundry\Trace;
use logic\Common\Verification;

class ImgclickController extends CoreController
{
    public function initialize()
    {


    }

    /**
     * 获取图形验证码
     */
    public function getimg()
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
        $CAPTCHA = new \core\verify\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->restful_success($base64);
    }



}
