<h2>�������� ����������� <small>� ����</small></h2><hr>
<form method="post" action="/console/pcflow" id="userSForm" class="form-horizontal span12">
	<div class="control-group">
		<label class="control-label span1">�����</label>
		<div class="controls span11">
			<input class="span9" name="userid" ID="userid" maxlength="60" placeholder="������� ��� ����� / ��� ���������� ������������ " type="text" value="<?=$filter;?>">
			<span class="muted">������� <span id="foundCounter">00</span> �������</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span1">&nbsp;</label>
		<div class="controls span11">
			<select multiple size=5 class="span9" name="userSelector" ID="userSelector">
			</select>
			<button type="submit" class="btn btn-info btn-small">��������</button>
		</div>
	</div>
</form>
<label for="hia" style="margin-left:90px;margin-bottom:20px;cursor:pointer;" id="hideinactive">&nbsp;<input type="checkbox" style="margin-top:-3px;" id="hia">&nbsp;&nbsp;&nbsp;������ ���������� ����������</label>
