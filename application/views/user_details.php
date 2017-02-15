<style type="text/css">
	.pre-label{
		display:table-cell;
		width:140px !important;
		margin-bottom: 2px;
	}
	.post-label{
		display:table-cell;
		text-align:left !important;
		width:170px !important;
	}
	select.long{
		width:554px;
	}
	select.short{
		width:305px;
	}
	select.vshort{
		width:99px;
	}
	select{
		height:31px;
	}
	input.long{
		width:554px;
	}
	#air,
	#bir,
	#sman,
	#servop,
	#superv{
		margin-left:  170px;
		margin-right: 10px;
		margin-top:   -2px;
	}
	#c_host,
	#c_login {
		cursor: pointer;
	}

</style>

<form method="post" action="/admin/users" id="userSForm" class="form-horizontal">
	<div class="control-group">
		<label class="control-label" style="width:120px;">�����</label>
		<div class="controls">
			<input style="width:420px;margin-bottom:5px;" name="userid" ID="userid" maxlength="60" placeholder="������� ��� ����� / ��� ���������� ������������ " type="text" value="<?=($filter) ? $filter : '';?>">
			<label style="display:inline;"><input type="checkbox" id="withFired" style="margin-top:-3px" checked="checked"> �������� ���������</label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2">&nbsp;</label>
		<div class="controls">
			<select multiple size=5 style="width:434px;margin-bottom:5px;" name="userSelector" ID="userSelector"></select>
			<button type="submit" class="btn btn-info">��������</button>
			<?=((int) $this->session->userdata("rank") == 1 ) ? '<button type="button" class="btn btn" id="usermerge">����������</button>' : ''; ?>
		</div>
	</div>
</form>

<h3 class="<?=(($fired) ? "muted" : "");?>" id="fio_plate">
	<?=($id) ? implode(array($name_f,$name_i,$name_o)," ") : "�� ������ ������������" ;?>&nbsp;
	<small style="margin-left:20px;" title="����� ������������ � ���� ������">#<?=$id;?>&nbsp;&nbsp;&nbsp;&nbsp;<?=(($fired) ? "������(�)" : "");?></small>
</h3>
<? if($id) { ?>
	<ul class="nav nav-tabs">
		<li class="<?=($page == 1) ? "active" : "";?>" title="���������� � ������������"><a href="#tab1" data-toggle="tab">� ������������</a></li>
		<li class="<?=($page == 2) ? "active" : "";?>" title="����������� � �������������� �������� ����"><a href="#tab2" data-toggle="tab">�������������� �������</a></li>
		<li class="<?=($page == 3) ? "active" : "";?>" title="������� ������ � ����������"><a href="#tab3" data-toggle="tab">������� ������</a></li>
		<li class="<?=($page == 4) ? "active" : "";?>" title="����������"><a href="#tab4" data-toggle="tab">����������� ���</a></li>
		<li class="<?=($page == 5) ? "active" : "";?>" title="��������"><a href="#tab5" data-toggle="tab">��������</a></li>
	</ul>
	<div class="tab-content" style="margin-bottom:60px;">
		<div class="tab-pane <?=($page == 1) ? "active" : "";?>" id="tab1">
			<form method=post action="/admin/usave" id="userform1" class="form-horizontal">
			<div class="control-group">
				<label class="control-label span2">�������</label>
				<div class="controls">
					<input class="span12" name="sname" ID="sname" onkeyup="validate('rtext',this.id);" maxlength="60" type="text" value="<?=$name_f;?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">���</label>
				<div class="controls">
					<input class="span12" name="name" ID="name" onkeyup="validate('rtext', this.id);" maxlength="60" type="text" value="<?=$name_i;?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">��������</label>
				<div class="controls">
					<input class="span12" name="fname" ID="fname" onkeyup="validate('rtext',this.id);" maxlength="60" type="text" value="<?=$name_o;?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">�������������</label>
				<div class="controls">
					<select class="span12" name="dept" id="dept">
						<option value=0>--��������--</option>
						<?=$dept;?>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">���������</label>
				<div class="controls">
					<select  class="span10" name="staff" ID="staff">
						<option value=0>--��������--</option>
						<?=$staff_id;?>
					</select>
					<label for="io" style="cursor:pointer; float:left;line-height:28px;" class="span2"><input type="checkbox" style="margin-top:-4px;" id="io" name="io" value="1" <?=$is_io;?>> �.�.</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">�����</label>
				<div class="controls">
					<select id="office" name="office" class="span12">
						<option value=0>--��������--</option>
						<?=$location?>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">Email</label>
				<div class="controls">
					<input class="span12" name="email" id="email" type="text" readonly="readonly" value="<?=$email;?>">
				</div>
			</div>
			
			<div class="control-group" style="margin-bottom:10px;">
				<label class="control-label span2">�������</label>
				<div class="controls">
					<textarea name="memo" id="memo" class="span12" cols="4" rows="4"><?=$memo;?></textarea>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label span2">�������</label>
				<div class="controls">
					<input class="span12" name="phone" id="phone" onkeyup="validate('num', 'phone');" maxlength="25" type="text" value="<?=$phone;?>">
				</div>
			</div>

			<div class="control-group" style="margin-bottom:20px;">
				<label class="control-label span2">��������</label>
				<div class="controls">
					<select class="span12" name="service" id="service" <?=($supchange) ? '' : ' disabled="disabled"'; ?>>
						<?=$service;?>
					</select>
					<?=($supchange) ? '' : '<input type="hidden" name="service" id="service" value="'.$serviceman.'">';?>
				</div>
			</div>
			<button type="button" class="btn btn-<?=(($fired) ? "inverse" : "info");?> <?=($saveable) ? "fireSw" : 'disabled' ;?>" id="fireSw" ref="<?=$id?>" style="width:100px;"><?=(($fired) ? "������(�)" : "�������");?></button>
			<a href="/admin/takeuser/<?=$id;?>/<?=$this->session->userdata('base_id');?>" class="btn btn-warning" <?=(((int) $this->session->userdata("rank") == 1) ? ' disabled="disabled"' : '');?> title="������� ���� ��������� ����� ������������">������� ������������</a>
			<button type="submit" class="btn btn-primary" name="saveID" value="<?=$id?>" id="generalSave" <?=($saveable) ? "" : 'disabled="disabled"' ;?>>���������</button>
			<!-- <a href="/admin/audit/<?=$id;?>" class="btn btn-info" target="_blank">������� ���������</a> -->
			</form>
		</div>

		<div class="tab-pane <?=($page == 2) ? "active" : "";?>" id="tab2">
			<h6>������� ���������� �������������� ��������</h6>

			<form method="post" id="Qadd" action="/admin/quickadd">
				<input type="hidden" form="Qadd" name="quser" value="<?=$id;?>">
				<div class="control-group">
					<select name="quick_reg" form="Qadd" class="span6" id="quick_reg">
						<option value=0>�������� �� ������</option>
						<optgroup label="����" style="padding:5px;">
							<option value=7>������ ����</option>
						</optgroup>
						<optgroup label="�������� � ����������� �����" style="padding:5px;">
							<option value=1>��������</option>
							<option value=2>����������� �����</option>
							<option value=3>�������� + ����������� �����</option>
						</optgroup>
						<optgroup label="���" style="padding:5px;">
							<option value=8>��� �� 2.0</option>
						</optgroup>
						<optgroup label="������ ����� �������� �������" style="padding:5px;">
							<option value=9>������ ����� �������� �������</option>
						</optgroup>
						<optgroup label="����������� �������" style="padding:5px;">
							<option value=10>����������� ������� (Token)</option>
						</optgroup>
					</select>
					<button type="submit" form="Qadd" class="btn btn-small btn-info" style="margin-top:-8px;margin-left:10px;">�������� ���������������� ������</button>
				</div>
			</form>
			
			<form method="post" action="/events/makeevent" accept-charset="UTF-8">
				<div id="modalEvent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
					<div class="modal-header">
						<h4>�������� ��������� �������� <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></h4>
					</div>
					<div class="modal-body">
						������������: <?=($id) ? implode(array($name_f, $name_i, $name_o), " ") : "�� ������ ������������" ;?>
							<input type="hidden" id="eventItemID" name="itemID" class="eventItemID" value=""><br>
							�������� �: <input type="text" id="startTime" name="startTime" class="wDate" value="<?=date("d.m.Y");?>"><br>
							<select name="eventAction[]" multiple="multiple" style="width:500px;" size=4>
								<option value="������������� ���������">������������� ���������</option>
								<option value="����������� ������ ��������">����������� ������ ��������</option>
								<option value="��������� ������� ���">��������� ������� ���</option>
								<option value="��������� ��������� ������� ����������� �����" class="mailEvent">��������� ��������� ������� ����������� �����</option>
								<option value="��������� ��������� ������-�������">��������� ��������� ������-�������</option>
							</select>
							<br>�����������<br>
							<textarea name="eventAnnotation" class="eventAnnotation" rows="6" cols="7" style="width:500px;height:70px;"></textarea>
					</div>
					<div class="modal-footer" style="text-align:right">
						<button type="submit" class="btn btn-primary">���������</button>
					</div>
				</div>
			</form>

			<div id="modalBackEvent" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
				<form method="post" action="/events/makebackevent" accept-charset="UTF-8">
					<div class="modal-header">
						<h4>������� ����� ��������� <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></h4>
					</div>
					<div class="modal-body">
						������������: <?=($id) ? implode(array($name_f,$name_i,$name_o)," ") : "�� ������ ������������" ;?>
							<input type="hidden" name="itemID" id="eventBackItemID" class="eventItemID" value="">
							�������� �: <input type="text" id="startTime2" name="startTime" class="wDate" value="<?=date("d.m.Y");?>"><br>
							<select name="eventAction[]" multiple="multiple" style="width:500px;" size=4>
								<option value="������� ��������� �� ������">������� ��������� �� ������</option>
								<option value="������ ����� ������ e-mail">������ ����� ������ e-mail</option>
							</select>
							<br>�����������<br>
							<textarea name="eventAnnotation" class="eventAnnotation" rows="6" cols="7" style="width:500px;height:70px;"></textarea>
					</div>
					<div class="modal-footer" style="text-align:right">
						<button type="submit" class="btn btn-primary">���������</button>
					</div>
				</form>
			</div>
			<form method="post" class="form-horizontal" action="">
				<?=$resources;?>
			</form>
		</div>

		<div class="tab-pane <?=($page == 3) ? "active" : "";?>" id="tab3">

			<div class="input-prepend control-group">
				<span class="add-on pre-label">����� IP</span>
				<input type="text" name="ip"  id="ip" class="long" maxlength="60" readonly="readonly" value="">
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">MAC</span>
				<input type="text" name="mac" id="mac" class="long" maxlength="60" readonly="readonly" value="<?=$mac;?>">
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�����&nbsp;&nbsp;<i class="icon-refresh" id="c_host" ref="login"></i></span>
				<input type="text" name="login" id="login" class="long" maxlength="60" form="userform1" value="<?=$login;?>">
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Host <i class="icon-refresh" id="c_host" ref="host"></i></span>
				<input type="text" name="host" id="host" class="long" form="userform1" value="<?=$host;?>">
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�������</span>
			</div>

			<label><input type="checkbox" id="air"    name="air"    form="userform1" value="1" <?=$air;?>>������������� ��</label>
			<label><input type="checkbox" id="bir"    name="bir"    form="userform1" value="1" <?=$bir;?>>������������� ���</label>
			<label><input type="checkbox" id="sman"   name="sman"   form="userform1" value="1" <?=$sman;?>>�������</label>
			<label><input type="checkbox" id="superv" name="superv" form="userform1" value="1" <?=$superv;?>>������������ ���������</label>
			<label><input type="checkbox" id="servop" name="servop" form="userform1" value="1" <?=$servop;?>>�������� ������������� �����</label><br><br>

			<button type="button" class="btn btn-<?=(($fired) ? "inverse" : "info");?> <?=($saveable) ? "fireSw" : 'disabled' ;?>" id="fireSw" ref="<?=$id?>" style="width:100px;"><?=(($fired) ? "������(�)" : "�������");?></button>
			<a href="/admin/takeuser/<?=$id;?>/<?=$this->session->userdata('base_id');?>" class="btn btn-warning" <?=(((int) $this->session->userdata("rank") == 1) ? ' disabled="disabled"' : '');?> title="������� ���� ��������� ����� ������������">������� ������������</a>
			<button type="submit" class="btn btn-primary " name="saveID" value="<?=$id?>" form="userform1" id="generalSave" <?=($saveable) ? "" : 'disabled="disabled"' ;?>>���������</button>
			<!-- <a href="/admin/audit/<?=$id;?>" class="btn btn-info" target="_blank">������� ���������</a> -->
		</div>

		<div class="tab-pane <?=($page == 4) ? "active" : "";?>" id="tab4">
			������&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="pcFilter" placeholder="�������� �� �� �����">
			<ul class="pclist">
				<?=$arm['pclist'];?>
			</ul>	
			<ul class="pcused">
				<?=$arm['pcused'];?>
			</ul>
			<button class="hide btn btn-info btn-large" style="margin-bottom:20px;clear:both;float:left;" id="hideallarm">������</button>
			<?=$arm['pcconfs'];?>
		</div>

		<div class="tab-pane <?=($page == 5) ? "active" : "";?>" id="tab5">
			<label class="checkbox" style="cursor:pointer;"><input type="checkbox" id="inactiveToggler">&nbsp;&nbsp;&nbsp;���������� ���������� ��������</label>
			<?=$arm['licenses'];?>
			<?if($saveable){?>
			<!-- �������� ���������� ���������� -->
			<!-- ������ ������ �������� �� ���� �� �������� ���������� �����-->
			<div id="modalRes" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
			<div id="modalRes2" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
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
			<div id="modalRes3" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 id="myModalLabel3">��������� ������������ �������� <small>�� ����</small></h3>
					�������� ������: <input type="text" id="ds32" size="44" placeholder="office 2012 ��� KQBKK">
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-hover table-condensed">
					<tr>
						<th></th>
						<th>��������</th>
						<th>���</th>
						<th>�������</th>
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

			<form method="post" id="licenseform" action="dummyurl" style="margin-bottom:80px;">
				<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
				<input type="hidden" name="userid" value="<?=$userid;?>">
			</form>
			<!-- �������� ���������� ���������� -->
			<?}?>
		</div>
	</div>
<? } else { ?>
	������ ������ ������������� ���� ��������� ����
<?}?>

<!-- ������ ����������� ���������� �� ������ -->
<div id="modalRes4" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 id="myModalLabel4">����������� ���������� �� ������ #<span id="bidsInfo"></span></h4>
	</div>
	<div class="modal-body">
		<div id="resCollection4" class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader" style="margin-left:45%">
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="�������">�������</button>
	</div>
</div>

<!-- <button type="button" class="btn btn-primary button-ask">���������</button> -->
<script type="text/javascript" src="/jscript/lsmc.js"></script>
<script type="text/javascript" src="/jscript/users.js"></script>