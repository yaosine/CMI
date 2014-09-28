<?php

/**
 * Common Module Interface
 * 
 * 提示工具
 *
 * @copyright (c) 2009-2014 Innet Inc
 * @author E:\pan\pan\code\cmi
yaosine@gmail.com
 *
 */

class cmi_tips{

    /**
     * 成功提示
     * @param string $text 提示内容
     * @param string $url 跳转链接
     * @return array
     */
    function success($text, $url=''){
        $html = '<h1 style="color:green;">OK</h1><h3>'.$text.'</h3>';
        echo $html;
        exit;
    }

    /**
     * 错误提示
     * @param string $text 提示内容
     * @param string $url 跳转链接
     * @return array
     */
    function error($text, $url=''){
        $html = '<h1 style="color:red;">Error</h1><h3>'.$text.'</h3>';
        echo $html;
        exit;
    }

}

?>