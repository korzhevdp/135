<style type="text/css">
	.col1 { width: 140px; }
	.col2 { }
	.col3 { width: 110px;}
	.col4 { width: 6px;}
</style>
<h3>���������.&nbsp;&nbsp;&nbsp;<small>����� �������</small></h3>

<form method="post" action="#" class="form-horizontal">

	<div class="well well-small span12 container-fluid" style="margin-left:0px;margin-bottom:80px;">

		<div class="control-group">
			<label for="login" class="control-label span2">������������ ����</label>
			<div class="controls">
				<select name="candidate" id="candidate" class="span10">
					<?=$candidates;?>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label for="login" class="control-label span2">�����</label>
			<div class="controls">
				<input type="text" name="login" id="login" class="span3" placeholder="������� ����� �����" value="<?=$user;?>">&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="text" name="description" id="login" class="span7" placeholder="������� ��������" value="<?=$description;?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label span2">&nbsp;&nbsp;</label>
			<div class="controls">
				<label class="checkbox inline" style="cursor:pointer;"><input type="checkbox" name="rank" id="rank" style="margin-top:2px;" <?=$adminrank?>> �������������</label>
			</div>
		</div>

		<div class="control-group">
			<label for="fn" class="control-label span2">����� ������</label>
			<div class="controls">
				<input type="password" name="newpassword" id="newpassword" class="span10" value="">
				<button type="submit" class="btn btn-info btn-small" value="1" name="newPass">������</button>
			</div>
		</div>

		<div class="control-group">
			<label for="followers" class="control-label span2">������������</label>
			<div class="controls">
				<select name="supervisor" id="supervisor" class="span10">
					<?=$sups?>
				</select>
			</div>
		</div>
		<input type="hidden" name="userToSave" value="<?=$this->input->post("showUser")?>"><br>
		<button type="submit" class="btn offset5" value="1" name="newAdmin">������� ������ ������������</button>
		<button type="submit" class="btn btn-primary" value="1" name="saveAdmin">���������</button>
	</div>

	<table class="table table-bordered table-condensed table-hover table-container">
	<tr>
		<th class="col1">�����</th>
		<th class="col2">������������</th>
		<th class="col3">������� �����</th>
		<th class="col3">����</th>
		<th class="col4"></th>
	</tr>
	<?=$admin_list;?>
	</table>
</form>