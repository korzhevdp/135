<h3>АРМ, назначенные для подразделений</h3>
<form method="post" action="/arm/showdep" class="form-horizontal">
	<select name="department" id="dep_id" style="width:80%">
		<?=$depts;?>
	</select>&nbsp;&nbsp;&nbsp;&nbsp;
	<button class="btn btn-info" type="submit" style="width:150px;">Показать</button>
</form>
<hr>
<h3 id="depname" class="close"></h3>

<!-- <table class="table table-bordered table-condensed">
<tr>
	<td><div class="pull-right">Подразделение <-> АРМ (PCs|Lic)</div></td>
</tr>
<tr>
	<td></td>
</tr>
</table> -->
<button type="button" class="btn openAdd">Создать АРМ</button>
<?=$arms;?>
<button type="button" class="btn openAdd">Создать АРМ</button>

<div id="newARM" class="hide">
	<h4>Создать АРМ</h4>
	<div class="span12 well well-small" style="margin:0px;">
		<div>
			<div style="width:175px;clear:left;float:left;height:24px;padding-top:6px;">Должность</div>
			<select id="newstaff" style="clear:right;width:400px;"><?=$staff;?></select>
		</div>
		<div>
			<div style="width:175px;float:left;height:24px;padding-top:6px;">Расположение</div>
			<select id="newloc" style="clear:right;width:400px;"><?=$locations;?></select>
		</div>
		<div class="pull-right">
			<button type="button" id="closeAdd" class="btn btn-large">Закрыть</button>
			<button type="button" id="makeARM" class="btn btn-large btn-primary">Создать АРМ</button>
		</div>
	</div>
</div>

<div id="modalWF" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Кого следует добавить в АРМ&nbsp;&nbsp;&nbsp;&nbsp;<small>1-2 человека</small></h3>
	</div>
	<form method="post" action="/arm/addptoarm" class="form-horizontal">
		<input type="hidden" name="department" id="dep_id" value="<?=$this->input->post('department')?>">
		<input type="hidden" name="arm_id" id="armID">
		<div class="modal-body" id="tableCn"></div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
			<button class="btn btn-primary" id="wfModalOk">Готово</button>
		</div>
	</form>
</div>

<div id="modalPC" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true" style="left:30%;width:1020px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Персональные компьютеры в АРМ&nbsp;&nbsp;&nbsp;&nbsp;</h3>
	</div>
	<!-- <form method="post" action="/arm/addptoarm" class="form-horizontal"> -->
		<div class="modal-body" id="tableCn">
			<table>
			<tr>
				<td id="pcGrid" style="width:800px;border-right:1px solid #E6E6E6;vertical-align:middle;">
					<center><img src="http://api.arhcity.ru/images/loading.gif" style="width:16px;height:16px;border:none;" alt=""> Загружаю...</center>
				</td>
				<td id="pcSelector" style="width:200px;text-align:center;vertical-align:top;background-color:#E9E9E9;padding:2px;">
					<center><img src="http://api.arhcity.ru/images/loading.gif" style="width:16px;height:16px;border:none;" alt=""> Загружаю...</center>
				</td>
			</tr>
			</table>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Закрыть</button>
			<!-- <button class="btn btn-primary" id="PCModalOk">Готово</button> -->
		</div>
	<!-- </form> -->
</div>

<script type="text/javascript">
<!--
	$('#closeAdd').click(function(){
		$("#newARM").addClass("hide");
		$(".openAdd").removeClass("hide");
	});

	$('.openAdd').click(function(){
		$("#newARM").removeClass("hide");
		$(".openAdd").addClass("hide");
	});

	$('#makeARM').click(function(){
		$.ajax({
			url       : "/arm/create_arm",
			data      : { 
				staff : $('#newstaff').val() , 
				loc   : $('#newloc').val(),
				dep   : $('#dep_id').val() 
			},
			type      : "POST",
			dataType  : "script",
			success   : function(){
				window.location.reload();
			},
			error: function(data,stat,err){
				alert([data,stat,err].join("<br>"));
			}
		});
		//alert( [ $('#newstaff').val() , $('#newloc').val(), $('#dep_id').val() ].join(',') );
	});

	$("#depname").html($("#dep_id :selected").html());

	$('.modal').modal({show: 0});

	// (раз)блокировщики ПК. Инициируются conf_getter_set()
	function locker_set(){
		$(".locker").click(function(){
			var pcid = $(this).attr("ref");
			$.ajax({
				url: "/arm/lockpc",
				type: "POST",
				data: { ref: pcid },
				dataType: "html",
				success: function(data){
					callPCList($("#toPCGrid").attr("ref"));
					$("#pcGrid").html(data);
					locker_set();
					unlocker_set();
					toPCGrid_set();
				},
				error: function(data,stat,err){
					alert([data,stat,err].join("<br>"));
				}
			});
		});
	}

	function unlocker_set(){
		$(".unlocker").click(function(){
			pcid = $(this).attr("ref");
			$.ajax({
				url: "/arm/unlockpc",
				type: "POST",
				data: { ref: pcid },
				dataType: "html",
				success: function(data){
					callPCList($("#toPCGrid").attr("ref"));
					$("#pcGrid").html(data);
					locker_set();
					unlocker_set();
					toPCGrid_set();
				},
				error: function(data,stat,err){
					alert([data,stat,err].join("<br>"));
				}
			});
		});
	}

	function grid_marker_set(){
		$(".pcflowtab").off("click").click(function(){
			$(".pcItem").each(function(){
				$(this).removeClass("btn-warning").addClass($(this).attr("sc"));
			});
			//alert($(this).attr("sc"));
			$('.pcItem[ref="' + $(this).attr("ref") + '"]').removeClass("btn-info").addClass("btn-warning");
		});
	}
	
	// установка переключателя в режим движения ПК
	// и подключение слушателя маркера
	function toPCGrid_set(){
		$("#toPCGrid").off("click").click(function(){
			var uid = $("#toPCGrid").attr("ref");
			callPCGrid(uid);
		});
	}

	// Извлечение конфигурации ПК. Устанавливает прослушивание событий кнопок блокировки
	function conf_getter_set(){
		$(".pcItem").click(function(){
			var pcid = $(this).attr("ref");
			$.ajax({
				url: "/arm/conf_get/1",
				type: "POST",
				data: { ref: pcid },
				dataType: "html",
				success: function(data){
					$("#pcGrid").html(data);
					locker_set();
					unlocker_set();
					toPCGrid_set();
				},
				error: function(data,stat,err){
					alert([data,stat,err].join("<br>"));
				}
			});
		});
	}

	function callPCGrid(uid){
		$.ajax({
			url: "/arm/pc_grid/" + uid + "/1",
			type: "POST",
			dataType: "html",
			success: function(data){
				$("#pcGrid").html(data);
				grid_marker_set(); // здесь ей самое место - при пуске обработчика GRID
			},
			error: function(data,stat,err){
				alert([data,stat,err].join("<br>"));
			}
		});
		return false;
	}

	function callPCList(uid){
		//alert(uid)
		$.ajax({
			url: "/arm/pc_list_get/1",
			data: { 
				uid: uid
			},
			type: "POST",
			dataType: "html",
			success: function(data){
				$("#pcSelector").html(data);
				conf_getter_set();
			},
			error: function(data,stat,err){
				alert([data,stat,err].join("<br>"));
			}
		});
	}

	$(".PCShow").click(function(){
		var ref = $(this).attr("ref");
		$('#modalPC').modal('show');
		callPCList(ref)
		callPCGrid(ref);
	});

	$(".armUserAdd").click(function(){
		$("#armID").val( $(this).attr('ref') );
		$("#modalWF").modal('show');
		$.ajax({
			url: "/arm/userlist/1",
			data: { 
				staff: $('#newstaff').val(),
				loc: $('#newloc').val(),
				dep: $('#dep_id').val() 
			},
			type: "POST",
			dataType: "html",
			success: function(data){
				$("#tableCn").empty().html(data);
			},
			error: function(data,stat,err){
				alert([data,stat,err].join("<br>"));
			}
		});
	});
//-->
</script>
