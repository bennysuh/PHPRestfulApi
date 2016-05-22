<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/16
 * Time: 上午10:36
 */
class CI_Helper_ApiDesc
{
    public function render()
    {
        $service = $_REQUEST['service'];


        $rules = array();
        $returns = array();
        $description = '';
        $descComment = '//请使用@desc 注释';

        $typeMaps = array(
            'string' => '字符串',
            'int' => '整型',
            'float' => '浮点型',
            'boolean' => '布尔型',
            'date' => '日期',
            'array' => '数组',
            'fixed' => '固定值',
            'enum' => '枚举类型',
            'object' => '对象',
        );


        list($className, $methodName) = explode('.', $service);


        $api = new $className();
        $rules = $api->getApiRules($methodName, false);


        $rMethod = new ReflectionMethod($className, $methodName);
        $docComment = $rMethod->getDocComment();
        $docCommentArr = explode("\n", $docComment);

        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);

            //标题描述
            if (empty($description) && strpos($comment, '@') === false && strpos($comment, '/') === false) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }

            //@return注释
            $pos = stripos($comment, '@return');
            if ($pos === false) {
                continue;
            }

            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';    //可选的字段说明
            }
            $returns[] = $returnCommentArr;
        }


        include './api_desc_tpl.php';


    }


}