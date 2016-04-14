<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Arm extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		$this->load->model('armmodel');
		$this->load->model('usefulmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function index($dep = 0){
		$this->showdep($dep);
	}

	public function warehouse(){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->armmodel->show_warehouse(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function invunits($dep = 0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->armmodel->show_invunits(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function get_inv_units(){
		$this->armmodel->get_inv_units();
	}

	public function get_inv_unit(){
		$this->armmodel->get_inv_unit();
	}

	public function showdep($dep = 0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->armmodel->showdep(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function create_arm(){
		$this->armmodel->create_arm();
	}

	public function addptoarm(){
		$this->armmodel->addptoarm();
	}

	public function dev_save(){
		$this->armmodel->dev_save();
	}

	public function inv_unit_save(){
		$this->armmodel->inv_unit_save();
	}

	## AJAX broadcast informer FX
	public function pc_list_get($print = 0){
		$this->armmodel->pc_list_get($print);
	}

	public function pc_grid($user = 0, $print = 0){
		$this->armmodel->pc_grid($user, $print);
	}

	public function userlist($print = 0){
		$this->armmodel->userlist($print);
	}

	public function conf_get($print = 0){
		$this->armmodel->conf_get($print);
	}

	public function lockpc(){
		$conf_id = $this->input->post("ref");
		//print $conf_id;
		$this->armmodel->lockpc($conf_id);
		$this->armmodel->conf_get(1);
	}

	public function unlockpc(){
		$conf_id = $this->input->post("ref");
		$this->armmodel->unlockpc($conf_id);
		$this->armmodel->conf_get(1);
	}
}

/* End of file arm.php */
/* Location: ./application/controllers/arm.php */