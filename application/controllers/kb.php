<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kb extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('kbmodel');
		$this->load->model('usefulmodel');
		if($this->session->userdata("admin_id") == 1){
			//$this->output->enable_profiler(TRUE);
		}
	}

	public function index(){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => "<h2>База знаний  <small>о некоторых вопросах обустройства ЛВС</small></h2><hr>".$this->kbmodel->index_show(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->load->view('page_container', $act);
	}

	public function page($page){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('knowledgebase/template/kbtemplate', array('text' => $this->load->view('knowledgebase/'.$page, array(), true)), true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->load->view('page_container', $act);

	}

}

/* End of file kb.php */
/* Location: ./application/controllers/kb.php */