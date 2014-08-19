<?php 
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	/**
	 * 
	 * @var User
	 */
	public $user;	

	/**
	 * 
	 * @var Register_model
	 */
	public $regmod;
	/**
	 * 
	 * @var Password_model
	 */
	public $pwdmod;
	/**
	 * 
	 * @var Check_model
	 */
	public $ckmod;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('Register_model', 'regmod');
		$this->load->model('Password_model', 'pwdmod');
        $this->load->model('Check_model', 'ckmod');
	}
	
	public function index() {
		$message = array(
			'email'=>array('status'=>'', 'info'=>''),
			'password_once'=>array('status'=>'', 'info'=>'')
		);
		if(submitcheck('registersubmit')) {
			$POST = newhtmlspecialchars($_POST);
			
			$result = $this->regmod->doreg($POST, $_POST['password_once']);
			
			if(!empty($result['success'])){
				$data = $result['success'];
				$setarr = array(
					'uid' => $data['uid'],
					'nickname' => $data['nickname'],
					'authpwd' => $data['authpwd'],
					'loginuser' => $data['memberid'],
				);
				

				$this->user->memberlogin($setarr, $data['cookietime']);
				
				showmessage('注册成功', SITE_URL, 0);
			} else if(!empty($result['failed'])) {
				$message = $result['failed'];
				
			}
		}
		
		$vars = array(
			'message' => $message
		);
		$this->tpl->assign($vars);
		set_page_title('注册');
		$this->tpl->display();
	}
	
	
	//AJAX方式检查邮箱地址
	public function checkemail(){
		ajax_header();
		$email = isset($_POST['email']) ? newhtmlspecialchars($_POST['email']) : '';
		$result = $this->ckmod->check_email($email, 1);
		if($result == 'OK'){
			$r = 'success';
		} else {
			$r = $result;
		}
		exit("email_$r");
    }
	
	
}
