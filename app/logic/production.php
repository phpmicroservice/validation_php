<?php

namespace app\logic;
use app\Base;

/**
 * 验证码的生成
 * @author Dongasai
 * @property driver $driver
 */
class production extends Base
{
    #配置信息
    protected $config = [
        'driver_name' => 'img_base64',
        'store_name' => 'Sql',
        'prefix'=>'',
        'identifying'=>'',
        'overtime' => 600,
        'driver_config' => [] #这是给验证码驱动使用的配置信息
    ];
    # identifying 验证码标示
    private $identifying = '';
    # 前缀
    private $prefix = '';
    # 储存驱动名字
    private $store_name = 'Session';
    # 储存驱动
    private $store;
    # 驱动名字
    private $driver_name = 'img';
    # 过期时间
    private $overtime = 600;
    # 是否严格验证
    private $stringent=false;
    # 验证码驱动
    private $driver;
    # 驱动配置
    private $driver_config = [];

    /**
     * 初始化
     */
    public function  __construct ($config)
    {
        $validation = new \pms\Validation();
        $validation->validate();
        $this->config = array_merge($this->config, $config);
        foreach ($this->config as $name => $value) {
            $this->$name = $value;
        }

    }

    /**
     * 获取验证码
     */
    public function getCaptcha(){
        $this->getStore();
        $this->driver=$this->getDriver();
        $code=$this->driver->createCode();

        $re = $this->set($code);;
        return $this->driver->getCaptcha($code, $this->identifying);

    }

    /**
     * 获取数据储存对象
     * @return \core\verifyCode\store
     */
    protected final function getStore(): store
    {
        if (empty($this->store)) {
            $class_name = '\app\logic\store\\' . $this->store_name;

            $this->store = new $class_name();
        } else {

        }

        return $this->store;

    }

    /**
     * 获取驱动
     */
    private function getDriver(): driver
    {
        if (empty($this->driver)) {
            $name = 'app\logic\driver\\' . $this->driver_name;

            $this->driver = new $name($this->driver_config);

        } else {

        }
        return $this->driver;


    }

    /**
     * 设置验证码
     * @param $identifying
     * @param $prefix
     * @param $value
     */
    protected final function set($value)
    {
        $identifying = $this->identifying;
        $this->store = $this->getStore();
        $dara = [
            'time' => time(),
            'code' => $value
        ];
        $re = $this->store->setValue( $identifying, $dara);
        if ($re === false) {
            throw new \Phalcon\Exception('error  127');
        }
        return true;
    }

    /**
     * 验证是否正确
     * @param $value
     * @return bool
     */
    public final function true_check( $value):bool
    {
        $re =$this->check($value);
        if ($re) {
            $this->set(null);
            return true;
        } else {
            return false;
        }
    }


    /**
     * 验证是否正确
     * @param $value
     * @return bool
     */
    public final function check( $value):bool
    {
        $this->getStore();
        $code = $this->get();

        $this->getDriver();
        if ($this->driver->check($code ,$value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 进行虚拟的验证码验证
     * 验证不管是否正确均不会重置验证码
     * @param $value
     * @return bool
     */
    public function check_virtual($value):bool
    {

        if ($this->check($value)) {
            return true;
        } else {
            return false;
        }
    }

    private function get()
    {
        $code = $this->store->getValue( $this->identifying);


        if (isset($code['time'])) {
            $old_time = $code['time'];
        }else{
            return null;
        }

        if ((time() - $old_time) > $this->overtime) {
            return null;
        }

        return $code['code'];



    }

    /**
     * 设置配置信息
     * @param type $config
     */
    public function setConfig($name, $value)
    {
        $this->$name = $value;
    }



}
