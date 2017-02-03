<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bids extends CI_Controller {
	/*
	Класс оформления заявок на информационные ресурсы
	*/
	
	private $online = 1;
	
	public function __construct() {
		parent::__construct();
		$this->session->set_userdata('pageHeader', 'Подача заявок на информационные ресурсы');

		$this->load->model('bidsmodel2');								// подключается модель UI заявок
		$this->load->model('bidsuimodel');								// подключается модель UI заявок
		$this->load->model('usefulmodel');								// подключается модель утилит
		$this->load->helper('url'); 									// подключается хелпер для перенаправления
		$this->load->library('user_agent');
		//$this->output->enable_profiler(TRUE);
	}
	
	public function index(){
		//$this->output->enable_profiler(TRUE);
		$userid = ($this->input->post("userSelector"))					// получается идентификатор пользователя
			? $this->input->post("userSelector")
			: 0;
		$res = ($userid)												// получаются начальные данные страницы
			? $this->bidsuimodel->user_data_get($userid)				// либо данные с учётом пользователя
			: $this->bidsuimodel->blank_data_get();						// либо бланк нового пользователя
		$res['filter'] = urldecode($this->session->userdata("filter"));	// раскодируется строка фильтрации пользователей
		$res['locs']   = $this->bidsuimodel->locs_get();

		$act['menu'] = ($this->session->userdata("base_id"))			// формируем меню
																		// либо меню пользователя
			? $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true)
			: '';														// либо пустое поле (пользователь "оформление заявок")

		if ($this->online) {
			$act['content'] = ( $this->agent->browser() === "Internet Explorer")
				? "Ваш браузер не соответствует требованиям.<br>Используйте Mozilla Firefox или Google Chrome"
				: $this->load->view('bids/mainform2', $res, true);
		}
		if (!$this->online ) {
			$act['content'] = ((int)$this->session->userdata('admin_id') === 1)
				? $this->load->view('bids/mainform2', $res, true)
				: "<h4>Подача заявок временно отключена.</h4>Идут технические работы. Попробуйте позже.";
		}
			// (!in_array($this->session->userdata('admin_id'), array()))
																		// селектор dev-режима. При нужде - добавить в массив ID операторов в array()
			//? $this->load->view('bids/mainform2', $res, true)			// девелоперский режим
			//: $this->load->view('bids/mainform',  $res, true);		// стандартный режим

		$act['footer'] = $this->load->view('page_footer', array(), true);	// загрузка подвала

		$this->load->view('page_container', $act);						// загрузка общего оформления
		$this->usefulmodel->no_cache();									// отключение параноидального кэширования FF и иных.
	}

	######## AJAX-секция для получения данных в интерфейсе
	public function apply_filter($filter="") {							// фильтрация пользователей
		$this->session->set_userdata('filter', $filter);				// обновление сессии
		$this->adminmodel->filter_users($filter);						// фильтрация пользователей
	}

	public function get_subproperties($res=0) {
		$this->bidsmodel2->subproperties_get($res);						// извлечение частных свойств ресурсов по rid
	}

	public function getwebportalsection() {
		$this->bidsmodel2->getWebPortalSection();						// извлечение частных свойств ресурсов по rid
	}

	public function getuserdata(){
		print $this->bidsuimodel->user_data_get2($this->input->post("uid"));
	}

	public function getuserresources(){
		print $this->bidsuimodel->user_res_get($this->input->post("uid"));
	}

	public function resetUID() {
		$this->session->set_userdata('uid', 0);
		print "uid unset";
	}
}

/* End of file bids2.php */
/* Location: ./application/controllers/bids2.php */