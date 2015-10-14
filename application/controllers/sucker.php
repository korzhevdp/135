<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sucker extends CI_Controller {
	function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->arhcity_foresucker();
		//$this->dv_foresucker();
	}
	
	public function dv_foresucker(){
		$newsurl="http://dvinaland.ru/";
		$page = file_get_contents($newsurl, "r"); // �������� ���������� URL � ������
		preg_match('/<\!\-\- dvinanews block \-\->(.*)<!\-\- end dvinanews block \-\->/isUm', $text, $matches);
		print_r($matches);
	}

	public function arhcity_foresucker(){
		set_time_limit (0);
		$newsurl="http://arhcity.ru/?page=0/";
		$result = $this->db->query("SELECT MAX(arhcity.f_counter) AS ct FROM arhcity");
		if($result->num_rows()){
			$row = $result->row(0);
		}
		$newsid = (strlen($row->ct)) ? $row->ct : 1 ;
		for($i = 1; $i <= 100; $i++){ // ��������� ����� �� 100 �������
			$news = array(); // ������-�������
			$text = file_get_contents($newsurl.($newsid+$i), "rb"); // �������� ���������� URL � ������

			if($text){ //���� ���-�� ������� �� ����� ��������
				#
				# ���������� ���������
				preg_match('/<div class="pagename">(.*)<\/div>/isUm', $text, $matches);
				$news['header'] = trim(strip_tags($matches[1]));
				# �� ������ ���� ��������� ��� - ���� � ���������� ����������
				if($news['header'] == "�������� �����������"){
					print $news['header']." - ".$newsid+$i."<br> ���������� ����� �������������� ��������� ������<br><br>";
					usleep(2000);
					continue; // �� � ����� ��� ����
				}
				# ���������� �����
				preg_match('/<div class="newsmsg_body">(.*)<\/div>/isUm', $text, $matches);
				$news['text'] = $matches[1];
				# ���������� ������
				preg_match('/����� ���������(\s+):(\s+)(.*)����/isUm', $text, $matches);
				$news['author'] = trim(strip_tags($matches[3]));
				# ���������� ���� ���������
				preg_match('/���� ���������(\s+):(\s+)(.*)������/isUm', $text, $matches);
				$news['date'] = trim(strip_tags($matches[3]));
				# ���������� ���� ��������
				preg_match('/��������� ���������(\s+):(\s+)(.*)���/isUm', $text, $matches);
				$news['edit'] = trim(strip_tags($matches[3]));
				
				$result = $this->db->query("INSERT INTO
				`arhcity`(
				`arhcity`.`url`,
				`arhcity`.`header`,
				`arhcity`.`text`,
				`arhcity`.`author`,
				`arhcity`.`date`,
				`arhcity`.`edit`,
				`arhcity`.`f_counter`) VALUES(?, ?, ?, ?, ?, ?, ?)", array(
					$newsurl.($newsid+$i),
					$news['header'],
					$news['text'],
					$news['author'],
					$news['date'],
					$news['edit'],
					($newsid+$i)
				));
				print "������� �� URL <b>".$newsurl.($newsid+$i)."</b> ������� �������<br>"; // �������� �������
			}else{
				print "������� �� URL <b>".$newsurl.($newsid+$i)."</b> �����������<br>"; // �������, ��?
			}
			usleep(4000); // ���� �����
		}
	}

}
/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */