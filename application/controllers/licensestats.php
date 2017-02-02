<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Licensestats extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('admin_id')) {
			$this->load->helper('url');
			redirect('login/index/auth');
		}
		(!$this->session->userdata('filter')) ? $this->session->set_userdata('filter', '') : "";
		(!$this->session->userdata('uid'))    ? $this->session->set_userdata('uid', 1)     : "";
		$this->load->model('usefulmodel');
		$this->load->model('licensemodel');
		if ($this->session->userdata("admin_id") === 1) {
			//$this->output->enable_profiler(TRUE);
		}
	}

	public function index() {
		$this->statistics();
	}

	public function statistics($lid=0){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => ($lid) ? $this->licensemodel->stat_get($lid) : $this->licensemodel->full_stat_get(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function servers(){
		$result  = $this->db->query("SELECT DISTINCT
		ak_licenses.id AS akid,
		ak_licenses.hostname,
		ak_licenses.product_name,
		inv_po_licenses_sets.id AS setid,
		inv_po_licenses.id,
		inv_po_licenses.number,
		inv_po_licenses_items.master,
		inv_po_types.name,
		inv_po_types.serverwise
		FROM
		ak_licenses
		LEFT OUTER JOIN inv_po_licenses_items ON (ak_licenses.product_key = inv_po_licenses_items.value)
		LEFT OUTER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		LEFT OUTER JOIN inv_po_licenses_sets ON (inv_po_licenses_items.set_id = inv_po_licenses_sets.id)
		LEFT OUTER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		WHERE
		(ak_licenses.active) 
		AND `ak_licenses`.`hostname` IN (SELECT `hosts`.`hostname` FROM `hosts` WHERE `hosts`.`server`)
		GROUP BY
		ak_licenses.id,
		inv_po_types.name,
		inv_po_licenses_sets.id,
		inv_po_licenses_items.master,
		inv_po_types.serverwise
		ORDER BY
		ak_licenses.product_name,
		inv_po_licenses.number");
		if ( $result->num_rows() ) {
			$input   = $this->getServerLicenseUnits($result);
			$setlist = $this->collectSets($result);
			$input2  = $this->getSetMasterSW($setlist);
		}
		$data = array(
			'serverLicenses' => $this->licensemodel->serverlicenses_get(),
			'modals'		 => $this->load->view('license/modals', array('dep_id' => 77, 'userid' => 592), true),
			'licenseList'	 => $this->getServerSWTable($input, $input2)
		);
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('license/servlicenses', $data, true),
			'footer'  => $this->load->view('page_footer', array(), true).'<script type="text/javascript" src="/jscript/lsmc.js"></script>'
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	private function getServerLicenseUnits($result) {
		$input = array();
		foreach ( $result->result_array() as $row ) {
			$input[$row['akid']] = $row;
		}
		return $input;
	}

	private function collectSets($result) {
		foreach ( $result->result_array() as $row ) {
			if ( strlen($row['setid']) ) {
				if ( !isset($setlist[$row['setid']]) ) {
					$setlist[$row['setid']] = 0;
				}
				$setlist[$row['setid']] += 1;
			}
		}
		return $setlist;
	}

	private function getSetMasterSW($setlist) {
		$output = array();
		$result = $this->db->query('SELECT
		`inv_po_types`.name,
		`inv_po_licenses_items`.`set_id`
		FROM
		`inv_po_licenses_items`
		INNER JOIN `inv_po_licenses_sets` ON (`inv_po_licenses_items`.set_id = `inv_po_licenses_sets`.id)
		LEFT OUTER JOIN `inv_po_types` ON (`inv_po_licenses_items`.type_id = `inv_po_types`.id)
		WHERE `inv_po_licenses_items`.`master`
		AND `inv_po_licenses_items`.`set_id` IN ('.implode(array_keys($setlist), ",").')');
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$output[$row->set_id] = $row->name;
			}
		}
		return $output;
	}

	private function getServerSWTable($input, $input2) {
		$output = array();
		foreach ($input as $licenseData) {
			if (strlen($licenseData['setid']) && isset($input2[$licenseData['setid']])) {
				$licenseData['from']   = $input2[$licenseData['setid']];
			} else {
				$licenseData['lmode']  = "";
				$licenseData['master'] = 1;
			}
			$licenseData['annot'] = ($licenseData['master'])
				? $licenseData['product_name']
				: $licenseData['product_name'].' <small class="muted">даунгрейд c '.$licenseData['from']."</small>";
			array_push($output, $this->load->view("license/listitems/serverlistitem", $licenseData, true));
		}
		return implode($output, "\n");
	}

	public function contents(){
		$output = "";
		$result = $this->db->query("SELECT
		CONCAT_WS(' - ', `inv_po_licensiars`.name, inv_po_licenses.number) as l_number,
		inv_po_licenses.id as lid,
		inv_po_licenses_sets.`max`,
		inv_po_types.name,
		inv_po_licenses_sets.id,
		inv_po_licenses_items.value,
		inv_po_licenses_items.master
		FROM
		inv_po_licenses_sets
		LEFT OUTER JOIN inv_po_licenses ON (inv_po_licenses_sets.license_id = inv_po_licenses.id)
		RIGHT OUTER JOIN inv_po_licenses_items ON (inv_po_licenses_sets.id = inv_po_licenses_items.set_id)
		LEFT OUTER JOIN inv_po_types ON (inv_po_licenses_items.type_id = inv_po_types.id)
		LEFT OUTER JOIN `inv_po_licensiars` ON (inv_po_licenses.licensiar_id = `inv_po_licensiars`.id)
		WHERE
		inv_po_licenses_items.master
		AND inv_po_types.serverwise
		AND NOT (inv_po_licenses_sets.deleted)
		AND inv_po_licenses_items.`act`
		ORDER BY
		inv_po_licenses_items.master DESC, l_number");
		if ($result->num_rows()) {
			$input  = $this->getServerLicenseContentData($result);
			$output = $this->getServerLicenseContentTable($input);
		}
		$act  = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('license/serverlicensescontent', array('list' => $output), true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	private function getServerLicenseContentData($result) {
		$input = array();
		foreach ($result->result() as $row) {
			if (!isset($input[$row->lid])) {
				$input[$row->lid] = array('desc' => $row->l_number);
			}
			if (!isset($input[$row->lid][$row->id])) {
				$input[$row->lid][$row->id] = array();
			}
			$setItemData = array(
				'name'   => $row->name,
				'count'  => $row->max,
				'pk'     => $row->value,
				'master' => $row->master
			);
			array_push($input[$row->lid][$row->id], $setItemData);
		}
		return $input;
	}

	private function getServerLicenseContentTable($input) {
		$output = array();
		$sid    = 1;
		foreach ($input as $lid => $sets) {
			$string = '<tr>
				<td colspan=4><a href="/licensestats/statistics/'.$lid.'">'.$sets['desc'].'</a></td>
			</tr>';
			array_push($output, $string);
			foreach ($sets as $sid => $items) {
				if ($sid !== 'desc'){
					$rowspan = sizeof($items);
					$rws = 0;
					foreach($items as $item){
						$style    = ($sid % 2) ? 'style = "background-color:#f3f3f3"' : 'style = "background-color:#e0e0FF"';
						$style2   = ($item['master']) ? "" : ' class="muted"';
						$spanning = ($rws) ? "" : "<td rowspan=".$rowspan." ".$style.">&nbsp;</td>";
						$string   = "<tr>
						".$spanning."
						<td ".$style2.">".$item['name']."</td>
						<td ".$style2.">".$item['count']."</td>
						<td ".$style2.">".$item['pk']."</td>
						</tr>";
						array_push($output, $string);
						$rws = 1;
					}
					$sid++;
				}
			}
		}
		return implode($output, "\n");
	}

	public function usage(){
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->licensemodel->po_usage_get(),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function user($userid = 0, $dep_id = 0){
		//$this->output->enable_profiler(TRUE);
		$userid = ($this->input->post('userid')) ? $this->input->post('userid') : $userid;
		$dep_id = ($this->input->post('dep_id')) ? $this->input->post('dep_id') : $dep_id;
		if(!$userid && $dep_id){
			$this->load->helper("url");
			redirect("licenses/dept/".$dep_id);
		}
		$data = $this->licensemodel->getHostlist($userid, $dep_id); // возвращает массив с 2 подмассивами
		$data['licenselist'] = $this->licensemodel->userlicenses_get($userid);
		$data['userid'] = $userid;
		$data['dep_id'] = $dep_id;
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('license/license', $data, true),
			'footer'  => $this->load->view('page_footer', '', true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

	public function dept($dep_id = 1){
		//$this->output->enable_profiler(TRUE);
		//$this->input->post('userid');
		$userid = ($this->input->post('userid')) ? $this->input->post('userid') : 0;
		$dep_id = ($this->input->post('dep_id')) ? $this->input->post('dep_id') : $dep_id ;
		//return false;
		if($userid){
			$this->load->helper("url");
			redirect("licenses/user/0/".$userid);
		}
		$data = $this->licensemodel->getHostlist(0, $dep_id); // возвращает массив с 2 подмассивами
		$data['licenselist'] = $this->licensemodel->deptlicenses_get($dep_id);
		$data['userid'] = 0;
		$data['dep_id'] = $dep_id;
		$act = array(
			'menu'    => $this->load->view('menu/navigation', $this->usefulmodel->getNavMenuData(), true),
			'content' => $this->load->view('license/license', $data, true),
			'footer'  => $this->load->view('page_footer', array(), true)
		);
		$this->usefulmodel->no_cache();
		$this->load->view('page_container', $act);
	}

}
/* End of file licensestats.php */
/* Location: ./application/controllers/licensestats.php */