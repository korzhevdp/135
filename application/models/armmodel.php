<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Armmodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function showdep($dep=0) {
		$dep    = ($dep) ? $dep : $this->input->post('department');
		$out    = array();
		$result = $this->db->query("SELECT
		`departments`.dn,
		`departments`.id,
		IF(departments.id = ?, 1,0) AS selected
		FROM
		`departments`
		WHERE `departments`.actual
		ORDER BY 
		`departments`.dn", array($dep));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$sel = ($row->selected) ? ' selected="selected"' : "" ;
				$string = '<option value="'.$row->id.'"'.$sel.'>'.$row->dn.'</option>';
				array_push( $out, $string );
			}
		}
		$act['depts'] = implode($out, "\n");
		$act['arms'] = "";
		if ($dep){
			$out       = array();
			$armusers  = array();
			$arm_ak    = array();
			
			$result = $this->db->query("");
			if($result->num_rows()){
				foreach($result->result() as $row){
					
				}
			}
			
			$armresult = $this->db->query("SELECT
			arm.arm_id,
			CONCAT_WS(' ', locations1.address, locations.address) AS address,
			staff.staff,
			users.id AS uid,
			CONCAT(users.name_f, ' ', SUBSTR(users.name_i, 1, 1), '. ', SUBSTR(users.name_o, 1, 1),'.') AS fio
			FROM
			arm
			LEFT OUTER JOIN locations ON (arm.location_id = locations.id)
			LEFT OUTER JOIN locations locations1 ON (locations1.id = locations.parent)
			LEFT OUTER JOIN staff ON (arm.staff_id = staff.id)
			LEFT OUTER JOIN users ON (arm.uid = users.id)
			WHERE
			(arm.dep_id = ?)
			GROUP BY
			users.id
			ORDER BY
			address", array($dep));
			if($armresult->num_rows()){
				foreach($armresult->result() as $row){
					//$pcs    = '<img src="/images/computer.png" style="width:32px;height:32px;border:none;" alt="">';
					//$lic    = '<img src="/images/l_man.png" style="width:32px;height:32px;border:none;" alt="">';
					$string = '<table class="table table-condensed table-bordered table-striped table-hover" style="margin-top:15px;margin-bottom:15px;">
					<tr>
						<td colspan=4>Должность: <b>'.$row->staff.'</b></td>
					</tr>
					<tr>
						<td style="width:20%;">
							<h5>'.$row->fio.'</h5>
							<i class="icon-map-marker"></i>'.$row->address.'
						</td>
						<td>
							<h4 class="muted">Компьютеры</h4>
						</td>
						<td>
							<h4 class="muted">Программы</h4>
						</td>
						<td>
							<h4 class="muted">Лицензии</h4>
						</td>
					</tr>
					</table>';
					array_push($out, $string);
				}
			}
			$act['arms'] = implode($out, "\n");
		}

		return $this->load->view('arm/arm.php', $act, true);
	}

	public function create_arm() {
		if($this->input->post("loc") && $this->input->post("staff") && $this->input->post("dep")){
			$result = $this->db->query("INSERT INTO arm (
				arm.location_id,
				arm.staff_id,
				arm.dep_id
			) VALUES (?, ?, ?)", array(
				$this->input->post("loc"),
				$this->input->post("staff"),
				$this->input->post("dep")
			));
		}else{
			print "недостаточно данных";
		}
	}

	public function addptoarm() {
		$input = array();
		//$this->output->enable_profiler(TRUE);
		foreach($this->input->post('users') as $val){
			array_push($input,"(".$this->input->post('arm_id').", ".$val.")");
		}
		$result = $this->db->query("INSERT INTO
		arm_bind(
			arm_bind.arm_id,
			arm_bind.uid
		) VALUES ".implode($input,", "));
		$this->load->helper('url');
		redirect("arm/showdep/".$this->input->post('department'));
	}

	public function get_inv_units() {
		$output = array();
		$text = iconv('utf-8', 'windows-1251', $this->input->post("text"));
		$result = $this->db->query("SELECT DISTINCT
		`ak_warehouse`.inv,
		`ak_warehouse`.`type`,
		`ak_warehouse`.`serial`,
		`ak_warehouse`.`fio`
		FROM
		`ak_warehouse`
		WHERE
		LOWER(CONCAT_WS(' ', `ak_warehouse`.inv, `ak_warehouse`.`type`, `ak_warehouse`.`serial`, `ak_warehouse`.`fio`)) LIKE LOWER('%".$text."%')
		GROUP BY `ak_warehouse`.inv
		HAVING MIN(`ak_warehouse`.`id`) and length(`ak_warehouse`.inv)
		ORDER BY `ak_warehouse`.inv ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->inv.'">'.$row->inv.' ('.$row->serial.') - '.$row->type.' - '.$row->fio.'</option>';
				array_push($output, $string);
			}
		}
		print implode($output, "\n");
	}

	public function get_inv_unit() {
		$output = array();
		$suboutput = array();
		$result = $this->db->query("SELECT
		ak_warehouse.id,
		DATE_FORMAT(ak_warehouse.purchase, '%d.%m.%Y') AS purchase,
		DATE_FORMAT(ak_warehouse.guarantee_end, '%d.%m.%Y') AS guarantee_end,
		ak_warehouse.supplier,
		ak_warehouse.`type`,
		ak_warehouse.inv,
		ak_warehouse.uid,
		ak_warehouse.name,
		ak_warehouse.qty,
		ak_warehouse.serial,
		ak_warehouse.dep,
		ak_warehouse.fio,
		ak_warehouse.room
		FROM
		ak_warehouse
		WHERE
		(ak_warehouse.`inv` = ?)
		ORDER BY ak_warehouse.`id`", array( $this->input->post("inv", true) ));
		if ($result->num_rows()) {
			foreach($result->result_array() as $row) {
				$string = $this->load->view("arm/warehouseitem", $row, true);
				if (preg_match("/(лавиат|мышь)/im", $row['type'])) {
					array_push($suboutput, $string);
				} else {
					array_push($output, $string);
				}
			}
			$info = $result->row(0);
		}
		print "data =  { 
			devcontent : '".implode($output, "")."<hr>".implode($suboutput, "")."',
			info: {
				guarantee_start : '".$info->purchase."',
				guarantee_end   : '".$info->guarantee_end."',
				supplier        : '".$info->supplier."',
				uid             :  ".$info->uid.",
				room            : '".$info->room."',
				serial          : '".$info->serial."',
				inv             : '".$info->inv."'
			}
		}";
	}

	private function make_whtypes_list() {
		$output = array();
		$result = $this->db->query("SELECT DISTINCT `ak_warehouse`.`type` FROM `ak_warehouse` ORDER BY `ak_warehouse`.type ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->type.'">'.$row->type."</option>";
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t");
	}

	private function make_whnames_list() {
		$output = array();
		$result = $this->db->query("SELECT DISTINCT `ak_warehouse`.`name` FROM `ak_warehouse` ORDER BY `ak_warehouse`.name ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->name.'">'.$row->name.'</option>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t");
	}

	private function make_users_list() {
		$output = array();
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		users.id,
		users.dep_id,
		`departments`.alias
		FROM
		`departments`
		INNER JOIN users ON (`departments`.id = users.dep_id)
		ORDER BY
		fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->id.'" dep="'.$row->dep_id.'">'.$row->fio.' ['.$row->alias.']</option>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t");
	}

	public function show_invunits($id = 0) {
		$id = ($this->input->post("invnum")) ? $this->input->post("invnum") : $id ;
		$act    = array(
			'typelist'   => $this->make_whtypes_list(),
			'namelist'   => $this->make_whnames_list(),
			'users'      => $this->make_users_list(),
			'cur_inv'    => $id,
			'additional' => '',
			'invfilter'  => $this->input->post('invfilter'),
			'invfilter2' => $this->input->post('invfilter2')
		);
		return $this->load->view('arm/units.php', $act, true);
	}

	private function get_wh_types() {
		$output = array();
		$result = $this->db->query("SELECT DISTINCT
		`ak_warehouse`.`type`
		FROM
		`ak_warehouse`
		ORDER BY `ak_warehouse`.type ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->type.'">'.$row->type."</option>";
				array_push($output, $string);
			}
		}
		return implode($output, "\n\t");
	}

	private function get_wh_names() {
		$output = array();
		$result = $this->db->query("SELECT DISTINCT
		`ak_warehouse`.`name`
		FROM
		`ak_warehouse`
		ORDER BY `ak_warehouse`.name ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value=\''.$row->name.'\'>'.$row->name.'</option>';
				array_push($output, $string);
			}
		}
		$act['namelist'] = implode($output, "\n\t");
	}

	public function show_warehouse($id = 0) {
		$id = ($this->input->post("invnum")) ? $this->input->post("invnum") : $id ;
		$output = array();
		$suboutput = array();

		$act    = array(
			'invvalue' => '',
			'invlist'  => '',
			'typelist' => $this->get_wh_types(),
			'namelist' => $this->get_wh_names(),
			'info'     => '',
			'cur_inv'  => $id
		);


		if ($id) {
			$act['cur_inv'] = $id;
			$result = $this->db->query("SELECT 
			ak_warehouse.id,
			ak_warehouse.purchase,
			ak_warehouse.guarantee_end,
			ak_warehouse.supplier,
			ak_warehouse.`type`,
			ak_warehouse.inv,
			ak_warehouse.uid,
			ak_warehouse.name,
			ak_warehouse.qty,
			ak_warehouse.serial,
			ak_warehouse.dep,
			ak_warehouse.fio,
			ak_warehouse.room
			FROM
			ak_warehouse
			WHERE
			(ak_warehouse.`inv` = ?)", array($id));
			if($result->num_rows()){
				foreach($result->result_array() as $row){
					$string = $this->load->view("arm/warehouseitem", $row, true);
					if(preg_match("/(лавиат|мышь)/im", $row['type'])){
						array_push($suboutput, $string);
					}else{
						array_push($output, $string);
					}
				}
				$info = $result->row(0);
				$row['users'] = $this->make_users_list();
				$act['info']  = $this->load->view('arm/wareinfo', $info, true);
					
			}
		}


		$act['invfilter']  = $this->input->post('invfilter');
		$act['invfilter2'] = $this->input->post('invfilter2');
		$act['contents']   = implode($output, "\n");
		$act['additional'] = implode($suboutput, "\n");
		return $this->load->view('arm/warehouse.php', $act, true);
	}

	public function param_lists_get(){
		$output = array();
		$input  = array();
		$result = $this->db->query("SELECT DISTINCT 
		scan_items.hostname
		FROM
		scan_items
		ORDER BY scan_items.hostname ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string= '<option value="'.$row->hostname.'">'.$row->hostname.'</option>';
				array_push($input, $string);
			}
		}
		$output['pcnames'] = implode($input, "\n");
		$input  = array();
		$result = $this->db->query("SELECT DISTINCT 
		scan_items.category
		FROM
		scan_items
		ORDER BY scan_items.category ASC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string= '<option value="'.$row->category.'">'.$row->category.'</option>';
				array_push($input, $string);
			}
		}
		$output['cat'] = implode($input, "\n");
		return $output;
	}

	public function param_table_get(){
		$output = array();
		//return $this->input->post("cat")." ".$this->input->post("host")." ".$this->input->post("par");
		if(!strlen($this->input->post("host")) && !strlen($this->input->post("cat")) && !strlen($this->input->post("par"))) {
			return "Не указано ни одного компьютера, категории или параметра поиска";
		}
		if(!strlen($this->input->post("host")) && strlen($this->input->post("cat")) && !strlen($this->input->post("par"))) {
			return "Уточните параметр поиска";
		}
		$sort = "";
		switch($this->input->post("sort")){
			case 1 :
				$sort = "ORDER by scan_items.hostname ASC";
			break;
			case 2 :
				$sort = "ORDER by scan_items.scandate DESC";
			break;
			case 3 :
				$sort = "ORDER by scan_items.value ASC";
			break;
			case 4 :
				$sort = "ORDER by `departments`.dn ASC";
			break;
			case 5 :
				$sort = "ORDER by `scan_items`.category ASC";
			break;
			case 6 :
				$sort = "ORDER by `scan_items`.category_name ASC";
			break;
		}
		if(strlen($this->input->post("host"))){
			$result = $this->db->query("SELECT  DISTINCT
			scan_items.hostname,
			scan_items.category,
			scan_items.category_name,
			scan_items.value,
			DATE_FORMAT(scan_items.scandate, '%d.%m.%Y') AS scandate,
			`departments`.dn
			FROM
			`hosts`
			RIGHT OUTER JOIN scan_items ON (`hosts`.hostname = scan_items.hostname)
			LEFT OUTER JOIN `users` ON (`hosts`.uid = `users`.id)
			LEFT OUTER JOIN `departments` ON (`users`.dep_id = `departments`.id)
			WHERE `scan_items`.`hostname` = ?
			AND   `scan_items`.`category` LIKE '".$this->input->post("cat")."%'
			AND   `scan_items`.`category_name` LIKE '".$this->input->post("par")."%'
			".$sort."
			LIMIT 5000", array($this->input->post("host")));
			//print $this->db->last_query();
			if($result->num_rows()){
				foreach($result->result() as $row){
					$string = "<tr><td>".$row->category."</td><td>".$row->category_name."</td><td>".$row->value."</td></tr>";
					array_push($output, $string);
				}
				array_unshift($output, '<table class="table table-condensed table-bordered">
				<tr>
				<th style="width:300px;"><a href="#" id="sortByCat">Категория</a></th>
				<th style="width:300px;"><a href="#" id="sortByParam">Параметр</a></th>
				<th><a href="#" id="sortByVal">Значение</a></th>
				</tr>');
				return '<h4 id="systemData">'.$row->hostname."&nbsp;&nbsp;&nbsp;&nbsp;<small>".$row->dn." ".$row->scandate."</small></h4>".implode($output, "\n")."</table>";
			}
		}else{
			//print 2;
			$result = $this->db->query("SELECT DISTINCT
			scan_items.hostname,
			scan_items.category,
			scan_items.category_name,
			scan_items.value,
			DATE_FORMAT(scan_items.scandate, '%d.%m.%Y') AS scandate,
			`departments`.dn
			FROM
			`hosts`
			RIGHT OUTER JOIN scan_items ON (`hosts`.hostname = scan_items.hostname)
			LEFT OUTER JOIN `users` ON (`hosts`.uid = `users`.id)
			LEFT OUTER JOIN `departments` ON (`users`.dep_id = `departments`.id)
			WHERE `scan_items`.`category` = '".$this->input->post("cat")."'
			AND   `scan_items`.`category_name` = '".$this->input->post("par")."'
			".$sort."
			LIMIT 5000");
			//print $this->db->last_query();
			if($result->num_rows()){
				foreach($result->result() as $row){
					$string = "<tr><td>".$row->hostname."</td><td>".$row->dn."</td><td>".$row->value."</td><td>".$row->scandate."</td></tr>";
					array_push($output, $string);
				}
				array_unshift($output, '<h4>'.$row->category_name.'</h4><table class="table table-condensed table-bordered">
				<tr>
				<th style="width:100px;"><a href="#" id="sortByHost">Компьютер</a></th>
				<th><a href="#" id="sortByDep">Подразделение</a></th>
				<th><a href="#" id="sortByVal">Значение</a></th>
				<th style="width:80px;"><a href="" id="sortByDate">Дата</a></th>
				</tr>');
				return '<h3 id="systemData">&nbsp;&nbsp;&nbsp;</h3>'.implode($output, "\n")."</table>";
			}
		}
		//return $output;
	}

	public function subparam_table_get(){
		$output = array();
		$result = $this->db->query("SELECT DISTINCT
		scan_items.category_name
		FROM
		scan_items
		WHERE `scan_items`.`category` = ?", array($this->input->post("cat")));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<option value="'.$row->category_name.'">'.$row->category_name.'</option>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n");
	}


### AJAX FX
	public function inv_unit_save(){
		//$this->output->enable_profiler(TRUE);
		//return false;
		$address = "";
		$fio     = "";
		$depname = "";
		$staff   = "";
		$result  = $this->db->query("SELECT 
		CONCAT_WS(' ', locations1.address, locations.address) AS address,
		CONCAT(users.name_f, ' ', UPPER(LEFT(users.name_i, 1)),'.', UPPER(LEFT(users.name_o, 1)),'.') AS fioinit,
		`departments`.dn,
		users.office_id,
		users.staff_id,
		users.dep_id,
		users.fired
		FROM
		users
		INNER JOIN locations ON (users.office_id = locations.id)
		INNER JOIN locations locations1 ON (locations1.id = locations.parent)
		INNER JOIN `departments` ON (users.dep_id = `departments`.id)
		WHERE
		(users.id = ?)
		LIMIT 1", array($this->input->post('receiver')));
		if($result->num_rows()){
			$row = $result->row(0);
			$address = $row->address;
			$depname = $row->dn;
			$depid   = $row->dep_id;
			$officeid= $row->office_id;
			$fio     = $row->fioinit;
			$staff   = $row->staff_id;
			$active   = ($row->fired) ? 0 : 1;
			// сначала сохраняем саму инвентарную единицу.
			$result = $this->db->query("UPDATE
			`ak_warehouse`
			SET
			`ak_warehouse`.purchase      = ?,
			`ak_warehouse`.guarantee_end = ?,
			`ak_warehouse`.fio           = ?,
			`ak_warehouse`.room          = ?,
			`ak_warehouse`.uid           = ?,
			`ak_warehouse`.dep           = ?
			WHERE
			`ak_warehouse`.inv           = ?", array(
				$this->input->post('datestart'),
				$this->input->post('dateend'),
				$fio,
				$this->input->post('room'),
				$this->input->post('receiver'),
				$depname,
				$this->input->post('invNum')
			));
			$result = $this->db->query("SELECT 
			`arm`.`arm_id`
			FROM
			`arm`
			WHERE
			arm.`ak_inv` = ?", array($this->input->post('invNum')));
			if($result->num_rows()){
				$result = $this->db->query("UPDATE
				arm
				SET
				arm.staff_id    = ?,
				arm.dep_id      = ?,
				arm.location_id = ?,
				arm.uid         = ?,
				arm.active      = ?
				WHERE
				ak_inv          = ?", array(
					$staff,
					$depid,
					$officeid,
					$this->input->post('receiver'),
					$active,
					$this->input->post('invNum')
				));
			}else{
				$result = $this->db->query("INSERT INTO
				arm(
					arm.staff_id,
					arm.dep_id,
					arm.location_id,
					arm.uid,
					arm.ak_inv,
					arm.active
				)
				VALUES( ?, ?, ?, ?, ?, ? )", array(
					$staff,
					$depid,
					$officeid,
					$this->input->post('receiver'),
					$this->input->post('invNum'),
					$active
				));
			}
		}
		$this->load->helper("url");
		redirect("arm/warehouse");
	}

	public function dev_save(){
		$result = $this->db->query("UPDATE
		`ak_warehouse`
		SET
		`ak_warehouse`.qty      = ?,
		`ak_warehouse`.serial   = ?,
		`ak_warehouse`.`type`   = ?,
		`ak_warehouse`.name     = ?
		WHERE `ak_warehouse`.id = ?", array(
			$this->input->post("qty"),
			$this->input->post("serial"),
			iconv('utf-8', 'windows-1251', $this->input->post("devtype")),
			iconv('utf-8', 'windows-1251', $this->input->post("devname")),
			$this->input->post("dev")
		));
	}

### AJAX broadcast informer FX

}
/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */