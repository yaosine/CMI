<?php

/**
 * Common Module Interface
 * 
 * 模块接口定义
 *
 * 用于定义本模块对外开放的接口
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

class cmi_demo2 extends cmi_module{

    function demo(){
        echo "demo2";
    }

    //demo2
    function demo2(){
        return 'demo2 return';
    }
}

?>