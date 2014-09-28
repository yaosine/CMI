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

class cmi_demo2 extends cmi{

    function demo2(){
        echo "demo2";
        $sdb = $this->db->load('SDB');
        $row = $sdb->result('select count(*) from user');
        var_dump($row);
    }

    function http_demo($a, $b, $c){
        echo "http_demo";
        echo '$c :';
        echo var_export($c);
		echo "http_demo ok!";
    }
}

?>