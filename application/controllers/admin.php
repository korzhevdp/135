<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid'))    ? $this->session->set_userdata('uid', 1) : "";
		$this->load->model('adminmodel');
		$this->load->model('usefulmodel');
		$this->load->model('licensemodel');
		//if($this->session->userdata("admin_id") == 1){
			//$this->output->enable_profiler(TRUE);
		//}
	}

	public function index($user_id=0, $page=1){
		$data = array('tickets' => 0);
		
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->adminmodel->startscreen_show(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->load->view('page_container', $act);
	}



	public function applyitem($id = 0){
		$result = $this->db->query("UPDATE
		`resources_items`
		SET
		`resources_items`.apply = 1,
		`resources_items`.applydate = NOW(),
		`resources_items`.applyer = ?
		WHERE
		`resources_items`.id = ?", array($this->session->userdata('base_id'), $id));
		//$this->load->view('page_container', $act);
		$this->usefulmodel->insert_audit("Помечена исполненной заявка #".$id." на доступ к информационному ресурсу. Исполнитель: #".$this->session->userdata("admin_id")." (b#".$this->session->userdata('base_id'));
		$this->load->helper('url');
		redirect("");
	}

	public function users($user_id=0, $page=1){
		$act = array();
		$user = $this->adminmodel->userdata_get($user_id, $page);
		$user['filter'] = (!$user_id) 
			? iconv('UTF-8', 'Windows-1251' , urldecode($this->session->userdata("filter"))) 
			: implode(array($user['name_f'], $user['name_i'], $user['name_o']), " ");
		$user['resources'] = $this->adminmodel->user_resources_get($user['id']);
		$user['userid'] = $user['id'];
		$user['arm'] = $this->adminmodel->user_arm_get($user['id']);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('user_details', $user, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function usave(){
		$this->adminmodel->user_save();
	}

	public function quickadd(){
		$this->adminmodel->quick_add();
	}

	public function aclgen(){
		$this->usefulmodel->aclgen();
	}

	public function takeuser($user, $newcurator){
		$this->usefulmodel->insert_audit("куратор #".$this->session->userdata('user_name').") забрал пользователя с id #".$user. " в кураторство.");
		$this->adminmodel->takeuser($user, $newcurator);
	}

	public function blockpc(){
		$invnum = $this->input->post("invnum");
		$user   = $this->input->post("user");
		$mode   = ($this->input->post("block") == "block") ? 0 : 1;
		$this->adminmodel->blockpc($invnum, $mode);
		$this->usefulmodel->insert_audit("У пользователя #".$user. " переключён статус компонента АРМ РС #".$invnum." в ".$mode."(".$this->input->post("block").")");
		$this->load->helper("url");
		redirect("admin/users/".$user."/4");
	}

	######## AJAX-секция
	public function apply_filter($filter = ""){
		$this->session->set_userdata('filter', $filter);
		$this->usefulmodel->filter_users($filter);
	}

	public function ressubmit(){
		$id    = $this->input->post('id');
		$num   = iconv('UTF-8', 'Windows-1251' , urldecode($this->input->post('num')));
		$date  = $this->input->post('date');
		$ip    = $this->input->post('ip');
		$email = $this->input->post('email');
		$date  = (!$date) ? date("Y-m-d") : implode(array_reverse(explode('.', $date)), "-");
		$this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.ok = 1,
			resources_items.exp = 0,
			resources_items.okdate = NOW()
			WHERE
			resources_items.id = ?", array($id));
		$this->db->query("UPDATE 
			resources_orders
			SET 
			resources_orders.docnum = ?,
			resources_orders.docdate = ?
			WHERE
			resources_orders.id = (SELECT resources_items.order_id FROM resources_items WHERE resources_items.id = ?)", array(
				$num,
				$date,
				$id
			));
		if($ip || $email){
			$ipc=explode(".",$ip);
			$ipappend = (sizeof($ipc) !== 2 && $ipc[0] == "192" && $ipc[1] == "168") ? $ipc[0].".".$ipc[1] : "192.168" ;
			$ipflex = implode(array_splice($ipc, ((sizeof($ipc) == 2) ? 0 : 2)), ".");
			if(!$email){
				//print 111;
				$this->db->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($id));
				$this->db->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $id));
				$this->aclgen();
			}else{
				$this->db->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($id));
				$this->db->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $id));
				$this->db->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,?,?)", array(1, $email, $id));
			}
		}
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") выполнил заявку #".$id);
	}

	public function resexpire($id=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.ok = 0,
			resources_items.exp = 1,
			resources_items.expdate = NOW()
			WHERE
			resources_items.id = ?", array($id));
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") отменил заявку #".$id);
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function resexpiredandapplied($id=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.ok        = 0,
			resources_items.exp       = 1,
			resources_items.expdate   = NOW(),
			resources_items.apply     = 1,
			resources_items.applydate = NOW()
			WHERE
			resources_items.id = ?", array($id));
		$this->usefulmodel->insert_audit("Администратор #".$this->session->userdata('user_name').") отменил заявку #".$id. " как повторную.");
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function resdelete($id=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.del = 1,
			resources_items.deldate = NOW()
			WHERE
			resources_items.id = ?", array($id));
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") удалил заявку #".$id);
		print $out;
	}

	public function reshookup($id=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.apply     = ?,
			resources_items.applydate = NOW()
			WHERE
			resources_items.id = ?", array($this->session->userdata('admin_id'), $id));
		$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." выполнил заявку #".$id);
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function usermerge($t_id=0, $rest_id=0){
		$rest_id = implode(explode("_", $rest_id), ",");
		$result = $this->db->query("UPDATE resources_items SET resources_items.uid = ? WHERE resources_items.uid IN (".$rest_id.")", array($t_id));
		$result = $this->db->query("DELETE FROM users WHERE users.id IN (".$rest_id.")");
		$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." объединил учётные записи #".$t_id." и ".$rest_id);
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function roomsget($id=0){
		$out = array('<option value=0>Выберите помещение</option>');
		$result = $this->db->query("SELECT 
		locations.`id`,
		locations.`address`,
		CASE 
			WHEN ASCII(RIGHT(locations.`address`, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(locations.`address`, '-'), 16, '0')
			ELSE LPAD(locations.`address`, 16, '0') END AS `vsort`
		FROM `locations`
		WHERE `locations`.parent = ?
		ORDER BY `vsort`", array($id));
		if(sizeof($result->num_rows())){
			foreach($result->result() as $row){
				$string = "<option value=".$row->id.">".$row->address."</option>";
				array_push($out,$string);
			}
		}
		print implode($out,"\n");
	}

	public function setfired($id=0){
		$result = $this->db->query("UPDATE 
		users 
		SET
		users.fired = 1 
		WHERE users.id = ?", array(trim($id)));
		if ($this->db->affected_rows()) {
			$result = $this->db->query("UPDATE
			`arm`
			SET
			active  = 0,
			out_ts  = NOW()
			WHERE
			arm.uid = ?", array(trim($id)));
			if($this->db->affected_rows()){
				print 1;
			}else{
				print 0;
			}
		}
	}

	public function switchfired($id=0){
		// getting state
		$result = $this->db->query("SELECT `users`.fired AS state FROM `users` WHERE users.id = ?", array($id));
		if($result->num_rows()){
			$row = $result->row();
			if($row->state){
				//уволен если
				$this->db->query("UPDATE 
				users
				SET
				users.fired = 0 
				WHERE users.id = ?", array(trim($id)));
				//print "unfired set\n<br>";
				$this->db->query("UPDATE
				`arm`
				SET
				active  = 1,
				out_ts  = '0000-00-00'
				WHERE
				arm.uid = ?", array(trim($id)));
				//print "arm active\n<br>";
			}else{
				//не уволен если
				$this->db->query("UPDATE
				users
				SET
				users.fired = 1
				WHERE users.id = ?", array(trim($id)));
				//print "fired set\n<br>";
				$this->db->query("UPDATE
				`arm`
				SET
				active  = 0,
				out_ts  = NOW()
				WHERE
				arm.uid = ?", array(trim($id)));
				//print "arm inactive\n<br>";
			}
		}
		//$this->aclgen();
	}
	
	public function switchsman($id=0){
		if($id){
			$this->db->query("UPDATE users SET users.sman = IF(users.sman = 0,1,0) WHERE users.id = ?", array($id));
			$result = $this->db->query("SELECT users.sman, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($id));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." ".(($row->sman) ? "выдал" : "отменил")." статус куратора пользователю сети #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$id."/3");
	}

	public function switchair($id=0){
		if($id){
			$this->db->query("UPDATE users SET users.air = IF(users.air = 0,1,0) WHERE users.id = ?", array($id));
			$result = $this->db->query("SELECT users.air, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($id));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." ".(($row->air) ? "выдал" : "отменил")." статус администратора информационных ресурсов пользователю сети #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$id."/3");
	}

	public function switchbir($id=0){
		if($id){
			$this->db->query("UPDATE users SET users.bir = IF(users.bir = 0,1,0) WHERE users.id = ?", array($id));
			$result = $this->db->query("SELECT users.bir, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($id));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." ".(($row->bir) ? "выдал" : "отменил")." статус администратора безопасности информационных ресурсов пользователю сети #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$id."/3");
	}

	public function invnumupdate($id=0, $number=0){
		$result = $this->db->query("UPDATE 
		hash_items
		SET
		`hash_items`.`inv_number` = ?
		WHERE `hash_items`.`id` = ?", array(trim($number),trim($id)));
		if($this->db->affected_rows()) {
			print 1;
		}else{
			print 0;
		}
	}

	public function stuck($id=0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->adminmodel->stuck_orders(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function phpinfo(){
		phpinfo();
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */