# PHPRestfulApi
基于CodeIgniter框架，用于Restful Api开发的PHP框架
1.引入参数验证系统
2.修改了CodeIgniter的view系统
3.传参格式json字符串


参数验证：
参数验证调用代码： /system/core/CodeIgniter.php  line：460左右
参数验证代码位于：/system/request目录


/*
 * 参数有效性验证
 *
 * */
$userparams = array();
if (!empty($params)) {
    try {
        $userparams = json_decode(urldecode($params[0]), true);
    } catch (Exception $ex) {
        show_error('param error.', PARAMS_ERROR);
        $userparams = array();
    }
}
if ($userparams === null) {
    show_error('param format error.', PARAMS_ERROR);
}

$par = $CI->checkValid($method, $userparams);

