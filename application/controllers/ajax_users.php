<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_users extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('usefulmodel');
	}

	public function apply_filter(){
		$this->session->set_userdata('filter', $this->input->post("search"));
		$this->usefulmodel->filter_users($this->input->post("search"), $this->input->post("fired"));
	}

	public function apply_filter_b($filter=""){
		$this->session->set_userdata('filter', $filter);
		$this->usefulmodel->filter_users($filter);
	}

}

/* End of file ajax_users.php */
/* Location: ./application/controllers/ajax_users.php */