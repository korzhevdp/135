<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid'))    ? $this->session->set_userdata('uid', 1)     : "";
		$this->load->model('usefulmodel');
		$this->load->model('armmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function index($user_id=0, $page=1) {
		$lists           = $this->armmodel->param_lists_get();
		$lists['cios']   = $this->get_cio();
		$lists['pcless'] = $this->get_pcless();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reports/standardreports', $lists, true),
			'footer'  => $this->load->view('page_footer', array(), true),
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	private function get_pcless() {
		$output = array();
		$result = $this->db->query("SELECT
		users.id,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		users.host,
		COUNT(`hosts`.id) AS pc_count,
		`departments`.dn
		FROM
		`hosts`
		RIGHT OUTER JOIN users ON (`hosts`.uid = users.id)
		LEFT OUTER JOIN `departments` ON (users.dep_id = `departments`.id)
		WHERE
		NOT (users.fired)
		AND NOT users.dep_id = 10
		GROUP BY
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o),
		`departments`.dn
		HAVING
		(pc_count = 0)
		ORDER BY `departments`.`dn`, fio");
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$string = '<tr><td><a href="http://192.168.1.35/admin/users/'.$row->id.'" target="_blank">'.$row->fio.'</a></td><td>'.$row->host.'</td><td>'.$row->dn.'</td></tr>';
				array_push($output, $string);
			}
		}
		return implode($output, "\n");
	}

	private function get_dc( $year = 0, $month = 0 ) {
		$year = ($year) ? $year : date("Y");
		$month = ($month) ? $month : date("n");
		$day_count = array(
			1  => '31',
			2  => '28',
			3  => '31',
			4  => '30',
			5  => '31',
			6  => '30',
			7  => '31',
			8  => '31',
			9  => '30',
			10 => '31',
			11 => '30',
			12 => '31'
		);
		if($month == 2 && $year % 400 == 0 || ($year % 4 == 0 && $year % 100 != 0)) {
			return '29';
		}else{
			return $day_count[$month];
		}
	}

	private function get_month($month = 0) {
		$month = ($month) ? $month : date("n");
		$months = array(
			1  => 'Январь',
			2  => 'Февраль',
			3  => 'Март',
			4  => 'Апрель',
			5  => 'Май',
			6  => 'Июнь',
			7  => 'Июль',
			8  => 'Август',
			9  => 'Сентябрь',
			10 => 'Октябрь',
			11 => 'Ноябрь',
			12 => 'Декабрь'
		);
		return $months[$month];
	}

	private function get_wd($daynum = 0, $mode="short") {
		$daynum = ($daynum) ? $daynum : date("N");
		$days = array(
			1  => array('day' => 'Понедельник', 'short' => 'пн'),
			2  => array('day' => 'Вторник'    , 'short' => 'вт'),
			3  => array('day' => 'Среда'      , 'short' => 'ср'),
			4  => array('day' => 'Четверг'    , 'short' => 'чт'),
			5  => array('day' => 'Пятница'    , 'short' => 'пт'),
			6  => array('day' => 'Суббота'    , 'short' => 'сб'),
			7  => array('day' => 'Воскресенье', 'short' => 'вс')
		);
		return $days[$daynum][$mode];
	}

	public function timetable($year = 0, $month = 0, $mode="html") {
		$year      = ($year)  ? $year  : date("Y");
		$month     = ($month) ? $month : date("n");
		$prevMth   = mktime(0, 0, 0, $month-1, 1, $year);
		$nextMth   = mktime(0, 0, 0, $month+1, 1, $year);
		$plink     = date("Y/n", $prevMth);
		$nlink     = date("Y/n", $nextMth);
		$prevmonth = $this->get_month(date("n", $prevMth)).' '.date("Y", $prevMth);
		$nextmonth = $this->get_month(date("n", $nextMth)).' '.date("Y", $nextMth);
		$prevbut   = '<a href="/reports/timetable/'.$plink.'" class="btn btn-info btn-small pull-left" style="margin-bottom:15px;"><< '.$prevmonth.'</a>';
		$nextbut   = '<a href="/reports/timetable/'.$nlink.'" class="btn btn-info btn-small pull-right" style="margin-bottom:15px;"> >> '.$nextmonth.'</a>';
		$sqllow    = $year.str_pad($month, 2, "0", STR_PAD_LEFT)."00";
		$sqlhigh   = $year.str_pad($month, 2, "0", STR_PAD_LEFT)."45";
		$wkstat    = array();
		$statuses  = array(
			1 => 'Я',
			4 => 'К',
			2 => 'В',
			3 => 'П',
			5 => 'О',
			6 => 'Б'
		);
		$result    = $this->db->query("SELECT 
			wkt.uid,
			wkt.`date`,
			wkt.dayhash,
			wkt.`status`
		FROM
		wkt
		WHERE wkt.dayhash BETWEEN ? AND ?", array($sqllow, $sqlhigh));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(!isset($wkstat[$row->uid])){
					$wkstat[$row->uid] = array();
				}
				$wkstat[$row->uid][$row->dayhash] = $row->status;
			}
		}
		//print_r($wkstat);
		
		$tableWidth = $this->get_dc($year, $month);
		$table = array('<table class="table table-condensed table-bordered table-striped" ><tr><th style="text-align:center;">Число&nbsp;месяца /<br> работник</th>');
		for($a = 1; $a <= $tableWidth; $a++){
			array_push($table, '<th style="width:3%;vertical-align:middle;text-align:center;" dh="'.$year.str_pad($month, 2, "0", STR_PAD_LEFT).str_pad($a, 2, "0", STR_PAD_LEFT).'" class="markAll" title="Установить для всех...">'.$this->get_wd(date("N", mktime(0, 0, 0, $month, $a, $year)))."<br>".$a."</th>");
		}
		array_push($table, "</tr>");

		$result = $this->db->query("SELECT
		users.id,
		CONCAT(users.name_f, ' ', LEFT(users.name_i, 1), '.', LEFT(users.name_o, 1), '.') AS fio
		FROM
		users
		WHERE
		(users.dep_id = 77) 
		AND NOT `users`.`fired`
		Order by fio");
		if($result->num_rows()){
			$rowc = 1;
			foreach($result->result() as $row){
				$line = array('<td style="vertical-align:middle;" id="uid'.$row->id.'">'.$row->fio."</td>");
				for($a = 1; $a <= $tableWidth; $a++){
					$dayHRead = $year.str_pad($month, 2, "0", STR_PAD_LEFT).str_pad($a, 2, "0", STR_PAD_LEFT);
					$celltext = "--";
					$classes = array("dayCell");
					$styles  = array();
					$titles  = array();
					$cday = date("N", mktime(0, 0, 0, $month, $a, $year));
					if(in_array($cday, array('6','7'))){
						array_push($classes, "holiday");
						array_push($styles, "color: #ffff33;font-weight: bolder;cursor:pointer;background-color: #ff6699;");
						array_push($titles,  $this->get_wd($cday)." - Выходной день");
						$celltext = "В";
					}else{
						array_push($classes, "workday");
						array_push($titles,  $this->get_wd($cday)." - Рабочий день");
					}
					$stat     = (isset($wkstat[$row->id][$dayHRead])) ? $wkstat[$row->id][$dayHRead] : 0;
					$celltext = ($stat) 
						? $statuses[$stat] 
						: "&middot;";
					array_push($line, '<td style="'.implode($styles, " ").'" class="'.implode($classes, " ").'" row="'.$rowc.'" uid="'.$row->id.'" title="'.implode($titles, "\n").'" dh="'.$dayHRead.'">'.$celltext."</td>");
				}
				$string = "<tr>".implode($line, "\n")."</tr>";
				array_push($table, $string);
				$rowc++;
			}
		}
		$data = array(
			'table'      => implode($table, "\n")."</table>",
			'nav'        => $prevbut.$this->get_month($month)." ".$year.$nextbut,
			'linktoword' => "/reports/timetable/".$year."/".$month."/word",
			'curmonth'   => $this->get_month($month)." ".$year." г."
		);
		$this->usefulmodel->no_cache();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => ($mode == "html") ? $this->load->view('reports/timetable', $data, true) : $this->load->view('reports/timetableword', $data, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		switch($mode){
			case "word":
				$filename = 'Табель учёта рабочего времени.doc';
				$this->load->helper('download');
				force_download($filename, iconv( "windows-1251", "utf8", $act['content']));
			break;
			case "html":
				$this->load->view('page_container', $act);
			break;
			
		}
	}

	private function getEsiaStates() {
		$output = array();
		$result = $this->db->query("SELECT
		resources_items.id,
		resources_items.uid,
		resources_items.ok,
		resources_items.ingroup,
		DATE_FORMAT(resources_items.initdate, '%d.%m.%Y')        AS initdate,
		DATE_FORMAT(resources_items.initdate, '%Y%m%d')          AS sortmode,
		DATE_FORMAT(resources_items.ingroupdate, '%d.%m.%Y')     AS ingroupdate,
		DATE_FORMAT(resources_items.okdate, '%d.%m.%Y')          AS okdate,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		users.fired,
		resources_pid.pid_value,
		`departments`.alias,
		`departments`.dn
		FROM
		users
		RIGHT OUTER JOIN resources_items ON (users.id = resources_items.uid)
		LEFT OUTER JOIN resources_pid ON (resources_items.id = resources_pid.item_id)
		LEFT OUTER JOIN `departments` ON (users.dep_id = `departments`.id)
		WHERE
		(resources_items.rid = 286) 
		AND (NOT (resources_items.del))
		AND (NOT (resources_items.`exp`))
		ORDER BY
		fired DESC, sortmode DESC, alias, fio");
		if ($result->num_rows()) {
			$input = $this->getESIAInputArray($result);
			foreach ($input as $val) {
				array_push($output, implode($val, "\n"));
			}
		}
		return $this->load->view("reports/esiareport", array("tablecontent" => implode($output, "\n")), true);
	}

	private function getESIAInputArray($result) {
		$input  = array();
		foreach ($result->result() as $row) {
			if (!isset($input[$row->initdate])) {
				$input[$row->initdate] = array('<tr><td colspan=6><h4 class="pull-right">'.$row->initdate.'</h4></td></tr>');
			}
			$style  = (!$row->ok)     ? ' class = "warning"' : '';
			$style  = ($row->ingroup) ? ' class = "success"' : $style;
			$style  = ($row->fired)   ? ' class = "error"'   : $style;

			if ((int)$this->session->userdata('rank') === 1) {
				$nest0 = ($row->ok) ? '<i class="icon-ok" title="Приглашение отправлено '.$row->okdate.'"></i>' : '<i class="icon-remove"></i>';
				$nest1 = ($row->ingroup)
					? '<i class="icon-ok" title="Включено в группу '.$row->ingroupdate.'"></i>'
					: '<button type="button" class="btn btn-mini btn-danger inGroupSw" ref="'.$row->id.'" title="Нажать только после включения в группу ЕСИА"><i class="icon-question-sign icon-white"></i></button>';
				$nest2 = '<button type="button" class="btn btn-mini btn-warning ESIAOff" ref="'.$row->id.'" title="Снять с учёта"><i class="icon-ban-circle icon-white"></i></button>';
			}

			if ((int)$this->session->userdata('rank') !== 1) {
				$nest0 = ($row->ok)
					? '<i class="icon-ok" title="Приглашение отправлено '.$row->okdate.'"></i>'
					: '<i class="icon-remove" title="Приглашение не было отправлено"></i>';
				$nest1 = ($row->ingroup)
					? '<i class="icon-ok" title="Включено в группу '.$row->ingroupdate.'"></i>'
					: '<i class="icon-question-sign" title="Ожидает включения в группу (при необходимости)"></i>';
				$nest2 = '<i class="icon-minus"></i>';
			}

			$string = '<tr'.$style.'>
				<td><a href="/admin/users/'.$row->uid.'/2" target="_blank">'.$row->fio.'</a></td>
				<td title="'.$row->dn.'">'.$row->alias.'</td>
				<td>'.nl2br($row->pid_value).'</td>
				<td>'.$nest0.'</td>
				<td>'.$nest1.'</td>
				<td>'.$nest2.'</td>
			</tr>';

			array_push($input[$row->initdate], $string);
		}
		return $input;
	}

	public function esia() {
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->getEsiaStates(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->load->view('page_container', $act);
	}

	public function insert_wkt_data() {
		if($this->input->post('dh') && is_array($this->input->post('dh'))){
			$this->db->query("DELETE 
			FROM `wkt` 
			WHERE wkt.uid = ? 
			AND wkt.dayhash IN (".implode($this->input->post('dh'), ",").")", array($this->input->post('uid')));
			$data   = array();
			foreach($this->input->post('dh') as $val){
				$string = "(".$this->input->post('uid').", NOW(), ".$val.", ".$this->input->post('stat').")";
				array_push($data, $string);
			}
			$result = $this->db->query("INSERT INTO
			`wkt`(
				`wkt`.`uid`,
				`wkt`.`date`,
				`wkt`.`dayhash`,
				`wkt`.`status`
			) VALUES ".implode($data, ",\n"));
			return true;
		}
		if($this->input->post('uids') && is_array($this->input->post('uids'))){
			$this->db->query("DELETE 
			FROM `wkt` 
			WHERE wkt.dayhash = ? 
			AND wkt.uid IN (".implode($this->input->post('uids'), ",").")", array($this->input->post('dhc')));
			$data   = array();
			foreach($this->input->post('uids') as $val){
				$string = "(".$val.", NOW(), ".$this->input->post('dhc').", ".$this->input->post('stat').")";
				array_push($data, $string);
			}
			$result = $this->db->query("INSERT INTO
			`wkt`(
				`wkt`.`uid`,
				`wkt`.`date`,
				`wkt`.`dayhash`,
				`wkt`.`status`
			) VALUES ".implode($data, ",\n"));
			return true;
		}
	}
	// AJAX Section
	public function selectpc() {
		print $this->armmodel->param_table_get();
	}

	public function selectparams() {
		print $this->armmodel->subparam_table_get();
	}

	/* get CIO List*/

	private function get_cio() {
		//$this->output->enable_profiler(TRUE);
		$staff_list = array( 8, 9, 10, 4, 11, 21, 22, 23, 27, 32, 45, 28, 29, 40 );
		$output     = array();
		$iterator   = 1;
		$result     = $this->db->query("SELECT DISTINCT
		LOWER(CONCAT(resources_pid.pid_value, '@arhcity.ru')) AS mail,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		CONCAT(IF(users.io, 'и.о. ', ''), staff.staff) AS staff,
		departments.dn
		FROM
		resources_pid
		RIGHT OUTER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
		RIGHT OUTER JOIN users ON (resources_items.uid = users.id)
		LEFT OUTER JOIN staff ON (users.staff_id = staff.id)
		LEFT OUTER JOIN departments ON (users.dep_id = departments.id)
		WHERE
		(users.staff_id IN (".implode($staff_list, ", ")."))
		AND (resources_pid.pid = 1)
		AND (resources_items.rid = 100)
		AND (NOT (users.fired))
		AND (NOT (resources_items.`exp`))
		AND (NOT (resources_items.del))
		ORDER BY mail");
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				array_push($output, "<tr><td>".$iterator++.". ".$row->mail."</td><td>".$row->fio."</td><td>".$row->staff."</td><td>".$row->dn."</td></tr>");
			}
		}
		return implode($output, "\n");
	}
}

/* End of file reports.php */
/* Location: ./application/controllers/reports.php */