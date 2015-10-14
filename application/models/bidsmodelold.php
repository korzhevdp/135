<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsmodelold extends CI_Model {

	function __construct(){
		parent::__construct();	// Call the Model constructor
		// ���������� ����� ��������� ������ - �� ��������� - word
		$this->session->set_userdata('genmode', "word");
		if($this->session->userdata("admin_id") == 1) {
			//�� ���� ���� ID == 1 (korzhevdp) - ����� pdf
			//$this->session->set_userdata('genmode', "pdf");
		}
	}

	public function user_data_get($userid){
		/*
		* user_data_get($userid). �������� ������ ������������. 
		* �������� int - ID ������������. 
		* ������������ ������ ������ ������������
		*/
		// �������� �������� ������ ������������
		$this->session->set_userdata("uid",$userid);
		$result = $this->db->query("SELECT 
		users.name_f,
		users.name_i,
		users.name_o,
		users.login,
		users.dep_id,
		users.staff_id,
		users.office_id,
		users.phone,
		`locations`.parent
		FROM
		users
		INNER JOIN `locations` ON (users.office_id = `locations`.id)
		WHERE
		(users.id = ?)", array($userid));
		if ($result->num_rows()){
			$return = $result->row_array();
		}

		// �������� ���������� ������ SELECT ��� �������������.
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id, IF(departments.id = ?,' selected',''),'>',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn",array($return['dep_id']));
		$output = array("<option value=0>�� �������</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		// ����������� ������������� � ������� ������� �������
		$return['dept'] = implode($output,"\n");

		// �������� ���������� ������� SELECT ��� ���������
		/*
		$result = $this->db->query("SELECT `locations`.`id`,
		`locations`.`address`,
		CASE
			WHEN ASCII(RIGHT(`address`, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`address`, '-'), 16, '0')
			ELSE LPAD(`address`, 16, '0') END AS `vsort`
		FROM `locations`
		WHERE `locations`.id <> 0 AND
		`locations`.`parent` = ?
		ORDER BY `locations`.`parent`, `vsort`",array($return['parent']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				$selected = ($row->id == $return['office_id']) ? 'selected="selected"' : '';
				array_push($output,'<option value='.$row->id.' '.$selected.'>'.$row->address.'</option>');
			}
			$return['location'][1] = implode($output,"\n");
		}
		*/
		/*
		$result = $this->db->query("SELECT 
		CONCAT(
			'<option value=',
			locations.id,
			IF(`locations`.id = ?, ' selected', '')
			,' >'
			,locations.address,
			'</option>') AS options
		FROM locations
		WHERE `locations`.parent = 0 AND
		`locations`.id <> 0
		ORDER BY `locations`.`address`", array($return['parent']));
		if ($result->num_rows()){
			$output=array();
			foreach($result->result() as $row){
				array_push($output, $row->options);
			}
			$return['location'][0] = implode($output, "\n");
		}

		$result = $this->db->query("SELECT
		CONCAT('<option value=',locations.id, IF(locations.id = ?,' selected',''),'>',locations.address,'</option>') AS options
		FROM
		locations
		WHERE
		locations.parent = ?
		ORDER BY 
		locations.address", array($return['office_id'], $return['parent']));
		$output = array("<option value=0>�� �������</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		// ����������� ��������� � ������� ������� �������
		$return['location'][1] = implode($output,"\n");

		if (!$return['parent']) {
			$return['location'][0] = (isset($return['location'][1])) ? $return['location'][1] : $return['location'][0];
			$return['location'][1] = "";
		}
		*/

		$input  = array();
		$output = array();
		$result = $this->db->query("SELECT `locations`.`id`,
		TRIM(CONCAT_WS(' ', locations1.address, `locations`.address)) AS address,
		`locations`.`parent`,
		CASE
			WHEN ASCII(RIGHT(`locations`.address, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`locations`.address, '-'), 24, '0')
			ELSE LPAD(`locations`.address, 24, '0') END AS `vsort`
		FROM `locations` locations1
		INNER JOIN `locations` ON (locations1.id = `locations`.parent)
		WHERE `locations`.id <> 0
		ORDER BY `locations`.`parent`, `vsort`");
		if ($result->num_rows()){
			foreach($result->result_array() as $row){
				if($row['parent'] == 0){
					$row['parent'] = $row['id'];
				}
				if(!isset($input[$row['parent']])){
					$input[$row['parent']] = array();
				}
				$selected = ($row['id'] == $return['office_id']) ? 'selected="selected"' : '';
				array_push($input[$row['parent']], '<option value='.$row['id'].' '.$selected.'>'.$row['address'].'</option>');
			}
			foreach($input as $key=>$val){
				array_push($output, implode($val, "\n"));
			}
			$return['location'] = implode($output,"\n");
		}

		
		// �������� ���������� ������� SELECT ��� ����������
		$result = $this->db->query("SELECT
		CONCAT(
		'<option value=',
		`staff`.`id`,
		IF(`staff`.`id` = ?, ' selected>','>'),
		`staff`.`staff`,
		'</option>') AS options
		FROM
		`staff`
		ORDER BY `staff`.`staff`",array($return['staff_id']));
		$output = array("<option value=0>�� �������</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output ,$row->options);
			}
		}
		// ����������� ��������� � ������� ������� �������
		$return['staff'] = implode($output,"\n");

		// �������� ���������� ������� �������������� ��������, 
		// ��� �������� �� � ��������� ������� ���������� � ������������ � ���������� GRP
		// � ������ ��������� ��������������� ������� � ��� �����������
		$result = $this->db->query("SELECT
		resources.id,
		resources.shortname,
		resources.cat,
		resources.grp,
		COUNT(resources_layers.id) AS con
		FROM
		resources
		LEFT OUTER JOIN resources_layers ON (resources.id = resources_layers.rid)
		WHERE
		(resources.active)
		GROUP BY
		resources.id
		ORDER BY
		resources.name");
		if ($result->num_rows()){
			$output = array();
			foreach($result->result() as $row){
				(!isset($output[$row->grp])) 
					? $output[$row->grp] = array() 
					: '';
				// ���������� �������� ����� ����������� (���������)
				$class = ($row->cat > 1) 
					? "btn-warning" 
					: "btn-info";
				// ���������� �������� ������ ���� (���������)
				$conf = ($row->cat > 1) 
					? "1" 
					: "0";
				// ���������� ����� ���� (���������)
				$string = '<li class="reslist btn '.$class.' span12" id="r_'.$row->id.'" grp="'.$row->grp.'" subs="'.$row->con.'" conf="'.$conf.'" style="margin: 2px 0px;"  title="������ ������� ���� '.(($row->cat > 1) 
					? '����������������' 
					: '').' ������ � ������ ���������">'.$row->shortname.'</li>';
				// �������� ����� � ��������������� ������ ����������
				array_push($output[$row->grp],$string);
			}
		}
		// ����������� ������ �������� � ������� ������� �������
		$return['rlist'] = $output;

		// ��������� �������� ���������������� ��������
		$result = $this->db->query("SELECT 
		DATE_FORMAT(resources_orders.docdate, '%e.%m.%Y') AS docdate,
		resources_items.ok,
		resources_items.id,
		resources_items.`exp`,
		resources_items.apply,
		resources_orders.docnum,
		resources.shortname,
		CONCAT_WS(' ',users1.name_f,users1.name_i,users1.name_o) AS sman,
		users1.phone
		FROM
		resources_orders
		INNER JOIN resources_items ON (resources_orders.id = resources_items.order_id)
		INNER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN `users` ON (resources_items.uid = `users`.id)
		INNER JOIN `users` users1 ON (`users`.service = users1.id)
		WHERE
		`resources_items`.`uid` = ? AND
		NOT `resources_items`.`del`",array($userid));
		$output = array();
		if ($result->num_rows()){
			// ��������� ������� ����� ��������
			array_push($output,'<table class="table table-striped table-hovered table-bordered" style="margin-left: 0px;width:100%">');
			array_push($output,'<tr>
				<th style="width:30px;">#</th>
				<th>�������������� ������</th>
				<th style="width:75px;">���� ������</th>
				<th style="width:75px;">����� ������</th>
				<th style="width:150px;">������</th>
				<th style="width:25px;text-align:center;vertical-align:middle;" title="�������� ����� ���� ������">
					<label for="checkAllPapers" style="cursor:pointer;"><i class="icon-file"></i></label>
					<input type="checkbox" id="checkAllPapers">
				</th>
			</tr>');
			// �����, ������ ������ �� ������ � ����� ������
			foreach($result->result() as $row){
				// ������� ���������� ������ ���
				$status = ($row->ok) 
					// ������� ���������� ������ ���������
					? ($row->apply) 
						? '���������&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="������ ��������� � ��������" style="cursor:pointer"></i>' 
						: '���������� � ��������&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="'.$row->sman.', ���.: '.$row->phone.'" style="cursor:pointer"></i>' 
					: '�� ������������&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-info-sign" title="���������� �������" style="cursor:pointer"></i>';
				// ������� ������ ������
				$status = ($row->exp) ? "��������" : $status;
				$string = '<tr>
					<td>'.(sizeof($output) - 1).'.</td>
					<td>'.$row->shortname.'</td>
					<td>'.$row->docdate.'</td>
					<td>'.$row->docnum.'</td>
					<td>'.$status.'</td>
					<td style="text-align:center;vertical-align:middle;"><input type="checkbox" class="paperChecker" ref="'.$row->id.'" title="�������� ����� ���� ������"></td>
				</tr>';
				//�������� ������ � �������
				array_push($output, $string);
			}
			//��������� �������
			array_push($output,'</table>');
		}else{
			//���� �� ������ ������� ���
			array_push($output,'<h3 class="muted">� ����� ������������ ��� �������� ������</h3>');
		}
		// ����������� ������ ������
		$return['ordersProcessed'] = implode($output,"\n");
		// ������
		return $return;
	}

	public function blank_data_get(){
		/*
		* ������� ������ �� ��������� (�����������)
		* ������������ ������ ������ ������������
		*/
		// ��������� ������ ������ ������
		$return = array(
			'name_f'		=> '',
			'name_i'		=> '',
			'name_o'		=> '',
			'phone'		=> '',
			'login'			=> '',
			'location'		=> array('','')
		);
		
		// �������� ������ �������������
		$result = $this->db->query("SELECT
		CONCAT('<option value=',departments.id,'>',departments.dn,'</option>') AS options
		FROM
		departments
		ORDER BY 
		departments.dn");
		$output = array("<option value=0>�� �������</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output,$row->options);
			}
		}
		$return['dept'] = implode($output,"\n");

		// �������� ������ ����������
		$result = $this->db->query("SELECT
		CONCAT('<option value=',`staff`.`id`,'>',`staff`.`staff`,'</option>') as options
		FROM
		`staff`
		ORDER BY `staff`.`staff`");
		$output = array("<option value=0>�� �������</option>");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				array_push($output ,$row->options);
			}
		}
		$return['staff'] = implode($output,"\n");

		// �������� ������ ���������
		$input  = array();
		$output = array();
		$result = $this->db->query("SELECT `locations`.`id`,
		TRIM(CONCAT_WS(' ', locations1.address, `locations`.address)) AS address,
		`locations`.`parent`,
		CASE
			WHEN ASCII(RIGHT(`locations`.address, 1)) BETWEEN 47 AND 58
			THEN LPAD(CONCAT(`locations`.address, '-'), 24, '0')
			ELSE LPAD(`locations`.address, 24, '0') END AS `vsort`
		FROM `locations` locations1
		INNER JOIN `locations` ON (locations1.id = `locations`.parent)
		WHERE `locations`.id <> 0
		ORDER BY `locations`.`parent`, `vsort`");
		if ($result->num_rows()){
			foreach($result->result_array() as $row){
				if($row['parent'] == 0){
					$row['parent'] = $row['id'];
				}
				if(!isset($input[$row['parent']])){
					$input[$row['parent']] = array();
				}
				array_push($input[$row['parent']], '<option value='.$row['id'].'>'.$row['address'].'</option>');
			}
			foreach($input as $key=>$val){
				array_push($output, implode($val, "\n"));
			}
			$return['location'] = implode($output,"\n");
		}

		// �������� ������ ��������
		$result = $this->db->query(" SELECT
		resources.id,
		resources.shortname,
		resources.cat,
		resources.grp,
		COUNT(resources_layers.id) AS con
		FROM
		resources
		LEFT OUTER JOIN resources_layers ON (resources.id = resources_layers.rid)
		WHERE
		(resources.active)
		GROUP BY
		resources.id
		ORDER BY
		resources.name");
		if ($result->num_rows()){
			$output = array();
			foreach($result->result() as $row){
				(!isset($output[$row->grp])) 
					? $output[$row->grp] = array() 
					: '';
				$class = ($row->cat > 1) 
					? "btn-warning" 
					: "btn-info";
				$conf = ($row->cat > 1) 
					? "1" 
					: "0";
				$string = '<li class="reslist btn '.$class.' span12" id="r_'.$row->id.'" grp="'.$row->grp.'" subs="'.$row->con.'" conf="'.$conf.'" style="margin: 2px 0px;"  title="������ ������� ������ � ������ ���������">'.$row->shortname.'</li>';
				array_push($output[$row->grp],$string);
			}
			$return['rlist'] = $output;
		}
		// ������ �������� ����, �������� :)
		$return['ordersProcessed']='<h3 class="muted">�� ������ ������������</h3>';

		return $return;
	}

	public function subproperties_get($res=0){
		/*
		* AJAX - ���������� HTML-�������� ���������
		* �������������� ���� (��������, ���� pid2, ������������� 
		* ����������� � ������� resources_layers)
		*/
		$output = array();
		$result=$this->db->query("SELECT 
		`resources_layers`.id,
		`resources_layers`.spn
		FROM
		`resources_layers`
		WHERE
		`resources_layers`.`rid` = ?", array($res));
		if($result->num_rows()){
			foreach($result->result() as $row){
				$markers = explode("(", $row->spn);
				(!isset($markers[1])) 
					? $markers[1] = '&nbsp;' 
					: "";
				$button = '<div class="span12" style="margin-bottom:5px;border-bottom:1px solid #EEEEEE;margin-left:0px;vertical-align:middle;">
				<button class="btn span4 subspad" type="button" value="'.$row->id.'" style="margin-bottom:5px;">'.$markers[0].'</button>
				<small class="span7">'.str_replace(")", "", $markers[1]).'</small>
				</div>';
				array_push($output,$button);
			}
		}
		print implode($output,"\n");
	}

	public function papers_get(){
			//$this->output->enable_profiler(TRUE);

		/*
		* ��������� ������ (���������). ��������� �� ���� ��������������� ������ �� _POST
		* ����� ������ HTML ��� ������� ������������ ������������ ���� �������� (Word)
		* ���� �� ���� HTML2PDF ��� �������������� ������������ ��������� PDF
		*/
		$genmode = $this->session->userdata('genmode');
		$papers = array();
		// ������ � ������� ��������� ���������� ��������: ������� � ����������������
		$res = (strlen($this->input->post("res")))
			? explode(",", $this->input->post("res"))
			: array();
		$confs = (strlen($this->input->post("confs")))
			? explode(",", $this->input->post("confs"))
			: array();

		/*
		* ��������� ������ �� ����� ���������
		*/
		
		// �����
		if(in_array(102, $res)) {
			array_push($papers, $this->domain_paper_get());
		}

		// ��������/����������� �����
		if(in_array(101, $res) || in_array(100, $res)) {
			array_push($papers, $this->internet_mail_paper_get($res));
		}

		// Wi-Fi
		if(in_array(274, $res)) {
			array_push($papers, $this->wf_paper_get($res));
		}
		/*
		//������ ����� ��
		if(in_array(281, $res)) {
			array_push($papers, $this->turv_paper_get($res));
		}
		*/
		// ������� ������ �������� �� ���������� ����� ������ 
		// �� �����, �������� � ����������� �����, � ����� Wi-Fi
		foreach($res as $key=>$val){
			if(in_array($res[$key], array(100, 101, 102, 274))){
				unset($res[$key]);
			}
		}
		/*
		* ���������������� ������� � ������� - ����������� � 2 �������
		* ����� � ��� �� �������
		*/
		$commons = (sizeof($res)) 
			? $this->common_res_paper_get($res, $papers) 
			: array();
		$confidents = (sizeof($confs)) 
			? $this->common_res_paper_get($confs, $papers,1) 
			: array();
		if($genmode == "pdf") {
			$this->return_PDF(array_merge($papers, $commons, $confidents));
		} else {
			$pagebreak = "<span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\";mso-fareast-font-family:\"Times New Roman\";mso-ansi-language:EN-US;mso-fareast-language:EN-US;mso-bidi-language:JI'><br clear=all style='page-break-before:always;mso-break-type:section-break'></span>";
			$outfile = implode(array_merge($papers, $commons, $confidents), $pagebreak);
			$filename = '������ �� �������������� ������� '.implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_").'.doc';
			//print $outfile;
			$this->load->helper('download');
			force_download($filename, $outfile);
		}
	}

	public function fill_template(){
		//
		// ���������� ������ ������ �������� �������
		//
		// �������� ������ ������� _POST ���������� �� ��������. 
		// ������������ ������������� ����������� ��������� ������ � ����
		$data = $this->input->post();
		$genmode = $this->session->userdata('genmode');

		// ����������� ���������� �������������.
		$deptlist = array();
		// ���� �� ���� ������� �������������
		// ���������� �������������� ����� � ����� (ID = 25)
		$real_d = ($data['dept']) 
			? $data['dept'] 
			: 25;
		// ��������� � ����������� �������������
		$result = $this->db->query("SELECT `departments`.id, `departments`.parent FROM `departments`");
		if($result->num_rows()){
			// � ������������� ������ ���������� ������ � ������������ �������������
			foreach($result->result() as $row){
				$deptlist[$row->id] = $row->parent;
			}
		}

		if(!in_array($real_d, array(10, 52))){
			// ��������� ����� �� �������� ���������
			while ($deptlist[$real_d]){
				if(!$deptlist[$deptlist[$real_d]]){ 
					// ���� �� ������������, ��� ID ������������ 
					// ������������� �������� ����� 0
					// ���������� ��������
					break;
				}
				// � ��� - ������ ����������� �������� �������� ID ��������
				$real_d = $deptlist[$real_d];
			}
		}
		

		// ���������� � ���� ������� ��������� �������� �����������
		// 10 - ��������
		// 52 - �������� - �� ��������� ����� �� �����.
		//
		$data['top_header'] = (in_array($real_d, array(10, 52))) 
			? "" 
			: "��P�� ��P��� �P����������";

		// ������� ������ �� ��������� ������������
		// $data['staff_id'] ������ �� _POST


		$result = $this->db->query("SELECT 
			CONCAT(', ', LCASE(`staff`)) AS `staff`
			FROM `staff`
			WHERE `id` = ?", array($data['staff_id']));
		if($result->num_rows()){
			$staff = $result->row_array();
			// ������� ��������
			$data = array_merge($data, $staff);
		}

		//
		// ��������� ������ �������� ������ ������
		// ��� ������������ �������������-��������
		// ��������� ������������ �������������-��������
		// ������ ��
		// �������� �������������
		// ���������
		// ����� �� ������������ ������ ������
		//

		$result = $this->db->query("SELECT
		CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1), '. ', users.name_f) AS fio,
		users.io,
		`staff`.staff AS `otv_dl`,
		UCASE(departments.`dn`) AS `org`,
		departments.`cred`,
		departments.`zakaz`
		FROM
		users
		INNER JOIN departments ON (users.`dep_id` = departments.id)
		INNER JOIN `staff` ON (departments.chief = `staff`.id)
		WHERE
		users.staff_id = departments.chief AND
		users.fired = 'N' AND
		departments.id = ?", array($real_d));
		if($result->num_rows()){
			$dept = $result->row_array();
			$dept['otv_dl'] = (!$dept['io']) ? $dept['otv_dl'] : "�.�. ".$dept['otv_dl'];
			// ������� ��������
			$data = array_merge($data, $dept);
		}
		$dept = array();
		##### ��������, ���������� ������ � ����������� � ����������� #####
		if(in_array($data['staff_id'], array(27, 40, 32))){
			if($data['staff_id'] == 27){
				$dept['fio']    = "";
				$dept['otv_dl'] = "����������� ���� ������";
				$dept['org']    = ""; //"����� ������ ������������";
				$dept['cred']   = "�� �������";
				$dept['zakaz']  = "����� ����� ����� �.������������. ����� 007.  15.03.2010";
			}
			if($data['staff_id'] == 40){
				// ���� ����� ������ ��� �� ����
				$dept['fio'] = $this->input->post("name_f")." ".strtoupper(substr($this->input->post("name_i"), 0, 1)).".".strtoupper(substr($this->input->post("name_o").".", 0 ,1));
				$dept['otv_dl'] = "�������� ����";
				$dept['org']    = ""; //"����� ������ ������������";
				$dept['cred']   = "�� �������";
				$dept['zakaz']  = "����� ����� ����� �.������������. ����� 007.  15.03.2010";
			}
			if($data['staff_id'] == 32){
				$dept['fio'] = $this->input->post("name_f")." ".strtoupper(substr($this->input->post("name_i"), 0, 1)).".".strtoupper(substr($this->input->post("name_o").".", 0 ,1));
				$dept['otv_dl'] = "�������� ����";
				$dept['org']    = ""; //"����� ������ ������������";
				$dept['cred']   = "�� �������";
				$dept['zakaz']  = "����� ����� ����� �.������������. ����� 007.  15.03.2010";
			}
		}
		$data = array_merge($data, $dept);

		// ������� ������ �� ��������, ������ �� �������������

		// ������ ���� � ���� ���� - �����������. ���������
		// ��������

		// $data['dept'] ������ �� _POST
		$result = $this->db->query("SELECT 
		admins.base_id as service
		FROM
		departments
		INNER JOIN admins ON (departments.service = admins.id)
		WHERE
		(departments.id = ?)", array($data['dept']));
		if($result->num_rows()){
			$serv = $result->row_array();
			// ������� ��������
			$data = array_merge($serv,$data);
		}
		
		// �������� ����������� ������� ����������
		// ���� ������ � ��������� (������� ������� �������)
		// ���� ���������� - ������ ������ - ������ �������
		// �� ���� ����, ������ ���� ������ ������ � ������� ������ ����� ���� �� �����
		$addrtag = (strlen($data['addr2']))
			? $data['addr2'] 
			: $data['addr1'];
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', locations1.address, `locations`.address) AS fulladdress
		FROM `locations`
		INNER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE locations.`id`= ?", array($addrtag));
		if($result->num_rows()){
			$addr = $result->row_array();
			// ������� ��������
			$data = array_merge($addr, $data);
		}


		//print $this->db->last_query();
		//exit;

		// ������� ������� �������� ���������
		$result = $this->db->query("SELECT `staff` AS `staffname` FROM `staff` WHERE `id` = ?", array($data['staff_id']));
		if($result->num_rows()){
			$staff = $result->row_array();
			$data = array_merge($staff,$data);
		}

		if( $data['addr1'] && $data['addr2'] ){
			$query = "SELECT `address` FROM `locations` WHERE `id` IN ('".$data['addr2']."','".$data['addr1']."')";
		}else{
			if (!isset($data['addr1']) || (!$data['addr1'])){
				$data['addr1'] = 1;
			}
			$query = "SELECT `address` FROM `locations` WHERE `id` = ".$data['addr1'];
		}

		$result = $this->db->query($query);
		if($result->num_rows()){
			$addr = $result->row_array();
			$data = array_merge($data, $addr);
		}

		(isset($data['ddate']) && strlen($data['ddate'])) 
			? "" 
			: $data['ddate'] = date("d.m.Y");
		(isset($data['dnum']) && strlen($data['dnum'])) 
			? ''
			: $data['dnum'] = '';
		
		// ���� - � ������ �������
		if($genmode == "pdf") {
			//
			// ��� "�����" PDF (HTML2PDF) ��������� ��������� � UTF-8 (��������� 
			// ��������� ��������� ������, ����� ������������ out-of-box-�������������)
			// ��������� (��� ����������� "��������� �����") ���� $data � UTF-8
			//
			$data['top_header'] = iconv('windows-1251', 'utf-8', $data['top_header']);
			$data['otv_dl'] = iconv('windows-1251', 'utf-8', $data['otv_dl']);
			$data['org'] = iconv('windows-1251', 'utf-8', $data['org']);
			$data['cred'] = iconv('windows-1251', 'utf-8', $data['cred']);
			$data['zakaz'] = iconv('windows-1251', 'utf-8', $data['zakaz']);
			$data['name_f'] = iconv('windows-1251', 'utf-8', $data['name_f']);
			$data['name_i'] = iconv('windows-1251', 'utf-8', $data['name_i']);
			$data['name_o'] = iconv('windows-1251', 'utf-8', $data['name_o']);
			$data['fio'] = iconv('windows-1251', 'utf-8', $data['fio']);
			$data['staff'] = iconv('windows-1251', 'utf-8', $data['staff']);
			$data['address'] = iconv('windows-1251', 'utf-8', $data['fulladdress']);
		}
		
		//print_r($data);
		//exit;

		return $data;
	}

	public function domain_paper_get(){

		$genmode = $this->session->userdata('genmode');
		$data = $this->input->post();									// ������ _POST � ����������


		$templatedata = $this->fill_template();							// �������� ������ ��� ������.
		if($data['uid']){ return "";}									// ��������� ���������� ���� ���� ����������, ��� ������������ ��� ���� � ����
		$userID = $this->new_user_insert($templatedata);			// ��������� ������ ������������ � ���� ������, �������� ������ ���� �������
		$orderID = $this->new_order_insert();					// ��������� � ���� ����� ������, �������� ������ ����� ������
		$resdata = array(array('uid' => $userID, 'order_id' => $orderID, 'rid' => 102));
		$this->resource_insert($resdata);							// ��������� �������� ������� � ��� ��������� � ������������ � ������
		if($genmode == "pdf") {
			// ��������� ������
			$string = $this->load->view('bids/2pdf/domain_p', $templatedata, true);
		}else{
			// ��������� ������
			$string = $this->load->view('bids/papers/domain_p', $templatedata, true);
		}

		$_POST['uid']= $userID;
		return $string;
	}

	public function internet_mail_paper_get($res){
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// �������� ������ ��� ������.
		$resdata = array();
		$subsdata = array();
		$addon = array();
		$addon['mailaction'] = "";
		$addon['inetaction'] = "";
		$addon['decision'] = array();
		$orderID = $this->new_order_insert();					// ��������� � ���� ����� ������, �������� ������ ����� ������
		if (in_array(100,$res)) {
			$addon['mailaction'] = "���������������� �������� ���� � ������� ".strtolower($this->input->post('email_addr'))."@arhcity.ru<BR>�� ������� ����������� ����� ����� ".$this->input->post('email_reason');
			array_push($addon['decision'], '���������������� ����� ����������� �����');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 100));
		}
		if (in_array(101,$res)) {
			$addon['inetaction'] = '������������ ������ � ���� "��������" '.$this->input->post('inet_reason');
			array_push($addon['decision'], '������������ ������ � ������������� ���� "��������"');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 101));
		}
		$addon['decision'] = implode($addon['decision'], ", ");
		$templatedata = array_merge($templatedata,$addon);								// ������� �������� �������������� ����� � ��������.

		if($genmode == "pdf") {
			// ��������� PDF - ��������� ����� � UTF-8
			$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['fulladdress'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['staffname'] = iconv('windows-1251', 'utf-8', $templatedata['staffname']);
			$templatedata['mailaction'] = iconv('windows-1251', 'utf-8', $templatedata['mailaction']);
			$templatedata['inetaction'] = iconv('windows-1251', 'utf-8', $templatedata['inetaction']);
			$templatedata['decision'] = iconv('windows-1251', 'utf-8', $templatedata['decision']);
			// ��������� ������
			$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
		}else{
			// ��������� ������
			$string = $this->load->view('bids/papers/inml', $templatedata, true);
		}

		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? AND
		`resources_items`.`rid` IN (100,101)", array($orderID));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if($row->rid == 100){
					array_push($subsdata, array('pid' => 1, 'pid_value' => $this->input->post('email_addr'), 'item_id' => $row->id));
					array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('inet_reason'), 'item_id' => $row->id));
				}
				if($row->rid == 101){
					array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('email_reason'), 'item_id' => $row->id));
				}
			}
		}
		$this->resource_subs_insert($subsdata);
		return $string;
	}

	public function wf_paper_get($res=274){
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// �������� ������ ��� ������.
		$resdata = array();
		$subsdata = array();
		$addon = array();
		$addon['mailaction'] = "";
		$addon['inetaction'] = "";
		$addon['decision'] = array();
		$orderID = $this->new_order_insert();					// ��������� � ���� ����� ������, �������� ������ ����� ������
		if (in_array(274,$res)) {
			$addon['inetaction'] = '������������ ������ � ��������-�������� ���������� ������������ ���� ��� '.$this->input->post('wf_reason');
			array_push($addon['decision'], '������������ ������ � ������������ ����');
			array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 274));
		}
		$addon['decision'] = implode($addon['decision'], ", ");
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? AND
		`resources_items`.`rid` IN (274)", array($orderID));
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('wf_reason'), 'item_id' => $row->id));
			}
		}
		$this->resource_subs_insert($subsdata);
		$templatedata = array_merge($templatedata,$addon);							// ������� �������� �������������� ����� � ��������.

		if($genmode == "pdf") {
			// ��������� ����� ��� PDF
			$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['fulladdress'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['staffname'] = iconv('windows-1251', 'utf-8', $templatedata['staffname']);
			$templatedata['mailaction'] = iconv('windows-1251', 'utf-8', $templatedata['mailaction']);
			$templatedata['inetaction'] = iconv('windows-1251', 'utf-8', $templatedata['inetaction']);
			$templatedata['decision'] = iconv('windows-1251', 'utf-8', $templatedata['decision']);
			// ��������� ������
			$string = $this->load->view('bids/2pdf/wf', $templatedata, true);
			//print_r($templatedata);
			//exit;
		}else{
			// ��������� ������
			$string = $this->load->view('bids/papers/wf', $templatedata, true);
		}
		return $string;
	}
	/*
	public function turv_paper_get(){
		$res=array(281);
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// �������� ������ ��� ������.
		$resdata = array();
		$subsdata = array();

		$orderID = $this->new_order_insert();					// ��������� � ���� ����� ������, �������� ������ ����� ������
		array_push($resdata, array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 281));
		$templatedata['res_container'] = '<tr>
		<td  style="font-size:11pt;text-align:center;">&nbsp;</td>
		<td  style="font-size:11pt;text-align:left;">������ ����� �������� �������</td>
		<td  style="font-size:11pt;text-align:center;width:17mm;"></td>
		<td  style="font-size:11pt;text-align:center;width:17mm;"></td>
		<td  style="font-size:11pt;text-align:center;width:17mm;">�</td>
		<td  style="font-size:11pt;text-align:center;width:17mm;"></td>
		</tr>
		<tr>
		<td  style="font-weight:bold;font-size:10px;" colspan=2>������� � �������������� �������</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		</tr>';
		$templatedata['owner_staff'] = "�.�. ���������� ����������";
		$templatedata['r_owner'] = "�.�. ���������";
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? AND
		`resources_items`.`rid` IN (281)", array($orderID));
		if($result->num_rows()){
			foreach($result->result() as $row){
				array_push($subsdata, array('pid' => 12, 'pid_value' => $this->input->post('wf_reason'), 'item_id' => $row->id));
			}
		}

		if($genmode == "pdf") {
			// ��������� ����� ��� PDF
			$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['fulladdress'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
			$templatedata['staffname'] = iconv('windows-1251', 'utf-8', $templatedata['staffname']);
			$templatedata['mailaction'] = iconv('windows-1251', 'utf-8', $templatedata['mailaction']);
			$templatedata['inetaction'] = iconv('windows-1251', 'utf-8', $templatedata['inetaction']);
			$templatedata['decision'] = iconv('windows-1251', 'utf-8', $templatedata['decision']);
			// ��������� ������
			$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
			//print_r($templatedata);
			//exit;
		}else{
			// ��������� ������
			$string = $this->load->view('bids/papers/turv', $templatedata, true);
		}
		return $string;
	}
	*/
	public function common_res_paper_get($res,$papers,$sogl=0){
		$genmode = $this->session->userdata('genmode');
		$templatedata = $this->fill_template();							// �������� ������ ��� ������.
		//$templatedata['fulladdress'] =  iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
		$templatedata['fulladdress'] = preg_replace("/[^�-��-�0-9\.\- ]/ism", '', $templatedata['fulladdress']);
		// �������� ������� �������
		$resdata = $this->common_papers_get($res);
		foreach($resdata['resources'] as $key=>$val){
			$templatedata['res_container'] = implode($val, "");
			$templatedata['res_container_sogl'] = implode($resdata['sogl'], "-<br>");		// ��������� ������������� � ����� ������ �������� ��� ����� ������������
			$templatedata['owner_staff'] = $resdata['owners'][$key]['owner_staff'];
			$templatedata['r_owner'] = $resdata['owners'][$key]['otv_dl_name'];
			$sogltype = ($key == "12") 
				? "14" 
				: "" ; // 12 - ������������� ������������� ��������� (����� -- ���). �������� ��� ����������� ����������� ����� ������������.

			if($genmode == "pdf") {
				$templatedata['address']     = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
				$templatedata['owner_staff'] = iconv('windows-1251', 'utf-8', $templatedata['owner_staff']);
				$templatedata['r_owner']     = iconv('windows-1251', 'utf-8', $templatedata['r_owner']);


				$page = ($sogl) 
					? $this->load->view('bids/2pdf/conf', $templatedata, true) 
					: $this->load->view('bids/2pdf/common', $templatedata, true);
				//print_r($templatedata);
				//exit;
				if(!sizeof($papers)){
					array_push($papers, $page);
				}else{
					array_push($papers, "<page>".$page."</page>");
				}
				($sogl) 
					? array_push($papers, "<page>".$this->load->view('bids/2pdf/sogl'.$sogltype, $templatedata, true)."</page>") 
					: "";
				($sogl) 
					? array_push($papers, "<page>".$this->load->view('bids/2pdf/ordermemo', $templatedata, true)."</page>") 
					: "";
			}else{
				$page = ($sogl) 
					? $this->load->view('bids/papers/conf', $templatedata, true) 
					: $this->load->view('bids/papers/common', $templatedata, true);
				array_push($papers,$page);									// ��������� ������
				$sogltype = ($key == "12")									// ��������� ����������� - ���������
					? "14" 
					: "" ;  // 12 - ������������� ������������� ��������� (����� -- ���). �������� ��� ����������� ����������� ����� ������������.
				($sogl) 
					? array_push($papers,$this->load->view('bids/papers/sogl'.$sogltype, $templatedata, true)) 
					: "";
				($sogl) 
					? array_push($papers,$this->load->view('bids/papers/ordermemo', $templatedata, true)) 
					: "";
			}
		}
		if($genmode == "pdf") {
			//print_r($papers);
			//exit;
		}
		return $papers;
	}

	public function common_papers_get($res){
		$genmode = $this->session->userdata('genmode');
		$layers = array();
		$subsdata = array();
		$subs = $this->input->post("subs");
		if($subs && strlen($subs)){
			$result = $this->db->query("SELECT 
			`resources_layers`.rid,
			`resources_layers`.spn
			FROM
			`resources_layers`
			WHERE
			`resources_layers`.`id` IN (".$this->input->post("subs").")");
			if ($result->num_rows()){
				foreach($result->result() as $row){
					(!isset($layers[$row->rid])) ? $layers[$row->rid] = array() : "";
					array_push($layers[$row->rid],$row->spn);
				}
			}
		}

		$result = $this->db->query("SELECT DISTINCT
		resources.cat,
		resources.id,
		resources.name,
		resources.bitmask,
		resources.`shortname`,
		resources.owner,
		`staff`.staff AS `owner_staff`,
		CONCAT(LEFT(`users`.`name_i`,1),'.',LEFT(`users`.`name_o`,1),'. ',`users`.name_f) as `otv_dl_name`
		FROM
		`departments`
		INNER JOIN `staff` ON (`departments`.chief = `staff`.id)
		INNER JOIN resources ON (`departments`.id = resources.owner)
		INNER JOIN `users` ON (`departments`.chief = `users`.staff_id)
		AND (`users`.dep_id = resources.owner)
		WHERE
		resources.id IN (".implode($res,",").") 
		AND NOT `users`.fired");
		$process = array();
		$owners = array();
		$layers1 = array();
		$layers2 = array();
		$layers3 = array();
		$layers4 = array();
		$sogl = array();

		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($process[$row->cat])) ? $process[$row->cat] = array() : "";
				(!isset($process[$row->cat][$row->owner])) ? $process[$row->cat][$row->owner] = array() : "";
				(!isset($owners[$row->owner])) ? $owners[$row->owner] = array() : "";
				$owners[$row->owner]['otv_dl_name']=$row->otv_dl_name;
				$owners[$row->owner]['owner_staff']=$row->owner_staff;
				
				if($genmode == "pdf") {
					//$layers=explode("\n", $layers[$row->itemid]);
					//print "<br>".$row->itemid;
					//print_r($layers);
					if(isset($layers[$row->id])){
						//exit;
						$layers1 = array_slice($layers[$row->id], 0, 12);
						$layers2 = array_slice($layers[$row->id], 12);
						$layers3 = array_slice($layers[$row->id], 0, 20);
						$layers4 = array_slice($layers[$row->id], 20);
					}
					$string = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers1)) ? "<br>- ".implode($layers1, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers2) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers2)) ? "<br><br>- ".implode($layers2, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>������� � �������������� �������</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
					$string2 = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers3)) ? "<br>- ".implode($layers3, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers4) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers4)) ? "<br><br>- ".implode($layers4, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>������� � �������������� �������</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
					$string = iconv('windows-1251', 'utf-8', $string);
					$string2 = iconv('windows-1251', 'utf-8', $string2);
					$sogl[$row->owner] = $string2;
				}else{
					$string = '<tr>
					<td  style="font-size:11pt;text-align:center;">&nbsp;</td>
					<td  style="font-size:11pt;text-align:left;"><b>'.$row->name.'</b>'.((isset($layers[$row->id]) && strlen($layers[$row->id])) 
						? "<br>- ".implode($layers[$row->id], "<br>- ") 
						: "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "11111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10011") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10001") ? "x" : "").'</td>
					</tr>
					<tr>
					<td style="font-weight:bold;font-size:10px;" colspan=2>������� � �������������� �������</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					</tr>';
				}
				$process[$row->cat][$row->owner][$row->id] = $string;
			}
		}

		$resdata = array();
		$output=array();
		$output['owners']=$owners;
		$output['resources']=array();
		$output['sogl']=$sogl;
		$orderIDs = array();

		foreach ($process as $cat=>$owner){
			// ��������� � ���� ����� ������, �������� ������ ����� ������
			//print_r($owner);
			//return false;
			$orderID = $this->new_order_insert();
			array_push($orderIDs, $orderID);
			foreach($owner as $ownerid => $out){
				(!isset($output['resources'][$ownerid])) ? $output['resources'][$ownerid] = array() : "";
				array_push($output['resources'][$ownerid], implode($out, ""));
				foreach($out as $rid => $label){
					array_push($resdata, array('uid' => $this->input->post('uid'), 'order_id' => $orderID, 'rid' => $rid));
				}
			}
		}
		$this->resource_insert($resdata);
		$result = $this->db->query("SELECT 
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`uid` = ? AND
		`resources_items`.order_id IN (".implode($orderIDs,",").")", array($this->input->post('uid')));
		if($result->num_rows()){
			foreach($result->result() as $row){
				if(isset($layers[$row->rid])){
					array_push($subsdata, "(2, '".implode($layers[$row->rid], "\n")."',".$row->id.")");
				}
			}
		}
		if(sizeof($subsdata)){
			$this->db->query("INSERT INTO 
			resources_pid (
				resources_pid.`pid`,
				resources_pid.`pid_value`,
				resources_pid.`item_id`
			) VALUES ".implode($subsdata,",\n"));
		}
		return $output;
	}

	########################################
	## inserting helper FX
	public function new_order_insert(){
		$this->db->query("INSERT INTO resources_orders (resources_orders.docdate) VALUES (NOW())");
		$orderID = $this->db->insert_id();
		return $orderID;
	}

	public function new_user_insert($data){
		$genmode = $this->session->userdata('genmode');
		if($genmode == "pdf") {
			$name_f = iconv("utf-8", "windows-1251", $data['name_f']);
			$name_i = iconv("utf-8", "windows-1251", $data['name_i']);
			$name_o = iconv("utf-8", "windows-1251", $data['name_o']);
		}else{
			$name_f = $data['name_f'];
			$name_i = $data['name_i'];
			$name_o = $data['name_o'];
		}
		
		$result = $this->db->query("INSERT INTO users (
		`name_f`,
		`name_i`,
		`name_o`,
		`host`,
		`login`,
		`phone`,
		`service`,
		`supervisor`,
		`dep_id`,
		`office_id`,
		`staff_id`
		) VALUES ( ?,?,?,?,?,?,?,?,?,?,? )",
		array(
			$name_f,
			$name_i,
			$name_o,
			$data['login'],
			$data['login'],
			$data['phone'],
			$data['service'],
			$data['service'],
			$data['dept'],
			(strlen($data['addr2']) || $data['addr2']) ? $data['addr2'] : $data['addr1'],
			$data['staff_id']
		));
		
		$userID = $this->db->insert_id();
		$link = mysql_connect('192.168.1.9', 'dbreferer', 'dbreferer');
		mysql_select_db('file_exchange', $link);

		//mysql_set_charset('cp1251', $link);
		mysql_query("SET NAMES cp1251", $link);
		//print implode(array($name_f, $name_i, $name_o), " 11 ");
		$result = mysql_query("SELECT `fios`.`fio` FROM `fios` WHERE TRIM(`fios`.`fio`) = '".implode(array($name_f, $name_i, $name_o), " ")."'", $link);
		if(!mysql_num_rows($result)){
			mysql_query("INSERT INTO fios (fios.`fio`) VALUES (TRIM('".implode(array($name_f, $name_i, $name_o), " ")."'))", $link);
		}
		
		mysql_close($link);
		return $userID;
	}

	public function resource_insert($data){
		$output = array();
		foreach($data as $key => $val){
			$string = "('".$val['uid']."','".$val['order_id']."','".$val['rid']."')";
			array_push($output,$string);
		}
		$this->db->query("INSERT INTO `resources_items` (
			`resources_items`.uid,
			`resources_items`.order_id,
			`resources_items`.rid) VALUES ".implode($output,",\n"));
	}

	public function resource_subs_insert($subs){
		$this->db->insert_batch("resources_pid", $subs);
	}
	## END inserting helper FX
	########################################

	public function reget_orders($orders){
		$genmode = $this->session->userdata('genmode');
		//$this->output->enable_profiler(TRUE);
		$orders = explode(",", $this->input->post("resources"));	//�������� ������ ��������
		if(!sizeof($orders)){
			print "������������ ������";
			return false; // ����� ��� ������ ������
		}

		# ������������� ��������
		$resids = array();
		$res = array();
		$confs = array();
		$papers = array();
		$layers = array();

		# ��������� 
		$result = $this->db->query("SELECT 
		resources_items.rid,
		resources.cat,
		resources.name,
		resources_pid.pid,
		CASE
		WHEN resources_pid.pid = 1 THEN CONCAT(resources_pid.pid_value,'@arhcity.ru')
		WHEN resources_pid.pid = 6 THEN INET_NTOA(resources_pid.pid_value)
		WHEN resources_pid.pid NOT IN (1,6) THEN resources_pid.pid_value
		END AS pid_value,
		resources_items.id,
		`resources_orders`.docnum,
		DATE_FORMAT(`resources_orders`.docdate,'%d.%m.%Y') as docdate
		FROM
		resources_items
		INNER JOIN resources ON (resources_items.rid = resources.id)
		LEFT OUTER JOIN resources_pid ON (resources_items.id = resources_pid.item_id)
		LEFT OUTER JOIN `resources_orders` ON (resources_items.order_id = `resources_orders`.id)
		WHERE `resources_items`.`id` IN (".implode($orders,",").")");
		if($result->num_rows()){
			foreach($result->result_array() as $row){
				if(!isset($res[$row['id']])){
					$res[$row['id']] = $row;
					$res[$row['id']]['pid'.$row['pid']] = $row['pid_value'];
				}else{
					if($row['pid'] == 6 && !isset($row['pid'.$row['pid']])){
						$res[$row['id']]['pid'.$row['pid']] = $row['pid_value'];
					}
				}
			}
		}

		//print $this->db->last_query();

		foreach($orders as $val){
			$_POST['ddate'] = $res[$val]['docdate'];
			$_POST['dnum'] = $res[$val]['docnum'];
			// �������� ������ ��� ������.
			$templatedata = $this->fill_template();
			if($res[$val]['rid'] == 102){
				// ������ �� �����
				if($genmode == "pdf") {
					// ��������� ������
					$string = $this->load->view('bids/2pdf/domain_p', $templatedata, true);
				}else{
					// ��������� ������
					$string = $this->load->view('bids/papers/domain_p', $templatedata, true);
				}
				array_push($papers,$string);
				// ����������� ���������, ��������� �� ��������� ������
				continue;
			}

			if($res[$val]['rid'] == 274){
				// ������ �� Wi-Fi. 
				// �������������
				$addon = array();
				$addon['mailaction'] = "";
				$addon['inetaction'] = "";
				$addon['decision'] = array();
				// $res[$val]['pid12'] - pid12 ��� ����� ������� ������, �� ����, ��������� ����������� ��� ����� �������.
				$reason = (isset($res[$val]['pid12'])) 
					? $res[$val]['pid12'] 
					: '<i>���������� � ���� ����������� �� ����������</i>';
				$addon['inetaction'] = '������������ ������ � ��������-�������� ���������� ������������ ���� ��� '.$reason;
				array_push($addon['decision'], '������������ ������ � ��������-�������� ���������� ������������ ����');

				$addon['decision'] = implode($addon['decision'], ", ");
				// ������� �������� �������������� ����� � ��������.
				$templatedata = array_merge($templatedata,$addon);
				if($genmode == "pdf") {
					// ��������� ����� ��� PDF
					$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
					$templatedata['fulladdress'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
					$templatedata['staffname'] = iconv('windows-1251', 'utf-8', $templatedata['staffname']);
					$templatedata['mailaction'] = iconv('windows-1251', 'utf-8', $templatedata['mailaction']);
					$templatedata['inetaction'] = iconv('windows-1251', 'utf-8', $templatedata['inetaction']);
					$templatedata['decision'] = iconv('windows-1251', 'utf-8', $templatedata['decision']);
					// ��������� ������
					$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
					//print_r($templatedata);
					//exit;
				}else{
					// ��������� ������
					$string = $this->load->view('bids/papers/wf', $templatedata, true);
				}
				array_push($papers,$string);
				continue; // ������ ������ ��� ������, ��������� �� ��������� ������
			}
		}

		// Internet/Email routine
		// �������������
		$addon = array();
		$addon['mailaction'] = "";
		$addon['inetaction'] = "";
		$addon['decision'] = array();
		$gen = 0; // ���� ������������� �������� ���������. 
		// ������������ ������ continue, ����� ����� ����������� ������ ����� 1 ������ � ����� � ������� ��������� (��������, �������)
		$sogl = array();
		foreach($orders as $val){
			if($res[$val]['rid'] == 101 || $res[$val]['rid'] == 100){
				// ������� ����������� pid12 - ����������� �����������
				$reason = (isset($res[$val]['pid12'])) 
					? $res[$val]['pid12'] 
					: '( ���������� � ���� ����������� �� ���������� )';
				if ($res[$val]['rid'] == 100) {
					$addon['mailaction'] = "���������������� �������� ���� � ������� ".$res[$val]['pid1']."<BR>�� ������� ����������� ����� ����� ��� ".$reason;
					array_push($addon['decision'], '���������������� ����� ����������� �����');
					$gen = 1;
				}
				if ($res[$val]['rid'] == 101) {
					$addon['inetaction'] = '������������ ������ � ���� "��������" ��� '.$reason;
					array_push($addon['decision'], '������������ ������ � ������������� ���� "��������"');
					$gen = 1;
				}
			}
		}
		
		// � ������ ����������� ����� ��������� ���������
		if($gen){
			$addon['decision'] = implode($addon['decision'], ", ");
			// ������� �������� �������������� ����� � ��������.
			$templatedata = array_merge($templatedata,$addon);
 			if($genmode == "pdf") {
				// ��������� PDF - ��������� ����� � UTF-8
				$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
				$templatedata['fulladdress'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
				$templatedata['staffname'] = iconv('windows-1251', 'utf-8', $templatedata['staffname']);
				$templatedata['mailaction'] = iconv('windows-1251', 'utf-8', $templatedata['mailaction']);
				$templatedata['inetaction'] = iconv('windows-1251', 'utf-8', $templatedata['inetaction']);
				$templatedata['decision'] = iconv('windows-1251', 'utf-8', $templatedata['decision']);
				// ��������� ������
				$string = $this->load->view('bids/2pdf/inml', $templatedata, true);
				// print_r($templatedata);
				// exit;
			}else{
				// ��������� ������
				$string = $this->load->view('bids/papers/inml', $templatedata, true);
			}
			
			array_push($papers,$string);
			// continue; // ������ ������ ��� ������, ��������� �� ��������� ������
		}
		#######################################################################################
		
		#### ������� ?
		/*
		$result = $this->db->query("SELECT 
		`resources_pid`.pid,
		`resources_pid`.pid_value,
		`resources_pid`.item_id
		FROM
		`resources_pid`
		WHERE
		`resources_pid`.`item_id` IN (".implode(array_keys($res),",").")");
		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($layers[$row->item_id])) ? $layers[$row->item_id] = array() : "";
				//(!isset($layers[$row->item_id][$row->pid])) ? $layers[$row->item_id][$row->pid] = array() : "";
				$layers[$row->item_id][$row->pid] = $row->pid_value;
			}
			if($genmode == "pdf") {
				//print_r($res);
				//exit;
			}
		}
		*/

		$result = $this->db->query("SELECT DISTINCT
		resources.cat,
		resources.id,
		resources_items.id AS itemid,
		resources.name,
		resources.bitmask,
		resources.shortname,
		resources.owner,
		staff.staff AS owner_staff,
		CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1), '. ', users.name_f) AS otv_dl_name
		FROM
		departments
		INNER JOIN staff ON (departments.chief = staff.id)
		INNER JOIN resources ON (departments.id = resources.owner)
		INNER JOIN users ON (departments.chief = users.staff_id)
		AND (resources.owner = users.dep_id)
		INNER JOIN resources_items ON (resources.id = resources_items.rid)
		WHERE
		resources_items.id IN (".implode(array_keys($res),",").") 
		AND resources_items.rid NOT IN (100,101,102,274)
		AND NOT `users`.fired");
		// �������-����������
		$process = array();
		$owners = array();
		$layers1 = array();
		$layers2 = array();
		$layers3 = array();
		$layers4 = array();

		if ($result->num_rows()){
			foreach($result->result() as $row){
				(!isset($process[$row->cat])) ? $process[$row->cat] = array() : "";
				(!isset($process[$row->cat][$row->owner])) ? $process[$row->cat][$row->owner] = array() : "";
				(!isset($owners[$row->owner])) ? $owners[$row->owner] = array() : "";
				$owners[$row->owner]['otv_dl_name']=$row->otv_dl_name;
				$owners[$row->owner]['owner_staff']=$row->owner_staff;
				$string = "";
				$string2 = "";
				if($genmode == "pdf") {
					//$layers=explode("\n", $layers[$row->itemid]);
					//print "<br>".$row->itemid;
					//print_r($layers);

					if(isset($res[$row->itemid]['pid2'])){
						$layerssrc = (explode("\n", $res[$row->itemid]['pid2']));
						//exit;
						$layers1 = array_slice($layerssrc, 0, 12);
						$layers2 = array_slice($layerssrc, 12);
						$layers3 = array_slice($layerssrc, 0, 20);
						$layers4 = array_slice($layerssrc, 20);
					}
					$string = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers1)) ? "<br>- ".implode($layers1, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers2) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers2)) ? "<br><br>- ".implode($layers2, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>������� � �������������� �������</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
					$string2 = '<table style="margin-left:10mm;margin-top:0mm;border-spacing:0mm;border-collapse:collapse;table-layout:fixed;">
					<tr>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						<b>'.$row->name.'</b><br><br><span style="font-size:10pt;">'.( (sizeof($layers3)) ? "<br>- ".implode($layers3, "<br>- ") : "").'</span>
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>'
					.(sizeof($layers4) 
						? '<tr style="margin-top:10mm;font-size:10pt;">
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:5mm;">&nbsp;</td>
					<td style="border:1px solid black;font-size:11pt;text-align:left;width:82.1mm;">
						'.((sizeof($layers4)) ? "<br><br>- ".implode($layers4, "<br>- ") : "").'
					</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.9mm;">'.(($row->bitmask == "11111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10111") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:19.8mm;">'.(($row->bitmask == "10011") ? "x" : "&nbsp;").'</td>
					<td style="border:1px solid black;font-size:11pt;text-align:center;width:18.9mm;">'.(($row->bitmask == "10001") ? "x" : "&nbsp;").'</td>
					</tr>' 
						: "").
					'<tr>
					<td style="font-weight:bold;font-size:10px;border:1px solid black;" colspan=2>������� � �������������� �������</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					<td style="border:1px solid black;">&nbsp;</td>
					</tr></table>';
					$string = iconv('windows-1251', 'utf-8', $string);
					$string2 = iconv('windows-1251', 'utf-8', $string2);
				}else{
					$string = '<tr>
					<td  style="font-size:11pt;text-align:center;">&nbsp;</td>
					<td  style="font-size:11pt;text-align:left;"><b>'.$row->name.'</b>'.((isset($res[$row->itemid]['pid2']) && strlen($res[$row->itemid]['pid2'])) 
						? "<br>- ".implode(explode("\n",$res[$row->itemid]['pid2']),"<br>- ") 
						: "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "11111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10111") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10011") ? "x" : "").'</td>
					<td  style="font-size:11pt;text-align:center;width:17mm;">'.(($row->bitmask == "10001") ? "x" : "").'</td>
					</tr>
					<tr>
					<td  style="font-weight:bold;font-size:10px;" colspan=2>������� � �������������� �������</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					<td >&nbsp;</td>
					</tr>';
				}
				$process[$row->cat][$row->owner][$row->id] = $string;
				$sogl[$row->owner] = $string2;
			}
		}
		foreach($process as $cat => $all_owners){
			foreach($all_owners as $owner_id => $resource_list){
				$templatedata = $this->fill_template();
				$templatedata['res_container'] = implode($resource_list);		// ������ �������� ��� ����� ������
				$templatedata['res_container_sogl'] = $sogl[$owner_id];		// ��������� ������������� � ����� ������ �������� ��� ����� ������������
				$arrayids = array_keys($resource_list);
				$templatedata['owner_staff'] = $owners[$owner_id]['owner_staff'];
				$templatedata['r_owner'] = $owners[$owner_id]['otv_dl_name'];
				$templatedata['layers3'] = $layers3;
				$templatedata['layers4'] = $layers4;
				$sogltype = ($owner_id == "12") 
					? "14" 
					: "" ; // 12 - ������������� ������������� ��������� (����� -- ���). �������� ��� ����������� ����������� ����� ������������.
				
				if($genmode == "pdf") {
					//$templatedata['address'] = iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
					$templatedata['owner_staff'] = iconv('windows-1251', 'utf-8', $templatedata['owner_staff']);
					$templatedata['r_owner'] =       iconv('windows-1251', 'utf-8', $templatedata['r_owner']);
					$templatedata['fulladdress'] =  iconv('windows-1251', 'utf-8', $templatedata['fulladdress']);
					$page = ($cat > 1) ? $this->load->view('bids/2pdf/conf', $templatedata, true) : $this->load->view('bids/2pdf/common', $templatedata, true);
					//print_r($templatedata);
					//exit;
					if(!sizeof($papers)){
						array_push($papers,"<page>".$page."</page>");
					}else{
						array_push($papers,"<page>".$page."</page>");
					}
					($cat > 1) 
						? array_push($papers, "<page>".$this->load->view('bids/2pdf/sogl'.$sogltype, $templatedata, true)."</page>") 
						: "";
					($cat > 1) 
						? array_push($papers, "<page>".$this->load->view('bids/2pdf/ordermemo', $templatedata, true)."</page>") 
						: "";
				}else{
					$page = ($cat > 1) 
						? $this->load->view('bids/papers/conf', $templatedata, true) 
						: $this->load->view('bids/papers/common', $templatedata, true);
					array_push($papers,$page);
					($cat > 1) 
						? array_push($papers,$this->load->view('bids/papers/sogl'.$sogltype, $templatedata, true)) 
						: "";
					($cat > 1) 
						? array_push($papers,$this->load->view('bids/papers/ordermemo', $templatedata, true)) 
						: "";
				}
			}
		}
		
		if($genmode == "pdf") {
			// �������� ��� ���������������
			$this->return_PDF($papers);
		}else{
			//$this->output->enable_profiler(TRUE);
			$pagebreak = "<span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\";mso-fareast-font-family:\"Times New Roman\";mso-ansi-language:EN-US;mso-fareast-language:EN-US;mso-bidi-language:JI'><br clear=all style='page-break-before:always;mso-break-type:section-break'></span>";
			$outfile = implode($papers,$pagebreak);
			$filename = '��������������_������_��_�������������� �������_'.implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_").'.doc';
			$this->load->helper('download');
			force_download($filename, $outfile);
		}
		
		//		header('Content-Description: File Transfer');
		//		header("Content-Type: application/msword");
		//		header("Content-Type: application/force-download;");
		//		header("Content-Disposition: inline; filename=\"��������������_������_��_�������������� �������_".implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_")).".doc\"";
	}

	public function return_PDF($papers){
		/*
		* ���������� ����� ������������ PDF
		* � ������ PDF-��������
		*/
		// ��� �����
		$filename = iconv( 'windows-1251', 'utf-8', '��������������_������_��_�������������� �������_'.implode(array($this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o')),"_").'.pdf' );
		// ��������� � �������� ��������� (��� ����������� ����������)
		$head = $this->load->view("bids/2pdf/chainhead", array(), true);
		$aft = $this->load->view("bids/2pdf/chainaft", array(), true);
		// ��������� ��������� ������.
		$outfile = $head.implode($papers, "").$aft;
		// ����� �������
		ob_start();
		$content = ob_get_clean();
		// ����������� ������. ����������� ���������� �� ������!
		require_once('/var/www/html/users-r4/2pdf/html2pdf.class.php');
		// ������� ��������� �������
		try{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->setTestTdInOnePage(false);
			$html2pdf->setDefaultFont('Times');
			$html2pdf->writeHTML($outfile, isset($_GET['vuehtml']));
			$html2pdf->Output($filename);
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

}

/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */