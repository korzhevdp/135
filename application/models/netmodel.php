<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Netmodel extends CI_Model {
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

	public function branches_get(){
		$result = $this->db->query("SELECT DISTINCT
		switch_connections.id,
		switch_connections.switch_ip,
		switch_connections.port,
		switch_connections.vlan,
		switch_connections.`type`,
		switch_connections.host_ip,
		switch_connections.host_name,
		switch_connections.location,
		switch_connections.`comment`,
		switch_connections.dir,
		switch_connections.patch_panel_id,
		switch_connections.active,
		IF(LENGTH(switch_connections.mac), switch_connections.mac, hosts.mac) AS mac
		FROM
		switch_connections
		LEFT OUTER JOIN `hosts` ON (switch_connections.host_name = `hosts`.hostname)
		GROUP BY switch_connections.host_name
		ORDER BY

		switch_connections.`type`,
		INET_ATON(switch_connections.`host_ip`),
		switch_connections.`port`");
		$output = array();
		$tableheader = '<table class="table table-condensed table-bordered table-striped">
		<tr>
			<th>Host</th>
			<th style="width:145px;">IP родителя</th>
			<th style="width:45px;">VLAN</th>
			<th>Линк</th>
			<th>Комментарий</th>
			<th>&nbsp;</th>
		</tr>';
		$output['server']  = array($tableheader);
		$output['switch']  = array($tableheader);
		$output['user']    = array($tableheader);
		$output['printer'] = array($tableheader);

		if($result->num_rows()){
			foreach($result->result() as $row){
				$dir    = ($row->dir) ? '<i class="icon-arrow-up"></i>' : '<i class="icon-arrow-down"></i>' ;
				$dirc   = ($row->dir) ? 'background-color:#ffff99;' : 'background-color:#66ffcc;' ;
				$act    = ($row->active) ? '' : ' class="danger"';
				$btn2   = ($row->active) 
					? '<button type="button" ref="'.$row->id.'" class="btn btn-mini btn-danger offSW"><i id="i'.$row->id.'" class="icon-remove icon-white"></i></button>' 
					: '<button type="button" ref="'.$row->id.'" class="btn btn-mini btn-warning onSW"><i id="i'.$row->id.'" class="icon-ok icon-white"></i></button>';
				$string = '<tr'.$act.' id="row'.$row->id.'">
					<td style="vertical-align:middle;">
						<input type="text" style="width:120px;" id="hm'.$row->id.'" value="'.$row->host_name.'" readonly>
						<input type="text" style="width:120px;" id="ip'.$row->id.'" value="'.$row->host_ip.'" readonly>
						<input type="text" style="width:120px;" id="mac'.$row->id.'" value="'.$row->mac.'" placeholder="MAC-адрес" readonly>, каб.
						<input type="text" style="width:55px;"  id="adr'.$row->id.'" value="'.$row->location.'" title="Адрес / кабинет" readonly>
					</td>
					<td id="psw'.$row->id.'"style="text-align:center;vertical-align:middle;">'.$row->switch_ip.'<strong>[:'.$row->port.']</strong></td>
					<td style="text-align:center;vertical-align:middle;">'.$row->vlan.'</td>
					<td style="text-align:center;vertical-align:middle;'.$dirc.'">'.$dir.'</td>
					<td id="cmn'.$row->id.'">'.$row->comment.'</td>
					<td style="text-align:center;vertical-align:middle;">
						<div class="btn-group">
						<button type="button" ref="'.$row->id.'" class="btn btn-mini btn-success editSW">Редактировать</button>
						'.$btn2.'
						</div>
					</td>
				</tr>';
				//print $row->type;
				array_push($output[$row->type], $string);
			}
			array_push($output['server'],  "</table>");
			array_push($output['switch'],  "</table>");
			array_push($output['user'],    "</table>");
			array_push($output['printer'], "</table>");
		}
		return $output;
	}

	public function unit_get(){
		$result = $this->db->query("SELECT
		switch_connections.id,
		switch_connections.switch_ip,
		switch_connections.port,
		switch_connections.vlan,
		switch_connections.`type`,
		switch_connections.host_ip,
		switch_connections.host_name,
		switch_connections.location,
		switch_connections.`comment`,
		switch_connections.dir,
		switch_connections.patch_panel_id,
		switch_connections.comment,
		IF(LENGTH(switch_connections.mac), switch_connections.mac, hosts.mac) AS mac
		FROM
		switch_connections
		LEFT OUTER JOIN `hosts` ON (switch_connections.host_name = `hosts`.hostname)
		where
		switch_connections.id = ?", array($this->input->post("unit")));
		if($result->num_rows()){
			$row = $result->row();
			print "unit = {
				host:    '".$row->host_name."',
				hostip:  '".$row->host_ip."',
				sip:     '".$row->switch_ip."',
				pport:   '".$row->port."',
				loc:     '".$row->location."',
				dir:     '".$row->dir."',
				vlan:    '".$row->vlan."',
				mac:     '".$row->mac."',
				comment: '".preg_replace("/[\n\r]/", "", $row->comment)."'
			};";
		}
		else{
			print "alert('Произошла ошибка при извлечении данных')";
		}
	}

}

/* End of file netmodel.php */
/* Location: ./application/models/netmodel.php */