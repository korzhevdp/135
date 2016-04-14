<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uvmrmodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function imdsp_get(){
		$output = array();
		$output['ie'] = array();
		$output['dsp'] = array();
		$output['coll'] = array();
		$accum = array();
		$users = array();
		$confres= array();
		$result = $this->db->query("SELECT 
		`resources`.id,
		`resources`.shortname
		FROM
		`resources`
		WHERE `resources`.cat > 1");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$confres[$row->id] = $row->shortname;
			}
		}

		$result = $this->db->query("SELECT DISTINCT
		resources_items.uid,
		resources_items.rid,
		IF(resources_items.rid = 100, 1, 0) AS email,
		IF(resources_items.rid = 101, 1, 0) AS inet,
		IF(resources.cat > 1, 1, 0) AS dsp
		FROM
		resources
		INNER JOIN resources_items ON (resources.id = resources_items.rid)
		INNER JOIN `users` ON (resources_items.uid = `users`.id)
		WHERE
		((resources_items.rid IN (100,101)) 
		OR (resources.cat > 1)) 
		AND NOT `users`.fired 
		AND `resources_items`.`ok`
		AND NOT `resources_items`.`del`
		AND NOT `resources_items`.`exp`
		ORDER by `users`.`name_f`, `users`.`name_i`, users.`name_o`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($accum[$row->uid]))    ? $accum[$row->uid]    = array( 0, 0 ) : "";
				(!isset($accum[$row->uid][2])) ? $accum[$row->uid][2] = array() : "";
				if(!$accum[$row->uid][0]){
					$accum[$row->uid][0] = ($row->email);
				}
				if(!$accum[$row->uid][1]){
					$accum[$row->uid][1] = ($row->inet);
				}
				($row->dsp) ? array_push($accum[$row->uid][2], $confres[$row->rid]) : "" ;
			}
		}

		$result = $this->db->query("SELECT
		`users`.`id`,
		`departments`.dn,
		CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o) as fio
		FROM
		`users`
		INNER JOIN `departments` ON (`users`.dep_id = `departments`.id)
		WHERE `users`.`id` IN (".implode(array_keys($accum),",").")");
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($users[$row->id])) ? $users[$row->id] = array() : "";
				$users[$row->id][0] = $row->fio;
				$users[$row->id][1] = $row->dn;
				$users[$row->id][2] = $row->id;
			}
		}

		foreach($accum as $key => $val){
			$counter = (sizeof($val[2]) && ($val[1] || ($val[0]))) 
				? sizeof($output['coll']) + 1 
				: (sizeof($val[2]) 
					? sizeof($output['dsp']) + 1 
					: sizeof($output['ie'])  + 1 );
			$string = '<tr class="'.(
				(sizeof($val[2]) && ($val[1] || ($val[0])))
					? 'error' 
					: (sizeof($val[2]) ? 'warning' : 'success' )
			).' searchable">
				<td >'.$counter.'</td>
				<td class="searchable_fio"><a href="uvmr/passport/'.$users[$key][2].'">'.$users[$key][0].'</a></td>
				<td>'.$users[$key][1].'</td>
				<td><center>'.(($val[0]) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>').'</center></td>
				<td><center>'.(($val[1]) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>').'</center></td>
				<td>'.((sizeof($val[2])) ? implode($val[2],"<br>\n") : '<center><i class="icon-remove"></i></center>').'</td>
			</tr>';
			(sizeof($val[2]) && ($val[1] || $val[0])) 
				? array_push($output['coll'],$string) 
				: (sizeof($val[2]) 
					? array_push($output['dsp'],$string) 
					: array_push($output['ie'],$string) );
			//array_push($output,$string);
		}

		return $output;
	}

	public function passport_get($euid = 0){
		$user = ($this->input->post('userSelector')) ? $this->input->post('userSelector') : $euid;
		$input = array();
		$output = array("id" => 0);
		if(!$user){
			return array ('content' => "Ничего не найдено", 'id' => 0, 'filter' => urldecode($this->session->userdata("filter")));
		}
		$result = $this->db->query("SELECT 
		CONCAT_WS(' ',`users`.name_f,`users`.name_i,`users`.name_o, if(`users`.fired, '<span class=\"btn btn-danger btn-large\">Уволена</span>','')) AS fio,
		`departments`.dn,
		`staff`.staff,
		CONCAT_WS(' ', locations1.address, `locations`.address) AS `address`
		FROM
		`users`
		INNER JOIN `departments` ON (`users`.dep_id = `departments`.id)
		INNER JOIN `staff` ON (`users`.staff_id = `staff`.id)
		INNER JOIN `locations` ON (`users`.office_id = `locations`.id)
		INNER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE `users`.`id` = ?", array($user));
		if($result->num_rows()){
			$output = $result->row_array();
		}

		$output['id'] = $user;
		$output['software'] = array();
		$result = $this->db->query("SELECT DISTINCT
		inv_po_installed_software.po_name,
		inv_po_installed_software.hostname,
		DATE_FORMAT(inv_po_installed_software.scandate, '%d.%m.%Y') AS scandate
		FROM
		`hosts`
		RIGHT OUTER JOIN inv_po_installed_software ON (`hosts`.hostname = inv_po_installed_software.hostname)
		LEFT OUTER JOIN hash_items ON (`hosts`.hostname = hash_items.hostname)
		WHERE
		(`hosts`.uid = ?) AND 
		(hash_items.active)
		ORDER BY
		inv_po_installed_software.hostname,
		inv_po_installed_software.po_name,
		inv_po_installed_software.scandate", array($user));
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($input[$row->hostname])) ? $input[$row->hostname]=array() : "";
				array_push($input[$row->hostname], '<li>'.$row->po_name.'<span class="muted pull-right"> ['.$row->scandate.']</span></li>');
			}
			foreach($input as $key=>$val){
				$string = '<h4>ПК: '.$key.'</h4><ul>'.implode($val, "\n").'</ul>';
				array_push($output['software'],$string);
			}
		}
		$output['software'] = implode($output['software'],"\n");
		
		$output['resources'] = array();
		$result = $this->db->query("SELECT 
		`resources`.shortname,
		`resources`.cat,
		`resources`.id
		FROM
		`resources`
		INNER JOIN `resources_items` ON (`resources`.id = `resources_items`.rid)
		WHERE
		`resources_items`.`uid` = ?
		AND `resources_items`.`ok`
		AND NOT `resources_items`.`del`
		AND NOT `resources_items`.`exp`", array($user));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$class = array();
				($row->cat > 1)							? array_push($class, 'btn-danger')  : "" ;
				($row->id == 103)						? array_push($class, 'btn-inverse') : "" ;
				(in_array($row->id, array(100,101)))	? array_push($class, 'btn-warning') : "" ;
				(!sizeof($class))						? array_push($class, 'btn-success') : "" ;
				array_push($output['resources'], '<li>'.$row->shortname.'<span style="margin-right:5px;" class="'.implode($class,"").' pull-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></li>');
			}
		}
		$output['resources'] = '<ul>'.implode($output['resources'], "\n").'</ul>';

		$output['os'] = array();
		$input = array();
		$result = $this->db->query("SELECT
		ak_licenses.product_name,
		`hosts`.hostname,
		ak_licenses.product_key
		FROM
		ak_licenses
		LEFT OUTER JOIN `hosts` ON (ak_licenses.hostname = `hosts`.hostname)
		WHERE
		(`hosts`.uid = ?)
		AND (ak_licenses.active)
		GROUP BY
		ak_licenses.product_name,
		`hosts`.hostname,
		ak_licenses.product_key
		HAVING
		(MAX(ak_licenses.scandate))", array($user));
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($input[$row->hostname])) ? $input[$row->hostname] = array() : "";
				array_push($input[$row->hostname], '<li>'.$row->product_name.'<span style="margin-right:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></li>');
			}
		}
		$output['pcs'] = array();
		foreach ($input as $key=>$val){
			$string = '<h4>'.$key.'</h4><ul>'.implode($val,"\n").'</ul>';
			array_push($output['pcs'], $key);
			array_push($output['os'], $string);
		}
		$output['os'] = '<ul>'.implode($output['os'], "\n").'</ul>';
		$output['pcs'] = implode($output['pcs'], ", ");
		return $output;
	}
}
/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */