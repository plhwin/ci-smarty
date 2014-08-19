<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function cache_header($cachetime = 21600) {
	@header("Pragma: public");
	@header("Cache-Control: max-age=$cachetime");
	@header("Expires: ".gmdate("D, d M Y H:i:s", time() + $cachetime)." GMT");
}

function get_tpl_resource_name(){
    //加载请求路由处理类,获取控制器名称,方法名称
	$RTR =& load_class('Router');
	$class  = $RTR->fetch_class();
	$method = $RTR->fetch_method();
	$CI =& get_instance();
	$CI->tpl->set_tpl_dir($class);

	$resource_name = $method.SMARTY_SUFFIX_NAME;
	return $resource_name;
}

function tplinit(){
	$RTR =& load_class('Router');
	$class  = strtolower($RTR->fetch_class());
	$method = strtolower($RTR->fetch_method());
	$CI =& get_instance();

	$vars = array(
		'uid'=>$CI->user->uid,
		'member'=>$CI->user->member,
		'timestamp'=>SITE_TIME,
		'sessionid'=>SESSIONID,
		'siteurl'=>SITE_URL,
		'imgdir'=>$CI->config->item('imgdir'),
		'cssdir'=>$CI->config->item('cssdir'),
		'jsdir'=>$CI->config->item('jsdir'),
		'imgpath'=>$CI->config->item('imgpath'),
		'respath'=>$CI->config->item('respath'),
		'site_name'=>$CI->config->item('site_name'),
		'site_domain'=>SITE_DOMAIN,
		'site_subdomain'=>SITE_SUBDOMAIN,
		'site_keywords'=>$CI->config->item('site_keywords'),
		'site_description'=>$CI->config->item('site_description'),
		'siteclass'=>$class,
		'sitemethod'=>$method,
		'inajax'=>INAJAX,
		'thisyear'=>date('Y'),
		'script_url'=>SCRIPT_URL,
		'redirect_url'=>urlencode(REDIRECT_URL)
	);
	$CI->tpl->assign($vars);
}