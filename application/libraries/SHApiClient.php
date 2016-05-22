<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 上午9:23
 */
class SHApiClient
{

    protected $host;//接口域名
    protected $filter;//过滤器 暂时未使用
    protected $parser;//解析器
    protected $service;//接口服务名称
    protected $timeoutMs;//超市时间 默认30000
    protected $params = array();//参数

    protected $METHOD;//调用方法 select update create

    const VERSION = "1";
    const DEVICE = "9";


    /**
     * 创建一个接口实例
     * @return SHApiClient
     */
    public static function getNewInstance()
    {
        return new self();
    }

    public function __construct()
    {
        $this->host = NET_DOMAIN;
        $this->timeoutMs = 30000;
        $this->parser = new SHApiClientParserJson();
    }


    /**
     * 设置接口域名
     * @param string $host
     * @return SHApiClient
     */
    public function withHost($host)
    {
        $this->host = $host;
        return $this;
    }


    /**
     * 设置结果解析器，仅当不是JSON返回格式时才需要设置
     * @param SHApiClientParser $parser 结果解析器
     * @return SHApiClient
     */
    public function withParser(SHApiClientParser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * 重置，将接口服务名称、接口参数、请求超时进行重置，便于重复请求
     * @return SHApiClient
     */
    public function reset()
    {
        $this->service = NET_DOMAIN;
        $this->timeoutMs = 30000;
        $this->params = array();

        return $this;
    }


    /**
     * 设置将在调用的接口服务名称
     * @param string $service 接口服务名称
     * @return SHApiClient
     */
    public function withService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * 设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
     * @param string $name 参数名字
     * @param string $value 值
     * @return SHApiClient
     */
    public function withParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }


    /**
     * @param array $params 参数数组
     * @return SHApiClient
     */
    public function withParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * 设置超时时间，单位毫秒
     * @param int $timeoutMS 超时时间，单位毫秒
     * @return SHApiClient
     */
    public function withTimeout($timeoutMS)
    {
        $this->timeoutMS = $timeoutMS;
        return $this;
    }


    /**
     * @param $method
     * @parasm array $params
     */
    public function Select($method, $params = array())
    {
        $this->METHOD = 'select';//无实际用处  只是予以重视
        $this->params = $params;
        $this->service = $this->host . '/' . $method . '/' . $this->METHOD;

        return $this->request();
    }

    /**
     * @param $method
     * @param array $params
     */
    public function Update($method, $params = array())
    {
        $this->METHOD = 'update';//无实际用处  只是予以重视
        $this->params = $params;
        $this->service = $this->host . '/' . $method . '/' . $this->METHOD;

        return $this->request();
    }

    /**
     * @param $method
     * @param array $params
     */
    public function Create($method, $params = array())
    {
        $this->METHOD = 'create';//无实际用处  只是予以重视
        $this->params = $params;
        $this->service = $this->host . '/' . $method . '/' . $this->METHOD;

        return $this->request();
    }

    /**
     * 发起接口请求
     * @return SHApiClientResponse
     */
    public function request()
    {
        $url = $this->service;//无实际用处  只是予以重视

        /**
         * 暂时去掉过滤器
         */
//        if ($this->filter !== null) {
//            $this->filter->filter($this->service, $this->params);
//        }

        $rs = $this->doRequest($url, $this->params, $this->timeoutMs);
        return $this->parser->parse($rs);


    }

    protected function doRequest($url, $params = array(), $timeoutMs = 30000)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeoutMs);


        $params['api'] = SHApiClient::VERSION;
        $params['device'] = SHApiClient::DEVICE;

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }


}

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 上午10:32
 *
 * 接口结果解析器
 *
 * - 可用于不同接口返回格式的处理
 */
interface SHApiClientParser
{
    /**
     * 结果解析
     * @param string $apiResult
     * @return PhalApiClientResponse
     */
    public function parse($apiResult);
}


/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 上午10:33
 */
class SHApiClientParserJson
{
    /**
     * @param $apiResult
     * #return SHApiClientResponse
     */
    public function parse($apiResult)
    {
        if ($apiResult === false) {
            show_error('system .net error:' . SYSTEM_NET_REQUEST_ERROR, SYSTEM_NET_ERROR);
        }

        $arr = json_decode($apiResult, true);

        if ($arr === false || empty($arr) || !is_array($arr) || !array_key_exists('errorcode', $arr)
            || !array_key_exists('errormsg', $arr) || !array_key_exists('data', $arr)
        ) {
            show_error('system .net error:' . SYSTEM_NET_DATA_FORMAT_ERROR, SYSTEM_NET_ERROR);
        }

        return new SHApiClientResponse($arr['errorcode'], $arr['data'], $arr['errormsg']);
    }

}


/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 上午10:31
 * 接口返回结果
 */
class SHApiClientResponse
{


    protected $code = 200;
    protected $data = array();
    protected $errormsg = '';

    public function __construct($code, $data = array(), $errormsg = '')
    {
        $this->code = $code;
        $this->data = $data;
        $this->errormsg = $errormsg;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getErrorMsg()
    {
        return $this->errormsg;
    }
}




