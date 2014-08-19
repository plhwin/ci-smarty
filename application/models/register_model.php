<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_model extends CI_Model {
	
    //构造函数
    public function __construct() {
        parent::__construct();
    }

 	/**
     * @desc 检查同一IP注册限制
     * @param $onlineip
     */
	public function check_reg_restrictions_logic($onlineip){
    	$result = 'OK';

    	$regipdate = $this->config->item('regipdate');
    	
    	$blacklistip = array();
    	
    	if(in_array($onlineip, $blacklistip)){
    		$result = "抱歉，你的IP $onlineip 被系统禁止使用注册功能。";
    	} else {
	    	if($regipdate) {
				$regtime = GetOne("SELECT regtime 
					FROM ".tname('member')." 
					WHERE regip='$onlineip' 
					ORDER BY regtime DESC");
				if($regtime && is_numeric($regtime)) {
					if(SITE_TIME - $regtime < $regipdate) {
// 						$result = '抱歉，同一个IP在 '.($regipdate/60).' 分钟内只能注册一个帐号';
						$result = '抱歉，同一个IP在 '.($regipdate).' 秒内只能注册一个帐号';
					}
				}
			}
    	}

		return $result;
    }


    public function doreg($setarr, $password_once){
    	
    	
    	$regip = getonlineip();
    	
    	$result = $this->check_reg_restrictions_logic($regip);
		if($result != 'OK'){
			$msgTodo = '请稍微休息一下，稍后再来注册。';
			$msgBody = array('<a href="'.SITE_URL.'">&raquo;返回主页</a>');
			showexclaimed($result, $msgTodo, $msgBody);
		}
    	

    	$message = array();
		$allowreg = 0;
		
		
		//检查邮箱
		$result = $this->ckmod->check_email($setarr['email'], 1);
   	 	if($result == 'OK'){
			$message['email'] = $this->getok();
			$allowreg++;
		} else {
			$message['email'] = $this->geterr($result);
		}
		//md5加密密码
		$passwordmd5 = '';

		//检查登录密码并生成md5加密密码
		$result = $this->pwdmod->check_password_once($password_once);
		if($result == 'OK'){
			$passwordmd5 = md5($password_once);
			$message['password_once'] = $this->getok();
			$allowreg++;
		} else {
			$message['password_once'] = $this->geterr($result);
		}

		
		if($allowreg == 2){
			//密码盐
			$salt = password_salt();
			
			//邀请人
			$inviteuid = $this->user->get_inviter();
			
			
			//默认昵称
			$setarr['nickname'] = substr($setarr['email'], 0, strpos($setarr['email'], '@'));
			
			
			$member = array(
				'nickname' => $setarr['nickname'],
				'inviteuid' => $inviteuid,
				'invitenum' => 0,
				'userpwd' => password_encrypt($password_once, $salt),
				'authpwd' => md5(SITE_TIME),//本地密码随机生成
				'salt' => $salt,
				'email' => $setarr['email'],
				'emailcert' => 0,
				'regtime' => SITE_TIME,
				'lastlogintime' => 0,
				'regip' => $regip,
				'lastloginip' => '',
				'status' => 1
			);
			$uid = inserttable('member', $member, 1);

			if($uid){
				if($inviteuid > 0){
					//更新邀请人的总数
					$this->db->query("UPDATE ".tname('member')." SET invitenum=invitenum+1 WHERE uid='$inviteuid'");
				}
				
				$member['uid'] = intval($uid);
				$member['memberid'] = $setarr['email'];
				$member['cookietime'] = 0;
				
				return array('success'=>$member);
			}
		}
		return array('failed'=>$message);
    }



	public function geterr($info){
		return array('status'=>'error','info'=>$info);
	}

	public function getok($info = ''){
		return array('status'=>'ok', 'info'=>$info);
	}

	/**
     * @desc 格式化注册信息数组，有错误的的时候只显示一项
     */
    public function format_message($message){
    	
    	$result = $message;
    	if(is_array($message)){
    		$newarr = array();
    		$num = 0;
    		foreach ($message as $key=>$value){
    			
    			if($value['icon'] == 'error'){
    				if($num > 0 && $key!='cellphone'){
    					continue;
    				}
    				$num++;
    			}
    			
    			$newarr[$key] = $value;
    		}
    		$result = $newarr;
    	}
    	return $result;
    }
    
}