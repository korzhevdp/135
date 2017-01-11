<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bids extends CI_Controller {
	/*

	Класс оформления заявок на информационные ресурсы
	
	*/
	public function __construct() {
		parent::__construct();
		$this->load->model('bidsmodel');								// подключается модель заявок
		$this->load->model('usefulmodel');								// подключается модель утилит
		$this->load->helper('url'); 									// подключается хелпер для перенаправления
		//$this->output->enable_profiler(TRUE);
	}
	
	public $dbwrite = 0;
	
	public function index(){
		//$this->output->enable_profiler(TRUE);
		//$this->load->helper('form');									// подключается хелпер создания HTML-форм (где-то используется)
		$userid = ($this->input->post("userSelector"))					// получается идентификатор пользователя
			? $this->input->post("userSelector")
			: 0;
		$res = ($userid)												// получаются начальные данные страницы
			? $this->bidsmodel->user_data_get($userid)					// либо данные с учётом пользователя
			: $this->bidsmodel->blank_data_get();						// либо бланк нового пользователя
		$res['filter'] = urldecode($this->session->userdata("filter"));	// раскодируется строка фильтрации пользователей
		$res['locs']   = $this->bidsmodel->locs_get();

		$act['menu'] = ($this->session->userdata("base_id"))			// формируем меню
			? $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true)			// либо меню пользователя
			: '';														// либо пустое поле (пользователь "оформление заявок")
		
		$act['content'] = $this->load->view('bids/mainform_dev', $res, true);
																		// (!in_array($this->session->userdata('admin_id'), array())) 
																		// селектор dev-режима. При нужде - добавить в массив ID операторов в array()
			//? 														// девелоперский режим
			//: $this->load->view('bids/mainform', $res, true);			// стандартный режим
		
		$act['footer'] = $this->load->view('page_footer', '', true);	// загрузка подвала
		
		$this->load->view('page_container', $act);						// загрузка общего оформления
		$this->usefulmodel->no_cache();									// отключение параноидального кэширования FF и иных.
	}

	public function getpapers(){										// простое получение заявок
		//$this->output->enable_profiler(TRUE);
		$this->bidsmodel->papers_get();									// вызов простого получения заявок из модели
	}

	public function phpinfo(){
		phpinfo();
	}

	######## AJAX-секция
	public function apply_filter($filter=""){							// фильтрация пользователей
		$this->session->set_userdata('filter',$filter);					// обновление сессии
		$this->adminmodel->filter_users($filter);						// фильтрация пользователей
	}

	public function get_subproperties($res=0){
		$this->bidsmodel->subproperties_get($res);					// извлечение частных свойств ресурсов по rid
	}

	public function getwebportalsection() {
		$this->bidsmodel->getWebPortalSection();					// извлечение частных свойств ресурсов по rid
	}

	public function reget_orders($res = array()){
		$this->bidsmodel->reget_orders($res);						// повторное получение заявок по itemid
	}

	public function getuserdata(){
		print $this->bidsmodel->user_data_get2($this->input->post("uid"));
	}

	public function getuserresources(){
		print $this->bidsmodel->user_res_get($this->input->post("uid"));
	}

	public function resetUID() {
		$this->session->set_userdata('uid', 0);
		print "uid unset";
	}

}