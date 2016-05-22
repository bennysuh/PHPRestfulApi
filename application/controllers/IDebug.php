<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/2/20
 * Time: 上午9:57
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class IDebug extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('idebug_model');

    }


    public function getRules()
    {
        return array(
            '*' => array(
                'code' => array('name' => 'code', 'type' => 'string', 'require' => false, 'min' => 4, 'max' => 4, 'desc' => '4位的验证码'),
            ),
            'index' => array(
                'username' => array('name' => 'username', 'type' => 'String', 'require' => false, 'desc' => '用户名'),
                'password' => array('name' => 'password', 'type' => 'Boolean', 'require' => false, 'desc' => '用户密码'),
                'sex' => array('name' => 'sex', 'type' => 'enum', 'range' => array('0', '1'))
            ),
        );
    }


    /**
     * 用户登录
     * @desc 用于用户登录
     * @return object code  操作码，0表示成功，1表示用户不存在
     */
    public function index()
    {
        $userData = array();
       // $response = $this->idebug_model->ShoppingCartItem();
        //$userData['data'] = $response->getData();

        $userData['data'] = "ok";


        $this->load->view('view', $userData);
    }


}