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
		$data = array(
			'leaderless'      => $this->integritymodel->int_leaders_get(),
			'deptless'        => $this->integritymodel->int_freeworkers_get(),
			'irless'          => $this->integritymodel->int_irless_get(),
			'curless'         => $this->integritymodel->int_curless_get(),
			'ownerless_hosts' => $this->integritymodel->int_ownerless_hosts_get(),
			'yield'           => $this->integritymodel->int_labels_get(),
			'userlist'        => $this->integritymodel->int_userlist_get(),
			'page'            => $page
		);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => "<h2>Целостность данных</h2><hr>".$this->load->view('integrity/report', $data, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
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

	}

	public function savelabel(){
		$result = $this->db->query("SELECT
		`ak_licenses`.id
		FROM 
		`ak_licenses`
		WHERE
		`ak_licenses`.`label` = ?", array(
			$this->input->post("label")
		));
		if ($result->num_rows()) {
			print "Fail";
			return false;
		}
		/*
		$result = $this->db->query("SELECT 
		`inv_po_licenses_sets`.label_starts + `inv_po_licenses_sets`.`max` AS maxlabel
		FROM
		`inv_po_licenses_sets`
		WHERE
		`inv_po_licenses_sets`.label_starts LIKE '".substr($this->input->post("label"), 0 -4)."%'");
		if ($result->num_rows()) {
			$row = 0;
		}
		*/
		//print "You may write";
		//return false;
		$result = $this->db->query("UPDATE
		`ak_licenses`
		SET
		`ak_licenses`.`label` = ?
		WHERE
		`ak_licenses`.`id` = ?", array(
			$this->input->post("label"),
			$this->input->post("pcID")
		));
		if ($this->db->affected_rows()) {
			print "OK";
			return true;
		}
		print "Not found";
		return false;
		//$this->output->enable_profiler(TRUE);
	}

}

/* End of file integrity.php */
/* Location: ./application/controllers/integrity.php */