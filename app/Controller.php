<?php

namespace app;


/**
 * 主控制器
 * Class Controller
 * @property \Phalcon\Cache\BackendInterface $gCache
 * @property \Phalcon\Config $dConfig
 * @property \pms\Validation\Message\Group $message
 * @property \pms\bear\Client $clientSync
 * @property \pms\bear\ClientSync $proxyCS
 * @property \app\logic\logic $lService
 * @package app\controller
 */
class Controller extends \pms\Controller
{
    public $user_id;
    protected $session_id;
    protected $sn; //模块名字
    protected $operation; //业务名字
    protected $identifying_app; //应用的唯一标示
    protected $identifying_type; //本次验证的验证码类型
    protected $identifying; //本次验证的唯一标示
    protected $lService;//服务层的注入

    /**
     * 初始化
     * @param $connect
     */
    public function initialize()
    {
        $this->user_id = $this->session->user_id;
        parent::initialize();
    }


    # 是否继续执行方法

    public function beforeExecuteRoute(\pms\Dispatcher $dispatcher)
    {
        //获取基本数据
        $this->sn = $this->getData('sn');
        $this->operation = $this->getData('operation');
        $this->identifying = $this->getData('identifying');
        $this->identifying_app = md5($this->sn . $this->operation);
        if ($this->identifying) {
            //存在验证id ,确认验证id的可用性
            if (!empty($dispatcher->session)) {
                #用户的
                var_dump([
                    $dispatcher->session->get($this->identifying_app),
                    $this->identifying
                ]);
                if ($this->session->get($this->identifying_app) != $this->identifying) {
                    $this->send('embezzle');
                    return false;
                }
            }
        }
        $this->lService = new \app\logic\logic();
        $this->identifying_type=$this>$this->lService->get_type($this->identifying);
        //验证业务是够可用


        $old_time = $this->gCache->get($this->identifying_app);
        if ($old_time && ($old_time + 6000) > time()) {
            //存在缓存的验证,不需要继续验证
        } else {

            $re = $this->lService->app_operation($this->sn, $this->operation);
            if (is_string($re)) {
                $this->send($re);
                return false;
            } else {
                //写入 全局缓存
                $this->gCache->save($this->identifying_app, time(), 600);
            }
        }
    }


    /**
     * 获取数据
     * @param $pa
     */
    public function getData($name = '', $defind = null)
    {
        $d = $this->connect->getData();
        if ($name) {
            return $d[$name] ?? $defind;
        }
        return $d;
    }


    /**
     * 发送消息
     * @param $re
     */
    public function send($re)
    {
        if ($re instanceof \pms\Validation\Message\Group) {
            # 错误消息
            $d = $re->toArray();
            $this->connect->send_error($d['message'], $d['data'], 424);
        } else {
            if(is_string($re)){
                $this->connect->send_error($re, '失败',400);
            }else{
                if (is_object($re)) {
                    $re = json_decode(json_encode($re));
                }
                $this->connect->send_succee($re, '成功');
            }
        }
    }


}