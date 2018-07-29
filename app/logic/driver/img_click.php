<?php

namespace core\verify\driver;

use core\Sundry\Trace;

/**
 * img_click 图片点击验证码 生成驱动
 * @author Dongasai
 */
class img_click  implements \core\verify\driver
{

    private $width=200;
    private $height=200;
    private $type=1;
    private $im;


    /**
     * img constructor.
     * 初始化
     * @param array $config
     */
    public function __construct($config = [])
    {
        if ($config) {
           foreach ($config as $k=>$v){
                $this->$k=$v;
           }
        }
    }

    /**
     * 获取验证码
     */
    function getCaptcha($code)
    {
        //创建图片
        $this->createImg();
        //设置干扰元素
        $this->setDisturb();
        //设置验证码
        $this->setCaptcha($code);
        //返回图片对象
        return $this->show();
    }


    /**
     * 创建图片
     */
    private function createImg()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($this->im, rand(150, 255), rand(150, 255), rand(150, 255));
        imagefill($this->im, 0, 0, $bgColor);
    }

    /**
     * 设置干扰元素
     */
    private function setDisturb()
    {


        //加入点干扰
        for ($i = 0; $i < 200; $i++) {
            $color = imagecolorallocate($this->im, rand(0,150), rand(0, 150), rand(0, 150));
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }

    }

    /**
     * 创建验证码
     */
    public function createCode()
    {
        $x =   rand(5, $this->width - 5);
        $y = rand(5, $this->height - 5);

        return $x.'_'.$y;
    }

    /**
     * 设置验证码
     */
    private function setCaptcha($code)
    {

        $code_arr=explode('_',$code);

        $color = imagecolorallocate($this->im, rand(100,200),rand(100,200),rand(100,200));
            imagefilledellipse($this->im,$code_arr[0],$code_arr[1],18,18,$color);

    }

    /**
     * 真是验证码
     */
    private function show()
    {
        if (imagetypes() & IMG_JPG) {
            header('Content-type:image/jpeg');
            imagejpeg($this->im);
        } elseif (imagetypes() & IMG_GIF) {
            header('Content-type: image/gif');
            imagegif($this->im);
        } elseif (imagetype() & IMG_PNG) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            throw new \Phalcon\Exception('Don\'t support image type!');

        }
        $i = ob_get_clean();
        return base64_encode( $i );
    }

    /**
     * 验证是否正确
     * @param $code 储存的内容
     * @param $value 提交的内容
     */
    public function check($code ,$value)
    {

        // TODO: Implement check() method.
        \output('production-check',[$code, $value]);
        if (empty($code)) {
            return false;
        }
        $code_arr=explode('_',$code);
        $value_arr=explode('_',$value);


        if(abs($code_arr[0]-$value_arr[0])<8 && abs($code_arr[1]-$value_arr[1])<8  ){
            return true;
        }
        return false;
    }

}
