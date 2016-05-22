<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午3:17
 */
class CI_Request_Var
{


    /** ------------------ 对外开放操作 ------------------ **/

    /**
     * 统一格式化操作
     * 扩展参数请参见各种类型格式化操作的参数说明
     *
     * @param string $varName 变量名
     * @param array $rule 格式规则：
     * array(
     *  'name' => '变量名',
     *  'type' => '类型',
     *  'default' => '默认值',
     *  'format' => '格式化字符串'
     *  ...
     *  )
     * @param array $params 参数列表
     * @return miexd 格式后的变量
     */
    public static function format($varName, $rule, $params)
    {
        $value = isset($rule['default']) ? $rule['default'] : NULL;
        $type = !empty($rule['type']) ? strtolower($rule['type']) : 'string';

        $key = isset($rule['name']) ? $rule['name'] : $varName;
        $value = isset($params[$key]) ? $params[$key] : $value;

        if ($value === NULL && $type != 'file') { //排除文件类型
            return $value;
        }

        return self::formatAllType($type, $value, $rule);
    }

    /**
     * 统一分发处理
     * @param string $type 类型
     * @param string $value 值
     * @param array $rule 规则配置
     * @return mixed
     */
    protected static function formatAllType($type, $value, $rule)
    {
        require_once BASEPATH . 'request/Request_Formatter_Base.php';//手动加载class CI_Request_Formatter_Base
        require_once BASEPATH . 'request/Request_Formatter.php';//手动加载interface CI_Request_Formatter//手动加载interface CI_Request_Formatter


        $diDefautl = 'Request_Formatter_' . ucfirst($type);
        $formatter =& load_class($diDefautl, 'request');

        if (!($formatter instanceof CI_Request_Formatter)) {
            show_error('system error:' . SYSTEM_PARAMS_RULE_RROR, SYSTEM_ERROR);
        }

        return $formatter->parse($value, $rule);
    }


}