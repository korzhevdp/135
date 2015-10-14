<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dataget extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		print snmpget("192.168.1.36", "public", "sysDescr.0")."<BR>";
		print snmpget("192.168.1.36", "public", "ifPhysAddress.14")."<BR>";
		print "Скорость интерфейса: ".snmpget("192.168.1.36", "public", "ifSpeed.14")."<BR>";
		print "Оперативная память: ".snmpget("192.168.1.36", "public", "hrMemorySize.0")."<BR>";
		print "Объём диска: ".snmpget("192.168.1.36", "public", "hrDiskStorageCapacity.31")."<BR>";

	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */