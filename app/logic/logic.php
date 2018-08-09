<?php

namespace app\logic;

use app\Base;
use app\model\verify_log;
use app\model\verify_operation;


/**
 * 逻辑
 * Class logic
 * @package logic\verify
 */
class logic extends Base
{
    /**
     * 服务名字和业务名字的正确性
     * @param $sn 服务名字
     * @param $operation
     * @return bool|string 正确返回true,失败返回string(失败原因)
     */
    public function app_operation($sn, $operation)
    {
        //验证业务逻辑
        $operationModel = verify_operation::findFirst([
            'sn =:sn: and operation = :operation: ',
            'bind' => [
                'sn' => $sn,
                'operation' => $operation
            ]
        ]);
        if ($operationModel instanceof verify_operation) {

        } else {
            return 'operation-error';
        }

        return true;
    }

    /**
     * 获取验证码的类型和本次验证的唯一标示
     * @param $REMOTE_ADDR IP地址
     * @param $app_id 应用id
     * @param $operation_id 业务id
     * @return array
     */
    public function verify_type($browser, $ip, $sn, $operation)
    {
        $array_type = [
            'dragging', 'img_base64', 'ajax', 'img_click', 'img_cn',
        ];
        $biaoshi = uniqid();
        $verify_operation = verify_operation::findFirst([
            'sn =:sn: and operation =:operation:',
            'bind' => [
                'sn' => $sn,
                'operation' => $operation
            ]]);
        if(!($verify_operation instanceof verify_operation)){
            return 'error-sn-operation';
        }
        if(empty($verify_operation->type)){
            $mt = mt_rand(0, 4);
            $type = $array_type[$mt];
        }else{
            $type=$verify_operation->type;
        }
        #

        //写入日志
        $verify_logmodel = new \app\model\verify_log();
        $verify_logmodel->setData([
            'status' => 0,
            'sn' => $sn,
            'operation' => $operation,
            'time' => time(),
            'ip' => $ip,
            'browser' => $browser,
            'identifying' => $biaoshi,
            'type' => $type
        ]);
        if ($verify_logmodel->save() === false) {
            return '_sql-error' . $verify_logmodel->getMessage();
        }
        $this->gCache->save($biaoshi, 0,600);

        return [
            'type' => $type,
            'identifying' => $biaoshi
        ];
    }

    /**
     * 获取当前验证的验证码类型
     * @param $identifying
     * @return \Phalcon\Mvc\Model\Resultset|\Phalcon\Mvc\Phalcon\Mvc\Model|string
     */
    public function get_type($identifying)
    {
        $verify_logmodel = \app\model\verify_log::findFirstByidentifying($identifying);
        if($verify_logmodel instanceof verify_log){
            return $verify_logmodel->type;
        }
        return '';
    }

    /**
     * 对验证进行虚拟通过
     * @param $identifying 验证标示
     */
    public function check_virtual($type, $identifying)
    {
        $verify_logmodel = \app\model\verify_log::findFirstByidentifying($identifying);
        if (!($verify_logmodel instanceof \app\model\verify_log)) {
            return '_empty-error';
        }
        if ($verify_logmodel->status <= 1 && $type == $verify_logmodel->type) {
            $verify_logmodel->status = 1;
            if ($verify_logmodel->save() === false) {
                return '_sq-error';
            }
            return true;
        } else {
            return '_status-error';
        }
    }


    /**
     *  进行真实的验证
     * @param $identifying 验证标示
     * @param $appid 应用id
     * @param $operation 业务的id
     * @param $time 时间
     * @param $signature 验证签名
     * @return bool|string  验证结果
     */
    public function true_check($sn,$operation,$identifying)
    {

        # 读取验证信息
        $verify_logmodel = \app\model\verify_log::findFirstByidentifying($identifying);
        if (!($verify_logmodel instanceof \app\model\verify_log)) {
            return '_model-error-120';
        }
        if($verify_logmodel->sn != $sn || $verify_logmodel->operation != $operation){
            return  'operation-error';
        }
        $verify_logmodel->status = 2;
        if (($verify_logmodel->time + 600) < RUN_TIME) {
            return '_over-tome';
        }

        if ($verify_logmodel->save() === false) {
            return '_sql-error';
        }
        $this->gCache->save($identifying, 2,600);
        return true;

    }

}