<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	/**
	 * 
	 * @var User
	 */
	public $user;
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function index(){
		$redirect_url = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : SITE_URL;
		$this->user->clearsession($this->user->uid);
    	$this->user->clearcookie();
		isset($_SESSION) || @session_start();
		session_destroy();
		showmessage('正在退出登录...', $redirect_url, 0);
	}
}