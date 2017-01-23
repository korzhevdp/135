<link rel="stylesheet" href="/css/bidsui.css">
<h3 class="muted">������ �� �������������� �������
	<button type = "button" 
		class    = "btn btn-warning pull-right"
		id       = "getHelp"
		title    = "������">������</button>
</h3>

<div id="breadCrumbs" class="acField hide">
	<a class="stageMarker muted" href="bids">������</a>
	<i class="icon-play"></i>
	<span class="stageMarker" id="stage2">������ ������������</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted" id="stage1">�������� ������</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted" id="stage3">����� �������������� ��������</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted">������!</span>
</div>

<!-- ����� ������������ -->
<div id="popID"
	class="control-group acField hide"
	rel="popover"
	data-content="������� ����� �.�.�. � ��� ����, ����� ������� ������� ��� ����� ������������������� ������������.
	����� ����� �������� ������� ������������ � ����������� ������ � ���� ������ �������� ��� ������� ����, ���� ������� ����� &quot;��������&quot;"
	data-original-title="����� ������������"
	data-trigger="manual">

	<div class="input-prepend input-append">
		<span class="add-on pre-label">�����</span>
		<input name="userid" ID="userid" maxlength="60"
			placeholder="������� ��� ����� / ��� ���������� ������������"
			type="text"
			value="<?= ($filter) ? iconv('UTF-8', 'Windows-1251', urldecode($filter)) : ''; ?>"
			title="���������� �����, ����� ����� ������� ��� �������� ����� ����������">
		<span class="add-on post-label"><label><input type="checkbox" id="withFired" style="margin-top:3px"> �������� ���������</label></span>
	</div>

	<select size=5 ID="userSelector" title="������ �������������" class="long"></select>
	&nbsp;&nbsp;&nbsp;
	<button type="submit" id="searchUser" class="btn btn-large" title="������� ���������� �� ���������� ������������">��������</button>
</div>

<div id="userHint" class="alert alert-info acField hide">
	�������� ���� ��� ������� ������������ ��������� ���� ��������������.<br>������ ����� ���������, ����� ��������� ����� �������.
</div>
<!-- ����� ������������ -->

<h4 id="userHeader" class="acField hide">
	<span id="fioAcknowledger">���-��</span><br>
	<small id="depAcknowledger">������-��</small>
</h4>

<div id="orderContainer" class="acField hide">
	<table class="table table-striped table-hovered table-bordered" style="margin-left: 0px;width:784px;">
		<tbody>
			<tr>
				<th>�������������� ������</th>
				<th style="width:100px;">���� ������</th>
				<th style="width:110px;">����� ������</th>
				<th style="width:210px;">������</th>
				<th style="width:25px;text-align:center;vertical-align:middle;" title="�������� ����� ���� ������">
					<label for="checkAllPapers" style="cursor:pointer;"><input type="checkbox" id="checkAllPapers"></label>
				</th>
			</tr>
		</tbody>
		<tbody id="orderList"></tbody>
	</table>
</div>

<!-- ������ ������ ������������ -->
<div id="userdata" class="acField hide">
	<div id="popInfo" class="control-group" rel="popover"
		data-content="��������� ������������ ������. ���� ���������� �������� ��� ����������� - ��������� ��� ��������� ��������������� ���� �����."
		data-original-title="������ � ������������"
		data-trigger="manual">
		<form method="post" action="/bidsfactory/getpapers" id="mainform" class="form-horizontal" accept-charset="utf-8">
			<input type="hidden" name="login" id= "login" value="<?= $login; ?>">
			<input type="hidden" name="uid"   id= "uid"   value="">
			<input type="hidden" name="res"   id= "res"   value="">
			<input type="hidden" name="confs" id= "confs" value="">
			<input type="hidden" name="subs"  id= "subs"  value="">

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�������</span>
				<input class="traceable fio_login"
					id="sname" 
					name="sname"
					maxlength="60"
					placeholder="������� ������������"
					form="mainform"
					type="text"
					value="<?=$name_f; ?>"
					valid="rword"
					pref="2"
					title="��� ����� ������� ��������� ������ ��������� � �������� ������� �����.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">���</span>
				<input class="traceable fio_login" 
					id="name" 
					name="name" 
					maxlength="60"
					placeholder="��� ������������"
					form="mainform"
					type="text"
					value="<?=$name_i;?>"
					valid="rword" 
					pref="2"
					title="��� ����� ����� ��������� ������ ��������� � �������� ������� �����.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">��������</span>
				<input class="traceable fio_login" id="fname" name="fname" maxlength="60"
					placeholder="�������� ������������"
					form="mainform"
					type="text"
					value="<?=$name_o;?>"
					valid="rword" pref="2"
					title="��� ����� �������� ��������� ������ ��������� � �������� ������� �����.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�������������</span>
				<select class="traceable long"
					valid="nonzero"
					form="mainform"
					id="dept"
					name="dept"
					title="�������� ������������� �� ������������� ������. ���� ������������� ��� � ������, ��� ����� ����� ������ � ����� ��������� �������.">
					<?=$dept;?>
				</select>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">���������</span>
				<select class="traceable long"
					valid="nonzero"
					form="mainform"
					id="staff"
					name="staff"
					title="�������� ��������� �� ������������� ������. ���� ��������� ��� � ������, � ����� ����� ������ � ����� ��������� �������.">
					<?=$staff;?>
				</select>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend input-append control-group">
				<span class="add-on pre-label" style="margin-left:0px;">�����</span>
				<select name="office"
				id="office" 
				class="traceable short"
				valid="nonzero"
				form="mainform"
				title="�������� ������">
					<?= $location[0]; ?>
				</select>
				<span class="add-on pre-label">�������</span>
				<select name="office2" 
					id="office2" 
					title="�������� �������"
					form="mainform"
					class="vshort">
					<?= $location[1]; ?>
				</select>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�������</span>
				<input type="text" 
					class="traceable"
					valid="num"
					pref="6"
					id="phone"
					name="phone"
					value="<?=$phone;?>"
					form="mainform"
					title="������� ������� �������, ���� ����.">
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>
			<input type="hidden" name="esiaMailAddr" id="f_esiaMailAddr">
		</form>
	</div>
</div>
<!-- ������ ������ ������������ ����������� -->

<div id="userOKButtons" class="acField hide">
	<h4>�� ���������?</h4>
	<span class="btn btn-large" id="userDataOK">��!</span><span class="btn btn-large" id="userDataFail">���</span><br><br>
	<div id="correctnessAnnot" class="alert alert-success hide">�������������� ���������� ���� ����������: �������� ���������� ������ ����, ��������� ���� ����������. ��������� ���� ��������� ����� ������ ��� ������ ������.</div>
</div>

<div id="copyButtons" class="acField hide">
	<h4>��� ��������� ��������?</h4>
		<span class="btn btn-large" id="regetOrder" style="margin-left:-30px;" disabled="disabled">����� ������</span>�������� ����� ����� �������� ������ (�������� ������ ������ ���������)<br>
		<span class="btn btn-large" id="newOrder" style="margin-left:-170px;">����� ������</span>�������� ������ �� ��� ���� �������������� ������
	</dl>
</div>

<!-- �������� �������� -->
<div id="resdata" class="acField hide" style="margin-left:0px;">
	<div id="popIR" class="control-group" rel="popover"
	data-content="� ��� ���� ������� �������� ��������������� �������. ������������ ������� ����� �������� �������, � ������� ��������� ����������."
	data-original-title="����� �� �� ��������"
	data-trigger="manual">
		<div class="input-prepend">
			<span class="add-on pre-label">����� �� ��������:</span>
			<input id="searchIR" type="text" maxlength="60"
				title="������� ������� ��������. � ������ ����� �������� ��������� �� ���������� �������"
				placeholder="������� �������� ��������������� �������">
		</div>
	</div>
	<div class="accordion pull-left" id="accordion" rel="popover"
				data-content="������ �������������� ��������. ����������� ������ ������� ���� � ��������� ������ ������ ��������"
				data-original-title="��������� �� �����."
				data-placement="left"
				data-trigger="manual"
				style="margin-left: 0px;">

		<?=$this->bidsuimodel->getResourceAccordion($rlist, 11);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 10);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 12);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 13);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 1);?>
		<?//=$this->bidsuimodel->getResourceAccordion($rlist, 2);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 3);?>
		<?//=$this->bidsuimodel->getResourceAccordion($rlist, 4);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 9);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 5);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 6);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 7);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 8);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 0);?>
	</div>
	<ul id="selectedList" class="well well-small" >
		<center>
			<h4 class="muted" style="top:50%;bottom:50%">��������� �������������� �������<br><br>
				<small>��������� ������ ����� � �������� �� ������� ��������. ��� ������� �� � ���������.<br>������ ��
					������� � ���� ������ �������� ������ �� ������.
				</small>
			</h4>
		</center>
	</ul>
	<button class="btn btn-block btn-primary" id="order" title="�� ����� ������!">�������� ������</button>
</div>
<!--  -->

<!-- ������ ��������� -->

<div id="navButtons acField" class="well well-small pull-right hide">
	<!-- <button type="button"
			class="btn disabled pull-right<?= ($this->input->post("passedUID") || $this->input->post("userSelector")) ? "" : " hide"; ?>"
			id="regetOrder"
			title="�������� ����� ������"
			style="margin-bottom:40px;">�������� ����� ��������� ������ </button>-->
	<button class="btn pull-left hide" id="back" title="�����������/��������������� ������ ������������"><i class="icon-backward"></i> � ������������</button>
	<button class="btn pull-right btn-primary hide disabled" id="order" title="�� ����� ������!">�������� ������</button>
	<button class="btn hide" id="toOrder" title="�������� ������ ������"><i class="icon-backward"></i> � ������ ������</button>
	<button class="btn pull-right btn-primary" title="������� � ������ �������������� ��������" id="forward">�����<i class="icon-forward icon-white"></i></button>
	<button type="button" class="btn btn-primary span3 pull-right" id="putOrder" style="margin-bottom:100px;margin-left:10px;" title="������� � ���������� ����� ������">�������� ����� ������</button>
	<button class="btn span3 pull-right<?= (!$this->input->post("passedUID") && !$this->input->post("userSelector")) ? " hide" : ""; ?>" id="reset" title="������, ���� ���-�� ����� ������ �� ���">������ ������</button>
</div>
<!-- ������ ��������� -->

<div id="startManual" class="alert alert-info alert-block hide" style="clear:both;">
	<span id="helpText">������</span>
</div>

<table id="startScreen" class="acField" style="border-spacing:4px;border-collapse:separate;">
	<tr>
		<th colspan=2><h4 style="margin-top:80px;margin-bottom:20px;">���������� ������ � ������ ���?</h4></th>
	</tr>
	<tr>
		<td style="vertical-align:top;border: 1px solid #D6D6D6">
			<span class="btn btn-large btn-info btn-block" id="newUser">��,<br>����� ����� ������������ ����</span>
			����� ��������� ������ �� ������:<ol>
				<li>� ��������� ���� ��������������</li>
				<li>� ���������-�������� ��������</li>
				<li>������ ������������ �� ������������� �������</li>
			</ol><br>
		</td>
		<td style="vertical-align:top;border: 1px solid #D6D6D6">
			<span class="btn btn-large btn-info btn-block" id="oldUser">���,<br>���� �������� ���� �������</span>
			<ul>
				<li>���������� ������������ ������������� ������ �� ������ � �������� ��������� ���� ��������������</li>
				<li>������������ �������� ������ �� �������������� �������</li>
				<li>��������� ����� ������</li>
			</ul>
		</td>
	</tr>
</table>

<div id="modalRes" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
	 aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel1">�������� ������ �������
			<small>������ �� ���</small>
		</h3>
	</div>
	<div class="modal-body">
		<div id="esiaMail">
			<h5>����� ����������� ����� ��� ����������� �����������:</h5>
			<input type="text" id="esiaMailAddr" name="esiaMailAddr" class="short" valid="email" placeholder="������� ����� ����������� �����">
			<span id="esiaMailAnnounce" class="hide" style="color:red">������� ����� ����������� �����!</span><hr>
		</div>
		<div>
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader">
		</div>
		<div id="resCollection" class="hide"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="���������� �� ������">������</button>
		<button class="btn btn-primary" aria-hidden="true" id="layerModalOk"
				title="��������� ����� ���� � ��������� � ������ ��������">������
		</button>
	</div>
</div>

<div id="modalWF"  class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3"
	 aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel3">��������� ������������ ����:</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label span3">�����������</label>
			<div class="controls">
				<textarea name="wf_reason" id="wf_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">������</button>
		<button class="btn btn-primary" id="wfModalOk">������</button>
	</div>
</div>

<div id="modalEmail" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel2">��������� ������������ ����:</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label">����� �����</label>
			<div class="controls">
				<div class="input-append">
					<input type="text" id="email_addr" name="email_addr" form="mainform" maxlength="40" valid="email" pref="1">
					<span class="add-on">@arhcity.ru</span>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">�����������</label>
			<div class="controls">
				<textarea name="email_reason" id="email_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">������</button>
		<button class="btn btn-primary" id="emailModalOk">������</button>
	</div>
</div>

<div id="modalInet"  class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel">��������� ������������ ����:</h3>
	</div>
	<div class="modal-body" style="text-align:center;">
		<div class="control-group">
			<label class="control-label pull-left">�����������</label>
			<div class="controls">
				<textarea name="inet_reason" id="inet_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">������</button>
		<button class="btn btn-primary" id="inetModalOk">������</button>
	</div>
</div>

<div id="modalPortal" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel">�������� ������ ��������-�������:</h3>
	</div>
	<div class="modal-body" id="portalListBody">
		����� ������ <input type="text" id="portalSectionFilter" class="short" placeholder="����� ��� �������� �������"><br>
		<ul id="portalSectionList"></ul>
		���� �������� ������ �� ��������� ������ ������ ������.<br>������ ������ ������� ������ <strong>"������"</strong>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">������</button>
		<button class="btn btn-primary" id="portalModalOk">������</button>
	</div>
</div>

<form class="hide" action="/bidsfactory/reget_orders" method="post" id="regetForm">
	<input type="hidden" name="resources" id="resources" value="">
</form>

<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">
<!--
	var locs = <?=$locs?>
//-->
</script>
<script type="text/javascript" src="/jscript/bidsmachine.js"></script>
