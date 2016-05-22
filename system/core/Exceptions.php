<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Exceptions Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Exceptions
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions
{

    /**
     * Nesting level of the output buffering mechanism
     *
     * @var    int
     */
    public $ob_level;

    /**
     * List of available error levels
     *
     * @var    array
     */
    public $levels = array(
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parsing Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice'
    );

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct()
    {
        $this->ob_level = ob_get_level();
        // Note: Do not log messages from this constructor.
    }

    // --------------------------------------------------------------------

    /**
     * Exception Logger
     *
     * Logs PHP generated error messages
     *
     * @param    int $severity Log level
     * @param    string $message Error message
     * @param    string $filepath File path
     * @param    int $line Line number
     * @return    void
     */
    public function log_exception($severity, $message, $filepath, $line)
    {
        $severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
        log_message('error', 'Severity: ' . $severity . ' --> ' . $message . ' ' . $filepath . ' ' . $line);
    }

    // --------------------------------------------------------------------

    /**
     * 404 Error Handler
     *
     * @uses    CI_Exceptions::show_error()
     *
     * @param    string $page Page URI
     * @param    bool $log_error Whether to log the error
     * @return    void
     */
    public function show_404($page = '', $log_error = TRUE)
    {
        if (is_cli()) {
            $heading = 'Not Found';
            $message = 'The controller/method pair you requested was not found.';
        } else {
            $heading = '404 controller Not Found';
            $message = 'The method you requested was not found:' . $page;
        }

        // By default we log this, but allow a dev to skip it
        if ($log_error) {
            log_message('error', $heading . ': ' . $page);
        }

        echo $this->show_error($heading, $message, 'error_404', METHOD_NOT_FOUND);
        exit(4); // EXIT_UNKNOWN_FILE
    }

    // --------------------------------------------------------------------

    /**
     * General Error Page
     *
     * Takes an error message as input (either as a string or an array)
     * and displays it using the specified template.
     *
     * @param    string $heading Page heading
     * @param    string|string[] $message Error message
     * @param    string $template Template name
     * @param    int $status_code (default: 500)
     *
     * @return    string    Error page output
     */
    public function show_error($heading, $message, $template = 'error_general', $status_code = SYSTEM_EXCEPTION)
    {
//        $templates_path = config_item('error_views_path');
//        if (empty($templates_path)) {
//            $templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
//        }
//        if (is_cli()) {
//            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
//            $template = 'cli' . DIRECTORY_SEPARATOR . $template;
//        } else {
//            set_status_header(200);
//            //$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
//            $template = 'html' . DIRECTORY_SEPARATOR . $template;
//        }

        set_status_header(200);
        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();
        $_response =& load_class('ApiResponse', 'core');
        $_response->setErrorMsg($message);
        $_response->setCode($status_code);
        $buffer = json_encode($_response->getResult());
        ob_end_clean();
        return $buffer;

    }

    // --------------------------------------------------------------------

    public function show_exception($exception)
    {
        /*        $templates_path = config_item('error_views_path');
                if (empty($templates_path)) {
                    $templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
                }

                $message = $exception->getMessage();
                if (empty($message)) {
                    $message = '(null)';
                }

                if (is_cli()) {
                    $templates_path .= 'cli' . DIRECTORY_SEPARATOR;
                } else {
                    set_status_header(500);
                    $templates_path .= 'html' . DIRECTORY_SEPARATOR;
                }*/


        /*$message = $exception->getMessage();
        if (empty($message)) {
            $message = 'system exception';
        }*/
        $message = 'system exception:' . SYSTEM_EXCEPTION;

        set_status_header(200);

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }

        ob_start();
        $_response =& load_class('ApiResponse', 'core');
        $_response->setErrorMsg($message);
        $_response->setCode(SYSTEM_EXCEPTION);
        $buffer = json_encode($_response->getResult());
        ob_end_clean();
        echo $buffer;
    }

    // --------------------------------------------------------------------

    /**
     * Native PHP error handler
     *
     * @param    int $severity Error level
     * @param    string $message Error message
     * @param    string $filepath File path
     * @param    int $line Line number
     * @return    string    Error page output
     */
    public function show_php_error($severity, $message, $filepath, $line)
    {

        $message = 'system error code:' . SYSTEM_GRAMMAR_ERROR;
        echo $this->show_error(null, $message, null, SYSTEM_ERROR);
        exit(4); // EXIT_UNKNOWN_FILE

    }

}
