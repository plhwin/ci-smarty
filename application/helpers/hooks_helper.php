<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function pagecache(){
	
	$RTR =& load_class('Router');
	$class  = strtolower($RTR->fetch_class());
	$method = strtolower($RTR->fetch_method());
	
	$CI =& get_instance();
	$cachemodule = $CI->config->item('cachemodule');
	
	$result = FALSE;
	
	$query = empty($_SERVER['QUERY_STRING'])?false:true;

	if($cachemodule && in_array($class, array_keys($cachemodule)) && !$query){
		if (empty($cachemodule[$class])){
			$result = TRUE;
		} else {
			if (in_array($method, $cachemodule[$class])){
				$result = TRUE;
			}
		}
	}
	if($result) {
		cache_header();
	}
	$CI->tpl->assign('pagecache', $result);
	
	return $result;
}