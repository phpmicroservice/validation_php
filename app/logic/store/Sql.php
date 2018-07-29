<?php


namespace core\verify\store;

use core\Sundry\Trace;

/**
 *
 * 数据库储存
 * Class Sql
 * @package core\verifyCode\store
 */
class Sql extends \core\CoreModel implements \core\verify\store
{
    public $code = '';

    public function initialize()
    {
        $this->setSource("verify_captcha");
    }

    /**
     * 设置值
     * @param $type
     * @param $identifying
     * @param $prefix
     * @param $value
     */
    public function setValue($identifying, $value)
    {


        $datamodel = $this->findFirstByidentification($identifying);
        if ($datamodel) {
            # 存在旧数据进行更新
            $datamodel->code = serialize($value);
            if ($datamodel->update() === false) {
                pre($this->getMessage());
                return false;
            }

        } else {
            #新的进行增加
            $data = [
                'identification' => $identifying,
                'code' => serialize($value)
            ];
            $this->setData($data);
            if ($this->save() === false) {
                \output($this->getMessage(), 'info');
                return false;
            }
        }


        return true;
    }

    /**
     * 获取值
     * @param $type
     * @param $identifying
     * @param $prefix
     */
    public function getValue($identifying)
    {
        $name = $identifying;
        $model = self::findFirstByidentification($name);
        if (!$model) {
            return [];
        }
        $data = $model->toArray();
        if (empty($data)) {
            return [];
        }

        return unserialize($model->code);

    }

}