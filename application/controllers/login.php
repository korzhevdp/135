<?php
class Login extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('loginmodel');
		$this->load->model('usefulmodel');
	}

	public function index($mode='auth'){
		if($this->input->post('name') && $this->input->post('pass')){
			$this->loginmodel->_test_user();
		}else{
			$this->loginmodel->index($mode);
		}
	}

	public function logout(){
		$this->load->helper('url');
		$this->session->sess_destroy();
		$this->usefulmodel->insert_audit("Завершение сессии оператора #".$this->session->userdata('user_name'));
		redirect('admin');
	}
}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */