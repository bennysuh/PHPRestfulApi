<?php
/**
 * Created by PhpStorm.
 * User: yjy
 * Date: 16/3/16
 * Time: 上午9:55
 */


define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}


$system_path = 'system';

$application_folder = 'application';

$view_folder = '';

if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE) {
    $system_path = $_temp . '/';
} else {
    // Ensure there's a trailing slash
    $system_path = rtrim($system_path, '/') . '/';
}

// Is the system path correct?
if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the system folder
define('BASEPATH', str_replace('\\', '/', $system_path));

// Path to the front controller (this file)
define('FCPATH', dirname(__FILE__) . '/');

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// The path to the "application" folder
if (is_dir($application_folder)) {
    if (($_temp = realpath($application_folder)) !== FALSE) {
        $application_folder = $_temp;
    }

    define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
} else {
    if (!is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
        exit(3); // EXIT_CONFIG
    }

    define('APPPATH', BASEPATH . $application_folder . DIRECTORY_SEPARATOR);
}

// The path to the "views" folder
if (!is_dir($view_folder)) {
    if (!empty($view_folder) && is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
        $view_folder = APPPATH . $view_folder;
    } elseif (!is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: ' . SELF;
        exit(3); // EXIT_CONFIG
    } else {
        $view_folder = APPPATH . 'views';
    }
}

if (($_temp = realpath($view_folder)) !== FALSE) {
    $view_folder = $_temp . DIRECTORY_SEPARATOR;
} else {
    $view_folder = rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

define("D_S", DIRECTORY_SEPARATOR);
$root = dirname(__FILE__);

require_once BASEPATH . 'core/Controller.php';
$allPhalApiApiMethods = get_class_methods('CI_Controller');

$files = listDir(APPPATH . 'controllers');

$allApiS = array();

foreach ($files as $value) {

    require_once $value;

    $subValue = substr($value, strpos($value, D_S . 'controllers' . D_S) + 1);
    //进行处理对于类似与Api/Auth/Api/Api.php 多层嵌套只取 Api/Api.php进行处理
    $arr = explode(D_S, $subValue);
    $subValue = implode(D_S, array_slice($arr, -2, 2));
    $apiServer = str_replace(array('controllers/', '.php'), array('', ''), $subValue);

    if (!class_exists($apiServer)) {
        continue;
    }

    $method = array_diff(get_class_methods($apiServer), $allPhalApiApiMethods);

    foreach ($method as $mValue) {
        $rMethod = new Reflectionmethod($apiServer, $mValue);
        if (!$rMethod->isPublic()) {
            continue;
        }

        $title = '//请检测函数注释';
        $desc = '//请使用@desc 注释';
        $docComment = $rMethod->getDocComment();
        if ($docComment !== false) {
            $docCommentArr = explode("\n", $docComment);
            $comment = trim($docCommentArr[1]);
            $title = trim(substr($comment, strpos($comment, '*') + 1));

            foreach ($docCommentArr as $comment) {
                $pos = stripos($comment, '@desc');
                if ($pos !== false) {
                    $desc = substr($comment, $pos + 5);
                }
            }
        }

        $service = $apiServer . '.' . ucfirst($mValue);
        $allApiS[$service] = array(
            'service' => $service,
            'title' => $title,
            'desc' => $desc,
        );
    }
}


//字典排列
ksort($allApiS);

function listDir($dir)
{
    $dir .= substr($dir, -1) == D_S ? '' : D_S;
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>接口列表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
<br/>
<div class="container">
    <div class="jumbotron">
        <div class="page-header">
            <h1>接口列表</h1>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>接口服务</th>
                <th>接口名称</th>
                <th>更多说明</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $uri = str_ireplace('listAllApis.php', 'checkApiParams.php', $_SERVER['REQUEST_URI']);

            foreach ($allApiS as $key => $item) {
                $link = $uri . '?service=' . $item['service'];
                $NO = $num++;
                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$item['service']}</a></td><td>{$item['title']}</td><td>{$item['desc']}</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>










