<?php

/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/15
 * Time: 下午7:30
 */
class CI_Request_Formatter_File extends CI_Request_Formatter_Base implements CI_Request_Formatter
{

    /**
     * 格式化文件类型
     *
     * @param array $rule array('name' => '', 'type' => 'file', 'default' => array(...), 'min' => '', 'max' => '', 'range' => array(...))
     *
     * @throws PhalApi_Exception_BadRequest
     */
    public function parse($value, $rule)
    {

        $default = isset($rule['default']) ? $rule['default'] : NULL;

        $index = $rule['name'];
        // 未上传
        if (!isset($_FILES[$index])) {
            // 有默认值 || 非必须
            if ($default !== NULL || (isset($rule['require']) && !$rule['require'])) {
                return $default;
            }
        }

        if (!isset($_FILES[$index]) || !isset($_FILES[$index]['error']) || !is_array($_FILES[$index])) {
            show_error('miss upload file:' . $index, PARAMS_ERROR);
        }

        if ($_FILES[$index]['error'] != UPLOAD_ERR_OK) {
            show_error('fail to upload file with error = ' . $_FILES[$index]['error'], PARAMS_ERROR);
        }

        $sizeRule = $rule;
        $sizeRule['name'] = $sizeRule['name'] . '.size';
        $this->filterByRange($_FILES[$index]['size'], $sizeRule);

        if (!empty($rule['range']) && is_array($rule['range'])) {
            $rule['range'] = array_map('strtolower', $rule['range']);
            $this->formatEnumValue(strtolower($_FILES[$index]['type']), $rule);
        }

        //对于文件后缀进行验证
        if (!empty($rule['ext'])) {
            $ext = trim(strrchr($_FILES[$index]['name'], '.'), '.');
            if (is_string($rule['ext'])) {
                $rule['ext'] = explode(',', $rule['ext']);
            }
            if (!$ext) {
                show_error('Not the file type' . json_encode($rule['ext']), PARAMS_ERROR);
            }
            if (is_array($rule['ext'])) {
                $rule['ext'] = array_map('strtolower', $rule['ext']);
                $rule['ext'] = array_map('trim', $rule['ext']);
                if (!in_array(strtolower($ext), $rule['ext'])) {
                    show_error('Not the file type' . json_encode($rule['ext']), PARAMS_ERROR);
                }
            }
        }

        return $_FILES[$index];
    }

}