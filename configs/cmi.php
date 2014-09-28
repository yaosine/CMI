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

//系统路径
define('DS', DIRECTORY_SEPARATOR);//系统分隔符
defined('CMI_PATH') or define('CMI_PATH', dirname(dirname(__FILE__)).DS);
defined('CMI_MODULE_PATH') or define('CMI_MODULE_PATH', CMI_PATH.'modules'.DS);
defined('CMI_CONFIG_PATH') or define('CMI_CONFIG_PATH', CMI_PATH.'configs'.DS);
defined('CMI_CLASS_PATH') or define('CMI_CLASS_PATH', CMI_PATH.'classes'.DS);
defined('CMI_ERROR_PATH') or define('CMI_ERROR_PATH', CMI_PATH.'errors'.DS);

//系统常量
define('SESSION_LEFT_TIME', 1800);
define('CMI_NOW_TIME', time());


return array(

    //默认加载模块
    'default_modules' => array('db', 'http'),

)

?>