<h3>�������� �� ������������ �����������&nbsp;&nbsp;&nbsp;<small></small></h3>
<form method="post" class="form-horizontal" action="">
	<div class="control-group">
		<label class="control-label span2">�������������</label>
		<div class="controls">
			<select name="dep_id" class="span10">
				<?=$depts;?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2">������������</label>
		<div class="controls">
			<select name="userid" class="span10">
				<?=$users;?>
			</select>
			<button type="submit" class="btn btn-mini btn-info" style="margin-left:10px;">��������</button>
		</div>
	</div>
</form>
<label class="checkbox" style="cursor:pointer;"><input type="checkbox" id="inactiveToggler">&nbsp;&nbsp;&nbsp;���������� ���������� ��������</label>

<?=$licenselist;?>

<!-- ������ ������ �������� �� ���� �� �������� ���������� �����-->
<div id="modalRes" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel1">����� �� ���� �������� <small>�� ��������� ���������</small></h3>
	</div>
	<div class="modal-body">
		<div id="resCollection" class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader" style="margin-left:45%">
		</div>
	</div>
	<div class="modal-footer">
		<form method="post" id="getform" action="/licenses/takeitem">
			<input type="hidden" name="itemid" id="itemid">
			<input type="hidden" name="akl" id="akl">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk" title="����� �������� �� ���� �������� � �������� ����� ������������" disabled>������</button>
		</form>
	</div>
</div>

<!-- ������ ���������� -->
<div id="modalRes2" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">��������� ������� <small> �� ����������� ������� ��������</small></h3>
	</div>
	<div class="modal-body">
		<div id="resCollection2" class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader" style="margin-left:45%">
		</div>
	</div>
	<div class="modal-footer">
		<form method="post" id="form2" action="/licenses/bideitem">
			<input type="hidden" name="itemid" id="itemid2">
			<input type="hidden" name="akl" id="akl2">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid2" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk2" title="��������� ������ ��������" disabled>������</button>
		</form>
	</div>
</div>

<!-- ������ ���������� ������������ �������� -->
<div id="modalRes3" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">��������� ������������ �������� <small>�� ����</small></h3>
		�������� ������: <input id="ds32" size="44" type="text">
	</div>
	<div class="modal-body">
			<table class="table table-condensed table-bordered">
			<tbody><tr>
				<th></th>
				<th class="span9">��������</th>
				<th class="span2">�������</th>
			</tr></tbody>
			<tbody id="resCollection3"></tbody>
		</table>
	</div>
	<div class="modal-footer">
		<form method="post" id="form3" action="/licenses/orderitem">
			<input type="hidden" name="itemid" id="itemid3">
			<input type="hidden" name="akl" id="akl3">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid3" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk3" title="��������� �������� �� ����" disabled>������</button>
		</form>
	</div>
</div>

<div id="convert2Lic" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="convert2Lic" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">��������� � �������� <small>���� ������������� �� ��</small></h3>
	</div>
	<form method="post" action="/licenses/convert">
		<div class="modal-body">
		<input type="hidden" id="convName">
		<table class="table table-condensed table-bordered table-hover">
		<tbody>
			<tr>
				<td>/</td>
				<td>����������� �����������</td>
			</tr>
		</tbody>

		<tbody id="instList">
		
		</tbody>
		</table>
		</div>
	</form>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
				<button class="btn btn-primary" aria-hidden="true" id="doConv" title="��������� � ��������">��������� � ��������</button>
		</div>

</div>

<form method="post" id="licenseform" action="dummyurl" style="margin-bottom:80px;">
	<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
	<input type="hidden" name="userid" value="<?=$userid;?>">
</form>
<script type="text/javascript" src="/jscript/lsmc.js"></script>