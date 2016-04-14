<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uvmr extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter','') : "";
		(!$this->session->userdata('uid')) ? $this->session->set_userdata('uid',1) : "";
		$this->load->model('uvmrmodel');
		$this->load->model('usefulmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function index($user_id=0,$page=1){
		$act = array();
		$data = array();
		$act['menu'] = $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true);
		$data['imdsp_table'] = $this->uvmrmodel->imdsp_get();
		$act['content'] = "<h2>Специальные отчёты Отдела по защите информации.</h2><hr>".$this->load->view('uvmr/uvmr', $data, true);
		$act['footer'] = $this->load->view('page_footer', array(), true);
		
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function passport($user_id=0){
		//$this->output->enable_profiler(TRUE);
		$act = array();
		$user = $this->uvmrmodel->passport_get($user_id);
		//print_r($user);
		$user['filter'] = urldecode($this->session->userdata("filter"));
		$user['userid'] = $user['id'];
		$act['menu']    = $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true);
		$act['content'] = $this->load->view('uvmr/passport' , $user, true);
		$act['footer']  = $this->load->view('page_footer' , array(), true);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

}

/* End of file uvmr.php */
/* Location: ./application/controllers/uvmr.php */