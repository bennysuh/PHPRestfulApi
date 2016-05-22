<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午3:31
 */
class CI_Request_Formatter_Base
{
    /**
     * 根据范围进行控制
     */
    protected function filterByRange($value, $rule)
    {
        $this->filterRangeMinLessThanOrEqualsMax($rule);

        $this->filterRangeCheckMin($value, $rule);

        $this->filterRangeCheckMax($value, $rule);

        return $value;
    }

    protected function filterRangeMinLessThanOrEqualsMax($rule)
    {
        if (isset($rule['min']) && isset($rule['max']) && $rule['min'] > $rule['max']) {
            show_error('min should <= max, but now ' . $rule['name'] . ' min = ' . $rule['min'] . ' and max = ' . $rule['max'], SYSTEM_PARAMS_RULE_RROR);
        }
    }

    protected function filterRangeCheckMin($value, $rule)
    {
        if (isset($rule['min']) && $value < $rule['min']) {
            show_error($rule['name'] . ' should >= ' . $rule['min'] . ', but now ' . $rule['name'] . ' = ' . $value, PARAMS_ERROR);
        }
    }

    protected function filterRangeCheckMax($value, $rule)
    {
        if (isset($rule['max']) && $value > $rule['max']) {
            show_error($rule['name'] . ' should <= ' . $rule['max'] . ', but now ' . $rule['name'] . ' = ' . $value, PARAMS_ERROR);
        }
    }

    /**
     * 格式化枚举类型
     * @param string $value 变量值
     * @param array $rule array('name' => '', 'type' => 'enum', 'default' => '', 'range' => array(...))
     * @throws PhalApi_Exception_BadRequest
     */
    protected function formatEnumValue($value, $rule)
    {
        if (!in_array($value, $rule['range'])) {
            show_error($rule['name'] . ' should be in ' . implode('/', $rule['range']) . ', but now {name} = ' . $value, PARAMS_ERROR);
        }
    }
}