<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bidsfactorymodel extends CI_Model {

	private $dbwrite       = true;										// запись в базу  вкл/выкл
	private $expose        = false;										// профайлер      вкл/выкл
	private $wrap_to_word  = true;										// обёртка в Word вкл/выкл
	private $wDirectory    = 'bids/papers2/';							// директория с формулярами заявок
	private $UID           = 0;											// текущий пользовательский идентификатор
	private $resItems      = array();
	private $resList       = array();
	private $resNamesList  = array();
	private $bidsData      = false;
	private $itemID        = 0;											// текущий идентификатор заявки
	private $primary       = true;										// режим генерации заявок
	private $mailBox       = '';
	private $defaultReason = ' для выполнения служебных обязанностей';
	private $decisions     = array(
		100 => 'зарегистрировать адрес электронной почты',
		101 => 'предоставить доступ к международной сети "Интернет"',
		286 => 'предоставить доступ в указанные группы Единой системы идентификации и аутентификации',
		274 => 'предоставить доступ к беспроводной сети'
	);
	private $pidData       = array();
	private $fnFIO         = '';										// ФИО текущего пользователя для имени файла
	private $resData       = array();
	private $selfHeader    = array(10, 52, 81, 82);						// список идентификаторов
	private $assertedHead  = 25;
	private $confAuthority = 2587;//id подписанта документов ДСП. e.g. 2333 - Шапошников; 2587 - Евменов

	function __construct() {
		parent::__construct();						// Call the Model constructor
	}

	###########################################
	## Getters
	private function getUserMailboxDB( $userID ) {
		$email = "";
		$result = $this->db->query("SELECT
		CONCAT(resources_pid.pid_value, '@arhcity.ru') as email
		FROM
		resources_pid
		RIGHT OUTER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
		WHERE
		(resources_pid.pid = 1)
		AND (resources_items.uid = ?)
		AND (`resources_items`.`ok`)
		AND NOT (`resources_items`.`del`)
		AND NOT (`resources_items`.`exp`)
		ORDER BY resources_items.id DESC
		LIMIT 1", array($userID));
		if ($result->num_rows()) {
			$row   = $result->row();
			$email = $row->email;
		}
		return $email;
	}

	private function getUserProperties( $userID, $mode = 0 ) {
		$result = $this->db->query("SELECT 
		users.name_f,
		users.name_i,
		users.name_o,
		users.login,
		users.dep_id AS dept,
		users.staff_id AS staff,
		users.office_id AS office,
		users.phone,
		`locations`.parent
		FROM
		users
		INNER JOIN `locations` ON (users.office_id = `locations`.id)
		WHERE
		(users.id = ?)", array($userID));
		if ( $result->num_rows() ) {
			if ( !$mode ) {
				return $result->row_array();
			}
			if ( $mode ) {
				$row = $result->row();
				return "udt = {
					name_f : '".$row->name_f."',
					name_i : '".$row->name_i."',
					name_o : '".$row->name_o."',
					login  : '".$row->login."',
					dept   : '".$row->dep_id."',
					staff  : '".$row->staff_id."',
					bldg   : '".$row->parent."',
					office : '".$row->office_id."',
					phone  : '".$row->phone."',
					email  : '".$this->getUserMailboxDB($userid)."'
				}";
			}
		}
		return false;
	}

	private function getDeptList() {
		$output = array();
		$result = $this->db->query("SELECT `departments`.id, `departments`.parent FROM `departments`");
		if ($result->num_rows()) {
			// в ассоциативный массив помещаются данные о родительском подразделении
			foreach ($result->result() as $row) {
				$output[$row->id] = $row->parent;
			}
		}
		return $output;
	}

	private function getHeadDept( $deptID, $deptlist ) {
		// пробегаем вверх по иерархии структуры подразделений
		if( !in_array($deptID, $this->selfHeader) ) {
			while ($deptlist[$deptID]) {
				if ( !$deptlist[$deptlist[$deptID]] ) {
					// пока не обнаруживаем, что ID родительского подразделения ВНЕЗАПНО равен 0, прекращаем пробежку
					return $deptID;
				}
				// а так - присваиваем пойнтеру значение ID родителя
				$deptID = $deptlist[$deptID];
			}
		}
		return $deptID;
	}

	private function getBidProperties( $deptID ) {
		$output = array (
			"otv_dl" => "У подразделения не указан руководитель",
			"fio"    => "У подразделения не указан руководитель",
			"cred"   => "Реквизиты отсутствуют",
			"zakaz"  => "Реквизиты отсутствуют",
			"org"    => "Не удалось найти все данные о подразделении",
			"io"     => 0
		);
		$result = $this->db->query("SELECT
		CONCAT(LEFT(users.name_i, 1), '.', LEFT(users.name_o, 1), '. ', users.name_f) AS fio,
		users.io,
		`staff`.staff AS `otv_dl`,
		UCASE(departments.`dn_blank`) AS `org`,
		departments.`cred`,
		departments.`zakaz`
		FROM
		users
		INNER JOIN departments ON (users.`dep_id` = departments.id)
		INNER JOIN `staff` ON (departments.chief = `staff`.id)
		WHERE
		users.staff_id = departments.chief
		AND NOT users.fired
		AND departments.id = ?", array($deptID));
		if ($result->num_rows()) {
			$output = $result->row_array();
			$output['otv_dl'] = (!$output['io']) ? $output['otv_dl'] : "И.о. ".$output['otv_dl'];
		}
		return $output;
	}

	private function getTemplateData() {
		// заполнение бланка заявки типовыми данными
		// получаем данные массива _POST переданные со страницы
		// пользователю предоставлена возможность исправить данные о себе
		

		//$outdata = (is_array($outdata)) ? $outdata : $this->input->post();
		$outdata = $this->bidsData[$this->itemID];
		$mod = 1;
		if ($outdata && isset($outdata['sname']) && isset($outdata['name']) && isset($outdata['fname'])) {
			$mod == 2;
			$outdata['name_f'] = $outdata['sname'];
			$outdata['name_i'] = $outdata['name'];
			$outdata['name_o'] = $outdata['fname'];
		}
		// определение начального подразделения.
		$deptlist = $this->getDeptList();
		// если не было выбрано подразделение
		// ассертивно предполагается мэрия в целом (ID = 25)
		$deptID = ($outdata['dept']) ? $outdata['dept'] : $this->assertedHead;
		// переходим к определению подразделения
		$deptID = $this->getHeadDept($deptID, $deptlist);

		// дописываем в поле признак заголовка корневой организации
		// 10 - Горсовет
		// 52 - Избирком - им заголовок мэрии не нужен.
		$outdata['top_header'] = (in_array($deptID, $this->selfHeader))
			? ""
			: 'АДМИНИСТРАЦИЯ МУНИЦИПАЛЬНОГО ОБРАЗОВАНИЯ "ГОРОД АРХАНГЕЛЬСК"';
		
		// выборка данных по должности пользователя
		// простая выборка названия должности // нормализую staffname и staff в дальнейшем одно из полей исключить
		if ( !isset($outdata['staffname']) || !strlen($outdata['staffname']) ) {
			$result = $this->db->query("SELECT
			LOWER(`staff`.`staff`) AS `staffname`
			FROM `staff` 
			WHERE `id` = ?", array($outdata['staff']));
			if ($result->num_rows()) {
				$staff = $result->row_array();
				$outdata['staffname'] = $staff['staffname'];
			}
		}
		
		if ( !isset($outdata['service']) ) {
			$result = $this->db->query("SELECT
			admins.base_id as service
			FROM
			departments
			INNER JOIN admins ON (departments.service = admins.id)
			WHERE
			(departments.id = ?)", array($outdata['dept']));
			if ($result->num_rows()) {
				$serv = $result->row_array();
				$outdata['service'] = $serv['service'];
			}
		}
		// данные полей заявки
		$outdata = array_merge($outdata, $this->getBidProperties($deptID));
		if (in_array($outdata['staff'], array(11, 27, 32, 40))) {
			##### Врезка с помощниками и советниками #####
			$overriddenDeptData = $this->overrideTemplateData($outdata['staff']);
			$outdata = array_merge($outdata, $overriddenDeptData);
		}

		// выбираем действующий признак размещения
		// либо здание с кабинетом (признак второго порядка)
		// либо обобщённый - только здание - первый порядок
		
		if ( !isset($outdata['addr2']) && isset($outdata['office2'])) {
			$outdata['addr2'] = $outdata['office2']; // нормализуем поля форм
		}
		if ( !isset($outdata['addr1']) ) {
			$outdata['addr1'] = $outdata['office']; // нормализуем поля форм
		}

		$addrtag = (isset($outdata['addr2']) && strlen($outdata['addr2']))
			? $outdata['addr2']
			: $outdata['addr1'];
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', locations1.address, `locations`.address) AS fulladdress
		FROM `locations`
		INNER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE locations.`id`= ?", array($addrtag));
		if ($result->num_rows()) {
			$addr = $result->row_array();
			$outdata['fulladdress'] = $addr['fulladdress'];
		}
		
		if ( $addrtag ) {
			$query = "SELECT `address` FROM `locations` WHERE `id` IN ('".$addrtag."')";
		} else {
			if (!$addrtag) {
				$outdata['office'] = 1;
			}
			$query = "SELECT `address` FROM `locations` WHERE `id` = ".$addrtag;
		}

		$result = $this->db->query($query);
		if ($result->num_rows()) {
			$addr = $result->row_array();
			$outdata = array_merge($outdata, $addr);
		}

		/*Что это????*/

		if (!isset($outdata['ddate']) || !strlen($outdata['ddate'])) {
			$outdata['ddate'] = date("d.m.Y");
		}
		if (!isset($outdata['dnum'])  || !strlen($outdata['dnum']))  {
			$outdata['dnum'] = '';
		}

		$confAuthorityData = $this->getConfAuthority();
		$outdata['authorityInitials'] = $confAuthorityData['initials'];
		$outdata['authorityName']     = $confAuthorityData['name'];
		$outdata['authorityStaff']    = $confAuthorityData['staff'];
		$outdata['authorityStaffD']   = $confAuthorityData['staffD'];
		return $outdata;
	}

	private function getConfAuthority() { // выборка подписанта ДСП заявок
		$output = array(
			'initials' => "Нет данных",
			'name'     => "Нет данных",
			'staff'    => "Нет данных",
			'staffD'   => "Нет данных"
		);
		$result = $this->db->query("SELECT
		staff.staff,
		staff.id,
		users.name_f,
		users.io,
		users.vrio,
		CONCAT(LEFT(users.name_i, 1), '.', LEFT(users.name_o, 1), '.') AS initials
		FROM
		users
		LEFT OUTER JOIN `staff` ON (users.staff_id = `staff`.id)
		WHERE
		(users.id = ?)", array( $this->confAuthority ));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$staff = $staffD = explode(" ", $row->staff);
				$staffD[0] = "Заместителю";
				if ( $row->id == 52 ) {
					$staffD[6] = "руководителю";
				}
				if ($row->io) {
					array_unshift($staffD, "И.o.");
					$staffD[1] = "заместителя";
					$staff = explode(" ",$row->staff);
					array_unshift($staff, "И.o.");
					$staff[1] = "заместителя";
				}
				$output    = array(
					'initials' => $row->initials,
					'name'     => $row->name_f,
					'staff'    => implode( $staff," "),
					'staffD'   => implode( $staffD, " " )
				);
			}
		}
		return $output;
	}

	################# SUBS #################
	private function getWiFiSubs( $orderID ) {
		$output = array();
		$result = $this->db->query("SELECT
		`resources_items`.id
		FROM
		`resources_items`
		WHERE
		`resources_items`.`order_id` = ?
		AND `resources_items`.`rid` IN (274)", array($orderID));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				array_push( $output, array( 'pid' => 12, 'pid_value' => $this->input->post('wf_reason'), 'item_id' => $row->id ) );
			}
		}
		return $output;
	}

	private function getIMSubs( $orderID ) {
		$output = array();
		$result = $this->db->query("SELECT
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? 
		AND `resources_items`.`rid` IN ( 100, 101 )", array($orderID));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				if ($row->rid == 100) {
					array_push($output, array('pid' => 1,  'pid_value' => iconv('utf-8', 'windows-1251', $this->input->post('email_addr')),   'item_id' => $row->id));
					array_push($output, array('pid' => 12, 'pid_value' => iconv('utf-8', 'windows-1251', $this->input->post('inet_reason')),  'item_id' => $row->id));
				}
				if ($row->rid == 101) {
					array_push($output, array('pid' => 12, 'pid_value' => iconv('utf-8', 'windows-1251', $this->input->post('email_reason')), 'item_id' => $row->id));
				}
			}
		}
		return $output;
	}

	private function getADMSubs( $orderID ) {
		$output = array();
		$result = $this->db->query("SELECT
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`order_id` = ? 
		AND `resources_items`.`rid` IN ( 283 )", array($orderID));
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				if ($row->rid == 100) {
					array_push($output, array('pid' => 12, 'pid_value' => $this->input->post('adm_reason'),  'item_id' => $row->id));
				}
			}
		}
		return $output;
	}

	######## выдача ТЕКСТОВ заявок #########
	private function getSpecialPapers( ) {
		//print_r($this->resList);
		$papers = array();
		$IMStat = false;
		foreach ( $this->resData as $key=>$val ) {
			$this->itemID = ($this->primary) ? 0 : $val['orderID'];
			if ($val['rid'] == 102 ) {
				array_push( $papers, $this->getSpecialDomain() );
			}
			if ( ($val['rid'] == 101 || $val['rid'] == 100) && !$IMStat ) {
				array_push($papers, $this->getSpecialIM());
				if ($this->primary) {
					$IMStat = true;
				}
			}
			if ( $val['rid'] == 274 ) {
				array_push($papers, $this->getSpecialWiFi());
			}
			if ( $val['rid'] == 283 ) {
				array_push($papers, $this->getAdmRights());
			}
		}
		return $papers;
	}

	private function getSpecialDomain() {
		$templatedata = $this->getTemplateData($this->itemID);						// получаем данные для заявки.
		if (!$this->session->userdata('uid')) {									// прерываем исполнение, если есть подозрение, что пользователь уже есть в базе
			$userID  = $this->insertNewUser($templatedata);						// вставляем нового пользователя в базу данных, получаем индекс этой вставки
			$orderID = $this->insertNewOrder();									// вставляем в базу новую заявку, получаем индекс новой заявки
			$resdata = array(array('uid' => $userID, 'order_id' => $orderID, 'rid' => 102));
			$this->insertResource($resdata);									// вставляем сущность ресурса с его привязкой к пользователю и заявке
		}
		return $this->load->view($this->wDirectory.'domain_p', $templatedata, true);
	}

	private function getSpecialIM() {
		$templatedata				= $this->getTemplateData();						// получаем данные для заявки.
		$templatedata['action']		= array();
		$templatedata['decision']	= array();
		$resdata					= array();
		$localData = array();
		if ( $this->primary ) {
			$inputData	= array(
				'mailBox'    => strtolower($this->input->post('email_addr')),
				'mailReason' => $this->input->post('email_reason') ? iconv('utf-8', 'windows-1251', $this->input->post('email_reason')) : $this->defaultReason,
				'inetReason' => $this->input->post('inet_reason')  ? iconv('utf-8', 'windows-1251', $this->input->post('inet_reason'))  : $this->defaultReason
			);
			$orderID = $this->insertNewOrder();									// вставляем в базу новую заявку, получаем индекс новой заявки
			$subsdata= $this->getIMSubs( $orderID );
			$this->insertResource($resdata);
			$this->insertSubs($subsdata);
			if ( isset($this->resList[100])) {
				array_push( $resdata, array('uid' => $this->UID, 'order_id' => $orderID, 'rid' => 100) );
				array_push( $templatedata['action'], "предоставить доступ к почтовому ящику электронной почты с адресом ".$inputData['mailBox']."@arhcity.ru на сервере электронной почты для ".$inputData['mailReason'] );
				array_push( $templatedata['decision'], $this->decisions[100] );
			}

			if ( isset($this->resList[101])) {
				array_push( $resdata, array('uid' => $this->UID, 'order_id' => $orderID, 'rid' => 101) );
				array_push( $templatedata['action'], 'доступ к сети "Интернет" для '.$inputData['inetReason'] );
				array_push( $templatedata['decision'], $this->decisions[101] );
			}
			//print_r($templatedata);
		}
		if ( !$this->primary ) {
			foreach ($this->pidData as $key=>$val) {
				if ( $val['rid'] == 100 || $val['rid'] == 101 ) {
					array_push($localData, $val);
				}
			}
			foreach ( $localData as $val ) {
				$inputData               = $this->bidsData[$val['order']];
				$inputData['mailBox']    = ( isset($this->pidData[$val['itemID']][1]) )  ? $this->pidData[$val['itemID']][1]  : "";
				$inputData['mailReason'] = ( isset($this->pidData[$val['itemID']][12]) ) ? $this->pidData[$val['itemID']][12] : $this->defaultReason;
				$inputData['inetReason'] = ( isset($this->pidData[$val['itemID']][12]) ) ? $this->pidData[$val['itemID']][12] : $this->defaultReason;
				if ($val['rid'] == 100) {
					if ($val['order'] == $this->itemID) {
						array_push($templatedata['action'], "предоставить доступ к почтовому ящику электронной почты с адресом ".$inputData['mailBox']."@arhcity.ru на сервере электронной почты ".$inputData['mailReason']);
						array_push( $templatedata['decision'], $this->decisions[$val['rid']] );
					}
				}
				if ($val['rid'] == 101) {
					if ($val['order'] == $this->itemID) {
						array_push($templatedata['action'], 'доступ к сети "Интернет" '.$inputData['inetReason']);
						array_push($templatedata['decision'], $this->decisions[$val['rid']]);
					}
				}
			}
		}
		$templatedata['decision'] = implode($templatedata['decision'], ", ");
		$templatedata['action']   = implode($templatedata['action'], ", ");


		return $this->load->view($this->wDirectory.'inml', $templatedata, true);
	}

	private function getSpecialWiFi() {
		$templatedata = $this->getTemplateData();									// получаем данные для заявки.
		$templatedata['mailaction'] = "";
		$templatedata['inetaction'] = 'предоставить доступ к Интернет-ресурсам средствами беспроводной сети для '.iconv('utf-8', 'windows-1251', $this->input->post('wf_reason'));
		$templatedata['decision']   = $this->decisions[274];
		if ( $this->primary ) {
			$orderID      = $this->insertNewOrder();							// вставляем в базу новую заявку, получаем индекс новой заявки
			$resdata      = array(array('uid' => $this->UID, 'order_id' => $orderID, 'rid' => 274));
			$subsdata     = $this->getWiFiSubs($orderID);
			$this->insertResource($resdata);
			$this->insertSubs($subsdata);
		}
		return $this->load->view($this->wDirectory.'wf', $templatedata, true);
	}

	private function getAdmRights() {
		$templatedata               = $this->getTemplateData();		// получаем данные для заявки.
		if ( !$this->primary ) {
			$reason = (isset($this->pidData[283][12])) ? $this->pidData[283][12] : " исполнения служебных обязанностей";
		}
		$reason = ( $this->primary ) ? $this->input->post('adm_reason') : $reason ;
		$templatedata['mailaction'] = '';
		$templatedata['inetaction'] = 'локальные административные права пользователя на персональном компьютере для '.$reason;
		$templatedata['decision']   = 'включить пользователя в группу локальных администраторов';
		if ( $this->primary ) {
			$orderID  = $this->insertNewOrder();						// вставляем в базу новую заявку, получаем индекс новой заявки
			$resdata  = array(array('uid' => $templatedata['uid'], 'order_id' => $orderID, 'rid' => 283));
			$subsdata = $this->getADMSubs( $orderID );
			$this->insertResource($resdata);
			$this->insertSubs($subsdata);
		}
		return $this->load->view($this->wDirectory.'adm', $templatedata, true);
	}
	########################################
	##

	private function wipeEmptyLayerIDs( $layerList ) {
		$layerList = explode(",", $layerList);
		foreach ( $layerList as $key=>$val ) {
			if ( !$val || !strlen($val) ) {
				unset( $layerList[$key] );
			}
		}
		return implode($layerList, ",");
	}

	private function getLayerList( $subs ) {
		$output = false;
		$subs   = $this->wipeEmptyLayerIDs($subs);
		if ($subs && strlen($subs)) {
			$result = $this->db->query("SELECT
			`resources_layers`.rid,
			`resources_layers`.spn
			FROM
			`resources_layers`
			WHERE
			`resources_layers`.`id` IN (".$subs.")");
			if ($result->num_rows()) {
				$output = array();
				foreach ($result->result() as $row) {
					if (!isset($output[$row->rid])) {
						$output[$row->rid] = array();
					}
					array_push($output[$row->rid], $row->spn);
				}
			}
		}
		if ( !$output ) {
			return "";
		}
		foreach ( $output as $key=>$val ) {
			$output[$key] = implode($val, "<br>- ");
		}
		return $output;
	}

	private function getAffectedOwnersList( $result ) {
		$output = array();
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$output[$row->owner] = array(
					'otv_dl_name' => $row->otv_dl_name,
					'owner_staff' => $row->owner_staff
				);
			}
		}
		return $output;
	}

	private function getResStructure( $result, $layers ) {
		$output = array();
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				$this->resNamesList[$row->id] = $row->name;
				//print "1111<br>";
				$additional_info = "";
				if ($row->id == 286) {
					$additional_info = "<br>Приглашение на подключение учётной записи в ЕСИА к организации прошу выслать на почтовый ящик ".$this->mailBox;
				}
				$bitmasks = array(
					"11111" => "",
					"10111" => "",
					"10011" => "",
					"10001" => ""
				);
				$bitmasks[$row->bitmask] = " x ";
				$layerList = ($this->primary)
					? ( ( isset($layers[$row->itemID]) ) ? "<br>- ".$layers[$row->itemID] : "" )
					: ( isset($layers[$row->id]) ) ? $layers[$row->id] : "";
				$data = array(
					'name'        => $row->name,
					'layerList'   => ( isset($layers[$row->itemID]) ) ? "<br>- ".$layers[$row->itemID] : "",
					'esiaMailBox' => $additional_info,
					'bitmask'     => $bitmasks
				);
				if (!isset($output[$row->cat])) {
					$output[$row->cat] = array();
				}
				if (!isset($output[$row->cat][$row->owner])) {
					$output[$row->cat][$row->owner] = array();
				}
				$output[$row->cat][$row->owner][$row->id] = $this->load->view($this->wDirectory."reschunk", $data, true);
			}
		}
		return $output;
	}

	private function getWebPortalSection() {
		$output = array();
		//$this->output->enable_profiler(TRUE);
		$search = iconv('utf-8', 'windows-1251', $this->input->post('search'));
		$DB3 = $this->load->database('web', TRUE);
		$result = $DB3->query("SELECT
		section2.secname AS parent2,
		section1.secname AS parent1,
		section.secname,
		section.sid
		FROM
		section
		RIGHT OUTER JOIN section section1 ON (section.secparent = section1.sid)
		RIGHT OUTER JOIN section section2 ON (section2.sid = section1.secparent)
		WHERE
		((section.sid = ?) OR (section.secname LIKE ?))
		AND (LENGTH(section1.secname))", array($search, $search."%") );
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				$string = '<li class="btn btn-small btn-block" title="'.$row->parent2." ".$row->parent1.'">['.$row->sid."/0] ".$row->secname.'</li>';
				array_push($output, $string);
			}
			print implode($output, "\n");
			return true;
		}
		print "<li>Ничего не найдено</li>";
	}

	private function getCommonPapers( $DSP=false ) {
		
		$papers    = array();
		$localData = array();
		//$outdata      = $this->getUserProperties($this->UID);
		$templatedata = $this->getTemplateData();							// получаем данные темплейта для заявки.
		$templatedata['fulladdress'] = preg_replace("/[^А-Яа-я0-9\.\- ]/ism", '', $templatedata['fulladdress']);
		// получаем рабочие массивы
		$resdata = $this->getPapersGroup();
		
		$agreedForms = array(
			// depID => resID
			'12' => '14',
			'21' => '21'
			//69 => 69
		);
		
		//print_r($this->resList);

		//print "<br><br><br>";

		foreach ($resdata['resources'] as $owner => $resourceSet) {
			$templatedata['soglLayerList']      = "";
			foreach ($resourceSet as $key2 => $val2) {
				if (isset($resdata['layers'][$key2]) && isset($resdata['layers'][$this->itemID] )) {
					$templatedata['soglLayerList']      = $resdata['layers'][$this->itemID];
				}
			}
			//print_r($resdata['resources']);
			//print $owner;
			//print_r($resourceSet);
			foreach ($resourceSet as $cat => $data) {
				//print_r($data);
				//return false;
				$templatedata['res_container']  = implode($data, "");
				$templatedata['owner_staff']    = $resdata['owners'][$owner]['owner_staff'];
				$templatedata['r_owner']        = $resdata['owners'][$owner]['otv_dl_name'];
				$sogltype = (isset($agreedForms[$owner])) ? $agreedForms[$owner] : false; 
				$page = ( $cat > 1 )
					? $this->load->view($this->wDirectory.'conf',   $templatedata, true) 
					: $this->load->view($this->wDirectory.'common', $templatedata, true);
				array_push($papers, $page);														// оформляем заявку
				if ( $cat > 1 ) {
					if ( $sogltype ) {
						$currentResIDs = array_keys($data);
						$templatedata['currentResName'] = $this->resNamesList[$currentResIDs[0]];
						array_push($papers, $this->load->view($this->wDirectory.'sogl'.$sogltype, $templatedata, true));
					}
					array_push($papers, $this->load->view($this->wDirectory.'ordermemo', $templatedata, true));
				}
			}
		}
		return $papers;
	}

	private function getPapersGroup() {
		//print_r($this->resList);

		$layers = array();
		if ( $this->primary ) {
			$layers = $this->getLayerList( $this->input->post("subs") );
			$result = $this->db->query("SELECT
			resources.id AS itemID,
			resources.id AS resID,
			resources.cat,
			resources.id,
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
			WHERE
			(`resources`.id IN (".implode(array_keys($this->resList), ",").")) 
			AND (NOT (users.fired))");
		}

		if ( !$this->primary ) {
			foreach ($this->pidData as $key=>$val) {
				if ($val['order'] == $this->itemID && isset($this->pidData[$key][2])) {
					$layers[$key] = $this->pidData[$key][2];
				}
			}

			$result = $this->db->query("SELECT
			`resources_items`.id AS itemID,
			resources.id AS resID,
			`resources`.cat,
			`resources`.id,
			`resources`.name,
			`resources`.bitmask,
			`resources`.shortname,
			`resources`.owner,
			`staff`.staff AS owner_staff,
			CONCAT(LEFT(users.name_i, 1),'.', LEFT(users.name_o, 1), '. ', users.name_f) AS otv_dl_name
			FROM
			departments
			INNER JOIN staff ON (departments.chief = staff.id)
			INNER JOIN resources ON (departments.id = resources.owner)
			INNER JOIN users ON (departments.chief = users.staff_id)
			AND (resources.owner = users.dep_id)
			LEFT OUTER JOIN `resources_items` ON (resources.id = `resources_items`.rid)
			WHERE
			(`resources_items`.id IN (".implode(array_keys($this->resList), ",")."))
			AND `resources`.id NOT IN ( 102, 101, 100, 274, 283 )
			AND (NOT (users.fired))");
		}
		$owners        = $this->getAffectedOwnersList($result);
		$process       = $this->getResStructure($result, $layers);
		$processedData = $this->processResources($process);
		$this->insertResource($processedData['resdata']);
		$this->insertSubsData($layers, $processedData, $this->UID);
		
		//print_r($process);
		
		return $output = array(
			'layers'	=> $layers, // здесь должен был быть список слоёв?
			'owners'	=> $this->getAffectedOwnersList($result),
			'resources'	=> $processedData['resources'],
		);
	}

	private function getUserIDByOrder( $order ) {
		$userID = 0;
		$result = $this->db->query("SELECT 
		resources_items.uid
		FROM
		resources_items
		WHERE
		(resources_items.`id` = ?)", array($order));
		if ($result->num_rows()) {
			$row    = $result->row();
			$userID = $row->uid;
		}
		return $userID;
	}

	private function getUserFIOByOrder( $UID ) {
		if ( $this->primary ) {
			return implode( array( $this->input->post('name_f'), $this->input->post('name_i'), $this->input->post('name_o') ), "_"); 
		}
		$result = $this->db->query("SELECT
		CONCAT_WS(' ', `users`.name_f, `users`.name_i, `users`.name_o) AS fio
		FROM users
		WHERE
		users.id = ?", array($UID));
		if ($result->num_rows()) {
			$row = $result->row();
			return $row->fio;
		}
		return '';
	}

	private function getPids() {
		$pidData = array();
		$result = $this->db->query("SELECT 
		resources_pid.pid_value,
		resources_pid.pid,
		resources_pid.item_id,
		resources_items.rid,
		resources_items.order_id,
		IF(resources.cat = 1, 0, 1) AS conf
		FROM
		resources_pid
		RIGHT OUTER JOIN resources_items ON (resources_pid.item_id = resources_items.id)
		LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
		WHERE
		(resources_pid.item_id IN (".implode($this->resItems, ",")."))");
		if ($result->num_rows()) {
			foreach ($result->result() as $row) {
				if ( !isset($pidData[$row->item_id]) ) {
					$pidData[$row->item_id] = array( 'rid' => $row->rid, 'order' => $row->order_id, 'itemID' => $row->item_id, 'conf' => $row->conf );
				}
				$pidData[$row->item_id][$row->pid] = implode(explode( "\n", $row->pid_value), "<br>- ");
			}
			$this->pidData = $pidData;
			return true;
		}
		return false;
	}

	private function getPrimaryBidsData ( ) {
		return array( '0' => array(
			'staffname'   => '',
			'phone'       => $this->input->post('phone'),
			'login'       => $this->input->post('login'),
			'fulladdress' => '',
			'office2'     => $this->input->post('office2'),
			'office'      => $this->input->post('office'),
			'staff'       => $this->input->post('staff'),
			'dept'        => $this->input->post('dept'),
			'name_f'      => iconv("UTF-8", "windows-1251", $this->input->post('sname')),
			'name_i'      => iconv("UTF-8", "windows-1251", $this->input->post('name')),
			'name_o'      => iconv("UTF-8", "windows-1251", $this->input->post('fname')),
			'dnum'        => '',
			'ddate'       => ''
		));
	}

	private function getBidsData ( $resItems ) {
		$result = $this->db->query("SELECT
		resources_items.rid,
		resources_items.order_id,
		`users`.dep_id AS dept,
		CONCAT_WS(' ', locations1.address, `locations`.address) as office,
		resources_items.id,
		`users`.name_f,
		`users`.name_i,
		`users`.name_o,
		`users`.login,
		`users`.staff_id,
		`users`.office_id,
		`users`.phone,
		locations.parent,
		`resources_orders`.docnum AS dnum,
		DATE_FORMAT(`resources_orders`.docdate, '%d.%m.%Y') AS ddate,
		LOWER(`staff`.staff) AS staff
		FROM
		resources_items
		RIGHT OUTER JOIN `users` ON (resources_items.uid = `users`.id)
		LEFT OUTER JOIN `locations` ON (`locations`.id = `users`.office_id)
		LEFT OUTER JOIN `resources_orders` ON (resources_items.order_id = `resources_orders`.id)
		LEFT OUTER JOIN `staff` ON (`users`.staff_id = `staff`.id)
		LEFT OUTER JOIN `locations` locations1 ON (`locations`.parent = locations1.id)
		WHERE
		resources_items.id IN (".implode($resItems, ",").")");
		if ($result->num_rows()) {
			$output = array();
			foreach ($result->result() as $row ) {
				$output[$row->order_id] = array(
					'staffname'   => $row->staff,
					'phone'       => $row->phone,
					'login'       => $row->login,
					'fulladdress' => $row->office,
					'office2'     => $row->office_id,
					'office'      => $row->office_id,
					'staff'       => $row->staff_id,
					'dept'        => $row->dept,
					'name_f'      => $row->name_f,
					'name_i'      => $row->name_i,
					'name_o'      => $row->name_o,
					'dnum'        => $row->dnum,
					'ddate'       => $row->ddate
				);
			}
			return $output;
		}
		return false;
	}

	########################################
	## Inserters FX
	private function insertSubsData( $layers, $processedData ) {
		$subsdata = array();
		$result = $this->db->query("SELECT
		`resources_items`.id,
		`resources_items`.rid
		FROM
		`resources_items`
		WHERE `resources_items`.`uid`= ?
		AND `resources_items`.order_id IN (".implode($processedData['orderIDs'], ",").")", array($this->UID));
		if ($result->num_rows()) {
			foreach($result->result() as $row) {
				if ( isset($layers[$row->rid]) ) {
					array_push( $subsdata, "(2, '".implode($layers[$row->rid], "\n")."',".$row->id.")" );
				}
			}
		}
		if ( sizeof($subsdata) && $this->dbwrite ) {
			$this->db->query("INSERT INTO
			resources_pid (
				resources_pid.`pid`,
				resources_pid.`pid_value`,
				resources_pid.`item_id`
			) VALUES ".implode($subsdata, ",\n"));
			return true;
		}
		return false;
	}

	private function insertNewOrder() {
		if ( $this->dbwrite ) {
			$this->db->query("INSERT INTO resources_orders (resources_orders.docdate) VALUES (NOW())");
			$orderID = $this->db->insert_id();
			return $orderID;
		} else {
			return 0;
		}
	}

	private function insertNewUser( $data ) {
		$userID = 0;
		if( $this->dbwrite ){
			$result = $this->db->query("INSERT INTO users (
			users.`name_f`,
			users.`name_i`,
			users.`name_o`,
			users.`host`,
			users.`login`,
			users.`phone`,
			users.`service`,
			users.`supervisor`,
			users.`dep_id`,
			users.`office_id`,
			users.`staff_id`
			) VALUES ( TRIM(?), TRIM(?), TRIM(?), ?, ?, ?, ?, ?, ?, ?, ? )",
			array(
				$data['name_f'],
				$data['name_i'],
				$data['name_o'],
				$data['login'],
				$data['login'],
				$data['phone'],
				$data['service'],
				$data['service'],
				$data['dept'],
				(isset($data['addr2']) && strlen($data['addr2']) && $data['addr2']) ? $data['addr2'] : $data['addr1'],
				$data['staff']
			));
			
			$userID = $this->db->insert_id();
			$this->session->set_userdata('uid', $userID);
			$_POST['uid'] = $userID; // вообще это неправильно совать и в сессию и в POST. Но функция ожидает uid там, а переписывать пока нет времени 31.03.2016
			$this->insertUserToCFS($data['name_f'], $data['name_i'], $data['name_o']);
		}
		return $userID;
	}

	private function insertResource( $data ) {
		if ( sizeof($data) && $this->dbwrite ) {
			$output = array();
			foreach ($data as $key => $val) {
				$string = "( '".$val['uid']."', '".$val['order_id']."', '".$val['rid']."', NOW() )";
				array_push($output, $string);
			}
			$this->db->query("INSERT INTO `resources_items` (
				`resources_items`.uid,
				`resources_items`.order_id,
				`resources_items`.rid,
				`resources_items`.initdate
			) VALUES ".implode($output, ",\n"));
			return true;
		}
		return false;
	}

	private function insertSubs( $subs ) {
		if ( sizeof($subs) && $this->dbwrite ) {
			$this->db->insert_batch("resources_pid", $subs);
			return true;
		}
		return false;
	}
	
	private function insertUserToCFS( $name_f, $name_i, $name_o ) {
		//внешний коннект. Создание пользователя на cfs2
		$DB2 = $this->load->database('12', TRUE);
		$DB2->query("SET NAMES cp1251");
		$result = $DB2->query("SELECT
			`fios`.`fio`
			FROM
			`fios` 
			WHERE TRIM(`fios`.`fio`) = TRIM(?)", array($name_f." ".$name_i." ".$name_o));
		if (!$result->num_rows()) {
			$DB2->query("INSERT INTO fios (fios.`fio`) VALUES (TRIM(?))", array($name_f." ".$name_i." ".$name_o));
		}
	}

	########################################
	## Utils
	private function overrideTemplateData( $staffID ) {
		if ( $staffID == 11 || $staffID == 27 ) {
			$otv_dl = 'Заместитель Главы муниципального образования "Город Архангельск"';
		}
		if ( $staffID == 32 ) {
			$otv_dl = 'Советник Главы муниципального образования "Город Архангельск"';
		}
		if ( $staffID == 40 ) {
			$otv_dl = 'Помощник Главы муниципального образования "Город Архангельск"';
		}
		return array(
			'fio'    => $this->input->post("name_f")." ".strtoupper(substr($this->input->post("name_i"), 0, 1).".".substr($this->input->post("name_o").".", 0 ,1)),
			'otv_dl' => $otv_dl,
			'org'    => "", //"Мэрия города Архангельска";
			'cred'   => "пл.В.И.Ленина, д.5, г.Архангельск, 163000<br>тел. 65-64-84, факс 65-20-71<br>Е-mail: adminkir@arhcity.ru; http:// www.arhcity.ru",
			'zakaz'  => 'Общий отдел Администрации муниципального образования "Город Архангельск". Заказ 001.  01.01.2016'
		);
	}

	private function processResources( $process ) {

		$output = array(
			'resources' => array(),
			'orderIDs'  => array(),
			'resdata'   => array()
		);
		foreach ($process as $cat=>$owner) {
			// вставляем в базу новую заявку, получаем индекс новой заявки
			$orderID = $this->insertNewOrder();
			array_push( $output['orderIDs'], $orderID );

			foreach ($owner as $ownerid => $resData) {
				if (!isset($output['resources'][$ownerid])) {
					$output['resources'][$ownerid] = array();
				}
				foreach ($resData as $resID => $label) {
					array_push($output['resdata'], array('uid' => $this->UID, 'order_id' => $orderID, 'rid' => $resID));
					$output['resources'][$ownerid][$cat] = $resData;
				}
			}
		}
		//print_r($output);
		return $output;
	}

	private function splitResFlow( $input=array() ) {
		$output = array();
		if (!$this->primary) {
			$input  = (sizeof($input)) ? implode($input, ",") : "0";
			$result = $this->db->query("SELECT
			resources_items.id as itemID,
			resources_items.order_id as orderID,
			resources.cat,
			resources.id
			FROM
			resources_items
			LEFT OUTER JOIN resources ON (resources_items.rid = resources.id)
			WHERE
			(resources_items.id IN (".$input."))");
			if ($result->num_rows()) {
				foreach($result->result() as $row) {
					array_push($output, array( 
						'rid'     => $row->id,
						'itemID'  => $row->itemID,
						'orderID' => $row->orderID,
						'conf'    => ($row->cat > 1) ? true : false
					));
				}
			}
		}
		if ($this->primary) {
			$result = $this->db->query("SELECT
			resources.cat,
			resources.id
			FROM
			resources
			WHERE
			(resources.id IN (".$this->input->post("res")."))");
			if ($result->num_rows()) {
				foreach($result->result() as $row) {
					array_push($output, array( 
						'rid'     => $row->id,
						'itemID'  => 0,
						'orderID' => 0,
						'conf'    => ($row->cat > 1) ? true : false
					));
				}
			}
		}
		return $output;
	}

	private function wipeOutSpecials() {
		foreach ( $this->resData as $key=>$val ) {
			if ( in_array($this->resData[$key]['rid'], array( 283, 102, 101, 100, 274 )) ) {
				unset($this->resData[$key]);
			}
		}
		foreach ( $this->resList as $key=>$val ) {
			if ( in_array($key, array( 283, 102, 101, 100, 274 )) ) {
				unset($this->resList[$key]);
			}
		}
	}

	private function getResList() {
		//print_r($this->resData);
		$output = array();
		foreach ($this->resData as $val) {
			if ( $this->primary ) {
				$output[$val['rid']] = ($val['conf']) ? 1 : 0;
			}
			if ( !$this->primary ) {
				$output[$val['itemID']] = ($val['conf']) ? 1 : 0;
			}
		}
		return $output;
	}

	########################################
	## Main Section
	public function papers_get( $resstring="" ) {
		/*
		* Получение заявок (первичное). Принимает на вход отсортированные данные из _POST
		* Выдаёт строку в HTML, направляемую пользователю
		*/
		$this->dbwrite      = ((int)$this->session->userdata('admin_id') === 1) ? false : true;
		$this->expose       = ((int)$this->session->userdata('admin_id') === 1) ? true  : false;
		$this->wrap_to_word = ((int)$this->session->userdata('admin_id') === 1) ? false : true;

		if ($this->expose) {
			$this->output->enable_profiler(TRUE);
		}
		$outfile            = "";
		if ( $this->primary ) {
			$this->UID      = $this->input->post('uid');
			$this->resItems = explode( ",", $this->input->post("res") );
			$this->mailBox  = $this->input->post("esiaMailAddr");
			$this->bidsData = $this->getPrimaryBidsData();
		}

		if ( !$this->primary ) {
			$this->resItems = explode( ",", $resstring );
			$this->UID      = $this->getUserIDByOrder( $this->resItems[0] );
			$this->getPids();
			$this->mailBox  = $this->getUserMailboxDB( $this->UID );
			$this->bidsData = $this->getBidsData( $this->resItems );
		}

		$this->resData      = $this->splitResFlow( $this->resItems );
		$this->resList      = $this->getResList();

		$papers             = $this->getSpecialPapers();

		$this->fnFIO        = $this->getUserFIOByOrder($this->UID);

		$this->wipeOutSpecials();
		/*
		* конфиденциальные ресурсы и обычные - извлекаются в 2 режимах
		* одной и той же функции
		*/
		$commons = array();
		if (sizeof($this->resData)) {
			$commons = $this->getCommonPapers();
		}

		//print_r($commons);

		if ( $this->wrap_to_word ) {
			$pagebreak = "<span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\";mso-fareast-font-family:\"Times New Roman\";mso-ansi-language:EN-US;mso-fareast-language:EN-US;mso-bidi-language:JI'><br clear=all style='page-break-before:always;mso-break-type:section-break'></span>";
			$outfile   = implode(array_merge($papers, $commons), $pagebreak);
			$filename  = 'Заявки на информационные ресурсы '.$this->fnFIO.'.doc';
			$this->load->helper('download');
			force_download($filename, $outfile);
			return true;
		}
		//$this->output->enable_profiler(TRUE);
		$pagebreak = "<hr>";
		$outfile = implode(array_merge($papers, $commons), $pagebreak);
		print $outfile;
		return true;
	}

	public function reget_orders() {
		//$this->output->enable_profiler(TRUE);
		//return false;
		$this->primary = false;
		$this->dbwrite = false;
		$this->papers_get($this->input->post("resources"));
	}
}

/* End of file bidsmodel2.php */
/* Location: ./application/models/bidsmodel2.php */