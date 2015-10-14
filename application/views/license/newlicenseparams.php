<h3>����� �������� <a href="/licenses/statistics" class="btn btn-info pull-right" style="margin-bottom:10px;">� ������ ��������</a></h3>
<table class="table table-bordered table-condensed table-striped table-hover">
<tr>
	<td><a href="/licenses/statistics/<?=(isset($id)) ? $id : "";?>.'"><b><?=$lname;?></b></a><br><?=$number;?><br><small class="muted">�� <?=$issue_date;?></small></td>
	<td><?=$purchase_date;?></td>
	<td><?=$purchase_info;?><br><?=$program;?><br><small class="muted"><?=$rname;?></small></td>
	<td>
		<?=$stat1?> <?=$stat2?>
	</td>
</tr></table>
<form class="form-horizontal" method="POST" action="/licenses/add_new_license/<?=$lid;?>" style="margin-bottom:70px;">
	<div class="control-group">
		<label class="control-label span2" for="lnum">� ��������</label>
		<div class="controls span10">
			<input type="text" name="lnum" id="lnum" class="span12" placeholder="00000000" value="<?=$number;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="licr">���������</label>
		<div class="controls span10">
			<select name="licr" id="licr" class="span11">
				<option value="0"> - �������� ���������� - </option>
				<?=$licr;?>
			</select>
			<button type="button" class="btn" id="button-addlicr" title="�������� ����������">+</button>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="dati">���� �������</label>
		<div class="controls span10">
			<input type="text" id="dati" name="dati" class="span12 wDate" placeholder="00.00.0000" value="<?=$issue_date;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="datp">���� �������</label>
		<div class="controls span10">
			<input type="text" id="datp" name="datp" class="span12 wDate" placeholder="00.00.0000" value="<?=$purchase_date;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="resl">��������</label>
		<div class="controls span10">
			<select name="resl" id="resl" class="span11">
				<option value="0"> - �������� ��������� - </option>
				<?=$resl;?>
			</select>
			<button type="button" class="btn" id="button-addresl" title="�������� ���������">+</button>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="prog">���������</label>
		<div class="controls span10">
			<input type="text" id="prog" name="prog" class="span12" placeholder="00000000" value="<?=$program;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="info">����������</label>
		<div class="controls span10">
			<textarea id="info" name="info" class="span12" placeholder="������ ��������, ������� ���������; ���������: ������������� ����������, ���������, ������, ������-������" cols="5" rows="5"><?=$purchase_info;?></textarea>
		</div>
	</div>
	<div class="control-group pull-right" style="margin-top:20px;">
		<a class="btn btn-warning" href="/licenses/verify_license/<?=$lid;?>" title="������ �� ��������� ��������� �������� ������� �� ������">�����������</a>&nbsp;&nbsp;
		<button type="submit" class="btn btn-primary">��������� ��������</button>
	</div>
	<input type="hidden" name="license_id" value="<?=$lid;?>">
</form>
<hr>

<div style="height:20px;">
	<button id="button-addPOtoset" class="btn pull-right">�������� ����� �� � ��������</button>
</div>

<div class="well well-small" style="margin-top:30px;">������ �������� ������������ ����������� � ������� �������.<br>�������� ����� �������� � ���� ��������� ����������� ����� ��, �������� ���������������� ������������ ������� � ��������� ��. �������������, ��� ����� �������� �� ������ ��� ���������� ������� ��.<br>
	����� �� - ��� ������������ ����������� ��������� ������� ����� ���� ���������� �� ����� �������� �� ���������� �� �� �������� "����������". ��������, �� �������� �� Windows 8.1 ����� ���������� Windows 8, Windows 7 � Windows Vista, �� �� ����� ����������, ���������������� ���������. ���������� ����������� �����������, �� ������� �������� ������� �� ���������� ������ ���� ������������ � �������� ������ ������. 
	<br> � ��������� ������� �������� �� � ��������� ��������� ����������. ����� ����� �� �� ������ ����� ���� �������� ��������.<br><br> ��� ��������� �� ������ ����� ������ ������ �� ���������� ��������� ����������� ����������� ��� ����� ������.<br>����� ����� ���� ����� ������ �������. <br>
</div>

<h4>��������� ��������</h4>

<?=(isset($sets)) ? $sets : "";?>
<!-- ������ ���������� ���������� -->
<div id="LicLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="LicLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">�������� ���������� ��</h3>
	</div>
	<div class="modal-body">
		<form method="post" id="form3" action="/licenses/add_licensiar" class="form-horizontal">
			<div class="control-group">
				<label class="control-label">�������� ����������</label>
				<div class="controls">
					<input type="text" name="licr_name" class="span12">
				</div>
			</div>
			<input type="hidden" name="redirect" value="<?=(isset($id)) ? $id : "";?>">
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
		<button class="btn btn-primary" aria-hidden="true" id="LicrModalSubmit" title="��������� �������� �� ����">������</button>
	</div>
</div>

<!-- ������ ���������� ��������� -->
<div id="ReslLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ReslLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">�������� ��������� ��</h3>
	</div>
	<div class="modal-body">
		<form method="post" id="form2" action="/licenses/add_reseller" class="form-horizontal">
			<div class="control-group">
				<label class="control-label">�������� ���������</label>
				<div class="controls">
					<input type="text" name="resl_name" class="span12">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">����� ���������</label>
				<div class="controls">
					<input type="text" name="resl_addr" class="span12">
				</div>
			</div>
			<input type="hidden" name="redirect" value="<?=(isset($id)) ? $id : "";?>">
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
		<button class="btn btn-primary" aria-hidden="true" id="reslModalSubmit" title="��������� �������� �� ����">������</button>
	</div>
</div>

<!-- ������ ���������� �� � ����� -->
<div id="SetLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ReslLabel" aria-hidden="true">
	<form method="post" action="/licenses/addpotoset" class="form-horizontal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">�������� �� � �����</h3>
		<div>���������� ��������������� �����: <input type="text" id="po_num" name="po_num" value="0"><input type="hidden" name="lid" value="<?=$id;?>"></div>
		<div>��������� ����� ��������: <input type="text" id="startnum" name="startnum" value="0"></div>
	</div>

	<div class="modal-body">
		
		<div id="addsoft">
			������ ��������� ����� �����
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
		<button class="btn btn-primary" aria-hidden="true" type="submit" title="��������� ��������� ���� �� � �����">������</button>
	</div>
	</form>
</div>
<script type="text/javascript" src="/jscript/lsmc.js"></script>

<script type="text/javascript">
<!--
$(function($){
	$.datepicker.regional['ru'] = {
		closeText: '�������',
		prevText: '<<',
		nextText: '>>',
		currentText: '�������',
		monthNames: ['������', '�������', '����', '������', '���', '����', '����', '������', '��������', '�������', '������', '�������'],
		monthNamesShort: ['���', '���', '���', '���', '���', '���', '���', '���', '���', '���', '���', '���'],
		dayNames: ['�����������', '�����������', '�������', '�����', '�������', '�������', '�������'],
		dayNamesShort: ['���', '���','���','���','���','���','���'],
		dayNamesMin: ['��','��','��','��','��','��','��'],
		weekHeader: '���',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$(".wDate").datepicker($.datepicker.regional['ru']);
	$(".wDate").datepicker( "option", "changeYear", true);
});
//-->
</script>