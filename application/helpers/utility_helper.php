<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//获取客户端IP
function getonlineip($format=0) {

	if(isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP'] && strcasecmp($_SERVER['HTTP_CDN_SRC_IP'], 'unknown')){
		$onlineip = $_SERVER['HTTP_CDN_SRC_IP'];
	} elseif(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
	$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';

	if($format) {
		$ips = explode('.', $onlineip);
		for($i=0;$i<3;$i++) {
			$ips[$i] = intval($ips[$i]);
		}
		return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
	} else {
		return $onlineip;
	}
}

function newhtmlspecialchars($string) {
	if(is_array($string)){
		return array_map('newhtmlspecialchars', $string);
	} else {
//		$string = htmlspecialchars($string, ENT_QUOTES);
		$string = htmlspecialchars($string);
		$string = sstripslashes($string);
		$string = saddslashes($string);
		return trim($string);
	}
}

//去掉slassh
function sstripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = sstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function isemail($email) {
	return strlen ( $email ) > 8 && preg_match ( "/^[-_+.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email );
}

function isusername($string){
	//只允许汉字，大小写字母，数字作为用户名
	return preg_match("/^[\x{4e00}-\x{9fa5}|a-z|A-Z|0-9]+$/u", $string);
}

function is_url($url){
	return preg_match("/^(https|http):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/", $url);
//	return preg_match("/^(https|http|ftp|rtsp|mms):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/", $url);
//	return preg_match('/http:\/\/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,3}/', $url);
}

function is_ip($ip){ 
	if(!strcmp(long2ip(sprintf('%u',ip2long($ip))),$ip)) {
		return true;
	}
	return false;
}

function is_private_ip($ip) { 
	/*
	ip地址中预留的内网ip地址如下：
	A类： 10.0.0.0 ～ 10.255.255.255
	B类： 172.16.0.0 ～ 172.31.255.255
	C类： 192.168.0.0 ～ 192.168.255.255
	D类：127.0.0.0 ~ 127.255.255.255
	*/
	$i = explode(".", $ip); 
	if ($i[0] == 10) return true;
	if ($i[0] == 127) return true;
	if ($i[0] == 172 && $i[1] > 15 && $i[1] < 32) return true; 
	if ($i[0] == 192 && $i[1] == 168) return true; 
	return false;
}

function get_subdomain($url){
	$url = trim($url);
	$urlarr = parse_url($url);
	$subdomain  = 'unkown';
	if(!empty($urlarr['host'])){
		$subdomain = $urlarr['host'];
	}
	return $subdomain;
}

function get_domain($url){
	$url = trim($url);
	$urlarr = parse_url($url);
	$domain  = 'unkown';

	if(!empty($urlarr['host'])){

		if(is_ip($urlarr['host'])){
			$domain = $urlarr['host'];
		} else {
			$hostarr = explode('.', $urlarr['host']);
			$length = count($hostarr);
			if($length >= 2){
				$domain_suffix = array(
					'.com','.net','.biz','.org','.info','.hk','.com.hk','.name','.ms','.so','.pl',
					'.travel','.mobi','.tw','.com.tw','.idv.tw','.org.tw','.jobs','.com.cn','.net.cn',
					'.org.cn','.gov.cn','.cn','.ac.cn','.bj.cn','.sh.cn','.tj.cn','.cq.cn','.he.cn',
					'.sx.cn','.nm.cn','.ln.cn','.jl.cn','.hl.cn','.js.cn','.zj.cn','.ah.cn','.fj.cn',
					'.jx.cn','.sd.cn','.ha.cn','.hb.cn','.hn.cn','.gd.cn','.gx.cn','.hi.cn','.sc.cn',
					'.gz.cn','.yn.cn','.xz.cn','.sn.cn','.gs.cn','.qh.cn','.nx.cn','.xj.cn','.tw.cn',
					'.hk.cn','.mo.cn','.la','.sh','.ac','.io','.ws','.us','.tm','.cc','.tv','.vc',
					'.ag','.bz','.in','.mn','.sc','.me','.tk','.ie','.cs','.jp','.cu','.mu','.sm',
					'.ad','.cv','.il','.mv','.sn','.ae','.de','.am','.it','.es','.cm','.mo','.edu',
					'.gov','.int','.pro','.aero','.coop','.museum','.gd','.fm','.at','.sb','.edu.cn'
				);

				//最后一位的结尾
				$lastone_suffix = '.'.$hostarr[$length-1];
				if(in_array($lastone_suffix, $domain_suffix)){
					$domain = $hostarr[$length-2].'.'.$hostarr[$length-1];
				}
				if($length >= 3){
					//最后两位的组合
					$lasttwo_suffix = '.'.$hostarr[$length-2].'.'.$hostarr[$length-1];
					if(in_array($lasttwo_suffix, $domain_suffix)){
						$domain = $hostarr[$length-3].'.'.$hostarr[$length-2].'.'.$hostarr[$length-1];
					}
				}
			}
		}
	}
	return $domain;
}

//计算字符串长度,1个中文字符长度为1，1个英文字符串长度为0.5
function cnstrlen($str) {
	$i = 0;
	$count = 0;
	$len = strlen ($str);
	while ($i < $len) {
		$chr = ord ($str[$i]);
		if($chr > 127){
			$count++;
		} else {
			$count+=0.5;
		}
		$i++;
		if($i >= $len) break;
		if($chr & 0x80) {
			$chr <<= 1;
			while ($chr & 0x80) {
				$i++;
				$chr <<= 1;
			}
		}
	}
	return $count;
}


//取消HTML代码
function shtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = shtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
			str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

function display(){
	$CI =& get_instance();
    $CI->tpl->display(get_tpl_resource_name());
}

function tpl_fetch($folder, $resource_name){
	tplinit();

	$CI =& get_instance();
	$CI->tpl->set_tpl_dir($folder);
    return $CI->tpl->fetch($resource_name.SMARTY_SUFFIX_NAME);
}

function set_site_title($title){
	tpl_assign('site_title', $title);
}

function set_page_title($title){
	tpl_assign('pagetitle', $title);
}

function set_module_title($title){
	tpl_assign('moduletitle', $title);
}

function set_meta_keywords($keywords){
	tpl_assign('meta_keywords', $keywords);
}

function set_meta_description($description){
	tpl_assign('meta_description', $description);
}

function tpl_assign($name, $value){
	$CI =& get_instance();
	$CI->tpl->assign($name, $value);
}



//判断提交是否正确
function submitcheck($var) {
	if(!empty($_POST[$var]) && $_SERVER['REQUEST_METHOD'] == 'POST') {
		if((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['formhash'] == formhash()) {
			return TRUE;
		} else {
			showmessage('您的请求来路不正确或表单验证串不符，无法提交。请尝试使用标准的web浏览器进行操作。', 0);
		}
	} else {
		return FALSE;
	}
}

function formhash() {

	$CI =& get_instance();
	$hashadd = defined('IN_ADMINCP') ? 'Only For site AdminCP' : '';
	$formhash = substr(md5(substr(SITE_TIME, 0, -7).'|'.$CI->user->uid.'|'.md5($CI->config->item('site_key')).'|'.$hashadd), 8, 8);
	return $formhash;
}

//检查会员是否登录
function checklogin($redirect_url = '') {
	if(!islogin()) {

		if(!empty($redirect_url)){
			$redirect_url = $redirect_url;
		} else {

			$redirect_url = SCRIPT_URL;

			if(INAJAX){
				if(!empty($_SERVER['HTTP_REFERER'])){
					$redirect_url = $_SERVER['HTTP_REFERER'];
				}
			}
		}
		showmessage('你需要登录后才能进行本次操作。',SITE_URL.'/login/?redirect_url='.urlencode($redirect_url), 0);
	}
}

function get_admin_privileges($all_privileges){
	$admin_privileges = array();
	if(isadmin()){
		$privileges = GetOne("SELECT privileges FROM ".tname('admin_user')." WHERE adminid='{$_SESSION['site_adminid']}'");
		$privileges = empty($privileges) ? '' : $privileges;
		
		
		if(!empty($privileges)){
			if($privileges == 'ALL PRIVILEGES'){
				$admin_privileges = $all_privileges;
			} else {
				$admin_privileges = unserialize($privileges);
				$admin_privileges = is_array($admin_privileges)?$admin_privileges:array();
			}
		}
	}
	return $admin_privileges;
}

//检查管理员是否登录
function ck_adminlogin() {
	if(!isadmin()) {
		showmessage('你需要登录后才能进行本次操作。',SITE_URL.'/admincp/login', 0);
	} else {
		$RTR =& load_class('Router');
		$class  = strtolower($RTR->fetch_class());
		$method = strtolower($RTR->fetch_method());
		
		//载入配置
		include APPPATH."config/adminmenu.php";
		$all_privileges = get_all_privileges($ADMIN_MENU);
		$admin_privileges = get_admin_privileges($all_privileges);
		
		
		if(check_privileges($all_privileges, $class, $method)){
			if(!check_privileges($admin_privileges, $class, $method)){
				
				$flag = empty($admin_privileges) ? TRUE : FALSE;
				
				if($ADMIN_MENU[$class]['method'][$method]['isajax']){
					showmessage(ICON_ERROR.'对不起，您没有权限进行此操作。', 0);
				}
				if(!$ADMIN_MENU[$class]['method'][$method]['status'] && !INAJAX){
					$flag = true;
				}
				
				if(!$flag){
					$jumpurl = '';
					$admin_flag = isset($admin_privileges[$class])? TRUE : FALSE;
					if($admin_flag){
						$admin_privileges = array($class=>$admin_privileges[$class]);
					}
					foreach ($admin_privileges as $key=>$val){
						foreach ($val as $vars){
							if($ADMIN_MENU[$key]['method'][$vars]['status']){
								$jumpurl .= "/$key/{$vars}";
								break 2;
							}
						}
					}
					if($jumpurl){
						showmessage('跳转到有权限访问的页面', SITE_URL.$jumpurl, 0);
					} else {
						$flag = TRUE;
					}				
				}
				
				if($flag){
					$msgTilte = '对不起，您没有权限进行此操作。';
					$msgTodo = '请确定您是否有足够的权限访问此页面。';
					$msgBody = array();
					showinformation($msgTilte,$msgTodo,$msgBody);
				}
			}
		}
		
		get_admin_menu($ADMIN_MENU, $admin_privileges, $class, $method);
		return $admin_privileges;
	}
}

function get_admin_menu($ADMIN_MENU, $admin_privileges, $class, $method){
	$menu_class = $menu_method = '';
	foreach ($ADMIN_MENU as $key=>$val){
		$class_arr = isset($admin_privileges[$key]) ? $val : array();
		
		$menu_method_num = 0;
		
		if(!empty($class_arr)){
			$menu_class_url = SITE_URL."/$key";
			foreach ($class_arr['method'] as $m=>$v){
				if(in_array($m, $admin_privileges[$key]) && $v['status']){
					if($class == $key){
						$menu_method_current = ($method == $m) ? 'nav_sub_now' : 'nav_sub';
						$menu_method_url = $menu_class_url."/$m";
						$menu_method .= "<div class=\"{$menu_method_current}\"><a href=\"{$menu_method_url}\">{$v['menu']}</a></div>\r\n";
					}
					$menu_method_num++;
				}
			}
		}
		if($menu_method_num && $val['status']){
			$menu_class_current = ($class == $key) ? ' class="current"' : '';
			$menu_class .= "<li {$menu_class_current}><a href=\"{$menu_class_url}\">{$val['menu']}</a></li>\r\n";
		}
		
	}
	
	$adminmenu = array(
		'menu_class' => $menu_class,
		'menu_method' => $menu_method,
	);
	$CI =& get_instance();
	$CI->tpl->assign('adminmenu', $adminmenu);
	return $adminmenu;
}

function check_privileges($privileges, $class, $method){
	$class_arr = isset($privileges[$class]) ? $privileges[$class] : array();
	$result = FALSE;
	if($class_arr){
		if(in_array($method, $class_arr)){
			$result = TRUE;
		}
	}
	return $result;
}

//是否是管理员
function isadmin(){
	if(empty($_SESSION['site_adminid']) || empty($_SESSION['site_adminname'])){
		return FALSE;
	} else {
		return TRUE;
	}
}

//判断字符串是否存在
function strexists($haystack, $needle) {
	return !(stripos($haystack, $needle) === FALSE);
}

//判断一个字符串在另一段字符中是否存在
//判断源字串的长度，然后用需要查找的字串去替换源字串，然后判断长度，长度改变即表示存在

function strisin($str,$string){
    $a = strlen($string);

    $l = strlen(str_ireplace($str, "", $string));

    if($l < $a){
    	return TRUE;
    } else {
    	return FALSE;
    }
}

function islogin(){
	$CI =& get_instance();
	return $CI->user->uid > 0 ? TRUE : FALSE;
}

//获取表名
function tname($tablename, $databasename = DB_CORE) {
	$pre = 'DB_'.strtoupper($databasename).'_PREFIX';
	$db_prefix = defined($pre) ? constant($pre) : DB_CORE_PREFIX;
	$databasename = $databasename == DB_CORE ? '' : $databasename.'.';
	return $databasename.$db_prefix.$tablename;
}


//添加数据
function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false) {
	$CI =& get_instance();

	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	$method = $replace?'REPLACE':'INSERT';
	$query = $CI->db->query($method.' INTO '.tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')');
	if($returnid && !$replace) {
		return $CI->db->insert_id();
	}
	return $query;
}


//更新数据
function updatetable($tablename, $setsqlarr, $wheresqlarr) {
	$CI =& get_instance();

	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {
		$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	return $CI->db->query('UPDATE '.tname($tablename).' SET '.$setsql.' WHERE '.$where);
}

/**
 *
 * @desc 获取表的下一个唯一标识符
 * @param $tablename
 * @param $idname
 */
function getUniqueId($tablename, $idname) {
	$CI =& get_instance();

	$query = $CI->db->query("SELECT MAX($idname) AS $idname FROM ".tname($tablename));
	if($query){
		$result = $query->row();
		if (empty($result)) {
			$uniqueId = 0;
		} else {
			$uniqueId = $result->$idname;
		}
		return ++$uniqueId;
	} else {
		return FALSE;
	}
}

/**
 * @desc 执行MySQL的UUID行数得到一个全局标识符
 * @param $db 数据库操作句柄
 * @param $flag 是否需要把生成的UUID中间的'-'去掉
 * @return unknown_type
 */
function get_uuid($flag = TRUE){
	$CI =& get_instance();
	
	$query = $CI->db->query("SELECT uuid() AS uuid");
	$result = FALSE;

	if($query){
		$row = $query->first_row();
		if (!empty($row->uuid)) {
			$result = $row->uuid;
			if($flag){
				$result = str_replace('-', '', $row->uuid);
			}
		}
	}
	return $result;
}

function ajax_header() {
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
}



/**
 * @desc 检查是否是合法的手机号格式，现阶段合法的格式：以13,15,18开头的11位数字
 * @param $cellphone
 */
function iscellphone($cellphone) {
	$pattern = "/^(13|15|18){1}\d{9}$/";
	return str_match($pattern, $cellphone);
}
//检查是否是合法的固定电话
function istelphone($telphone) {
	$pattern = "/^(0){1}[0-9]{2,3}\-\d{7,8}(\-\d{1,6})?$/";
	return str_match($pattern, $telphone);
}

function is_confirmation($confirmation) {
	$pattern = "/^[a-z\d]{32}$/i";
	return str_match($pattern, $confirmation);
}

function is_webid($webid) {
	$pattern = "/^[a-z\d]{1,32}$/i";
	return str_match($pattern, $webid);
}

function is_seourl($seourl) {
	$pattern = "/^[a-z\d\-\_]{1,100}$/i";
	return str_match($pattern, $seourl);
}

function str_match($pattern, $str){
	if(!empty($str)){
		if(preg_match($pattern, $str)) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
* 执行sql语句，只得到一条记录
*/
function GetRow($sql, &$db = null){
	if(!$db){
		$CI =& get_instance();
		$db = $CI->db;
	}
	$query = $db->query($sql.' LIMIT 1');
	if($query){
	    return $query->row_array();
	}
    return FALSE;
}

/**
* 取得一个某个字段的值
*/
function GetOne($sql, &$db = null){

	if($row = GetRow($sql, $db)){
		$row = array_values($row);
		return $row[0];
	}
    return FALSE;
}

//获取文件后缀名
function fileext($filename) {
	return trim(substr(strrchr($filename, '.'), 1, 10));
}

//utf-8转unicode
function utf8_to_unicode($str) {
	$unicode = array();
	$values = array();
	$lookingFor = 1;
	for ($i = 0; $i < strlen( $str ); $i++ ) {
		$thisValue = ord( $str[ $i ] );

        if ( $thisValue < ord('A') ) {
            // exclude 0-9
            if ($thisValue >= ord('0') && $thisValue <= ord('9')) {
                 // number
                 $unicode[] = chr($thisValue);
            } else {
                 $unicode[] = '\\'.dechex($thisValue);
            }
        } else {

        	if ( $thisValue < 128) {
        		$unicode[] = $str[ $i ];
        	} else {
                    if(count( $values ) == 0) {
                    $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                    }

                    $values[] = $thisValue;

                    if ( count( $values ) == $lookingFor ) {
                    $number = ( $lookingFor == 3 ) ?
                            ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                            ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                        $number = dechex($number);
                        $unicode[] = (strlen($number)==3)?"\u0".$number:"\u".$number;
                        $values = array();
                        $lookingFor = 1;
              	}
            }
        }
	}
	return implode("",$unicode);
}



function imagecreatefrombmp( $filename ){

    if ( ! $f1 = fopen ( $filename , "rb" )) return FALSE ;
    $FILE = unpack ( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset" , fread ( $f1 , 14 ));
    if ( $FILE [ 'file_type' ] != 19778 ) return FALSE ;
    $BMP = unpack ( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
    '/Vvert_resolution/Vcolors_used/Vcolors_important' , fread ( $f1 , 40 ));
    $BMP [ 'colors' ] = pow ( 2 , $BMP [ 'bits_per_pixel' ]);
    if ( $BMP [ 'size_bitmap' ] == 0 ) $BMP [ 'size_bitmap' ] = $FILE [ 'file_size' ] - $FILE [ 'bitmap_offset' ];
    $BMP [ 'bytes_per_pixel' ] = $BMP [ 'bits_per_pixel' ] / 8 ;
    $BMP [ 'bytes_per_pixel2' ] = ceil ( $BMP [ 'bytes_per_pixel' ]);
    $BMP [ 'decal' ] = ( $BMP [ 'width' ] * $BMP [ 'bytes_per_pixel' ] / 4 );
    $BMP [ 'decal' ] -= floor ( $BMP [ 'width' ] * $BMP [ 'bytes_per_pixel' ] / 4 );
    $BMP [ 'decal' ] = 4 - ( 4 * $BMP [ 'decal' ]);
    if ( $BMP [ 'decal' ] == 4 ) $BMP [ 'decal' ] = 0 ;
    $PALETTE = array ();
    if ( $BMP [ 'colors' ] < 16777216 ) {
    	$PALETTE = unpack ( 'V' . $BMP [ 'colors' ] , fread ( $f1 , $BMP [ 'colors' ] * 4 ));
    }
    $IMG = fread ( $f1 , $BMP [ 'size_bitmap' ]);
    $VIDE = chr ( 0 );
    $res = imagecreatetruecolor( $BMP [ 'width' ] , $BMP [ 'height' ]);
    $P = 0 ;
    $Y = $BMP [ 'height' ] - 1 ;
    while ( $Y >= 0 ) {
	    $X = 0 ;
	    while ( $X < $BMP [ 'width' ])
	    {
		    if ( $BMP [ 'bits_per_pixel' ] == 24 ){
		    	$COLOR = unpack ( "V" , substr ( $IMG , $P , 3 ) . $VIDE );
		    } elseif ( $BMP [ 'bits_per_pixel' ] == 16 ) {
			    $COLOR = unpack ( "n" , substr ( $IMG , $P , 2 ));
			    $COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ];
		    } elseif ( $BMP [ 'bits_per_pixel' ] == 8 ) {
			    $COLOR = unpack ( "n" , $VIDE . substr ( $IMG , $P , 1 ));
			    $COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ];
		    } elseif ( $BMP [ 'bits_per_pixel' ] == 4 ) {
			    $COLOR = unpack ( "n" , $VIDE . substr ( $IMG , floor ( $P ) , 1 ));
			    if (( $P * 2 ) % 2 == 0 ) {
			    	$COLOR [ 1 ] = ( $COLOR [ 1 ] >> 4 );
			    } else {
			    	$COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x0F );
			    }
			    $COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ];
		    } elseif ( $BMP [ 'bits_per_pixel' ] == 1 ) {
		    	$COLOR = unpack ( "n" , $VIDE . substr ( $IMG , floor ( $P ) , 1 ));
		    	if (( $P * 8 ) % 8 == 0 ) $COLOR [ 1 ] = $COLOR [ 1 ] >> 7 ;
		    	elseif (( $P * 8 ) % 8 == 1 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x40 ) >> 6 ;
			    elseif (( $P * 8 ) % 8 == 2 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x20 ) >> 5 ;
			    elseif (( $P * 8 ) % 8 == 3 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x10 ) >> 4 ;
			    elseif (( $P * 8 ) % 8 == 4 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x8 ) >> 3 ;
			    elseif (( $P * 8 ) % 8 == 5 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x4 ) >> 2 ;
			    elseif (( $P * 8 ) % 8 == 6 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x2 ) >> 1 ;
			    elseif (( $P * 8 ) % 8 == 7 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x1 );
		    	$COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ];
		    } else {
		    	return FALSE ;
		    }

		    imagesetpixel( $res , $X , $Y , $COLOR [ 1 ]);
		    $X ++ ;
		    $P += $BMP [ 'bytes_per_pixel' ];
    	}
	    $Y -- ;
	    $P += $BMP [ 'decal' ];
    }
    fclose ( $f1 );
    return $res ;
}


//获取字符串
function getstr($string, $length, $in_slashes=0, $out_slashes=0, $html=0) {

	$string = trim($string);

	if($in_slashes) {
		//传入的字符有slashes
		$string = sstripslashes($string);
	}
	if($html < 0) {
		//去掉html标签
		$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
		$string = shtmlspecialchars($string);
	} elseif ($html == 0) {
		//转换html标签
		$string = shtmlspecialchars($string);
	}

	if($length && strlen($string) > $length) {
		//截断字符
		$wordscut = '';
		$CI =& get_instance();
		if(strtolower($CI->config->item('charset')) == 'utf-8') {
			//utf8编码
			$n = 0;
			$tn = 0;
			$noc = 0;
			while ($n < strlen($string)) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n++;
				}
				if ($noc >= $length) {
					break;
				}
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			$wordscut = substr($string, 0, $n);
		} else {
			for($i = 0; $i < $length - 1; $i++) {
				if(ord($string[$i]) > 127) {
					$wordscut .= $string[$i].$string[$i + 1];
					$i++;
				} else {
					$wordscut .= $string[$i];
				}
			}
		}
		$string = $wordscut;
	}

	if($out_slashes) {
		$string = saddslashes($string);
	}
	return trim($string);
}

function cutstr($string, $length, $dot = ' ...') {

	$CI =& get_instance();
	
	if(strlen($string) <= $length) {
		return $string;
	}

	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

	$strcut = '';
	if(strtolower($CI->config->item('charset')) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.$dot;
}

//SQL ADDSLASHES
function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}


//时间格式化
function sgmdate($dateformat, $timestamp='', $format=0) {

	$CI =& get_instance();

	if(empty($timestamp)) {
		$timestamp = TIME_STAMP;
	}

	$timeoffset = intval($CI->config->item('timeoffset'));
	$result = '';
	if($format) {
		$time = TIME_STAMP - $timestamp;

		if($time > 24*3600) {
			$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
		} elseif ($time > 3600) {
			$result = intval($time/3600).'小时前';
		} elseif ($time > 60) {
			$result = intval($time/60).'分钟前';
		} elseif ($time > 0) {
			$result = $time.'秒前';
		} else {
			$result = '现在';
		}
	} else {
		$result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
	}
	return $result;
}

//字符串时间化
function sstrtotime($string) {
	$CI =& get_instance();
	$time = '';
	if($string) {
		$time = strtotime($string);
		if(gmdate('H:i', TIME_STAMP + $CI->config->item('timeoffset') * 3600) != date('H:i', TIME_STAMP)) {
			$time = $time - $CI->config->item('timeoffset') * 3600;
		}
	}
	return $time;
}

/**
 * @desc 全角字符与成半角字符的相互转换
 * @param $string
 * @param $flag 0=全角到半角,1=半角到全角
 */
function sbc2dbc($string, $flag = 0) {
	//全角
	$SBC = array(
		'０' , '１' , '２' , '３' , '４' ,

		'５' , '６' , '７' , '８' , '９' ,

		'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,

		'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,

		'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,

		'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,

		'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,

		'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,

		'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,

		'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,

		'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,

		'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,

		'ｙ' , 'ｚ' , '－' , '　'  , '：' ,

		'．' , '，' , '／' , '％' , '＃' ,

		'！' , '＠' , '＆' , '（' , '）' ,

		'＜' , '＞' , '＂' , '＇' , '？' ,

		'［' , '］' , '｛' , '｝' , '＼' ,

		'｜' , '＋' , '＝' , '＿' , '＾' ,

		'￥' , '￣' , '｀'

	);
	//半角
	$DBC = array(
		 '0', '1', '2', '3', '4',

		 '5', '6', '7', '8', '9',

		 'A', 'B', 'C', 'D', 'E',

		 'F', 'G', 'H', 'I', 'J',

		 'K', 'L', 'M', 'N', 'O',

		 'P', 'Q', 'R', 'S', 'T',

		 'U', 'V', 'W', 'X', 'Y',

		 'Z', 'a', 'b', 'c', 'd',

		 'e', 'f', 'g', 'h', 'i',

		 'j', 'k', 'l', 'm', 'n',

		 'o', 'p', 'q', 'r', 's',

		 't', 'u', 'v', 'w', 'x',

		 'y', 'z', '-', ' ', ':',

		'.', ',', '/', '%', '#',

		'!', '@', '&', '(', ')',

		'<', '>', '"', '\'','?',

		'[', ']', '{', '}', '\\',

		'|', '+', '=', '_', '^',

		'￥', '~', '`'
	);

	//半角到全角
	if($flag == 1) {
		return str_replace($DBC, $SBC, $string);
	}
	//全角到半角
	else {
		return str_replace($SBC, $DBC, $string);
	}
}

//密码加密算法
function password_encrypt($password, $salt){
	$passwordmd5 = md5($password);
	return md5($passwordmd5.$salt);
}
//生成密码盐
function password_salt(){
	$salt = substr(uniqid(rand()), -6);
	return $salt;
}


//Ajax信息格式化输出
function ajax_output($message, $success = 0){

	if(!is_array($message)){
		$message = array('message' => $message);
	}
	$message['success'] = $success;
	$message['message'] = sstripslashes($message['message']);
	exit(json_encode($message));
}

function get_send_url($urltpl, $title, $url, $charset){
	$CI =& get_instance();
	$site_charset = strtolower($CI->config->item('charset'));
	$media_charset = strtolower($charset);
	if($site_charset != $media_charset){
		$title = mb_convert_encoding(trim($title), $media_charset, $site_charset);
		$url = mb_convert_encoding(trim($url), $media_charset, $site_charset);
	}
	$replace_searchs = array('[$title]','[$url]');
	$replace_replaces = array(urlencode($title), urlencode($url));
	$sendurl = str_replace($replace_searchs, $replace_replaces, $urltpl);
	return $sendurl;
}

function sendmessage($message){
	$CI =& get_instance();
	$CI->tpl->assign('message', $message);
	exit(tpl_fetch('send', 'media'));
}

function convToUtf8($str, $encodearr=array('CP936','UTF-8')) {
	$encode = mb_detect_encoding($str, array('ASCII','GB2312','GBK','UTF-8'));
	if(!in_array($encode, $encodearr)) {
		return mb_convert_encoding($str, 'UTF-8', $encode);
	} else{
		return $str;
	}
}

function get_alexa($domain){
	$alexa_interface = "http://data.alexa.com/data/+wQ411en8000lA?cli=10&dat=snba&ver=7.0&cdt=alx_vw=20&wid=12206&act=00000000000&ss=1680×1050&bw=964&t=0&ttl=35371&vis=1&rq=4&url=$domain";
	
//	* ezdy01DOo100QI是aid
//  * “cli=10&dat=snba&ver=7.0&cdt=alx_vw=20&”这部分是固定值
//  * wid是个随机数
//  * act数据包含了Alexa Toobar功能的被使用情况
//  * ss很明显是屏幕分辨率了
//  * bw是IE窗口的宽度
//  * t取值是0或1，和当前IE的window对象还有referrer有关
//  * ttl是当前页面打开速度，和Site Stats中的Speed有关
//  * vis表明IE是否显示工具条
//  * rq是对象计数器

	$result = jfopen($alexa_interface);
	
	$rankarr = $emailarr = $phonearr = $ownerarr = array();
	
	if($result){
		preg_match_all("/\<POPULARITY URL=\"(.*?)\" TEXT=\"(.*?)\"\/\>/is", $result, $rankarr);
		preg_match_all("/\<EMAIL ADDR=\"(.*?)\"\/\>/is", $result, $emailarr);
		preg_match_all("/\<PHONE NUMBER=\"(.*?)\"\/\>/is", $result, $phonearr);
		preg_match_all("/\<OWNER NAME=\"(.*?)\"\/\>/is", $result, $ownerarr);
	}
	
	$alexa = array();
	
	$alexa['alexarank'] = empty($rankarr[2][0]) ? 0 : intval($rankarr[2][0]);
	$alexa['email'] = empty($emailarr[1][0]) ? '' : isemail($emailarr[1][0]) ? $emailarr[1][0] : '';
	$alexa['telphone'] = empty($phonearr[1][0]) ? '' : $phonearr[1][0];
	$alexa['siteowner'] = empty($ownerarr[1][0]) ? '' : $ownerarr[1][0];
	
	return $alexa;
}

function jfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 1, $block = TRUE) {
	
	//$post参数实例：$post = sprintf('action=%s&url=%s', 's', rawurlencode($url));
	
	$return = '';
	$matches = parse_url($url);
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;

	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: '.strlen($post)."\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	}
	$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {
			while (!feof($fp)) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}

			$stop = false;
			while(!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
		@fclose($fp);
		return $return;
	}
}

function get_site($domain){
	$url = "http://www.$domain";
	$lines_array = @file($url); 
	
	$titlearr = $metaarr = array();
	
	if($lines_array){
		$lines_string = implode('', $lines_array);
		preg_match('/\<title\>(.*)\<\/title\>/i', $lines_string, $titlearr);
		
		preg_match_all('/<[\s]*meta[\s]*name="?'.'([^>"]*)"?[\s]*'.'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $lines_string, $metaarr);
		
	}
	$keywords = $description = '';
	if(is_array($metaarr[1]) && !empty($metaarr[1])){
		foreach ($metaarr[1] as $key=>$val){
			if($val == 'keywords'){
				$keywords = $metaarr[2][$key];
			} else if($val == 'description'){
				$description = $metaarr[2][$key];
			}
		}
	}
	$siteinfo = array();
	$siteinfo['title'] = empty($titlearr[1]) ? '' : convToUtf8($titlearr[1]);
	$siteinfo['keywords'] = empty($keywords) ? '' : convToUtf8($keywords);
	$siteinfo['description'] = empty($description) ? '' : convToUtf8($description);
	
	return $siteinfo;
}

function sendmail($toemail, $subject, $message, $from = '', $mailusername = 1, $maildelimiter = "\n"){
	$CI =& get_instance();
	$charset = strtolower($CI->config->item('charset'));
	
	$email_from = $from == '' ? '=?'.$charset.'?B?'.base64_encode($CI->config->item('site_name'))."?= <'.$CI->config->item('emailsender').'>" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?'.$charset.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
	$email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.$charset.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;
	
	$email_subject = '=?'.$charset.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', $subject)).'?=';
	$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));

	$host = empty($_SERVER['HTTP_HOST'])?'':$_SERVER['HTTP_HOST'];
	
	$headers = "From: $email_from{$maildelimiter}";
	$headers .= "X-Priority: 3{$maildelimiter}";
	$headers .= "X-Mailer: $host {$maildelimiter}";
	$headers .= "MIME-Version: 1.0{$maildelimiter}";
	$headers .= "Content-type: text/html; charset=".$charset."{$maildelimiter}";
	$headers .= "Content-Transfer-Encoding: base64{$maildelimiter}";
	
	$result = @mail($email_to, $email_subject, $email_message, $headers);	
	return $result;
}

//二维数组排序
//示例：$services = array_msort($services, array('total_shares'=>SORT_DESC));
//$rekey(0 or 1) 返回结果是否需要重新索引数组下标
function array_msort($array, $cols, $rekey = 0){
	//http://cn.php.net/array_multisort
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
    	$i = 0;
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            $key = $rekey ? $i++ : $k;
            if (!isset($ret[$key])) $ret[$key] = $array[$k];
            $ret[$key][$col] = $array[$k][$col];
        }
    }
    return $ret;
}

function get_all_privileges($ADMIN_MENU){
	$admin_privileges = array();
	foreach ($ADMIN_MENU as $key=>$val){
		$row = array();
		foreach($val['method'] as $class=>$method){
			$row[] = $class;
		}
		$admin_privileges[$key] = $row;
	}
	return $admin_privileges;
}

function is_sql_in_tables($sql, $tables, $database=DB_CORE){
	$result = FALSE;
	$pattern = '/.*(from|update|into)[\s|\r\n|\n|\t]+(.*?)[\s|\r\n|\n|\t]/is';
 	preg_match($pattern, $sql, $sqls);
	if(!empty($sqls[2])){
		$table = $sqls[2];
		if(strexists($table, '.')){
			$table = str_ireplace($database.'.', '', $table);
		}
		if(in_array($table, $tables)){
			$result = TRUE;
		}
	}
	return $result;
}

function phpAlert($message, $url='') {
	echo '<script type="text/javascript">alert("'.$message.'");</script>';
	if(!empty($url)) echo '<script type="text/javascript">window.parent.location.href="'.$url.'";</script>';
	exit;
}
function hideLocation($url, $message = '') {
	if ($message != '') echo '<script type="text/javascript">alert("'.$message.'");</script>';
	echo '<script type="text/javascript">parent.window.location="'.$url.'";</script>';
	exit;
}

function output_js($js){
	@header("Content-type:text/javascript; charset=utf-8");
	echo $js;
	exit;
}

//检查验证码
function checkseccode($seccode){
	//在这里补全验证码验证逻辑
	return true;
}

function addAdminLog($description){
	$description = str_replace('{me}', $_SESSION['site_adminname'], $description);
	$setarr = array(
		'adminid' => intval($_SESSION['site_adminid']),
		'addtime' => SITE_TIME,
		'description' => $description
	);
	return inserttable('admin_logs', $setarr);
}
