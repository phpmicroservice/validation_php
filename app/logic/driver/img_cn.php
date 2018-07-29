<?php

namespace core\verify\driver;


/**
 * img 图片中文点击验证码
 * @author Dongasai
 */
class img_cn implements \core\verify\driver
{

    private $msg = '请依次点击文字:';
    private $width = 200;
    private $char_number = 6;#文字个数
    private $click_number = 3;# 点击个数
    private $height = 200;
    private $type = 1;
    private $im;

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
     * 创建验证码 169_107-168_45
     */
    public function createCode()
    {
        $arr = [];
        for ($i = 0; $i < $this->click_number; $i++) {
            $x = rand(10, $this->width - 10);
            $y = rand(10, $this->height - 10);
            $arr [$i] = $x . '_' . $y;
        }
        return join('-', $arr);
    }

    /**
     * 验证是否正确
     * @param $code 储存的内容
     * @param $value 提交的内容
     */
    public function check($code, $value)
    {
        $code_arr = explode('-', $code);
        $value_arr = explode('-', $value);
        foreach ($code_arr as $k => $code2) {

            $code_arr2 = explode('_', $code2);
            $value_arr2 = explode('_', $value_arr[$k]);
            $x = abs($code_arr2[0] - $value_arr2[0]);
            $y = abs($code_arr2[1] - $value_arr2[1]);
            if (!($x < 10 && $y < 10)) {
                \output('infoe', [$code_arr2, $value_arr2]);
                return false;
            }
        }
        return true;

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
        $info = base64_encode($i);
        return [
            'img' => $info,
            'msg' => $this->msg
        ];
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
        $cn2000 = include('img_cn/cn.php');
        $text = [];
        for ($i = 0; $i < $this->char_number; $i++) {
            $suanji = mt_rand(0, 499);
            $text[$i] = $cn2000[$suanji];
        }
        $code_arr = explode('-', $code);

        for ($i = 0; $i < $this->char_number; $i++) {
            $textColor = imagecolorallocate($this->im, rand(20, 200), rand(20, 200), rand(20, 200));
            if (isset($code_arr[$i])) {
                $this->msg = $this->msg . $text[$i];
                $zxy = explode('_', $code_arr[$i]);
                $textX = $zxy[0] - 7;
                $textY = $zxy[1] + 7;
            } else {
                $textX = rand(5, $this->width - 5);
                $textY = rand(5, $this->height - 5);
            }
            imagettftext($this->im, 15, rand(20, 50), $textX, $textY, $textColor, WWW_DIR . "/core/verify/driver/img_cn/fangzhengkaitijian.ttf", $text[$i]);
        }

    }

}
