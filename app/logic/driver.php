<?php

namespace app\logic;

/**
 * Class core 验证码的核心类
 * @package core\verify\driver
 */
interface driver
{
    /**
     * 创建验证码
     * @return mixed
     */
    public function createCode();

    /**
     * 创建验证码/发送验证码
     * @param $code
     * @return mixed
     */
    public function getCaptcha($code);

    public function check($code ,$value);

}
