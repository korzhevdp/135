<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse extends CI_Controller {
	function __construct(){
		parent::__construct();
	}

	public function userlist($print){
		$out = array('<table class="table table bordered">');
		$result = $this->db->query("SELECT 
		`users`.id,
		CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) fio
		FROM
		`users`
		WHERE
		NOT `users`.`fired` AND
		`users`.`id` NOT IN( SELECT DISTINCT `arm_bind`.`uid` FROM `arm_bind`)
		ORDER BY fio");
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$string = '<tr>
				<td>
					<label for="m'.$row->id.'" style="cursor:pointer"><i class="icon-user"></i>&nbsp;&nbsp;&nbsp;'.$row->fio.'</label>
				</td>
				<td>
					<input type="checkbox" name="users[]" id="m'.$row->id.'" value="'.$row->id.'">
				</td>
				</tr>';
				array_push($out, $string);
			}
		}
		if ($print) {
			print  implode($out, "\n")."</table>";
			return true;
		}
		return implode($out, "\n")."</table>";
	}

	public function pc_list_get($print){
		//print_r($arm_ak);
		$uid = $this->input->post("uid");
		$ak_list = array('<a href="#" class="btn btn-small" id="toPCGrid" ref="'.$uid.'">Диаграмма движения ПК</a><hr>');
		$result = $this->db->query("SELECT Distinct
			hash_items.hostname,
			hash_items.id,
			hash_items.active,
			`hosts`.uid
			FROM
			hash_items
			INNER JOIN `hosts` ON (hash_items.hostname = `hosts`.hostname)
			WHERE
			(`hosts`.uid = ?)
			ORDER BY hash_items.active DESC, hash_items.hostname", array($uid));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$class = ($row->active) ? "btn-info" : "";
				$state = ($row->active) ? "Эксплуатируется" : "Выведен";
				array_push( $ak_list, '<button style="width:155px;margin-bottom:4px;" ref="'.$row->id.'" title="'.$state.'" sc="'.$class.'" class="btn btn-mini pcItem '.$class.'">'.$row->hostname.'</button>' );
			}
		}
		if($print){
			print implode($ak_list, "\n");
		} else {
			return implode($ak_list, "\n");
		}
	}

	public function pc_grid($userid = 0, $print = 0){
		$userid = ($userid) ? $userid : ( ($this->input->post("userSelector") ) ? $this->input->post("userSelector") : 0);
		if(!$userid){
			return "";
		}
		$user   = "";
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o) AS `fio`,
		`users`.host,
		`departments`.dn
		FROM
		`departments`
		INNER JOIN `users` ON (`departments`.id = `users`.dep_id)
		WHERE `users`.`id` = ?", array($userid));
		if($result->num_rows()){
			$row     = $result->row();
			$user    = "<h4>".$row->fio."&nbsp;&nbsp;<small>".$row->dn."</small></h4><br><br>";
			$refhost = strtoupper($row->host);
		}

		$input  = array();
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
		ORDER BY hash_items.id", array($userid));
		if ($result->num_rows()) {
			foreach($result->result() as $row){
				if(!isset($input[$row->all_md5])){
					$input[$row->all_md5] = array();
				}
				$class  = (($row->hostname == $refhost) ? 'btn-success' : '');
				$class  = ($row->active) ? $class : 'btn-inverse';
				$string = '<span class="btn btn-mini '.$class.' pcflowtab" style="min-width:120px;margin-top:7px;" ref="'.$row->id.'" title="Переименован: '.$row->ts.'. Конфигурация № '.$row->id.'">'.$row->hostname.'</span>';
				array_push($input[$row->all_md5], $string);
			}
		}
		foreach ($input as $val) {
			array_push($output, '<div style="display:block;clear:both;padding-bottom:7px;border-bottom:1px dotted #A6A6A6">'.implode($val, ' <i class="icon-arrow-right"></i> ').'</div>');
		}
		if (!$print) {
			return $user.implode($output, "");
		}
		print $user.implode($output,"");

	}

	public function conf_get($print = 0){
		$string = "";
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
		hash_items.all_md5
		FROM
		hash_items
		WHERE `hash_items`.`id` = ?", array($this->input->post("ref")));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<table id="tbd'.$row->id.'" class="table-arm table table-bordered table-condensed table-striped'.(($row->active) ? '' : ' muted').'">
				<tr>
					<td colspan=2>
						АК: <strong>'.$row->hostname.'</strong><small class="offset1">Дата сканирования '.$row->date.'</small>
						<div class="btn-group pull-right">
							<a href="#" class="btn btn-warning btn-mini'.(($row->active) ? ' locker' : ' unlocker').'" ref="'.$row->id.'">'.(($row->active) ? 'Деактивировать' : 'Активировать').'</a>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						Инвентарный номер
					</td>
					<td>
						<input type="text" id="inv'.$row->id.'" value="'.$row->inv_number.'" style="margin-bottom:0px;line-height:14px;font-size:14px;height:14px;">
						<button type="button" class="btn btn-mini btn-warning invsaver" ref="'.$row->id.'">Сохранить</button>
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
		}
		if($print){
			print $string;
		}else{
			return $string;
		}
	}

	public function lockpc($conf_id){
		$this->db->query("UPDATE hash_items SET hash_items.active = 0 WHERE hash_items.id = ?", array($conf_id));
	}

	public function unlockpc($conf_id){
		$this->db->query("UPDATE hash_items SET hash_items.active = 1 WHERE hash_items.id = ?", array($conf_id));
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
}
/* End of file warehouse.php */
/* Location: ./application/controllers/warehouse.php */