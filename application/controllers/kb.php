<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kb extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('kbmodel');
		if($this->session->userdata("admin_id") == 1){
			//$this->output->enable_profiler(TRUE);
		}
	}

	public function index(){
		$act = array();
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$act['content'] = "<h2>База знаний  <small>о некоторых вопросах обустройства ЛВС</small></h2><hr>";
		$act['content'] .= $this->kbmodel->index_show();
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->load->view('page_container', $act);
	}

	public function page($page){
		$act = array();
		$act['menu'] = $this->load->view('menu/navigation', '', true);
		$act['content'] = "<h2>База знаний  <small>о некоторых вопросах обустройства ЛВС</small></h2><hr>";
		$act['content'] .= $this->load->view('knowledgebase/'.$page, '', true);
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->load->view('page_container', $act);
	}

}

/* End of file kb.php */
/* Location: ./application/controllers/kb.php */