<?php

/**
 * Common Module Interface
 * 
 * HTTP接口调用
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

include './cmi.php';
$cmi = new cmi;

$mods = isset($_REQUEST['mods']) ? $_REQUEST['mods'] : '';//模块名
$func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';//接口名
$args = isset($_REQUEST['args']) ? $_REQUEST['args'] : '';//参数

if($mods && $func){
	$cmi->mod($mods);
	$result = $cmi->$mods->$func($args);
	if($result) echo json_encode($result);
}
elseif($args){
	$args_info = json_decode($args);
	if(is_array($args_info)){
		foreach ($args_info as $key => $value) {
			if(isset($value[0])){
				if(isset($value[1])){
					$cmi->mod($value[0]);
					$args_arr = isset($value[2]) ? array_slice($value, 2) : array();
					$args_num = count($args_arr);
					if($args_num == 5) $result[] = $cmi->$value[0]->$value[1]($args_arr[0], $args_arr[1], $args_arr[2], $args_arr[3], $args_arr[4]);
					elseif($args_num == 4) $result[] = $cmi->$value[0]->$value[1]($args_arr[0], $args_arr[1], $args_arr[2], $args_arr[3]);
					elseif($args_num == 3) $result[] = $cmi->$value[0]->$value[1]($args_arr[0], $args_arr[1], $args_arr[2]);
					elseif($args_num == 2) $result[] = $cmi->$value[0]->$value[1]($args_arr[0], $args_arr[1]);
					elseif($args_num == 1) $result[] = $cmi->$value[0]->$value[1]($args_arr[0]);
					else $result[] = $cmi->$value[0]->$value[1]();
				}
			}
		}
		if($result) echo json_encode($result);
	}
}
?>