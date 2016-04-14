<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminmodel extends CI_Model {
	function __construct(){
		parent::__construct();	// Call the Model constructor
	}

	public function userdata_get($user_id = 0, $page = 1) {
		$user_id = ($user_id) ? $user_id : (($this->input->post('userSelector')) ? $this->input->post('userSelector') : 0);
		
		$user = array(
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
		
		$user['servop'] = ($user['servoperator'])	? 'checked="checked"' : "" ;
		$user['air']	= ($user['air'])			? 'checked="checked"' : "" ;
		$user['bir']	= ($user['bir'])			? 'checked="checked"' : "" ;
		$user['sman']	= ($user['sman'])			? 'checked="checked"' : "" ;
		$user['superv']	= ($user['superv'])			? 'checked="checked"' : "" ;

		$user['page'] = $page;
		
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id, IF(departments.id = ?,' selected',''),' >',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn",array($user['dep_id']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$user['id_dep'] = implode($output,"\n");
		}

		$result = $this->db->query("SELECT 
		CONCAT('<option value=',`staff`.id, IF(`staff`.id = ?,' selected',''),' >',`staff`.staff,'</option>') AS options
		FROM
		`staff`
		ORDER BY `staff`.`staff`",array($user['staff_id']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$user['staff_id'] = implode($output,"\n");
		}

		$user['mac'] = "";
		if($user['id']){
			$result = $this->db->query("SELECT 
			hash_items.networkcard_macaddress
			FROM
			`hosts`
			INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
			WHERE
			(`hosts`.uid = ?)
			ORDER BY hosts.ts DESC",array($user['id']));
			if ($result->num_rows()){
				$output=array();
				foreach($result->result() as $row){
					array_push($output,$row->networkcard_macaddress);
				}
				$user['mac'] = implode($output, ";  ");
			}
		}

		$user['email'] = "";
		if($user['id']){
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
			if ($result->num_rows()){
				$output=array();
				foreach($result->result() as $row){
					array_push($output,$row->email);
				}
				$user['email'] = implode($output,"; ");
			}
		}
		
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
				$selected = ($row['id'] == $user['office_id']) ? 'selected="selected"' : '';
				array_push($input[$row['parent']], '<option value='.$row['id'].' '.$selected.'>'.$row['address'].'</option>');
			}
			foreach($input as $key=>$val){
				array_push($output, implode($val, "\n"));
			}
			$user['location'] = implode($output,"\n");
		}
		/*
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',locations.id, IF(`locations`.id = ?,' selected',''),' >',locations.address,'</option>') AS options
		FROM locations
		WHERE `locations`.parent = 0 AND
		`locations`.id <> 0
		ORDER BY `locations`.`address`",array($user['parent']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$user['location'][0] = implode($output, "\n");
		}
		if (!$user['parent']) {
			$user['location'][0] = $user['location'][1];
			$user['location'][1] = "";
		}
		*/
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',users.id, IF(`users`.id = ?,' selected',''),' >',
		CONCAT_WS(' ',users.name_f, users.name_i, users.name_o),'</option>') AS options
		FROM
		users
		WHERE `users`.`sman` = 1 AND
		`users`.`fired` = 0
		Order by 
		CONCAT(users.name_f, users.name_i, users.name_o)", array($user['service']));
		$user['serviceman'] = $user['service'];
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				array_push($output, $row->options);
			}
			$user['service'] = implode($output, "\n");
		}
		$user['supchange'] = (
			$this->session->userdata('rank') 
			|| (
					$this->session->userdata('is_sup') 
					&& $this->session->userdata('canSee') == $user['supervisor']
				)
			) ? 1 : 0 ;
		$user['saveable'] = (
			$this->session->userdata('rank') 
			|| $this->session->userdata('canSee') == $user['supervisor'] 
			|| $user['serviceman'] == $this->session->userdata('base_id') 
		) ? 1 : 0 ;

		$user['is_io'] = ($user['io']) ? ' checked="checked"' : "";
		return $user;
	}

	public function user_resources_get($user_id) {
		$active		= array();
		$expired	= array();
		$res		= array();
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
			DATE_FORMAT(resources_items.okdate, '%e.%m.%Y %H:%i') AS okdate,
			resources_items.`exp`,
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
			(resources_items.uid = ?) AND 
			(NOT (resources_items.del))
			ORDER BY
			resources_items.ok", array($user_id));
		if($result->num_rows()){
			foreach($result->result_array() as $row){
				if(!isset($res[$row['id']])){
					$res[$row['id']] = array();
				}
				$res[$row['id']]['pid'.$row['pid']] = $row['pid_value'];
				unset( $row['pid'], $row['pid_value'] );
				$res[$row['id']] = array_merge($res[$row['id']], $row);
			}
		}
		foreach ($res as $key=>$row){
			$row['editAllowed'] = ($this->session->userdata('rank') || $this->session->userdata("admin_id") == 26) ? '' : ' disabled="disabled"';
			####################
			$row['ipChunk']  = (in_array($row['rid'], array(100,101)))	? $this->load->view("iptemplate", $row, true) : '';
			$row['mnChunk']  = (in_array($row['rid'], array(100)))		? $this->load->view("mntemplate", $row, true) : '';
			$row['osa_date'] = ( $row["ok"] ) ? "исполнено ОСА: ".$row['okdate'] : "";
			#####################
			$row['button1'] = '<span class="btn btn-'.(($row["ok"]) ? ((!$row["apply"]) ? "inverse" : "success" ) : "primary").' btn-small activate" prop="'.$row['id'].'"
			title="'.(($row["ok"]) ? ((!$row["apply"]) ? "Обновить данные заявки" : "Архив" ) : "Пометить исполненной отделом СА").'">
			<i class="'.(($row["ok"]) ? "icon-edit" : "icon-ok-sign").' icon-white"></i>&nbsp;'.(($row["ok"]) ? ((!$row["apply"]) ? "Ждём куратора" : "Исполнено" ) : "Активировать").'</span>';

			$row['button1'] = ($this->session->userdata('rank') || $this->session->userdata("admin_id") == 26) ? $row['button1'] : '<span class="btn btn-'.((!$row["apply"]) ? "warning" : "success").' btn-small'.(($this->session->userdata('canSee') == $row['supervisor']) ? ' hookup': "").'" prop="'.$row['id'].'">
			<i class="'.(($row["ok"]) ? "icon-edit" : "icon-ok-sign").' icon-white"></i>&nbsp;'.((!$row["apply"]) ? "Доложить об исполнении" : "Исполнено").'&nbsp;</span>';

			$row['button2'] = ($this->session->userdata('rank')) ? '<span class="btn btn-warning btn-small expire" prop="'.$row['id'].'" title="Отменить"><i class="icon-remove-sign icon-white"></i>&nbsp;</span>
			<span class="btn btn-danger btn-small delete" prop="'.$row['id'].'"  title="Удалить"><i class="icon-trash icon-white"></i>&nbsp;</span>
			<span class="btn btn-info btn-small makeEvent" prop="'.$row['id'].'"  title="Создать поручение"><i class="icon-calendar icon-white"></i>&nbsp;</span>' : '';

			$template = $this->load->view("restemplate", $row, true);
			($row['exp']) ? array_push($expired, $template) : array_push($active, $template);
		}
		$output = array(
			'asize'		=> sizeof($active),
			'active'	=> implode($active, "\n"),
			'esize'		=> sizeof($expired),
			'expired'	=> implode($expired, "\n")
		);
		return $this->load->view("reslisttemplate", $output, true);
	}

	public function user_arm_get($user_id) {
		if(!$user_id){
			$return = array();
			$return['pcconfs'] = "Не выбран пользователь";
			$return['pclist'] = "";
			$return['pcused'] = "";
			$return['pcpo'] = "";
			$return['licenses'] = "";
			return $return;
		}
		$output = array();
		$headers = array();
		$pclist = array();
		$usedpc = array();
		$result = $this->db->query("SELECT DISTINCT
		hash_items.id,
		hash_items.inv_number,
		CONCAT_WS(' ', hash_items.baseboard_manufacturer,hash_items.baseboard_product,hash_items.baseboard_serialnumber,hash_items.baseboard_version) as mb,
		hash_items.computersystemproduct_name AS system,
		hash_items.bios_description AS bios,
		CONCAT_WS(' ', hash_items.processor_description,hash_items.processor_name) AS processor,
		hash_items.physicalmemory_capacity AS ram,
		CONCAT_WS(' ', hash_items.networkcard_description, hash_items.networkcard_macaddress) AS nic,
		hash_items.diskdrive_pnpdeviceid AS hdd,
		hash_items.cdromdrive_caption AS cdrom,
		hash_items.videocontroller_caption AS video,
		DATE_FORMAT(hash_items.ts,'%d.%m.%Y') as `date`,
		hash_items.active,
		hash_items.hostname,
		hash_items.all_md5
		FROM
		`hosts`
		INNER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
		WHERE
		(`hosts`.uid = ?)
		ORDER BY hash_items.hostname, hash_items.ts DESC", array($user_id));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<li title="'.$row->hostname.'" act="'.$row->id.'" class="armselector">'.$row->hostname.'</li>';
				if ($row->active) { 
					array_push($pclist,$string);
				}else{ 
					array_push($usedpc,$string);
				}
				$pcmode = ($row->active) 
					? '<button type="submit" class="btn btn-warning btn-mini" form="invform'.$row->id.'" name="block" value="block">Заблокировать</button>'
					: '<button type="submit" class="btn btn-warning btn-mini" form="invform'.$row->id.'" name="block" value="unblock">Разблокировать</button>';
				$string = '<table id="tbd'.$row->id.'" class="table-arm hide table table-bordered table-condensed table-striped'.(($row->active) ? '' : ' muted').'">
					<tr>
						<td colspan=2>
							АК: <strong>'.$row->hostname.'</strong><small class="offset1">Дата сканирования '.$row->date.'</small>
							'.$pcmode.'
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
				</table>
				<form method="post" id="invform'.$row->id.'" style="display:none" action="/admin/blockpc">
					<input type="hidden" name="invnum" value="'.$row->id.'">
					<input type="hidden" name="user" value="'.$user_id.'">
				</form>';
				array_push($output, $string);
			}
		}

		$return = array();
		$return['pcconfs'] = implode($output,"\n");
		$return['pclist']  = implode($pclist,"\n");
		$return['pcused']  = implode($usedpc,"\n");

		$return['licenses'] = $this->licensemodel->userlicenses_get($user_id);
		return $return;
	}

	public function no_cache() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 
	}

	public function filter_users($filter = "") {
		$uid = ($this->session->userdata("uid")) ? $this->session->userdata("uid") : "1";
		$filter = iconv('UTF-8', 'Windows-1251' , urldecode($filter)).'%';
		$mode = (preg_match("/[a-zA-Z]/",$filter)) ? "host" : "name";
		$output = array();
		if($mode == "name"){
			$result =$this->db->query("SELECT 
			CONCAT('<option value=',
			`users`.`id`,
			IF(`users`.id = ?, ' selected = \"selected\">', '>'),
			CONCAT_WS(' ', TRIM(`users`.`name_f`), TRIM(`users`.`name_i`), TRIM(`users`.`name_o`)), '</option>') AS `options`
			FROM
			`users`
			WHERE
			LOWER(CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o)) LIKE ?
			ORDER BY TRIM(`users`.name_f) ASC, TRIM(`users`.name_i) ASC, TRIM(`users`.name_o) ASC", array($uid, $filter));
		}else{
			$result = $this->db->query("SELECT 
			CONCAT('<option value=',
			`users`.id,
			IF(`users`.id = ?, ' selected = \"selected\">', '>'),
			CONCAT_WS(' ', TRIM(`users`.name_f), TRIM(`users`.name_i), TRIM(`users`.name_o)),
			'</option>') as options
			FROM
			`users`
			WHERE 
			users.host LIKE ?
			ORDER BY TRIM(`users`.name_f) ASC, TRIM(`users`.name_i) ASC, TRIM(`users`.name_o) ASC",array($uid, $filter));
		}
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		$list = implode($output,"\n");
		print $list;
	}

	public function summary_show() {
		$this->db->query("SET lc_time_names = 'ru_RU';");
		$summary		= array();
		$usertable		= array();
		$usertable_l	= array();
		$a_id			= $this->session->userdata("admin_id");
		$is_sup			= $this->session->userdata('is_sup');
		$base_id		= $this->session->userdata('base_id');
		$my_sup			= $this->session->userdata('canSee');
		$rank			= $this->session->userdata('rank');
		$this->load->helper("form");

		$result = $this->db->query("SELECT 
		users.id,
		users.service,
		CONCAT(users.name_f, ' ', SUBSTR(users.name_i, 1, 1),'.', SUBSTR(users.name_o, 1, 1), '. [', departments.alias,']') AS userfio,
		CONCAT(users1.name_f,' ',LEFT(users1.name_i,1),'.',LEFT(users1.name_o,1),'.') AS curator
		FROM
		users
		INNER JOIN departments ON (users.dep_id = departments.id)
		INNER JOIN `users` users1 ON (users.service = users1.id)
		WHERE
		NOT `users`.fired ".(( (int) $rank == 1) ? "" : "AND `users`.supervisor = ".$my_sup ));

		$summary['user_num'] = $result->num_rows();
		if($summary['user_num']){
			foreach ($result->result() as $row){
				$string = '<tr><td><a href="/admin/users/'.$row->id.'">'.$row->userfio.'</a></td>
				<td>'.$row->curator.'</td>
				<td><button type="button" class="btn btn-mini btn-warning">пометить уволенным</button></td></tr>';
				array_push($usertable, $string);
			}
			$summary['user_table'] = implode($usertable,"\n");
		}

		$summary['servicemendata'] = "";

		$servicemen = array();
		$result = $this->db->query("SELECT 
		COUNT(users.id) AS `count`,
		CONCAT_WS(' ', users1.name_f, users1.name_i, users1.name_o) AS curator
		FROM
		users users1
		INNER JOIN users ON (users1.id = users.service)
		WHERE
		NOT users.fired AND
		NOT users1.fired ".(( $rank == 1) ? "" : "AND `users`.supervisor = ".$my_sup)."
		GROUP BY
		users.service", array($base_id));
		if($result->num_rows()){
			foreach ($result->result() as $row){
				$string = "['".$row->curator." [".$row->count."]', ".$row->count."]";
				array_push($servicemen,$string);
			}
			$summary['servicemendata'] = implode($servicemen,", ");
		}


		$orders		= array();
		$otabs		= array();
		$opanes		= array();
		$graphdata	= array();
		$result		= $this->db->query("SELECT 
		DATE_FORMAT(resources_orders.docdate, '%b.') as `mnth`,
		COUNT(resources_items.id) AS fdate,
		DATE_FORMAT(resources_orders.docdate, '%Y') AS fyr
		FROM
		resources_items
		INNER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		INNER JOIN users ON (resources_items.uid = users.id)
		WHERE
		NOT users.fired ".(( (int) $rank == 1) ? "" : "AND `users`.supervisor = ".$my_sup)." AND
		DATE_FORMAT(resources_orders.docdate, '%Y') > (DATE_FORMAT(NOW(), '%Y') - 4)
		GROUP BY
		DATE_FORMAT(resources_orders.docdate, '%Y.%M')
		ORDER BY
		resources_orders.docdate", array($base_id));
		if($result->num_rows()){
			foreach ($result->result() as $row){
				(!isset($orders[$row->fyr])) ? $orders[$row->fyr] = array() : "" ;
				array_push($orders[$row->fyr], "['".$row->mnth."(".$row->fdate.")', ".$row->fdate."]");
			}
			foreach($orders as $key=>$val){
				array_push($graphdata,'\''.$key.'\' : ['.implode($orders[$key],",").']');
				array_push($otabs, '<li class="'.((date("Y") == $key) ? 'active' : '').'"><a href="#tab'.$key.'" data-toggle="tab" class="tabber" key="'.$key.'">'.$key.' год</a></li>');
				array_push($opanes, '<div class="tab-pane '.((date("Y") == $key) ? 'active' : '').'" id="tab'.$key.'"><div class="span12" style="height:200px;margin-left:0px;margin-bottom:20px;display:block;" id="ordchart'.$key.'"></div></div>');
			}
			$summary['tabs'] = implode($otabs,"\n");
			$summary['graph'] = implode($graphdata,",\n\t\t");
			$summary['panes'] = implode($opanes,"\n");
		}


		$o_ok		= array();
		$o_toapply	= array();
		$o_applied	= array();
		$result		= $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		resources.name,
		resources_items.ok,
		resources_items.apply,
		DATE_FORMAT(`resources_orders`.docdate,'%d.%m.%Y') as `date`
		FROM
		resources_items
		LEFT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		WHERE
		NOT (resources_items.del) AND
		(NOT (resources_items.exp)) AND
		resources_items.uid ".(( (int) $rank == 1) ? "" : "AND `users`.supervisor = ".$my_sup)."
		ORDER BY
		resources_orders.docdate");
		//print $this->db->last_query();
		$summary['o_overall'] = $result->num_rows();
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!$row->apply){
					array_push($o_toapply,$row->apply);
				}else{
					array_push($o_applied,$row->apply);
				}
				if($row->ok){
					array_push($o_ok,$row->ok);
				}
			}
		}
		$summary['o_ok'] = sizeof($o_ok);
		$summary['o_toapply'] = sizeof($o_toapply);
		$summary['o_applied'] = sizeof($o_applied);

		$output = array();
		array_push($output,'<table class="table table-bordered table-condensed table-striped table-hover" style="margin-left:0px">
		<tr>
			<th class="span4">ФИО</th>
			<th class="span5">Ресурс</th>
			<th class="span1">Дата</th>
			<th class="span1">СА</th>
			<th class="span1">ИСП</th>
		</tr>');
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		users.id AS userid,
		resources.shortname,
		resources_items.id,
		DATE_FORMAT(resources_orders.docdate,'%d.%m.%Y') as docdate,
		resources_items.ok
		FROM
		resources
		RIGHT OUTER JOIN resources_items ON (resources.id = resources_items.rid)
		LEFT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		WHERE
		NOT resources_items.apply AND
		NOT `resources_items`.`exp` AND
		(resources_items.uid) AND 
		NOT resources_items.del
		".(( (int) $rank == 1) ? "" : "AND `users`.supervisor = ".$my_sup)."
		ORDER BY
		resources_orders.docdate DESC
		LIMIT 50");
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,'<tr>
					<td><a href="/admin/users/'.$row->userid.'">'.$row->fio.'</a></td>
					<td>'.$row->shortname.'</td>
					<td>'.$row->docdate.'</td>
					<td><center>'.(($row->ok) ? '<i class="icon-ok"></i>' : '<i class="icon-remove icon-white"></i>').'</center></td>
					<td>
						<div class="btn-group">
							<a class="btn btn-warning btn-mini dropdown-toggle" data-toggle="dropdown" id="btn'.$row->id.'" href="#">Действие&nbsp;&nbsp;<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li class="serv_complete" ref="'.$row->id.'"><a href="#">Выполнено</a><li>
								<li class="divider"></li>
								<li class="serv_decline" ref="'.$row->id.'"><a href="#">Отклонена</a><li>
							</ul>
						</div>
					</td>
				</tr>');
			}
		}
		$summary['ordprocessing'] = implode($output,"\n")."\n</table>";

		$output = array();
		$result = $this->db->query("SELECT 
			admins.id,
			CONCAT(users.name_f, ' ', SUBSTR(users.name_i, 1, 1),'.', SUBSTR(users.name_o, 1, 1),'.') AS fio,
			`departments`.dn
			FROM
			admins
			INNER JOIN users ON (admins.base_id = users.id)
			INNER JOIN `departments` ON (users.dep_id = `departments`.id)
			WHERE NOT users.fired
			ORDER BY fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<label class="checkbox" title="'.$row->fio.' '.$row->dn.'" style="width:48%;float:left;clear:none;margin:5px;cursor:pointer;"><input type="checkbox" name="rec[]" value="'.$row->id.'" style="vertical-align:middle;margin-top:3px;margin-right:5px;"> '.$row->fio.'</label>';
				array_push($output, $string);
			}
		}

		$summary['receivers'] = implode($output);

		
		return $this->load->view('page_summary', $summary, true);
	}

	public function startscreen_show() {
		$this->db->query("SET lc_time_names = 'ru_RU';");
		$summary	= array(
			'last_approved' => "Заявки, обработанные ОСА (последние 50)",
			'awaiting'      => "Заявки, стоящие в очереди на получение в ОСА"
		);
		$a_id		= $this->session->userdata("admin_id");
		$is_sup		= $this->session->userdata('is_sup');
		$base_id	= $this->session->userdata('base_id');
		$my_sup		= $this->session->userdata('canSee');
		$rank		= $this->session->userdata('rank');

		$result = $this->db->query("SELECT 
		CONCAT(users.name_f, ' ', UPPER(LEFT(users.name_i, 1)),'.', UPPER(LEFT(users.name_o, 1)),'.') AS fio,
		departments.alias,
		resources.shortname,
		resources_items.id,
		users.phone,
		users.id AS uid,
		CONCAT_WS(' ', `locations`.address, locations1.address) AS address,
		DATE_FORMAT( `resources_items`.okdate, '%d.%m.%Y' ) AS okdate,
		DATE_FORMAT( `resources_items`.initdate, '%d.%m.%Y' ) AS initdate,
		`resources_orders`.docnum, '%d.%m.%Y'
		FROM
		resources_items
		LEFT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN departments ON (users.dep_id = departments.id)
		LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN `locations` ON (`locations`.id = users.office_id)
		LEFT OUTER JOIN `locations` locations1 ON (locations1.id = `locations`.parent)
		LEFT OUTER JOIN `resources_orders` ON (resources_items.order_id = `resources_orders`.id)
		WHERE
		(resources_items.ok) 
		AND NOT (resources_items.del)
		AND NOT (resources_items.exp)
		AND NOT (resources_items.apply)
		AND (users.sman = ? OR users.supervisor = ? OR ? = 1)
		-- AND (resources_items.okdate >= DATE_SUB(NOW(), INTERVAL 10 DAY))
		ORDER BY `resources_items`.okdate DESC
		LIMIT 50", array($a_id, $my_sup, $rank));
		$summary['last_approved'] = $this->make_res_table($result);

		$result = $this->db->query("SELECT 
		CONCAT(users.name_f, ' ', UPPER(LEFT(users.name_i, 1)),'.', UPPER(LEFT(users.name_o, 1)),'.') AS fio,
		departments.alias,
		resources.shortname,
		resources_items.id,
		users.phone,
		users.id AS uid,
		CONCAT_WS(' ', `locations`.address, locations1.address) AS address,
		DATE_FORMAT( `resources_items`.okdate, '%d.%m.%Y' ) AS okdate,
		DATE_FORMAT( `resources_items`.initdate, '%d.%m.%Y' ) AS initdate,
		`resources_orders`.docnum, '%d.%m.%Y'
		FROM
		resources_items
		LEFT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN departments ON (users.dep_id = departments.id)
		LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN `locations` ON (`locations`.id = users.office_id)
		LEFT OUTER JOIN `locations` locations1 ON (locations1.id = `locations`.parent)
		LEFT OUTER JOIN `resources_orders` ON (resources_items.order_id = `resources_orders`.id)
		WHERE
		NOT (resources_items.ok)
		AND NOT (resources_items.del)
		AND NOT (resources_items.exp)
		AND (users.sman = ? OR users.supervisor = ? OR ? = 1)
		-- AND (resources_items.okdate >= DATE_SUB(NOW(), INTERVAL 10 DAY))
		ORDER BY `resources_items`.id DESC
		LIMIT 50", array($a_id, $my_sup, $rank));
		$summary['awaiting'] = $this->make_res_table($result);

		return $this->load->view('startscreen', $summary, true);
	}

	private function make_res_table($result) {
		$output = array();
		if ($result->num_rows()) {
			foreach($result->result() as $row){
				$docnum = ($row->docnum == "0") ? "б/н" : $row->docnum;
				$string = '<tr>
				<td style="width:330px;"><a href="/admin/users/'.$row->uid.'" target="_blank">'.$row->fio.'</a><br><small class="muted">'.$row->alias.', '.$row->address.' тел.: '.$row->phone.'</small></td>
				<td>'.$row->shortname.'<br><small class="muted">от '.$row->initdate.' № '.$docnum.'</small></td>
				<td style="vertical-align:middle;width:130px;text-align:center">'.$row->okdate.'</td>
				<td style="vertical-align:middle;width:148px;"><a href="/admin/applyitem/'.$row->id.'" class="btn btn-warning">Снять с контроля</a></td>
				</tr>';
				array_push($output, $string);
			}
			return implode($output, "\n");
		}
	}

	public function user_save() {
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
		`users`.supervisor = (SELECT admins.base_id FROM departments LEFT OUTER JOIN admins ON (departments.service = admins.id) WHERE `departments`.`id` = ? LIMIT 1),
		`users`.host = TRIM(?),
		`users`.login = TRIM(?),
		`users`.air = ?,
		`users`.bir = ?,
		`users`.sman = ?,
		`users`.servoperator = ?,
		`users`.superv = ?,
		`users`.io = ?
		WHERE
		`users`.`id` = ?",array(
			$this->input->post('sname'),
			$this->input->post('name'),
			$this->input->post('fname'),
			$this->input->post('dept'),
			$this->input->post('staff'),
			$this->input->post('office'),
			$this->input->post('memo'),
			$this->input->post('phone'),
			$this->input->post('service'),
			$this->input->post('dept'),
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
		$this->load->helper('url');
		$this->usefulmodel->insert_audit("Сохранена учётная карточка пользователя ".$this->input->post('login'));

		redirect("admin/users/".$this->input->post('saveID'));
	}

	public function stuck_orders() {
		$is_sup = $this->session->userdata('is_sup');
		$base_id = $this->session->userdata('base_id');
		$rank = $this->session->userdata('rank');

		$output = array();
		array_push($output,'<h2>Застрявшие заявки</h2>');
		array_push($output,'<table class="span12 table table-bordered table-striped table-hover table-condensed" style="margin-left:0px;margin-bottom:80px;">
		<tr>
			<th class="span4">Пользователь</th>
			<th class="span1">Дата подачи</th>
			<th class="span5">Информационный ресурс</th>
			<th class="span2">Ссылки</th>
		</tr>');

		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		users.id as `userid`,
		resources.shortname,
		resources_items.id,
		DATE_FORMAT(resources_orders.docdate, '%d.%m.%Y') as docdate
		FROM
		resources
		RIGHT OUTER JOIN resources_items ON (resources.id = resources_items.rid)
		LEFT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN resources_orders ON (resources_items.order_id = resources_orders.id)
		WHERE
		(NOT users.fired)
		AND (NOT (resources_items.ok))
		AND (NOT (resources_items.exp))
		AND (resources_items.uid)
		AND (NOT (resources_items.del)) ".(( (int) $rank == 1) ? "" : (($is_sup == 1) ? "AND `users`.supervisor = ".$base_id : " AND `users`.service = ".$base_id))."
		ORDER BY resources.shortname, fio");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string ='<tr>
				<td>'.$row->fio.'</td>
				<td>'.$row->docdate.'</td>
				<td>'.$row->shortname.'</td>
				<td>
					'.((strlen($row->userid) && $row->userid) ? '<a href="/admin/users/'.$row->userid.'/2" target="_blank">Учётная карточка</a><br>' : '').'
					'.((!$row->userid) ? '<button class="deleter btn btn-warning btn-mini" id="bt'.$row->id.'" prop="'.$row->id.'" >Удалить</button>' : '').'
				</td>
				</tr>';
				array_push($output,$string);
			}
		}

		return implode($output,"\n")."</table>";
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
		$result = $this->db->query("UPDATE `users` SET `users`.service = ? WHERE `users`.id = ?", array($newcurator, $user));
		$this->load->helper("url");
		redirect("admin/users/".$user);
	}

	public function blockpc($invnum, $mode) {
		//$this->output->enable_profiler(TRUE);
		$this->db->query("UPDATE
		`hash_items`
		SET
		`hash_items`.active = ?
		WHERE `hash_items`.`id` = ?", array($mode, $invnum));
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */