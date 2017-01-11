<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsmodel extends CI_Model {

	public $dbwrite      = 1;		// запись в базу вкл/выкл
	public $wrap_to_word = 1;		// 
	public $genmode      = "word";
	public $selfHeader   = array(10, 52, 81, 82);

	function __construct(){
		parent::__construct();	// Call the Model constructor
		// определяем режим генерации заявки - по умолчанию - word
		if($this->session->userdata("admin_id") == 1) {
			//но если твой ID == 1 (korzhevdp) - тогда pdf
			$this->genmode = "doc";
		}
	}

	public function getResourceAccordion($rlist, $group_id){
		$addition = "";
		$state    = "";
		$hide     = "";
		if ($group_id == 11) {
			$hide     = ' hide';
			$addition = ' id="domain_data"';
			$state    = ' in';
		}
		$resgroups    = array(
			0  => 'Прочие',
			1  => '1C: программные продукты',
			2  => 'Сервера баз данных MS SQL',
			3  => 'Регистрация обращений граждан',
			4  => 'Справочно-Правовые Системы',
			5  => 'Управление по вопросам семьи опеки и попечительства',
			6  => 'Автоматизированные информационные системы (АИС / ГИС)',
			7  => 'Департамент муниципального имущества',
			8  => 'Департамент образования',
			9  => 'Городское хозяйство',
			11 => 'Заявка на подключение к сети муниципалитета',
			10 => 'Интернет и электронная почта',
			12 => 'Беспроводная сеть Wi-Fi',
			13 => 'ЕСИА / Государственные услуги'
		);
		if ( !isset($rlist[$group_id])) {
			$rlist[$group_id] = array();
		}
		return '
		<div class="accordion-group'.$hide.'"'.$addition.'>
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse'.$group_id.'" href="#collapse'.$group_id.'">
					<span class="badge badge-success hide" id="badge-collapse'.$group_id.'">0</span>'.$resgroups[$group_id].'
				</a>
			</div>
			<div id="collapse'.$group_id.'" ref="'.$group_id.'" class="accordion-body collapse'.$state.'">
				<div class="accordion-inner">
					<ul class="rlist" id="group'.$group_id.'" style="margin:0px;">
						'.implode($rlist[$group_id], "\n").'
					</ul>
				</div>
			</div>
		</div>';
	}

	private function get_user_mail_from_db($userid) {
		$email = "";
		$result = $this->db->query("SELECT 
		CONCAT(resources_pid.pid_value, '@arhcity.ru') as email
		FROM
		resources_pid
		RIGHT OUTER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
		WHERE
		(resources_pid.pid = 1)
		AND (resources_items.uid = ?)
		AND (`resources_items`.`ok`)
		AND NOT (`resources_items`.`del`)
		AND NOT (`resources_items`.`exp`)
		ORDER BY resources_items.id DESC
		LIMIT 1", array($userid));
		if ($result->num_rows()) {
			$row = $result->row();
			$email = $row->email;
		}
		return $email;
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

	public function user_data_get2($userid) {
		$email = $this->get_user_mail_from_db($userid);
		$result = $this->db->query("SELECT 
		users.name_f,
		users.name_i,
		users.name_o,
		users.login,
		users.dep_id,
		users.staff_id,
		users.office_id,
		users.phone,
		`locations`.parent
		FROM
		users
		INNER JOIN `locations` ON (users.office_id = `locations`.id)
		WHERE
		(users.id = ?)", array($userid));
		if($result->num_rows()){
			$row = $result->row();
			return "udt = {
				name_f : '".$row->name_f."',
				name_i : '".$row->name_i."',
				name_o : '".$row->name_o."',
				login  : '".$row->login."',
				dept   : '".$row->dep_id."',
				staff  : '".$row->staff_id."',
				bldg   : '".$row->parent."',
				office : '".$row->office_id."',
				phone  : '".$row->phone."',
				email  : '".$email."'
			}";
		}
	}

	public function user_res_get($userid){
		// формируем табличку пользовательских ресурсов
		$result = $this->db->query("SELECT 
		DATE_FORMAT(resources_orders.docdate, '%e.%m.%Y') AS docdate,
		resources_items.ok,
		resources_items.id,
		resources_items.`exp`,
		resources_items.apply,
		resources_orders.docnum,
		resources.shortname,
		CONCAT_WS(' ',users1.name_f,users1.name_i,users1.name_o) AS sman,
		users1.phone
		FROM
		resources_orders
		INNER JOIN resources_items ON (resources_orders.id = resources_items.order_id)
		INNER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN `users` ON (resources_items.uid = `users`.id)
		INNER JOIN `users` users1 ON (`users`.service = users1.id)
		WHERE
		`resources_items`.`uid` = ? AND
		NOT `resources_items`.`del`",array($userid));
		$output = array();
		if ($result->num_rows()){
			// заголовки таблицы двумя строками
			array_push($output,'<table class="table table-striped table-hovered table-bordered" style="margin-left: 0px;width:704px;">');
			array_push($output,'<tr>
				<th>Информационный ресурс</th>
				<th style="width:100px;">Дата подачи</th>
				<th style="width:110px;">Номер заявки</th>
				<th style="width:210px;">Статус</th>
				<th style="width:25px;text-align:center;vertical-align:middle;" title="Получить копию всех заявок">
					<label for="checkAllPapers" style="cursor:pointer;"><input type="checkbox" id="checkAllPapers"></label>
				</th>
			</tr>');
			// далее, каждая заявка на ресурс в своей строке
			foreach($result->result() as $row){
				// признак исполнения заявки ОСА
				$status = ($row->ok) 
					// признак исполнения заявки куратором
					? ($row->apply) 
						? 'Выполнена&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="Ресурс подключен и настроен" style="cursor:pointer"></i>' 
						: 'Выполнена<br>Обратитесь к куратору&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="'.$row->sman.', тел.: '.$row->phone.'" style="cursor:pointer"></i>' 
					: 'На согласовании&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="Собираются подписи" style="cursor:pointer"></i>';
				// признак отмены заявки
				$status = ($row->exp) ? "Отменена" : $status;
				$string = '<tr>
					<td>'.$row->shortname.'</td>
					<td>'.$row->docdate.'</td>
					<td>'.$row->docnum.'</td>
					<td>'.$status.'</td>
					<td style="text-align:center;vertical-align:middle;"><input type="checkbox" class="paperChecker" ref="'.$row->id.'" title="Получить копию этой заявки"></td>
				</tr>';
				//помещаем строку в таблицу
				array_push($output, $string);
			}
			//завершаем таблицу
			array_push($output,'</table>');
		}else{
			//если ни одного ресурса нет
			array_push($output,'<div id="bidstatus"><h3 class="muted">У этого пользователя нет поданных заявок</h3></div>');
		}
		return implode($output, "\n");
	}

	public function locs_get(){
		$input  = array();
		$output = array();
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',locations.id,'>',locations.address,'</option>') AS options,
		`locations`.parent
		FROM locations
		WHERE 
		`locations`.parent <> 0 
		AND `locations`.id <> 0
		ORDER BY `locations`.`address`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($input[$row->parent])){
					$input[$row->parent] = array();
				}
				array_push($input[$row->parent], $row->options);
			}
		}
		foreach($input as $key=>$val){
			array_push($output, "\t\t".$key.": ['".implode($val, "', '")."']");
		}
		return "{\n".implode($output, ",\n")."\n}";
	}

	public function user_data_get($userid){
		/*
		* user_data_get($userid). Получает данные пользователя. 
		* Получает int - ID пользователя. 
		* Возвращается массив данных пользователя
		*/
		// Получаем основные данные пользователя
		$this->session->set_userdata("uid", $userid);
		$result = $this->db->query("SELECT 
		users.name_f,
		users.name_i,
		users.name_o,
		users.login,
		users.dep_id,
		users.staff_id,
		users.office_id,
		users.phone,
		`locations`.parent
		FROM
		users
		INNER JOIN `locations` ON (users.office_id = `locations`.id)
		WHERE
		(users.id = ?)", array($userid));
		if ($result->num_rows()){
			$return = $result->row_array();
		}

		// Получаем содержимое списка SELECT для подразделений.
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id, IF(departments.id = ?,' selected',''),'>',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn", array($return['dep_id']));
		$output = array("<option value=0>не выбрано</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		// подмешиваем подразделения в элемент массива выхлопа
		$return['dept'] = implode($output,"\n");

		// Получаем содержимое списков SELECT для помещений
		/*
		$result = $this->db->query("SELECT `locations`.`id`,
		`locations`.`address`,
		CASE
			WHEN ASCII(RIGHT(`address`, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`address`, '-'), 16, '0')
			ELSE LPAD(`address`, 16, '0') END AS `vsort`
		FROM `locations`
		WHERE `locations`.id <> 0 AND
		`locations`.`parent` = ?
		ORDER BY `locations`.`parent`, `vsort`",array($return['parent']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				$selected = ($row->id == $return['office_id']) ? 'selected="selected"' : '';
				array_push($output,'<option value='.$row->id.' '.$selected.'>'.$row->address.'</option>');
			}
			$return['location'][1] = implode($output,"\n");
		}
		*/
		$result = $this->db->query("SELECT 
		CONCAT(
			'<option value=',
			locations.id,
			IF(`locations`.id = ?, ' selected', '')
			,' >'
			,locations.address,
			'</option>') AS options
		FROM locations
		WHERE `locations`.parent = 0 AND
		`locations`.id <> 0
		ORDER BY `locations`.`address`", array($return['parent']));
		if ($result->num_rows()){
			$output = array();
			foreach($result->result() as $row){
				array_push($output, $row->options);
			}
			$return['location'][0] = implode($output, "\n");
		}

		$result = $this->db->query("SELECT
		CONCAT('<option value=',locations.id, IF(locations.id = ?,' selected',''),'>',locations.address,'</option>') AS options
		FROM
		locations
		WHERE
		locations.parent = ?
		ORDER BY 
		locations.address", array($return['office_id'], $return['parent']));
		$output = array("<option value=0>не выбрано</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		// подмешиваем помещения в элемент массива выхлопа
		$return['location'][1] = implode($output,"\n");

		if (!$return['parent']) {
			$return['location'][0] = (isset($return['location'][1])) ? $return['location'][1] : $return['location'][0];
			$return['location'][1] = "";
		}
		
		// Получаем содержимое списков SELECT для должностей
		$result = $this->db->query("SELECT
		CONCAT(
		'<option value=',
		`staff`.`id`,
		IF(`staff`.`id` = ?, ' selected>','>'),
		`staff`.`staff`,
		'</option>') AS options
		FROM
		`staff`
		ORDER BY `staff`.`staff`",array($return['staff_id']));
		$output = array("<option value=0>не выбрано</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output ,$row->options);
			}
		}
		// подмешиваем помещения в элемент массива выхлопа
		$return['staff'] = implode($output,"\n");

		// Получаем содержимое списков информационных ресурсов, 
		// для упаковки их в различные разделы аккордеона в соответствии с параметром GRP
		// с учётом категории информационного ресурса в его отображении
		$result = $this->db->query("SELECT
		resources.id,
		resources.shortname,
		resources.cat,
		resources.grp,
		COUNT(resources_layers.id) AS con
		FROM
		resources
		LEFT OUTER JOIN resources_layers ON (resources.id = resources_layers.rid)
		WHERE
		(resources.active)
		GROUP BY
		resources.id
		ORDER BY
		resources.name");
		if ($result->num_rows()){
			$output = array();
			foreach($result->result() as $row){
				(!isset($output[$row->grp])) 
					? $output[$row->grp] = array() 
					: '';
				// определяем цветовую схему отображения (категория)
				$class = ($row->cat > 1) 
					? "btn-warning" 
					: "btn-info";
				// определяем параметр пункта меню (категория)
				$conf = ($row->cat > 1) 
					? "1" 
					: "0";
				// определяем пункт меню (категория)
				$string = '<li class="reslist btn '.$class.' btn-block" id="r_'.$row->id.'" grp="'.$row->grp.'" subs="'.$row->con.'" conf="'.$conf.'" title="Щелчок добавит этот '.(($row->cat > 1) 
					? 'КОНФИДЕНЦИАЛЬНЫЙ' 
					: '').' ресурс в список выбранных">'.$row->shortname.'</li>';
				// помещаем пункт в соответствующий раздел аккордеона
				array_push($output[$row->grp],$string);
			}
		}
		// подмешиваем список ресурсов в элемент массива выхлопа
		$return['rlist'] = $output;

		// формируем табличку пользовательских ресурсов
		// подмешиваем список заявок
		$return['ordersProcessed'] = implode($this->users_res_get($userid), "\n");
		// выхлоп
		return $return;
	}

	public function blank_data_get(){
		/*
		* Входные данные не требуются (конструктор)
		* Возвращается массив данных пользователя
		*/
		// начальный пустой массив данных
		$return = array(
			'name_f'		=> '',
			'name_i'		=> '',
			'name_o'		=> '',
			'phone'			=> '',
			'login'			=> '',
			'location'		=> array('','')
		);
		
		// создаётся список подразделений
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id,'>',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn");
		$output = array("<option value=0>не выбрано</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		$return['dept'] = implode($output,"\n");

		// создаётся список должностей
		$result = $this->db->query("SELECT
		CONCAT('<option value=',`staff`.`id`,'>',`staff`.`staff`,'</option>') as options
		FROM
		`staff`
		ORDER BY `staff`.`staff`");
		$output = array("<option value=0>не выбрано</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output ,$row->options);
			}
		}
		$return['staff'] = implode($output,"\n");

		// создаётся список помещений
		$result = $this->db->query("SELECT 
		CONCAT('<option value=',locations.id,'>',locations.address,'</option>') AS options
		FROM locations
		WHERE `locations`.parent = 0 AND
		`locations`.id <> 0
		ORDER BY `locations`.`address`");
		if ($result->num_rows()){
			$output = array("<option value=0>не выбрано</option>");
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
			$return['location'][0] = implode($output, "\n");
		}

		// создаётся список ресурсов
		$result = $this->db->query("SELECT
		resources.id,
		resources.shortname,
		resources.cat,
		resources.grp,
		COUNT(resources_layers.id) AS con
		FROM
		resources
		INNER JOIN departments ON (resources.owner = departments.id)
		LEFT OUTER JOIN resources_layers ON (resources.id = resources_layers.rid)
		WHERE
		(resources.active)
		GROUP BY
		resources.id
		ORDER BY
		resources.name");
		if ($result->num_rows()){
			$output = array();
			foreach($result->result() as $row){
				(!isset($output[$row->grp])) 
					? $output[$row->grp] = array() 
					: '';
				$class = ($row->cat > 1) 
					? "btn-warning" 
					: "btn-info";
				$conf = ($row->cat > 1) 
					? "1" 
					: "0";
				$string = '<li class="reslist btn '.$class.' span12" id="r_'.$row->id.'" grp="'.$row->grp.'" subs="'.$row->con.'" conf="'.$conf.'" style="margin: 2px 0px;"  title="Щелчок добавит ресурс в список выбранных">'.$row->shortname.'</li>';
				array_push($output[$row->grp],$string);
			}
			$return['rlist'] = $output;
		}
		// список ресурсов пуст, априорно :)
		$return['ordersProcessed']='<h3 class="muted">Не выбран пользователь</h3>';

		return $return;
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

	public function papers_get(){
		/*
		* Получение заявок (первичное). Принимает на вход отсортированные данные из _POST
		* Выдаёт строку HTML для парсера направляемую пользователю либо напрямую (Word)
		* либо на вход HTML2PDF для пернаправления пользователю документа PDF
		*/
		// $genmode = $this->session->userdata('genmode');
		
		// genmode override
		$genmode = "word";
		
		$papers = array();
		$outfile = "";
		// парсим в массивы коллекции полученных ресурсов: обычные и конфиденциальные
		$res = (strlen($this->input->post("res")))
			? explode(",", $this->input->post("res"))
			: array();
		$confs = (strlen($this->input->post("confs")))
			? explode(",", $this->input->post("confs"))
			: array();

		/*
		* Получение заявок по типам генерации
		*/
		
		// домен
		if(in_array(102, $res)) {
			array_push($papers, $this->domain_paper_get());
		}
		foreach($res as $key=>$val){
			if($val == 102){
				unset($res[$key]);
			}
		}
		// интернет/электронная почта
		if(in_array(101, $res) || in_array(100, $res)) {
			array_push($papers, $this->internet_mail_paper_get($res));
		}
		foreach($res as $key=>$val){
			if($val == 101 || $val == 100){
				unset($res[$key]);
			}
		}

		// Wi-Fi
		if(in_array(274, $res)) {
			array_push($papers, $this->wf_paper_get($res));
		}
		foreach($res as $key=>$val){
			if($val == 274){
				unset($res[$key]);
			}
		}

		// Adm Rights
		if(in_array(283, $res)) {
			array_push($papers, $this->adm_doc_get(283));
		}
		foreach($res as $key=>$val){
			if($val == 283){
				unset($res[$key]);
			}
		}


		//print 111;
		/*
		* конфиденциальные ресурсы и обычные - извлекаются в 2 режимах
		* одной и той же функции
		*/
		$commons = (sizeof($res)) 
			? $this->common_res_paper_get($res, $papers)
			: array();
		$confidents = (sizeof($confs)) 
			? $this->common_res_paper_get($confs, $papers, 1)
			: array();
		if($this->genmode == "pdf") {
			$this->return_PDF($this->to_utf8(array_merge($papers, $commons, $confidents)));
		} else {
			if( $this->wrap_to_word ){
				$pagebreak = "<span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\";mso-fareast-font-family:\"Times New Roman\";mso-ansi-language:EN-US;mso-fareast-language:EN-US;mso-bidi-language:JI'><br clear=all style='page-break-before:always;mso-break-type:section-break'></span>";
				$outfile = implode(array_merge($papers, $commons, $confidents), $pagebreak);
				$filename = 'Заявки на информационные ресурсы '.implode(
				array(
					$this->input->post('name_f'),
					$this->input->post('name_i'),
					$this->input->post('name_o')
				), "_").'.doc';
				$this->load->helper('download');
				force_download($filename, $outfile);
			}else{
				$this->output->enable_profiler(TRUE);
				$pagebreak = "<hr>";
				$outfile = implode(array_merge($papers, $commons, $confidents), $pagebreak);
				print $outfile;
			}
		}
	}

	public function fill_template(){
		//
		// заполнение бланка заявки типовыми данными
		//
		// получаем данные массива _POST переданные со страницы. 
		// пользователю предоставлена возможность исправить данные о себе
		$data = $this->input->post();
		$mod = 1;
		//print_r($data);
		//exit;
		if(isset($data['sname']) && isset($data['name']) && isset($data['fname'])){
			$mod == 2;
			$data['name_f'] = $data['sname'];
			$data['name_i'] = $data['name'];
			$data['name_o'] = $data['fname'];
		}
		if(!isset($data['staff_id'])){
			$data['staff_id'] = $data['staff']; // нормализуем поля форм - вынужденная мера, если честно
		}
		if(!isset($data['staff'])){
			$data['staff'] = $data['staff_id']; // нормализуем поля форм - вынужденная мера, если честно
		}		

		$genmode = $this->session->userdata('genmode');
		$genmode = "word";
		// определение начального подразделения.
		$deptlist = array();
		// если не было выбрано подразделение
		// ассертивно предполагается мэрия в целом (ID = 25)
		$data['deptForTemplate'] = 0;
		$real_d = ($data['dept'])
			? $data['dept']
			: 25;
		// переходим к определению подразделения
		$result = $this->db->query("SELECT `departments`.id, `departments`.parent FROM `departments`");
		if($result->num_rows()){
			// в ассоциативный массив помещаются данные о родительском подразделении
			foreach($result->result() as $row){
				$deptlist[$row->id] = $row->parent;
			}
		}

		if(!in_array($real_d, $this->selfHeader)) {
			// пробегаем вверх по иерархии структуры
			while ($deptlist[$real_d]){
				if(!$deptlist[$deptlist[$real_d]]){ 
					// пока не обнаруживаем, что ID родительский 
					// подразделений ВНЕЗАПНО равен 0
					// прекращаем пробежку
					break;
				}
				// а так - просто присваиваем пойнтеру значение ID родителя
				$real_d = $deptlist[$real_d];
			}
		}
		$data['deptForTemplate'] = $real_d;
		

		// дописываем в поле признак заголовка корневой организации
		// 10 - Горсовет
		// 52 - Избирком - им заголовок мэрии не нужен.
		//
		$data['top_header'] = (in_array($real_d, $this->selfHeader))
			? ""
			: 'АДМИНИСТРАЦИЯ МУНИЦИПАЛЬНОГО ОБРАЗОВАНИЯ "ГОРОД АРХАНГЕЛЬСК"';

		// выборка данных по должности пользователя
		// $data['staff_id'] пришла из _POST


		$result = $this->db->query("SELECT
			CONCAT(', ', `staff`) AS `staff`
			FROM `staff`
			WHERE `id` = ?", array((($mod == 2) ? $data['staff_id'] : $data['staff'])));
		if($result->num_rows()){
			$staff = $result->row_array();
			// слияние массивов
			$data = array_merge($data, $staff);
		}
		//print_r($data);
		//return false;
		//
		// Извлекаем прочие признаки бланка заявки
		// ФИО руководителя подразделения-эмитента
		// Должность руководителя подразделения-эмитента
		// Фактор ИО
		// Название подразделения
		// Реквизиты
		// Бланк по номенклатуре Общего Отдела
		//

		$result = $this->db->query("SELECT
		CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1), '. ', users.name_f) AS fio,
		users.io,
		`staff`.staff AS `otv_dl`,
		UCASE(departments.`dn_blank`) AS `org`,
		departments.`cred`,
		departments.`zakaz`
		FROM
		users
		INNER JOIN departments ON (users.`dep_id` = departments.id)
		INNER JOIN `staff` ON (departments.chief = `staff`.id)
		WHERE
		users.staff_id = departments.chief
		AND NOT users.fired
		AND departments.id = ?", array($real_d));
		if($result->num_rows()){
			$dept = $result->row_array();
			$dept['otv_dl'] = (!$dept['io']) ? $dept['otv_dl'] : "И.о. ".$dept['otv_dl'];
			// слияние массивов
			$data = array_merge($data, $dept);
		}
		$dept = array();
		##### Внимание, начинается врезка с помощниками и советниками #####

		if (in_array($data['staff_id'], array(11, 27, 32, 40))) {
			$dept = $this->overrideTemplateData($data['staff_id']);
		}

		$data = array_merge($data, $dept);

		// выборка данных по куратору, исходя из ПОДРАЗДЕЛЕНИЯ

		// самому себе в глаз дать - несподручно. ИСПРАВИТЬ
		// ИСПРАВИЛ

		// $data['dept'] пришла из _POST
		$result = $this->db->query("SELECT 
		admins.base_id as service
		FROM
		departments
		INNER JOIN admins ON (departments.service = admins.id)
		WHERE
		(departments.id = ?)", array($data['dept']));
		if($result->num_rows()){
			$serv = $result->row_array();
			// слияние массивов
			$data = array_merge($serv, $data);
		}
		
		// выбираем действующий признак размещения
		// либо здание с кабинетом (признак второго порядка)
		// либо обобщённый - только здание - первый порядок

		if(!isset($data['addr2']) && isset($data['office2'])) {
			$data['addr2'] = $data['office2']; // нормализуем поля форм - снова вынужденная мера
		}

		if(!isset($data['addr1'])){
			$data['addr1'] = $data['office']; // нормализуем поля форм - снова вынужденная мера
		}

		$addrtag = (isset($data['addr2']) && strlen($data['addr2']))
			? $data['addr2'] 
			: $data['addr1'];
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', locations1.address, `locations`.address) AS fulladdress
		FROM `locations`
		INNER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE locations.`id`= ?", array($addrtag));
		if($result->num_rows()){
			$addr = $result->row_array();
			// слияние массивов
			$data = array_merge($addr, $data);
		}

		// простая выборка названия должности // нормализую staffname и staff в дальнейшем одно из полей исключить
		$result = $this->db->query("SELECT
			`staff`.`staff` AS `staffname`,
			`staff`.`staff`
			FROM `staff` 
			WHERE `id` = ?", array($data['staff_id']));
		if($result->num_rows()){
			$staff = $result->row_array();
			$data = array_merge($staff, $data);
		}

		if( $addrtag ){
			$query = "SELECT `address` FROM `locations` WHERE `id` IN ('".$addrtag."')";
		}else{
			if (!$addrtag){
				$data['office'] = 1;
			}
			$query = "SELECT `address` FROM `locations` WHERE `id` = ".$addrtag;
		}

		$result = $this->db->query($query);
		if($result->num_rows()){
			$addr = $result->row_array();
			$data = array_merge($data, $addr);
		}

		(isset($data['ddate']) && strlen($data['ddate'])) 
			? "" 
			: $data['ddate'] = date("d.m.Y");
		(isset($data['dnum']) && strlen($data['dnum'])) 
			? ''
			: $data['dnum'] = '';
		
		// пока - в режиме отладки
		if($this->genmode == "pdf") {
			//
			// для "варки" PDF (HTML2PDF) требуется перевести в UTF-8 (оставлены 
			// дефолтные настройки класса, целях максимальной out-of-box-совместимости)
			// переводим (для наглядности "индусским кодом") поля $data в UTF-8
			//
			//$data = $this->to_utf8($data);
			/*
			$data['top_header'] = iconv('windows-1251', 'utf-8', $data['top_header']);
			$data['otv_dl']		= iconv('windows-1251', 'utf-8', $data['otv_dl']);
			$data['org']		= iconv('windows-1251', 'utf-8', $data['org']);
			$data['cred']		= iconv('windows-1251', 'utf-8', $data['cred']);
			$data['zakaz']		= iconv('windows-1251', 'utf-8', $data['zakaz']);
			$data['name_f']		= iconv('windows-1251', 'utf-8', $data['name_f']);
			$data['name_i']		= iconv('windows-1251', 'utf-8', $data['name_i']);
			$data['name_o']		= iconv('windows-1251', 'utf-8', $data['name_o']);
			$data['fio']		= iconv('windows-1251', 'utf-8', $data['fio']);
			$data['staff']		= iconv('windows-1251', 'utf-8', $data['staff']);
			$data['address']	= iconv('windows-1251', 'utf-8', $data['fulladdress']);
			*/
		}
		
		//print_r($data);
		//exit;
		//print_r($data);
		return $data;
	}

	public function domain_paper_get(){
		$genmode = $this->session->userdata('genmode');
		$data    = $this->input->post();										// читаем _POST в переменную
		$templatedata = $this->fill_template();									// получаем данные для заявки.
		if (!$this->session->userdata('uid')) {									// прерываем исполнение, если есть подозрение, что пользователь уже есть в базе
			$userID  = $this->new_user_insert($templatedata);					// вставляем нового пользователя в базу данных, получаем индекс этой вставки
			$orderID = $this->new_order_insert();								// вставляем в базу новую заявку, получаем индекс новой заявки
			$resdata = array(array('uid' => $userID, 'order_id' => $orderID, 'rid' => 102));
			$this->resource_insert($resdata);									// вставляем сущность ресурса с его привязкой к пользователю и заявке
		}
		if($this->genmode == "pdf") {
			// оформляем заявку
			$string = $this->load->view('bids/2pdf/domain_p', $templatedata, true);
		}else{
			// оформляем заявку
			$string = $this->load->view('bids/papers/domain_p', $templatedata, true);
		}
		return $string;
	}

	public function internet_mail_paper_get($res){
		$genmode				= $this->session->userdata('genmode');
		$templatedata			= $this->fill_template();						// получаем данные для заявки.
		$resdata				= array();
		$subsdata				= array();
		$addon					= array();
		$addon['mailaction']	= "";
		$addon['inetaction']	= "";
		$addon['decision']		= array();
		$orderID = $this->new_order_insert();									// вставляем в базу новую заявку, получаем индекс новой заявки
		if (in_array(100, $res)) {
			$addon['mailaction'] = "зарегистрировать почтовый ящик с адресом ".strtolower($this->input->post('email_addr'))."@arhcity.ru<BR>на сервере электронной почты ".$this->input->post('email_reason');
			array_push($addon['decision'], 'зарегистрировать адрес электронной почты');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 100));
		}
		if (in_array(101, $res)) {
			$addon['inetaction'] = 'предоставить доступ к сети "Интернет" '.$this->input->post('inet_reason');
			array_push($addon['decision'], 'предоставить доступ к международной сети "Интернет"');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 101));
		}
		$addon['decision'] = implode($addon['decision'], ", ");
		$templatedata = array_merge($templatedata,$addon);								// слияние массивов дополнительных полей и основных.

		if($this->genmode == "pdf") {
			// генерация PDF - конверсия полей в UTF-8
			//$templatedata = $this->to_utf8($templatedata);
			// оформляем заявку
			$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
		}else{
			// оформляем заявку
			$string = $this->load->view('bids/papers/inml', $templatedata, true);
		}

		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? 
		AND `resources_items`.`rid` IN (100,101)", array($orderID));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if($row->rid == 100){
					array_push($subsdata, array('pid' => 1, 'pid_value' => $this->input->post('email_addr'), 'item_id' => $row->id));
					array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('inet_reason'), 'item_id' => $row->id));
				}
				if($row->rid == 101){
					array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('email_reason'), 'item_id' => $row->id));
				}
			}
		}
		$this->resource_subs_insert($subsdata);
		return $string;
	}

	public function wf_paper_get($res=274){
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// получаем данные для заявки.
		$resdata = array();
		$subsdata = array();
		$addon = array();
		$addon['mailaction'] = "";
		$addon['inetaction'] = "";
		$addon['decision'] = array();
		$orderID = $this->new_order_insert();							// вставляем в базу новую заявку, получаем индекс новой заявки
		if (in_array(274,$res)) {
			$addon['inetaction'] = 'предоставить доступ к Интернет-ресурсам средствами беспроводной сети для '.$this->input->post('wf_reason');
			array_push($addon['decision'], 'предоставить доступ к беспроводной сети');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 274));
		}
		$addon['decision'] = implode($addon['decision'], ", ");
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ?
		AND `resources_items`.`rid` IN (274)", array($orderID));
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('wf_reason'), 'item_id' => $row->id));
			}
		}
		$this->resource_subs_insert($subsdata);
		$templatedata = array_merge($templatedata,$addon);							// слияние массивов дополнительных полей и основных.

		if($this->genmode == "pdf") {
			// конверсия полей для PDF
			// оформляем заявку
			//$templatedata = $this->to_utf8($templatedata);
			$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
		}else{
			// оформляем заявку
			$string = $this->load->view('bids/papers/wf', $templatedata, true);
		}
		return $string;
	}

	public function adm_doc_get($res=283){
		$genmode             = $this->session->userdata('genmode');
		$templatedata        = $this->fill_template();							// получаем данные для заявки.
		$resdata             = array();
		$subsdata            = array();
		$addon               = array();
		$addon['mailaction'] = "";
		$addon['inetaction'] = "";
		$addon['decision']   = array();
		$orderID             = $this->new_order_insert();						// вставляем в базу новую заявку, получаем индекс новой заявки

		$addon['inetaction'] = 'локальные административные права пользователя на персональном компьютере для '.$this->input->post('adm_reason');
		array_push($addon['decision'], 'включить пользователя в группу локальных администраторов');
		array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 283));

		$addon['decision'] = implode($addon['decision'], ", ");
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? 
		AND `resources_items`.`rid` = ?", array($orderID, 283));
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('adm_reason'), 'item_id' => $row->id));
			}
		}
		$this->resource_subs_insert($subsdata);
		$templatedata = array_merge($templatedata,$addon);							// слияние массивов дополнительных полей и основных.

		if($this->genmode == "pdf") {
			// конверсия полей для PDF
			// оформляем заявку
			//$templatedata = $this->to_utf8($templatedata);
			$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
		}else{
			// оформляем заявку
			$string = $this->load->view('bids/papers/adm', $templatedata, true);
		}
		return $string;
	}

	public function common_res_paper_get($res, $papers, $sogl=0){
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// получаем данные для заявки.
		//$templatedata['fulladdress'] =  iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
		$templatedata['fulladdress'] = preg_replace("/[^А-Яа-я0-9\.\- ]/ism", '', $templatedata['fulladdress']);
		// получаем рабочие массивы
		$resdata = $this->common_papers_get($res);
		foreach($resdata['resources'] as $key=>$val){
			$templatedata['res_container'] = implode($val, "");
			$templatedata['res_container_sogl'] = implode($resdata['sogl'], "-<br>");		// правильно пошинкованный в длину список ресурсов для листа согласований
			$templatedata['owner_staff'] = $resdata['owners'][$key]['owner_staff'];
			$templatedata['r_owner'] = $resdata['owners'][$key]['otv_dl_name'];
			$sogltype = ($key == "12") 
				? "14" 
				: "" ; // 12 - идентификатор подразделения владельца (ИнГео -- ДГр). Выгоднее при определении подписантов листа согласований.

			if($this->genmode == "pdf") {
				//$templatedata = $this->to_utf8($templatedata);
				$page = ($sogl) 
					? $this->load->view('bids/2pdf/conf', $templatedata, true) 
					: $this->load->view('bids/2pdf/common', $templatedata, true);
				//print_r($templatedata);
				//exit;
				if(!sizeof($papers)){
					array_push($papers, $page);
				}else{
					array_push($papers, "<page>".$page."</page>");
				}
				($sogl) 
					? array_push($papers, "<page>".$this->load->view('bids/2pdf/sogl'.$sogltype, $templatedata, true)."</page>") 
					: "";
				($sogl) 
					? array_push($papers, "<page>".$this->load->view('bids/2pdf/ordermemo', $templatedata, true)."</page>") 
					: "";
			} else {
				$page = ($sogl) 
					? $this->load->view('bids/papers/conf', $templatedata, true) 
					: $this->load->view('bids/papers/common', $templatedata, true);
				array_push($papers,$page);									// оформляем заявку
				$sogltype = ($key == "12")									// повторное определение - вычистить
					? "14" 
					: "" ;  // 12 - идентификатор подразделения владельца (ИнГео -- ДГр). Выгоднее при определении подписантов листа согласований.
				($sogl) 
					? array_push($papers,$this->load->view('bids/papers/sogl'.$sogltype, $templatedata, true)) 
					: "";
				($sogl) 
					? array_push($papers,$this->load->view('bids/papers/ordermemo', $templatedata, true)) 
					: "";
			}
		}
		return $papers;
	}

	public function common_papers_get($res){
		$genmode  = $this->session->userdata('genmode');
		$layers   = array();
		$subsdata = array();
		$subs     = $this->input->post("subs");
		if($subs && strlen($subs)){
			$result = $this->db->query("SELECT 
			`resources_layers`.rid,
			`resources_layers`.spn
			FROM
			`resources_layers`
			WHERE
			`resources_layers`.`id` IN (".$this->input->post("subs").")");
			if ($result->num_rows()){
				foreach($result->result() as $row){
					(!isset($layers[$row->rid])) ? $layers[$row->rid] = array() : "";
					array_push($layers[$row->rid], $row->spn);
				}
			}
		}

		$result = $this->db->query("SELECT DISTINCT
		resources.cat,
		resources.id,
		resources.name,
		resources.bitmask,
		resources.`shortname`,
		resources.owner,
		`staff`.staff AS `owner_staff`,
		CONCAT(LEFT(`users`.`name_i`,1),'.',LEFT(`users`.`name_o`,1),'. ',`users`.name_f) as `otv_dl_name`
		FROM
		`departments`
		INNER JOIN `staff` ON (`departments`.chief = `staff`.id)
		INNER JOIN resources ON (`departments`.id = resources.owner)
		INNER JOIN `users` ON (`departments`.chief = `users`.staff_id)
		AND (`users`.dep_id = resources.owner)
		WHERE
		resources.id IN (".implode($res,",").") 
		AND NOT `users`.fired");
		$process = array();
		$owners = array();
		$layers1 = array();
		$layers2 = array();
		$layers3 = array();
		$layers4 = array();
		$sogl = array();

		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($process[$row->cat])) ? $process[$row->cat] = array() : "";
				(!isset($process[$row->cat][$row->owner])) ? $process[$row->cat][$row->owner] = array() : "";
				(!isset($owners[$row->owner])) ? $owners[$row->owner] = array() : "";
				$owners[$row->owner]['otv_dl_name']=$row->otv_dl_name;
				$owners[$row->owner]['owner_staff']=$row->owner_staff;
				
				if($this->genmode == "pdf") {
					//$layers=explode("\n", $layers[$row->itemid]);
					//print "<br>".$row->itemid;
					//print_r($layers);
					if(isset($layers[$row->id])){
						//exit;
						$layers1 = array_slice($layers[$row->id], 0, 12);
						$layers2 = array_slice($layers[$row->id], 12);
						$layers3 = array_slice($layers[$row->id], 0, 20);
						$layers4 = array_slice($layers[$row->id], 20);
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
					$sogl[$row->owner] = $string2;
				} else {
					//print $row->id."<br>";
					//print_r($layers);
					//exit;
					$additional_info = "";
					if ($row->id == 286) {
						$additional_info = "<br>Приглашение на подключение учётной записи в ЕСИА к организации прошу выслать на почтовый ящик ".$this->input->post("esiaMailAddr");
					}
					$string = '<tr>
					<td  style="font-size:11pt;text-align:center;">&nbsp;</td>
					<td  style="font-size:11pt;text-align:left;"><b>'.$row->name.'</b>'.((isset($layers[$row->id]) && sizeof($layers[$row->id])) 
						? "<br>- ".implode($layers[$row->id], "<br>- ") 
						: "").$additional_info.'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "11111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10011") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10001") ? "x" : "").'</td>
					</tr>
					<tr>
					<td style="font-weight:bold;font-size:10px;" colspan=2>Отметка о предоставлении доступа</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					</tr>';
				}
				$process[$row->cat][$row->owner][$row->id] = $string;
			}
		}

		$resdata		= array();
		$output			= array(
			'owners'	=> $owners,
			'resources'	=> array(),
			'sogl'		=> $sogl
		);
		$orderIDs		= array();

		foreach ($process as $cat=>$owner){
			// вставляем в базу новую заявку, получаем индекс новой заявки
			$orderID = $this->new_order_insert();
			array_push($orderIDs, $orderID);
			foreach($owner as $ownerid => $out){
				(!isset($output['resources'][$ownerid])) ? $output['resources'][$ownerid] = array() : "";
				array_push($output['resources'][$ownerid], implode($out, ""));
				foreach($out as $rid => $label){
					array_push($resdata, array('uid' => $this->input->post('uid'), 'order_id' => $orderID, 'rid' => $rid));
				}
			}
		}
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`uid`= ?
		AND `resources_items`.order_id IN (".implode($orderIDs,",").")", array($this->input->post('uid')));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(isset($layers[$row->rid])){
					array_push($subsdata, "(2, '".implode($layers[$row->rid], "\n")."',".$row->id.")");
				}
			}
		}
		if(sizeof($subsdata)){
			$this->db->query("INSERT INTO 
			resources_pid (
				resources_pid.`pid`,
				resources_pid.`pid_value`,
				resources_pid.`item_id`
			) VALUES ".implode($subsdata, ",\n"));
		}
		return $output;
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
				print $val."<br>";
			}
			return $data;
		}
		if(is_array($data)){
			$data = iconv('windows-1251', 'utf-8', $data);
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

/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodeldev.php */