<?php
class Loginmodel extends CI_Model{

	function __construct(){
		parent::__construct();		// Call the Model constructor
		$this->load->helper('url');
	}

	function index($mode='auth'){
		$act = Array();
		$act['reg'] = ($mode=='auth') ? 0 : 1;
		$act['errorlist'] = "";
		$this->load->view('login/login_view2',$act);
	}

	function _test_user(){
		$errors = array();
		$result = $this->db->query("SELECT 
		`admins`.id,
		`admins`.base_id,
		`admins`.password,
		`admins`.rank,
		`admins`.uid,
		`admins`.active,
		`admins`.supervisor,
		`admins`.description
		FROM
		`admins`
		WHERE
		admins.user = ?", array($this->input->post('name', TRUE)));
		$ack = array();
		if ($result->num_rows()){
			$row = $result->row();
			if('secret'.$this->input->post('pass') == $row->password){ // если пароль верен
				if(!$row->active){//а может быть пользователя мы отключили?
					array_push($errors,"Пользователь с указанными именем и паролем неактивен.");
				}
				if(!sizeof($errors)){
					$result = $this->db->query("SELECT 
					users.id,
					CONCAT_WS(' ', users.name_f, users.name_i, users.name_o) AS fio,
					departments.alias,
					IF(users.id IN (SELECT DISTINCT admins.supervisor FROM `admins`), 1,0) as supervisor
					FROM
					users
					INNER JOIN departments ON (users.dep_id = departments.id)
					WHERE
					(users.id = ?)", array($row->base_id));
					if ($result->num_rows()){
						$row2 = $result->row();
					}
					$this->session->set_userdata('admin_id', $row->id); // id оператора для модулей
					$this->session->set_userdata('canSee', $row->supervisor); // руководитель
					$this->session->set_userdata('base_id', $row->base_id); // учётный номер в базе данных
					$this->session->set_userdata('rank', $row->rank); // ранг оператора
					$this->session->set_userdata('user_name', $this->input->post('name')); // системное имя оператора (для аудита)
					$this->session->set_userdata('selfname', $row2->fio); // Человеческое имя оператора - для заголовка
					$this->session->set_userdata('is_sup', $row2->supervisor); // булево значение - супервизор ли
					$this->usefulmodel->insert_audit("Зарегистрирован вход оператора #".$this->session->userdata('user_name'));
					redirect("");
				}
			}else{
				array_push($errors,'<div class="alert alert-error span5" style="clear:both;margin:40px;"><a class="close" data-dismiss="alert" href="#">x</a>
				<h4 class="alert-heading">Ошибка!</h4>
				Пользователь с указанными именем и паролем не найден. Проверьте правильность ввода имени пользователя и пароля. Обратите внимание, что прописные и строчные буквы различаются
				</div>');
				$this->usefulmodel->insert_audit("Зарегистрирована неудачная попытка входа оператора #".$this->input->post('name'));
			}
		}else{
			array_push($errors,'<div class="alert alert-error span5" style="clear:both;margin:40px;"><a class="close" data-dismiss="alert" href="#">x</a>
				<h4 class="alert-heading">Ошибка!</h4>
				Пользователь с указанными именем и паролем не найден. Проверьте правильность ввода имени пользователя и пароля. Обратите внимание, что прописные и строчные буквы различаются
				</div>');
			$this->usefulmodel->insert_audit("Зарегистрирована неудачная попытка входа оператора #".$this->input->post('name'));
		}
		$act['errorlist']=implode($errors,"<br>\n");
		$this->load->view('login/login_view2',$act);
	}

}
#
/* End of file loginmodel.php */
/* Location: ./system/application/models/loginmodel.php */