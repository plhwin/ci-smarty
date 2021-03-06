<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admincp extends CI_Controller {
	
    public function __construct(){
    	parent::__construct();
    	
    	$RTR =& load_class('Router');
    	if($RTR->fetch_method() != 'login'){
  			ck_adminlogin();
    	}
    	
    	set_module_title('超管后台');
    }
    
	public function index(){
		set_page_title('首页');
		$this->tpl->display();
    }
    
    public function login(){
    	
    	$errorinfo = array();
    	if(submitcheck('loginsubmit')) {
    		if(empty($_POST['adminname'])) {
    			$errorinfo[] = '请输入用户名';
    		} else {
    			if(empty($_POST['password'])) {
    				$errorinfo[] = '请输入密码';
   			} else {
    				if(empty($_POST['seccode'])) {
    					$errorinfo[] = '请输入验证码';
    				} else {
    					if(!checkseccode($_POST['seccode'])){
							$errorinfo[] = '验证码错误,如看不清,请点击图片刷新';
						} else {
							
							$sql = "SELECT * 
			    				FROM ".tname('admin_user')." 
			    				WHERE adminname='{$_POST['adminname']}'";
			    			$adminuser = GetRow($sql);
	    					if(empty($adminuser)){
			    				$errorinfo[] = '用户名不存在';
			    			} else {
								if(md5($_POST['password']) != $adminuser['password']) {
									$errorinfo[] = '密码错误';
								} else {
									if($adminuser['status'] == 1){
										$_SESSION['site_adminid'] = $adminuser['adminid'];
										$_SESSION['site_adminname'] = $adminuser['adminname'];
										
										addAdminLog("{me} 成功登录系统。");
										
										showmessage("登录成功", SITE_URL."/admincp", 0);
									} else {
										$msgTilte = '对不起，您没有权限登录管理系统。';
										$msgTodo = '请确定您是否有足够的权限登录管理系统。';
										$msgBody = array();
										showprohibition($msgTilte,$msgTodo,$msgBody);
									}
								}
			    			}
						
						}
    				}
    			}
    		}
    	}
    	$vars = array(
			'errorinfo' => $errorinfo
		);
		set_page_title('登录');
		$this->tpl->assign($vars);
		$this->tpl->display();
    }
    
    public function logout(){
    	addAdminLog("{me} 成功退出系统。");
    	session_destroy();
    	showmessage('你已经安全退出了', SITE_URL.'/admincp/login', 0);
    }
}