<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 下午3:49
 *
 * 下单流程相关
 */
class MakeOrder extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('makeorder_model');

    }

    public function getRules()
    {
        return array(
            'GetDefaultOrderInfo' => array(
                'token' => array('name' => 'token', 'type' => 'String', 'min' => 12, 'max' => 16, 'require' => false, 'desc' => '用户token'),
            ),
        );
    }


    /**
     * 获取下单默认信息
     * @desc 客户端下单页面获取下单相关默认信息在
     */
    public function GetDefaultOrderInfo()
    {

        $userData = array();
        $response = $this->makeorder_model->GetDefaultOrderInfo();
        //$response = $this->makeorder_model->GetDefaultOrderInfo($this->token);
        $userData['data'] = $response->getData();

        $this->load->view('view', $userData);

    }


}