<ul class="nav nav-list">
	<li<?=(($_SERVER["REQUEST_URI"] == "/") ? ' class="active"' : '')?>><strong><a href= "/">����� ����������</a></strong></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/events") ? ' class="active"' : '')?>><a href="/events"><i class="icon-envelope"></i>��������� - <?=$tickets;?></a></li>
	<li class="nav-header">�������</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/admin/users") ? ' class="active"' : '')?>><a href= "/admin/users"><i class="icon-user"></i>������������ ����</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/console") ? ' class="active"' : '')?>><a href="/console"><i class="icon-user"></i>����� ������</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/console/pcflow") ? ' class="active"' : '')?>><a href="/console/pcflow"><i class="icon-random"></i>�������� ��</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/reports") ? ' class="active"' : '')?>><a href="/reports">����������� ������</a></li>
	<? if (in_array($this->session->userdata("canSee"), array(159, 255, 389, 2187))) { ?>
	<li<?=(($_SERVER["REQUEST_URI"] == "/reports/timetable") ? ' class="active"' : '')?>><a href="/reports/timetable"><i class="icon-calendar"></i>������ ����� �������� ������� <!-- <span class="badge badge-warning">New!</span> --></a></li>
	<? } ?>
	<? if (
		   $this->session->userdata("rank")     == 1
		|| $this->session->userdata("admin_id") == 16
		|| $this->session->userdata("admin_id") == 26
		|| $this->session->userdata("admin_id") == 17
	) { ?>
	<li class="nav-header">���������� ��� ������ ��</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/uvmr") ? ' class="active"' : '')?>><a href="/uvmr"><i class="icon-book"></i>����������� ������ ��</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/uvmr/passport") ? ' class="active"' : '')?>><a href="/uvmr/passport"><i class="icon-book"></i>�������� ���</a></li>
	<? } ?>


	<li<?=(($_SERVER["REQUEST_URI"] == "/reports/esia") ? ' class="active"' : '');?>><a href="/reports/esia" <?=( ($esiaWarn) ? ' style="color:red;font-weight:bold !important"' : '' );?>><i class="icon-certificate"></i>���� / ���������&nbsp;&nbsp;<?=( ($esiaWarn) ? '<i class="icon-exclamation-sign"></i>' : '' );?></a></li>
	<? if ($this->session->userdata("rank") == 1) { ?>
	<li class="nav-header">���������� ��� ������ ��</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/network") ? ' class="active"' : '')?>><a href="/network">��������� ����</a></li>
	<li class="nav-header">���������� ����������</li>

	<li<? if ($_SERVER["REQUEST_URI"] == "/licensestats")	{ ?> class="active" <? } ?>><a href="/licensestats"><i class="icon-certificate"></i>������ ��������</a></li>
	<li<? if ($_SERVER["REQUEST_URI"] == "/licensestats/contents")		{ ?> class="active" <? } ?>><a href="/licensestats/contents"><i class="icon-certificate"></i>������ �� ��������</a></li>
	<li<? if ($_SERVER["REQUEST_URI"] == "/licensestats/usage")			{ ?> class="active" <? } ?>><a href="/licensestats/usage"><i class="icon-certificate"></i>������������� ��������</a></li>
	<li<? if ($_SERVER["REQUEST_URI"] == "/licensestats/user")			{ ?> class="active" <? } ?>><a href="/licensestats/user"><i class="icon-certificate"></i>�������� �� ��</a></li>
	<li<? if ($_SERVER["REQUEST_URI"] == "/licensestats/servers") { ?> class="active"<? } ?>><a href="/licensestats/servers"><i class="icon-certificate"></i>�������� �� ��������</a></li>

	<? } ?>
	<? if (
		   (int)$this->session->userdata("rank")     === 1
		|| (int)$this->session->userdata("admin_id") === 38) {
	?>
	<li class="nav-header">���</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/arm") ? ' class="active"' : '')?>><a href="/arm">������������������ ��</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/arm/invunits") ? ' class="active"' : '')?>><a href="/arm/invunits">����������� �������</a></li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/arm/warehouse") ? ' class="active"' : '')?>><a href="/arm/warehouse">�����</a></li>
	<? } ?>
	<li class="nav-header">�������������� �������</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/bids") ? ' class="active"' : '')?>><a href="/bids"><strong>���������� ������</strong></a></li>
	
	<li class="nav-header">�����������</li>

	<? if ($this->session->userdata("rank") == 1) { ?>
	<li<?=(($_SERVER["REQUEST_URI"] == "/reference/staff") ? ' class="active"' : '')?>><a href="/reference/staff">���������</a></li>
	
	<li<?=(($_SERVER["REQUEST_URI"] == "/reference/depts") ? ' class="active"' : '')?>><a href="/reference/depts">�������������</a></li>
	
	<li<?=(($_SERVER["REQUEST_URI"] == "/reference/locations") ? ' class="active"' : '')?>><a href="/reference/locations">���������</a></li>
	
	<li<?=(($_SERVER["REQUEST_URI"] == "/integrity") ? ' class="active"' : '')?>><a href="/integrity">����������� ������</a></li>
	<? } ?>
	
	<? if ($this->session->userdata("rank") == 1 || $this->session->userdata("admin_id") == 4) { ?>
	<li<?=(($_SERVER["REQUEST_URI"] == "/reference/resources") ? ' class="active"' : '')?>><a href="/reference/resources">�������</a></li>
	<? } ?>

	<? if ($this->session->userdata("rank") == 1) { ?>
	<li class="nav-header">������ � �������</li>
	<li<?=(($_SERVER["REQUEST_URI"] == "/reference/useraccess") ? ' class="active"' : '')?>><a href="/reference/useraccess">������������ MLan-Console</a></li>
	<? } ?>
</ul>