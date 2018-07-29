<?php

namespace apps\verify\controllers;

use core\Sundry\Trace;
use logic\Common\Verification;

/**
 * 中文图片点击 imgcn
 * Class ImgcnController
 * @package apps\verify\controllers
 */
class ImgcnController extends CoreController
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
            'driver_name' => 'img_cn',
            'store_name' => 'Sql',
            'identifying' => $this->identifying,
            'driver_config' => [
                'width' => 200,
                'height' => 200,
            ]
        ];
        $CAPTCHA = new \core\verify\production($config);
        $base64 = $CAPTCHA->getCaptcha();
        return $this->restful_success($base64);
    }


}
