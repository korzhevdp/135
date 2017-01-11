<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bids extends CI_Controller {
	/*

	����� ���������� ������ �� �������������� �������
	
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('bidsmodel');								// ������������ ������ ������
		$this->load->model('usefulmodel');								// ������������ ������ ������
		$this->load->helper('url'); 									// ������������ ������ ��� ���������������
		//$this->output->enable_profiler(TRUE);
	}
	
	public $dbwrite = 0;
	
	public function index(){
		//$this->output->enable_profiler(TRUE);
		//$this->load->helper('form');									// ������������ ������ �������� HTML-���� (���-�� ������������)
		$userid = ($this->input->post("userSelector"))					// ���������� ������������� ������������
			? $this->input->post("userSelector")
			: 0;
		$res = ($userid)												// ���������� ��������� ������ ��������
			? $this->bidsmodel->user_data_get($userid)					// ���� ������ � ������ ������������
			: $this->bidsmodel->blank_data_get();						// ���� ����� ������ ������������
		$res['filter'] = urldecode($this->session->userdata("filter"));	// ������������� ������ ���������� �������������
		$res['locs']   = $this->bidsmodel->locs_get();

		$act['menu'] = ($this->session->userdata("base_id"))			// ��������� ����
			? $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true)			// ���� ���� ������������
			: '';														// ���� ������ ���� (������������ "���������� ������")
		
		$act['content'] = $this->load->view('bids/mainform_dev', $res, true);
																		// (!in_array($this->session->userdata('admin_id'), array())) 
																		// �������� dev-������. ��� ����� - �������� � ������ ID ���������� � array()
			//? 														// ������������� �����
			//: $this->load->view('bids/mainform', $res, true);			// ����������� �����
		
		$act['footer'] = $this->load->view('page_footer', '', true);	// �������� �������
		
		$this->load->view('page_container', $act);						// �������� ������ ����������
		$this->usefulmodel->no_cache();									// ���������� ��������������� ����������� FF � ����.
	}

	public function getpapers(){										// ������� ��������� ������
		//$this->output->enable_profiler(TRUE);
		$this->bidsmodel->papers_get();									// ����� �������� ��������� ������ �� ������
	}

	public function phpinfo(){
		phpinfo();
	}

	######## AJAX-������
	public function apply_filter($filter=""){							// ���������� �������������
		$this->session->set_userdata('filter',$filter);					// ���������� ������
		$this->adminmodel->filter_users($filter);						// ���������� �������������
	}

	public function get_subproperties($res=0){
		$this->bidsmodel->subproperties_get($res);					// ���������� ������� ������� �������� �� rid
	}

	public function getwebportalsection() {
		$this->bidsmodel->getWebPortalSection();					// ���������� ������� ������� �������� �� rid
	}

	public function reget_orders($res = array()){
		$this->bidsmodel->reget_orders($res);						// ��������� ��������� ������ �� itemid
	}

	public function getuserdata(){
		print $this->bidsmodel->user_data_get2($this->input->post("uid"));
	}

	public function getuserresources(){
		print $this->bidsmodel->user_res_get($this->input->post("uid"));
	}

	public function resetUID() {
		$this->session->set_userdata('uid', 0);
		print "uid unset";
	}

}