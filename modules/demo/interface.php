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

class cmi_demo extends cmi_module{

    function demo(){
        $demo = isset($_GET['demo']) ? $_GET['demo'] : 0;
        switch ($demo) {

            case '1':
                //调用本模块接口
                $this->demo1();
                break;

            case '2':
                //调用其它模块接口（单个）
                $this->mod('demo2')->demo();
                break;

            case '3':
                //调用其它模块接口（多个）
                $this->mod('demo1', 'demo2');
                //$this->demo1->demo2();
                $this->demo2->demo2();
                break;

            case '4':
                //调用模块默认模型接口
                $this->model_demo();
                break;

            case '5':
                //调用模块其它模型接口
                $this->other_model_demo();
                break;

            case '6':
                //数据库操作:action=query,insert,update,delete
                $this->db_demo();
                break;

            case '7':
                //获取模块配置
                $this->config_demo();
                break;
            
            default:
                $this->demo1();
                break;
        }

        echo "<br><br><br>Requset OK!<br><br>
        <i>Demo：</i><br>
        <i>1.调用本模块接口</i><br>
        <i>2.调用其它模块接口（单个）</i><br>
        <i>3.调用其它模块接口（多个）</i><br>
        <i>4.调用模块默认模型接口</i><br>
        <i>5.调用模块其它模型接口</i><br>
        <i>6.数据库操作:action=query,insert,update,delete</i><br>
        <i>7.获取模块配置</i><br>
        <br>";
        
    }

    //demo
    function demo1(){
        echo "demo1";
    }

    //demo2
    function demo2(){
        return 'demo return';
    }

    //数据库操作:action=query,insert,update,delete
    function db_demo(){
        $action = isset($_GET['action']) ? $_GET['action'] : 0;
        if($action == 'query') return $this->model->db_query_demo();
        if($action == 'insert') return $this->demo->model->db_insert_demo();
        if($action == 'update') return $this->demo->model->db_update_demo();
        if($action == 'delete') return $this->demo->model->db_delete_demo();
    }

    //获取模块配置
    function config_demo(){
        echo "config_demo<br>";
        var_dump($this->config());
    }

    //调用模块默认模型接口
    function model_demo(){
        echo "model_demo start<br>";
        $this->model->demo();
        echo "<br>";
    }

    //调用模块其它模型接口
    function other_model_demo(){
        echo "other_model_demo start<br>";
        $this->model('other');
        $this->other->demo();
        echo "<br>";
    }
}

?>