<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refmodel extends CI_Model {
	function __construct(){
		parent::__construct();// Call the Model constructor
		//$this->output->enable_profiler(TRUE);
	}

	############################
	############################
	#		Информационные ресурсы
	############################

	public function res_list_get($res){
		$mode   = ($this->input->post("d")) ? "WHERE `resources`.active" : "" ;
		//$res    = ($this->input->post('resource')) ? $this->input->post('resource') : $this->input->post('resID');
		$result = $this->db->query("SELECT 
		`resources`.name,
		`resources`.id,
		`resources`.active,
		`departments`.alias
		FROM
		`departments`
		RIGHT OUTER JOIN `resources` ON (`departments`.id = `resources`.owner)
		".$mode."
		ORDER BY `resources`.name ASC");
		$options_a = array();
		$options_s = array();
		if ($result->num_rows()){
			foreach($result->result() as $row){
				$checked = ($res == $row->id) ? ' selected="selected"' : '';
				$string  = '<option value="'.$row->id.'"'.$checked.'>'.$row->name.' - '.$row->alias.'</option>';
				($row->active) ? array_push($options_a, $string) :  array_push($options_s, $string);
			}
		}

		return '<option value="0">-- Выберите ресурс --</option><optgroup label="Активные">'.implode($options_a, "\n").'</optgroup>'.'<optgroup label="Неактивные">'.implode($options_s, "\n").'</optgroup>';
	}

	public function res_data_get($resource=0, $tab=1){
		$output     = array();
		$admins_bir = array();
		$depts_bir  = array();
		$table      = array();
		$groups     = array(
			11 => 'Подключение к ЛВС',
			1  => '1C:',
			2  => 'MS SQL-сервер',
			3  => 'Регистрация обращений граждан (территориальные округа)',
			4  => 'Справочно-Правовые Системы',
			5  => 'Департамент здравоохранения',
			6  => 'Информационные системы (АИС / ГИС)',
			7  => 'Департамент муниципального имущества',
			8  => 'Департамент образования',
			10 => 'Интернет / Электронная почта',
			0  => 'Прочие'
		);
		$categories   = array(
			0  => 'Выберите из списка',
			1  => 'Общедоступный',
			2  => 'ДСП',
			3  => 'Секретный'
		);
		$status       = array(
			0  => 'Неактивный',
			1  => 'Активный'
		);
		$arm          = array(
			0 => 'Не прикрепляется',
			1 => 'Прикрепляется'
		);
		$res_data     = array(
			'name'         => 'Ресурс',
			'action'       => '',
			'shortname'    => '',
			'location'     => '',
			'grp'          => 'no',
			'owner'        => 0,
			'cat'          => 1,
			'active'       => 1,
			'arm_related'  => 0,
			'adm'          => 255,
			'adm_sec'      => "1084_25",
			'tab1'         => '',
			'tab2'         => '',
			'tab3'         => '',
			'f_depname'    => '',
			'f_softname'   => '',
			'f_dbname'     => '',
			'f_application'=> '',
			'f_origin'     => 1,
			'f_developer'  => '',
			'f_reseller'   => '',
			'f_date'       => '',
			'f_startdate'  => '',
			'f_enddate'    => '',
			'f_endreason'  => '',
			'f_lang'       => '',
			'f_stored_at'  => '',
			'f_licenses'   => '',
			'f_supporter'  => '',
			'f_location'   => '',
			'f_pc_prereq'  => '',
			'f_doc'        => '',
			'f_users'      => '',
			'f_retro'      => '',
			'f_datacycle'  => '',
			'f_cat'        => '',
			'f_owner'      => '',
			'f_dbvol'      => '',
			'in_report'    => 0,
			'dl_owner'     => 0
		);

		$result = $this->db->query("SELECT 
		resources.name,
		resources.shortname,
		resources.location,
		resources.owner,
		resources.cat,
		resources.grp,
		resources.adm_sec,
		resources.`action`,
		resources.active,
		resources.adm,
		resources.bitmask,
		resources.arm_related,
		resources.f_depname,
		resources.f_dbname,
		resources.f_application,
		resources.f_origin,
		resources.f_developer,
		resources.f_reseller,
		DATE_FORMAT(resources.f_date, '%d.%m.%Y') AS f_date,
		DATE_FORMAT(resources.f_enddate, '%d.%m.%Y') AS f_enddate,
		DATE_FORMAT(resources.f_startdate, '%d.%m.%Y') AS f_startdate,
		resources.f_endreason,
		resources.f_lang,
		resources.f_stored_at,
		resources.f_licenses,
		resources.f_supporter,
		resources.f_location,
		resources.f_pc_prereq,
		resources.f_doc,
		resources.f_retro,
		resources.f_users,
		resources.f_datacycle,
		resources.f_cat,
		resources.f_owner,
		resources.f_dbvol,
		resources.in_report,
		resources.f_usermemo
		FROM
		resources
		WHERE
		(resources.id = ?)", array($resource));
		if ($result->num_rows()){
			// array_merge???

			$res_data = $result->row_array();
			$res_data['tab1']          = '';
			$res_data['tab2']          = '';
			$res_data['tab3']          = '';
		}
		$res_data['tab'.$tab] = 'active';

		$res_data['resource'] = $resource;
		$result = $this->db->query("SELECT
		departments.id,
		departments.dn
		FROM
		departments
		ORDER BY 
		departments.dn", array());
		if ($result->num_rows()){
			$output  = array();
			$output2 = array();
			foreach($result->result() as $row){
				$string  = '<option value="'.$row->id.'"'.(($row->id == $res_data['f_owner']) ? ' selected="selected"' : "").'>'.$row->dn.'</option>';
				$string2 = '<option value=\''.$row->dn.'\'>'.$row->dn.'</option>';
				array_push($output, $string);
				array_push($output2, $string2);
			}
			$res_data['owner']    = implode($output, "\n");
			$res_data['dl_owner'] = implode($output2, "\n");
		}

		$output = array();
		foreach($groups as $key=>$val){
			$string = '<option value="'.$key.'"'.(($key == $res_data['grp']) ? ' selected="selected"' : "").'>'.$val.'</option>';
			array_push($output, $string);
		}
		$res_data['group'] = implode($output, "\n");
			

		$output = array();
		foreach($categories as $key=>$val){
			$string = '<option value="'.$key.'"'.(($key == $res_data['cat']) ? ' selected="selected"' : "").'>'.$val.'</option>';
			array_push($output, $string);
		}
		$res_data['category'] = implode($output, "\n");

		$output = array();
		foreach($status as $key=>$val){
			$string = '<option value="'.$key.'"'.(($key == $res_data['active']) ? ' selected="selected"' : "").'>'.$val.'</option>';
			array_push($output, $string);
		}
		$res_data['status'] = implode($output, "\n");

		$output = array();
		foreach($arm as $key=>$val){
			$string = '<option value="'.$key.'"'.(($key == $res_data['arm_related']) ? ' selected="selected"' : "").'>'.$val.'</option>';
			array_push($output, $string);
		}
		$res_data['arm'] = implode($output, "\n");

		$output = array();
		$result = $this->db->query("SELECT
		users.id,
		CONCAT_WS(' ',users.name_f,users.name_i,users.name_o) as fio
		FROM
		users
		WHERE users.air
		AND NOT users.fired
		ORDER BY 
		concat(users.name_f,users.name_i,users.name_o)");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				$checked = ($res_data['adm'] == $row->id) ? ' selected="selected"' : "";
				$string = '<option value="'.$row->id.'"'.$checked.'>'.$row->fio.'</option>';
				array_push($output, $string);
			}
		}
		$res_data['admins'] = implode($output,"\n");
		######################################################
		$list = array();
		$result = $this->db->query("SELECT DISTINCT
		`resources`.f_lang AS name
		FROM
		`resources`
		WHERE LENGTH(TRIM(`resources`.f_lang)) > 0
		ORDER BY `resources`.f_lang ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($list, '<option value="'.$row->name.'">'.$row->name.'</option>');
			}
		}
		$res_data['lang_list'] = implode($list, "\n");

		$list = array();
		$result = $this->db->query("SELECT DISTINCT
		`resources`.f_supporter AS name
		FROM
		`resources`
		WHERE LENGTH(TRIM(`resources`.f_supporter)) > 0
		ORDER BY `resources`.f_supporter ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($list, '<option value="'.$row->name.'">'.$row->name.'</option>');
			}
		}
		$res_data['supporter_list'] = implode($list, "\n");
		####################################################
		// администраторы безопасности
		
		$adm_src = explode(",", $res_data['adm_sec']);
		
		foreach($adm_src as $val){
			if(!strlen($val) || !strpos($val, "_")){
				$val = "1084_25";
			}
			$array = explode("_", $val);
			$result = $this->db->query("SELECT 
			CONCAT_WS(' ',`users`.name_f, `users`.name_i, `users`.name_o) as fio,
			(SELECT `departments`.`dn` FROM `departments` WHERE `departments`.`id` = ?) as dept
			FROM
			`users`
			WHERE `users`.`id` = ?", array($array[1], $array[0]));
			if ($result->num_rows()){
				$row = $result->row();
				$del_button = ($val == "1084_25") 
					? '' 
					: '<button type="submit" class="btn btn-mini pull-right" name="deleteThis" value="'.$val.'"><i class="icon-trash"></i> Удалить</button>' ;
				$string = "<tr>
					<td>".$row->fio."</td>
					<td>".$row->dept.$del_button.'</td>
				</tr>';
				array_push($table, $string);
			}
			$res_data['table_admsec'] = implode($table, "\n");
		}

		$output = array();
		$result = $this->db->query("SELECT
		users.id, 
		CONCAT_WS(' ',users.name_f,users.name_i,users.name_o) as fio
		FROM
		users
		WHERE users.bir
		AND NOT users.fired
		ORDER BY fio");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->id.'">'.$row->fio.'</option>';
				array_push($output, $string);
			}
		}
		$res_data['admsec_users'] = implode($output, "\n");
		//print_r($res_data);
		return $res_data;
	}

	public function res_data_save(){
		//$this->output->enable_profiler(TRUE);
		//return false;
			$registered = ($this->input->post('in_report')) ? "1" : "0";
			/*
			print "UPDATE resources 
			SET
			resources.shortname   = ".htmlspecialchars($this->input->post('shortname')).",<br>
			resources.location    = ".$this->input->post('location').",<br>
			resources.active      = ".$this->input->post('status').",<br>
			resources.grp         = ".$this->input->post('group').",<br>
			resources.arm_related = ".$this->input->post('arm').",<br>
			resources.action      = ".$this->input->post('action').",<br>
			resources.adm         = ".$this->input->post('adm').",<br>
			resources.in_report   = ".$registered."
			WHERE<br>
			resources.id          = ".$this->input->post('resID');
			*/
			$this->db->query("UPDATE resources 
			SET
			resources.shortname   = ?,
			resources.location    = ?,
			resources.active      = ?,
			resources.grp         = ?,
			resources.arm_related = ?,
			resources.action      = ?,
			resources.adm         = ?,
			resources.in_report   = ?
			WHERE
			resources.id          = ?", 
			array(
				htmlspecialchars($this->input->post('shortname')),
				$this->input->post('location'),
				$this->input->post('status'),
				$this->input->post('group'),
				$this->input->post('arm'),
				$this->input->post('action'),
				$this->input->post('adm'),
				$registered,
				$this->input->post('resID')
			));
			$this->usefulmodel->insert_audit("Сохранено описание ресурса #".$this->input->post('resID'));
	}

	public function res_form_save(){
		//$this->output->enable_profiler(TRUE);
		//return false;
		if($this->input->post('saveMode') === 'save'){
			$fdate  = (preg_match("/\d\d\.\d\d\.\d\d\d\d/", $this->input->post('f_date')))      ? $this->input->post('f_date')      : "00.00.0000";
			$fsdate = (preg_match("/\d\d\.\d\d\.\d\d\d\d/", $this->input->post('f_startdate'))) ? $this->input->post('f_startdate') : "00.00.0000";

			$this->db->query("UPDATE
			resources
			SET
			f_depname = ?,
			name = ?,
			f_dbname = ?,
			f_application = ?,
			f_origin = ?,
			f_developer = ?,
			f_reseller = ?,
			f_date = ?,
			f_startdate = ?,
			f_lang = ?,
			f_stored_at = ?,
			f_licenses = ?,
			f_supporter = ?,
			f_location = ?,
			f_pc_prereq = ?,
			f_doc = ?,
			f_users = ?,
			f_retro = ?,
			f_datacycle = ?,
			f_cat = ?,
			cat = ?,
			f_owner = ?,
			owner = ?,
			f_dbvol = ?,
			f_usermemo = ?
			WHERE resources.id = ?", array(
				$this->input->post('f_depname'),
				$this->input->post('name'),
				$this->input->post('f_dbname'),
				$this->input->post('f_application'),
				$this->input->post('f_origin'),
				$this->input->post('f_developer'),
				$this->input->post('f_reseller'),
				implode(array_reverse(explode(".", $fdate)), "-"),
				implode(array_reverse(explode(".", $fsdate)), "-"),
				$this->input->post('f_lang'),
				$this->input->post('f_stored_at'),
				$this->input->post('f_licenses'),
				$this->input->post('f_supporter'),
				$this->input->post('f_location'),
				$this->input->post('f_pc_prereq'),
				$this->input->post('f_doc'),
				$this->input->post('f_users'),
				$this->input->post('f_retro'),
				$this->input->post('f_datacycle'),
				$this->input->post('f_category'),
				$this->input->post('f_category'),
				$this->input->post('f_owner'),
				$this->input->post('f_owner'),
				$this->input->post('f_dbvol'),
				$this->input->post('f_usermemo'),
				$this->input->post('resID')
			));
		}
		if($this->input->post('saveMode') === 'new'){
			$this->db->query("INSERT INTO
			resources (
				resources.name,
				resources.shortname,
				resources.location,
				resources.owner,
				resources.cat,
				resources.active,
				resources.grp,
				resources.arm_related,
				resources.action,
				resources.adm
			) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )",array(
				htmlspecialchars($this->input->post('fullname')),
				htmlspecialchars($this->input->post('shortname')),
				$this->input->post('location'),
				$this->input->post('owner'),
				$this->input->post('category'),
				$this->input->post('status'),
				$this->input->post('group'),
				$this->input->post('arm'),
				$this->input->post('action'),
				$this->input->post('adm')
			));
			$this->usefulmodel->insert_audit("Добавлено описание ресурса");
		}
	}

	public function res_admsec_add(){
		$resource = $this->input->post('resource');
		$string = $this->input->post('as_adm')."_".$this->input->post('as_dept');
		$this->db->query("UPDATE
		`resources`
		SET
		`resources`.`adm_sec` = CONCAT(?, ',', `resources`.`adm_sec`)
		WHERE
		`resources`.id = ?",array($string, $resource));
		$this->usefulmodel->insert_audit("Добавлен администратор безопасности ресурса");
	}

	public function res_admsec_remove(){
		$resource = $this->input->post('resource');
		$string = $this->input->post('deleteThis');
		$this->db->query("UPDATE
		`resources`
		SET
		`resources`.`adm_sec` = REPLACE(REPLACE(`resources`.`adm_sec`, ? ,''), ?, '')
		WHERE
		`resources`.id = ?",array($string.",", $string, $resource));
		$this->usefulmodel->insert_audit("Удалён администратор безопасности ресурса");
	}

	############################
	############################
	#		Помещения мэрии
	############################
	public function locations_list_get($location=0){
		$output = array('<option value="0">Выберите здание</option>');
		$result = $this->db->query("SELECT 
		`locations`.id,
		`locations`.address
		FROM
		`locations`
		WHERE `locations`.`parent` = 0 AND
		`locations`.`id` > 0
		ORDER BY `locations`.`address`");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				$selected = ($row->id == $location) ? ' selected="selected"' : '';
				$string   = '<option value="'.$row->id.'"'.$selected.'>'.$row->address.'</option>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n");
	}

	public function location_data_get($location=0){
		$locations['address'] = "Не выбран";
		$locations_table = array();

		$result = $this->db->query("SELECT 
		`locations`.address
		FROM
		`locations`
		WHERE
		`locations`.id = ?", array($location));
		if ($result->num_rows()){
			$row = $result->row();
			$locations['address'] = $row->address;
		}
		if($location) {
			$result = $this->db->query("SELECT 
			`locations`.id,
			`locations`.address,
			`locations`.lev,
			`locations`.`floor`
			FROM
			`locations`
			WHERE
			`locations`.`parent` = ? AND
			`locations`.`id` > 0
			order by 
			`locations`.address DESC",array($location));
			$locations_table = array();
			if ($result->num_rows()){
				foreach($result->result() as $row){
					$string = '<tr>
						<td><input type="text" name="loc_name_'.$row->id.'" value="'.$row->address.'"></td>
						<td><input type="text" name="loc_floor_'.$row->id.'" value="'.$row->floor.'"></td>
						<td><button name="locationID" type="submit" class="btn btn-primary btn-mini" value="'.$row->id.'">Сохранить</button></td>
					</tr>';
					array_push($locations_table, $string);
				}
			}
		}
		$locations['locations_table'] = implode($locations_table,"\n");
		$locations['id'] = $location;
		return $locations;
	}

	public function location_data_save(){
		$result = $this->db->query("UPDATE 
		locations 
		SET 
		locations.address = ? 
		WHERE
		locations.id = ?",array(
			$this->input->post('fullname'),
			$this->input->post('locationIDToSave')
		));
		$this->usefulmodel->insert_audit("Сохранён адрес здания");
	}

	public function location_data_add(){
		$result = $this->db->query("INSERT INTO 
		locations (
			locations.address,
			locations.parent,
			locations.floor,
			locations.lev
		) VALUES (?,0,0,1)",array($this->input->post('fullname')));
		$this->usefulmodel->insert_audit("Добавлен адрес здания");
	}

	public function sublocation_data_add(){
		$result = $this->db->query("INSERT INTO 
		locations (
			locations.address,
			locations.parent,
			locations.floor,
			locations.lev
		) VALUES ('',?,0,2)",array($this->input->post('locationIDToSave')));
		$this->usefulmodel->insert_audit("Добавлен адрес размещения");
	}

	public function sublocation_data_save(){
		$location_id = $this->input->post('locationID');
		$result = $this->db->query("UPDATE 
		locations 
		SET 
		locations.address = ?,
		locations.floor = ?
		WHERE
		locations.id = ?",array(
			$this->input->post('loc_name_'.$location_id),
			$this->input->post('loc_floor_'.$location_id),
			$location_id
		));
		$this->usefulmodel->insert_audit("Cохранён адрес размещения");
	}

	############################
	############################
	#		Операторы системы
	############################
	public function admins_list_get() {
		$result = $this->db->query("SELECT 
		admins.id,
		admins.user,
		admins.rank,
		IF(admins.rank = 0, 'Оператор', 'Администратор') AS userrank,
		IF(LENGTH(admins.description), CONCAT('(', admins.description,')'), '') AS `desc`,
		admins.active,
		CONCAT_WS(' ', users.name_f,  CONCAT(LEFT(users.name_i,  1), '.', LEFT(users.name_o,  1), '.')) AS supervisorfio,
		CONCAT_WS(' ', users1.name_f, CONCAT(LEFT(users1.name_i, 1), '.', LEFT(users1.name_o, 1), '.')) AS adminfio
		FROM
		admins
		LEFT OUTER JOIN users ON (admins.supervisor = users.id)
		LEFT OUTER JOIN users users1 ON (admins.base_id = users1.id)
		WHERE NOT users1.fired
		ORDER BY
		admins.rank DESC,
		`desc`,
		supervisorfio,
		adminfio");
		$output = array();

		if ($result->num_rows()) {
			foreach ( $result->result() as $row ) {
				$classes = array();
				($row->rank)
					? array_push( $classes, "warning" )
					: '';
				(!$row->active) 
					? array_push( $classes, "muted" )
					: '';
				$class = (sizeof($classes))
					? 'class="'.implode($classes, " ").'"'
					: '';
				
				$string = '<tr '.$class.'>
				<td><i class="icon-user"></i>&nbsp;<strong>'.$row->user.'</strong></td>
				<td>'.$row->adminfio.' '.$row->desc.'</td>
				<td>'.$row->supervisorfio.'</td>
				<td>'.$row->userrank.'</td>
				<td><button type="submit" class="btn btn-mini btn-primary" name="showUser" value="'.$row->id.'">Показать</button></td>
				</tr>';
				array_push($output,$string);
			}
		}
		return implode($output,"\n");
	}

	public function new_password(){
		$result = $this->db->query("UPDATE 
		admins 
		SET 
		admins.password = ?
		WHERE
		admins.id = ?",array(
			"secret".$this->input->post("newpassword"),
			$this->input->post("userToSave")
		));
		$this->db->query("INSERT INTO audit (audit.author,audit.query,audit.desc) VALUES (?,?,?)",array($this->session->userdata('admin_id'),$this->db->last_query(),"Задан новый пароль"));
	}

	public function admin_data_get(){
		$row = array(
			'adminrank'  => '',
			'uid'        => $this->session->userdata('uid'),
			'base_id'    => 0,
			'supervisor' => 0,
			'user'       => '',
			'description'=> ''
		);
		$result = $this->db->query("SELECT
		admins.user,
		admins.rank,
		admins.description,
		admins.supervisor,
		admins.base_id
		FROM
		admins
		WHERE
		admins.id = ?", array(
			$this->input->post("showUser")
		));
		if ($result->num_rows()){
			$row = $result->row_array();
			$row['adminrank'] = ($row['rank']) ? ' checked="checked"' : "";
		}
		//print $row['uid'];
		$result = $this->db->query("SELECT
		users.id,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio
		FROM
		users
		WHERE users.sman
		AND NOT users.fired
		ORDER BY fio");
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row2) {
				$selected = ( $row2->id == $this->session->userdata('uid') ) ? ' selected="selected"': '';
				$string = '<option value="'.$row2->id.'"'.$selected.'>'.$row2->fio.'</option>';
				array_push($output, $string);
			}
			$row['followers'] = implode($output,"\n");
		}
		
		$result = $this->db->query("SELECT
		users.id,
		users.login,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS `fio`
		FROM
		users
		WHERE
		NOT users.fired
		ORDER BY `fio`");
		if ($result->num_rows()){
			$output=array("<option value=0>выберите кандидата</option>");
			foreach($result->result() as $row2){
				$string='<option login="'.$row2->login.'" value="'.$row2->id.'"'.(($row2->id == $row['base_id']) ? " selected" : "").'>'.$row2->fio.'</option>';
				array_push($output,$string);
			}
			$row['candidates'] = implode($output,"\n");
		}

		$result = $this->db->query("SELECT 
		CONCAT_WS(' ',users.name_f,users.name_i,users.name_o) AS fio,
		users.id
		FROM
		users
		WHERE
		users.superv
		AND NOT users.fired
		ORDER BY fio");
		if ($result->num_rows()){
			$output = array("<option value=0>выберите руководителя</option>");
			foreach ($result->result() as $row2) {
				$string="<option value=".$row2->id.(($row2->id == $row['supervisor']) ? " selected" : "").">".$row2->fio."</option>";
				array_push($output, $string);
			}
			$row['sups'] = implode($output, "\n");
		}
		return $row;
	}

	public function admin_data_save(){
		$rank = ($this->input->post('rank')) ? 1 : 0;
		if($this->input->post("newAdmin")){
			//$this->output->enable_profiler(TRUE);
			//return false;

			$this->db->query("INSERT INTO
			admins (
				admins.user,
				admins.password,
				admins.rank,
				admins.active,
				admins.description,
				admins.supervisor,
				admins.base_id
			) VALUES ( ?, ?, ?, ?, ?, ?, ? )", array(
				$this->input->post('login'),
				"secret".$this->input->post('newpassword'),
				$rank,
				1,
				$this->input->post('description'),
				$this->input->post('supervisor'),
				$this->input->post('candidate')
			));
			$this->usefulmodel->insert_audit("Добавлен новый оператор ML-Console");
		}
		if($this->input->post("saveAdmin")){
			$this->db->query("UPDATE
				admins 
				SET
				admins.user        = ?,
				admins.rank        = ?,
				admins.active      = ?,
				admins.description = ?,
				admins.supervisor  = ?,
				admins.base_id     = ?
				WHERE
				admins.id          = ?", array(
					$this->input->post('login'),
					$rank,
					1,
					$this->input->post('description'),
					$this->input->post('supervisor'),
					$this->input->post('candidate'),
					$this->input->post('userToSave')
			));
			$this->usefulmodel->insert_audit("Cохранён оператор ML-Console");
		}
	}
	############################
	############################
	#		Подразделения
	############################
	public function dept_list_get($dept){
		$result = $this->db->query("SELECT
		CONCAT('<option value=',
		`departments`.id,
		IF(`departments`.`id` = ?, ' selected>','>'),
		`departments`.dn,
		'</option>') as options
		FROM
		`departments`
		ORDER BY
		`departments`.dn",array($dept));
		$output = array("<option value=0>Выберите подразделение</option>");

		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		return implode($output,"\n");
	}

	public function dept_data_get($dept){
		$output = array(
			'id' => 0,
			'dn' => "",
			'dn_blank' => "",
			'service' => 0,
			'chief' => 0,
			'alias' => "",
			'cred' => "",
			'parent' => "",
			'actual' => "",
			'zakaz'=> ""
		);
		$result = $this->db->query("SELECT 
		`departments`.id,
		`departments`.dn_blank,
		`departments`.dn,
		`departments`.service,
		`departments`.alias,
		`departments`.cred,
		`departments`.parent,
		`departments`.chief,
		`departments`.zakaz,
		`departments`.actual
		FROM
		`departments`
		WHERE
		departments.id = ?",array($dept));
		if ($result->num_rows()){
			$output = $result->row_array();
		}

		$output['actual'] = form_dropdown('actual',array("1"=>"Действует", "0"=>"Упразднён"),$output['actual'], 'class="span12" id = "actual"');

		$result = $this->db->query("SELECT 
		CONCAT(
		'<option value=',
		`departments`.id,
		IF(`departments`.`id` = ?, ' selected>','>'),
		`departments`.dn,
		'</option>') as options
		FROM
		`departments`
		ORDER BY `departments`.dn",array($output['parent']));
		$parents = array();
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($parents ,$row->options);
			}
		}
		$output['parent'] = implode($parents,"\n");

		$result = $this->db->query("SELECT DISTINCT
		CONCAT(
		'<option value=',
		`admins`.`id`,
		IF(`admins`.`id` = ?, ' selected>','>'),
		CONCAT(`admins`.user,' (',`admins`.`description`,')'),
		'</option>') as options
		FROM
		`admins`
		WHERE `admins`.`active`
		GROUP BY `admins`.`supervisor`",array($output['service']));
		$curators = array();
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($curators ,$row->options);
			}
		}
		$output['curator'] = implode($curators,"\n");

		$result = $this->db->query("SELECT
		CONCAT(
		'<option value=',
		`staff`.`id`,
		IF(`staff`.`id` = ?, ' selected>','>'),
		`staff`.`staff`,
		'</option>') as options
		FROM
		`staff`
		ORDER BY `staff`.`staff`",array($output['chief']));
		$chief = array();
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($chief ,$row->options);
			}
		}
		$output['chief'] = implode($chief,"\n");
		return $output;
	}

	public function dept_data_save() {
		$dep_qfn = str_replace('"', '&quot;', $this->input->post("dep_qfn"));
		$dep_fn  = str_replace('"', '&quot;', $this->input->post("dep_fn"));
		$zakaz   = str_replace('"', '&quot;', $this->input->post("zakaz"));
		$dep_req = str_replace('"', '&quot;', $this->input->post("dep_req"));
		if($this->input->post('saveDept')){
			$this->db->query("UPDATE departments
			SET 
			departments.dn = ?,
			departments.dn_blank = ?,
			departments.service = ?,
			departments.alias = ?,
			departments.cred = ?,
			departments.parent = ?,
			departments.chief = ?,
			departments.zakaz = ?,
			departments.actual = ?
			WHERE
			departments.id = ?",array(
				$dep_qfn,
				$dep_fn,
				$this->input->post("curator"),
				$this->input->post("shortname"),
				$dep_req,
				$this->input->post("dep_parent"),
				$this->input->post("dep_dn"),
				$zakaz,
				$this->input->post("actual"),
				$this->input->post("depToSave")
			));
			$this->usefulmodel->insert_audit("Cохранёно описание подразделения");
		}
		if($this->input->post('newDept')){
			$this->db->query("INSERT INTO
			departments (
				departments.dn,
				departments.dn_blank,
				departments.service,
				departments.alias,
				departments.cred,
				departments.parent,
				departments.chief,
				departments.zakaz,
				departments.actual
			) VALUES (?,?,?,?,?,?,?,?,?)",array(
				$dep_qfn,
				$dep_fn,
				$this->input->post("curator"),
				$this->input->post("shortname"),
				$dep_req,
				$this->input->post("dep_parent"),
				$this->input->post("dep_dn"),
				$zakaz,
				$this->input->post("actual"),
				$this->input->post("depToSave")
			));
			$this->usefulmodel->insert_audit("Добавлено описание подразделения");
		}
	}


	############################
	############################
	#		Должности
	############################
	private function staff_list_get($staff) {
		$result = $this->db->query("SELECT
		`staff`.id,
		`staff`.staff
		FROM
		`staff`
		ORDER BY staff", array($staff));
		$output = array('<option value="0">Выберите должность</option>');

		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$selected = ($row->id == $staff) ? ' selected="selected"': "";
				$string   = '<option value="'.$row->id.'"'.$selected.'>'.$row->staff.'</option>';
				array_push($output, $string);
			}
		}
		return implode($output,"\n");
	}

	public function staff_data_get($staff){
		$output = array(
			'id'    => "",
			'staff' => ""
		);
		$result = $this->db->query("SELECT 
		`staff`.id,
		`staff`.staff
		FROM
		`staff`
		WHERE
		staff.id = ?", array($staff));
		if ($result->num_rows()){
			$output = $result->row_array();
		}
		$output['staff_list'] = $this->staff_list_get($staff);
		return $output;
	}

	public function staff_data_save() {
		if($this->input->post('saveStaff')){
			$this->db->query("UPDATE departments
			SET 
			staff.staff = ?
			WHERE
			staff.id = ?",array($this->input->post("staff"),$this->input->post("staffToSave")));
			$this->usefulmodel->insert_audit("Cохранёно описание должности");
		}
		if($this->input->post('newStaff')){
			$this->db->query("INSERT INTO
			staff (
				staff.staff
			) VALUES (?)", array($this->input->post("staff")));
			$this->usefulmodel->insert_audit("Добавлено описание должности");
		}
	}

	############################
	############################
	#		Утилиты
	############################
	public function no_cache(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 
	}

}

/* End of file refmodel.php */
/* Location: ./application/models/refmodel.php */