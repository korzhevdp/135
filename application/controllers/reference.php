<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reference extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		$this->session->set_userdata('pageHeader', 'Справочники');

		$this->load->model('refmodel');
		$this->load->model('usefulmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function resources($id=0, $tab=0){
		$resource     = ($this->input->post('resource')) ? $this->input->post('resource') : $this->input->post('resID');
		$resource     = ($id) ? $id : $resource;
		$res          = $this->refmodel->res_data_get($resource);
		$res['list']  = $this->refmodel->res_list_get($resource);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reference/resources', $res, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->refmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function adm_sec_add(){
		$this->refmodel->res_admsec_add();
		$this->load->helper('url');
		redirect("reference/resources/".$this->input->post("resID")."/2");
	}

	public function adm_sec_remove(){
		$this->refmodel->res_admsec_remove();
		$this->load->helper('url');
		redirect("reference/resources/".$this->input->post("resID")."/2");
	}

	public function save_resource(){
		$this->refmodel->res_data_save();
		$this->load->helper('url');
		redirect("reference/resources/".$this->input->post("resID")."/1");
	}

	public function save_form(){
		//$this->output->enable_profiler(TRUE);
		$this->refmodel->res_form_save();
		//return false;
		$this->load->helper('url');
		
		redirect("reference/resources/".$this->input->post("resID")."/1");
	}

	public function res_finish(){
		//$this->output->enable_profiler(TRUE);
		$result = $this->db->query("UPDATE
		`resources`
		SET
		`resources`.f_enddate = NOW(),
		`resources`.f_endreason = ?,
		`resources`.active = 0
		WHERE
		 `resources`.`id` = ?", array(
			substr(trim($this->input->post("f_endreason")), 0, 600),
			$this->input->post("resID")
		));
		$this->load->helper('url');
		redirect("reference/resources/".$this->input->post("resID")."/1");
	}
	
	public function res_restart(){
		//$this->output->enable_profiler(TRUE);
		$result = $this->db->query("UPDATE
		`resources`
		SET
		`resources`.active = 1
		WHERE
		 `resources`.`id` = ?", array(
			$this->input->post("resID")
		));
		$this->load->helper('url');
		redirect("reference/resources/".$this->input->post("resID")."/1");
	}

	#########################################################
	#########################################################

	public function locations($location = 0){
		//$this->load->helper('form');
		if ($this->input->post("saveName")) {
			$this->refmodel->location_data_save();
		}
		if ($this->input->post("addNewName")) {
			$this->refmodel->location_data_add();
		}
		if ($this->input->post("addSubLocation")) {
			$this->refmodel->sublocation_data_add();
		}
		if ($this->input->post("locationID")) {
			$this->refmodel->sublocation_data_save();
		}
		$location = ($this->input->post('location')) ? $this->input->post('location') : $location;
		$res = $this->refmodel->location_data_get($location);
		$res['locations'] = $this->refmodel->locations_list_get($location);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reference/locations', $res, true),
			'footer'  => $this->load->view('page_footer', array(), true),
		);
		$this->refmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function useraccess($location=0){
		$this->session->set_userdata('pageHeader', 'Управление операторами');
		if($this->input->post("newpassword")){
			$this->refmodel->new_password();
		}
		if($this->input->post("newAdmin") || $this->input->post("saveAdmin")) {
			$this->refmodel->admin_data_save();
			//return false;
		}
		$this->load->helper('form');
		$res = $this->refmodel->admin_data_get();
		$res['admin_list'] = $this->refmodel->admins_list_get();
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reference/admins', $res, true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->load->view('page_container', $act);
		$this->refmodel->no_cache();
	}

	public function depts($dept=0){
		$dept = ($this->input->post("depSelector")) ? $this->input->post("depSelector") : $dept;
		if($this->input->post("newDept") || $this->input->post("saveDept")){
			$this->refmodel->dept_data_save();
		}
		$this->load->helper('form');
		$res = $this->refmodel->dept_data_get($dept);
		$res['dept_list'] = $this->refmodel->dept_list_get($dept);
		$act['menu'] = $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true);
		$act['content'] = $this->load->view('reference/depts', $res, true);
		$act['footer'] = $this->load->view('page_footer', '', true);
		$this->load->view('page_container', $act);
		$this->refmodel->no_cache();
	}

	public function staff($staff = 0) {
		$staff = ($this->input->post("staffSelector")) ? $this->input->post("staffSelector") : $staff;
		if($this->input->post("newStaff") || $this->input->post("saveStaff")){
			$this->refmodel->staff_data_save();
		}
		$this->load->helper('form');
		$res = $this->refmodel->staff_data_get($staff);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('reference/staff', $res, true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->load->view('page_container', $act);
		$this->refmodel->no_cache();
	}
	######## AJAX-секция
}

/* End of file reference.php */
/* Location: ./application/controllers/reference.php */