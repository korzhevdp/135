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
	input[type=text].short {
		width:250px;
		margin-top:4px;
	}
	input[type=text],
	textarea {
		width:540px;
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
	#searchIR{
		width:542px;
	}
	#userSelector{
		margin-left:150px;
	}
	.userHeader{
		border-bottom: 1px solid #000099;
		padding-bottom: 3px;
		margin-bottom: 20px;
	}
	#navButtons{
		vertical-align:middle;
		margin-top:20px;
		margin-bottom:40px;
	}
	#userOKButtons,
	#copyButtons {
		height: 120px;
		width: 704px;
		--display: table-cell;
		--border: 1px solid red;
		text-align:center;
		vertical-align:middle;
	}
	#userOKButtons span{
		margin-left:20px;
		margin-right:20px;
	}
	#breadCrumbs{
		margin-bottom:25px;
	}
	#breadCrumbs span,
	#breadCrumbs a {
		cursor:pointer;
	}
	#regetOrder, 
	#newOrder {
		margin:10px;
	}
	#startScreen {
		width:704px;
		height:400px;
	}
	#startScreen th{
		--background-color:#cacaca;
		height:40px;
	}
	#startScreen td{
		--border: 1px solid #cacaca;
		width:50%;
		padding:5px;
		vertical-align:middle;
	}
	#newUser,
	#oldUser{
		margin-top:0px;
		margin-bottom:20px;
	}
	.accordion-group{
		width:340px;
	}
	#selectedList{
		float:left;
		width:320px;
		min-height:200px;
	}
	.badge{
		margin-right: 5px;
	}
	#userHint{
		margin-left:149px;
		width: 505px;
	}
	.stageMarker{
		margin-left:10px;
		margin-right:10px;
	}
	.reslist{
		margin: 2px 0px
	}
	#getHelp{
		margin-bottom:100px;
		margin-left:10px;
	}
	#email_addr{
		width:390px;
	}
	.modal textarea{
		width:470px;
		min-width:470px;
		max-width:470px;
		height:100px;
		min-height:100px;
		max-height:100px;
	}
	#order{
		clear:both;
		width: 706px;
	}
	#portalSectionList{
		list-style-type: none;
		width:45%;
	}
	#portalListBody {
		height:250px;
		overflow:auto;
	}
	#portalListBody li {
		padding-left: 8px !important;
		text-align: left !important;
	}

</style>

<script type="text/javascript" src="/jscript/jqueryui.js"></script>

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

<div id="orderList" class="acField hide">
	
</div>

<!-- ������ ������ ������������ -->
<div id="userdata" class="acField hide">
	<div id="popInfo" class="control-group" rel="popover"
		data-content="��������� ������������ ������. ���� ���������� �������� ��� ����������� - ��������� ��� ��������� ��������������� ���� �����."
		data-original-title="������ � ������������"
		data-trigger="manual">
		<form method="post" action="/bids/getpapers" id="mainform" class="form-horizontal">
			<input type="hidden" name="login" id= "login" value="<?= $login; ?>">
			<input type="hidden" name="uid"   id= "uid"   value="">
			<input type="hidden" name="res"   id= "res"   value="">
			<input type="hidden" name="confs" id= "confs" value="">
			<input type="hidden" name="subs"  id= "subs"  value="">

			<div class="input-prepend control-group">
				<span class="add-on pre-label">�������</span>
				<input class="traceable fio_login" id="sname" name="sname" maxlength="60"
					placeholder="������� ������������"
					form="mainform"
					type="text"
					value="<?=$name_f; ?>"
					valid="rword" pref="2"
					title="��� ����� ������� ��������� ������ ��������� � �������� ������� �����.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">���</span>
				<input class="traceable fio_login" id="name" name="name" maxlength="60"
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

		<?=$this->bidsmodel->getResourceAccordion($rlist, 11);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 10);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 12);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 13);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 1);?>
		<?//=$this->bidsmodel->getResourceAccordion($rlist, 2);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 3);?>
		<?//=$this->bidsmodel->getResourceAccordion($rlist, 4);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 9);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 5);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 6);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 7);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 8);?>
		<?=$this->bidsmodel->getResourceAccordion($rlist, 0);?>
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
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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

<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">
<!--
var state	= 0,
	udt		= false
	res		= [],
	res		= [],
	subs	= [],
	confs	= [],
	locs	= <?=$locs?>,
	bmode	= 'new',
	allowedRes = {
		'81' : [ 100, 101, 102 ]
	};

//########################################################
//# rev 3 JS code
//########################################################

function disableUnallowedRes() {
	var dept = ( udt === undefined || !udt || udt.dept === undefined) ? $("#dept").val() : udt.dept;
	if (allowedRes[dept] === undefined){
		$('.reslist').removeClass("toDisabled disabled").prop('disabled', false);
		$('.reslist').each(function (){
			$(this).addClass( ($(this).attr("conf") == "0") ? "btn-info" : "btn-warning" );
		});
		return false;
	}
	$('.reslist').addClass("toDisabled");
	$("#selectedList .reslist").click();
	for ( a in allowedRes[dept] ) {
		if (allowedRes[dept].hasOwnProperty(a)) {
			$('.reslist[id=r_' + allowedRes[dept][a] + ']').removeClass("toDisabled")
		}
	}
	$(".toDisabled").removeClass("btn-info btn-warning").addClass("disabled").prop('disabled', true).removeClass("toDisabled");
}

$("#searchUser, #stage2").click(function(){
	getUserData();
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
});

$("#userSelector").dblclick(function(){
	getUserData();
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
});

function getUserData(){
	uid = $("#userSelector").val();
	if(uid != null){
		$.ajax({
			url: "/bids/getuserdata",
			type: "POST",
			data: {
				uid: uid
			},
			dataType: "script",
			success: function () {
				bldg = (udt.bldg == "0") ? udt.office : udt.bldg ; 
				$("#sname").val(udt.name_f);
				$("#name" ).val(udt.name_i);
				$("#fname").val(udt.name_o);
				$("#dept" ).val(udt.dept);
				$("#staff").val(udt.staff);
				$("#esiaMailAddr, #f_esiaMailAddr").val(udt.email);
				$("#office").val(bldg);
				if (locs[parseInt(bldg)] !== undefined) {
					$("#office2").empty().append(locs[parseInt(bldg)].join("\n")).val(udt.office);
				} else {
					$("#office2").empty();
				}
				$("#phone").val(udt.phone);
				$("#uid").val(uid);
				$("#login").val(udt.login);
				$(".traceable").parent().removeClass("warning error success");
				disableUnallowedRes();
			},
			error: function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
	}else{
		
	}
	$("#userdata, #popID, #userOKButtons, #breadCrumbs").removeClass("hide");
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
}

$("#oldUser").click(function(){
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs").fadeIn(700);
	bmode = "old";
});

$("#newUser").click(function(){
	//alert("����� ���� ������");
	$(".acField").fadeOut(100);
	$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
	bmode = "new";
	$("#r_102").appendTo('#selectedList').removeClass("btn-info").addClass("btn-success");
	$.ajax({
			url: "/bids/resetUID",
			type: "POST",
			data: {},
			dataType: "text",
			success: function (data) {
				console.log(data);
			},
			error: function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
});

$("#office").change(function(){
	//alert(locs[parseInt($(this).val())].join("\n"));
	$("#office2").empty().append(locs[parseInt($(this).val())].join("\n"));
});

$("#userDataOK, #stage1").unbind().click(function(){
	uid = $("#userSelector").val();
	$.ajax({
		url: "/bids/getuserresources",
		type: "POST",
		data: {
			uid: uid
		},
		dataType: "html",
		success: function (data) {
			$(".acField").fadeOut(100);
			$("#userHeader, #breadCrumbs, #orderList, #userHeader, #copyButtons").fadeIn(700);
			$("#fioAcknowledger").html([ $("#sname").val(), $("#name").val(), $("#fname").val() ].join("\n"));
			$("#depAcknowledger").html( $("#dept option:selected").text() );
			$(".stageMarker").addClass("muted");
			$("#stage1").removeClass("muted");
			if(bmode == "old"){
				$("#orderList").html(data);
			}else{
				$("#orderList").html('<h5 class="muted">&nbsp;&nbsp;&nbsp;<i class="icon-exclamation-sign"></i>&nbsp;&nbsp;������ �� ������ � ���� ����� ��������� �������������</h5><hr>');
			}
			// actions
			$(".paperChecker").unbind().click(function () {
				if ($(".paperChecker:checked").size() > 0) {
					$("#regetOrder").removeClass("disabled").removeAttr("disabled").addClass("btn-primary");
					$("#putOrder").removeClass("btn-primary");
				} else {
					$("#regetOrder").addClass("disabled").attr('disabled', 'disabled').removeClass("btn-primary");
					$("#putOrder").addClass("btn-primary");
				}
				if ($(".paperChecker:checked").size() == $(".paperChecker").size()) {
					$("#checkAllPapers").attr("checked", "checked");
				} else {
					$("#checkAllPapers").removeAttr("checked");
				}
			});
			$("#checkAllPapers").unbind().click(function () {
				$(".paperChecker").prop('checked', $("#checkAllPapers").prop('checked'));
			});
		},
		error: function (data, stat, err) {
			$("#consoleContent").html([data, stat, err].join("<br>"));
		}
	});
});

$("#userDataFail").click(function(){
	$("#correctnessAnnot").fadeIn(1000).delay(15000).fadeOut(1000);
});

$("#newOrder, #stage3").click(function(){
	$(".acField").fadeOut(100);
	$("#resdata, #breadCrumbs, #userHeader").fadeIn(700);
	$(".stageMarker").addClass("muted");
	$("#stage3").removeClass("muted");

});

//########################################################
//########################################################

$('.modalRes .modalEmail .modalInet').modal({ show: 0 });

// #passedUID - ������ ���������� ������������ ( 0 - �� ������ )
/*
function checkPP() {
	var uid = $("#passedUID").val();
	if (uid != "0") {
		$("#depAcknowledger").html($("#dept :selected").html());
		$("#startManual").addClass("hide");
	}
}

// #navPage - ����������� ����� �������� ( 1 - ������ ������������, 2 - ����� �������������� �������� )

function displayCurrentPage() {
	var page = parseInt($("#navPage").val());
	switch (page) {
		case 1:
			$("#back").addClass("hide");
			$("#resdata").addClass("hide");
			$("#order").addClass("hide");
			$("#reset").addClass("hide");
			$("#reset").removeClass("hide");
			$("#forward").removeClass("hide");
			$("#userdata").removeClass("hide");
			$("#toOrder").removeClass("hide");
			break;
		case 2:
			$("#userdata").addClass("hide");
			$("#forward").addClass("hide");
			$("#back").removeClass("hide");
			$("#resdata").removeClass("hide");
			$("#order").removeClass("hide");
			$("#toOrder").addClass("hide");
			break;
	}
}

checkPP();

$("#forward").click(function () {
	var page = parseInt($("#navPage").val());
	$("#navPage").val(++page);
	checkPP();
	displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage3").removeClass("muted");
	$('#popInfo').popover('hide');
	state = 2;
	showHelp($("#getHelp").hasClass('active'));
});

$("#reset").click(function () {
	$("#passedUID").val(0);
	$("#userSelector").val(0);
	$("#userSForm").submit();
});
*/
$("#order").click(function () {

	if ($(this).hasClass("disabled")) {
		return false;
	}
	if ($("#sname").val().length < 3 || $("#name").val().length < 2 || $("#fname").val().length < 3) {
		alert("��������� ������������ �������� �����, ������� � �������� ������������");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	if ($("#dept").val() == "0") {
		alert("�������� �������������");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	if ($("#staff").val() == "0") {
		alert("�������� ���������");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	var lsubs = [],
		res = [],
		confs = [];

	$("#selectedList").find("li").each(function () {
		if ($(this).attr('conf') == 0) {
			res.push($(this).attr("id").split("_")[1]);
		} else {
			confs.push($(this).attr("id").split("_")[1]);
		}
	});
	if (!res.length && !confs.length) {
		alert("�������������� ������� �� �������");
		return false;
	}
	for (a in subs) {
		lsubs.push(subs[a].join(","));
	}

	if ($("#userSelector").val() != "") {
		/*
		$("#f_name_f").val($("#sname").val());
		$("#f_name_i").val($("#name").val());
		$("#f_name_o").val($("#fname").val());
		$("#f_staff").val($("#staff").val());
		$("#f_addr1").val($("#office").val());
		$("#f_addr2").val($("#office2").val());
		$("#f_dept").val($("#dept").val());
		$("#f_phone").val($("#phone").val());
		$("#f_uid").val($("#passedUID").val());
		*/
		$("#login").val(recode_field);
		$("#confs").val(confs.join(","));
		$( "#res" ).val(res.join(","));
		$("#subs" ).val(lsubs.join(","));
		$("#mainform").submit();
	} else {
		$("#f_uid").val($("#userSelector").val());
	}
});
/*
$("#back").click(function () {
	var page = parseInt($("#navPage").val());
	$("#navPage").val(--page);
	checkPP();
	displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
	$('#popIR, #accordion').popover('hide');
	state = 1;
	showHelp($("#getHelp").hasClass('active'));
});
*/
$("li.reslist").click(function () {
	if($(this).hasClass("disabled")){
		return false;
	}
	if($(this).attr("id") == "r_102"){
		return false;
	}
	if ($(this).parent().attr("id") === 'selectedList' ) {
		console.log('remove');
		removeFromList(this);
	} else {
		console.log('add');
		addToList(this);
	}
	if (!$("#selectedList li").size()) {
		$("#order").addClass("disabled");
	}
});

function addToList(a) {
	var resID = parseInt($(a).attr('id').split('_')[1], 10);
	if (parseInt($(a).attr('subs')) > 0) {
		$('#modalRes').modal('show');

		subs[resID] = [];
		$("#esiaMail").addClass("hide");
		if (resID === 286) {
			$("#esiaMail").removeClass("hide");
		}
		$.ajax({
			url      : "/bids/get_subproperties/" + resID,
			type     : "POST",
			dataType : "html",
			success  : function (data) {
				$("#resCollection").html(data).removeClass('hide');
				$("#gifLoader").addClass('hide');
				$(".subspad").click(function () {
					($(this).hasClass("btn-success")) ? $(this).removeClass("btn-success") : $(this).addClass("btn-success");
					subs[resID] = [];
					$(".subspad").each(function () {
						if ($(this).hasClass("btn-success")) {
							subs[resID].push($(this).attr("ref"));
						}
					});
				});
				
				$("#layerModalOk").click(function () {
					if (!$("#esiaMail").hasClass("hide") && !$("#esiaMailAddr").val().length) {
						$("#esiaMailAnnounce").removeClass("hide");
						return false;
					}
					if (subs[resID].length) {
						$("#f_esiaMailAddr").val($("#esiaMailAddr").val());
						$("#order").removeClass("disabled");
						$(a).appendTo('#selectedList');
						$(a).attr('title', '������� ������ ���������� ������ �������');
					}
					$('#modalRes').modal('hide');
				});
			},
			error     : function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
		return false;
	}
	if (resID === 101) {
		$('#modalInet').modal('show');
		$("#inetModalOk").click(function () {
			if ($("#inet_reason").val().length < 10) {
				alert("����������� ������������� ����������� � ���� �������� ������� ��������.");
				return false;
			}
			$("#f_inet_reason").val($("#inet_reason").val());
			$('#modalInet').modal('hide');
			$(a).appendTo('#selectedList');
			$(a).attr('title', '������ ���������� ������ �������');
			$("#order").removeClass("disabled");
		});
		return false;
	}
	if (resID === 100) {
		$('#modalEmail').modal('show');
		$("#emailModalOk").click(function () {
			if ($("#email_addr").val().length < 1) {
				alert("������� ����� ����������� �����.");
				return false;
			}
			if ($("#email_reason").val().length < 10) {
				alert("����������� ������������� ����������� ����������� ����� ������� ��������.");
				return false;
			}
			$("#f_email_addr").val($("#email_addr").val());
			$("#f_email_reason").val($("#email_reason").val());
			$('#modalEmail').modal('hide');
			$(a).appendTo('#selectedList');
			$(a).attr('title', '������� ������ ���������� ������ �������');
			$("#order").removeClass("disabled");
		});
		return false;
	}
	if (resID === 274) {
		$('#modalWF').modal('show');
		$("#wfModalOk").click(function () {
			if ($("#wf_reason").val().length < 10) {
				alert("����������� ������������� ����������� � ��������-�������� ���������� ������������ ���� ������� ��������");
				return false;
			}
			$("#f_wf_reason").val($("#wf_reason").val());
			$('#modalWF').modal('hide');
			$("#order").removeClass("disabled");
			$(a).appendTo('#selectedList');
			$(a).attr('title', '������� ������ ���������� ������ �������');
		});
		return false;
	}
	if (resID === 13)  {
		$('#modalPortal').modal('show');
		$("#portalModalOk").click(function () {

			$('#modalPortal').modal('hide');
			$("#order").removeClass("disabled");
			$(a).appendTo('#selectedList');
			$(a).attr('title', '������ ���������� ������ �������');
		});
		return false;
	}
	$(a).appendTo('#selectedList');
	$("#order").removeClass("disabled");
	$(a).attr('title', '������ ���������� ������ �������');
}

function removeFromList(a) {
	//console.log('removing to #group' + $(a).attr('grp'));
	$(a).appendTo('#group' + $(a).attr('grp'));
	$(a).attr('title', '������ ������� ������ � ������ ������');
}

$("#portalSectionFilter").keyup(function(){
	if ( $(this).val().length < 4 ){
		return false;
	}
	search = $(this).val();
	$.ajax({
		url: "/bids/getwebportalsection",
		type: "POST",
		data: {
			search: search
		},
		dataType: "html",
		success: function (data) {
			$("#portalSectionList").empty().append(data);
		},
		error: function (data, stat, err) {
			console.log( [data, stat, err].join("<br>") );
		}
	});
});

/*
$(".fio_login").keyup(function () {
	if ($("#fname").val().length > 0 && $("#name").val().length > 0 && $("#sname").val().length > 0) {
		recode();
	}
})
*/
$('.traceable').bind('change keyup', function () {
	var pref = parseInt($(this).attr('pref')),
		reg = $(this).attr('valid'),
		length = $(this).val().length,
		val = validate(reg, $(this).val());
	console.log(val)
	if (!length || !val) {
		$(this).parent().removeClass('success').removeClass('warning').addClass('error');
		//$(this).css("color", "red");
		//$(this).siblings().last().addClass("icon-cancel").removeClass("icon-ok").removeClass("hide");
		//alert($(this).siblings().last().attr("id"));
	} else {
		//$(this).css("color", "black");
		if ($(this).val().length < pref) {
			$(this).parent().removeClass('error').removeClass('success').addClass('warning');
			//$(this).siblings().last().addClass("hide");
		} else {
			$(this).parent().removeClass('error').removeClass('warning').addClass('success');
			//$(this).siblings().last().removeClass("hide").addClass("icon-ok");
		}
	}
});

function validate(dt, val) {
	var r,
	//if (dt == 'email'){r = '^.+@[^\.].*\.[a-z]{2,}$';}
	library = {
		email   : '^([0-9a-zA-Z\.\-_]+)@(.*)\.([a-zA-Z]{2,})$',
		text    : '[^a-z \\-"]',
		entext  : '[^0-9a-z \-"]',
		rtext   : '[^�-���\\-\\.\\, ]',
		rword   : '[^�-��� ]',
		nonzero : '^[0]$',
		date    : '[^0-9\\.]',
		weight  : '[^0-9 x������\\.]',
		num     : '[^0-9\\- ]',
		mtext   : '[^0-9 a-z�-���\\.\\,\\-"]',
		reqnum  : '[^0-9 \/\\��\-]'
	}

	//console_alert(r);
	var rgEx = new RegExp(library[dt], 'i');
	var OK = rgEx.exec(val);
	if (OK) {
		return 0;
	} else {
		return 1;
	}
}

$("#searchIR").keyup(function () {
	var text = $("#searchIR").val();
	$(".badge").html(0);
	$(".reslist").removeClass("hide");

	if (!text.length) {
		$(".badge").addClass("hide");
		return;
	}

	$(".reslist").each(function () {
		if ($(this).html().toLowerCase().indexOf(text.toLowerCase()) + 1) {
			src = $(this).parent().parent().parent().attr("ref");
			$(this).removeClass("hide");
			srf = parseInt($("#badge-collapse" + src).html());
			srf++;
			$("#badge-collapse" + src).empty().html(srf++);
		} else {
			$(this).addClass("hide");
		}
	});

	$(".badge").each(function () {
		if (parseInt($(this).html()) > 0) {
			$(this).removeClass("hide");
		} else {
			$(this).addClass("hide");
		}
	})
	//alert("press");
});
/*
if (parseInt($("#passedUID").val()) > 0) {
	$("#domain_data").addClass("hide");
}

$("#formDisplayer").click(function () {
	$("#mainform").slideToggle();
});

$("#putOrder").click(function () {
	$("#userdata, #navButtons, #toOrder").removeClass("hide");
	$("#orderList, #putOrder, #regetOrder").addClass("hide");
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
	$('#popID').popover('hide');
	state = 1;
	showHelp($("#getHelp").hasClass('active'));
});

$("#toOrder").click(function () {
	$("#navButtons, #resdata, #toOrder, #regetOrder").addClass("hide");
	$("#orderList, #putOrder, #regetOrder").removeClass("hide");
	$("#navPage").val(1);
	checkPP();
	displayCurrentPage();
	$("#userdata").addClass("hide"); // ������ ����� displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage1").removeClass("muted");
	$('#popInfo').popover('hide');
	state = 0;
	showHelp($("#getHelp").hasClass('active'));
});

$("#checkAllPapers").click(function () {
	if ($(this).is(":checked")) {
		$(".paperChecker").attr("checked", "checked");
		$("#regetOrder").removeClass("disabled").addClass("btn-primary");
		$("#putOrder").removeClass("btn-primary");
	} else {
		$(".paperChecker").removeAttr("checked");
		$("#regetOrder").addClass("disabled").removeClass("btn-primary");
		$("#putOrder").addClass("btn-primary");
	}
});
*/
$("#sname, #name, #fname").keyup(function () {
	$("#fioAcknowledger").html([ $("#sname").val(), $("#name").val(), $("#fname").val() ].join(" "));
});

$("#dept").change(function () {
	disableUnallowedRes();
	$("#depAcknowledger").html($("#dept option:selected").html());
});

$("#regetOrder").click(function () {
	if($(this).attr("disabled") == "disabled"){
		return false;
	}
	var ids = [];
	$(".paperChecker:checked").each(function () {
		ids.push(parseInt($(this).attr("ref")));
	});
	$('<form action="/bids/reget_orders" method="post" id="regetForm"><input type="hidden" name="name_f" id="r_name_f" value=""><input type="hidden" name="name_i" id="r_name_i" value=""><input type="hidden" name="name_o" id="r_name_o" value=""><input type="hidden" name="addr1" id="r_addr1" value=""><input type="hidden" name="addr2" id="r_addr2" value=""><input type="hidden" name="staff_id" id="r_staff" value=""><input type="hidden" name="dept" id="r_dept" value=""><input type="hidden" name="phone" id="r_phone" value=""><input type="hidden" name="login" id="r_login" value="<?=$login;?>"><input type="hidden" name="uid" id="r_uid" value="0"><input type="hidden" name="resources" value="' + ids.join(",") + '"></form>').appendTo('body');
	$("#r_name_f").val($("#sname").val());
	$("#r_name_i").val($("#name").val());
	$("#r_name_o").val($("#fname").val());
	$("#r_staff").val($("#staff").val());
	$("#r_addr1").val($("#office").val());
	$("#r_addr2").val($("#office2").val());
	$("#r_dept").val($("#dept").val());
	$("#r_phone").val($("#phone").val());
	$("#r_uid").val($("#passedUID").val());
	$("#regetForm").submit().remove();
	//console.log(ids.join(","));
});

// ���������� ������������� ������
$('#getHelp').click(function () {
	$(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
	showHelp($(this).hasClass('active'));
});

function showHelp(con) {
	if (con == true) {
		switch (state) {
			case 0:
				$('#popID').popover('show');
				break;
			case 1:
				$('#popInfo').popover('show');
				break;
			case 2:
				$('#popIR, #accordion').popover('show');
		}
	}
	else {
		$('#popID, #popInfo, #popIR, #accordion').popover('hide');
	}
}

/*
 function showAll() {
 $('div').removeClass('hide');
 }
 */
//-->
</script>
