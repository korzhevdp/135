<form method="post" action="/uvmr/passport" id="userSForm" class="form-horizontal">
	<div class="control-group">
		<label class="control-label span2">�����</label>
		<div class="controls">
			<input class="span8" name="userid" ID="userid" maxlength="60" placeholder="������� ��� ����� / ��� ���������� ������������ " type="text" value="<?=(isset($filter)) ? iconv('UTF-8', 'Windows-1251' , urldecode($filter)) : '';?>">
			<span class="muted">������� <span id="foundCounter">00</span> �������</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2">&nbsp;</label>
		<div class="controls">
			<select multiple size=5 class="span8" name="userSelector" ID="userSelector">
			</select>
			<button type="submit" class="btn btn-info btn-small">��������</button>
			<?=($this->session->userdata("is_sup") || (int) $this->session->userdata("rank") == 1 ) ? '<button type="button" class="btn btn-small" id="usermerge">����������</button>' : ''; ?>
		</div>
	</div>
</form>

<hr>

<h3><?=(isset($fio)) ? $fio : "";?></h3>
<h4><small><?=(isset($dn)) ? $dn : "";?> - <?=(isset($address)) ? $address : "";?></small></h4>

<hr>
<h4>������� �������:</h4>
<?=(isset($pcs)) ? $pcs : "";?>

<hr>
<h4>������������ �������������� �������</h4>
<?=(isset($resources)) ? $resources : "";?>

<hr>
<h4>������������ ������� � ������ ��</h4>
<?=(isset($os)) ? $os : "";?>

<hr>
<h4>������������� ����������� �����������</h4>
<?=(isset($software)) ? $software : "";?>
<script type="text/javascript" src="/jscript/users.js"></script>
<div style="margin-bottom:80px;">&nbsp;&nbsp;</div>
