<?php

/**
 * Common Module Interface
 * 
 * session操作类
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

class cmi_session extends cmi {

    private $domian = '.inzhan.com';
    private $life_time = '1800';
    private $db_table = 'session';
    private $db_handle;

    /**
     * 构造函数
     * 
     */
    public function cmi_session() {
        @ini_set("session.cookie_domain", $this->domian);
        @session_module_name("user");
        @session_set_save_handler(
            array(&$this, "open"),
            array(&$this, "close"),
            array(&$this, "read"),
            array(&$this, "write"),
            array(&$this, "destroy"),
            array(&$this, "gc")
        );
        
        session_start();
    }

    function open($savePath, $sessName) {
        if(!$this->life_time) $this->life_time = get_cfg_var("session.gc_maxlifetime");
        $db = $this->mod('db');
        $db->load('SDB_SESSION');
        $db->connect();
        $this->db_handle = $db->connection;
        return true;
    }

    function close() {
        $this->gc($this->left_time);
        // close database-connection
        return @mysql_close($this->db_handle);
    }

    function read($sessID) {
        // fetch session-data
        $res = @mysql_query("SELECT session_data AS d FROM ".$this->db_table." 
            WHERE session_id = '$sessID'
            AND session_expires > ".time(), $this->db_handle);
        
        // return data or an empty string at failure
        if($row = @mysql_fetch_assoc($res))
            return $row['d'];

        return "";
    }

    function write($sessID, $sessData) {
        // new session-expire-time
        $newExp = time() + $this->life_time;

        // is a session with this id in the database?
        $res = @mysql_query("SELECT * FROM ".$this->db_table." 
            WHERE session_id = '$sessID'", $this->db_handle);

        // if yes,
        if(@mysql_num_rows($res)) {

            // ...update session-data
            @mysql_query("UPDATE ".$this->db_table." 
                SET session_expires = '$newExp',
                session_data = '$sessData'
                WHERE session_id = '$sessID'", $this->db_handle);

            // if something happened, return true
            if(@mysql_affected_rows($this->db_handle))
                return true;
        }
        // if no session-data was found,
        else {
            // create a new row
            @mysql_query("INSERT INTO ".$this->db_table." (
                session_id,
                session_expires,
                session_data)
                VALUES(
                    '$sessID',
                    '$newExp',
                    '$sessData')", $this->db_handle);
            // if row was created, return true
            if(@mysql_affected_rows($this->db_handle))
                return true;
        }

        // an unknown error occured
        return false;
    }

    function destroy($sessID) {
        // delete session-data
        @mysql_query("DELETE FROM ".$this->db_table." WHERE session_id = '$sessID'", $this->db_handle);

        // if session was deleted, return true,
        if(@mysql_affected_rows($this->db_handle))
            return true;

        // ...else return false
        return false;
    }

    function gc($sessMaxLifeTime) {
        // delete old sessions
        @mysql_query("DELETE FROM ".$this->db_table." WHERE session_expires < ".time(), $this->db_handle);

        // return affected rows
        return @mysql_affected_rows($this->db_handle);
    }
}

?>