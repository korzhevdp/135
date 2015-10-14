<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Integrity extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter','') : "";
		(!$this->session->userdata('uid')) ? $this->session->set_userdata('uid',1) : "";
		$this->load->model('integritymodel');
		$this->load->model('usefulmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function index(){
		$this->show();
	}

	public function show($page=1){
		$act = array();
		$data = array();
		$data['leaderless']=$this->integritymodel->int_leaders_get();
		$data['deptless']=$this->integritymodel->int_freeworkers_get();
		$data['irless']=$this->integritymodel->int_irless_get();
		$data['curless']=$this->integritymodel->int_curless_get();
		$data['ownerless_hosts']=$this->integritymodel->int_ownerless_hosts_get();
		$data['yield']=$this->integritymodel->int_labels_get();
		$data['userlist']=$this->integritymodel->int_userlist_get();
		$data['page'] = $page;
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$act['content'] = "<h2>����������� ������</h2><hr>".$this->load->view('integrity/report', $data, true);
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function hosts_bind($uid, $hid){
		$this->db->query("UPDATE hosts SET hosts.uid = ? WHERE hosts.id = ?", array($uid, $hid));
	}

	public function mark_serv($hid){
		$this->db->query("UPDATE hosts SET hosts.server = 1 WHERE hosts.id = ?", array($hid));
	}

	public function mark_noise($hid){
		$this->db->query("UPDATE hosts SET hosts.noise = 1 WHERE hosts.id = ?", array($hid));
	}

	public function writelabel($id){
		/*
		$this->db->query("UPDATE ak_licenses SET ak_licenses.l_written = 1 WHERE ak_licenses.id = ?", array($id));
		$this->load->helper('url');
		redirect('integrity/show/6');
		*/
		$file = "/var/ncontrol/".strtoupper($this->input->post('host')).".txt";
		$open = fopen($file, "w");
		fputs($open, $this->input->post('label'));
		fclose($open);
	}

	public function labelgiven($id){
		$this->db->query("UPDATE ak_licenses SET ak_licenses.l_given = 1 WHERE ak_licenses.id = ?", array($id));
		$this->load->helper('url');
		redirect('integrity/show/6');
	}

	public function labelreport(){
		$this->integritymodel->labelreport();
	}

	public function checklabel(){
		$result = $this->db->query("SELECT `ak_licenses`.id FROM `ak_licenses` WHERE `ak_licenses`.`label` = ? AND NOT `ak_licenses`.`manual`", array($this->input->post("label")));
		($result->num_rows()) ? print 0 : print 1;
	}

	public function savelabel(){
		$this->db->query("UPDATE 
		`ak_licenses` 
		SET 
		`ak_licenses`.`label` = ? 
		WHERE `ak_licenses`.`id` = ?", array(
			"1".str_replace("-", "", $this->input->post("newNum")),
			$this->input->post("pcId")
		));
		$this->load->helper('url');
		redirect('integrity/show/6#'.$this->input->post("pcId"));
		//$this->output->enable_profiler(TRUE);
	}

}

/* End of file integrity.php */
/* Location: ./application/controllers/integrity.php */