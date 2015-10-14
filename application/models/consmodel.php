<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Consmodel extends CI_Model {
	function __construct(){
		parent::__construct();	// Call the Model constructor
	}

	public function searchform_get(){
		$form = array();
		$this->load->helper('form');
		$output = array();
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id, IF(departments.id = ?,' selected',''),' >',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn",array($this->input->post('dep_id')));
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$form['depts'] = implode($output,"\n");
		}
		
		$output = array();
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',`staff`.id, IF(`staff`.id = ?,' selected',''),' >',`staff`.staff,'</option>') AS options
		FROM
		`staff`
		ORDER BY `staff`.`staff`",array($this->input->post('staff_id')));
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$form['staffs'] = implode($output,"\n");
		}
		/*
		$output=array("<option value=0>Выберите здание</option>");
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',locations.id, IF(`locations`.id = ?,' selected',''),' >',locations.address,'</option>') AS options
		FROM locations
		WHERE `locations`.parent = 0 AND
		`locations`.id <> 0
		ORDER BY `locations`.`address`",array($this->input->post('office')));
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$form['location'][0] = implode($output,"\n");
		}

		$output=array("<option value=0>Выберите помещение</option>");
		$result = $this->db->query("SELECT 
		`locations`.`id`,
		`locations`.`address`,
		`locations`.`parent`,
		CASE
			WHEN ASCII(RIGHT(`address`, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`address`, '-'), 16, '0')
			ELSE LPAD(`address`, 16, '0') END AS `vsort`
		FROM `locations`
		WHERE `locations`.id <> 0 AND
		`locations`.`parent` = ?
		ORDER BY `locations`.`parent`, `vsort`",array($this->input->post('office')));
		if ($result->num_rows()){
			foreach($result->result() as $row){
				$selected = ($row->id == $this->input->post('office2')) ? 'selected="selected"' : '';
				array_push($output,'<option value='.$row->id.' '.$selected.'>'.$row->address.'</option>');
			}
			$form['location'][1] = implode($output,"\n");
		}
		*/
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
		if ($result->num_rows()){
			foreach($result->result_array() as $row){
				if($row['parent'] == 0){
					$row['parent'] = $row['id'];
				}
				if(!isset($input[$row['parent']])){
					$input[$row['parent']] = array();
				}
				$selected = ($row['id'] == $this->input->post("office")) ? 'selected="selected"' : '';
				array_push($input[$row['parent']], '<option value='.$row['id'].' '.$selected.'>'.$row['address'].'</option>');
			}
			foreach($input as $key=>$val){
				array_push($output, implode($val, "\n"));
			}
			$form['location'] = implode($output,"\n");
		}

		$output=array();
		$result = $this->db->query("SELECT
		CONCAT('<option value=',resources.id, IF(resources.id = ?,' selected',''),' >',resources.name,' [', `departments`.alias, ']</option>') AS options
		FROM
		`departments`
		INNER JOIN resources ON (`departments`.id = resources.owner)
		WHERE
		(resources.active)
		ORDER BY
		resources.name",array($this->input->post('res')));
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$form['res'] = implode($output,"\n");
		}

		$form['inet'] = form_dropdown("inet",array(0 => "Несущественно", 1 => "Есть", 2 => "Нет"), $this->input->post('inet'), 'class="span12"');
		$form['fired'] = form_dropdown("fired",array(0 => "Включить уволенных", 1 => "Исключить уволенных", 2 => "Только уволенные"), $this->input->post('fired'), 'class="span12"');

		return $form;
	}

	public function search_perform(){
		//$this->output->enable_profiler(TRUE);

		$conditions = array();
		$results    = array();
		$results['num'] = "";

		if($this->input->post('name')){
			if(strlen($this->input->post('name'))){
				if(preg_match("/[a-z0-9]/i",$this->input->post('name'))){
					array_push($conditions,"`users`.host LIKE '".$this->input->post('name')."%'");
				}else{
					array_push($conditions,"CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) LIKE '%".$this->input->post('name')."%'");
				}
			}else{
				array_push($conditions,"CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) LIKE '%'");
			}
		}

		($this->input->post('dep_id')) 
			? array_push($conditions,"`users`.dep_id = ".$this->input->post('dep_id')) 
			: "" ;

		($this->input->post('staff_id')) 
			? array_push($conditions,"`users`.dep_id = ".$this->input->post('staff_id')) 
			: "" ;

		($this->input->post('fired')) 
			? ($this->input->post('fired') == 2) 
				? array_push($conditions,"`users`.fired = 1")
				: array_push($conditions,"`users`.fired = 0")
			: "" ;
		// поиск по размещению.


		if ($this->input->post('office')){
			$result = $this->db->query("SELECT 
			`locations`.parent
			FROM
			`locations`
			WHERE `locations`.id = ?
			LIMIT 1", array($this->input->post('office')));
			if($result->num_rows()){
				$row = $result->row();
				if($row->parent){
					array_push($conditions, "`locations`.id = ".$this->input->post('office'));
				}else{
					array_push($conditions, "`locations`.parent = ".$this->input->post('office'));
				}
			}
		}


		$collection = array();
		$result = $this->db->query("SELECT 
		users.id
		FROM
		departments
		INNER JOIN users ON (departments.id = users.dep_id)
		INNER JOIN staff ON (users.staff_id = staff.id)
		INNER JOIN locations ON (users.office_id = locations.id)
		".(sizeof($conditions) ? "WHERE " : " ").implode($conditions, "\nAND "));
		if($result->num_rows()){
			foreach ($result->result() as $row){
				array_push($collection, (string) $row->id);
			}
		}
		$emails = array();
		if($this->input->post('email') && strlen($this->input->post('email'))){
			$result = $this->db->query("SELECT DISTINCT
			`resources_items`.uid
			FROM
			`resources_pid`
			INNER JOIN `resources_items` ON (`resources_pid`.item_id = `resources_items`.id)
			WHERE
			`resources_pid`.`pid` = 1 AND
			`resources_items`.`ok` AND
			NOT `resources_items`.`del` AND
			NOT `resources_items`.`exp` AND
			`resources_pid`.`pid_value` LIKE '".$this->input->post('email')."%'");
			if($result->num_rows()){
				foreach ($result->result() as $row){
					array_push($emails, (string) $row->uid);
				}
			}
			$collection = array_intersect($emails,$collection);
		}

		$inets = array();
		if($this->input->post('inet')){
			$result = $this->db->query("SELECT DISTINCT 
			resources_items.uid
			FROM
			resources_items
			INNER JOIN `users` ON (resources_items.uid = `users`.id)
			WHERE
			(resources_items.ok) AND 
			(NOT (resources_items.del)) AND 
			(NOT (resources_items.`exp`)) AND 
			(resources_items.rid = 101) AND
			NOT `users`.`fired`");
			if($result->num_rows()){
				foreach ($result->result() as $row){
					array_push($inets, (string) $row->uid);
				}
			}
			// в зависимости от режима поиска по наличию сети интернет - производится вычисление массива.
			if($this->input->post('inet') == "1"){
				$collection = array_intersect($inets, $collection);
			}
			if($this->input->post('inet') == "2"){
				$collection = array_diff($collection, $inets);
			}
		}

		$ips = array();
		// поиск по диапазонам IP-адресов
		if($this->input->post('ip') && strlen($this->input->post('ip'))){
			$ip = explode(".", preg_replace("/[\.\,юб]/i", ".", $this->input->post('ip')));
			$ipnext = array();
			foreach($ip as $key=>$val){
				if(!strlen($val)){
					unset($ip[$key]);
				}
			}
			if( (int) sizeof($ip) == 1){
				$ipstart = implode($ip).".0";
				$ipend = implode($ip).".255";
			}
			if( (int) sizeof($ip) == 2){
				$ipstart = implode($ip,".");
				$ipend = implode($ip,".");
			}
			$result = $this->db->query("SELECT DISTINCT
			`resources_items`.uid
			FROM
			`resources_pid`
			INNER JOIN `resources_items` ON (`resources_pid`.item_id = `resources_items`.id)
			WHERE
			`resources_pid`.`pid` = 6
			AND `resources_items`.`ok`
			AND NOT `resources_items`.`del`
			AND NOT `resources_items`.`exp` 
			AND `resources_pid`.`pid_value` BETWEEN INET_ATON(?) AND INET_ATON(?)", array("192.168.".$ipstart, "192.168.".$ipend) );

			if($result->num_rows()){
				foreach ($result->result() as $row){
					array_push($ips, (string) $row->uid);
				}
			}
			$collection = array_intersect($ips, $collection);
		}

		$res = array();
		if($this->input->post('res')){
			$result = $this->db->query("SELECT DISTINCT 
			resources_items.uid
			FROM
			resources_items
			INNER JOIN `users` ON (resources_items.uid = `users`.id)
			WHERE
			(resources_items.ok) AND 
			(NOT (resources_items.del)) AND 
			(NOT (resources_items.`exp`)) AND 
			(resources_items.rid = ?) AND
			NOT `users`.`fired`", array($this->input->post('res')));
			if($result->num_rows()){
				foreach ($result->result() as $row){
					array_push($res, (string) $row->uid);
				}
				//print "<br><br><br><br><br>".$result->num_rows();
			}
			$collection = array_intersect($res,$collection);
		}
		
		$results['found'] = (sizeof($collection)) ? $this->build_result($collection) : '<h3 class="muted">Ничего не найдено</h3>';
		
		$results['num'] = sizeof($collection);
		//print_r($collection);
		return $this->load->view('console/searchresults', $results, true);
	}

	public function build_result($collection){
		$output = array();
		$inets = array();
		$emails = array();
		$result = $this->db->query("SELECT DISTINCT 
		resources_items.uid
		FROM
		resources_items
		INNER JOIN `users` ON (resources_items.uid = `users`.id)
		WHERE
		resources_items.uid IN (".implode($collection,",").") AND
		(resources_items.ok) AND 
		(NOT (resources_items.del)) AND 
		(NOT (resources_items.`exp`)) AND 
		(resources_items.rid = 101) AND
		NOT `users`.`fired`");
		if($result->num_rows()){
			foreach ($result->result() as $row){
				array_push($inets, (string) $row->uid);
			}
		}

		$result = $this->db->query("SELECT DISTINCT
		CONCAT(resources_pid.pid_value, '@arhcity.ru') AS pid_value,
		resources_items.uid
		FROM
		resources_items
		INNER JOIN resources_pid ON (resources_items.id = resources_pid.item_id)
		WHERE
		(resources_items.uid IN (".implode($collection,",").")) AND 
		(resources_pid.pid = 1) AND 
		(resources_items.ok) AND 
		(NOT (resources_items.`exp`)) AND 
		(NOT (resources_items.del))
		GROUP BY `resources_pid`.`pid_value`");
		if($result->num_rows()){
			foreach ($result->result() as $row){
				(!isset($emails[$row->uid])) ? $emails[$row->uid]=array() : "";
				array_push($emails[$row->uid], $row->pid_value);
			}
		}

		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		TRIM(CONCAT_WS(' ', locations1.address, locations.address)) AS address,
		CONCAT_WS(' ', users1.name_f, users1.name_i, users1.name_o) AS serviceman,
		departments.dn,
		staff.staff,
		users.bir,
		users.id,
		users.memo,
		users.air,
		users.fired,
		users.login,
		users.sman,
		users.phone
		FROM
		users
		INNER JOIN departments ON (users.dep_id = departments.id)
		INNER JOIN staff ON (users.staff_id = staff.id)
		INNER JOIN locations ON (users.office_id = locations.id)
		INNER JOIN locations locations1 ON (locations1.id = locations.parent)
		LEFT OUTER JOIN users users1 ON (users.service = users1.id)
		WHERE
		(users.id IN (".implode($collection,",")."))
		ORDER BY CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o)");
		//print $this->db->last_query();
		if($result->num_rows()){
			foreach($result->result_array() as $row){
				(in_array((string) $row['id'],$inets)) ? $row['inet'] = 1 : $row['inet'] = 0;
				$row['emails'] = "";
				foreach($emails as $key=>$addrs){
					if($row['id'] == $key) {
						$row['emails'] = implode($addrs,", ");
					}
				}
				$string = $this->load->view('console/plate', $row, true);
				array_push($output,$string);
			}
		}
		return implode($output,"\n\n");
	}

	public function pc_grid($userid = 0, $print = 0){
		$userid = ($userid) ? $userid : ( ($this->input->post("userSelector") ) ? $this->input->post("userSelector") : 0);
		if(!$userid){
			return "";
		}
		$user = "";
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o) AS `fio`,
		`users`.host,
		`departments`.dn
		FROM
		`departments`
		INNER JOIN `users` ON (`departments`.id = `users`.dep_id)
		WHERE `users`.`id` = ?", array($userid));
		if($result->num_rows()){
			$row = $result->row();
			$user = "<h4>".$row->fio."&nbsp;&nbsp;<small>".$row->dn."</small></h4><br><br>";
			$refhost = strtoupper($row->host);
		}

		$input = array();
		$output = array();
		$result = $this->db->query("SELECT DISTINCT 
		hash_items1.id,
		hash_items1.active,
		hash_items1.hostname,
		hash_items1.all_md5,
		hosts1.uid,
		DATE_FORMAT(hash_items1.ts, '%d.%m.%Y') as ts
		FROM
		`hosts`
		INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
		INNER JOIN hash_items hash_items1 ON (hash_items1.all_md5 = hash_items.all_md5)
		INNER JOIN `hosts` hosts1 ON (hash_items1.hostname = hosts1.hostname)
		WHERE
		(`hosts`.uid = ?)
		ORDER BY hash_items.hostname
		", array($userid));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($input[$row->all_md5])){
					$input[$row->all_md5] = array();
				}
				$class = (($row->hostname == $refhost) ? 'btn-success' : '');
				$class = (!$row->active) ? 'btn-inverse' : $class;
				$string = '<span class="btn '.$class.' pcflowtab" style="min-width:150px;margin-top:7px;" ref="'.$row->id.'" href="/console/pcflow/'.$row->uid.'" title="Переименован: '.$row->ts.'. Конфигурация № '.$row->id.'">'.$row->hostname.'</span>';
				array_push($input[$row->all_md5], $string);
			}
		}
		foreach($input as $val){
			array_push($output, '<div style="display:block;clear:both;padding-bottom:7px;border-bottom:1px dotted #E6E6E6">'.implode($val, ' <i class="icon-arrow-right"></i> ').'</div>');
		}
		if(!$print){
			return $user.implode($output,"");
		}else{
			print $user.implode($output,"");
		}
	}

	public function user_pcconf_get($conf_id){
		$string = "Не удалось отобразить данные конфигурации";
		$result = $this->db->query("SELECT 
		hash_items.id,
		hash_items.inv_number,
		CONCAT_WS(' ', hash_items.baseboard_manufacturer, hash_items.baseboard_product, hash_items.baseboard_serialnumber, hash_items.baseboard_version) AS mb,
		hash_items.computersystemproduct_name AS system,
		hash_items.bios_description AS bios,
		CONCAT_WS(' ', hash_items.processor_description, hash_items.processor_name) AS processor,
		hash_items.physicalmemory_capacity AS ram,
		CONCAT_WS(' ', hash_items.networkcard_description, hash_items.networkcard_macaddress) AS nic,
		hash_items.diskdrive_pnpdeviceid AS hdd,
		hash_items.cdromdrive_caption AS cdrom,
		hash_items.videocontroller_caption AS video,
		DATE_FORMAT(hash_items.ts, '%d.%m.%Y') AS `date`,
		hash_items.active,
		hash_items.hostname,
		hash_items.all_md5,
		CONCAT_WS('-', SUBSTR(hash_items.label,2,5), SUBSTR(hash_items.label,7,3), SUBSTR(hash_items.label,10,3), SUBSTR(hash_items.label,13,3)) AS label,
		`hosts`.uid
		FROM
		`hosts`
		INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
		WHERE
		(hash_items.id = ?)",array($conf_id));
		if($result->num_rows()){
			$row = $result->row();
			$string='<table id="tbd'.$row->id.'" class="table-arm table table-bordered table-condensed table-striped'.(($row->active) ? '' : ' muted').'">
				<tr>
					<td colspan=2>
						АК: <strong>'.$row->hostname.'</strong><small class="offset1">Дата сканирования '.$row->date.'</small>
						<div class="pull-right">
							<a href="/console/'.(($row->active) ? 'lockpc' : 'unlockpc').'/'.$row->id.'/'.$row->uid.'" class="btn btn-warning">'.(($row->active) ? 'Деактивировать' : 'Активировать').'</a>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						Инвентарный номер
					</td>
					<td>
						<input type="text" value="'.$row->inv_number.'" style="margin-bottom:0px;line-height:14px;font-size:14px;height:14px;">
					</td>
				</tr>
				<tr>
					<td>
						Инвентарный номер наклейки ОС
					</td>
					<td>
						<input type="text" value="'.$row->label.'" style="margin-bottom:0px;line-height:14px;font-size:14px;height:14px;">
					</td>
				</tr>
				<tr>
					<td class="span3">#</td>
					<td>'.$row->all_md5.'</td>
				</tr>
				<tr>
					<td class="span3">М. плата</td>
					<td>'.$row->mb.'</td>
				</tr>
				<tr>
					<td>Система</td>
					<td>'.$row->system.'</td>
				</tr>
				<tr>
					<td>BIOS</td><small></small>
					<td>'.$row->bios.'</td>
				</tr>
				<tr>
					<td>Процессор</td>
					<td>'.$row->processor.'</td>
				</tr>
				<tr>
					<td>Объём RAM</td>
					<td>'.$row->ram.' МБ.</td>
				</tr>
				<tr>
					<td>Сетевая карта</td>
					<td>'.$row->nic.'</td>
				</tr>
				<tr>
					<td>Жёсткий диск</td>
					<td>'.$row->hdd.'</td>
				</tr>
				<tr>
					<td>Оптический привод</td>
					<td>'.$row->cdrom.'</td>
				</tr>
				<tr>
					<td>Видеокарта</td>
					<td>'.$row->video.'</td>
				</tr>
				<tr>
					<td>Дата сканирования</td>
					<td>'.$row->date.'</td>
				</tr>
				</table>';
		}
		return $string;
	}

}

/* End of file consmodel.php */
/* Location: ./application/models/consmodel.php */