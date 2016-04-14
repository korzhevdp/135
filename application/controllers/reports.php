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
		$lists = $this->armmodel->param_lists_get();
		$lists['cios'] = $this->get_cio();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reports/standardreports', $lists, true),
			'footer'  => $this->load->view('page_footer', array(), true),
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	function get_dc( $year = 0, $month = 0 ) {
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

	function get_month($month = 0) {
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

	function get_wd($daynum = 0, $mode="short") {
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
		$pm        = mktime(0, 0, 0, $month-1, 1, $year);
		$nm        = mktime(0, 0, 0, $month+1, 1, $year);
		$plink     = date("Y/n", $pm);
		$nlink     = date("Y/n", $nm);
		$prevmonth = $this->get_month(date("n", $pm)).' '.date("Y", $pm);
		$nextmonth = $this->get_month(date("n", $nm)).' '.date("Y", $nm);
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
		
		$tw = $this->get_dc($year, $month);
		$table = array('<table class="table table-condensed table-bordered table-striped" ><tr><th style="text-align:center;">Число&nbsp;месяца /<br> работник</th>');
		for($a = 1; $a <= $tw; $a++){
			array_push($table, '<th style="width:3%;vertical-align:middle;text-align:center;" dh="'.$year.str_pad($month, 2, "0", STR_PAD_LEFT).str_pad($a, 2, "0", STR_PAD_LEFT).'" class="markAll" title="Установить для всех...">'.$this->get_wd(date("N", mktime(0, 0, 0, $month, $a, $year)))."<br>".$a."</th>");
		}
		array_push($table, "</tr>");

		$result = $this->db->query("SELECT 
		users.id,
		CONCAT(users.name_f, ' ', LEFT(users.name_i, 1), '.', LEFT(users.name_o, 1), '.') AS fio
		FROM
		users
		WHERE
		(users.dep_id = 77) AND
		NOT `users`.`fired`
		Order by fio");
		if($result->num_rows()){
			$rowc = 1;
			foreach($result->result() as $row){
				$line = array('<td style="vertical-align:middle;" id="uid'.$row->id.'">'.$row->fio."</td>");
				for($a = 1; $a <= $tw; $a++){
					$dh = $year.str_pad($month, 2, "0", STR_PAD_LEFT).str_pad($a, 2, "0", STR_PAD_LEFT);
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
					$stat     = (isset($wkstat[$row->id][$dh])) ? $wkstat[$row->id][$dh] : 0;
					$celltext = ($stat) 
						? $statuses[$stat] 
						: "&middot;";
					array_push($line, '<td style="'.implode($styles, " ").'" class="'.implode($classes, " ").'" row="'.$rowc.'" uid="'.$row->id.'" title="'.implode($titles, "\n").'" dh="'.$dh.'">'.$celltext."</td>");
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

		//$this->load->view('page_container', $act);
	}

	public function insert_wkt_data() {
		//$this->output->enable_profiler(TRUE);
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
		$i          = 1;
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
				array_push($output, "<tr><td>".$i++.". ".$row->mail."</td><td>".$row->fio."</td><td>".$row->staff."</td><td>".$row->dn."</td></tr>");
			}
		}
		return implode($output, "\n");
	}

	public function cfstest(){
		$DB1 = $this->load->database('12', TRUE);
		$result = $DB1->db->query("SELECT 
		`files`.fid,
		`files`.folder,
		`files`.new_filename
		FROM
		`files`
		LIMIT 10");
		if($result->num_rows()){
			foreach($result->result() as $row){
				print 1123;
			}
		}
	}

}

/* End of file reports.php */
/* Location: ./application/controllers/reports.php */