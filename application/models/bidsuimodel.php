<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsuimodel extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model("usefulmodel");
	}

	private function getUserMailFromDB($userid) {
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
			$row   = $result->row();
			$email = $row->email;
		}
		return $email;
	}

	private function blankDataResList() {
		// �������� ������ ��������
		$result = $this->db->query("SELECT
		resources.id,
		resources.shortname,
		resources.cat,
		resources.grp,
		COUNT(resources_layers.id) AS layers
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
		$output = array();
		if ($result->num_rows()){
			foreach ($result->result() as $row) {
				if (!isset($output[$row->grp])) {
					$output[$row->grp] = array();
				}
				$class  = ($row->cat > 1) ? "btn-warning" : "btn-info";
				$conf   = ($row->cat > 1) ? "1" : "0";
				$string = '<li 
					class="reslist btn '.$class.' btn-block"
					id="r_'.$row->id.'"
					grp="'.$row->grp.'"
					subs="'.$row->layers.'"
					conf="'.$conf.'"
					style="margin: 2px 0px;"
					title="������ ������� ������ � ������ ���������">'.$row->shortname.'</li>';
				array_push($output[$row->grp], $string);
			}
		}
		return $output;
	}

	private function blankDataDeptList() {
		$result = $this->db->query("SELECT
		departments.id,
		departments.dn AS `value`
		FROM
		departments
		ORDER BY 
		departments.dn");
		return $this->usefulmodel->returnList($result);
	}

	private function blankDataStaffList() {
		$result = $this->db->query("SELECT
		`staff`.`id`,
		`staff`.`staff` AS `value`
		FROM
		`staff`
		ORDER BY `staff`.`staff`");
		return $this->usefulmodel->returnList($result);
	}

	private function blankDataLocationsList() {
		$result = $this->db->query("SELECT 
		locations.id,
		locations.address AS value
		FROM locations
		WHERE `locations`.parent = 0 
		AND `locations`.id <> 0
		ORDER BY `locations`.`address`");
		return $this->usefulmodel->returnList($result);
	}

	public function getResourceAccordion($rlist, $group_id) {
		$addition = "";
		$state    = "";
		$hide     = "";
		if ($group_id == 11) {
			$hide     = ' hide';
			$addition = ' id="domain_data"';
			$state    = ' in';
		}
		$resgroups    = array(
			0  => '������',
			1  => '1C: ����������� ��������',
			2  => '������� ��� ������ MS SQL',
			3  => '����������� ��������� �������',
			4  => '���������-�������� �������',
			5  => '���������� �� �������� ����� ����� � ��������������',
			6  => '������������������ �������������� ������� (��� / ���)',
			7  => '����������� �������������� ���������',
			8  => '����������� �����������',
			9  => '��������� ���������',
			11 => '������ �� ����������� � ���� ��������������',
			10 => '�������� � ����������� �����',
			12 => '������������ ���� Wi-Fi',
			13 => '���� / ��������������� ������'
		);
		if ( !isset($rlist[$group_id]) ) {
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
	
	public function locs_get() {
		$input  = array();
		$output = array();
		$result = $this->db->query("SELECT
		locations.id,
		locations.address,
		`locations`.parent
		FROM locations
		WHERE
		`locations`.parent <> 0
		AND `locations`.id <> 0
		ORDER BY `locations`.`address`");
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				if ( !isset($input[$row->parent]) ) {
					$input[$row->parent] = array();
				}
				$string = '<option value="'.$row->id.'">'.$row->address.'</option>';
				array_push($input[$row->parent], $string);
			}
		}
		foreach ($input as $key=>$val) {
			array_push($output, "\t\t".$key.": ['".implode($val, "', '")."']");
		}
		return "{\n".implode($output, ",\n")."\n}";
	}
	
	public function blank_data_get() {
		/*
		* ������� ������ �� ��������� (�����������)
		* ������������ ������ ������ ������������
		*/
		// ��������� ������ ������ ������
		$return = array(
			'name_f'		=> '',
			'name_i'		=> '',
			'name_o'		=> '',
			'phone'			=> '',
			'login'			=> '',
			'location'		=> array('','')
		);
		
		// �������� ������ �������������
		$return['dept']  = $this->blankDataDeptList();
		// �������� ������ ����������
		$return['staff'] = $this->blankDataStaffList();

		// �������� ������ ���������
		$return['location'][0] = $this->blankDataLocationsList();

		$return['rlist'] = $this->blankDataResList();
		
		// ������ �������� ����, �������� :)
		$return['ordersProcessed']='<h3 class="muted">�� ������ ������������</h3>';

		return $return;
	}

	public function user_data_get2($userid) {
		$email = $this->getUserMailFromDB($userid);
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
			return "udt = {".
				"\n\tname_f : '".$row->name_f."',".
				"\n\tname_i : '".$row->name_i."',".
				"\n\tname_o : '".$row->name_o."',".
				"\n\tlogin  : '".$row->login."',".
				"\n\tdept   :  ".$row->dep_id.",".
				"\n\tstaff  :  ".$row->staff_id.",".
				"\n\tbldg   :  ".$row->parent.",".
				"\n\toffice :  ".$row->office_id.",".
				"\n\tphone  : '".$row->phone."',".
				"\n\temail  : '".$email."'".
			"\n}";
		}
	}

	public function user_res_get($userid) {
		// ��������� �������� ���������������� ��������
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
		`resources_items`.`uid` = ? 
		AND NOT `resources_items`.`del`",array($userid));
		$output = array();
		if ($result->num_rows()){
			foreach($result->result() as $row){
				// ������� ���������� ������ ���
				$status = ($row->ok) 
					// ������� ���������� ������ ���������
					? ($row->apply) 
						? '���������<br><small>������ ��������� � ��������</small>' 
						: '���������<br><small>���������� � ��������: '.$row->sman.', ���.: '.$row->phone.'</small>' 
					: '�� ������������';
				// ������� ������ ������
				$status = ($row->exp) ? "��������" : $status;
				$string = '<tr>
					<td>'.$row->shortname.'</td>
					<td>'.$row->docdate.'</td>
					<td>'.$row->docnum.'</td>
					<td>'.$status.'</td>
					<td style="text-align:center;vertical-align:middle;"><input type="checkbox" class="paperChecker" ref="'.$row->id.'" title="�������� ����� ���� ������"></td>
				</tr>';
				//�������� ������ � �������
				array_push($output, $string);
			}
		}else{
			//���� �� ������ ������� ���
			array_push($output,'<tr><td colspan=5><h3>� ����� ������������ ��� �������� ������</h3></td></tr>');
		}
		return implode($output, "\n");
	}

}

/* End of file bidsmodel2.php */
/* Location: ./application/models/bidsmodel2.php */