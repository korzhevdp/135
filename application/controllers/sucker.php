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
		$page = file_get_contents($newsurl, "r"); // получаем содержимое URL в строку
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
		for($i = 1; $i <= 100; $i++){ // проверяем вперёд на 100 записей
			$news = array(); // массив-приёмник
			$text = file_get_contents($newsurl.($newsid+$i), "rb"); // получаем содержимое URL в строку

			if($text){ //если что-то похожее на текст получено
				#
				# определяем заголовок
				preg_match('/<div class="pagename">(.*)<\/div>/isUm', $text, $matches);
				$news['header'] = trim(strip_tags($matches[1]));
				# на случай если документа нет - спим и быстренько продолжаем
				if($news['header'] == "Документ отсутствует"){
					print $news['header']." - ".$newsid+$i."<br> Продолжаем поиск предполагаемой следующей статьи<br><br>";
					usleep(2000);
					continue; // ну и будет нам пока
				}
				# определяем текст
				preg_match('/<div class="newsmsg_body">(.*)<\/div>/isUm', $text, $matches);
				$news['text'] = $matches[1];
				# определяем автора
				preg_match('/Автор документа(\s+):(\s+)(.*)Дата/isUm', $text, $matches);
				$news['author'] = trim(strip_tags($matches[3]));
				# определяем дату документа
				preg_match('/Дата документа(\s+):(\s+)(.*)Послед/isUm', $text, $matches);
				$news['date'] = trim(strip_tags($matches[3]));
				# определяем дату редакции
				preg_match('/Последнее изменение(\s+):(\s+)(.*)Хэш/isUm', $text, $matches);
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
				print "Новость по URL <b>".$newsurl.($newsid+$i)."</b> всосана успешно<br>"; // победная реляция
			}else{
				print "Новость по URL <b>".$newsurl.($newsid+$i)."</b> отсутствует<br>"; // грустно, да?
			}
			usleep(4000); // спим долго
		}
	}

}
/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */