<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->model('usefulmodel');
	}

	/*
	BETA HelpDESK в составе 1.35 Служит для связи кураторов и сисадминов.
	Пересылает события от админа к куратору по связям, извлекаемым из заявки на ресурс.

	Куратор определяется автоматически. Сообщения видны всем кураторам подчинённым одному руководителю.
	В силу исторической специфики заполнения данных, возможно неверное определение руководителя, поэтому 
	рекомендуется в ненадёжных случаях сохранять данные пользователя и только потом определять поручения.
	Руководитель кураторов проверяется каждый раз при сохранении пользователя и при забирании 
	пользователя в кураторство. 
	По этой причине изменены: $CI->adminmodel->takeuser() на выборку руководителя куратора из таблицы 
	(`admins`.supervisor) вместо (`departments`.service)
	
	Пересылаются следующие данные: информация о заявке и пользователе, рекомендуемое действие и расширенный комментарий

	Кураторы пересылают сисадминам тот же набор данных. Списки доступных действий доступны на редактирование
	плашки в представлении user_details.php: div#modalEvent, div#modalBackEvent
	Реализовано в	$CI->events->makeEvent(); для сисадминов
					$CI->events->makeBackEvent(); для кураторов

	DDL:
		CREATE TABLE `events` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`text` text,
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`active` tinyint(1) DEFAULT '1',
		`closed_by` int(11) DEFAULT '0',
		`owner` int(11) DEFAULT '1',
		`enddate` datetime DEFAULT '1970-01-01 00:00:00',
		`item_id` int(11) DEFAULT '0',
		`recipient` int(11) DEFAULT '1',
		`active_since` date NOT NULL DEFAULT '1970-01-01',
		`uid` int(11) NOT NULL DEFAULT '0',
		`type` int(11) DEFAULT '0',
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=cp1251;

	Кроме того, возможна пересылка отложенных сообщений. Такие сообщения имеют поле даты начала показа.
	Возможна пересылка сообщений не связанных с конкретной заявкой (например напоминание о блокировке пользователя в домене)
	Такие сообщения имеют другой набор определяющих данных, т.н. слабое связывание через идентификатор пользователя и тип сообщения. Характеризуются получателем (events.`recipient`) - администратор (`admins`.id) = 1. 
	Не имеют признака (events.item_id) связывающего их с заявкой и обрабатываются чуть по иному. Реализовано в $CI->admin->switchfired();
	
	*/

	public function index() {
		$output = array();
		$where  = ( (int) $this->session->userdata("rank") === 1)
			? "" 
			: "AND `events`.uid IN (SELECT `users`.id FROM users WHERE `users`.supervisor = ?)";
		$result = $this->db->query("SELECT 
		events.`id`,
		events.`text`,
		events.recipient,
		DATE_FORMAT(events.created, '%d.%m.%Y %H:%i') AS created,
		events.item_id
		FROM
		events
		WHERE
		events.active
		AND events.active_since < NOW()
		".$where."
		ORDER BY FIELD(events.recipient, 1) DESC, events.`id` DESC
		LIMIT 50", array($this->session->userdata("canSee")));

		if($result->num_rows()){
			foreach ($result->result() as $row) {
				$string = '<table class="table table-condensed table-bordered">
				<tr'.(in_array((int) $row->recipient, array(1 , 0)) ? ' class="warning"' : "" ).'>
				'.$row->text.'
				<td class="date">'.$row->created.'</td>
				<td class="control" title="Снять с контроля"><a href="/events/markdone/'.$row->id.'"><i class="icon-remove icon-white"></i></a></td>
				</tr>
				</table>';
				array_push($output, $string);
			}
		}
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('events', array("messages" => implode($output,"\n")), true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		//print $this->session->userdata("canSee");
		$this->load->view('page_container', $act);
	}

	public function makeevent(){
		//$this->output->enable_profiler(TRUE);
		$data = $this->getEventData($this->input->post("itemID"));
		if(is_array($this->input->post("eventAction")) && sizeof($this->input->post("eventAction"))) {
			foreach ($this->input->post("eventAction") as $key=>$val) {
				array_push($data['actions'], "<li>".iconv("utf-8", "windows-1251", $val)."</li>");
			}
		}
		$data['comment']      = iconv("utf-8", "windows-1251", $this->input->post("eventAnnotation"));
		$data['eventmessage'] = $this->load->view("eventtemplate", $data, true);
		$this->insertEventData($data);
	}

	public function makebackevent(){
		//$this->output->enable_profiler(TRUE);
		$data = $this->getEventData($this->input->post("itemID"));
		if(is_array($this->input->post("eventAction")) && sizeof($this->input->post("eventAction"))) {
			foreach ($this->input->post("eventAction") as $key=>$val) {
				array_push($data['actions'], "<li>".iconv("utf-8", "windows-1251", $val)."</li>");
			}
		}
		$data['comment']      = iconv("utf-8", "windows-1251", $this->input->post("eventAnnotation"));
		$data['eventmessage'] = $this->load->view("eventtemplate", $data, true);
		$this->insertEventData($data);
	}

	/* получение данных об источнике пересылаемого события */
	private function getEventData($itemID) {
		$data = array(
			'fio'          => '',
			'actions'      => array(),
			'recipient'    => '',
			'comment'      => '',
			'itemID'       => $itemID,
			'uid'          => 0,
			'shortname'    => '',
			'active_since' => date("Y-m-d")
		);
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o) AS fio,
		users.supervisor,
		users.id AS uid,
		`resources`.shortname
		FROM
		`users`
		RIGHT OUTER JOIN `resources_items` ON (`users`.id = `resources_items`.uid)
		LEFT OUTER JOIN `resources` ON (`resources_items`.rid = `resources`.id)
		WHERE `resources_items`.`id` = ?", $itemID);
		if ($result->num_rows()) {
			$row = $result->row(0);
			$data['fio']          = $row->fio;
			$data['recipient']    = ( (int) $this->session->userdata("rank") ===  0 ) ? 0 : $row->supervisor;
			$data['uid']          = $row->uid;
			$data['shortname']    = $row->shortname;
			$data['active_since'] = implode(array_reverse(explode(".", $this->input->post("startTime"))), "-");
		}
		return $data;
	}
	
	/* вставка данных */
	private function insertEventData($data) {
		$result = $this->db->query("INSERT INTO
		`events` (
			`events`.created,
			`events`.enddate,
			`events`.active,
			`events`.`text`,
			`events`.recipient,
			`events`.item_id,
			`events`.owner,
			`events`.uid,
			`events`.active_since
		) VALUES ( NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 1, ?, ?, ?, ?, ?, ? )", array(
			$data['eventmessage'],
			$data['recipient'],
			$data['itemID'],
			$this->session->userdata('admin_id'),
			$data['uid'],
			$data['active_since']
		));
		$this->load->helper("url");
		redirect("admin/users/".$data["uid"]."/2");
	}
	
	/* снятие сообщения с контроля */

	public function markdone($eid){
		$this->db->query("UPDATE events SET events.active = 0, events.closed_by = ? WHERE events.id = ?", array($this->session->userdata("admin_id"), $eid));
		$this->load->helper("url");
		redirect("events");
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */