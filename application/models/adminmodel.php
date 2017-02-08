<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminmodel extends CI_Model {
	function __construct(){
		parent::__construct();	// Call the Model constructor
		$this->load->model("usefulmodel");
	}

	public function userdata_get($user_id = 0, $page = 1) {
		$user_id = ($user_id) ? $user_id : (($this->input->post('userSelector')) ? $this->input->post('userSelector') : 0);
		
		$user = $this->getEmptyUserDataArray();

		$result = $this->db->query("SELECT
		users.id,
		TRIM(users.name_f) as name_f,
		TRIM(users.name_i) as name_i,
		TRIM(users.name_o) as name_o,
		users.dep_id,
		users.staff_id,
		users.office_id,
		TRIM(users.host) as host,
		TRIM(users.login) as login,
		users.memo,
		users.phone,
		users.service,
		users.fired,
		users.air,
		users.bir,
		users.io,
		users.sman,
		LCASE(DATE_FORMAT(users.fired_date, '%e %M %Y')) AS fired_date,
		locations.parent,
		`admins`.id AS sup_id,
		users.supervisor,
		users.superv,
		users.servoperator
		FROM
		users
		LEFT OUTER JOIN locations ON (users.office_id = locations.id)
		LEFT OUTER JOIN admins ON (users.supervisor = admins.base_id)
		WHERE
		(users.id = ?)", array($user_id));
		if ($result->num_rows()){
			$user = $result->row_array();
		}
		$user['serviceman'] = $user['service'];
		$user['dept']       = $this->getUserDeptList($user);
		$user['staff_id']   = $this->getUserStaffList($user);
		$user['mac']        = $this->getUserMAC($user);
		$user['email']      = $this->getUserEMail($user);
		$user['location']   = $this->getUserLocationList($user);
		$user['service']    = $this->getUserServiceList($user);
		
		$user['servop']     = ($user['servoperator'])	? 'checked="checked"' : "" ;
		$user['air']        = ($user['air'])			? 'checked="checked"' : "" ;
		$user['bir']        = ($user['bir'])			? 'checked="checked"' : "" ;
		$user['sman']       = ($user['sman'])			? 'checked="checked"' : "" ;
		$user['superv']     = ($user['superv'])			? 'checked="checked"' : "" ;

		$user['page']       = $page;
		$user['is_io']      = ($user['io']) ? ' checked="checked"' : "";
		$user['supchange']  = (
			$this->session->userdata('rank')
			|| (
				$this->session->userdata('is_sup')
				&& $this->session->userdata('canSee') == $user['supervisor']
				)
			) ? 1 : 0 ;
		$user['saveable']   = (
			$this->session->userdata('rank') 
			|| $this->session->userdata('canSee') == $user['supervisor'] 
			|| $user['serviceman'] == $this->session->userdata('base_id') 
		) ? 1 : 0 ;
		return $user;
	}

	private function getEmptyUserDataArray() {
		return array(
			'id'			=> 0,
			'name_f'		=> '',
			'name_i'		=> '',
			'name_o'		=> '',
			'dep_id'		=> 0,
			'staff_id'		=> 0,
			'office_id'		=> 0,
			'host'			=> '',
			'login'			=> '',
			'memo'			=> '',
			'phone'			=> '',
			'service'		=> 0,
			'fired'			=> 0,
			'air'			=> 0,
			'bir'			=> 0,
			'sman'			=> 0,
			'fired_date'	=> '',
			'parent'		=> 1,
			'sup_id'		=> 0,
			'supervisor'	=> 0,
			'is_io'			=> 0,
			'io'			=> 0,
			'servoperator'	=> '',
			'superv'		=> 0
		);
	}

	private function getUserServiceList($user) {
		$result = $this->db->query("SELECT 
		users.id,
		CONCAT_WS(' ',users.name_f, users.name_i, users.name_o) AS value
		FROM
		users
		WHERE `users`.`sman` = 1
		AND `users`.`fired` = 0
		ORDER BY
		CONCAT(users.name_f, users.name_i, users.name_o)");
		if ($result->num_rows()) {
			return $this->usefulmodel->returnList($result, $user['service']);
		}
		return "";
	}

	private function getUserStaffList($user) {
		$result = $this->db->query("SELECT
		`staff`.id,
		`staff`.staff as value
		FROM
		`staff`
		ORDER BY `staff`.`staff`", array($user['staff_id']));
		if ($result->num_rows()){
			return $this->usefulmodel->returnList($result, $user['staff_id']);
		}
		return "";
	}

	private function getUserDeptList($user) {
		$result = $this->db->query("SELECT
		departments.id,
		departments.dn AS value
		FROM
		departments
		ORDER BY
		departments.dn");
		if ($result->num_rows()) {
			return $this->usefulmodel->returnList($result,  $user['dep_id']);
		}
		return "";
	}

	private function getUserMAC($user) {
		if ($user['id']) {
			$result = $this->db->query("SELECT
			hash_items.networkcard_macaddress
			FROM
			`hosts`
			INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
			WHERE
			(`hosts`.uid = ?)
			ORDER BY hosts.ts DESC",array($user['id']));
			if ($result->num_rows()) {
				$output = array();
				foreach ($result->result() as $row) {
					array_push($output, $row->networkcard_macaddress);
				}
				return implode($output, ";  ");
			}
			return "";
		}
		return "";
	}

	private function getUserEMail($user) {
		if ($user['id']) {
			$result = $this->db->query("SELECT DISTINCT
			CONCAT(resources_pid.pid_value,'@arhcity.ru') AS email
			FROM
			resources_pid
			INNER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
			WHERE
			(resources_items.uid = ?)
			AND (resources_items.rid = 100)
			AND (resources_pid.pid = 1) 
			AND (NOT (resources_items.del))
			AND (NOT (resources_items.`exp`))
			AND (resources_items.ok)", array($user['id']) );
			if ($result->num_rows()) {
				$output = array();
				foreach ($result->result() as $row) {
					array_push($output, strtolower($row->email));
				}
				return implode($output, "; ");
			}
			return "";
		}
		return "";
	}

	private function getUserLocationList($user) {
		$input  = array();
		$output = array();
		$result = $this->db->query("SELECT `locations`.`id`,
		TRIM(CONCAT_WS(' ', locations1.address, `locations`.address)) AS address,
		`locations`.`parent`,
		CASE
			WHEN ASCII(RIGHT(`locations`.address, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`locations`.address, '-'), 24, '0')
			ELSE LPAD(`locations`.address, 24, '0') END AS `vsort`
		FROM `locations` locations1
		INNER JOIN `locations` ON (locations1.id = `locations`.parent)
		WHERE `locations`.id <> 0
		ORDER BY `locations`.`parent`, `vsort`");
		if ($result->num_rows()) {
			foreach($result->result_array() as $row) {
				if($row['parent'] == 0){
					$row['parent'] = $row['id'];
				}
				if(!isset($input[$row['parent']])) {
					$input[$row['parent']] = array();
				}
				$selected = ($row['id'] == $user['office_id']) ? 'selected="selected"' : '';
				array_push($input[$row['parent']], '<option value='.$row['id'].' '.$selected.'>'.$row['address'].'</option>');
			}
			foreach ($input as $key=>$val) {
				array_push($output, implode($val, "\n"));
			}
			return implode($output,"\n");
		}
	}

	public function user_resources_get($user_id) {
		$res    = array();
		$result = $this->db->query("SELECT
		resources_items.id,
		resources_items.rid,
		resources_items.uid,
		resources_items.order_id,
		resources_pid.pid,
		IF(resources_pid.pid = 6, REPLACE(INET_NTOA(resources_pid.pid_value), '192.168.', ''), resources_pid.pid_value) AS pid_value,
		resources_items.apply,
		resources_items.applydate,
		resources_items.ok,
		resources_items.`exp`,
		DATE_FORMAT(resources_items.okdate, '%e.%m.%Y %H:%i') AS okdate,
		resources.shortname,
		resources.location,
		resources.`action`,
		resources.`cat`,
		resources.bitmask,
		resources_orders.docnum,
		(DATE_FORMAT(resources_orders.docdate, '%e.%m.%Y')) AS docdate,
		`users`.supervisor
		FROM
		resources_pid
		RIGHT OUTER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
		LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
		INNER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		INNER JOIN `users` ON (resources_items.uid = `users`.id)
		WHERE
		(resources_items.uid = ?)
		AND NOT (resources_items.del)
		ORDER BY
		resources_items.ok, resources_items.id DESC", array($user_id));
		if($result->num_rows()) {
			foreach($result->result_array() as $row){
				if(!isset($res[$row['id']])){
					$res[$row['id']] = array();
				}
				$res[$row['id']]['pid'.$row['pid']] = strtolower($row['pid_value']);
				unset( $row['pid'], $row['pid_value'] );
				$res[$row['id']] = array_merge($res[$row['id']], $row);
			}
		}
		$output = $this->fillResourceListTemplate($res);
		return $this->load->view("reslisttemplate", $output, true);
	}

	private function fillResourceListTemplate($res) {
		$active  = array();
		$expired = array();
		foreach ($res as $key=>$row) {
			$row['editAllowed'] = ($this->session->userdata('rank') || $this->session->userdata("admin_id") == 26) ? '' : ' disabled="disabled"';
			####################
			$row['ipChunk']  = (in_array($row['rid'], array(100,101)))	? $this->load->view("iptemplate", $row, true) : '';
			$row['mnChunk']  = (in_array($row['rid'], array(100)))		? $this->load->view("mntemplate", $row, true) : '';
			$row['osa_date'] = ( $row["ok"] ) ? "исполнено ОСА: ".$row['okdate'] : "";
			#####################
			$row['button1']  = $this->getButton1($row);
			$row['button2']  = ($this->session->userdata('rank'))
				? '<span class="btn btn-warning btn-small expire" prop="'.$row['id'].'" title="Отменить"><i class="icon-remove-sign icon-white"></i>&nbsp;</span>
				<span class="btn btn-danger btn-small delete"  prop="'.$row['id'].'" title="Удалить"><i class="icon-trash icon-white"></i>&nbsp;</span>
				<span class="btn btn-info btn-small makeEvent" prop="'.$row['id'].'" title="Создать поручение"><i class="icon-calendar icon-white"></i>&nbsp;</span>'
				: '';
			$row['button2']  = ($this->session->userdata("admin_id") == 26 && $row['rid'] == 103)
				? '<span class="btn btn-warning btn-small expire" prop="'.$row['id'].'" title="Отменить"><i class="icon-remove-sign icon-white"></i>&nbsp;</span>'
				: $row['button2'];

			$template = $this->load->view("restemplate", $row, true);
			($row['exp']) ? array_push($expired, $template) : array_push($active, $template);
		}
		return array(
			'asize'		=> sizeof($active),
			'active'	=> implode($active, "\n"),
			'esize'		=> sizeof($expired),
			'expired'	=> implode($expired, "\n")
		);
	}

	private function getAdminModeButton1($row) {
		$color   = ($row["apply"])	? "success"   : "warning";
		$hooking = ((int)$this->session->userdata('canSee') === (int)$row['supervisor']) ? ' hookup' : "";
		$active  = ($row["ok"])		? "icon-edit" : "icon-ok-sign";
		$applied = ($row["apply"])	? "Исполнено" : "Доложить об исполнении";
		$button1 = '<span class="btn btn-'.$color.' btn-small'.$hooking.'" prop="'.$row['id'].'">
			<i class="'.$active.' icon-white"></i>&nbsp;'.$applied.'&nbsp;
		</span>
		<span class="btn btn-info btn-small makeBackEvent" prop="'.$row['id'].'"  title="Создать сообщение администратору">
			<i class="icon-calendar icon-white"></i>&nbsp;
		</span>';
		return $button1;
	}

	private function getUserModeButton1($row) {
		$color = "primary";
		$title = "Пометить исполненной отделом СА";
		$icon  = "icon-ok-sign";
		$label = "Активировать";
		if ($row['ok']) {
			$icon  = "icon-edit";
			$color = ($row["apply"]) ? "success"   : "inverse";
			$title = ($row["apply"]) ? "Архив"     : "Обновить данные заявки";
			$label = ($row["apply"]) ? "Исполнено" : "Ждём куратора";
		}
		return '<span class="btn btn-'.$color.' btn-small activate" prop="'.$row['id'].'" title="'.$title.'">
			<i class="'.$icon.' icon-white"></i>&nbsp;'.$label.
		'</span>';
	}

	
	private function getButton1($row) {
		if ((int)$this->session->userdata('rank') === 0 || (int)$this->session->userdata("admin_id") === 26) {
			return $this->getAdminModeButton1($row);
		}
		return $this->getUserModeButton1($row);
	}

	private function getNullARM() {
		return array(
			'pcconfs'  => "Не выбран пользователь",
			'pclist'   => "",
			'pcused'   => "",
			'pcpo'     => "",
			'licenses' => ""
		);
	}

	public function user_arm_get($user_id) {
		if (!$user_id) {
			$this->getNullARM();
		}
		$output  = array();
		$headers = array();
		$pclist  = array();
		$usedpc  = array();
		$result  = $this->db->query("SELECT DISTINCT
		hash_items.id,
		hash_items.inv_number,
		CONCAT_WS(' ', hash_items.baseboard_manufacturer,hash_items.baseboard_product,hash_items.baseboard_serialnumber,hash_items.baseboard_version) AS mb,
		hash_items.computersystemproduct_name AS system,
		hash_items.bios_description AS bios,
		CONCAT_WS(' ', hash_items.processor_description,hash_items.processor_name) AS processor,
		hash_items.physicalmemory_capacity AS ram,
		CONCAT_WS(' ', hash_items.networkcard_description, hash_items.networkcard_macaddress) AS nic,
		hash_items.diskdrive_pnpdeviceid AS hdd,
		hash_items.cdromdrive_caption AS cdrom,
		hash_items.videocontroller_caption AS video,
		DATE_FORMAT(hash_items.ts,'%d.%m.%Y') AS `date`,
		hash_items.active,
		hash_items.hostname,
		hash_items.all_md5
		FROM
		`hosts`
		INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
		WHERE
		(`hosts`.uid = ?)
		ORDER BY hash_items.hostname, hash_items.ts DESC", array($user_id));
		if ($result->num_rows()) {
			foreach ($result->result_array() as $row) {
				$string1 = '<li title="'.$row['hostname'].'" act="'.$row['id'].'" class="armselector">'.$row['hostname'].'</li>';
				($row['active'])
					? array_push($pclist, $string1)
					: array_push($usedpc, $string1);
				$row['user_id'] = $user_id;
				$row['pcmode'] = ($row['active'])
					? '<button type="submit" class="btn btn-warning btn-mini" form="invform'.$row['id'].'" name="block" value="block">Заблокировать</button>'
					: '<button type="submit" class="btn btn-warning btn-mini" form="invform'.$row['id'].'" name="block" value="unblock">Разблокировать</button>';
				$string2 = $this->load->view('userarmtemplate', $row, true);
				array_push($output, $string2);
			}
		}

		return array(
			'pcconfs'  => implode($output, "\n"),
			'pclist'   => implode($pclist, "\n"),
			'pcused'   => implode($usedpc, "\n"),
			'licenses' => $this->licensemodel->userlicenses_get($user_id),
		);
	}

	public function user_save() {
		$this->updateFEEntry($this->input->post('saveID'));
		$result = $this->db->query("UPDATE
		`users`
		SET
		`users`.name_f = TRIM(?),
		`users`.name_i = TRIM(?),
		`users`.name_o = TRIM(?),
		`users`.dep_id = ?,
		`users`.staff_id = ?,
		`users`.office_id = ?,
		`users`.memo = ?,
		`users`.phone = ?,
		`users`.service = ?,
		`users`.supervisor = (SELECT admins.supervisor FROM admins WHERE `admins`.`base_id` = ? LIMIT 1),
		`users`.host = TRIM(?),
		`users`.login = TRIM(?),
		`users`.air = ?,
		`users`.bir = ?,
		`users`.sman = ?,
		`users`.servoperator = ?,
		`users`.superv = ?,
		`users`.io = ?
		WHERE
		`users`.`id` = ?", array(
			$this->input->post('sname'),
			$this->input->post('name'),
			$this->input->post('fname'),
			$this->input->post('dept'),
			$this->input->post('staff'),
			$this->input->post('office'),
			$this->input->post('memo'),
			$this->input->post('phone'),
			$this->input->post('service'),
			$this->input->post('service'),
			$this->input->post('host'),
			$this->input->post('login'),
			$this->input->post('air'),
			$this->input->post('bir'),
			$this->input->post('sman'),
			$this->input->post('servop'),
			$this->input->post('superv'),
			$this->input->post('io'),
			$this->input->post('saveID')
		));
		$this->usefulmodel->insert_audit("Сохранена учётная карточка пользователя ".$this->input->post('login'));
		$this->load->helper('url');
		redirect("admin/users/".$this->input->post('saveID'));
	}

	private function updateFEEntry($userID) {
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o ) as fio
		FROM
		`users`
		WHERE `users`.`id` = ?", array($userID));
		if ($result->num_rows()) {
			if ($result->num_rows() > 1) {
				return false;
			}
			$row = $result->row(0);
			$fio = $row->fio;
			$newfio = ucwords(implode( array( $this->input->post('sname'), $this->input->post('name'), $this->input->post('fname')), " "));
			if ($fio != $newfio) {
				$DB2 = $this->load->database('12', TRUE);
				$DB2->query("UPDATE `fios` SET `fios`.`fio` = TRIM(?) WHERE TRIM(`fios`.`fio`) = TRIM(?)", array( $newfio, $fio ));
			}
		}
	}

	public function quick_add() {
		$this->db->query("INSERT INTO resources_orders (resources_orders.docdate) VALUES (NOW())");
		$orderID = $this->db->insert_id();
		$hash = array(
			1 => array( 101 ),
			2 => array( 100 ),
			3 => array( 101, 100 ),
			7 => array( 102 ),
			8 => array( 58 ),
			9 => array( 281 ),
			10 => array( 103 )
		);
		foreach($hash[$this->input->post("quick_reg")] as $val){
			$this->db->query("INSERT INTO
			`resources_items` (
				`resources_items`.uid,
				`resources_items`.order_id,
				`resources_items`.rid
			) VALUES ( ?, ?, ? )" , array(
				$this->input->post("quser"),
				$orderID,
				$val
			));
		}
		$this->usefulmodel->insert_audit("Добавлена административная заявка на доступ к информационному ресурсу. Пользователь: #".$this->input->post("quser"));
		$this->load->helper("url");
		redirect("admin/users/".$this->input->post("quser")."/"."2");
	}
	
	public function takeuser($user, $newcurator) {
		$result = $this->db->query("UPDATE 
		`users` 
		SET
		`users`.service = ?,
		`users`.supervisor = (
			SELECT 
			admins.supervisor
			FROM
			admins
			WHERE
			`admins`.`base_id` = ?
		)
		WHERE `users`.id = ?", array($newcurator, $newcurator, $user));
		$this->load->helper("url");
		redirect("admin/users/".$user);
	}
}

/* End of file adminmodel.php */
/* Location: ./application/models/adminmodel.php */