<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午7:35
 */
class CI_Request_Formatter_Int extends CI_Request_Formatter_Base implements CI_Request_Formatter
{
    /**
     * 对整型进行格式化
     *
     * @param mixed $value 变量值
     * @param array $rule array('min' => '最小值', 'max' => '最大值')
     * @return int/string 格式化后的变量
     *
     */
    public function parse($value, $rule)
    {
        return intval($this->filterByRange(intval($value), $rule));
    }
}