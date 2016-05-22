<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午7:16
 */
class CI_Request_Formatter_Callable extends CI_Request_Formatter_Base implements CI_Request_Formatter
{


    /**
     * 对回调类型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule array('callback' => '回调函数', 'params' => '第三个参数')
     * @return boolean/string 格式化后的变量
     *
     */
    public function parse($value, $rule)
    {
        if (!isset($rule['callback']) || !is_callable($rule['callback'])) {
            show_error('invalid callback for rule: ' . $rule['name'], PARAMS_ERROR);
        }

        if (isset($rule['params'])) {
            return call_user_func($rule['callback'], $value, $rule, $rule['params']);
        } else {
            return call_user_func($rule['callback'], $value, $rule);
        }
    }
}