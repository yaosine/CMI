#CMI: Common Module Interface 公用模块接口


##调用方法

$cmi->mod('demo');
$cmi->demo->model_demo();
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