<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Password_model extends  CI_Model {

	
    //构造函数
    public function __construct() {
        parent::__construct();
    }

	/**
     * @desc 检查当前登录密码是否匹配
     * @param $password_once
     */
    public function check_password_current($uid, $password){
    	
    	if($uid > 0){
    		if($member = $this->user->get_member_by_uid($uid)){
	    		if(empty($password)){
					$result = '密码不能为空。';
				} else {
					$password_encryption = password_encrypt($password, $member['salt']);
					if($member['userpwd'] != $password_encryption){
						$result = '密码不正确。';
					} else {
						$result = 'OK';
					}
				}
    		} else {
    			$result = '会员不存在。';
    		}
    	} else {
    		$result = '会员uid有误。';
    	}
		return $result;
    }
    
    
 	/**
     * @desc 检查密码
     * @param $password_once
     */
    public function check_password_once($password_once){
    	if(empty($password_once)){
			$result = '密码不能为空。';
		} else {
			$pwd_once_len = strlen($password_once);
			if($pwd_once_len < 6 || $pwd_once_len > 16) {
				$result = '密码长度应在6到16个字符之间。';
			} else {
				$result = 'OK';
			}
		}
		return $result;
    }
    
    /**
     * @desc 检查重复输入的密码
     */
	public function check_password_twice($password_twice, $passwordmd5){
		if(empty($password_twice)){
			$result = '确认密码不能为空。';
		} else {
			if(!empty($passwordmd5) && $passwordmd5 != md5($password_twice)){
				$result = '两次密码输入不一致。';
			} else {
				$result = 'OK';
			}
		}
		return $result;
    }
	
    /**
     * @desc 修改用户密码
     * @param $newpassword 用户修改的新密码 
     */
    public function change_password($uid, $newpassword){
    	
    	//生成新的密码盐
		$salt = password_salt();
		//生成新的验证密码
		$authpwd = md5(SITE_TIME);
		//生成新的密码
		$userpwd = password_encrypt($newpassword, $salt);
		
		$setarr = array(
			'userpwd' => $userpwd,
			'authpwd' => $authpwd,
			'salt' => $salt,
		);
    	
    	return updatetable('member', $setarr, array('uid'=>$uid));
    }
}