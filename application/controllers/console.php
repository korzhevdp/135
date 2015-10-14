<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Console extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter','') : "";
		(!$this->session->userdata('uid')) ? $this->session->set_userdata('uid',1) : "";
		$this->load->model('consmodel');
		$this->load->model('usefulmodel');
		if($this->session->userdata("admin_id") == 1){
			//$this->output->enable_profiler(TRUE);
		}
	}

	public function index($user_id=0,$page=1){
		$act = array();
		$properties = $this->consmodel->searchform_get();
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$act['content'] = $this->load->view('console/searchform', $properties, true);
		$act['footer'] = $this->load->view('page_footer', '', true);
		
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function search($user_id=0,$page=1){
		$act = array();
		$properties = $this->consmodel->searchform_get();
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$act['content'] = $this->load->view('console/searchform', $properties, true);
		$act['content'] .= $this->consmodel->search_perform();
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function pcflow($user_id=0, $page=1){
		$act = array();
		$data = array();
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$data['filter'] = (strlen($this->input->post('userid'))) ? $this->input->post('userid') : "";
		$act['content'] = $this->load->view('console/pcflow', $data, true);
		$act['content'] .= $this->consmodel->pc_grid($user_id);
		$act['content'] .= $this->load->view('console/pcconf', $data, true);
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function pcconf($conf_id){
		print $this->consmodel->user_pcconf_get($conf_id);
	}

	public function lockpc($conf_id,$return){
		$this->db->query("Update hash_items set hash_items.active = 0 Where hash_items.id = ?", array($conf_id));
		$this->load->helper('url');
		redirect("console/pcflow/".$return);
	}

	public function unlockpc($conf_id,$return){
		$this->db->query("Update hash_items set hash_items.active = 1 Where hash_items.id = ?", array($conf_id));
		$this->load->helper('url');
		redirect("console/pcflow/".$return);
	}
}

/* End of file console.php */
/* Location: ./application/controllers/console.php */