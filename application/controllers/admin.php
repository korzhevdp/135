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
		$this->load->model('startscreenmodel');
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->startscreenmodel->startscreen_show(),
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

	public function audit($id=0){
		$this->load->view('audit', $this->adminmodel->getAuditData($id));
	}

	######## AJAX-секция
	public function apply_filter($filter = ""){
		$this->session->set_userdata('filter', $filter);
		$this->usefulmodel->filter_users($filter);
	}

	public function ressubmit(){
		$id      = $this->input->post('id');
		$num     = iconv('UTF-8', 'Windows-1251' , urldecode($this->input->post('num')));
		$date    = $this->input->post('date');
		$ip      = $this->input->post('ip');
		$email   = $this->input->post('email');
		$date    = (!$date) ? date("Y-m-d") : implode(array_reverse(explode('.', $date)), "-");
		$res_id  = 0;
		$user_id = 0;
		$DB1     = $this->load->database('35', TRUE);
		$DB2     = $this->load->database('web', TRUE);

		$result = $DB1->query("SELECT
		`resources_items`.rid,
		`resources_items`.uid
		FROM
		`resources_items`
		WHERE `resources_items`.`id` = ?", array($id));
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
		resources_items.id = ?", array($id));

		$DB1->query("UPDATE
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

		if ($ip || $email) {
			$ipc      = explode(".",$ip);
			$ipappend = (sizeof($ipc) !== 2 && $ipc[0] == "192" && $ipc[1] == "168") ? $ipc[0].".".$ipc[1] : "192.168" ;
			$ipflex   = implode(array_splice($ipc, ((sizeof($ipc) == 2) ? 0 : 2)), ".");
			if (!$email) {
				//print 111;
				$DB1->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($id));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $id));
				$this->aclgen();
			} else {
				$DB1->query("DELETE FROM resources_pid WHERE resources_pid.item_id = ? AND resources_pid.pid NOT IN (12,2)", array($id));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,INET_ATON('".$ipappend.".".$ipflex."'),?)", array(6, $id));
				$DB1->query("INSERT INTO resources_pid (pid, pid_value, item_id) VALUES (?,?,?)", array(1, $email, $id));
			}
		}

		if ($res_id == 13) {
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
				$webString = implode(array($row->dn, ", ".$row->office, ", тел.: ".$row->phone), " ");
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
					$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") добавил учётную запись на web-сервере www.arhcity.ru #".$row->login);
				}
				if ($result2->num_rows()) {
					$this->usefulmodel->insert_audit("Учётная запись на web-сервере www.arhcity.ru #".$row->login." не добавлена. Пользователь существует.");
				}

			}
		}

		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") выполнил заявку #".$id);
	}

	public function resexpire($id=0){
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
			resources_items.id = ?", array($id));
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") отменил заявку #".$id);
		($this->db->affected_rows()) ? print 1 : print 0;
	}

	public function ingroup($id=0){
		$this->db->query("UPDATE 
		resources_items
		SET 
		resources_items.ingroup = 1,
		resources_items.ingroupdate = NOW()
		WHERE
		resources_items.id = ?", array($this->input->post("itemID")));
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") включил в группу ЕСИА пользователя по заявке #".$id);
		if ($this->db->affected_rows()) {
			print 1;
			return true;
		}
		print 0;
		return false;
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

	public function resdelete($id=0) {
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
			resources_items.id = ?", array($id));
		$this->usefulmodel->insert_audit("Отдел сетевого администрирования (администратор #".$this->session->userdata('user_name').") удалил заявку #".$id);
		($this->db->affected_rows()) ? print 1 : print 0;
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

	public function usermerge() {
		$target  = $this->input->post('target');
		$sources = implode($this->input->post('sources'), ", ");
		$result  = $this->db->query("UPDATE
		resources_items 
		SET
		resources_items.uid = ?
		WHERE resources_items.uid IN (".$sources.")", array($target));
		$result  = $this->db->query("DELETE FROM users WHERE users.id IN (".$sources.")");
		$this->usefulmodel->insert_audit("Куратор #".$this->session->userdata('user_name')." объединил учётные записи #".$target." и ".$sources);
		print ($this->db->affected_rows()) ? 1 : 0 ;
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
			$this->db->query("UPDATE
			`arm`
			SET
			active  = 0,
			out_ts  = NOW()
			WHERE
			arm.uid = ?", array(trim($id)));
			if ($this->db->affected_rows()) {
				print 1;
				return true;
			}
			print 0;
		}
	}

	public function switchfired(){
		// checkin io state or new 
		$id = $this->input->post("id");
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
		AND `users`.id <> ?", array($id, $id));
		if ($result->num_rows()) {
			$this->fireUser($id);
			print 'data = { error : 0, message : "Увольнение прошло успешно" };';
			return true;
		}
		print "data = { error : 1, message : 'Невозможно уволить пользователя. Подразделение остаётся без руководителя. Укажите для подразделения нового руководителя или укажите и.о. руководителя.' };";
	}

	private function fireUser($id) {
		// getting state
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		`users`.fired AS state
		FROM
		`users`
		WHERE users.id = ?", array($id));

		if ($result->num_rows()) {
			$row = $result->row();
			if($row->state){
				//если пользователь уволен
				$this->db->query("UPDATE users SET users.fired = 0 WHERE users.id = ?", array( trim($id) ));
				$this->db->query("UPDATE `arm` SET active  = 1, out_ts  = '0000-00-00' WHERE arm.uid = ?", array( trim($id) ));
				return true;
				//print "arm active\n<br>";
			}
			// если не уволен
			$this->db->query("UPDATE users SET users.fired = 1, users.fired_date = NOW() WHERE users.id = ?", array( trim($id) ));
			//print "fired set\n<br>";
			$result = $this->db->query("SELECT IF(COUNT(`events`.id) > 0, 0, 1) AS f1 FROM `events` WHERE `events`.`type` = 1 AND `events`.active AND `events`.`uid` = ?", array(trim($id)));
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
						'<td class="text toAdmin">Заблокировать учётную запись в домене:<br><a href="/admin/users/'.$id.'">'.$row->fio.'</a></td><td class="more">Комментарий:<br>Удаление старых учётных записей в домене</td>',
						$id,
						1
					));
				}
			}
			$this->db->query("UPDATE `arm` SET active  = 0, out_ts  = NOW() WHERE arm.uid = ?", array(trim($id)));
			//print "arm inactive\n<br>";
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