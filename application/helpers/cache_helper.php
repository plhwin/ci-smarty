<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function cache_read($action, $mode = 'i'){
	$cachefile = CACHE_DATA.'/data_'.$action.'.php';
	if(!file_exists($cachefile)) return array();
	return $mode == 'i' ? include $cachefile : @file_get_contents($cachefile);
}

function cache_update($action = ''){
	
	$CI =& get_instance();
	$data = array();
	switch($action){
		case 'cms_category';
			$query = $CI->db->query("SELECT * FROM ".tname('cms_category')." WHERE 1 ORDER BY sortrank ASC");
			if($query){
				foreach ($query->result_array() as $row){
					$data[] = $row;
				}
			}
		break;
		case 'cms_directory';
			$category = cache_read('cms_category');
			if(!$category){
				$category = cache_update('cms_category');
			}
			
			//获取全部的一级分类
			$topcate = array();
			foreach ($category as $val){
				if($val['parentid'] == 0){
					$topcate[] = $val;
				}
			}
			
			$CI->load->model('Cms_model', 'cmsmod');
			
			foreach ($topcate as $row){
				$orderaddtime = "DESC";
				//帮助分类的目录缓存按照 发布时间越早排序越靠前 的规则生成缓存
				if($row['cid'] == 1){
					$orderaddtime = "ASC";
				}
				$row['child'] = $CI->cmsmod->get_child_by_cid($row['cid'], $orderaddtime);
				$row['cms'] = $CI->cmsmod->get_cms_by_cid($row['cid']);
				$data[] = $row;
			}
		break;
		
		default:
			$actions = array('cms_category', 'cms_directory');
			array_map('cache_update', $actions);
			return true;
	}
	cache_write('data_'.$action.'.php', $data);
	return $data;
}

//数组转换成字串
function arrayeval($array, $level = 0) {
	$space = '';
	for($i = 0; $i <= $level; $i++) {
		$space .= "\t";
	}
	$evaluate = "Array\n$space(\n";
	$comma = $space;
	foreach($array as $key => $val) {
		$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
		$val = !is_array($val) && (!preg_match("/^\-?\d+$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
		if(is_array($val)) {
			$evaluate .= "$comma$key => ".arrayeval($val, $level + 1);
		} else {
			$evaluate .= "$comma$key => $val";
		}
		$comma = ",\n$space";
	}
	$evaluate .= "\n$space)";
	return $evaluate;
}

function cache_write($file, $string, $type = 'array'){
	if(is_array($string)){
		if($type == 'array'){
			$string = "<?php\r\n".
				"if ( ! defined('BASEPATH')) exit('No direct script access allowed');\r\n".
				"return ".arrayeval($string).
				";\r\n?>";
		}elseif($type == 'constant'){
			$data = '';
			foreach($string as $key => $value) {
				$data .= "define('".strtoupper($key)."','".addslashes($value)."');\n";
			}
			$string = "<?php\r\n".$data."\r\n?>";
		}
	}
	if(!is_dir(CACHE_DATA)) {
		mkdir(CACHE_DATA, 0777, true);
	}
	$strlen = @file_put_contents(CACHE_DATA.'/'.$file, $string);
	chmod(CACHE_DATA.'/'.$file, 0777);
	return $strlen;
}

function cache_delete($action){
	return @unlink(CACHE_DATA.'/data_'.$action.'.php');
}