<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 管理员导航菜单、权限
|--------------------------------------------------------------------------
*/

$ADMIN_MENU = array(
	'admincp' => array(
		'menu' => '首页',
		'isajax' => 0,
		'status' => 1,
		'method' => array(
			'index' => array(
				'menu' => '管理首页',
				'isajax' => 0,
				'status' => 1
			)
		)
	),
	
	'logcp' => array(
		'menu' => '系统日志',
		'isajax' => 0,
		'status' => 1,
		'method' => array(
			'index' => array(
				'menu' => '日志记录',
				'isajax' => 0,
				'status' => 1
			)
		)
	),
	
	'privilegescp' => array(
		'menu' => '权限',
		'isajax' => 0,
		'status' => 1,
		'method' => array(
			'adminlist' => array(
				'menu' => '管理员',
				'isajax' => 0,
				'status' => 1
			),
			'addadmin' => array(
				'menu' => '新增管理员',
				'isajax' => 0,
				'status' => 1
			),
			'edit' => array(
				'menu' => '修改权限',
				'isajax' => 0,
				'status' => 0
			),
			'editadmin' => array(
				'menu' => '修改资料',
				'isajax' => 0,
				'status' => 0
			)
		)
	),
	'adminsettings' => array(
		'menu' => '设置',
		'isajax' => 0,
		'status' => 1,
		'method' => array(
			'index' => array(
				'menu' => '基本设置',
				'isajax' => 0,
				'status' => 1
			),
			'password' => array(
				'menu' => '密码修改',
				'isajax' => 0,
				'status' => 1
			)
		)
	),
);