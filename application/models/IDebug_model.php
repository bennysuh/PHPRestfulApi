<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/21
 * Time: 下午3:55
 */
class IDebug_model extends CI_Model
{


    public function ShoppingCartItem()
    {

        $method = "Customers/ShoppingCartItem";
        $param = array("ShoppingCartTypeId" => 1, "ProductVariantIds" => array(31, 32));

        return $this->shapiclient->Select($method, $param);
    }


}