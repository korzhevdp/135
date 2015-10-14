<ul class="nav nav-list">
	<li<?=(($_SERVER["PATH_INFO"] == "/") ? ' class="active"' : '')?>><strong><a href= "/">����� ����������</a></strong> 
	<?=(sizeof($this->session->userdata('tickets')) ? '<span class="badge badge-important">'.sizeof($this->session->userdata('tickets')).'</span>' : "");?>
	</li>
	<li class="nav-header">�������</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/admin/users") ? ' class="active"' : '')?>><a href= "/admin/users"><i class="icon-user"></i>������������ ����</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/console") ? ' class="active"' : '')?>><a href="/console"><i class="icon-user"></i>����� ������</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/console/pcflow") ? ' class="active"' : '')?>><a href="/console/pcflow"><i class="icon-random"></i>�������� ��</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/reports") ? ' class="active"' : '')?>><a href="/reports">����������� ������</a></li>
		<? if (in_array($this->session->userdata("canSee"), array(159, 255, 389, 153, 600))) { ?>
	<li<?=(($_SERVER["PATH_INFO"] == "/reports/timetable") ? ' class="active"' : '')?>><a href="/reports/timetable"><i class="icon-calendar"></i>������ ����� �������� ������� <!-- <span class="badge badge-warning">New!</span> --></a></li>
	<? } ?>
	<? if ($this->session->userdata("rank") == 1 || $this->session->userdata("admin_id") == 16 || $this->session->userdata("admin_id") == 26 || $this->session->userdata("admin_id") == 17) { ?>
	<li class="nav-header">���������� ��� ������ ��</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/uvmr") ? ' class="active"' : '')?>><a href="/uvmr"><i class="icon-book"></i>����������� ������ ��</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/uvmr/passport") ? ' class="active"' : '')?>><a href="/uvmr/passport"><i class="icon-book"></i>�������� ���</a></li>
	<? } ?>

	<? if ($this->session->userdata("rank") == 1) { ?>
	<li class="nav-header">���������� ��� ������ ��</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/network") ? ' class="active"' : '')?>><a href="/network">��������� ����</a></li>
	<li class="nav-header">���������� ����������</li>

	<li<?=(($_SERVER["PATH_INFO"] == "/licenses/statistics") ? ' class="active"' : '')?>><a href="/licenses/statistics"><i class="icon-certificate"></i>������ ��������</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/licenses/contents") ? ' class="active"' : '')?>><a href="/licenses/contents"><i class="icon-certificate"></i>������ �� ��������</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/licenses/usage") ? ' class="active"' : '')?>><a href="/licenses/usage"><i class="icon-certificate"></i>������������� ��������</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/licenses/user") ? ' class="active"' : '')?>><a href="/licenses/user"><i class="icon-certificate"></i>�������� �� ��</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/licenses/servers") ? ' class="active"' : '')?>><a href="/licenses/servers"><i class="icon-certificate"></i>�������� �� ��������</a></li>

	<? } ?>
	<? if ($this->session->userdata("rank") == 1 || $this->session->userdata("admin_id") == 38) { ?>
	<li class="nav-header">���</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/arm") ? ' class="active"' : '')?>><a href="/arm">������������������ ��</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/arm/invunits") ? ' class="active"' : '')?>><a href="/arm/invunits">����������� �������</a></li>
	<li<?=(($_SERVER["PATH_INFO"] == "/arm/warehouse") ? ' class="active"' : '')?>><a href="/arm/warehouse">�����</a></li>
	<? } ?>
	<li class="nav-header">�������������� �������</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/bids") ? ' class="active"' : '')?>><a href="/bids"><strong>���������� ������</strong></a></li>
	
	<li class="nav-header">�����������</li>



	<? if ($this->session->userdata("rank") == 1) { ?>
	<li<?=(($_SERVER["PATH_INFO"] == "/reference/staff") ? ' class="active"' : '')?>><a href="/reference/staff">���������</a></li>
	
	<li<?=(($_SERVER["PATH_INFO"] == "/reference/depts") ? ' class="active"' : '')?>><a href="/reference/depts">�������������</a></li>
	
	<li<?=(($_SERVER["PATH_INFO"] == "/reference/locations") ? ' class="active"' : '')?>><a href="/reference/locations">���������</a></li>
	
	<li<?=(($_SERVER["PATH_INFO"] == "/integrity") ? ' class="active"' : '')?>><a href="/integrity">����������� ������</a></li>
	<? } ?>
	
	<? if ($this->session->userdata("rank") == 1 || $this->session->userdata("admin_id") == 4) { ?>
	<li<?=(($_SERVER["PATH_INFO"] == "/reference/resources") ? ' class="active"' : '')?>><a href="/reference/resources">�������</a></li>
	<? } ?>

	<? if ($this->session->userdata("rank") == 1) { ?>
	<li class="nav-header">������ � �������</li>
	<li<?=(($_SERVER["PATH_INFO"] == "/reference/useraccess") ? ' class="active"' : '')?>><a href="/reference/useraccess">������������ MLan-Console</a></li>
	<? } ?>
</ul>