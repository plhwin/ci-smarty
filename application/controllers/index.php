<?php 
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}
	
	public function index() {
		set_site_title($this->config->item('site_title'));
		$this->tpl->display();
	}
	
	public function showmsg() {
		showmessage("消息提示内容", 0);
	}
	
	public function throwmsg() {
		showsuccess("成功提示", '刚才的操作已经成功。', array('无超链接提示1', '<a href="'.SITE_URL.'">返回首页</a>', '提示3'));
	}
	
}