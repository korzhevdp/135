<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Network extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if(!$this->session->userdata('admin_id')){
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid'))    ? $this->session->set_userdata('uid', 1) : "";
		$this->load->model('usefulmodel');
		$this->load->model('netmodel');
		//$this->output->enable_profiler(TRUE);
	}

	public function index($user_id=0,$page=1){
		$act = array();
		$act['menu']     = $this->load->view('menu/navigation', '', true);
		$act['content']  = "<h2>Структура ЛВС мэрии</h2><hr>";
		$act['content'] .= $this->load->view('network/structure', $this->netmodel->branches_get(), true);
		$act['footer']   = $this->load->view('page_footer', '', true);
		
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function getunit(){
		///$this->output->enable_profiler(TRUE);
		$this->netmodel->unit_get();

	}

	public function saveunit(){
		$this->output->enable_profiler(TRUE);
		/*
			host:   $("#chm").val(),
			hostip: $("#cip").val(),
			mac:    $("#cmc").val(),
			loc:    $("#clc").val(),
			sip:    $("#cpip").val(),
			pport:  $("#cpport").val(),
			vlan:   $("#сvlan").val(),
			dir:    $("#cdir").val()
		*/
		$result = $this->db->query("UPDATE
		switch_connections
		SET
		switch_connections.host_ip = ?,
		switch_connections.host_name = ?,
		switch_connections.location = ?,
		switch_connections.mac = ?,
		switch_connections.vlan = ?,
		switch_connections.dir = ?,
		switch_connections.comment = ?
		WHERE
		switch_connections.id = ?", array(
			$this->input->post("hostip"),
			$this->input->post("host"),
			$this->input->post("loc"),
			$this->input->post("mac"),
			$this->input->post("vlan"),
			$this->input->post("dir"),
			iconv("UTF-8", "Windows-1251", $this->input->post("comment")),
			$this->input->post("id")
		));
	}

		public function sw(){
		$result = $this->db->query("UPDATE
		switch_connections
		SET
		switch_connections.active = ?
		WHERE
		switch_connections.id = ?", array(
			$this->input->post("mode"),
			$this->input->post("node")
		));
	}
}

/* End of file network.php */
/* Location: ./application/controllers/network.php */