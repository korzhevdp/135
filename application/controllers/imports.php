<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Imports extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}
/*
	public function syncall() {
		$this->importusers();
		$this->importmatrix();
		$this->pidrestore();
		$this->load->helper("url");
		redirect("admin/users");
	}

	public function importusers() {
		$DB1 = $this->load->database('16', TRUE);
		$DB2 = $this->load->database('35', TRUE); 
		$export = array();
		$export1 = "INSERT INTO 
		`users` (
			users.id,
			users.name_f,
			users.name_i,
			users.name_o,
			users.dep_id,
			users.staff_id,
			users.office_id,
			users.mac,
			users.host,
			users.fired_date,
			users.fired,
			users.service,
			users.supervisor,
			users.login,
			users.last_update,
			users.air,
			users.bir,
			users.io,
			users.vrio,
			users.sman,
			users.phone,
			users.memo
		) VALUES\n";

		$result = $DB1->query("SELECT 
			users.id,
			users.name_f,
			users.name_i,
			users.name_o,
			users.id_dep AS dep_id,
			users.staff_id,
			users.office_id,
			users.mac,
			users.host,
			users.fired_date,
			IF(users.fired = 'N', 0, 1) AS fired,
			users.service,
			users.supervisor,
			users.login,
			users.last_update,
			if(users.air = 'N', 0, 1) AS air,
			IF(users.bir = 'N', 0, 1) AS bir,
			IF(users.io = 'N', 0, 1) AS io,
			IF(users.vrio = 'N', 0, 1) AS vrio,
			IF(users.sman = 'N', 0, 1) AS sman,
			users.phone,
			users.memo
			FROM
			users
			ORDER BY `users`.id");
		if($result->num_rows()){
			foreach($result->result() as $row){
			$string = "(
			'".$row->id."',
			'".$row->name_f."',
			'".$row->name_i."',
			'".$row->name_o."',
			'".$row->dep_id."',
			'".$row->staff_id."',
			'".$row->office_id."',
			'".$row->mac."',
			'".$row->host."',
			'".$row->fired_date."',
			'".$row->fired."',
			'".$row->service."',
			'".$row->supervisor."',
			'".$row->login."',
			'".$row->last_update."',
			'".$row->air."',
			'".$row->bir."',
			'".$row->io."',
			'".$row->vrio."',
			'".$row->sman."',
			'".$row->phone."',
			'".$row->memo."')";
				array_push($export, $string);
			}
		}
		
		$NEWTABLE=$export1.implode($export,",\n");

		$DDL35 = "CREATE TABLE `users` (
		`id` int(11) NOT NULL auto_increment,
		`name_f` tinytext NOT NULL,
		`name_i` tinytext NOT NULL,
		`name_o` tinytext NOT NULL,
		`dep_id` int(11) NOT NULL,
		`staff_id` int(11) NOT NULL,
		`office_id` int(11) NOT NULL,
		`mac` tinytext,
		`host` tinytext NOT NULL,
		`fired_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
		`memo` tinytext,
		`phone` tinytext NOT NULL,
		`service` int(11) NOT NULL,
		`supervisor` int(11) NOT NULL default '159',
		`login` tinytext NOT NULL,
		`last_update` timestamp NOT NULL default '0000-00-00 00:00:00',
		`io` tinyint(1) NOT NULL default '0',
		`vrio` tinyint(1) NOT NULL default '0',
		`sman` tinyint(1) NOT NULL default '0',
		`bir` tinyint(1) NOT NULL default '0',
		`air` tinyint(1) NOT NULL default '0',
		`fired` tinyint(1) NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `id_dep` (`dep_id`),
		KEY `staff_id` (`staff_id`),
		KEY `office_id` (`office_id`),
		KEY `service` (`service`),
		KEY `supervisor` (`supervisor`),
		FULLTEXT KEY `host` (`host`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=cp1251";

		$DB2->query("DROP table users");
		$DB2->query($DDL35);
		$DB2->query($NEWTABLE);
	}

	public function importmatrix() {
		$DB1 = $this->load->database('16', TRUE); // export connection
		$DB2 = $this->load->database('35', TRUE); // import connection

		$export = array();
		########################## export / import items ###################################
		#### 16 section
		$result = $DB1->query("SELECT 
		res_properties.id,
		res_properties.rid,
		res_properties.uid,
		res_properties.bid AS order_id,
		res_properties.user_doc_apply AS docapply,
		IF(res_properties.expired = 'Y', 1, 0) AS `exp`,
		IF(res_properties.deleted = 'Y', 1, 0) AS `del`,
		IF(res_properties.ok = 'Y', 1, 0) AS `ok`,
		res_properties.bid_applyer,
		res_properties.bitmask AS `matrix`
		FROM
		res_properties
		WHERE
		res_properties.pn NOT IN (12)");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = "('".$row->id."','".$row->rid."','".$row->uid."','".$row->order_id."','".$row->docapply."','".$row->exp."','".$row->del."','".$row->ok."','".$row->matrix."')";
				array_push($export,$string);
			}
		}
		$query_ri = "INSERT INTO resources_items (
			resources_items.id,
			resources_items.rid,
			resources_items.uid,
			resources_items.order_id,
			resources_items.okdate,
			resources_items.exp,
			resources_items.del,
			resources_items.ok,
			resources_items.matrix) VALUES\n".implode($export,",\n");
		//print nl2br($query);
		#### 35 section
		$DDL_ri = "CREATE TABLE `resources_items` (
		`id` int(11) NOT NULL auto_increment,
		`rid` int(11) default NULL,
		`uid` int(11) default NULL,
		`order_id` int(11) default NULL,
		`ok` tinyint(1) default '0',
		`okdate` timestamp NOT NULL default '0000-00-00 00:00:00',
		`exp` tinyint(1) default '0',
		`expdate` timestamp NOT NULL default '0000-00-00 00:00:00',
		`del` tinyint(1) default '0',
		`deldate` timestamp NOT NULL default '0000-00-00 00:00:00',
		`apply` int(11) default '0',
		`applydate` timestamp NOT NULL default '0000-00-00 00:00:00',
		`matrix` int(5) NOT NULL default '10001',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=8757 DEFAULT CHARSET=cp1251";
		$DB2->query("DROP table `resources_items`");
		$DB2->query($DDL_ri);
		$DB2->query($query_ri);

	########################## export / import orders ###################################
	#### 16 section
		$export = array();
		$result = $DB1->query("SELECT 
		`bids`.id,
		`bids`.user_doc_num AS `docnum`,
		`bids`.user_doc_date AS `docdate`,
		`bids`.`comment`
		FROM
		`bids`");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$string = "('".$row->id."','".$row->docnum."','".$row->docdate."','".$row->comment."')";
				array_push($export,$string);
			}
		}
		$query_oi = "INSERT INTO `resources_orders` (
			`resources_orders`.`id`,
			`resources_orders`.`docnum`,
			`resources_orders`.`docdate`,
			`resources_orders`.`comment`
		) VALUES\n".implode($export,",\n");
		
		#### 35 section
		$DB2->query("DROP TABLE `resources_orders`");
		$DB2->query("CREATE TABLE `resources_orders` (
		`id` int(11) NOT NULL auto_increment,
		`docnum` tinytext,
		`docdate` datetime default NULL,
		`comment` text,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=cp1251");
		$DB2->query($query_oi);
	}

	public function pidrestore() {
		########################## restoring pids ###################################
		#### 16 section
		$DB1 = $this->load->database('16', TRUE); // export connection
		$DB2 = $this->load->database('35', TRUE); // import connection
		$result = $DB1->query("SELECT 
		res_properties.pn AS pid,
		res_properties.pv AS pid_value,
		res_properties1.id,
		res_properties.rid
		FROM
		res_properties
		INNER JOIN res_properties res_properties1 ON (res_properties.bid = res_properties1.bid)
		WHERE
		LENGTH(res_properties.pv) > 0 AND
		(res_properties.pn IN (1,6,12)) AND 
		(res_properties1.pn IN (1,6))");
		$export1 = array();
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($export1[$row->id])) ? $export1[$row->id] = array() : "";
				(!isset($export1[$row->id][$row->rid])) ? $export1[$row->id][$row->rid] = array() : "" ;
				$export1[$row->id][$row->rid][$row->pid] = $row->pid_value;
			}
		}
		//print_r($export1);
		//exit;

		$result = $DB2->query("SELECT 
		resources_items.id,
		resources_items.order_id
		FROM
		resources_items
		where
		resources_items.rid in (100,101)");
		$export2 = array();
		if($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($export2[$row->id])) ? $export2[$row->id] = array() : "";
				$export2[$row->id] = $row->order_id;
			}
		}

		//print_r($export2);
		$DB2->query("TRUNCATE TABLE `resources_pid`");
		foreach($export1 as $order_id=>$val){
			foreach($val as $res_id => $pid){
				foreach($pid as $pid_id => $pid_value){
					$DB2->query("INSERT INTO resources_pid (item_id,pid,pid_value) VALUES (?,?,?)",array($order_id,$pid_id,$pid_value));
				}
			}
		}
	}
*/
	public function hostsallocation(){
		$hosts = array();
		$result=$this->db->query("SELECT 
		`users`.id,
		`hosts`.hostname
		FROM
		`users`
		INNER JOIN `hosts` ON (`users`.host  LIKE  CONCAT(`hosts`.hostname,'%'))");
		if($result->num_rows()){
			foreach($result->result() as $row){
				$hosts[$row->hostname] = $row->id;
			}
		}
		foreach($hosts as $key=>$val){
			$this->db->query("UPDATE hosts SET hosts.uid = ? WHERE (hosts.uid = 0 OR hosts.uid IS NULL) and hosts.hostname Like '".$key."%'", array($val));
		}
		$this->db->query("UPDATE `hosts` 
		SET `hosts`.`mac` = (
			SELECT 
			hash_items.networkcard_macaddress
			FROM
			hash_items
			WHERE
			(`hash_items`.`hostname` = `hosts`.`hostname`) AND
			(`hash_items`.`ts` <= `hosts`.`ts`)
			ORDER BY
			hash_items.ts DESC
			LIMIT 1
		)");

		$this->load->helper("url");
		redirect("integrity");
	}

	public function labellist(){
		$array = array(
			"100039310029202" => 111,
			"100039322382500" => 50,
			"100039447826301" => 5,
			"100039361603901" => 50,
			"100039435912023" => 70,
			"100039485852388" => 130,
			"101804000329832" => 8,
			"101804001355000" => 95,
			"100039436466218" => 12,
			"101804000185973" => 29
		);
		foreach($array as $key=>$val){
			for($i=0; $i<$val; $i++){
				print (integer) $key++."<br>\n";
			}
			print "<br><br>";
		}
	}
}

/* End of file imports.php */
/* Location: ./application/controllers/imports.php */