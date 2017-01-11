<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsfactory extends CI_Controller {
	/*
	����� ���������� ������ �� �������������� �������
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('bidsfactorymodel');							// ������������ ������ ������
		$this->load->model('usefulmodel');								// ������������ ������ ������
		//$this->load->helper('url'); 									// ������������ ������ ��� ���������������
		//$this->output->enable_profiler(TRUE);
	}

	public function getpapers() {										// ������� ��������� ������
		//$this->output->enable_profiler(TRUE);
		$this->bidsfactorymodel->papers_get();							// ����� �������� ��������� ������ �� ������
	}

	public function reget_orders($res = array()) {
		$this->bidsfactorymodel->reget_orders($res);					// ��������� ��������� ������ �� itemid
	}
}
/* End of file bidsfactory.php */
/* Location: ./application/controllers/bidsfactory.php */