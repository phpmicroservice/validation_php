<?php

namespace apps\verify\controllers;

use core\Sundry\Trace;
use logic\Common\Verification;

class DraggingController extends CoreController
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
            'driver_name' => 'dragging',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 100,
                'height' => 15,
            ]
        ];
        $CAPTCHA = new \core\verify\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->restful_success($base64);
    }


}
