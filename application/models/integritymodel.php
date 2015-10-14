<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Integritymodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function int_leaders_get(){
		$src_dept = array();
		$duped_leaders = array();
		$duped_leaders_out = array();
		$output = array('<table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span8">Подразделение</th><th>Ожидается должность</th></tr>');
		$result = $this->db->query("SELECT 
		`staff`.staff,
		`departments`.id,
		`departments`.dn
		FROM
		`departments`
		INNER JOIN `staff` ON (`departments`.chief = `staff`.id)
		WHERE 
		departments.actual");
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($src_dept[$row->id])){
					$src_dept[$row->id] = array();
				}
				array_push($src_dept[$row->id],array('dn' => $row->dn, 'staff' => $row->staff));
			}
		}
		$result = $this->db->query("SELECT 
		departments.id,
		departments.dn,
		users.id AS uid,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio
		FROM
		users
		INNER JOIN departments ON (users.staff_id = departments.chief)
		AND (users.dep_id = departments.id)
		WHERE
		(NOT (users.fired)) AND 
		(departments.actual)
		ORDER BY
		departments.id");
		
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($duped_leaders[$row->id])){
					$duped_leaders[$row->id] = array();
				}
				array_push($duped_leaders[$row->id],"<tr><td>".$row->dn."</td><td><a href=\"/admin/users/".$row->uid."\">".$row->fio."</a></td></tr>");
			}
		}
		foreach($duped_leaders as $key => $val){
			if(sizeof($duped_leaders[$key]) < 2){
				unset($duped_leaders[$key]);
			}else{
				array_push($duped_leaders_out,implode($duped_leaders[$key],"\n"));
			}
		}
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(isset($src_dept[$row->id])){
					unset($src_dept[$row->id]);
				}
			}
		}
		foreach($src_dept as $val){
			array_push($output,"<tr><td>".$val[0]['dn']."</td><td>".$val[0]['staff']."</td></tr>");
		}
		array_push($output,"</table>");
		array_push($output,'<h5>Проверьте также повторяющиеся записи о руководителях</h5><table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span8">Подразделение</th><th>ФИО пользователя</th></tr>'.implode($duped_leaders_out,"")."</table>");
		return implode($output,"");
	}

	public function int_freeworkers_get(){
		$output = array('<table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span4">ФИО пользователя</th><th>Подразделение</th></tr>');
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) AS fio,
		`departments`.dn,
		`users`.id as uid
		FROM
		`users`
		INNER JOIN `departments` ON (`users`.dep_id = `departments`.id)
		WHERE
		NOT `departments`.`actual` AND
		NOT `users`.`fired`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,"<tr><td><a href=\"/admin/users/".$row->uid."\">".$row->fio."</a></td><td>".$row->dn."</td></tr>");
			}
		}
		return implode($output,"")."</table>";
	}

	public function int_irless_get(){
		$output = array('<table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span4">Информационные ресурсы</th><th>Подразделение</th></tr>');
		$result = $this->db->query("SELECT 
		`departments`.dn,
		`resources`.shortname
		FROM
		`resources`
		INNER JOIN `departments` ON (`resources`.owner = `departments`.id)
		WHERE
		NOT `departments`.`actual` AND
		`resources`.`active`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,"<tr><td>".$row->shortname."</td><td>".$row->dn."</td></tr>");
			}
		}
		return implode($output,"")."</table>";
	}

	public function int_curless_get(){
		$output = array('<table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span6">ФИО пользователя</th><th>Подразделение</th></tr>');
		$result = $this->db->query("SELECT 
		users.id,
		departments.dn,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio
		FROM
		users
		INNER JOIN departments ON (users.dep_id = departments.id)
		WHERE
		(departments.actual)
		AND NOT `users`.`fired`
		AND `users`.`service` NOT IN (
			SELECT `users`.`id` FROM `users` WHERE users.`sman` AND NOT `users`.`fired`
		)
		ORDER BY
		departments.dn,
		fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,'<tr><td><a href="/admin/users/'.$row->id.'" target="_blank">'.$row->fio.'</a></td><td>'.$row->dn.'</td></tr>');
			}
		}
		return implode($output,"")."</table>";
	}

	public function int_ownerless_hosts_get(){
		$output = array('<table class="table table-condensed table-bordered table-striped table-hover"><tr><th class="span8">ФИО пользователя</th><th>Действие</th></tr>');
		$result = $this->db->query("SELECT 
		`hosts`.hostname,
		`hosts`.id,
		DATEDIFF(NOW(), `hosts`.ts) AS wdate
		FROM
		`hosts`
		WHERE
		((ISNULL(`hosts`.uid)) OR 
		(NOT (`hosts`.uid))) AND 
		(NOT (`hosts`.server)) AND 
		(NOT (`hosts`.noise))
		ORDER BY
		FIELD(wdate, 0, 1, 2, 3, 4, 5) DESC");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$freshness = ($row->wdate <= 5) ? 'class="info"' : "";
				array_push($output,'<tr id="def'.$row->id.'" '.$freshness.'>
				<td>'.strtoupper($row->hostname).'</td>
				<td><button class="btn btn-info btn-mini button-hb" hm="'.strtoupper($row->hostname).'" ref="'.$row->id.'" title="Направить на связывание">Связать</button></td>
				<td><button class="btn btn-danger btn-mini button-serv" title="Маркировать сервером" ref="'.$row->id.'">Сервер</button></td>
				<td><button class="btn btn-danger btn-mini button-noise" title="Маркировать шумом" ref="'.$row->id.'">Шум</button></td>
				</tr>');
			}
		}
		array_unshift($output,'<h3 id="b_counter">'.$result->num_rows()."</h3>");
		return implode($output,"")."</table>";
	}

	public function int_userlist_get(){
		$output = array('<option value="0">Выберите пользователя</option>');
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) AS fio,
		`users`.id
		FROM
		`users`
		ORDER BY
		fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,'<option value="'.$row->id.'">'.$row->fio.'</option>');
			}
		}
		return implode($output,"\n");
	}

	public function int_labels_get(){
		$labels = array();
		$result = $this->db->query("SELECT 
		`hash_items`.label,
		`hash_items`.hostname
		FROM
		`hash_items`
		WHERE
		LENGTH(`hash_items`.`label`)
		ORDER BY `hash_items`.`id`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$labels[$row->label] = $row->hostname;
			}
		}

		$output = array('<table class="table table-condensed table-bordered table-striped table-hover">
		<tr><th class="span4">Компьютер</th><th>№ наклейки</th><th>Статус</th></tr>');
		$result = $this->db->query("SELECT DISTINCT 
		ak_licenses.id,
		ak_licenses.product_name,
		ak_licenses.hostname,
		ak_licenses.active,
		ak_licenses.l_written,
		ak_licenses.l_given,
		DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') AS scandate,
		ak_licenses.label AS verlabel,
		CONCAT_WS('-', SUBSTR(ak_licenses.label, 2, 5), SUBSTR(ak_licenses.label, 7, 3), SUBSTR(ak_licenses.label, 10, 3), SUBSTR(ak_licenses.label, 13, 3)) AS label,
		`hosts`.uid,
		ak_licenses.id
		FROM
		`hosts`
		RIGHT OUTER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
		WHERE
		ak_licenses.active AND
		(LENGTH(ak_licenses.label))
		GROUP BY
		ak_licenses.id,
		ak_licenses.hostname
		ORDER BY
		label, scandate");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$class  = array();
				$status = array();
				$wr = "";
				if (isset($labels[$row->verlabel])) {
					$wrclass = ($labels[$row->verlabel] == $row->hostname) ? "btn-info" : "btn-danger" ;
					$wr = '<span class="btn btn-mini '.$wrclass.'" title="Номер наклейки '.substr($row->verlabel, 1).' зафиксирован в реестре Windows на имя компьютера '.$labels[$row->verlabel].'">REG</a>';
				}
				(!$row->active) ? array_push($class, "muted") : "" ;
				array_push($status, ( (file_exists("/var/ncontrol/".strtoupper($row->hostname).".txt")) 
					? '<span class="btn btn-mini btn-success" title="Значение наклейки записано">WR</span>' 
					: '<span class="btn btn-mini btn-danger label-write" label="'.$row->label.'" item="'.$row->id.'" id="wr'.$row->id.'" host="'.strtoupper($row->hostname).'" title="Наклейка не записана">WR</span>' ) ) ;
				array_push($status, ( ($row->l_given) 
					? '<a href="/integrity/labelgiven/'.$row->id.'" class="btn btn-mini btn-success" title="Наклейка выдана">GN</a>' 
					: '<a href="/integrity/labelgiven/'.$row->id.'" class="btn btn-mini btn-warning" title="Наклейка не выдана">GN</a>') ) ;
				
				$string = '<tr class="'.implode($class," ").'" title="обнаружена: '.$row->scandate.'">
				<td><a name="'.$row->hostname.'"></a><a href="/admin/users/'.$row->uid.'" target="_blank">'.$row->hostname.'</a><br>'.$row->product_name.'</td>
				<td class="labelMover" nm="'.$row->hostname.'" ref="'.$row->id.'">'.$row->label.'</td>
				<td>'.implode($status," ").' '.$wr.'</td></tr>';
				array_push($output, $string);
			}
		}
		array_push($output, "</table>");
		return implode($output, "\n");
	}

	public function labelreport(){
		$labels = array();
		$result = $this->db->query("SELECT 
		`hash_items`.label,
		`hash_items`.hostname
		FROM
		`hash_items`
		WHERE
		LENGTH(`hash_items`.`label`)
		ORDER BY `hash_items`.`id`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$labels[$row->label] = $row->hostname;
			}
		}

		$output = array('<table style="width:170mm;table-layout:fixed;border-spacing: 0px;border-collapse: collapse;padding:3px;">
		<tr><th style="width:55mm;border: 1px solid black;">Компьютер</th><th style="border: 1px solid black;">№ наклейки</th><th style="border: 1px solid black;">Статус</th></tr>');
		$result = $this->db->query("SELECT DISTINCT 
			ak_licenses.product_name,
			ak_licenses.active,
			ak_licenses.l_written,
			ak_licenses.hostname,
			ak_licenses.l_given,
			ak_licenses.label AS verlabel,
			DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') AS scandate,
			CONCAT_WS('-', SUBSTR(ak_licenses.label, 2, 5), SUBSTR(ak_licenses.label, 7, 3), SUBSTR(ak_licenses.label, 10, 3), SUBSTR(ak_licenses.label, 13, 3)) AS label,
			CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
			CONCAT(users1.name_f, ' ', SUBSTR(users1.name_i, 1), '.', SUBSTR(users1.name_o, 1), '.') AS serv
			FROM
			`hosts`
			RIGHT OUTER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
			INNER JOIN users ON (`hosts`.uid = users.id)
			LEFT OUTER JOIN `users` users1 ON (users.service = users1.id)
			WHERE
			(ak_licenses.active) AND 
			(LENGTH(ak_licenses.label))
			GROUP BY
			ak_licenses.id
			ORDER BY
			label,
			scandate");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$class = array();
				$status = array();
				(!$row->active) ? array_push($class, "muted") : "" ;
				($row->l_written) ? array_push($status, 'Записано в реестр') : "" ;
				($row->l_given) ? array_push($status, 'Наклейка выдана') : "" ;
				(isset($labels[$row->verlabel])) ? array_push($status, 'Верифицировано') : "";
				
				$string = '<tr>
				<td style="width:55mm;border: 1px solid black;">'.$row->hostname.'<br>'.$row->product_name.'</td>
				<td style="width:35mm;border: 1px solid black;">'.$row->label.'</td>
				<td style="border: 1px solid black;">'.implode($status,",<br>").'</td>
				</tr>';
				array_push($output, $string);
			}
		}
		array_push($output, "</table>");
		$act = array();
		$act['content'] = implode($output, "\n");
		$this->load->helper('download');
		header("Content-Type: text/html; charset=windows-1251");
		force_download('Отчёт о расходе наклеек.doc', $this->load->view('integrity/report_word', $act, true));
		return true;
	}

}
/* End of file integritymodel.php */
/* Location: ./application/models/integritymodel.php */