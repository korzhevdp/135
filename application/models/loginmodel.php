<?php
class Loginmodel extends CI_Model{

	function __construct(){
		parent::__construct();		// Call the Model constructor
		$this->load->helper('url');
	}

	private $errors = array();

	private function setUserSessionData($row) {
		$result = $this->db->query("SELECT
		users.id,
		CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
		departments.alias,
		IF(users.id IN (SELECT DISTINCT admins.supervisor FROM `admins`), 1, 0) AS supervisor
		FROM
		users
		INNER JOIN departments ON (users.dep_id = departments.id)
		WHERE
		(users.id = ?)", array($row->base_id));
		if ($result->num_rows()){
			$row2 = $result->row();
		$this->session->set_userdata('admin_id',  (int) $row->id);					// id ��������� ��� �������
		$this->session->set_userdata('canSee',    $row->supervisor);				// ������������
		$this->session->set_userdata('base_id',   (int) $row->base_id);				// ������� ����� � ���� ������
		$this->session->set_userdata('rank',      $row->rank);						// ���� ���������
		$this->session->set_userdata('user_name', $this->input->post('name'));		// ��������� ��� ��������� (��� ������)
			$this->session->set_userdata('selfname',  $row2->fio);					// ������������ ��� ��������� - ��� ���������
			$this->session->set_userdata('is_sup',    $row2->supervisor);			// ������ �������� - ���������� ��
			$this->usefulmodel->insert_audit("��������������� ���� ��������� #".$this->session->userdata('user_name'));
			return true;
		}
		return false;
	}

	private function setUserNotFound() {
		$message = '<div class="alert alert-error span5" style="clear:both;margin:40px;">
			<a class="close" data-dismiss="alert" href="#">x</a>
			<h4 class="alert-heading">������!</h4>
			������������ � ���������� ������ � ������� �� ������. ��������� ������������ ����� ����� ������������ � ������. �������� ��������, ��� ��������� � �������� ����� �����������
		</div>';
		array_push($this->errors, $message);
		$this->usefulmodel->insert_audit("���������������� ��������� ������� ����� ��������� #".$this->input->post('name'));
	}

	public function index($mode='auth') {
		$act = array(
			'reg'       => (( $mode === 'auth' ) ? 0 : 1),
			'errorlist' => ""
		);
		$this->load->view('login/login_view2', $act);
	}

	public function test_user() {
		$result = $this->db->query("SELECT
		`admins`.id,
		`admins`.base_id,
		`admins`.password,
		`admins`.rank,
		`admins`.uid,
		`admins`.active,
		`admins`.supervisor,
		`admins`.description
		FROM
		`admins`
		WHERE
		admins.user = ?", array($this->input->post('name', TRUE)));
		if ( !$result->num_rows() ) {
			$this->setUserNotFound();
		}

		if ($result->num_rows()) {
			$row = $result->row();
			if ('secret'.$this->input->post('pass') != $row->password ) {
				$this->setUserNotFound();
			}
			if ( !$row->active ) {																//� ����� ���� ������������ �� ���������?
				array_push( $this->errors, "������������ � ���������� ������ � ������� ���������." );
			}
			if ( !sizeof($this->errors) ) {
				if ( $this->setUserSessionData($row) ) {
					redirect("");
				}
			}
		}
		$act['errorlist'] = implode( $this->errors, "<br>\n");
		$this->load->view( 'login/login_view2', $act );
	}

}
#
/* End of file loginmodel.php */
/* Location: ./system/application/models/loginmodel.php */