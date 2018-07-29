<?php

namespace app\logic;

use app\Base;


/**
 * 逻辑
 * Class logic
 * @package logic\verify
 */
class logic extends Base
{
    /**
     * 验证appid和业务id的正确性
     * @param $appid 应用id
     * @param $operation 业务id
     * @return bool|string 正确返回true,失败返回string(失败原因)
     */
    public function app_operation($appid, $operation)
    {
        //验证app
        $appModel = \app\model\verify_apps::findFirstByid($appid);
        if ($appModel instanceof \app\model\verify_apps) {

        } else {
            return '_app_id-error-' . $appid;
        }
        //验证业务逻辑
        $operationModel = model\verify_operation::findFirst([
            'app_id =:appid: and id = :operation: ',
            'bind' => [
                'appid' => $appid,
                'operation' => $operation
            ]
        ]);
        if ($operationModel instanceof model\verify_operation) {

        } else {
            return '_operation-error';
        }

        return true;
    }

    /**
     * 获取验证码的类型和本次验证的唯一标示
     * @param $HTTP_USER_AGENT 浏览器
     * @param $REMOTE_ADDR IP地址
     * @param $app_id 应用id
     * @param $operation_id 业务id
     * @return array
     */
    public function verify_type($HTTP_USER_AGENT, $REMOTE_ADDR,
                                $app_id, $operation_id
    )
    {

        $array = [
            'dragging', 'img_base64', 'ajax', 'img_click', 'img_cn',
        ];
        $biaoshi = uniqid();
        $mt = mt_rand(1, 1);

        //写入日志
        $verify_logmodel = new \app\model\verify_log();
        $verify_logmodel->setData([
            'status' => 0,
            'app_id' => $app_id,
            'operation' => $operation_id,
            'time' => time(),
            'browser' => $HTTP_USER_AGENT,
            'ip' => $REMOTE_ADDR,
            'identifying' => $biaoshi,
            'type' => $array[$mt]
        ]);
        if ($verify_logmodel->save() === false) {
            return '_sql-error' . $verify_logmodel->getMessage();
        }
        $this->session->set($biaoshi, 0);
        return [
            'type' => $array[$mt],
            'identifying' => $biaoshi
        ];
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
    public function true_check($identifying, $appid, $operation, $time, $signature)
    {

        # 读取验证信息
        $verify_logmodel = \app\model\verify_log::findFirstByidentifying($identifying);
        if (!($verify_logmodel instanceof \app\model\verify_log)) {
            return '_model-error-120';
        }
        # 进行签名验证
        $re121 = $this->check_signature($verify_logmodel->app_id, $time, $signature);
        if (is_string($re121)) {
            return $re121;
        }


        # 进行二次验证
        $verify_logmodel = \app\model\verify_log::findFirstByidentifying($identifying);
        if ($verify_logmodel->status === '1') {
            $verify_logmodel->status = 2;

            if (($verify_logmodel->time + 600) < RUN_TIME) {
                return '_over-tome';
            }

            if ($verify_logmodel->save() === false) {
                return '_sql-error';
            }
            $this->session->set($identifying, 2);
            return true;
        } else {
            return '_status-error';
        }
    }

    /**
     * 进行签名验证
     * @param $app_id
     * @param $time
     * @param $signature
     *
     */
    private function check_signature($app_id, $time, $signature)
    {

        $appModel = \app\model\verify_apps::findFirstById($app_id);
        if (!($appModel instanceof \app\model\verify_apps)) {
            return '_data-error';
        }
        $signature_new = sha1($time . $appModel->cipher);

        if (strcasecmp($signature_new, $signature) === 0) {
            return true;
        }
        return '_signature-error';
    }

}