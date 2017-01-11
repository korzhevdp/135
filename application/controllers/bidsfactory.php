<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsfactory extends CI_Controller {
	/*
	Класс оформления заявок на информационные ресурсы
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('bidsfactorymodel');							// подключается модель заявок
		$this->load->model('usefulmodel');								// подключается модель утилит
		//$this->load->helper('url'); 									// подключается хелпер для перенаправления
		//$this->output->enable_profiler(TRUE);
	}

	public function getpapers() {										// простое получение заявок
		//$this->output->enable_profiler(TRUE);
		$this->bidsfactorymodel->papers_get();							// вызов простого получения заявок из модели
	}

	public function reget_orders($res = array()) {
		$this->bidsfactorymodel->reget_orders($res);					// повторное получение заявок по itemid
	}
}
/* End of file bidsfactory.php */
/* Location: ./application/controllers/bidsfactory.php */