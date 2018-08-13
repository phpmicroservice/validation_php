<?php

namespace app\logic\driver;



/**
 * img 图片验证码生成驱动
 * @author Dongasai
 */
class img_base64  implements \app\logic\driver
{

    private $width=100;
    private $height=40;
    private $complex=4;
    private $type=1;
    private $stringent=false;
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
        $bgColor = imagecolorallocate($this->im, 0, 0, 0);
        imagefill($this->im, 0, 0, $bgColor);
    }

    /**
     * 设置干扰元素
     */
    private function setDisturb()
    {
        $area = ($this->width * $this->height) / 70;
        $disturbNum = ($area > 250) ? 250 : $area;

        //加入点干扰
        for ($i = 0; $i < $disturbNum; $i++) {
            $color = imagecolorallocate($this->im, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->im, rand(1, $this->width - 2), rand(1, $this->height - 2), $color);
        }
        //加入弧线
        for ($i = 0; $i <= $this->complex; $i++) {
            $color = imagecolorallocate($this->im, rand(128, 255), rand(125, 255), rand(100, 255));
            imagearc($this->im, rand(0, $this->width), rand(0, $this->height), rand(30, 300), rand(20, 200), 50, 30, $color);
        }
    }

    /**
     * 创建验证码
     */
    public function createCode()
    {
        $code='';
        $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";
        for ($i = 0; $i < $this->complex; $i++) {
            $code .= $str{rand(0, strlen($str) - 1)};
        }
        return $code;
    }

    /**
     * 设置验证码
     */
    private function setCaptcha($code)
    {

        for ($i = 0; $i < $this->complex; $i++) {
            $color = imagecolorallocate($this->im, rand(50, 250), rand(100, 250), rand(128, 250));
            $size = 5;
            $x = floor($this->width / $this->complex) * $i + 5;
            $y = rand(0, $this->height - 20);
            imagechar($this->im, $size, $x, $y, $code{$i}, $color);
        }
    }

    /**
     * 真是验证码
     */
    private function show()
    {
        $filename=RUNTIME_DIR.'/cache/'.uniqid();

        if (imagetypes() & IMG_JPG) {
            $houzhui='jpeg';
            $filename.='.jpg';
            imagejpeg($this->im,$filename);
        } elseif (imagetypes() & IMG_GIF) {
            $houzhui='gif';
            $filename.='.gif';
            imagegif($this->im,$filename);
        } elseif (imagetype() & IMG_PNG) {
            $houzhui='png';
            $filename.='.png';
            imagepng($this->im,$filename);
        } else {
            throw new \Phalcon\Exception('Don\'t support image type!');
        }
        var_dump($filename);
        $fp = fopen($filename,"rb", 0);
        $gambar = fread($fp,filesize($filename));
        $re = base64_encode( $gambar );
        unlink($filename);
        return 'data:image/'.$houzhui.';base64,'.$re;
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
        if(!$this->stringent){
            $code=strtolower($code);
            $value=strtolower($value);
        }
        if($code ==$value ){
            return true;
        }
        return false;
    }

}
