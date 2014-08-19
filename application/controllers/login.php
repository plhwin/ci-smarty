<?php 
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * 
	 * @var User
	 */
	public $user;
	/**
	 * 
	 * @var Password_model
	 */
	public $pwdmod;
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model('Password_model', 'pwdmod');
	}
	
	public function index() {
		$remember = 'on';
		$message = array();
		
		if(submitcheck('loginsubmit')) {
			$POST = newhtmlspecialchars($_POST);
			$remember = empty($POST['remember']) ? 'off' : $remember;
			
			
			//登录ID全角转半角，去除2边空白
			$memberid = sbc2dbc(trim($POST['memberid']));
			if(empty($memberid) || $memberid == '请输入邮箱地址') {
				$message[] = '请输入邮箱地址';
			} else if($_POST['password'] == '') {
				$message[] = '请输入登录密码';
			} else {
			
				$wheresql = "email='{$memberid}'";
				
				$number = GetOne("SELECT COUNT(*) FROM ".tname('member')." WHERE $wheresql");
				if($number === FALSE){
					$message[] = '非常抱歉，目前数据库服务器繁忙，请稍后重新登录！';
				} else {
					if($number == 1){
						$member = GetRow("SELECT * FROM ".tname('member')." WHERE $wheresql");
						
						if($member['status'] == 1){
							
							//检查当前密码
							$result = $this->pwdmod->check_password_current($member['uid'], $POST['password']);
							if($result == 'OK'){
								$setarr = array(
									'uid' => $member['uid'],
									'nickname' => $member['nickname'],
									'authpwd' => $member['authpwd'],
									'loginuser' => $memberid,
								);
								
								$cookietime = ($remember == 'on') ? $this->config->item('logincookietime') : 0;
								
								$this->user->memberlogin($setarr, $cookietime);
								
								showmessage('登录成功', REDIRECT_URL ? REDIRECT_URL : SITE_URL, 0);
								
							} else {
								$message[] = '登录'.$result;
							}
							
						} else {
							$message[] = '你的登录帐号已经被禁用，请联系客服';
						}
						
				
					} else if($number > 1){
						$message[] = '你的登录帐号存在重复，请联系客服';
					} else {
						$message[] = '你输入的登录帐号不存在';
					}
				}
			}
		}
		
		$vars = array(
			'message' =>$message,
			'remember' => $remember,
		);
		
		set_page_title("登录");
		$this->tpl->assign($vars);
		$this->tpl->display();
	}
}
