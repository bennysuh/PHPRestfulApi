<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 上午8:31
 */
class CI_ApiResponse
{

    /**
     * @var int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     */
    protected $code = 200;

    /**
     * @var array 待返回给客户端的数据
     */
    protected $data = array();

    /**
     * @var string $msg 错误返回信息
     */
    protected $errormsg = '';


    /**
     * 设置返回状态码
     * @param int $ret 返回状态码，其中：200成功，400非法请求，500服务器错误
     * @return PhalApi_Response
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 设置返回数据
     * @param array /string $data 待返回给客户端的数据，建议使用数组，方便扩展升级
     * @return PhalApi_Response
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 设置错误信息
     * @param string $msg 错误信息
     * @return PhalApi_Response
     */
    public function setErrorMsg($errormsg)
    {
        $this->errormsg = $errormsg;
        return $this;
    }


    public function getResult()
    {
        $rs = array(
            'code' => $this->code,
            'data' => $this->data,
            'errormsg' => $this->errormsg,
        );

        return $rs;
    }


}

