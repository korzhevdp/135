<style type="text/css">
	.labelMover{
		cursor:pointer;
	}
	.labelMover:hover{
		text-decoration:underline;
	}
	#hostHelpDiv{
		display:none;
	}
</style>
<div class="tabbable tabs-left" style="margin-bottom:60px;">
	<ul class="nav nav-tabs">
		<li <?=($page == 1) ? 'class="active"' : '';?>><a href="#tab1" data-toggle="tab">Подразделения без руководителей</a></li>
		<li <?=($page == 2) ? 'class="active"' : '';?>><a href="#tab2" data-toggle="tab">Работники несуществующих<br>подразделений</a></li>
		<li <?=($page == 3) ? 'class="active"' : '';?>><a href="#tab3" data-toggle="tab">Информационные ресурсы<br>несуществующих подразделений</a></li>
		<li <?=($page == 4) ? 'class="active"' : '';?>><a href="#tab4" data-toggle="tab">Пользователи без куратора</a></li>
		<li <?=($page == 5) ? 'class="active"' : '';?>><a href="#tab5" data-toggle="tab">Бесхозные хосты</a></li>
		<li <?=($page == 6) ? 'class="active"' : '';?>><a href="#tab6" data-toggle="tab">Расход наклеек</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?=($page == 1) ? " active" : '';?>" id="tab1">
			<h5>Проверьте наличие указанных работников</h5>
			<?=$leaderless;?>
		</div>
		<div class="tab-pane<?=($page == 2) ? " active" : '';?>" id="tab2">
			<?=$deptless;?>
		</div>
		<div class="tab-pane<?=($page == 3) ? " active" : '';?>" id="tab3">
			<?=$irless;?>
		</div>
		<div class="tab-pane<?=($page == 4) ? " active" : '';?>" id="tab4">
			<?=$curless;?>
		</div>
		<div class="tab-pane<?=($page == 5) ? " active" : '';?>" id="tab5">
			<h3>Мониторинг имён компьютеров в сети</h3>
			<span class="btn btn-small btn-info pull-right" id="hostHelp"><i class="icon-question-sign icon-white"></i> Справка по управлению хостами</span><br>
			Управление связями имени компьютера и пользователями ЛВСМ<br>
			<strong>Внимание</strong>: связывание запускается вручную. До этой операции даже обнаруженные и обработанные конфигурации компьютеров будут не видны на страницах справочников по пользователям!
			<div id="hostHelpDiv">
				<strong>Автоматический режим</strong> связывания хостов пытается подобрать соответствие между имеющимися в таблице пользователей логинами и сетевыми именами компьютеров. По умолчанию логин пользователя и имя компьютера должны быть одинаковыми и подчиняться <a href="http://192.168.1.35/kb/page/2.php">правилам, установленным соответствующим распоряжением</a>. Автоматический режим самостоятельно устанавливает соответствие ПК и пользователей в большинстве случаев.<br><br>
				<strong>Ручной режим</strong> связывания хостов предназначен для подбора соответствия хостов и пользователей в случае, когда имена компьютеров не соответствуют правилам, а также для управления ПК специального назначения (серверами) имена которых не регламентируются соответствующим распоряжением.<br>
				Обнаруженный в сети хост может быть помечен как:<ul>
					<li>"шумовой" - оказавшийся в сети с именем, оставшимся от поставщика по умолчанию, не несущий смысловой нагрузки, "гостевой" компьютер, компьютер с которого сбор статистики необязателен и т.д. в зависимости от ситуации. Помечается нажатием кнопки "Шум", после чего исключается из отчёта несвязанных хостов.</li>
					<li>Сервер - компьютер, исполняющий роль сервера, требующий отнесения его к специальному разделу статистики по лицензиям и использованию. Нажатием кнопки "Сервер" относится к пользователю с идентификатором #592 (Шамов Виктор Анатольевич), <a href="http://192.168.1.35/admin/users/592/4">с чьей страницы</a> может быть просмотрен на предмет <a href="http://192.168.1.35/admin/users/592/4">конфигурации</a> и <a href="http://192.168.1.35/admin/users/592/5">используемых лицензий</a>. После чего имя компьютера исключается из отчёта несвязанных хостов.</li>
					<li>Обычный компьютер пользователя, но в имени хоста которого допущены ошибки или несоответствия. При нажатии кнопки "Связать" имя компьютера оказывается в таблице связывания слева. Из выпадающего списка в правой колонке таблицы связывания следует выбрать пользователя. После этого нажатием синей кнопки будет установлено постоянное соответствие хоста и пользователя. Вслед за этим имя компьютера исключается из отчёта несвязанных хостов.</li>
				</ul> 
			</div>
			<hr>
			<h4>Автоматический режим</h4>
			<a href="/imports/hostsallocation" class="btn btn-warning" id="doAutoBind" style="margin-bottom:20px;" title="Выполнить скрипт автоматического определения владельцев хостов">Автоматическое связывание</a> 
			<h4>Ручной режим</h4>
			<table class="table table-condensed table-bordered table-striped table-hover">
			<tr><th class="span4">Хост</th><th>Пользователь</th></tr>
			<tr>
				<td style="vertical-align:middle;text-align:center;">
					<span id="bindedhost"></span>
					<input type="hidden" id="b_hostid"></td>
				<td>
				<select id="b_uid" class="span12"><?=$userlist;?></select>
				</td></tr>
			</table>
			<button class="btn btn-primary pull-right" id="doBind" style="margin-bottom:20px;" title="Назначить владельца хоста вручную">Связать</button>
			<?=$ownerless_hosts;?>
		</div>
		<div class="tab-pane<?=($page == 6) ? " active" : '';?>" id="tab6">
			<h5>Расход наклеек</h5>
			<a href="/integrity/labelreport" class="btn btn-primary" style="margin-bottom:10px;">Отчёт в формате Word</a>
			<?=(isset($yield)) ? $yield : "Нет данных для обработки";?>
			<a href="/integrity/labelreport" class="btn btn-primary" style="margin-bottom:10px;">Отчёт в формате Word</a>
		</div>
	</div>
</div>

<div class="modal hide" id="labelMove" style="width:640px;">
	
		<div class="modal-header">
			<h5>Уточнение данных наклейки<span class="pull-right" style="cursor:pointer;" data-dismiss="modal">&times;</span></h5>
		</div>
		<div class="modal-body" id="">
			Новый номер: <input type="text" id="newNum" name="newNum">
			<input type="hidden" id="pcId" name="pcId">
			<input type="hidden" id="host" name="host">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn" data-dismiss="modal">Закрыть</button>
			<button type="button" id="labelSaver" class="btn btn-primary">Изменить номер</button>

		</div>

</div>

<script type="text/javascript">
<!--

	$(".modal").modal("hide");

	$(".labelMover").click(function(){
		var ref  = $(this).attr('ref');
		$("#newNum").val( $(this).html() );
		$("#pcId").val(ref);

		$("#labelSaver").unbind().click(function(){
			$.ajax({
				url       : "/integrity/savelabel",
				type      : "POST",
				dataType  : "text",
				data      : {
					label : "1" + $("#newNum").val().split("-").join(""),
					pcID  : ref
				},
				success   : function (data) {
					if ( data === "Fail" ) {
						alert("Этот номер наклейки уже назначен другому компьютеру. Смена невозможна");
						return false;
					}
					if ( data === "Not found" ) {
						alert("Компьютер с таким ID не найден. Данные не обновлены");
						return false
					}
					$(".labelMover[ref='"+ref+"']").html($("#newNum").val());
					$("#labelMove").modal('hide');
				},
				error     : function(data,stat,err){
					//alert([data,stat,err].join("<br>"));
				}
			});
		});
		$("#labelMove").modal('show');
	});

	$(".button-hb").click(function(){
		$("#bindedhost").empty().html($(this).attr('hm'));
		$("#b_hostid").val($(this).attr('ref'));
	});

	$("#doBind").click(function(){
		$.ajax({
			url: "/integrity/hosts_bind/" + $("#b_uid").val() + '/' + $("#b_hostid").val(),
			type: "POST",
			dataType: "html",
			cache: false,
			success: function(data){
				//alert(typeof parseInt($("#b_counter").html() - 1));
				$("#b_counter").html(parseInt($("#b_counter").html()) - 1);
				$("#def" + $("#b_hostid").val()).addClass("hide");
			},
			error: function(data,stat,err){
				//alert([data,stat,err].join("<br>"));
			}
		});
	});

	$(".button-serv").click(function(){
		btn = $(this);
		$.ajax({
			url: "/integrity/mark_serv/" + $(this).attr("ref"),
			type: "POST",
			dataType: "html",
			cache: false,
			success: function(data){
				//alert(typeof parseInt($("#b_counter").html() - 1));
				$("#b_counter").html(parseInt($("#b_counter").html()) - 1);
				$("#def" + btn.attr("ref")).addClass("hide");
			},
			error: function(data,stat,err){
				//alert([data,stat,err].join("<br>"));
			}
		});
	});

	$(".button-noise").click(function(){
		btn = $(this);
		$.ajax({
			url: "/integrity/mark_noise/" + $(this).attr("ref"),
			type: "POST",
			dataType: "html",
			cache: false,
			success: function(data){
				//alert(typeof parseInt($("#b_counter").html() - 1));
				$("#b_counter").html(parseInt($("#b_counter").html()) - 1);
				$("#def" + btn.attr("ref")).addClass("hide");
			},
			error: function(data,stat,err){
				//alert([data,stat,err].join("<br>"));
			}
		});
	});

	$(".label-write").click(function(){
		label = $(this).attr("label");
		host  = $(this).attr("host");
		id    = $(this).attr("item");
		$.ajax({
			url: "/integrity/writelabel",
			type: "POST",
			data: {
				host  : host,
				label : label
			},
			success: function(data){
				$("#wr" + id).removeClass("btn-danger").addClass("btn-success");
			},
			error: function(data,stat,err){
				//alert([data,stat,err].join("<br>"));
			}
		});
	});

	/*
		$("#doLabelReport").click(function(){
			$.ajax({
				url: "/integrity/labelreport",
				type: "POST",
				dataType: "html",
				success: function(data){},
				error: function(data,stat,err){
					alert([data,stat,err].join("<br>"));
				}
			});
		});
	*/
	$("#hostHelp").click(function(){
		$("#hostHelpDiv").fadeToggle()
	});
	



//-->
</script>