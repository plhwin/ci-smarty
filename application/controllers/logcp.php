<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logcp extends CI_Controller {


	public function __construct(){
		parent::__construct();
		$this->myaccess = ck_adminlogin();
		set_module_title('系统日志');
	}
	
	public function index(){
		$GET = newhtmlspecialchars($_GET);
		$startdate = empty($GET['startdate']) ? '' : $GET['startdate'];
		$enddate = empty($GET['enddate']) ? '' : $GET['enddate'];
		$adminid = empty($GET['adminid']) ? '' : intval($GET['adminid']);
		
		$wheresql = "1";
		if(!empty($startdate)) $wheresql .= " AND addtime>='".strtotime($startdate.' 00:00:00')."'";
		if(!empty($enddate)) $wheresql .= " AND addtime<='".strtotime($enddate.' 23:59:59')."'";
		if(!empty($adminid)) $wheresql .= " AND adminid='{$adminid}'";

		
		$this->load->library('page');
		$this->page->pernum = 5;//页码偏移量
		$pagesize = 20;//每页显示数量
		$total = GetOne("SELECT COUNT(logid) FROM ".tname('admin_logs')." WHERE $wheresql");
		$total = $total?intval($total):0;
		$page = $this->page->getpage($total, $pagesize);
		
		$sql = "SELECT *  
			FROM ".tname('admin_logs')."
			WHERE $wheresql 
			ORDER BY logid DESC  
			LIMIT {$this->page->limit}";
		
		
		$query = $this->db->query($sql);
		$list = array();
		
		if($query){
			foreach ($query->result_array() as $row){
				$adminer = GetRow("SELECT adminname,realname FROM ".tname('admin_user')." WHERE adminid = {$row['adminid']}");
				if($adminer){
					$row['adminname'] = $adminer['adminname'];
					$row['realname'] = $adminer['realname'];
				}
				$row['adddate'] = sgmdate('Y-m-d', $row['addtime'], 1);
				$list[] = $row;
			}
		}
		
		$search = array(
			'adminid' => $adminid,
			'startdate' => $startdate,
			'enddate' => $enddate
		);
		
		
		$vars = array(
			'list' => $list,
			'page' => $page,
			'search' => $search
		);
		$this->tpl->assign($vars);
		set_page_title('日志记录');
		$this->tpl->display();
	}
}