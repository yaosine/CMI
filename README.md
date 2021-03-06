#CMI:公用模块接口
>CMI：Common Module Interface

CMI是一个超轻量级的PHP框架，适合多功能、高并发Web系统开发


##功能特性

* 轻量
	* 超轻量级框架
	* 速度与性能并存
* 模块化
	* 高效率的模块化开发
		* 子系统分离开发
		* 模块间轻松引用
		* 业务逻辑与数据处理分离
	* 降低模块间的耦合度
		* 减少模块间的影响，防止对某一模块修改所引起的水波效应
* 远程调用
	* 降低故障风险
		* 单个业务宕机不影响其它业务
	* 接口合并请求
		* 提升速度
		* 减少带宽



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
* cmi.php 程序入口
* index.php 远程调用入口
* debug.php 调试文件


##调用方法

    //本地调用
	case 'local':

		//调用本地单个接口
		$cmi->mod('demo');
		$rs = $cmi->demo->demo2();
		$cmi->debug($rs);

		//调用本地多个接口
		$cmi->mod('demo', 'demo2');
		$cmi->demo->demo1();
		$rs = $cmi->demo2->demo();
		$cmi->debug($rs);
		break;

	//远程调用
	case 'http':
		
		//HTTP方式调用单个接口
		$data = array(
			//模块名，接口名，参数1，参数2，参数3
			array('demo', 'demo2', 'args1', 'args2', 'args3')
		);
		$rs = $cmi->call_http($data);//远程调用接口
		var_dump($rs);

		//HTTP方式调用多个接口
		$data = array(
			//模块名，接口名，参数1，参数2，参数3
			array('demo', 'demo2', 'args1', 'args2', 'args3'),
			array('demo2', 'demo2', 'args'),
		);
		$rs = $cmi->call_http($data);//合并HTTP请求
		var_dump($rs);
		
		break;







##代码示例

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


