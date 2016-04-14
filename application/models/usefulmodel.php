<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usefulmodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function no_cache(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 
	}

	public function filter_users($filter="", $withFired){
		$uid = ($this->session->userdata("uid")) ? $this->session->userdata("uid") : "1";
		$filter = iconv('UTF-8', 'Windows-1251' , urldecode($filter)).'%';
		$mode = (preg_match("/[a-zA-Z]/",$filter)) ? "host" : "name";
		$fired = ($withFired) ? "" : " AND NOT users.fired ";
		//print $fired;
		if($mode == "name"){
			$result =$this->db->query("SELECT 
			CONCAT('<option value=',
			`users`.`id`,
			IF(`users`.id = ?, ' selected = \"selected\">', '>'),
			CONCAT_WS(' ', `users`.`name_f`, `users`.`name_i`, `users`.`name_o`), '</option>') AS `options`
			FROM
			`users`
			WHERE
			LOWER(CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o)) LIKE ?
			".$fired."
			ORDER BY `users`.name_f ASC,`users`.name_i ASC,`users`.name_o ASC", array($uid, $filter));
		}else{
			$result = $this->db->query("SELECT 
			CONCAT('<option value=',
			`users`.id,
			IF(`users`.id = ?, ' selected = \"selected\">', '>'),
			CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o),
			'</option>') as options
			FROM
			`users`
			WHERE 
			users.host LIKE ?
			".$fired."
			ORDER BY `users`.name_f ASC,`users`.name_i ASC,`users`.name_o ASC",array($uid, $filter));
		}
		$output = array();
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		$list = implode($output,"\n");
		//print $this->db->last_query();
		print $list;
	}

	public function insert_audit($desc="Операции не дано описания"){
		$this->db->query("INSERT INTO audit (audit.author,audit.query,audit.desc) VALUES (?,?,?)",array($this->session->userdata('admin_id'),$this->db->last_query(),$desc));
	}

	public function aclgen(){
		//detecting dupes
		$input = array();
		$dupes = array();
		$cl = "xxx";
		$output = array();
		$result = $this->db->query("SELECT DISTINCT
		users.login,
		`resources_pid`.pid_value,
		INET_NTOA(`resources_pid`.pid_value) as ip
		FROM
		resources_items
		INNER JOIN users ON (resources_items.uid = users.id)
		INNER JOIN `resources_pid` ON (resources_items.id = `resources_pid`.item_id)
		WHERE
		(resources_items.ok) AND 
		(NOT (resources_items.`exp`)) AND 
		(NOT (resources_items.del)) AND 
		(resources_items.rid = 101) AND
		`resources_pid`.`pid` = 6 AND
		not users.`fired`
		ORDER BY `resources_pid`.pid_value");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$input[$row->ip] = $row->ip."/255.255.255.255 # ".$row->login;
			}
		}
		foreach($input as $key => $val ){
			$subnet = explode(".",$key);
			if($subnet[2]!==$cl){
				$cl = $subnet[2];
				array_push($output, "\n### subnet 192.168.".$subnet[2].".0/24");
			}
			array_push($output, $val);
		}
		$telnet=fsockopen("212.14.176.38", 587);
		(!$telnet) ? die ("Cannot connect!") : fputs($telnet,implode($output,"\n"));
		($telnet) ? fclose($telnet) : "";
	}

	public function getNavMenuData(){
		$data = array();
		$result = $this->db->query("SELECT 
		COUNT(events.id) AS `count`
		FROM
		events
		WHERE
		(events.recipient = ?)
		AND events.active
		LIMIT 1", array($this->session->userdata("canSee")));
		if($result->num_rows()){
			$row = $result->row(0);
			$data['tickets'] = $row->count;
		}
		return $data;
	}

}

/* End of file usefulmodel.php */
/* Location: ./application/models/usefulmodel.php */