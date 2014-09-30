<?php

/**
 * Common Module Interface
 * 
 * 公用配置
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

header("Content-Type: text/html;charset=utf-8");

defined('CMI_HTTP_URL') or define('CMI_HTTP_URL', 'http://127.0.0.1/CMI/');//HTTP请求地址

//系统路径
defined('DS') or define('DS', DIRECTORY_SEPARATOR);//系统分隔符
defined('CMI_PATH') or define('CMI_PATH', dirname(dirname(__FILE__)).DS);//CMI根目录
defined('CMI_MODULE_PATH') or define('CMI_MODULE_PATH', CMI_PATH.'modules'.DS);//模块目录
defined('CMI_CONFIG_PATH') or define('CMI_CONFIG_PATH', CMI_PATH.'configs'.DS);//公用配置目录
defined('CMI_CLASS_PATH') or define('CMI_CLASS_PATH', CMI_PATH.'classes'.DS);//公用类目录
defined('CMI_ERROR_PATH') or define('CMI_ERROR_PATH', CMI_PATH.'errors'.DS);//错误日志目录

//系统常量
defined('SESSION_LEFT_TIME') or define('SESSION_LEFT_TIME', 1800);//SESSION过期时间
defined('CMI_NOW_TIME') or define('CMI_NOW_TIME', time());//当前时间戳

//系统配置
return array(
	
    //默认加载模块
    //'default_modules' => array('db', 'http'),

)

?>