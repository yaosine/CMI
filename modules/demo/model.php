<?php

/**
 * Common Module Interface
 * 
 * 模块数据模型
 * 
 * 用于实现接口的具体逻辑，包括数据处理及各模块交互等业务逻辑
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

class cmi_model_demo extends cmi_module{

    function demo(){
        echo "model_demo end";
    }

    //调用其它模块接口
    function other_demo(){
        echo "other_demo<br>";
        $this->mod('demo2');
        $this->demo2->demo();
    }

    //数据库查询操作
    function db_query_demo(){

        echo "<br>db_query_demo:<br>";
        $this->mod('db');
        $sdb = $this->db->load('SDB');//加载SDB从库
        //$sdb = $this->mod('db')->load('SDB');//直接加载

        //取单个值
        $data = $sdb->result('select count(*) from user');
        var_dump($data);//输出单个值

        //取多个值
        $data = $sdb->result('select name,mail from user');
        var_dump($data);//输出数组：$data['name'], $data['mail']

        //取列表
        $sdb->query('select * from user');
        $data = $sdb->fetch_array();
        var_dump($data);//输出结果集

        //循环取列表
        $query = $sdb->query('select * from user');
        while ($row = $sdb->fetch_array($query)) {
            $data[] = $row;//数据处理
        }
        if(isset($rs)) var_dump($rs);
    }

    //数据库插入操作
    function db_insert_demo(){

        echo "<br>db_insert_demo:<br>";
        $this->mod('db');
        $mdb = $this->db->load('MDB');//加载MDB主库

        //使用SQL插入数据
        if(isset($_GET['do']) && $_GET['do'] == 1) $rs = $mdb->query("insert into user (name, mail) value('test', 'test@test.com')");

        //使用insert方法插入数据：表名，数组
        if(isset($_GET['do']) && $_GET['do'] == 2) $rs = $mdb->insert('user', array('name'=>'test', 'mail'=>'test@test.com'));

        if(isset($rs)) var_dump($rs);
    }

    //数据库更新操作
    function db_update_demo(){

        echo "<br>db_update_demo:<br>";
        $this->mod('db');
        $mdb = $this->db->load('MDB');//加载MDB主库

        //使用SQL更新数据
        if(isset($_GET['do']) && $_GET['do'] == 1) $rs = $mdb->query("UPDATE user SET `name` = 'test2', `mail`='test2@test.com' WHERE `id` = 5");

        //使用update方法更新数据：表名，数组，条件
        if(isset($_GET['do']) && $_GET['do'] == 2) $rs = $mdb->update('user', array('name'=>'test2', 'mail'=>'test2@test.com'), array('id'=>6));
        
        if(isset($rs)) var_dump($rs);
    }

    //数据库删除操作
    function db_delete_demo(){

        echo "<br>db_delete_demo:<br>";
        $this->mod('db');
        $mdb = $this->db->load('MDB');//加载MDB主库

        //使用SQL更新数据
        if(isset($_GET['do']) && $_GET['do'] == 1) $rs = $mdb->query("DELETE FROM user WHERE `id` = 6");

        //使用update方法更新数据：表名，数组，条件
        if(isset($_GET['do']) && $_GET['do'] == 2) $rs = $mdb->delete('user', array('id'=>7));
        
        if(isset($rs)) var_dump($rs);
    }
}

?>