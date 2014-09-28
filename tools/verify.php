<?php

/**
 * Common Module Interface
 * 
 * 验证工具
 *
 * @copyright (c) 2009-2014 Innet Inc
 * @author E:\pan\pan\code\cmi
yaosine@gmail.com
 *
 */

class cmi_verify{

    /**
     * 是否为有效的邮箱
     *
     * @param string $mail
     * @return bool
     */
    function is_mail($mail){
        return preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $mail) ? true : false;
    }

    /**
     * 是否为有效的手机号码
     *
     * @param string $mobile
     * @return bool
     */
    function is_mobile($mobile){
        return ereg("^1[0-9]{10}$", $mobile);
    }

    /**
     * 是否为有效的密码
     *
     * @param string $pwd
     * @return bool
     */
    function is_password($pwd){
        return ereg("^[a-zA-Z0-9]{6,}$", $pwd);
    }


}

?>