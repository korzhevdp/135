<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Licenses extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid')) ? $this->session->set_userdata('uid', 1) : "";
		$this->load->model('usefulmodel');
		$this->load->model('licensemodel');
		if ( (int)$this->session->userdata("admin_id") === 1) {
			//$this->output->enable_profiler(TRUE);
		}
	}



	public function addnew($id = 0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->licensemodel->license_form_get($id),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function add_new_license(){

		$redirect = $this->licensemodel->add_new_license();
		$this->load->helper('url');
		redirect("licenses/addnew/".$redirect);
	}

	public function saveset(){
		//$this->output->enable_profiler(TRUE);
		$redirect = $this->licensemodel->saveset();
		$this->load->helper('url');
		redirect("licenses/addnew/".$redirect);
	}

	public function addpotoset(){
		$this->licensemodel->addpotoset();
		$this->load->helper('url');
		redirect("licenses/addnew/".$this->input->post("lid"));
	}

	public function addtype(){
		$result = $this->db->query("SELECT 
		`inv_po_types`.id
		FROM
		`inv_po_types`
		WHERE
		TRIM(`inv_po_types`.name) = TRIM(?)", array($this->input->post("typename")));
		if (!$result->num_rows()) {
			$result = $this->db->query("INSERT INTO `inv_po_types`( `inv_po_types`.name ) VALUES ( TRIM(?) )", array($this->input->post("typename")));
			return true;
		}
		print "��� ������������ ����������� ��� ����������";
	}

	###############################
	/*
		//������� ��������� ���������� ����� � �������� ����� � ������������ ����������.
		SELECT DISTINCT 
		ak_licenses.hostname,
		ak_licenses.product_name,
		ak_licenses.product_key,
		inv_po_licenses.number,
		ak_licenses.product_serial,
		ak_licenses.verify_pk,
		IF(inv_po_licenses_items.master, '����', '����') AS l_mode
		FROM
		ak_licenses
		LEFT OUTER JOIN `hosts` ON (ak_licenses.hostname = `hosts`.hostname)
		LEFT OUTER JOIN inv_po_licenses_items ON (ak_licenses.product_key = inv_po_licenses_items.value)
		LEFT OUTER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		LEFT OUTER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		LEFT OUTER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		WHERE
		(`hosts`.server = 1) AND 
		(ak_licenses.active = 1) AND 
		(NOT (ak_licenses.manual))
		ORDER BY
		inv_po_licenses.number,
		ak_licenses.product_name,
		`hosts`.hostname
	*/
	#############################

	##################################################################### AJAX

	public function make_reject($lid=0){
		//$this->output->enable_profiler(TRUE);
		$this->load->helper('url');
		$this->db->query("UPDATE 
		ak_licenses 
		SET ak_licenses.active = 0,
		ak_licenses.activation_memo = ?
		WHERE `ak_licenses`.id = ?", array("��������������: ".date('d.m.Y H:i:s').', ������������: '.$this->session->userdata('user_name'), $lid));
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' ������������� ������ � �������� '. $lid);
		redirect('/licenses/user/'.$this->input->post('userid').'/'.$this->input->post('dep_id'));
	}

	public function make_recall($lid=0){
		//$this->output->enable_profiler(TRUE);
		$this->load->helper('url');
		$this->db->query("UPDATE 
		ak_licenses 
		SET
		ak_licenses.active = 1,
		ak_licenses.activation_memo = ?
		WHERE `ak_licenses`.id = ?", array("������������: ".date('d.m.Y H:i:s').', ������������: '.$this->session->userdata('user_name'), $lid));
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' ����������� ������ � �������� '. $lid);
		redirect('/licenses/user/'.$this->input->post('userid').'/'.$this->input->post('dep_id'));
	}

	public function get_related_licenses($pk=""){
		print $this->licensemodel->get_related_licenses($pk);
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' �������� ������ �������� �� ������������ ����� (������� �� ����)');
	}

	public function get_all_licenses(){
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' �������� ������ ���� �������� ��� ���������� ������������');
		print $this->licensemodel->get_all_licenses();
	}

	public function takeitem($lid=0){
		$this->load->helper('url');
		$this->db->query("UPDATE
		ak_licenses
		SET
		ak_licenses.item_id = ?
		WHERE
		`ak_licenses`.id = ?", array(
			$this->input->post('itemid'),
			$this->input->post('akl')
		));
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' ���� �� ���� �������� ��� ���������� ������ �������� #'.$this->input->post('akl'));
		redirect('/licenses/user/'.$this->input->post('userid').'/'.$this->input->post('dep_id'));
	}

	public function orderitem($lid=0){
		$itemid = $this->licensemodel->orderitem();
		$this->load->helper('url');
		$this->usefulmodel->insert_audit("�������� #".$this->session->userdata('user_name') .' �������� �������� '.$itemid.' ������������ #'.$this->input->post('akl'));
		redirect('/licenses/user/'.$this->input->post('userid').'/'.$this->input->post('dep_id'));
	}

	public function bideitem($lid=0){
		print $this->licensemodel->bideitem();
		$this->load->helper('url');
		redirect('/licenses/user/'.$this->input->post('userid').'/'.$this->input->post('dep_id'));
	}

	public function get_bide($lid=0){
		print $this->licensemodel->get_license_to_bide($lid);
	}

	public function removeitem($lid=0, $redirect){
		$this->licensemodel->removeitem($lid, $redirect);
	}



	# ��������� �������� ��������
	# !- ������ �������� ������������ �������� ��,
	# ��������� � �������� -!
	public function save_license($lid=0){
		$this->licensemodel->save_license($lid);
	}

	# ���������� ���� "�������� ���������" �.�. 
	# ����������� ������� � (�����������) ������
	public function verify_license($lid=0){
		$this->licensemodel->verify_license($lid);
	}

	# C������ ������ � ������ �� �������.
	# ��������� ����� downgrade � ��.
	public function makemaster($item, $redirect){
		$this->licensemodel->makemaster($item, $redirect);
	}

	# �������� ���������� ��
	public function add_licensiar(){
		$this->licensemodel->add_licensiar();
	}

	# �������� ���������� (���������) ��
	public function add_reseller(){
		$this->licensemodel->add_reseller();
	}

	# �������� ����� �� � ��������
	public function add_set_to_license($lid=0){
		$this->licensemodel->add_set_to_license($lid);
	}
	
	# ��������� ������ ����� �� ��� ���������� � �����
	public function get_typelist(){
		print $this->licensemodel->get_typelist();
	}

	private function getTypeDowngradedFrom($setID) {
		$result = $this->db->query("SELECT
		`inv_po_types`.name
		FROM
		inv_po_licenses
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses.id = inv_po_licenses_sets.license_id)
		INNER JOIN inv_po_licenses_items ON (inv_po_licenses_sets.id = inv_po_licenses_items.set_id)
		INNER JOIN `inv_po_types` ON (inv_po_licenses_items.type_id = `inv_po_types`.id)
		WHERE
		`inv_po_licenses_items`.`master`
		AND `inv_po_licenses_items`.`type` IN ('MAK', 'VLK')
		AND `inv_po_licenses_items`.`act`
		AND `inv_po_licenses_sets`.id = ?
		LIMIT 1", array($setID));
		if ($result->num_rows()) {
			$row = $result->row(0);
			return $row->name;
		}
		return "";
	}

	public function related_lic_get(){
		$table = array(
			'direct' => array(),
			'down'   => array(),
		);
		$df           = "";
		$direct_count = 0;
		$total_count  = 0;
		$master       = 1;

		$set_usage = array();
		$result = $this->db->query("SELECT 
		COUNT(ak_licenses.item_id) AS usage_sum,
		inv_po_licenses_items.set_id
		FROM
		inv_po_licenses_items
		INNER JOIN ak_licenses ON (inv_po_licenses_items.id = ak_licenses.item_id)
		WHERE
		(LENGTH(ak_licenses.item_id))
		AND NOT(ak_licenses.manual)
		AND ak_licenses.active
		GROUP BY
		inv_po_licenses_items.set_id");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$set_usage[$row->set_id] = $row->usage_sum;
			}
		}

		$result = $this->db->query("SELECT DISTINCT
		inv_po_licenses.number,
		inv_po_licenses.id AS lid,
		inv_po_licenses_sets.id AS sid,
		inv_po_licenses_sets.max AS `maxcount`,
		inv_po_licensiars.name AS licensiar,
		inv_po_licenses_items.master,
		RIGHT(inv_po_licenses_items.value, 5) AS checkval,
		inv_po_licenses_items.`type`,
		inv_po_resellers.name AS reseller
		FROM
		inv_po_licenses
		INNER JOIN inv_po_licensiars ON (inv_po_licenses.licensiar_id = inv_po_licensiars.id)
		INNER JOIN inv_po_resellers ON (inv_po_licenses.reseller_id = inv_po_resellers.id)
		INNER JOIN inv_po_licenses_sets ON (inv_po_licenses.id = inv_po_licenses_sets.license_id)
		INNER JOIN inv_po_licenses_items ON (inv_po_licenses_sets.id = inv_po_licenses_items.set_id)
		WHERE
		inv_po_licenses_items.type_id = ?
		AND inv_po_licenses_items.type IN ('MAK', 'VLK')
		AND `inv_po_licenses_items`.`act`
		AND NOT inv_po_licenses_sets.deleted
		ORDER BY
		inv_po_licenses.number DESC", array($this->input->post('id')));
		if($result->num_rows()){

			foreach($result->result() as $row){
				$color = "success";
				$title = "������ ��������";
				if ($row->master) {
					$direct_count += $row->maxcount;
				}
				if (!$row->master) {
					$df = $this->getTypeDowngradedFrom($row->sid);
				}
				$total_count += $row->maxcount;
				$color = ($row->maxcount - ((isset($set_usage[$row->sid])) ? $set_usage[$row->sid] : 0 ) > 0) ? $color : 'error';
				$title = ($row->maxcount - ((isset($set_usage[$row->sid])) ? $set_usage[$row->sid] : 0 ) > 0) ? $title : '�������� ���������';
				$string = '<tr class="'.$color.'" title="'.$title.'">
					<td>'.$row->licensiar.'</td>
					<td><a href="/licenses/statistics/'.$row->lid.'" target="_blank">'.$row->number.'</a></td>
					<td>'.$row->reseller.'</td>
					<td title="'.$df.'">'.$row->maxcount.'</td>
					<td class="useCheck" title="set #'.$row->sid.'" ref="'.$row->checkval.'">'.($row->maxcount - ((isset($set_usage[$row->sid])) ? $set_usage[$row->sid] : 0 )).'</td>
				</tr>';
				if ($row->master) {
					array_push($table['direct'], $string);
				}
				if (!$row->master) {
					array_push($table['down'],   $string);
				}
			}

		}
		$data = array(
			"TDown"       => $total_count - $direct_count,
			"TDir"        => $direct_count,
			"tableDirect" => implode( $table['direct'], "\n" ),
			"tableDown"   => implode( $table['down'], "\n" )
		);
		$this->load->view("license/usagestatchunk", $data);
	}

	public function related_pk_get(){
		$output = array();
		$result = $this->db->query("SELECT DISTINCT
		CONCAT_WS(' ', users.name_f, CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1),'.')) AS fio,
		`departments`.alias,
		DATE_FORMAT(ak_licenses.scandate, '%d.%m.%Y') AS scandate,
		users.id
		FROM
		`hosts`
		INNER JOIN ak_licenses ON (`hosts`.hostname = ak_licenses.hostname)
		INNER JOIN users ON (`hosts`.uid = users.id)
		LEFT OUTER JOIN `departments` ON (users.dep_id = `departments`.id)
		WHERE
		(ak_licenses.product_key LIKE ?) AND 
		(ak_licenses.active)
		-- AND (NOT (ak_licenses.manual))
		GROUP BY ak_licenses.id
		ORDER BY fio", array("%".$this->input->post("pk")));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<tr><td title="'.$row->alias.'"><a href="/admin/users/'.$row->id.'/4" target="_blank">'.$row->fio.'</a></td><td>'.$row->scandate.'</td></tr>';
				array_push($output, $string);
			}
		}
		print '<table class="table table-condensed table-bordered table-striped">
		<tr>
			<td>���</td>
			<td>����</td>
		</tr>
		'.implode($output, "\n")."</table>";
	}

	public function getpolist(){
		$this->licensemodel->getpolist();
	}

	public function convertsofttolicense(){
		$this->output->enable_profiler(TRUE);
		//$this->licensemodel->getpolist();
	}
	
	public function active(){
		$this->db->query("UPDATE `inv_po_licenses`
		SET
		`inv_po_licenses`.`active` = IF(`inv_po_licenses`.`active` = 1, 0, 1)
		WHERE
		`inv_po_licenses`.`id` = ?", array($this->input->post("lid")));
		$result = $this->db->query("SELECT 
		inv_po_licenses.active
		FROM
		inv_po_licenses
		WHERE `inv_po_licenses`.`id` = ?", array($this->input->post("lid")));
		if($result->num_rows()){
			$row = $result->row();
			print $row->active;
		}
	}
}

/* End of file licenses.php */
/* Location: ./application/controllers/licenses.php */