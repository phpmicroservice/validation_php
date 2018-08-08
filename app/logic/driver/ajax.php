<?php

namespace app\logic\driver;

/**
 * img 图片验证码生成驱动
 * @author Dongasai
 */
class ajax  implements \app\logic\driver
{


    /**
     * img constructor.
     * 初始化
     * @param array $config
     */
    public function __construct($config = [])
    {

    }

    /**
     * 获取验证码
     */
    function getCaptcha($code)
    {

    }


    /**
     * 创建图片
     */
    private function createImg()
    {

    }

    /**
     * 设置干扰元素
     */
    private function setDisturb()
    {

    }

    /**
     * 创建验证码
     */
    public function createCode()
    {

    }

    /**
     * 设置验证码
     */
    private function setCaptcha($code)
    {


    }

    /**
     * 真是验证码
     */
    private function show()
    {

    }

    /**
     * 验证是否正确
     * @param $code 储存的内容
     * @param $value 提交的内容
     */
    public function check($code ,$value)
    {
       return true;
    }

}
