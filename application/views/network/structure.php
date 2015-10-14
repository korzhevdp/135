<ul class="nav nav-tabs">
	<li class="active"><a href="#switch"  data-toggle="tab">Коммутаторы</a></li>
	<li><a href="#user"    data-toggle="tab">Пользователи</a></li>
	<li><a href="#printer" data-toggle="tab">Принтеры</a></li>
	<li><a href="#server"  data-toggle="tab">Сервера</a></li>
</ul>

<div class="tab-content">
	
	<div class="tab-pane active" id="switch">
		<?=implode($switch, "\n");?>
	</div>

	<div class="tab-pane fade" id="user">
		<?=implode($user, "\n");?>
	</div>

	<div class="tab-pane fade" id="printer">
		<?=implode($printer, "\n");?>
	</div>

	<div class="tab-pane fade" id="server">
		<?=implode($server, "\n");?>
	</div>

</div>

<div id="Cm" class="modal hide fade" style="width:600px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Сетевое устройство</h3>
	</div>
	<div class="modal-body" style="text-align:right;">
		<div style="clear:both">
			<div class="input-prepend input-append" style="float:right">
			<span class="add-on" style="width:121px;">Имя устройства</span>
			<input type="text" id="chm" style="width:424px;">
			</div>
		</div>

		<div style="clear:both">
			<div   class="input-prepend input-append" style="float:right">
			<span  class="add-on" style="width:120px;">IP-адрес</span>
			<input id="cip" style="width:150px;" type="text" placeholder="IP-адрес устройства">
			<span  class="add-on" style="width:90px;">MAC-адрес</span>
			<input id="cmc" style="width:160px;" type="text" placeholder="MAC-адрес устройства">
			</div>
		</div>

		<div style="clear:both">
			<div class="input-prepend input-append" style="float:right">
			<span class="add-on" style="width:121px;">Расположение</span>
			<input type="text" id="clc" style="width:424px;" placeholder="Адрес помещения">
			</div>
		</div>

		<div style="clear:both">
			<div class="input-prepend input-append" style="float:right">
			<span class="add-on" style="width:120px;">Switch IP</span>
			<input style="width:150px;" type="text" placeholder="IP адрес коммутатора" id="cpip">
			<span class="add-on" style="width:90px;">Порт</span>
			<input style="width:160px;" type="text" placeholder="Порт коммутатора" id="cpport">
			</div>
		</div>

		<div style="clear:both">
			<div class="input-prepend input-append" style="float:right">
			<span class="add-on" style="width:120px;">VLAN</span>
			<input style="width:150px;" type="text" placeholder="ID виртуальной сети" id="сvlan">
			<span class="add-on" style="width:90px;">Направление</span>
			<select id="cdir" style="width:175px;">
				<option value="0">Downlink</option>
				<option value="1">Uplink</option>
			</select>
			</div>
		</div>

		<div style="clear:both">
		<textarea id="comment" style="width:555px;" rows="" cols=""></textarea>
		</div>
		<input type="hidden" id="cid">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="saveSW">Сохранить</button>
	</div>
</div>

<script type="text/javascript">
<!--

	$('.modal').modal({
		show: 0
	});

	$(".editSW").unbind().click(function(){
		//$('#Cm').modal('show');
		ref = $(this).attr('ref');
		$.ajax({
			url: "/network/getunit",
			type: "POST",
			dataType: "script",
			data: { unit: $(this).attr('ref') },
			success: function(){
				$("#cid").val(ref);
				$("#chm").val(unit.host);
				$("#cip").val(unit.hostip);
				$("#cmc").val(unit.mac);
				$("#clc").val(unit.loc);
				$("#cpip").val(unit.sip);
				$("#cpport").val(unit.pport);
				$("#сvlan").val(unit.vlan);
				$("#comment").val(unit.comment);
				$("#cdir option[value=" + unit.dir + "]").attr("selected", "selected");
				$('#Cm').modal('show');
			},
			error: function(data,stat,err){
				$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});

	});

	$("#saveSW").unbind().click(function(){
		//alert($(this).attr('ref'));
		//ref = $(this).attr('ref');
		datum = {
			id:      $("#cid").val(),
			host:    $("#chm").val(),
			hostip:  $("#cip").val(),
			mac:     $("#cmc").val(),
			loc:     $("#clc").val(),
			sip:     $("#cpip").val(),
			pport:   $("#cpport").val(),
			vlan:    $("#сvlan").val(),
			dir:     $("#cdir").val(),
			comment: $("#comment").val()
		};
		//alert(datum.toSource());
		//return false;
		$.ajax({
			url: "/network/saveunit",
			type: "POST",
			dataType: "html",
			data: datum,
			success: function(data){
				$('#hm' + datum.id).val(datum.host);
				$('#ip' + datum.id).val(datum.hostip);
				$('#mac' + datum.id).val(datum.mac);
				$('#adr' + datum.id).val(datum.loc);
				$('#cmn' + datum.id).html(datum.comment);
				$('#psw' + datum.id).html(datum.sip + '<strong>[:' + datum.pport + ']</strong>');
				$('#Cm').modal('hide');
				//alert(data);
			},
			error: function(data,stat,err){
				$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	});

	$(".offSW").unbind().click(function(){
		ref = $(this).attr("ref")
		data = {
			mode : 0,
			node: ref
		}
		$.ajax({
			url: "/network/sw",
			type: "POST",
			dataType: "html",
			data: datum,
			success: function(data){
				$("#row" + ref).addClass("danger");
				$("#i" + ref).removeClass("icon-remove").addClass("icon-ok");
				$("#i" + ref).parent().removeClass("btn-danger").addClass("btn-warning");
				//alert(data);
			},
			error: function(data,stat,err){
				$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	});

	$(".onSW").unbind().click(function(){
		ref = $(this).attr("ref")
		data = {
			mode : 1,
			node: ref
		}
		$.ajax({
			url: "/network/sw",
			type: "POST",
			dataType: "html",
			data: datum,
			success: function(data){
				$("#row" + ref).removeClass("danger");
				$("#i" + ref).removeClass("icon-ok").addClass("icon-remove");
				$("#i" + ref).parent().removeClass("btn-warning").addClass("btn-danger");
				//alert(data);
			},
			error: function(data,stat,err){
				$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	});
//-->
</script>