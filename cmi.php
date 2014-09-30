<?php

/**
 * Common Module Interface
 * 
 * 公用模块接口
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

include('./configs/cmi.php');
class cmi{

	/**
	 * 构造函数
	 * @param string [[模块名1][,模块名2][,模块名...]]
	 * @return void
	 */
	function cmi(){
		$args = func_get_args();
		foreach($args as $m) $this->mod($m);//加载初始化模块
	}

	/**
	 * 加载模块
	 * @param string [模块名1][,模块名2][,模块名...]
	 * @return void
	 */
	function mod($m){

		$args = func_get_args();
		if(empty($args)) return false;
		if(is_array($args)){
			if(count($args) == 1 && is_string($m)) $args = explode(',', $m);
			if(count($args) > 1){
				foreach($args as $m) $this->mod(trim($m));
				return true;
			}
		}
		
		if(!isset($this->$m)){
			$file = CMI_MODULE_PATH.$m.DS.'interface.php';//加载模块类
			if(!file_exists($file)) return $this->load_class($m);//加载系统类
			require_once($file);
			$class = 'cmi_' . $m;
			$mod = new $class();
			$this->$m = $mod;
			$this->debug($m);
		}
		
		return $this->$m;
	}

	/**
	 * 加载类
	 * @param string $name 类名
	 * @param string $path 路径
	 * @param bool $init 是否初始化
	 * @return object
	 */
	function load_class($m, $path='') {
		if(!isset($this->$m)){
			$file = $path ? $page : CMI_CLASS_PATH.$m.'.class.php';
			if(!file_exists($file)) return false;
			require_once($file);
			$class = 'cmi_' . $m;
			$mod = new $class();
		}
		$this->$m = $mod;
		return $this->$m;
	}

	/**
	 * 加载公用配置
	 * @param string $m 模块名
	 * @return object
	 */
	function load_config($m='') {
		if(!$m) $m = 'cmi';
		$file = CMI_CONFIG_PATH.$m.'.php';
		if(!file_exists($file)) return false;
		return include($file);
	}

	/**
	 * 调用HTTP接口
	 * @param string $m 模块名
	 * @param array $args 参数
	 * @return object
	 */
	function call_http($args=array()) {
		$this->mod('http');
		$data['args'] = json_encode($args);
		$rs = $this->http->post(CMI_HTTP_URL, $data);
		if($rs['code'] == 1) return json_decode($rs['data']);
	}

	/**
	 * 获取默认模块
	 * @return object
	 */
	function get_default_modules() {
		$default_modules = (object)null;
		$configs = $this->load_config();
		if(!isset($configs['default_modules'])) return false;
		$default_modules_name = $configs['default_modules'];
		foreach($default_modules_name as $m){
			if(!isset($default_modules->$m)){
				$file = CMI_MODULE_PATH.$m.DS.'interface.php';//加载模块类
				if(!file_exists($file)) $file = CMI_CLASS_PATH.$m.'.class.php';//加载系统类
				if(!file_exists($file)) return false;
				require_once($file);
				$class = 'cmi_' . $m;
				$mod = new $class();
				$default_modules->$m = $mod;
			}
		}
		return $default_modules;
	}

	/**
	 * debug
	 * @param string $m 变量
	 * @return object
	 */
	function debug($m){
		if(isset($_GET['debug'])) var_dump($m);
	}
}

class cmi_module extends cmi{

	var $m;//模块名
	function cmi_module(){
		$configs = parent::load_config();
		if(isset($configs['default_modules'])){
			$default_modules = parent::get_default_modules();
			if(is_array($configs['default_modules'])) foreach($configs['default_modules'] as $m) $this->$m = $default_modules->$m;
		}
		$this->model();//加载数据模型
		if(!$this->m) $this->m = str_replace(array('cmi_', 'cmi'), '', get_class($this));//获取模块名
	}

	/**
	 * 加载数据模型
	 * @param string $name 类名
	 * @param string $m 模块名
	 * @param bool $init 是否初始化
	 * @return object
	 */
	function model($name='', $m='') {
    	if(!$m) $m = str_replace(array('cmi_', 'cmi'), '', get_class($this));//获取模块名
    	if(!$m) return false;
    	if(!$name) $name = 'model';
		if(!isset($this->$m->$name)){
			if(!isset($this->$m)) $this->$m = (object)null;
			$file = $name == 'model' ? CMI_MODULE_PATH.$m.DS.'model.php' : CMI_MODULE_PATH.$m.DS.$name.'.model.php';
			if(!file_exists($file)) return false;
			include_once($file);
			$class = $name == 'model' ? 'cmi_model_'.$m : 'cmi_model_'.$m.'_'.$name;
			$model = new $class();
			$this->$name = $model;
		}
		return $this->$name;
	}

	/**
	 * 加载模块配置
	 * @param string $m 模块名
	 * @return object
	 */
	function config($m='') {
		if(!$m) $m = str_replace(array('cmi_', 'cmi'), '', get_class($this));//获取模块名
    	if(!$m) return false;
		if(!isset($this->configs)){
			$file = CMI_MODULE_PATH.$m.DS.'config.php';
			if(!file_exists($file)) return false;
			$this->configs = include($file);
		}
		return $this->configs;
	}

}

?>