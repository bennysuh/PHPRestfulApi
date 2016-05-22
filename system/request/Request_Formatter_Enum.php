<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午7:23
 */
class CI_Request_Formatter_Enum extends CI_Request_Formatter_Base implements CI_Request_Formatter
{
    /**
     * 检测枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @return 当不符合时返回$rule
     */
    public function parse($value, $rule)
    {
        $this->formatEnumRule($rule);

        $this->formatEnumValue($value, $rule);

        return $value;
    }

    /**
     * 检测枚举规则的合法性
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws PhalApi_Exception_InternalServerError
     */
    protected function formatEnumRule($rule)
    {
        if (!isset($rule['range'])) {
            show_error('miss ' . $rule['name'] . '\'s enum range', PARAMS_ERROR);
        }

        if (empty($rule['range']) || !is_array($rule['range'])) {
            show_error($rule['name'] . '\'s enum range can not be empty', PARAMS_ERROR);
        }
    }
}