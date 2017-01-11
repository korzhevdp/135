<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Licensemodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function hostlist_get($uid=0, $dep_id=0){
		//print "-".$uid."--".$dep_id."-";
		$output = array(
			'users' => array('<option value="0">Выберите пользователя</option>'),
			'depts' => array('<option value="0">Выберите подразделение</option>')
		);
		$depmark = ($dep_id) ? "WHERE users.dep_id = ".$dep_id : "";
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) as fio,
		users.`id` AS `uid`
		FROM
		users ".$depmark."
		ORDER BY fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output['users'], '<option value="'.$row->uid.'"'.(($row->uid == $uid) ? ' selected="selected"' : '').'>'.$row->fio.'</option>');
			}
		}
		$result = $this->db->query("SELECT
		departments.dn AS `name`,
		departments.`id`
		FROM
		departments
		ORDER BY name");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output['depts'], '<option value="'.$row->id.'"'.(($row->id == $dep_id) ? ' selected="selected"' : '').'>'.$row->name.'</option>');
			}
		}
		
		$output['depts'] = implode($output['depts'],"\n");
		$output['users'] = implode($output['users'],"\n");
		return $output;
	}

	public function userlicenses_get($uid=0){
		$input	= array();
		$output	= array();
		$fired	= "";
		$result	= $this->db->query("SELECT DISTINCT
		ak_licenses.product_name,
		ak_licenses.product_serial,
		ak_licenses.product_key,
		RIGHT(ak_licenses.product_key, 5) AS pkshort,
		ak_licenses.active,
		ak_licenses.manual,
		ak_licenses.verify_pk,
		ak_licenses.item_id,
		ak_licenses.id,
		`users`.`fired`,
		DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') as scandate,
		ak_licenses.hostname,
		CONCAT_WS('-',substr(ak_licenses.label,2,5), substr(ak_licenses.label,7,3), substr(ak_licenses.label,10,3), substr(ak_licenses.label,13,3)) as label
		FROM
		`hosts`
		RIGHT OUTER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
		INNER JOIN `users` ON (`users`.`id` = `hosts`.`uid`)
		WHERE
		(`hosts`.uid = ?) AND
		NOT `hosts`.noise
		GROUP BY `ak_licenses`.`id`
		ORDER BY `ak_licenses`.`hostname` ASC, `ak_licenses`.`scandate` DESC", array($uid));

		if($result->num_rows()){
			foreach($result->result() as $row){
				if (!strlen($fired)) {
					$fired = ($row->fired) ? '<span class="btn-danger btn-small">Уволен(а)</span>' : '';
				} 
				$class		= array();
				$annotation	= array("#".$row->id);
				$props		= array();
				$bindto		= "";
				$intinfo	= "";

				$label = (strpos($row->product_name, 'indows') === FALSE) 
					? "" 
					: '<small class="muted">для получения нужно <a href="#" refid="'.$row->id.'" ref="'.$row->pkshort.'" class="button-take btn btn-mini">изъять из пула</a></small>';
				$takefrompool = "";
				if (!isset($input[$row->hostname])){
					$input[$row->hostname] = array();
				}
				if($row->manual){ 
					array_push($class, "info");
					array_push($annotation, "добавлена вручную");
					array_push($props, '<span class="btn-info btn-small">РУЧН</span>');
					$takefrompool = '';
					$bindto = ' class="hide"';
					$intinfo = "Назначены вручную: ";
					if(stristr($row->product_name,"indows")){
						$intinfo.=' <span class="btn-info">Windows</span>';
					}
					if(stristr($row->product_name,"ffic")){
						$intinfo.=' <span class="btn-info">Office</span>';
					}
				}else{
					array_push($annotation, "обнаружена при сканировании");
				}
				if(!$row->active) {
					array_push($class, "muted hide");
					array_push($annotation, "неактивна");
					array_push($props, '<span class="btn-small">НЕАКТ</span>');
					$bindto = ' class="hide"';
					$takefrompool = ' class="hide"';
					$label = "";
				}else{
					array_push($class, "success");
					array_push($annotation, "активна");
					array_push($props, '<span class="btn-primary btn-small">АКТ</span>');
				}
				if(strpos($row->product_serial,"-OEM-")){
					array_push($annotation, "OEM");
					array_push($props, '<span class="btn-danger btn-small">OEM</span>');
				}
				if($row->item_id && $row->active){
					$label = ($this->session->userdata('rank') == 1) ? $row->label : " Получите в отделе ОСА";
					array_push($props, '<span class="btn-success btn-small">ПУЛ</span>');
				}
				if(stristr($row->product_name,"windows") && strlen($row->verify_pk) == 5){
					array_push($annotation, "Верификация: ".$row->verify_pk);
				}
				$string = '<tr class="'.implode($class, " ").'" title="'.implode($annotation,", ").'">
					<td>'.$row->product_name.'<br>'.$row->product_key.'<br>Номер наклейки: <b>'.$label.'</b> <span class="hide">/ # '.$row->id.'</span><div class="pull-right">'.implode($props,"&nbsp;").'</div></td>
					<td style="text-align:center;vertical-align:middle;">'.$row->scandate.'</td>
					<td style="vertical-align:middle;">
						<div class="btn-group">
							<a class="btn btn-primary '.(($row->active) ? 'button-reject' : 'button-recall').'" style="width:66%" href="#" title="'.(($row->active) ? 'Отозвать лицензию' : 'Отменить отзыв').'" ref="'.$row->id.'">
							'.(($row->active) ? 'Отозвать' : 'Отменить отзыв').'
							</a>
							<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li'.$bindto.'><a href="#" class="button-bide" title="Заполнить обнаруженную лицензию данными из назначенной вручную" ref="'.$row->id.'">Связать с...</a></li>
								<li'.$takefrompool.'><a href="#" class="button-take" title="Задействовать лицензию из пула по ключу" refid="'.$row->id.'" ref="'.$row->pkshort.'">Взять из пула...</a></li>
							</ul>
						</div>
					</td>
				</tr>';
			array_push($input[$row->hostname], $string);
			}
			foreach($input as $key=>$val){
				array_push($output,'<h4>'.$key.'  '.$fired.' 
					<div class="btn-group pull-right" style="margin-bottom:10px;">
							<a class="btn btn-large" href="#" title="" ref="'.$row->id.'">'.$key.'</a>
							<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="#" class="button-order" ref="'.$key.'" title="Назначить произвольную лицензию из пула вручную">Назначить лицензию</a></li>
								<li><a href="#" class="button-convert" ref="'.$key.'" title="Добавить в список лицензий ПО из списка установленного на ПК">Перевести в лицензию</a></li>
								<li><a href="#" title="Возврат всех лицензий в пул и исключение из АРМ">Списание</a></li>
							</ul>
						</div>
				</h4>
				'.$intinfo.'
				<table class="table table-condensed table-bordered table-hover table-licenses">
				<tr>
					<th class="span8">Детали лицензий</th>
					<th class="span1">Дата</th>
					<th class="span3">Действия</th>'.
				"</tr>".implode($val,"\n").'</table>');
			}
		}
		return implode($output);
	}

	public function serverlicenses_get($uid=0){
		$input	= array();
		$output	= array();
		$fired	= "";
		$result	= $this->db->query("SELECT DISTINCT
		ak_licenses.product_name,
		ak_licenses.product_serial,
		ak_licenses.product_key,
		RIGHT(ak_licenses.product_key, 5) AS pkshort,
		ak_licenses.active,
		ak_licenses.manual,
		ak_licenses.verify_pk,
		ak_licenses.item_id,
		ak_licenses.id,
		`users`.`fired`,
		DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') as scandate,
		ak_licenses.hostname,
		CONCAT_WS('-', SUBSTR(ak_licenses.label, 2, 5), SUBSTR(ak_licenses.label, 7, 3), SUBSTR(ak_licenses.label, 10, 3), SUBSTR(ak_licenses.label, 13, 3)) AS label
		FROM
		`hosts`
		RIGHT OUTER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
		INNER JOIN `users` ON (`users`.`id` = `hosts`.`uid`)
		WHERE
		`hosts`.server = 1
		AND NOT `hosts`.noise
		GROUP BY `ak_licenses`.`id`
		ORDER BY `ak_licenses`.`hostname` ASC, `ak_licenses`.`scandate` DESC");

		if($result->num_rows()){
			foreach($result->result() as $row){
				$class		= array();
				$annotation	= array("#".$row->id);
				$props		= array();
				$bindto		= "";
				$intinfo	= "";

				$label = (strpos($row->product_name, 'indows') === FALSE) 
					? "" 
					: '<small class="muted">для получения нужно <a href="#" refid="'.$row->id.'" ref="'.$row->pkshort.'" class="button-take btn btn-mini">изъять из пула</a></small>';
				$takefrompool = "";
				if (!isset($input[$row->hostname])) {
					$input[$row->hostname] = array();
				}
				if ($row->manual) {
					array_push($class, "info");
					array_push($annotation, "добавлена вручную");
					array_push($props, '<span class="btn-info btn-small">РУЧН</span>');
					$takefrompool = '';
					$bindto = ' class="hide"';
					$intinfo = "Назначены вручную: ";
					if (stristr($row->product_name, "indows")) {
						$intinfo.=' <span class="btn-info">Windows</span>';
					}
					if (stristr($row->product_name, "ffice")) {
						$intinfo.=' <span class="btn-info">Office</span>';
					}
				} else {
					array_push($annotation, "обнаружена при сканировании");
				}
				if ($row->active) {
					array_push($class, "success");
					array_push($annotation, "активна");
					array_push($props, '<span class="btn-primary btn-small">АКТ</span>');
				} else {
					array_push($class, "muted hide");
					array_push($annotation, "неактивна");
					array_push($props, '<span class="btn-small">НЕАКТ</span>');
					$bindto = ' class="hide"';
					$takefrompool = ' class="hide"';
					$label = "";
				}
				if(strpos($row->product_serial, "-OEM-")) {
					array_push($annotation, "OEM");
					array_push($props, '<span class="btn-danger btn-small">OEM</span>');
				}
				if($row->item_id && $row->active){
					$label = ($this->session->userdata('rank') === 1) ? $row->label : " Получите в отделе ОСА";
					array_push($props, '<span class="btn-success btn-small">ПУЛ</span>');
				}
				if(stristr($row->product_name,"windows") && strlen($row->verify_pk) === 5){
					array_push($annotation, "Верификация: ".$row->verify_pk);
				}
				$data = array(
					'label'       => $label,
					'poolswitch'  => $takefrompool,
					'bindto'      => $bindto,
					'mainclass'   => implode($class, " "),
					'maintitle'   => implode($annotation, ", "),
					'properties'  => implode($props, "&nbsp;"),
					'productname' => $row->product_name,
					'productkey'  => $row->product_key,
					'scandate'    => $row->scandate,
					'pkshort'     => $row->pkshort,
					'license'     => $row->id,
					'link'        => ($row->active)
						? '<a class="btn btn-primary button-reject" style="width:66%" href="#" title="Отозвать лицензию" ref="'.$row->id.'">Отозвать</a>'
						: '<a class="btn btn-primary button-recall" style="width:66%" href="#" title="Отменить отзыв" ref="'.$row->id.'">Отменить отзыв</a>',
					
				);
				$string = $this->load->view("license/licensecontrols", $data, true);
				array_push($input[$row->hostname], $string);
			}
			foreach($input as $key=>$val) {
				$data = array(
					'pcref' => $row->id,
					'ref'   => $key,
					'info'  => $intinfo,
					'data'  => implode($val,"\n")
				);
				array_push($output, $this->load->view('license/pclicenses', $data, true));
			}
		}
		return implode($output).'<script type="text/javascript" src="/jscript/lsmc.js"></script>';
	}

	public function deptlicenses_get($depid=0){
		$input	= array();
		$output	= array();
		$fired	= "";
		$users = array();
		
		$result	= $this->db->query("SELECT 
		users.id
		FROM
		users
		WHERE
		`users`.`dep_id` = ?
		AND NOT (users.fired)", array($depid));
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($users, $row->id);
				//print $row->id;
			}
		}

		$result	= $this->db->query("SELECT DISTINCT
		ak_licenses.product_name,
		ak_licenses.product_serial,
		ak_licenses.product_key,
		RIGHT(ak_licenses.product_key, 5) AS pkshort,
		ak_licenses.active,
		ak_licenses.manual,
		ak_licenses.verify_pk,
		ak_licenses.item_id,
		ak_licenses.id,
		`users`.`fired`,
		users.id AS uid,
		DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') as scandate,
		ak_licenses.hostname,
		CONCAT_WS('-',substr(ak_licenses.label,2,5), substr(ak_licenses.label,7,3), substr(ak_licenses.label,10,3), substr(ak_licenses.label,13,3)) as label
		FROM
		`hosts`
		RIGHT OUTER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
		INNER JOIN `users` ON (`users`.`id` = `hosts`.`uid`)
		WHERE
		(`hosts`.uid IN(".implode($users, ",").")) AND
		NOT `hosts`.noise
		GROUP BY `ak_licenses`.`id`
		ORDER BY `ak_licenses`.`hostname` ASC, `ak_licenses`.`scandate` DESC");

		if($result->num_rows()){
			foreach($result->result() as $row){
				if (!strlen($fired)) {
					$fired = ($row->fired) ? '<span class="btn-danger btn-small">Уволен(а)</span>' : '';
				} 
				$class		= array();
				$annotation	= array("#".$row->id);
				$props		= array();
				$bindto		= "";
				$intinfo	= "";

				$label = (strpos($row->product_name, 'indows') === FALSE) 
					? "" 
					: '<small class="muted">для получения нужно <a href="#" class="button-take btn btn-mini" refid="'.$row->id.'" ref="'.$row->pkshort.'">изъять из пула</a></small>';
				$takefrompool = "";
				if (!isset($input[$row->hostname])){
					$input[$row->hostname] = array();
				}
				if($row->manual){ 
					array_push($class, "info");
					array_push($annotation, "добавлена вручную");
					array_push($props, '<span class="btn-info btn-small">РУЧН</span>');
					$takefrompool = '';
					$bindto = ' class="hide"';
					$intinfo = "Назначены вручную: ";
					if(stristr($row->product_name,"indows")){
						$intinfo.=' <span class="btn-info">Windows</span>';
					}
					if(stristr($row->product_name,"ffic")){
						$intinfo.=' <span class="btn-info">Office</span>';
					}
				}else{
					array_push($annotation, "обнаружена при сканировании");
				}
				if(!$row->active) {
					array_push($class, "muted hide");
					array_push($annotation, "неактивна");
					array_push($props, '<span class="btn-small">НЕАКТ</span>');
					$bindto = ' class="hide"';
					$takefrompool = ' class="hide"';
					$label = "";
				}else{
					array_push($class, "success");
					array_push($annotation, "активна");
					array_push($props, '<span class="btn-primary btn-small">АКТ</span>');
				}
				if(strpos($row->product_serial,"-OEM-")){
					array_push($annotation, "OEM");
					array_push($props, '<span class="btn-danger btn-small">OEM</span>');
				}
				if($row->item_id && $row->active){
					$label = ($this->session->userdata('rank') == 1) ? $row->label : " Получите в отделе ОСА";
					array_push($props, '<span class="btn-success btn-small">ПУЛ</span>');
				}
				if(stristr($row->product_name,"windows") && strlen($row->verify_pk) == 5){
					array_push($annotation, "Верификация: ".$row->verify_pk);
				}
				$string = '<tr class="'.implode($class, " ").'" title="'.implode($annotation,", ").'">
					<td>'.$row->product_name.'<br>'.$row->product_key.'<br>Номер наклейки: <b>'.$label.'</b> <span class="hide">/ # '.$row->id.'</span><div class="pull-right">'.implode($props,"&nbsp;").'</div></td>
					<td style="text-align:center;vertical-align:middle;">'.$row->scandate.'</td>
					<td style="vertical-align:middle;">
						<div class="btn-group">
							<a class="btn btn-primary '.(($row->active) ? 'button-reject' : 'button-recall').'" style="width:66%" href="#" title="'.(($row->active) ? 'Отозвать лицензию' : 'Отменить отзыв').'" ref="'.$row->id.'">
							'.(($row->active) ? 'Отозвать' : 'Отменить отзыв').'
							</a>
							<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li'.$bindto.'><a href="#" class="button-bide" title="Заполнить обнаруженную лицензию данными из назначенной вручную" ref="'.$row->id.'">Связать с...</a></li>
								<li'.$takefrompool.'><a href="#" class="button-take" title="Задействовать лицензию из пула по ключу" refid="'.$row->id.'" ref="'.$row->pkshort.'">Взять из пула...</a></li>
								<li><a href="/admin/users/'.$row->uid.'/5" title="перейти к пользователю" target="_blank">К пользователю</a></li>
							</ul>
						</div>
					</td>
				</tr>';
			array_push($input[$row->hostname], $string);
			}
			foreach($input as $key=>$val){
				array_push($output,'<h4>'.$key.' '.$fired.' 
					<div class="btn-group pull-right" style="margin-bottom:10px;">
							<a class="btn btn-large" href="#" title="" ref="'.$row->id.'">'.$key.'</a>
							<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="#" class="button-order" ref="'.$key.'" title="Назначить произвольную лицензию из пула вручную">Назначить лицензию</a></li>
								<li><a href="#" class="button-convert" ref="'.$key.'" title="Добавить в список лицензий ПО из списка установленного на ПК">Перевести в лицензию</a></li>
								<li><a href="#" title="Возврат всех лицензий в пул и исключение из АРМ">Списание</a></li>
							</ul>
						</div>
				</h4>
				'.$intinfo.'
				<table class="table table-condensed table-bordered table-hover table-licenses">
				<tr>
					<th class="span8">Детали лицензий</th>
					<th class="span1">Дата</th>
					<th class="span3">Действия</th>'.
				"</tr>".implode($val,"\n").'</table>');
			}
		}
		return implode($output);
	}

	public function get_related_licenses($pk = ""){
		$search = (strlen($pk)) ? " AND (inv_po_licenses_items.verify_pk = ?)" : " AND (inv_po_licenses_items.verify_pk = '')";
		$output = array('<table class="table table-bordered table-hover table-condensed">','<tr><th></th><th class="span9">Лицензия</th><th class="span2">Остаток</th></tr>');
		$result = $this->db->query("SELECT
		inv_po_types.name,
		inv_po_licenses_sets.id AS sid,
		inv_po_licenses_sets.`max`,
		inv_po_licenses.number,
		inv_po_licenses_items.value,
		inv_po_licenses_items.master,
		inv_po_licenses_items.id,
		inv_po_licensiars.name AS lname
		FROM
		inv_po_types
		INNER JOIN inv_po_licenses_items ON (inv_po_types.id = inv_po_licenses_items.type_id)
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		INNER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		WHERE
		(NOT (inv_po_licenses_sets.deleted))
		".$search, array($pk));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$differ = $row->max;
				$result2 = $this->db->query("SELECT COUNT(*) as differ
				FROM ak_licenses
				WHERE
				ak_licenses.active
				AND ak_licenses.item_id IN (
					SELECT 
					`inv_po_licenses_items`.`id`
					FROM 
					`inv_po_licenses_items` 
					WHERE `inv_po_licenses_items`.`set_id` = ?
				)", array($row->sid));
				if($result2->num_rows()){
					foreach($result2->result() as $row2){
						$differ -= $row2->differ;
					}
				}
				$ordertype = ($row->master) ? '<span class="btn-primary btn-small">ПРЯМ</span>' : '<span class="btn-warning btn-small">ДГРД</span>';
				$string = '<tr>
					<td style="text-align:center;vertical-align:middle;"><input type="radio" id="item'.$row->id.'" name="itm" class="itemsel" ref="'.$row->id.'"></td>
					<td><label for="item'.$row->id.'" style="cursor:pointer;">'.$row->name.'<br>'.$row->lname.', лиц. № '.$row->number.' <b class="hide">Item: '.$row->id.'</b>&nbsp;&nbsp;&nbsp;'.$ordertype.'</label></td>
					<td style="text-align:center;vertical-align:middle;"><label for="item'.$row->id.'" style="cursor:pointer;">'.($differ).'</label></td>
				</tr>';
				array_push($output, $string);
			}
		}
		array_push($output,'</table>');
		return implode($output,"\n");
	}

	public function get_all_licenses(){
		$output = array();
		$result = $this->db->query("SELECT
		inv_po_types.name,
		inv_po_licenses_sets.id AS sid,
		inv_po_licenses_sets.max,
		inv_po_licenses.number,
		inv_po_licenses_items.value,
		inv_po_licenses_items.master,
		inv_po_licenses_items.id,
		inv_po_licenses_items.verify_pk,
		inv_po_licensiars.name as licensiar
		FROM
		inv_po_types
		INNER JOIN inv_po_licenses_items ON (inv_po_types.id = inv_po_licenses_items.type_id)
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		INNER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		WHERE
		NOT inv_po_licenses_sets.deleted
		AND inv_po_licenses_items.type NOT IN ('KMS')
		ORDER BY
		inv_po_types.name, inv_po_licenses_items.master DESC");
		if ($result->num_rows()) {
			foreach ($result->result_array() as $row) {
				$result2 = $this->db->query("SELECT COUNT(*) AS differ
				FROM ak_licenses
				WHERE
				ak_licenses.item_id IN (
					SELECT 
					`inv_po_licenses_items`.`id`
					FROM 
					`inv_po_licenses_items` 
					WHERE `inv_po_licenses_items`.`set_id` = ?
				)", array($row['sid']));
				if ($result2->num_rows()) {
					foreach ($result2->result() as $row2) {
						$row['max'] -= $row2->differ;
					}
				}
				array_push($output, $this->load->view("license/listitems/getalllicenseslistitem", $row, true));
			}
		}
		return implode($output,"\n");
	}

	public function orderitem(){
		//$this->output->enable_profiler(TRUE);
		$result = $this->db->query("SELECT 
		inv_po_licenses_items.value,
		inv_po_types.name,
		inv_po_licenses_sets.label_starts +
		(SELECT COUNT(ak_licenses.id) FROM ak_licenses WHERE ak_licenses.item_id = ? AND `ak_licenses`.`active`) AS label
		FROM
		inv_po_types
		INNER JOIN inv_po_licenses_items ON (inv_po_types.id = inv_po_licenses_items.type_id)
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		WHERE
		(inv_po_licenses_items.id = ?)", array($this->input->post('itemid'), $this->input->post('itemid')));
		if($result->num_rows()){
			$row = $result->row(); 
			$this->db->query("INSERT INTO 
			ak_licenses (
				ak_licenses.product_name,
				ak_licenses.product_serial,
				ak_licenses.product_key,
				ak_licenses.hostname,
				ak_licenses.username,
				ak_licenses.os_version,
				ak_licenses.scandate,
				ak_licenses.md5,
				ak_licenses.manual,
				ak_licenses.active,
				ak_licenses.label
			) VALUES ( ?, ?, ?, ?, ?, '7+', NOW(), md5(NOW()), 1, 1, ? )", array(
				$row->name,
				'manual serial',
				$row->value,
				$this->input->post('akl'),
				$this->input->post('akl'),
				$row->label
			));
			return $row->name;
		}
		return " Нет данных ";
	}

	public function get_license_to_bide($lid){
		$output = array();
		$result = $this->db->query("SELECT 
		ak_licenses.product_name,
		ak_licenses.product_key
		FROM
		ak_licenses
		WHERE
		(ak_licenses.id = ?)",array($lid));
		if($result->num_rows()){
			$row=$result->row();
			array_push($output,"<h4>".$row->product_name." <small>".$row->product_key."</small></h4>");
		}
		array_push($output, '<table class="table table-bordered table-hover table-condensed">','<tr><th></th><th class="span9">Лицензия</th>');
		$result = $this->db->query("SELECT 
		ak_licenses.id,
		ak_licenses.product_name,
		ak_licenses.product_key
		FROM
		ak_licenses
		WHERE
		(ak_licenses.manual) AND
		ak_licenses.active AND
		(LOWER(ak_licenses.hostname) = (
			SELECT LOWER(ak_licenses.hostname)
			FROM ak_licenses
			WHERE ak_licenses.id = ?
		))", array($lid,$lid));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<tr>
					<td style="text-align:center;vertical-align:middle;"><input type="radio" id="item'.$row->id.'" name="itm" class="itemsel" ref="'.$row->id.'"></td>
					<td><label for="item'.$row->id.'" style="cursor:pointer;">'.$row->product_name.'<br>'.$row->product_key.'</label></td>
				</tr>';
				array_push($output,$string);
			}
		}
		array_push($output,'</table>');
		return implode($output,"\n");
	}

	public function bideitem(){
		$result = $this->db->query("SELECT 
		ak_licenses.product_key AS `pk`,
		ak_licenses.product_name AS `pn`,
		ak_licenses.label
		FROM ak_licenses
		WHERE ak_licenses.id = ?",array($this->input->post("itemid")));
		if($result->num_rows()){
			$row=$result->row();
			$result = $this->db->query("UPDATE 
			ak_licenses 
			SET 
			ak_licenses.product_key = ?,
			ak_licenses.product_name = ?,
			ak_licenses.label = ?
			WHERE 
			ak_licenses.id = ?", array($row->pk, $row->pn, $row->label, $this->input->post("akl")));

			$this->db->query("UPDATE 
			ak_licenses 
			SET 
			ak_licenses.active = 0
			WHERE 
			ak_licenses.id = ?", array($this->input->post("itemid")));
		}
	}
	
	public function full_stat_get(){
		$output = array();
		$output['general'] = array();
		$result = $this->db->query("SELECT 
		inv_po_licenses.id,
		inv_po_licenses.number,
		inv_po_licenses.program,
		DATE_FORMAT(inv_po_licenses.issue_date, '%d.%m.%Y') AS issue_date,
		inv_po_licenses.purchase_info,
		DATE_FORMAT(inv_po_licenses.purchase_date, '%d.%m.%Y') AS purchase_date,
		inv_po_licenses.active,
		inv_po_licensiars.name AS lname,
		inv_po_resellers.name AS rname,
		inv_po_resellers.address,
		IF(DATEDIFF(NOW(),inv_po_licenses.verify_date) < 180, 1,0) AS verified,
		DATE_FORMAT(inv_po_licenses.verify_date, '%d.%m.%Y') AS verify_date
		FROM
		inv_po_licenses
		INNER JOIN inv_po_resellers ON (inv_po_licenses.reseller_id = inv_po_resellers.id)
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		ORDER BY inv_po_licensiars.name, inv_po_licenses.number");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string='<tr>
					<td>'.(sizeof($output['general']) + 1).'</td>
					<td><a href="/licenses/statistics/'.$row->id.'"><b>'.$row->lname.'</b></a><br>'.$row->number.'<br><small class="muted">от '.$row->issue_date.'</small></td>
					<td>'.$row->purchase_date.'</td>
					<td>'.$row->purchase_info.'<br>'.$row->program.'<br><small class="muted">'.$row->rname.'</small></td>
					<td>
						'.(($row->active) ? '<span class="btn-primary btn-mini">АКТ</span>' : '<span class="btn-mini">НЕАКТ</span>').'
						'.(($row->verified) ? '<span class="btn-primary btn-mini btn-success" title="Наличие проверено '.$row->verify_date.'">ВЕР</span>' : '<span class="btn-mini btn-danger" title="Наличие не проверено начиная с '.$row->verify_date.'">НЕТ&nbsp;ВЕР</span>').'
					</td>
				</tr>';
				array_push($output['general'], $string);
			}
		}
		$output['general'] = implode($output['general'], "\n");
		return $this->load->view('license/licensestat', $output, true);
	}

	public function stat_get($lid = 0){
		if(!$lid){
			$this->load->helper('url');
			redirect('/licenses/statistics');
		}
		$input = array();
		$output = array();
		$licr = array();
		$resl = array();
		$result = $this->db->query("SELECT 
		inv_po_licenses.id,
		inv_po_licenses.number,
		inv_po_licenses.program,
		DATE_FORMAT(inv_po_licenses.issue_date, '%d.%m.%Y') AS issue_date,
		inv_po_licenses.purchase_info,
		DATE_FORMAT(inv_po_licenses.purchase_date, '%d.%m.%Y') AS purchase_date,
		inv_po_licenses.active,
		inv_po_licensiars.name AS lname,
		inv_po_resellers.id AS resid,
		inv_po_licensiars.id AS liid,
		inv_po_resellers.name AS rname,
		inv_po_resellers.address,
		IF(DATEDIFF(NOW(),inv_po_licenses.verify_date) < 180, 1,0) AS verified,
		DATE_FORMAT(inv_po_licenses.verify_date, '%d.%m.%Y') AS verify_date
		FROM
		inv_po_licenses
		INNER JOIN inv_po_resellers ON (inv_po_licenses.reseller_id = inv_po_resellers.id)
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		WHERE inv_po_licenses.id = ?", array($lid));
		if($result->num_rows()){
			$row = $result->row_array();
			$result2 = $this->db->query("SELECT inv_po_resellers.id, inv_po_resellers.name FROM inv_po_resellers ORDER by inv_po_resellers.name");
			if($result2->num_rows()){
				foreach($result2->result() as $row2){
					$selected = ($row2->id == $row['resid']) ? ' selected="selected"' : "";
					array_push($resl,'<option value='.$row2->id.' '.$selected.'>'.$row2->name.'</option>');
				}
			}
			$result2 = $this->db->query("SELECT inv_po_licensiars.id, inv_po_licensiars.name FROM inv_po_licensiars ORDER BY inv_po_licensiars.name");
			if($result2->num_rows()){
				foreach($result2->result() as $row2){
					$selected = ($row2->id == $row['liid']) ? ' selected="selected"' : "";
					array_push($licr,'<option value='.$row2->id.' '.$selected.' style="margin:0px;">'.$row2->name.'</option>');
				}
			}
			$row['lid']      = $lid;
			$row['licr']     = implode($licr,"\n");
			$row['resl']     = implode($resl,"\n");
			$row['stat1']    = ($row['active']) ? '<span class="btn-primary btn-mini">АКТ</span>' : '<span class="btn-mini">НЕАКТ</span>';
			$row['activate'] = '<span class="btn btn-inverse btn-mini" id="licAct" ref="'.$row['id'].'">'.(($row['active']) ? "Деактивировать" : "Активировать").'</span>';
			$row['stat2']    = ($row['verified']) ? '<span class="btn-primary btn-mini btn-success" title="Наличие проверено '.$row['verify_date'].'">ВЕР</span>' : '<span class="btn-mini btn-danger" title="Наличие не проверено начиная с '.$row['verify_date'].'">НЕТ&nbsp;ВЕР</span>';
			
			$string = $this->load->view('license/licenseparams', $row, true);
			array_push($output, $string);
		}

		$usecount = array();
		$result = $this->db->query("SELECT 
		inv_po_licenses_items.set_id,
		COUNT(`ak_licenses`.id) AS cnt
		FROM
		inv_po_licenses_items
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		INNER JOIN `ak_licenses` ON (inv_po_licenses_items.id = `ak_licenses`.item_id)
		WHERE
		NOT(`ak_licenses`.`manual`) AND
		`ak_licenses`.`active`
		GROUP BY
		inv_po_licenses_items.set_id");
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($usecount[$row->set_id])){$usecount[$row->set_id] = 0;}
				$usecount[$row->set_id] += $row->cnt;
			}
		}

		$result=$this->db->query("SELECT 
		inv_po_licenses_sets.`max`,
		inv_po_licenses_items.id AS itid,
		inv_po_types.name,
		inv_po_licenses_items.value,
		inv_po_licenses_sets.deleted,
		inv_po_licenses_items.set_id,
		inv_po_licenses_items.`type`,
		inv_po_licenses_sets.id,
		inv_po_licenses_items.master
		FROM
		inv_po_licenses
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses.id = inv_po_licenses_sets.license_id)
		INNER JOIN inv_po_licenses_items ON (inv_po_licenses_sets.id = inv_po_licenses_items.set_id)
		INNER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		WHERE
		(inv_po_licenses.id = ?) AND
		(inv_po_licenses_items.act)
		GROUP BY
		inv_po_licenses_items.id
		ORDER BY inv_po_licenses_items.set_id, inv_po_licenses_items.master DESC", array($lid));
		if($result->num_rows()){
			array_push($output,"<h4>Структура лицензии</h4>");
			foreach($result->result_array() as $row){
				if(!isset($usecount[$row['set_id']])){
					$usecount[$row['set_id']] = 0;
				}
				$row['cnt'] = $usecount[$row['set_id']];
				(!isset($input[$row['set_id']])) ? $input[$row['set_id']] = array() : "";
				$input[$row['set_id']]['deleted'] = ($row['deleted']) ? 1 : 0;
				$row['lid'] = $lid;
				$row['ismaster'] = ($row['master']) ? 'class="success"' : "";
				$row['remainder'] = ($row['max'] - $usecount[$row['set_id']]);
				$string = $this->load->view('license/itemparams', $row, true);
				array_push($input[$row['set_id']], $string);
			}
			foreach($input as $val){
				$data = array();
				$data['muted'] = ($val['deleted']) ? " hide" : '';
				unset($val['deleted']);
				$data['items'] = implode($val,"\n");
				$string = $this->load->view('license/setcontainer', $data, true);
				array_push($output, $string);
			}
			array_push($output,'<form method="post" id="licenceform" action="" class="hide" style="margin-bottom:80px;"></form>
			<script type="text/javascript" src="/jscript/lsmc.js"></script>');
		}
		return implode($output, "\n");
	}

	public function removeitem($item, $redirect){
		$this->db->query("UPDATE inv_po_licenses_items SET inv_po_licenses_items.act = 0 WHERE inv_po_licenses_items.id = ?", array($item));
		$this->load->helper("url");
		redirect("licenses/statistics/".$redirect);
	}

	public function save_license($lid){
		$this->db->query("SET lc_time_names = 'ru_RU'");
		$this->db->query("UPDATE 
		`inv_po_licenses` SET
		`inv_po_licenses`.licensiar_id = ?,
		`inv_po_licenses`.number = ?,
		`inv_po_licenses`.program = ?,
		`inv_po_licenses`.reseller_id = ?,
		`inv_po_licenses`.issue_date = ?,
		`inv_po_licenses`.purchase_info = ?,
		`inv_po_licenses`.purchase_date = ?
		WHERE
		`inv_po_licenses`.id = ?", array(
			$this->input->post("licr", true),
			$this->input->post("lnum", true),
			$this->input->post("prog", true),
			$this->input->post("resl", true),
			implode(array_reverse(explode(".", $this->input->post("dati", true))), "-"),
			$this->input->post("info", true),
			implode(array_reverse(explode(".", $this->input->post("datp", true))), "-"),
			$lid
		));
		$this->load->helper("url");
		redirect("licenses/statistics/".$lid);
	}

	public function verify_license($lid){
		$this->db->query("UPDATE `inv_po_licenses` SET
		`inv_po_licenses`.verified = 1,
		`inv_po_licenses`.verify_date = NOW()
		WHERE
		`inv_po_licenses`.id = ?",array($lid));
		$this->load->helper("url");
		redirect("licenses/statistics/".$lid);
	}

	public function makemaster($item, $redirect){
		$result = $this->db->query("SELECT `inv_po_licenses_items`.set_id FROM `inv_po_licenses_items` WHERE `inv_po_licenses_items`.id = ?", array($item));
		if($result->num_rows()){
			$row = $result->row();
			$this->db->query("UPDATE `inv_po_licenses_items` SET `inv_po_licenses_items`.master = 0 WHERE `inv_po_licenses_items`.set_id = ?", array($row->set_id));
			$this->db->query("UPDATE `inv_po_licenses_items` SET `inv_po_licenses_items`.master = 1 WHERE `inv_po_licenses_items`.id = ?", array($item));
		}
		$this->load->helper("url");
		redirect("licenses/statistics/".$redirect);
	}

	private function showResellers($reseller = 0) {
		$output = array();
		$result = $this->db->query("SELECT
		inv_po_resellers.id,
		inv_po_resellers.name
		FROM
		inv_po_resellers
		ORDER BY inv_po_resellers.name");
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$selected = ($row->id == $reseller) ? ' selected="selected"' : "";
				array_push($output, '<option value="'.$row->id.'"'.$selected.'>'.$row->name.'</option>');
			}
		}
		return implode($output, "\n");
	}

	private function showLicensiars($licensiar = 0) {
		$output = array();
		$result = $this->db->query("SELECT
		inv_po_licensiars.id,
		inv_po_licensiars.name
		FROM
		inv_po_licensiars
		ORDER BY inv_po_licensiars.name");
		if($result->num_rows()){
			foreach($result->result() as $row) {
				$selected = ($row->id == $licensiar) ? ' selected="selected"' : "";
				array_push($output, '<option value='.$row->id.$selected.' style="margin:0px;">'.$row->name.'</option>');
			}
		}
		return implode($output, "\n");
	}

	private function getLicenseNullData() {
		// формируем базовый объект формы лицензии
		return array(
			'lname'         => '',
			'issue_date'    => '',
			'purchase_date' => '',
			'program'       => '',
			'number'        => '',
			'purchase_info' => '',
			'rname'         => '',
			'lid'           => '',
			'resid'         => 0,
			'liid'          => 0
		);
	}

	private function getLicenseData($id) {
		$result = $this->db->query("SELECT
		inv_po_licenses.id,
		inv_po_licenses.number,
		inv_po_licenses.program,
		DATE_FORMAT(inv_po_licenses.issue_date, '%d.%m.%Y') AS issue_date,
		inv_po_licenses.purchase_info,
		DATE_FORMAT(inv_po_licenses.purchase_date, '%d.%m.%Y') AS purchase_date,
		inv_po_licenses.active,
		inv_po_licensiars.name AS lname,
		inv_po_resellers.id AS resid,
		inv_po_licensiars.id AS liid,
		inv_po_resellers.name AS rname,
		inv_po_resellers.address,
		IF(DATEDIFF(NOW(),inv_po_licenses.verify_date) < 180, 1,0) AS verified,
		DATE_FORMAT(inv_po_licenses.verify_date, '%d.%m.%Y') AS verify_date
		FROM
		inv_po_licenses
		INNER JOIN inv_po_resellers ON (inv_po_licenses.reseller_id = inv_po_resellers.id)
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		WHERE inv_po_licenses.id = ?", array($id));
		if ($result->num_rows()) {
			$object = $result->row_array();
			return $object;
		}
		return $this->getLicenseNullData();
	}

	private function getUseCountArray($result) {
		$output = array();
		foreach($result->result() as $row){
			if (!isset($output[$row->set_id])) {
				$output[$row->set_id] = 0;
			}
			$output[$row->set_id] += $row->cnt;
		}
		return $output;
	}

	private function collectLicenseSetOfItems($result, $id, $usecount) {
		$output = array();
		foreach ($result->result_array() as $row) {
			if (!isset($output[$row['set_id']])) {
				$output[$row['set_id']] = array();
			}
			$output[$row['set_id']]['deleted'] = ($row['deleted']) ? 1 : 0;
			$row['lid'] = $id;
			$row['ismaster']  = ($row['master']) ? 'class="success"' : "";
			$row['remainder'] = ($row['max'] - $usecount[$row['set_id']]);
			$string = $this->load->view('license/edititemparams', $row, true);
			array_push($output[$row['set_id']], $string);
		}
		return $output;
	}

	private function getSetList($input) {
		foreach ($input as $val) {
			$data = array();
			$data['muted'] = ($val['deleted']) ? " hide" : '';
			unset($val['deleted']);
			$data['items'] = implode($val,"\n");

			$string = $this->load->view('license/editsetcontainer', $data, true);
			array_push($output, $string);
		}
		return $output;
	}

	private function getSetsOfLisense($id = 0) {
		if (!$id) {
			return "";
		}
		$output  = array();
		$result=$this->db->query("SELECT
		inv_po_licenses_sets.id,
		inv_po_licenses_sets.label_starts,
		inv_po_licenses_sets.deleted,
		inv_po_licenses_sets.`max`,
		inv_po_licenses_items.qty,
		IF(ISNULL(inv_po_licenses_items.id), 0, inv_po_licenses_items.id) AS itid,
		inv_po_licenses_items.value,
		inv_po_licenses_items.master,
		inv_po_licenses_items.set_id,
		inv_po_licenses_items.`type`,
		inv_po_licenses_items.`act`,
		inv_po_types.name,
		COUNT(ak_licenses.item_id) AS cnt
		FROM
		inv_po_licenses_items
		RIGHT OUTER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		LEFT OUTER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		LEFT OUTER JOIN ak_licenses ON (inv_po_licenses_items.id = ak_licenses.item_id)
		WHERE
		(inv_po_licenses_sets.license_id = ?)
		AND inv_po_licenses_items.`act`
		GROUP BY itid
		ORDER BY cnt DESC, inv_po_licenses_items.set_id, inv_po_licenses_items.master DESC",array($id));
		if($result->num_rows()){
			//array_push($output,"<h4>Структура лицензии</h4>");
			$usecount = $this->getUseCountArray($result);
			$input    = $this->collectLicenseSetOfItems($result, $id, $usecount);
			$output   = $this->getSetList($input);
		}
		return implode($output, "\n");
	}

	public function license_form_get($id = 0){
		$object = $this->getLicenseData($id);
		#выбираем справочники реселлеров и лицензиаров
		## дополняем объект необходимыми полями
		$object['licr']  = $this->showLicensiars($object['liid']);
		$object['resl']  = $this->showResellers($object['resid']);
		$object['stat1'] = "";
		$object['stat2'] = "";
		$object['lid']   = $id;
		// действия на тот случай если нужно показать конкретную лицензию
		$object['sets'] = $this->getSetsOfLisense($id);
		return $this->load->view("license/newlicenseparams", $object, true);
	}

	public function add_new_license(){
		if(!$this->input->post('license_id')){
			$result = $this->db->query("INSERT INTO 
			inv_po_licenses (
				inv_po_licenses.licensiar_id,
				inv_po_licenses.number,
				inv_po_licenses.program,
				inv_po_licenses.reseller_id,
				inv_po_licenses.issue_date,
				inv_po_licenses.purchase_info,
				inv_po_licenses.purchase_date,
				inv_po_licenses.active
			) VALUES( ?, ?, ?, ?, ?, ?, ?, ? )", array(
				$this->input->post('licr'),
				$this->input->post('lnum'),
				$this->input->post('prog'),
				$this->input->post('resl'),
				implode(array_reverse(explode(".", $this->input->post("dati", true))), "-"),
				$this->input->post("info", true),
				implode(array_reverse(explode(".", $this->input->post("datp", true))), "-"),
				0
			));
			$return = $this->db->insert_id();
		}else{
			$result = $this->db->query("UPDATE
			inv_po_licenses
			SET
			inv_po_licenses.licensiar_id = ?,
			inv_po_licenses.number = ?,
			inv_po_licenses.program = ?,
			inv_po_licenses.reseller_id = ?,
			inv_po_licenses.issue_date = ?,
			inv_po_licenses.purchase_info = ?,
			inv_po_licenses.purchase_date = ?
			WHERE
			inv_po_licenses.id = ?", array(
				$this->input->post('licr'),
				$this->input->post('lnum'),
				$this->input->post('prog'),
				$this->input->post('resl'),
				implode(array_reverse(explode(".", $this->input->post("dati", true))), "-"),
				$this->input->post("info", true),
				implode(array_reverse(explode(".", $this->input->post("datp", true))), "-"),
				$this->input->post('license_id')
			));
			$return = $this->input->post('license_id');
		}
		return $return;
	}

	public function add_licensiar(){
		if(strlen($this->input->post("licr_name"))){
			$this->db->query("INSERT INTO inv_po_licensiars (inv_po_licensiars.name) VALUES (?)", array($this->input->post("licr_name")));
		}
		$this->load->helper("url");
		redirect("licenses/addnew/".$this->input->post("redirect"));
	}

	public function add_reseller(){
		if($this->input->post("resl_name") && strlen($this->input->post("resl_name")) && strlen($this->input->post("resl_addr"))){
			$this->db->query("INSERT INTO 
			inv_po_resellers (
				inv_po_resellers.name,
				inv_po_resellers.address
			) VALUES (?, ?)", array(
				$this->input->post("resl_name"),
				$this->input->post("resl_addr")
			));
		}
		$this->load->helper("url");
		redirect("licenses/addnew/".$this->input->post("redirect"));
	}

	public function add_set_to_license($lid){
		$this->load->helper("url");
		if($lid){
			$this->db->query("INSERT INTO 
			inv_po_licenses_sets (
				inv_po_licenses_sets.license_id,
				inv_po_licenses_sets.max,
				inv_po_licenses_sets.deleted
			) VALUES (?, 0, 0)", array($lid));
			redirect("licenses/addnew/".$lid);
		}else{
			redirect("licenses/statistics");
		}
	}

	public function get_typelist(){
		$output = array();
		$result = $this->db->query("SELECT
		`inv_po_types`.id,
		`inv_po_types`.name
		FROM
		`inv_po_types`
		ORDER BY `inv_po_types`.`name`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<tr ref="'.$row->name.'"><td><input type="checkbox" name="typelist[]" value="'.$row->id.'" id="tl'.$row->id.'"></td><td><label for="tl'.$row->id.'">'.$row->name.'</label></td></tr>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n");
	}

	public function addpotoset(){
		$this->output->enable_profiler(TRUE);
		$set = $this->input->post("typelist");
		$setid = 0;
		$result = $this->db->query("INSERT INTO 
		inv_po_licenses_sets (
			inv_po_licenses_sets.license_id,
			inv_po_licenses_sets.max,
			inv_po_licenses_sets.deleted,
			inv_po_licenses_sets.label_starts
		) VALUES (?, ?, ?, ?)", array(
			$this->input->post('lid'),
			$this->input->post('po_num'),
			0,
			$this->input->post('startnum')));
		$setid = $this->db->insert_id();
		$output = array();
		foreach($set as $val){
			array_push($output, "(".$val.",".$setid.")");
		}
		$this->db->query("INSERT INTO 
		inv_po_licenses_items (
			inv_po_licenses_items.type_id, 
			inv_po_licenses_items.set_id
		) VALUES ". implode($output, ", "));
		//print $query;
	}

	public function saveset(){
		$result = $this->db->query("UPDATE 
		inv_po_licenses_items
		SET 
		inv_po_licenses_items.value = ?,
		inv_po_licenses_items.master = 0,
		inv_po_licenses_items.type = ?,
		inv_po_licenses_items.qty = ?,
		inv_po_licenses_items.verify_pk = ?,
		inv_po_licenses_items.act = 1
		WHERE
		inv_po_licenses_items.id = ?", array(
			$this->input->post('keyvalue'),
			$this->input->post('keytype'),
			$this->input->post('maknum'),
			substr($this->input->post('keyvalue'), -5),
			$this->input->post('item_id')
		));
		return $this->input->post("lid");
	}

	public function po_usage_get(){
		$stat   = array();
		$output = array();
		//
		/*
		Использование лицензий. Сначала делается выборка всех наборов софта с суммированием по типу ПО
		Общее количество лицензированных установок ПО, вычисляется по имеющимся в базе лицензиям по каждому типу ПО,
		включенному в отчёты
		*/
		//
		$result = $this->db->query("SELECT
		SUM(inv_po_licenses_sets.max) AS `totalsum`,
		inv_po_types.name,
		inv_po_licenses_items.type_id
		FROM
		inv_po_licenses_items
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		INNER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		WHERE
		NOT inv_po_licenses_sets.deleted
		AND inv_po_licenses_items.act
		AND inv_po_licenses_items.`type` <> 'KMS'
		AND inv_po_licenses_items.type_id IN (
			SELECT
			`inv_po_types`.`id`
			FROM `inv_po_types`
			WHERE 
			`inv_po_types`.`inreport` = 'Y'
		)
		GROUP BY
		inv_po_licenses_items.type_id
		ORDER BY
		inv_po_types.name");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$stat[$row->type_id] = array(
					'name'     => $row->name,
					'totalsum' => $row->totalsum
				);
			}
		}

		$result = $this->db->query("SELECT
		IFNULL(SUM(inv_po_licenses_sets.max), 0) AS difference,
		inv_po_types.name,
		inv_po_licenses_items.type_id
		FROM
		inv_po_licenses_items
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		INNER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		INNER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		WHERE
		NOT inv_po_licenses_sets.deleted 
		AND inv_po_licenses_items.act
		AND inv_po_licenses_items.master 
		AND inv_po_types.id IN (
			SELECT 
			`inv_po_types`.`id` 
			FROM `inv_po_types` 
			WHERE `inv_po_types`.`inreport` = 'Y'
		)
		GROUP BY
		inv_po_licenses_items.type_id,
		inv_po_types.name
		ORDER BY
		inv_po_licenses_items.type_id");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$stat[$row->type_id]['diff']  = $row->difference;
			}
		}
		

		$use_count = array();
		$result = $this->db->query("SELECT 
		COUNT(ak_licenses.item_id) AS usage_sum,
		ak_licenses.item_id,
		`inv_po_types`.id
		FROM
		`inv_po_licenses_items`
		INNER JOIN ak_licenses ON (`inv_po_licenses_items`.id = ak_licenses.item_id)
		INNER JOIN `inv_po_types` ON (`inv_po_licenses_items`.type_id = `inv_po_types`.id)
		WHERE
		(LENGTH(ak_licenses.item_id))
		GROUP BY
		`inv_po_types`.id");
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$use_count[$row->id] = array(
					'sum'  => $row->usage_sum,
					'item' => $row->item_id
				);
			}
		}
		
		$table = array();
		//print_r($stat);
		//return true;
		foreach($stat as $key => $val){
			//print $use_count[$key]['sum']."<br>";

			$val['diff']  = (isset($val['diff']))   ? $val['diff']  : 0 ;
			$val['diff']  = (strlen($val['diff']))  ? $val['diff']  : 0 ;

			$usage_qty  = (!isset($use_count[$key]['sum'])  || !strlen($use_count[$key]['sum']))  ? "-" : $use_count[$key]['sum'];
			$usage_item = (!isset($use_count[$key]['item']) || !strlen($use_count[$key]['item'])) ? 0   : $use_count[$key]['item'];
			$string ='<tr style="cursor:pointer" class="relShow" ref="'.$key.'">
			<td>'.$val['name'].'</td>
			<td>'.$val['diff'].'<span class="muted" title="С учётом даунгрейда"> / '.$val['totalsum'].'</span></td>
			<td>'.$usage_qty.'</td>
			</tr>
			<tr class="relrow hide" id="relrow'.$key.'">
				<td colspan=3 id="relation'.$key.'"></td>
			</tr>';
			array_push($table, $string);
		}

		$output['content'] = implode($table, "\n");
		return $this->load->view('license/licenseusage', $output, true);
	}

	public function getpolist(){
		$output = array();
		$result = $this->db->query("SELECT 
		inv_po_installed_software.po_name,
		DATE_FORMAT(inv_po_installed_software.scandate, '%d.%m.%Y') AS scandate
		FROM
		inv_po_installed_software
		WHERE
		(inv_po_installed_software.hostname = ?)
		ORDER BY
		inv_po_installed_software.po_name", array($this->input->post('host')));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$id=sizeof($output);
				$string = '<tr><td><input class="poCheck" type="checkbox" id="i'.$id.'" title="'.$row->scandate.'" refn="'.$row->po_name.'"></td><td><label for="i'.$id.'" style="cursor:pointer;">'.$row->po_name.'</label></td></tr>';
				array_push($output, $string);
			}
			print implode($output, "\n");
		}
	}
}

/* End of file licensemodel.php */
/* Location: ./application/models/licensemodel.php */