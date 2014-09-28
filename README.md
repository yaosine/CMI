#CMI: Common Module Interface 公用模块接口


##调用方法

$cmi->mod('demo');

$cmi->demo->model_demo();



##目录结构

* classes `公用类`
* configs `公用配置`
* modules `公用模块`
    *  demo `演示模块`
        * config.php 模块配置文件
        * interface.php 模块接口定义 
        * model.php 模块数据模型
        * other.model.php 模块其它模型
    *  demo2 `演示模块2`
        * config.php 模块配置文件
        * interface.php 模块接口定义 
        * model.php 模块数据模型
* tools `工具类`
* cmi.php 入口文件
* debug.php 调试文件


##代码示例
	
	demo模块demo接口定义：
	
	CMI/modules/demo/interface.php

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