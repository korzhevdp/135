<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		$this->session->set_userdata('pageHeader', '������������ - ������');

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
		$this->session->set_userdata('pageHeader', '�������� ������');
		$data = array('tickets' => 0);
		$this->load->model('startscreenmodel');
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->startscreenmodel->startScreenShow(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->load->view('page_container', $act);
	}

	public function applyitem($itemID = 0){
		$result = $this->db->query("UPDATE
		`resources_items`
		SET
		`resources_items`.apply = 1,
		`resources_items`.applydate = NOW(),
		`resources_items`.applyer = ?
		WHERE
		`resources_items`.id = ?", array($this->session->userdata('base_id'), $itemID));
		//$this->load->view('page_container', $act);
		$this->usefulmodel->insert_audit("�������� ����������� ������ #".$itemID." �� ������ � ��������������� �������. �����������: #".$this->session->userdata("admin_id")." (b#".$this->session->userdata('base_id'));
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
		$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name').") ������ ������������ � id #".$user. " � �����������.");
		$this->adminmodel->takeuser($user, $newcurator);
	}

	public function blockpc(){
		$invnum = $this->input->post("invnum");
		$user   = $this->input->post("user");
		$mode   = ($this->input->post("block") == "block") ? 0 : 1;
		$this->db->query("UPDATE `hash_items` SET `hash_items`.active = ? WHERE `hash_items`.`id` = ?", array($mode, $invnum));
		$this->usefulmodel->insert_audit("� ������������ #".$user. " ���������� ������ ���������� ��� �� #".$invnum." � ".$mode."(".$this->input->post("block").")");
		$this->load->helper("url");
		redirect("admin/users/".$user."/4");
	}

	public function audit($itemID=0){
		$this->load->view('audit', $this->adminmodel->getAuditData($itemID));
	}

	######## AJAX-������
	public function apply_filter($filter = ""){
		$this->session->set_userdata('filter', $filter);
		$this->usefulmodel->filter_users($filter);
	}

	public function ressubmit(){
		$itemID      = $this->input->post('id');
		$num     = iconv('UTF-8', 'Windows-1251' , urldecode($this->input->post('num')));
		$date    = $this->input->post('date');
		$ipAddr  = $this->input->post('ip');
		$email   = $this->input->post('email');
		$date    = (!$date) ? date("Y-m-d") : implode(array_reverse(explode('.', $date)), "-");
		$res_id  = 0;
		$user_id = 0;
		$DB1     = $this->load->database('35', TRUE);

		$result = $DB1->query("SELECT
		`resources_items`.rid,
		`resources_items`.uid
		FROM
		`resources_items`
		WHERE `resources_items`.`id` = ?", array($itemID));
		if ($result->num_rows()) {
			$row = $result->row(0);
			$res_id  = $row->rid;
			$user_id = $row->uid;
		}

		$DB1->query("UPDATE
		resources_items 
		SET 
		resources_items.ok = 1,
		resources_items.exp = 0,
		resources_items.okdate = NOW()
		WHERE
		resources_items.id = ?", array($itemID));

		$DB1->query("UPDATE
		resources_orders
		SET 
		resources_orders.docnum = ?,
		resources_orders.docdate = ?
		WHERE
		resources_orders.id = (SELECT resources_items.order_id FROM resources_items WHERE resources_items.id = ?)", array(
			$num,
			$date,
			$itemID
		));

		if ($ipAddr || $email) {
			$ipc      = explode(".",$ipAddr);
			$ipappend = (sizeof($ipc) !== 2 && $ipc[0] == "192" && $ipc[1] == "168") ? $ipc[0].".".$ipc[1] : "192.168" ;
			$ipflex   = implode(array_splice($ipc, ((sizeof($ipc) == 2) ? 0 : 2)), ".");
			if (!$email) {
				//print 111;
				$DB1->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($itemID));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $itemID));
				$this->aclgen();
			} else {
				$DB1->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($itemID));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $itemID));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,?,?)", array(1, $email, $itemID));
			}
		}

		if ($res_id == 13) {
			$this->insertWebServerAccount($user_id);
		}

		$this->usefulmodel->insert_audit("����� �������� ����������������� (������������� #".$this->session->userdata('user_name').") �������� ������ #".$itemID);
	}

	private function insertWebServerAccount($user_id) {
		$DB1    = $this->load->database('35', TRUE);
		$DB2    = $this->load->database('web', TRUE);
		$result = $DB1->query("SELECT
		`departments`.dn,
		`users`.phone,
		CONCAT_WS(' ', locations1.address, `locations`.address) AS office,
		CONCAT_WS(' ',`users`.name_f, `users`.name_i, `users`.name_o) AS fio,
		LOWER(`users`.login) AS login
		FROM
		`departments`
		RIGHT OUTER JOIN `users` ON (`departments`.id = `users`.dep_id)
		LEFT OUTER JOIN `locations` ON (`users`.office_id = `locations`.id)
		LEFT OUTER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE `users`.`id` = ?
		LIMIT 1", array($user_id));
		if ($result->num_rows()) {
			//print "web";
			$row       = $result->row();
			$webString = implode(array($row->dn, ", ".$row->office, ", ���.: ".$row->phone), " ");
			$result2   = $DB2->query("SELECT
			`users`.id
			FROM
			`users`
			WHERE LOWER(`users`.`fullname`) = LOWER('".$row->fio."')
			OR LOWER(`users`.`username`) = '".$row->login."'");
			if (!$result2->num_rows()) {
				//print "\nweb insert";
				$DB2->query("INSERT INTO
				users(
					username,
					fullname,
					userdescr
				) VALUES ( ?, ?, ? )", array(
					$row->login,
					ucwords($row->fio),
					$webString,
				));
				$this->usefulmodel->insert_audit("����� �������� ����������������� (������������� #".$this->session->userdata('user_name').") ������� ������� ������ �� web-������� www.arhcity.ru #".$row->login);
				return true;
			}
			$this->usefulmodel->insert_audit("������� ������ �� web-������� www.arhcity.ru #".$row->login." �� ���������. ������������ ����������.");
			return false;
		}
	}

	public function resexpire($itemID=0){
		if ((int)$this->session->userdata('rank') !== 1) {
			print 0;
			return false;
		}
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.ok = 0,
			resources_items.exp = 1,
			resources_items.expdate = NOW()
			WHERE
			resources_items.id = ?", array($itemID));
		$this->usefulmodel->insert_audit("����� �������� ����������������� (������������� #".$this->session->userdata('user_name').") ������� ������ #".$itemID);
		($this->db->affected_rows()) ? print 1 : print 0;
	}

	public function ingroup($itemID=0){
		$this->db->query("UPDATE 
		resources_items
		SET 
		resources_items.ingroup = 1,
		resources_items.ingroupdate = NOW()
		WHERE
		resources_items.id = ?", array($this->input->post("itemID")));
		$this->usefulmodel->insert_audit("����� �������� ����������������� (������������� #".$this->session->userdata('user_name').") ������� � ������ ���� ������������ �� ������ #".$itemID);
		if ($this->db->affected_rows()) {
			print 1;
			return true;
		}
		print 0;
		return false;
	}

	public function resexpiredandapplied($itemID=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.ok        = 0,
			resources_items.exp       = 1,
			resources_items.expdate   = NOW(),
			resources_items.apply     = 1,
			resources_items.applydate = NOW()
			WHERE
			resources_items.id = ?", array($itemID));
		$this->usefulmodel->insert_audit("������������� #".$this->session->userdata('user_name').") ������� ������ #".$itemID. " ��� ���������.");
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function resdelete($itemID=0) {
		if ((int)$this->session->userdata('rank') !== 1) {
			print 0;
			return false;
		}
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.del = 1,
			resources_items.deldate = NOW()
			WHERE
			resources_items.id = ?", array($itemID));
		$this->usefulmodel->insert_audit("����� �������� ����������������� (������������� #".$this->session->userdata('user_name').") ������ ������ #".$itemID);
		($this->db->affected_rows()) ? print 1 : print 0;
	}

	public function reshookup($itemID=0){
		$result = $this->db->query("UPDATE 
			resources_items 
			SET 
			resources_items.apply     = ?,
			resources_items.applydate = NOW()
			WHERE
			resources_items.id = ?", array($this->session->userdata('admin_id'), $itemID));
		$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name')." �������� ������ #".$itemID);
		$out = ($this->db->affected_rows()) ? 1 : 0 ;
		print $out;
	}

	public function usermerge() {
		$target  = $this->input->post('target');
		$sources = implode($this->input->post('sources'), ", ");
		$result  = $this->db->query("UPDATE
		resources_items 
		SET
		resources_items.uid = ?
		WHERE resources_items.uid IN (".$sources.")", array($target));
		$result  = $this->db->query("DELETE FROM users WHERE users.id IN (".$sources.")");
		$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name')." ��������� ������� ������ #".$target." � ".$sources);
		print ($this->db->affected_rows()) ? 1 : 0 ;
	}
	/*
	public function roomsget($itemID=0){
		$out = array('<option value=0>�������� ���������</option>');
		$result = $this->db->query("SELECT 
		locations.`id`,
		locations.`address`,
		CASE 
			WHEN ASCII(RIGHT(locations.`address`, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(locations.`address`, '-'), 16, '0')
			ELSE LPAD(locations.`address`, 16, '0') END AS `vsort`
		FROM `locations`
		WHERE `locations`.parent = ?
		ORDER BY `vsort`", array($itemID));
		if(sizeof($result->num_rows())){
			foreach($result->result() as $row){
				$string = "<option value=".$row->id.">".$row->address."</option>";
				array_push($out,$string);
			}
		}
		print implode($out,"\n");
	}
	*/
	public function setfired($itemID=0){
		$result = $this->db->query("UPDATE 
		users 
		SET
		users.fired = 1 
		WHERE users.id = ?", array(trim($itemID)));
		if ($this->db->affected_rows()) {
			$this->db->query("UPDATE
			`arm`
			SET
			active  = 0,
			out_ts  = NOW()
			WHERE
			arm.uid = ?", array(trim($itemID)));
			if ($this->db->affected_rows()) {
				print 1;
				return true;
			}
			print 0;
		}
	}

	public function switchfired(){
		// checkin io state or new 
		$itemID = $this->input->post("id");
		$result = $this->db->query("SELECT 
		departments.chief,
		users.id
		FROM
		departments
		LEFT OUTER JOIN users ON (departments.chief = users.staff_id)
		AND (departments.id = users.dep_id)
		WHERE
		NOT (users.fired)
		AND departments.id = (SELECT `users`.dep_id FROM `users` WHERE `users`.id = ?)
		AND `users`.id <> ?", array($itemID, $itemID));
		if ($result->num_rows()) {
			$this->fireUser($itemID);
			print 'data = { error : 0, message : "���������� ������ �������" };';
			return true;
		}
		print "data = { error : 1, message : '���������� ������� ������������. ������������� ������� ��� ������������. ������� ��� ������������� ������ ������������ ��� ������� �.�. ������������.' };";
	}

	private function fireUser($itemID) {
		// getting state
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		`users`.fired AS state
		FROM
		`users`
		WHERE users.id = ?", array($itemID));

		if ($result->num_rows()) {
			$row = $result->row();
			if($row->state){
				//���� ������������ ������
				$this->db->query("UPDATE users SET users.fired = 0 WHERE users.id = ?", array( trim($itemID) ));
				$this->db->query("UPDATE `arm` SET active  = 1, out_ts  = '0000-00-00' WHERE arm.uid = ?", array( trim($itemID) ));
				return true;
				//print "arm active\n<br>";
			}
			// ���� �� ������
			$this->db->query("UPDATE users SET users.fired = 1, users.fired_date = NOW() WHERE users.id = ?", array( trim($itemID) ));
			//print "fired set\n<br>";
			$result = $this->db->query("SELECT 
			IF(COUNT(`events`.id) > 0, 0, 1) AS f1 
			FROM `events` 
			WHERE `events`.`type` = 1 
			AND `events`.active 
			AND `events`.`uid` = ?", array(trim($itemID)));
			if ($result->num_rows()) {
				$rowz = $result->row(0);
				if ($rowz->f1 == 1) {
					$this->db->query("INSERT INTO
					`events` (
					`events`.active,
					`events`.owner,
					`events`.active_since,
					`events`.`text`,
					`events`.uid,
					`events`.type
					) VALUES ( 1, 1, DATE_ADD(NOW(), INTERVAL 14 DAY ), ?, ?, ? )", array(
						'<td class="text toAdmin">������������� ������� ������ � ������:<br><a href="/admin/users/'.$itemID.'">'.$row->fio.'</a></td><td class="more">�����������:<br>�������� ������ ������� ������� � ������</td>',
						$itemID,
						1
					));
				}
			}
			$this->db->query("UPDATE `arm` SET active  = 0, out_ts  = NOW() WHERE arm.uid = ?", array(trim($itemID)));
			//print "arm inactive\n<br>";
		}
		//$this->aclgen();
	}

	public function switchsman($itemID=0){
		if($itemID){
			$this->db->query("UPDATE users SET users.sman = IF(users.sman = 0,1,0) WHERE users.id = ?", array($itemID));
			$result = $this->db->query("SELECT users.sman, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($itemID));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name')." ".(($row->sman) ? "�����" : "�������")." ������ �������� ������������ ���� #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$itemID."/3");
	}

	public function switchair($itemID=0){
		if($itemID){
			$this->db->query("UPDATE users SET users.air = IF(users.air = 0,1,0) WHERE users.id = ?", array($itemID));
			$result = $this->db->query("SELECT users.air, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($itemID));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name')." ".(($row->air) ? "�����" : "�������")." ������ �������������� �������������� �������� ������������ ���� #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$itemID."/3");
	}

	public function switchbir($itemID=0){
		if($itemID){
			$this->db->query("UPDATE users SET users.bir = IF(users.bir = 0,1,0) WHERE users.id = ?", array($itemID));
			$result = $this->db->query("SELECT users.bir, LOWER(users.host) as `host` FROM users WHERE users.id = ?", array($itemID));
			if($result->row()){
				$row = $result->row();
				$this->usefulmodel->insert_audit("������� #".$this->session->userdata('user_name')." ".(($row->bir) ? "�����" : "�������")." ������ �������������� ������������ �������������� �������� ������������ ���� #".$row->host);
			}
		}
		$this->load->helper("url");
		redirect("admin/users/".$itemID."/3");
	}

	public function invnumupdate($itemID=0, $number=0){
		$result = $this->db->query("UPDATE 
		hash_items
		SET
		`hash_items`.`inv_number` = ?
		WHERE `hash_items`.`id` = ?", array(trim($number),trim($itemID)));
		if($this->db->affected_rows()) {
			print 1;
		}else{
			print 0;
		}
	}

	public function stuck($itemID=0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->adminmodel->stuck_orders(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function textget(){
		$file = file_get_contents("\\kd1\R_storage$\DATA1\ADUSHEVVI101.log");
		print $file;
		//phpinfo();
	}

	public function getbidinfo() {
		$output = array();

		$result = $this->db->query("SELECT 
		resources_items.matrix, 
		CASE
			WHEN resources_items.ok  THEN concat_ws( ' ', '���������:', DATE_FORMAT(resources_items.okdate,  '%d.%m.%Y %H:%i'))
			WHEN resources_items.exp THEN concat_ws( ' ', '��������:',  DATE_FORMAT(resources_items.expdate, '%d.%m.%Y %H:%i'))
			ELSE '������� ��������� ���'
		END AS `status`,
		DATE_FORMAT(resources_orders.docdate, '%d.%m.%Y') AS docdate,
		resources_orders.docnum,
		if (resources_items.apply, CONCAT_WS( ' ', '������� �������� ��:', resources_items.applydate), '����� ��������� �������� ��������') AS applystate,
		resources_items.applyer
		FROM
		resources_items
		LEFT OUTER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		WHERE resources_items.id = ?", array($this->input->post("id")));
		if ($result->num_rows()) {
			$row = $result->row();
			$data = '������: <strong>'.$row->status.'</strong><br>���� ������: <strong>'.$row->docdate."</strong><br>����� ������: <strong>".$row->docnum."</strong><br>".$row->applystate;
		}

		$result = $this->db->query("SELECT 
		CASE
		WHEN `resources_pid`.`pid` = 1 THEN CONCAT(resources_pid.pid_value, '@arhcity.ru')
		WHEN `resources_pid`.`pid` = 6 THEN INET_NTOA(resources_pid.pid_value)
		ELSE resources_pid.pid_value
		END as descr,
		`resources_desc`.pn
		FROM
		`resources_desc`
		RIGHT OUTER JOIN resources_pid ON (`resources_desc`.id = resources_pid.pid)
		WHERE
		(resources_pid.item_id = ?)", array($this->input->post("id")));
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$string = '<tr><th>'.$row->pn.'</th><td>'.nl2br($row->descr).'</td></tr>';
				array_push($output, $string);
			}
		}

		print '<small>'.$data.'<table class="table table-condensed table-bordered"><tr><th>����</th><th>��������</th></tr>'.implode($output, "\n")."</table></small>";
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */