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
		ini_set('output_buffering', "Off");
		//$this->output->enable_profiler(TRUE);
	}

	public function index($page=1) {
		$this->show_page(array('page' => $page));
	}

	private function show_page($input) {
		$data = array(
			'page'      => (isset($input['page']))      ? $input['page']      : 1,
			'data'      => (isset($input['data']))      ? $input['data']      : '',
			'pcsearch'  => (isset($input['pcsearch']))  ? $input['pcsearch']  : '',
			'macsearch' => (isset($input['macsearch'])) ? $input['macsearch'] : ''
		);
		$act = array(
			'menu'     => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content'  => $this->load->view('network/structure', $data, true),
			'footer'   => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function getunit() {
		///$this->output->enable_profiler(TRUE);
		$this->netmodel->unit_get();
	}

	public function saveunit() {
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

	public function sw() {
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

	/*
		require 'rubygems'
		require 'active_record'
		require 'snmp'
		require 'net/ping/tcp'
		include SNMP

		module SNMP
		  class OctetString
			def to_mac
			  raise "Invalid MAC" unless self.length == 6
			  self.unpack("H2H2H2H2H2H2").join(":")
			end
		  end
		end

		ActiveRecord::Base.establish_connection(
		  adapter: "mysql",
		  host: "192.168.1.35",
		  username: "net",
		  password: "net",
		  database: "arhnet"
		  )

		class SwitchMac < ActiveRecord::Base
		end

		def snmp_request_with_parameter(manager, param, result_hash)
		  param_OID = ObjectId.new(param)
		  /1.3.6.1.2.1.(.+)$/ =~ param
		  key_mask = $1 + '.'
		  next_OID = param_OID
		  
		  while next_OID.subtree_of?(param_OID)
			response = manager.get_next(next_OID)
			varbind = response.varbind_list.first
		  
			if varbind.value.class == SNMP::OctetString
			  value = varbind.value.to_mac
			else
			  value = varbind.value.to_s
			end
		  
			%r{#{key_mask}(.+)} =~ varbind.name.to_s    
			key = $1
			result_hash[key] = value if key != nil   
			next_OID = varbind.name
		  end
		  return result_hash
		end

		def switch_scan(hostname, key_param, value_param, result_array)
		  Manager.open(:Host => hostname, :Version => :SNMPv2c, :Community => 'public') do |manager|
			begin
			  key_hash = {}
			  value_hash = {}
		  
			  snmp_request_with_parameter(manager, key_param, key_hash)
			  snmp_request_with_parameter(manager, value_param, value_hash)
			
			  key_hash.each_key do |key|
				if value_hash.has_key?(key)
				  result_key = key_hash[key]
				  result_value = value_hash[key]
				  result_array.push([result_key, result_value]) if result_key != "0"
				end
			  end
			rescue RequestTimeout
			  result_array.clear
			end
		  end
		  return result_array
		end

		def net_config
		  subnets = [1, 51, 52, 53, 54, 55]
		  network = []
		  subnets.each do |subnet_id|
			subnet = Array.new(34) { |i| "192.168.#{subnet_id}." + "#{221+i}" }
			network += subnet
		  end
		  return network
		end

		network = net_config
		port_to_mac_array = []

		dot1dTpFdbAddress = "1.3.6.1.2.1.17.4.3.1.1"        # parameter for mac
		dot1dTpFdbPort = "1.3.6.1.2.1.17.4.3.1.2"           # parameter for port
		atPhysAddress = "1.3.6.1.2.1.3.1.1.2"            	# parameter to define ip by mac via 248 switch

		network.each do |switch_ip| 
		  switch_scan(switch_ip, dot1dTpFdbPort, dot1dTpFdbAddress, port_to_mac_array)
		  unless port_to_mac_array.empty?
			port_to_mac_array.each do |key, value|
				if SwitchMac.find_by(mac: value, port: key, switch_ip: switch_ip).nil?
					sw = SwitchMac.new(switch_ip: switch_ip, port: key, mac: value)
					sw.save if key != 1
				end
			end
			port_to_mac_array.clear
		  end
		end
	*/

	private function recodeOID($host) {
		$mac     = "1.3.6.1.2.1.17.4.3.1.1";        # parameter for mac
		$port    = "1.3.6.1.2.1.17.4.3.1.2";        # parameter for port
		// 152.10
		//.1.3.6.1.4.1.25506.8.35.3.1.1.1
		$p_port  = "1.3.6.1.2.1.3.1.1.2";           # parameter to define ip by mac via 248 switch
		snmp_set_valueretrieval ( SNMP_VALUE_LIBRARY );
		$refs    = array();
		$inserts = array();
		$result  = $this->db->query("SELECT
		`switch_mac_rawdata`.hash
		FROM
		`switch_mac_rawdata`
		WHERE `switch_mac_rawdata`.`ip` = ?", array($host));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$refs[$row->hash] = 1;
			}
		}
		/*
		$newkey - промежуточный хэш хранения. применяется для связи данных при работе workers. 
		Рассчитывается и используется ввиду того, что при опросе по SNMP последние 7 сегментов OID совпадают, что позволяет разделить
		изучение портов и MAC.
		*/
		/*
		MAC Worker
		*/
		
		$object  = snmprealwalk( $host, "public", $mac );
		foreach ($object as $key=>$val) {
			$val = $this->parse_MAC($val);
			$newkey = implode(array_slice(explode(".", $key), -5), "");
			if ( isset($refs[$newkey]) ) {
				$this->db->query("UPDATE
				`switch_mac_rawdata`
				SET `switch_mac_rawdata`.mac    = ?
				WHERE `switch_mac_rawdata`.`ip` = ?
				AND `switch_mac_rawdata`.`hash` = ?", array($val, $host, $newkey));
			} else {
				array_push($inserts, "( '".$host."', ".$newkey.", '".$val."' )");
			}
		}
		if (sizeof($inserts)) {
			$this->db->query("INSERT INTO
			`switch_mac_rawdata`(
				`switch_mac_rawdata`.ip,
				`switch_mac_rawdata`.hash,
				`switch_mac_rawdata`.mac
			) VALUES ".implode($inserts, ",\n"));
		}
		/*
		Port Worker
		*/
		$refs    = array();
		$inserts = array();
		$result  = $this->db->query("SELECT
		`switch_mac_rawdata`.hash
		FROM
		`switch_mac_rawdata`
		WHERE `switch_mac_rawdata`.`ip` = ?", array($host));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$refs[$row->hash] = 1;
			}
		}
		$object2  = snmprealwalk( $host, "public", $port );
		foreach($object2 as $key=>$val){
			$newkey = implode(array_slice(explode(".", $key), -5), "");
			$val = $this->parse_INT($val);
			if ( isset($refs[$newkey]) ) {
				$this->db->query("UPDATE
				`switch_mac_rawdata`
				SET `switch_mac_rawdata`.port   = ? 
				WHERE `switch_mac_rawdata`.`ip` = ?
				AND `switch_mac_rawdata`.`hash` = ?", array($val, $host, $newkey));
			} else {
				array_push($inserts, "( '".$host."', ".$newkey.", '".$val."' )");
			}
		}
		if (sizeof($inserts)) {
			$this->db->query("INSERT INTO
			`switch_mac_rawdata`(
				`switch_mac_rawdata`.ip,
				`switch_mac_rawdata`.hash,
				`switch_mac_rawdata`.port
			) VALUES ".implode($inserts, ",\n"));
		}
	}
	
	public function collect_macs($mask="") {
		$supplied_mask = (!strlen($mask) && strlen($this->input->post("switchesRange")) && $this->input->post("switchesRange")) 
			? $this->input->post("switchesRange")
			: $mask;
		$mask =  preg_replace("/[^\.0-9]/", "", $supplied_mask);
		if (!strlen($supplied_mask)) {
			print "You have sullpied a wrong range";
			return false;
		}
		ob_start();
		//$mask = ($this->input->post("scan_mask") && strlen($this->input->post("scan_mask"))) ? $this->input->post("scan_mask") : $mask;
		$result = $this->db->query("SELECT 
		switch_net.`ip`
		FROM
		switch_net
		WHERE
		switch_net.`ip` LIKE '".$mask."%'
		ORDER BY switch_net.`ip`");
		if ($result->num_rows()) {
			$count = $total = $result->num_rows();
			print "Network Scan for discovered MAC-addresses initiated...<br><strong>".$total."</strong> switches has been scheduled to be processed so far<br><br>";
			flush();
			ob_flush();
			foreach($result->result() as $row){
				$this->recodeOID( $row->ip);
				print "Switch with IP: ".$row->ip." - scanned,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>".--$count."</strong> switches left, ".round(((1 - $count/$total) * 100), 2)."% completed.<br>";
				flush();
				ob_flush();
			}
		} else {
			Print "Nothing to scan";
		}
		$this->collect_main_data();
		ob_end_flush();
	}

	private function parse_MAC($val) {
		if ($val === false){
			return false;
		}
		$val = str_replace("Hex-STRING: ", "", (string) $val);
		$val = str_replace(" ", ":", trim($val));
		return $val;
	}

	private function parse_INT($val) {
		$val = str_replace("INTEGER: ", "", (string) $val);
		return $val;
	}

	private function parse_String($val, $parentheses = false) {
		if( $val === false ) {
			return false;
		}
		$val = str_replace("STRING: ", "", (string) $val);
		$val = str_replace('"', "", (string) $val);
		if ($parentheses) {
			$val = preg_replace("/\((.*)\)/", '', $val);
		}
		return $val;
	}

	private function discover_switches($host, $addtobase = false) {
		snmp_set_valueretrieval ( SNMP_VALUE_LIBRARY );
		$switch_mac = $this->parse_MAC(@snmpget($host, "public", ".1.3.6.1.2.1.17.1.1.0", 300000, 5));
		if($switch_mac !== false ){
			$portnum = $this->parse_INT(@snmpget($host, "public", ".1.3.6.1.2.1.17.1.2.0", 100000, 5));
			//$serial  = snmpget($host, "public", ".1.3.6.1.4.1.11.2.36.1.1.2.9.0", 100000, 5);
			$info    = $this->parse_String(@snmpget($host, "public", ".1.3.6.1.2.1.1.1.0", 100000, 5));
			$room    = $this->parse_String(@snmpget($host, "public", ".1.3.6.1.2.1.1.6.0", 100000, 5));
			$sn      = strtoupper($this->parse_String(@snmpget($host, "public", ".1.3.6.1.4.1.11.2.36.1.1.2.9.0", 100000, 5)));
			$altsn   = strtoupper($this->parse_String(@snmpget($host, "public", ".1.3.6.1.2.1.1.4.0", 100000, 5))); // вообще, тут у маленьких находится контактная информация.
			$model   = $this->parse_String(@snmpget($host, "public", ".1.3.6.1.2.1.1.5.0", 100000, 5));
			//$ver   = @snmpget($host, "public", ".1.3.6.1.4.1.11.2.36.1.1.2.6.0", 100000, 5);
			if (!$sn) {
				$sn = $altsn;
			}
			print "<tr>
				<td><a href=\"http://".$host."\" target=\"_blank\">".$host."</a></td>
				<td>".$switch_mac."</td>
				<td>".$portnum."</td>
				<td>".$room."</td>
				<td>".$model."</td>
				<td>".$info."</td>
				<td>".$sn."</td>
			</tr>";
			flush();
			ob_flush();
			$result = $this->db->query("SELECT 
				switch_net.`id`
				FROM
				switch_net
				WHERE
				`switch_net`.`sn` = ?", array($sn));
			if (!$result->num_rows() && $addtobase) {
				$this->db->query("INSERT INTO
				`switch_net`(
					`switch_net`.ip,
					`switch_net`.mac,
					`switch_net`.sn,
					`switch_net`.port_qty,
					`switch_net`.model,
					`switch_net`.location_w,
					`switch_net`.memo
				) VALUES( ?, ?, ?, ?, ?, ?, ? )", array(
					$host,
					$switch_mac,
					$sn,
					$portnum,
					$model,
					$room,
					$info
				));
			}
		}
	}

	public function collect_switches($nets = "") {
		ob_start();
		$nets = (!strlen($nets) && $this->input->post("netlist") && strlen($this->input->post("netlist"))) ? $this->input->post("netlist") : $nets ;
		print "<table class=\"table table-condensed table-bordered table-striped\"><tr>
			<td>IP</td>
			<td>MAC</td>
			<td>PORTS</td>
			<td>ROOM</td>
			<td>MODEL</td>
			<td>INFO</td>
			<td>SN</td>
		</tr>";
		$subnets = $this->get_subnets_array($nets);
		if (!$subnets) {
			exit;
		}
		foreach($subnets as $net){
			$this->discover_switches("192.168.".$net.".10", true);
			for ($a = 221; $a < 255; $a++) {
				$this->discover_switches("192.168.".$net.".".$a, true);
				flush();
				ob_flush();
			}
		}
		print "</table>";
		ob_end_flush();
	}

	public function collect_main_data() {
		$swdata = array();
		$result = $this->db->query("SELECT DISTINCT
		`switch_data`.port,
		`switch_data`.ip,
		`switch_data`.mac
		FROM
		`switch_data`
		GROUP BY `switch_data`.ip, `switch_data`.port");
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				if (!isset($swdata[$row->ip])) { $swdata[$row->ip] = array(); }
				$swdata[$row->ip][$row->port] = $row->mac;
			}
		}
		$result = $this->db->query("SELECT
		`switch_mac_rawdata`.ip,
		`switch_mac_rawdata`.mac,
		`switch_mac_rawdata`.port
		FROM
		`switch_mac_rawdata`
		WHERE `switch_mac_rawdata`.port > 1
		AND   `switch_mac_rawdata`.port < 49
		AND `switch_mac_rawdata`.mac <> 0");
		if($result->num_rows()){
			foreach($result->result() as $row){
				if (!isset($swdata[$row->ip][$row->port])) {
					if ($row->port > 1 || $row->port <= 48) {
						$this->db->query("INSERT INTO
						`switch_data` (
							`switch_data`.port,
							`switch_data`.mac,
							`switch_data`.ip
						) VALUES ( ?, ?, ? )", array($row->port, $row->mac, $row->ip));
					}
				} else {
					$this->db->query("UPDATE
					`switch_data`
					SET
					`switch_data`.mac  = ?
					WHERE `switch_data`.port = ?
					AND `switch_data`.ip   = ?", array( $row->mac, $row->port, $row->ip ));
				}
			}
		}
	}

	private function get_subnets_array($nets) {
		if(strlen($nets)){
			$subnets = explode("-", $nets);
		} else {
			print "</table><br>A list of subnetworks was not supplied..";
			$subnets = false;
		}
		return $subnets;
	}

	public function show_switches($nets = "") {
		$nets = ($this->input->post('sSwitchesRange') && strlen($this->input->post('sSwitchesRange'))) ? $this->input->post('sSwitchesRange') : $nets ;
		ob_start();
		print "<table border=1><tr>
			<td>IP</td>
			<td>MAC</td>
			<td>PORTS</td>
			<td>ROOM</td>
			<td>MODEL</td>
			<td>INFO</td>
			<td>SN</td>
		</tr>";
		$subnets = $this->get_subnets_array($nets);
		if (!$subnets) {
			exit;
		}
		foreach($subnets as $net){
			$this->discover_switches("192.168.".$net.".10", false);
			for ($a = 221; $a < 255; $a++) {
				$this->discover_switches("192.168.".$net.".".$a, false);
				flush();
				ob_flush();
			}
		}
		print "</table>";
		ob_end_flush();
	}

	public function get_host($host = "") {
		if (!strlen($host)) {
			if ($this->input->post("host") && strlen($this->input->post("host"))) {
				$host = $this->input->post("host");
			} else {
				$this->show_page(array(2));
			}
		}

		$output = array('<table class="table table-condensed table-bordered table-striped">
		<tr>
			<td>Host</td>
			<td>MAC</td>
			<td>Switch IP</td>
			<td>Switch Port</td>
		</tr>');
		$result = $this->db->query("SELECT DISTINCT
		switch_mac_rawdata.ip,
		switch_mac_rawdata.mac,
		switch_mac_rawdata.port,
		`switch_mac_rawdata`.`ts`
		FROM
		switch_mac_rawdata
		WHERE
		switch_mac_rawdata.mac IN (
			SELECT `hosts`.mac 
			FROM `hosts` 
			WHERE 
			`hosts`.hostname LIKE '".$host."%'
		)
		AND (switch_mac_rawdata.port <> 1)
		AND (switch_mac_rawdata.port < 49)
		ORDER BY 
		switch_mac_rawdata.mac");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<tr><td>'.$host.'</td><td>'.$row->mac.'</td><td><a target="_blank" href="http://'.$row->ip.'">'.$row->ip.'</a></td><td>'.$row->port.'</td></tr>';
				array_push($output, $string);
			}
		}
		$viewdata = array(
			'page'     => 2,
			'data'     => implode($output, "\n")."</table>",
			'pcsearch' => $host
		);
		$this->show_page($viewdata);
	}

	public function get_mac($mac = "") {
		if (!strlen($mac)) {
			if ($this->input->post("mac") && strlen($this->input->post("mac"))) {
				$mac = $this->input->post("mac");
			} else {
				$this->show_page(array(2));
			}
		}
		$output = array('<table class="table table-condensed table-bordered table-striped">
		<tr>
			<td>Host</td>
			<td>MAC</td>
			<td>Switch IP</td>
			<td>Switch Port</td>
		</tr>');
		$result = $this->db->query("SELECT DISTINCT
		switch_mac_rawdata.ip,
		switch_mac_rawdata.mac,
		switch_mac_rawdata.port,
		`switch_mac_rawdata`.`ts`
		FROM
		switch_mac_rawdata
		WHERE
		switch_mac_rawdata.mac LIKE '%".$mac."'
		AND (switch_mac_rawdata.port <> 1)
		AND (switch_mac_rawdata.port < 49)
		ORDER BY 
		switch_mac_rawdata.ip");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = '<tr><td> --- </td><td>'.$row->mac.'</td><td><a target="_blank" href="http://'.$row->ip.'">'.$row->ip.'</a></td><td>'.$row->port.'</td></tr>';
				array_push($output, $string);
			}
		}
		$viewdata = array(
			'page'      => 2,
			'data'      => implode($output, "\n")."</table>",
			'macsearch' => $mac
		);
		$this->show_page($viewdata);
	}

	public function clear_data($range, $sw = false) {
		if ($sw) {
			$this->db->query("DELETE
			FROM
			`switch_net`
			WHERE `switch_net`.`ip` LIKE '".$this->db->escape_like_str($range)."%'");
		}

		$this->db->query("DELETE
		FROM
		switch_mac_rawdata
		WHERE
		`switch_mac_rawdata`.`ip` LIKE '".$this->db->escape_like_str($range)."%'");
		
		print $range."  clear...<br><br>";
	}

	public function rescan_range($range) {
		$this->clear_data($range);
		$this->collect_macs($range);
	}

}

/* End of file network.php */
/* Location: ./application/controllers/network.php */