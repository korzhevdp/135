<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kbmodel extends CI_Model {
	function __construct(){
		parent::__construct();
	}

	public function index_show(){
		$this->load->helper('file');
		$output = array();
		$filearray = get_filenames('application/views/knowledgebase', TRUE);
		foreach ($filearray as $name) {
			$pathsegments = explode("/", $name);
			$text = read_file($name);
			preg_match("/<title>(.*)<\/title>/i", $text, $matches);
			//array_push($output, $matches[1]);
			if ( isset($matches[1]) ) {
				$link = '<li><a href="/kb/page/'.$pathsegments[sizeof($pathsegments) - 1].'">'.$matches[1].'</a></li>';
				array_push($output, $link);
			}
		}
		
		return "<ol>".implode($output, "\n")."</ol>";
	}

}
/* End of file bidsmodel.php */
/* Location: ./application/models/bidsmodel.php */