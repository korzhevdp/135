<!-- �������� ���������� ���������� -->
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
		�������� ������: <input type="text" id="ds32" size="44">
	</div>
	<div class="modal-body">
		<table class="table table-bordered table-hover table-condensed">
		<tr>
			<th></th>
			<th class="span9">��������</th>
			<th class="span2">�������</th>
		</tr>
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