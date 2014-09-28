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

class cmi{

	var $modules;
	var $configs;
	var $models;

	/**
	 * 构造函数
	 * @param string [[模块名1][,模块名2][,模块名...]]
	 * @return void
	 */
	function cmi(){
		//$this->mod('session');
		$this->configs = require('configs/cmi.php');
		$default_modules = $this->configs['default_modules'];
		foreach($default_modules as $m) $this->mod($m);

		$args = func_get_args();
		foreach($args as $m) $this->mod($m);
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
			$file = CMI_MODULE_PATH.$m.DS.'interface.php';
			if(!file_exists($file)) $file = CMI_CLASS_PATH.$m.'.class.php';
			if(!file_exists($file)) return false;
			require_once($file);
			$class = 'cmi_' . $m;
			$mod = new $class();
			$this->config($m);
			$this->$m = $mod;
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
	 * 加载模型
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
			$file = $name == 'model' ? CMI_MODULE_PATH.$m.DS.'model.php' : CMI_MODULE_PATH.$m.DS.$name.'.model.php';
			if(!file_exists($file)) return false;
			include($file);
			$class = $name == 'model' ? 'cmi_model_'.$m : 'cmi_model_'.$m.'_'.$name;
			$model = new $class();
			$this->$m->$name = $model;
		}
		return $this->$m->$name;
	}

	/**
	 * 加载模块配置
	 * @param string $m 模块名
	 * @return object
	 */
	function config($m='') {
		if(!$m) $m = str_replace(array('cmi_', 'cmi'), '', get_class($this));//获取模块名
    	if(!$m) return false;
		if(!isset($this->$m->configs)){
			$file = CMI_MODULE_PATH.$m.DS.'config.php';
			if(!file_exists($file)) return false;
			$this->$m->configs = include($file);
		}
		return $this->$m->configs;
	}

	/**
	 * 加载公用配置
	 * @param string $m 模块名
	 * @return object
	 */
	function load_config($m) {
		if(!isset($this->configs->$m)){
			$file = CMI_CONFIG_PATH.$m.'.php';
			if(!file_exists($file)) return false;
			$this->configs->$m = include($file);
		}
		return $this->configs->$m;
	}

	/**
	 * http
	 * @param string $m 模块名
	 * @return object
	 */
	function http($backtrace) {
		$trace = $backtrace[0];
		$data['mods'] = str_replace('cmi_', '', $trace['class']);
		$data['func'] = $trace['function'];
		//$data['args'] = $trace['args'][0];
		$data['args'] = json_encode($trace['args']);
		$rs = $this->http->post('http://cmi.inzhan.com/', $data);
		if($_GET['debug']) var_dump($rs);
		if($_GET['trace']) var_dump($data, $trace);
		return $rs['data'];
	}

	/**
	 * 加载模块
	 * @param string $m 模块名
	 * @return object
	 */
	function load($m) {
		$this->modules = $m;
		if(!$this->$m) $this->$m = &$this;
		return true;
	}

	/**
	 * 请求接口
	 * @param string $m 模块名
	 * @param array $args 参数
	 * @return object
	 */
	function call($func, $args=array()) {
		$data['mods'] = $this->modules;
		$data['func'] = $func;
		$data['args'] = json_encode($args);
		$rs = $this->http->post('http://cmi.inzhan.com/', $data);
		if($_GET['debug']) var_dump($data, $rs);
		return $rs['data'];
	}

	/**
	 * debug
	 * @param string $m 变量
	 * @return object
	 */
	function debug($m){
		if($_GET['debug']) var_dump($m);
	}
}

?>