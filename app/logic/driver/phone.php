<?php
/**
 * Created by PhpStorm.
 * User: Dongasai
 * Date: 2017/5/7
 * Time: 22:47
 */

namespace app\logic\driver;


class phone implements \app\logic\driver
{
    private $config = [
        'name' => '用户'
    ];
    # 发送给谁
    private $name = '';
    private $identifying = '';

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        foreach ($this->config as $name => $value) {
            $this->$name = $value;
        }

    }

    /**
     * 创建验证码
     */
    public function createCode()
    {
        return mt_rand(10000, 99999);
    }

    /**
     * 发送验证码
     * @param $code
     */
    public function getCaptcha($code)
    {
        $re = \core\message\SMS\facade::sendV($this->identifying,
            $this->name,
            $code
        );
        return $re;

    }


}