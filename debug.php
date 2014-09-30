<?php

/**
 * Common Module Interface
 * 
 * 测试
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

echo 'CMI test:<hr>';

require_once './cmi.php';
$cmi = new cmi;

$func = isset($_GET['func']) ? $_GET['func'] : '';
switch ($func) {

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
			array('demo', 'demo2', 'args1', 'args2', 'args3'),//模块名，接口名，参数1，参数2，参数3
		);
		$rs = $cmi->call_http($data);//远程调用接口
		var_dump($rs);

		//HTTP方式调用多个接口
		$data = array(
			array('demo', 'demo2', 'args1', 'args2', 'args3'),//模块名，接口名，参数1，参数2，参数3
			array('demo2', 'demo2', 'args'),
		);
		$rs = $cmi->call_http($data);//合并HTTP请求
		var_dump($rs);
		
		break;

	case 'url':
		//HTTP方式调用多个接口
		$cmi->mod('http');
		$rs = $cmi->http->get('http://127.0.0.1/CMI/?mods=demo&func=demo2');
		if($rs['code'] == 1) var_dump($rs['data']);
		$cmi->debug($rs);
		break;

	default:
		//接口调用演示
		$cmi->mod('demo');
		$cmi->demo->demo();
		break;
}

if(isset($_GET['cmi'])) var_dump($cmi);
echo '<br><hr>CMI test end';

/*
//接口实例
$cmi->mod('user');
$rs = $cmi->user->get_user(1000001);//获取用户信息
$cmi->mod('login');
$rs = $cmi->login->is_login(1000001);//查询用户是否登录
*/

?>