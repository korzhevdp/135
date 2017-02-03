<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bids extends CI_Controller {
	/*
	����� ���������� ������ �� �������������� �������
	*/
	
	private $online = 1;
	
	public function __construct() {
		parent::__construct();
		$this->session->set_userdata('pageHeader', '������ ������ �� �������������� �������');

		$this->load->model('bidsmodel2');								// ������������ ������ UI ������
		$this->load->model('bidsuimodel');								// ������������ ������ UI ������
		$this->load->model('usefulmodel');								// ������������ ������ ������
		$this->load->helper('url'); 									// ������������ ������ ��� ���������������
		$this->load->library('user_agent');
		//$this->output->enable_profiler(TRUE);
	}
	
	public function index(){
		//$this->output->enable_profiler(TRUE);
		$userid = ($this->input->post("userSelector"))					// ���������� ������������� ������������
			? $this->input->post("userSelector")
			: 0;
		$res = ($userid)												// ���������� ��������� ������ ��������
			? $this->bidsuimodel->user_data_get($userid)				// ���� ������ � ������ ������������
			: $this->bidsuimodel->blank_data_get();						// ���� ����� ������ ������������
		$res['filter'] = urldecode($this->session->userdata("filter"));	// ������������� ������ ���������� �������������
		$res['locs']   = $this->bidsuimodel->locs_get();

		$act['menu'] = ($this->session->userdata("base_id"))			// ��������� ����
																		// ���� ���� ������������
			? $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true)
			: '';														// ���� ������ ���� (������������ "���������� ������")

		if ($this->online) {
			$act['content'] = ( $this->agent->browser() === "Internet Explorer")
				? "��� ������� �� ������������� �����������.<br>����������� Mozilla Firefox ��� Google Chrome"
				: $this->load->view('bids/mainform2', $res, true);
		}
		if (!$this->online ) {
			$act['content'] = ((int)$this->session->userdata('admin_id') === 1)
				? $this->load->view('bids/mainform2', $res, true)
				: "<h4>������ ������ �������� ���������.</h4>���� ����������� ������. ���������� �����.";
		}
			// (!in_array($this->session->userdata('admin_id'), array()))
																		// �������� dev-������. ��� ����� - �������� � ������ ID ���������� � array()
			//? $this->load->view('bids/mainform2', $res, true)			// ������������� �����
			//: $this->load->view('bids/mainform',  $res, true);		// ����������� �����

		$act['footer'] = $this->load->view('page_footer', array(), true);	// �������� �������

		$this->load->view('page_container', $act);						// �������� ������ ����������
		$this->usefulmodel->no_cache();									// ���������� ��������������� ����������� FF � ����.
	}

	######## AJAX-������ ��� ��������� ������ � ����������
	public function apply_filter($filter="") {							// ���������� �������������
		$this->session->set_userdata('filter', $filter);				// ���������� ������
		$this->adminmodel->filter_users($filter);						// ���������� �������������
	}

	public function get_subproperties($res=0) {
		$this->bidsmodel2->subproperties_get($res);						// ���������� ������� ������� �������� �� rid
	}

	public function getwebportalsection() {
		$this->bidsmodel2->getWebPortalSection();						// ���������� ������� ������� �������� �� rid
	}

	public function getuserdata(){
		print $this->bidsuimodel->user_data_get2($this->input->post("uid"));
	}

	public function getuserresources(){
		print $this->bidsuimodel->user_res_get($this->input->post("uid"));
	}

	public function resetUID() {
		$this->session->set_userdata('uid', 0);
		print "uid unset";
	}
}

/* End of file bids2.php */
/* Location: ./application/controllers/bids2.php */