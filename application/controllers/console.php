<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Console extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}

		$this->session->set_userdata('pageHeader', 'Поиск людей');
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid'))    ? $this->session->set_userdata('uid', 1)     : "";
		$this->load->model('consmodel');
		$this->load->model('usefulmodel');
		if($this->session->userdata("admin_id") == 1){
			//$this->output->enable_profiler(TRUE);
		}
	}

	public function index($user_id=0,$page=1){
		$properties = $this->consmodel->searchform_get();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('console/searchform', $properties, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function search($user_id=0,$page=1){
		$properties = $this->consmodel->searchform_get();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('console/searchform', $properties, true).$this->consmodel->search_perform(),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function pcflow($user_id=0, $page=1){
		$this->session->set_userdata('pageHeader', 'Движение компьютеров');
		$data = array(
			'filter' => (strlen($this->input->post('userid'))) ? $this->input->post('userid') : ""
		);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('console/pcflow', $data, true)
				.$this->consmodel->pc_grid($user_id)
				.$this->load->view('console/pcconf', $data, true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function pcconf($conf_id){
		print $this->consmodel->user_pcconf_get($conf_id);
	}

	public function lockpc($conf_id,$return){
		$this->db->query("UPDATE hash_items SET hash_items.active = 0 WHERE hash_items.id = ?", array($conf_id));
		$this->load->helper('url');
		redirect("console/pcflow/".$return);
	}

	public function unlockpc($conf_id,$return){
		$this->db->query("UPDATE hash_items SET hash_items.active = 1 WHERE hash_items.id = ?", array($conf_id));
		$this->load->helper('url');
		redirect("console/pcflow/".$return);
	}
}

/* End of file console.php */
/* Location: ./application/controllers/console.php */