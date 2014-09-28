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


require_once './cmi.php';
$cmi = new cmi;

echo 'CMI test:<br>';


//HTTP
$cmi->load('http');
$rs = $cmi->http->post('http://cmi.comoom.com/', array('mods'=>'demo'));
$cmi->debug($rs);

echo "<hr>";
//模块接口调用
$cmi->mod('demo');
$cmi->demo->demo();

//多模块接口调用
$cmi->mod('demo, demo2');
$cmi->demo->demo();
$cmi->demo2->demo2();

//数据库操作
$sdb = $cmi->db->load('SDB');
$query = $sdb->query('select count(*) from user');
$row = $sdb->fetch_array();
var_dump($row);




/*
//接口实例
$cmi->mod('user');
$rs = $cmi->user->get_user(1000001);//获取用户信息
$cmi->mod('login');
$rs = $cmi->login->is_login(1000001);//查询用户是否登录
*/

?>