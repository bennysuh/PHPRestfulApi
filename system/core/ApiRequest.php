<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午2:31
 */
class CI_ApiRequest
{

    public function getByRule($rule, $params)
    {
        $rs = NULL;
        if (!isset($rule['name'])) {
            show_error('system error code: ' . SYSTEM_PARAMS_RULE_ERROR, SYSTEM_ERROR);
        }

        $_requestVar =& load_class('Request_Var', 'request');
        $rs = $_requestVar::format($rule['name'], $rule, $params);

        if ($rs === NULL && (isset($rule['require']) && $rule['require'])) {
            show_error($rule['name'] . ' require, but miss', PARAMS_ERROR);
        }

        return $rs;

    }


}

