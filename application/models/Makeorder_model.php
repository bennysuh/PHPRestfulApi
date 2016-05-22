<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/22
 * Time: 下午3:50
 *
 *下单流程相关
 */
class Makeorder_model extends CI_Model
{

    public function GetDefaultOrderInfo()
    {

        $method = "Orders/Cart";
        $param = array('CustomerId' => 10, 'Checked' => true);

        return $this->shapiclient->Select($method, $param);
    }

}

