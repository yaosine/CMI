<?php

/**
 * Common Module Interface
 * 
 * 模块接口定义
 *
 * 用于定义本模块对外开放的接口
 *
 * @copyright (c) 2009-2014 Innet Inc
 * @author E:\pan\pan\code\cmi
yaosine@gmail.com
 *
 */

class cmi_demo extends cmi{

    function demo(){

        $this->demo1();

        $this->mod('demo2')->demo2();
        $this->mod('demo2');
        $this->demo2->demo2();

        $this->config_demo();

        echo "<hr>";
        $this->model_demo();

        echo "<hr>";
        $this->other_model_demo();

        echo "demo";
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