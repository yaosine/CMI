<?php

/**
 * Common Module Interface
 * 
 * Memcache配置
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

return array (
    
    //单台
    'MEM_ONE' => array(
        'host' => '192.168.1.188',
        'port' => '11211',
        'weight' => 1, //权重，在多个服务器中占的比重
        'pconnect' => 0, //是否长连接
    ),

    //组
    'MEM_POOL' => array(
        array(
            'host' => '192.168.1.188',
            'port' => '11211',
            'weight' => 1, //权重，在多个服务器中占的比重
            'pconnect' => 0, //是否长连接
        ),
        array(
            'host' => '192.168.1.188',
            'port' => '11211',
            'weight' => 1, //权重，在多个服务器中占的比重
            'pconnect' => 0, //是否长连接
        ),
    ),

);