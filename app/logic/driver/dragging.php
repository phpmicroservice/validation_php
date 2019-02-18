<?php

namespace app\logic\driver;


/**
 * 滑动 验证码
 * Class dragging
 * @package core\verify\driver
 */
class dragging implements \app\logic\driver
{

    private $width = 100;
    private $height = 15;

    /**
     * img constructor.
     * 初始化
     * @param array $config
     */
    public function __construct($config = [])
    {
        if ($config) {
            foreach ($config as $k => $v) {
                $this->$k = $v;
            }
        }
    }

    /**
     * 创建验证码
     */
    public function createCode()
    {
        $x = rand(20, $this->width - 20);
        return $x;
    }

    /**
     * 验证是否正确
     * @param $code 储存的内容
     * @param $value 提交的内容
     */
    public function check($code, $value)
    {
        // TODO: Implement check() method.
       \pms\output('production-check', [$code, $value]);
        if (empty($code)) {
            return false;
        }

        if (abs($code - $value) < 8) {
            return true;
        }
        return false;
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
        return base64_encode($i);
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
        for ($i = 0; $i < bcdiv($this->width, 2, 0); $i++) {
            $color = imagecolorallocate($this->im, rand(0, 250), rand(0, 250), rand(0, 250));
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }

    }


    /**
     * 设置验证码
     */
    private function setCaptcha($code)
    {

        # 获取颜色
        $color = imagecolorallocate($this->im, rand(0, 250), rand(0, 250), rand(0, 250));
        # 画一个椭圆
        $ret = imagefilledellipse($this->im, (int)$code, $this->height / 2, $this->height / 2, $this->height, $color);

    }
}