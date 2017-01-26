<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Startscreenmodel extends CI_Model {
	function __construct(){
		parent::__construct();	// Call the Model constructor
	}

	public function startScreenShow() {
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
		$summary['last_approved'] = $this->makeResTable($result);

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
		$summary['awaiting'] = $this->makeResTable($result);
		return $this->load->view('startscreen', $summary, true);
	}

	private function makeResTable($result) {
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
}

/* End of file adminmodel.php */
/* Location: ./application/models/adminmodel.php */