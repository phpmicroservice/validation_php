<?php

namespace apps\verify\controllers;

/**
 * 核心控制器,用户状态机
 * Class CoreController
 *
 * @package core
 * @property \Phalcon\Loader $autoLoader
 * @property \logic\verify\logic $lService
 */
class CoreController extends \Phalcon\Mvc\Controller
{

    protected $appid; //应用id
    protected $operation; //业务id
    protected $identifying_app; //应用的唯一标示
    protected $identifying; //本次验证的唯一标示
    protected $lService;//服务层的注入


    use \core\JumpTrait;

    # 是否继续执行方法

    public function beforeExecuteRoute()
    {
        //获取基本数据
        $this->appid = $this->request->get('appid','string');
        $this->operation = $this->request->get('operation','string');
        $this->identifying=$this->request->get('identifying','string');
        if($this->identifying){
            //存在验证id ,确认验证id的可用性


        }
        //验证
        $this->lService = new \logic\verify\logic;

        $this->identifying_app=md5($this->appid . $this->operation);

        $old_time=$this->session->get($this->identifying_app);
        if ($old_time && ($old_time+6000)>time()) {
                //存在缓存的验证,不需要继续验证


        }else{
            $re = $this->lService->app_operation($this->appid, $this->operation);
            if (is_string($re)) {
                $this->restful_error($re);
                return false;
            } else {
                //写入 session 缓存
                $this->session->set($this->identifying_app, time());
            }
        }
    }

    /**
     * 初始化方法
     */
    public function initialize()
    {


    }

    /**
     * 渲染视图
     */
    public function display()
    {
        $this->view->render($this->router->getControllerName(), $this->router->getActionName());
    }

    /**
     * 给视图层传值
     * @param type $key 变量名
     * @param type $value 变量的值
     * @property \Phalcon\Config $config
     * @return type
     */
    protected final function assign($key, $value)
    {
        if (is_array($key)) {
            return $this->view->setVar($key[0], $key[1]);
        }
        return $this->view->setVar($key, $value);
    }

    /**
     * 获取Params传送的值
     * @param null $name
     */
    public function getParam($name = null, $defind = null)
    {

        $data = $this->router->getParams();
        if (is_null($name)) {
            return $data;
        }
        return isset($data[$name]) ? $data[$name] : $defind;
    }

    /**
     * 获取数据
     * @param type $parameter
     * @return boolean
     */
    protected final function getData($parameter)
    {
        $data = [];
        $type = '';
        foreach ($parameter as $k => $v) {
            $type = $v[0];

            if (isset($v[4])) {

            } else {
                # 不存在必选验证
                $v[4] = false;
            }
            if ($type === 'post') {
                if ($v[4]) {
                    if (!$this->request->hasPost($v[1])) {
                        return $this->translate->t('request-post-isset-field', [
                            'field' => $this->translate->t($v[1])
                        ]);
                    }
                }
                $data[$k] = $this->request->getPost($v[1], $v[2], $v[3]);
            } elseif ($type === 'get') {
                if ($v[4]) {
                    if (!$this->request->has($v[1])) {
                        return $this->translate->t('request-get-isset-field', [
                            'field' => $this->translate->t($v[1])
                        ]);
                    }
                }

                $data[$k] = $this->request->get($v[1], $v[2], $v[3]);
            } else {
                return $v[1] . 'is isset';
            }
        }
        if ($type === 'post') {
            unset($_POST);
        } elseif ($type === 'get') {
            unset($_GET);
        }

        return $data;
    }

}
