<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsmodel2 extends CI_Model {

	public $dbwrite      = 0;		// запись в базу вкл/выкл
	public $wrap_to_word = 0;		// 
	public $genmode      = "word";

	function __construct(){
		parent::__construct();	// Call the Model constructor
		// определяем режим генерации заявки - по умолчанию - word
		if($this->session->userdata("admin_id") == 1) {
			//но если твой ID == 1 (korzhevdp) - тогда pdf
			$this->genmode = "doc";
		}
	}

	private function overrideTemplateData($staffID) {
		if ( $staffID == 11 || $staffID == 27 ) {
			$otv_dl = 'Заместитель Главы муниципального образования "Город Архангельск"';
		}
		if ( $staffID == 32 ) {
			$otv_dl = 'Советник Главы муниципального образования "Город Архангельск"';
		}
		if ( $staffID == 40 ) {
			$otv_dl = 'Помощник Главы муниципального образования "Город Архангельск"';
		}
		return array(
			'fio'    => $this->input->post("name_f")." ".strtoupper(substr($this->input->post("name_i"), 0, 1).".".substr($this->input->post("name_o").".", 0 ,1)),
			'otv_dl' => $otv_dl,
			'org'    => "", //"Мэрия города Архангельска";
			'cred'   => "пл.В.И.Ленина, д.5, г.Архангельск, 163000<br>тел. 65-64-84, факс 65-20-71<br>Е-mail: adminkir@arhcity.ru; http:// www.arhcity.ru",
			'zakaz'  => 'Общий отдел Администрации муниципального образования "Город Архангельск". Заказ 001.  01.01.2016'
		);
	}

	public function subproperties_get($res=0){
		/*
		* AJAX - возвращает HTML-обёрнутые возможные
		* дополнительные поля (например, поля pid2, перечисляющие 
		* определения в таблице resources_layers)
		*/
		$output = array();
		$result=$this->db->query("SELECT 
		`resources_layers`.id,
		`resources_layers`.ga,
		`resources_layers`.spn
		FROM
		`resources_layers`
		WHERE
		`resources_layers`.`rid` = ?
		AND `resources_layers`.`active`
		ORDER BY `resources_layers`.ga DESC, `resources_layers`.spn", array($res));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$markers = explode("(", $row->spn);
				$class = "";
				$title = ' title="Предоставить доступ к разделу '.$markers[0].'"';
				$note  = "";

				if (!$row->ga) {
					$class = " btn-warning";
					$title = ' title="Для предоставления доступа могут потребоваться дополнительные согласования"';
					$note  = '&nbsp;&nbsp;&nbsp;<i class="icon-exclamation-sign icon-white"></i>';
				}

				(!isset($markers[1])) 
					? $markers[1] = '&nbsp;' 
					: "";
				$button = '<span class="btn btn-block subspad'.$class.'" ref="'.$row->id.'" style="margin-bottom:5px;"'.$title.'>'.$markers[0].$note.'</span>';
				array_push($output, $button);
			}
		}
		print implode($output,"\n");
	}

	public function getWebPortalSection() {
		$output = array();
		//$this->output->enable_profiler(TRUE);
		$search = iconv('utf-8', 'windows-1251', $this->input->post('search'));
		$DB3 = $this->load->database('web', TRUE);
		$result = $DB3->query("SELECT
		section2.secname AS parent2,
		section1.secname AS parent1,
		section.secname,
		section.sid
		FROM
		section
		RIGHT OUTER JOIN section section1 ON (section.secparent = section1.sid)
		RIGHT OUTER JOIN section section2 ON (section2.sid = section1.secparent)
		WHERE
		((section.sid = ?) OR (section.secname LIKE ?))
		AND (LENGTH(section1.secname))", array($search, $search."%") );
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$string = '<li class="btn btn-small btn-block" title="'.$row->parent2." ".$row->parent1.'">['.$row->sid."/0] ".$row->secname.'</li>';
				array_push($output, $string);
			}
			print implode($output, "\n");
			return true;
		}
		print "<li>Ничего не найдено</li>";
	}

	########################################
	## inserting helper FX
	public function new_order_insert(){
		if( $this->dbwrite ){
			$this->db->query("INSERT INTO resources_orders (resources_orders.docdate) VALUES (NOW())");
			$orderID = $this->db->insert_id();
			return $orderID;
		}else{
			return 0;
		}
	}

	public function new_user_insert($data){
		$name_f = $data['name_f'];
		$name_i = $data['name_i'];
		$name_o = $data['name_o'];
		if( $this->dbwrite ){
			$result = $this->db->query("INSERT INTO users (
			users.`name_f`,
			users.`name_i`,
			users.`name_o`,
			users.`host`,
			users.`login`,
			users.`phone`,
			users.`service`,
			users.`supervisor`,
			users.`dep_id`,
			users.`office_id`,
			users.`staff_id`
			) VALUES ( TRIM(?), TRIM(?), TRIM(?), ?, ?, ?, ?, ?, ?, ?, ? )",
			array(
				$name_f,
				$name_i,
				$name_o,
				$data['login'],
				$data['login'],
				$data['phone'],
				$data['service'],
				$data['service'],
				$data['dept'],
				(isset($data['addr2']) && strlen($data['addr2']) && $data['addr2']) ? $data['addr2'] : $data['addr1'],
				$data['staff_id']
			));
			
			$userID = $this->db->insert_id();
			$this->session->set_userdata('uid', $userID);
			$_POST['uid'] = $userID; // вообще это неправильно совать и в сессию и в POST. Но функция ожидает uid там, а переписывать пока нет времени 31.03.2016
			$this->insert_to_cfsX($name_f, $name_i, $name_o);
		}

		return $userID;
	}

	private function insert_to_cfsX($name_f, $name_i, $name_o) {
		//внешний коннект. Создание пользователя на cfs2
		$DB2 = $this->load->database('12', TRUE);
		$DB2->query("SET NAMES cp1251");
		$result = $DB2->query("SELECT
			`fios`.`fio`
			FROM
			`fios` 
			WHERE TRIM(`fios`.`fio`) = TRIM(?)", array($name_f." ".$name_i." ".$name_o));
		if (!$result->num_rows()) {
			$DB2->query("INSERT INTO fios (fios.`fio`) VALUES (TRIM(?))", array($name_f." ".$name_i." ".$name_o));
		}
	}

	public function resource_insert($data){
		if( $this->dbwrite ){
			$output = array();
			foreach($data as $key => $val){
				$string = "('".$val['uid']."', '".$val['order_id']."', '".$val['rid']."', NOW())";
				array_push($output, $string);
			}
			$this->db->query("INSERT INTO `resources_items` (
			`resources_items`.uid,
			`resources_items`.order_id,
			`resources_items`.rid,
			`resources_items`.initdate
			) VALUES ".implode($output,",\n"));
		}else{
			return false;
		}
	}

	public function resource_subs_insert($subs){
		if( $this->dbwrite ){
			$this->db->insert_batch("resources_pid", $subs);
		}
	}

	## END inserting helper FX
	########################################

	public function to_utf8($data){
		//
		if(is_array($data)){
			foreach($data as $key=>$val){
				$data[$key] = iconv('windows-1251', 'utf-8', $val);
			}
			return $data;
		}
	}
	
	private function get_userid_by_order($order) {
		$userid = 0;
		$result = $this->db->query("SELECT 
		resources_items.uid
		FROM
		resources_items
		WHERE
		(resources_items.`id` = ?)", array($order));
		if ($result->num_rows()) {
			$row    = $result->row();
			$userid = $row->uid;
		}
		return $userid;
	}

	public function reget_orders($orders){
		//$this->output->enable_profiler(TRUE);
		$orders = explode(",", $this->input->post("resources"));	//получаем массив ресурсов
		if(!sizeof($orders)){
			print "Недостаточно данных";
			return false; // выход при случае ошибки
		}
		$userid = $this->get_userid_by_order($orders[0]);
		# инициализация массивов
		$resids = array();
		$res    = array();
		$confs  = array();
		$papers = array();
		$layers = array();

		# извлекаем 
		$result = $this->db->query("SELECT 
		resources_items.rid,
		resources.cat,
		resources.name,
		resources_pid.pid,
		CASE
		WHEN resources_pid.pid = 1 THEN CONCAT(resources_pid.pid_value,'@arhcity.ru')
		WHEN resources_pid.pid = 6 THEN INET_NTOA(resources_pid.pid_value)
		WHEN resources_pid.pid NOT IN (1,6) THEN resources_pid.pid_value
		END AS pid_value,
		resources_items.id,
		`resources_orders`.docnum,
		DATE_FORMAT(`resources_orders`.docdate,'%d.%m.%Y') AS docdate
		FROM
		resources_items
		INNER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN resources_pid ON (resources_items.id = resources_pid.item_id)
		LEFT OUTER JOIN `resources_orders` ON (resources_items.order_id = `resources_orders`.id)
		WHERE `resources_items`.`id` IN (".implode($orders,",").")");
		if($result->num_rows()){
			foreach($result->result_array() as $row){
				if(!isset($res[$row['id']])){
					$res[$row['id']] = $row;
					$res[$row['id']]['pid'.$row['pid']] = $row['pid_value'];
				}else{
					if($row['pid'] == 6 && !isset($row['pid'.$row['pid']])){
						$res[$row['id']]['pid'.$row['pid']] = $row['pid_value'];
					}
				}
			}
		}
		//print_r($res);
		//return false;
		//print $this->db->last_query();

		foreach($orders as $val){
			$_POST['ddate'] = $res[$val]['docdate'];
			$_POST['dnum']  = $res[$val]['docnum'];
			
			// получаем данные для заявки.
			$templatedata = $this->fill_template();
			//print_r($templatedata);

			if($res[$val]['rid'] == 102){
				// заявка на домен
				if($this->genmode == "pdf") {
					// оформляем заявку
					$string = $this->load->view('bids/2pdf/domain_p', $templatedata, true);
				}else{
					// оформляем заявку
					$string = $this->load->view('bids/papers/domain_p', $templatedata, true);
				}
				array_push($papers, $string);
				// циклограмма закончена, переходим на следующий ресурс
				continue;
			}

			if($res[$val]['rid'] == 274){
				// заявка на Wi-Fi.
				// Инициализация
				$addon = array(
					'mailaction'	=> "",
					'inetaction'	=> "",
					'decision'		=> array()
				);
				// $res[$val]['pid12'] - pid12 для этого ресурса должен, по идее, содержать обоснование для этого ресурса.
				$reason = (isset($res[$val]['pid12'])) 
					? $res[$val]['pid12'] 
					: '<i>информации о цели подключения не обнаружено</i>';
				$addon['inetaction'] = 'предоставить доступ к Интернет-ресурсам средствами беспроводной сети для '.$reason;
				array_push($addon['decision'], 'предоставить доступ к Интернет-ресурсам средствами беспроводной сети');

				$addon['decision'] = implode($addon['decision'], ", ");
				// слияние массивов дополнительных полей и основных.
				$templatedata = array_merge($templatedata, $addon);
				if($this->genmode == "pdf") {
					// конверсия полей для PDF
					// оформляем заявку
					$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
				}else{
					// оформляем заявку
					$string = $this->load->view('bids/papers/wf', $templatedata, true);
				}
				array_push($papers, $string);
				continue; // хватит пилить эту заявку, переходим на следующий ресурс
			}
		}

		// Internet/Email routine
		// инициализация
		$addon = array(
			'mailaction'	=> "",
			'inetaction'	=> "",
			'decision'		=> array()
		);
		$gen = 0; // флаг необходимости выходной генерации. 
		// используется вместо continue, чтобы иметь возможность помещения более 1 заявки в бланк в текущем алгоритме (косолапо, конечно)
		$sogl = array();
		foreach($orders as $val){
			if($res[$val]['rid'] == 101 || $res[$val]['rid'] == 100){
				// выборка содержимого pid12 - обоснования подключения
				$reason       = (isset($res[$val]['pid12'])) 
					? $res[$val]['pid12']
					: '( данные об обосновании подключения отсутствуют в системе )';
				$mail_address = (isset($res[$val]['pid1'])) 
					? $res[$val]['pid1'] 
					: "______@arhcity.ru";
				if ($res[$val]['rid'] == 100) {
					
					$addon['mailaction'] = "зарегистрировать почтовый ящик с адресом ".$mail_address."<BR>на сервере электронной почты для ".$reason;
					array_push($addon['decision'], 'зарегистрировать адрес электронной почты');
					$gen = 1;
				}
				if ($res[$val]['rid'] == 101) {
					$addon['inetaction'] = 'предоставить доступ к сети "Интернет" для '.$reason;
					array_push($addon['decision'], 'предоставить доступ к международной сети "Интернет"');
					$gen = 1;
				}
			}
		}

		// в случае выставления флага генерации исполняем
		if($gen){
			$addon['decision'] = implode($addon['decision'], ", ");
			// слияние массивов дополнительных полей и основных.
			$templatedata = array_merge($templatedata,$addon);
 			if($this->genmode == "pdf") {
				// генерация PDF - конверсия полей в UTF-8
				//$templatedata = $this->to_utf8($templatedata);
				// оформляем заявку
				$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
			}else{
				// оформляем заявку
				$string = $this->load->view('bids/papers/inml', $templatedata, true);
			}
			array_push($papers,$string);
			// continue; // хватит пилить эту заявку, переходим на следующий ресурс
		}
		#######################################################################################
		#### излишне ?
		/*
		$result = $this->db->query("SELECT 
		`resources_pid`.pid,
		`resources_pid`.pid_value,
		`resources_pid`.item_id
		FROM
		`resources_pid`
		WHERE
		`resources_pid`.`item_id` IN (".implode(array_keys($res),",").")");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($layers[$row->item_id])) ? $layers[$row->item_id] = array() : "";
				//(!isset($layers[$row->item_id][$row->pid])) ? $layers[$row->item_id][$row->pid] = array() : "";
				$layers[$row->item_id][$row->pid] = $row->pid_value;
			}
			if($this->genmode == "pdf") {
				//print_r($res);
				//exit;
			}
		}
		*/

		$result = $this->db->query("SELECT DISTINCT
		resources.cat,
		resources.id,
		resources_items.id AS itemid,
		resources.name,
		resources.bitmask,
		resources.shortname,
		resources.owner,
		staff.staff AS owner_staff,
		CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1), '. ', users.name_f) AS otv_dl_name
		FROM
		departments
		INNER JOIN staff ON (departments.chief = staff.id)
		INNER JOIN resources ON (departments.id = resources.owner)
		INNER JOIN users ON (departments.chief = users.staff_id)
		AND (resources.owner = users.dep_id)
		INNER JOIN resources_items ON (resources.id = resources_items.rid)
		WHERE
		resources_items.id IN (".implode(array_keys($res),",").") 
		AND resources_items.rid NOT IN (100,101,102,274)
		AND NOT `users`.fired");
		// массивы-накопители
		$process = array();
		$owners  = array();
		$layers1 = array();
		$layers2 = array();
		$layers3 = array();
		$layers4 = array();
		$email = $this->get_user_mail_from_db($userid);
		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($process[$row->cat])) ? $process[$row->cat]								= array() : "";
				(!isset($process[$row->cat][$row->owner])) ? $process[$row->cat][$row->owner]	= array() : "";
				(!isset($owners[$row->owner])) ? $owners[$row->owner]							= array() : "";
				$owners[$row->owner]['otv_dl_name']												= $row->otv_dl_name;
				$owners[$row->owner]['owner_staff']												= $row->owner_staff;
				$string																			= "";
				$string2																		= "";
				if($this->genmode == "pdf") {
					if(isset($res[$row->itemid]['pid2'])){
						$layerssrc = (explode("\n", $res[$row->itemid]['pid2']));
						//exit;
						$layers1 = array_slice($layerssrc, 0, 12);
						$layers2 = array_slice($layerssrc, 12);
						$layers3 = array_slice($layerssrc, 0, 20);
						$layers4 = array_slice($layerssrc, 20);
					}
					$string = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers1)) ? "<br>- ".implode($layers1, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers2) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers2)) ? "<br><br>- ".implode($layers2, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>Отметка о предоставлении доступа</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
					$string2 = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers3)) ? "<br>- ".implode($layers3, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers4) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers4)) ? "<br><br>- ".implode($layers4, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>Отметка о предоставлении доступа</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
				} else {
					$additional_info = "";
					if ($row->id == 286) {
						$additional_info = "<br>Приглашение на подключение учётной записи в ЕСИА к организации прошу выслать на почтовый ящик ". $email;
					}
					$string = '<tr>
					<td  style="font-size:11pt;text-align:center;">&nbsp;</td>
					<td  style="font-size:11pt;text-align:left;"><b>'.$row->name.'</b>'.((isset($res[$row->itemid]['pid2']) && strlen($res[$row->itemid]['pid2'])) 
						? "<br>- ".implode(explode("\n",$res[$row->itemid]['pid2']),"<br>- ") 
						: "").$additional_info.'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "11111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10011") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10001") ? "x" : "").'</td>
					</tr>
					<tr>
					<td  style="font-weight:bold;font-size:10px;" colspan=2>Отметка о предоставлении доступа</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					</tr>';
				}
				$process[$row->cat][$row->owner][$row->id] = $string;
				$sogl[$row->owner] = $string2;
			}
		}
		foreach($process as $cat => $all_owners){
			foreach($all_owners as $owner_id => $resource_list){
				$templatedata						= $this->fill_template();
				$templatedata['res_container']		= implode($resource_list);		// список ресурсов для листа заявки
				$templatedata['res_container_sogl'] = $sogl[$owner_id];		// правильно пошинкованный в длину список ресурсов для листа согласований
				$arrayids							= array_keys($resource_list);
				$templatedata['owner_staff']		= $owners[$owner_id]['owner_staff'];
				$templatedata['r_owner']			= $owners[$owner_id]['otv_dl_name'];
				$templatedata['layers3']			= $layers3;
				$templatedata['layers4']			= $layers4;
				$sogltype = ($owner_id == "12") 
					? "14" 
					: "" ; // 12 - идентификатор подразделения владельца (ИнГео -- ДГр). Выгоднее при определении подписантов листа согласований.
				
				if($this->genmode == "pdf") {
					//$templatedata = $this->to_utf8($templatedata);
					$page = ($cat > 1) ? $this->load->view('bids/2pdf/conf', $templatedata, true) : $this->load->view('bids/2pdf/common', $templatedata, true);
					if(!sizeof($papers)){
						array_push($papers,"<page>".$page."</page>");
					}else{
						array_push($papers,"<page>".$page."</page>");
					}
					($cat > 1) 
						? array_push($papers, "<page>".$this->load->view('bids/2pdf/sogl'.$sogltype, $templatedata, true)."</page>") 
						: "";
					($cat > 1) 
						? array_push($papers, "<page>".$this->load->view('bids/2pdf/ordermemo', $templatedata, true)."</page>") 
						: "";
				}else{
					$page = ($cat > 1) 
						? $this->load->view('bids/papers/conf', $templatedata, true) 
						: $this->load->view('bids/papers/common', $templatedata, true);
					array_push($papers,$page);
					($cat > 1) 
						? array_push($papers,$this->load->view('bids/papers/sogl'.$sogltype, $templatedata, true)) 
						: "";
					($cat > 1) 
						? array_push($papers,$this->load->view('bids/papers/ordermemo', $templatedata, true)) 
						: "";
				}
			}
		}
		
		if($this->genmode == "pdf") {
			// работаем под администратором
			$this->return_PDF($papers);
		}else{
			$filename = 'Переоформление_заявок_на_информационные ресурсы_'.implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_").'.doc';
			if($this->wrap_to_word){
				$pagebreak = "<span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\";mso-fareast-font-family:\"Times New Roman\";mso-ansi-language:EN-US;mso-fareast-language:EN-US;mso-bidi-language:JI'><br clear=all style='page-break-before:always;mso-break-type:section-break'></span>";
				$outfile = implode($papers, $pagebreak);
				$this->load->helper('download');
				force_download($filename, $outfile);
			}else{
				//$this->output->enable_profiler(TRUE);
				$pagebreak = "<hr>";
				$outfile = implode($papers, $pagebreak);
				print $outfile;
			}
		}
		
		//		header('Content-Description: File Transfer');
		//		header("Content-Type: application/msword");
		//		header("Content-Type: application/force-download;");
		//		header("Content-Disposition: inline; filename=\"Переоформление_заявок_на_информационные ресурсы_".implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_")).".doc\"";
	}

	public function return_PDF($papers){
		/*
		* подключает класс формирования PDF
		* и создаёт PDF-документ
		*/
		// Имя файла
		$filename = iconv( 'windows-1251', 'utf-8', 'Переоформление_заявок_на_информационные ресурсы_'.implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_").'.pdf' );
		// Заголовки и закрытие документа (для прохождения валидности)
		$head = $this->load->view("bids/2pdf/chainhead", array(), true);
		$aft = $this->load->view("bids/2pdf/chainaft", array(), true);
		// Формируем строковый выхлоп.
		$outfile = $head.implode($papers, "").$aft;
		// сброс буферов
		ob_start();
		$content = ob_get_clean();
		// Подключение класса. Внимательно проследить за путями!
		require_once($_SERVER['DOCUMENT_ROOT'].'/2pdf/html2pdf.class.php');
		// Передаём параметры парсеру
		try{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->setDefaultFont('Times');
			$html2pdf->writeHTML($outfile, isset($_GET['vuehtml']));
			$html2pdf->Output($filename);
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

}

/* End of file bidsmodel2.php */
/* Location: ./application/models/bidsmodel2.php */