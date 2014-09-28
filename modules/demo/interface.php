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

class cmi_demo extends cmi{

    function demo(){

        //调用本模块接口
        $this->demo1();

        //调用其它模块接口（单个）
        $this->mod('demo2')->demo2();

        //调用其它模块接口（多个）
        $this->mod('demo2');
        $this->demo2->demo2();
        
        //调用模块模型接口
        $this->model_demo();

        //调用模块其它模型接口
        $this->other_model_demo();

        //获取模块配置
        $this->config_demo();
        
    }

    function demo1(){
        echo "demo1";
    }

    function config_demo(){
        echo "config_demo";
        var_dump($this->config());
    }

    function model_demo(){
        echo "model_demo start<br>";
        $this->model();
        $this->demo->model->demo();
        echo "<br>";
    }

    function other_model_demo(){
        echo "other_model_demo start<br>";
        $this->model('other');
        $this->demo->other->demo();
        echo "<br>";
    }
}

?>