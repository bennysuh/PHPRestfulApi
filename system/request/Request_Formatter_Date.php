<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午7:22
 */
class CI_Request_Formatter_Date extends CI_Request_Formatter_Base implements CI_Request_Formatter
{
    /**
     * 对日期进行格式化
     *
     * @param timestamp $value 变量值
     * @param array $rule array('format' => 'timestamp', 'min' => '最小值', 'max' => '最大值')
     * @return timesatmp/string 格式化后的变量
     *
     */
    public function parse($value, $rule)
    {
        $rs = $value;

        $ruleFormat = !empty($rule['format']) ? strtolower($rule['format']) : '';
        if ($ruleFormat == 'timestamp') {
            $rs = strtotime($value);
            if ($rs <= 0) {
                $rs = 0;
            }

            $rs = $this->filterByRange($rs, $rule);
        }

        return $rs;
    }
}