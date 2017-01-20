<h2>Структура ЛВС мэрии&nbsp;&nbsp;&nbsp;&nbsp;<small>Отчёты и визуализация</small></h2>
<hr>
<ul class="nav nav-tabs">
	<li <?=($page === 1) ? 'class="active"' : "" ?>><a href="#switch"  data-toggle="tab">Коммутаторы</a></li>
	<li <?=($page === 2) ? 'class="active"' : "" ?>><a href="#user"    data-toggle="tab">Пользователи</a></li>
	<li <?=($page === 3) ? 'class="active"' : "" ?>><a href="#control" data-toggle="tab">Управление</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane <?=($page === 1) ? 'active' : "fade" ?>" id="switch">
		Схемы подключения коммутаторов
	</div>

	<div class="tab-pane <?=($page === 2) ? 'active' : "fade" ?>" id="user">
		<form method="post" action="/network/get_swusers">
			SWITCH:&nbsp;<input type="text" name="switchip" id="switchip" placeholder="192.168." value="<?=($switchip) ? $switchip : "192.168." ?>">
			<button type="submit" class="btn btn-primary btn-small" style="margin-top:-8px;">Поиск</button>
		</form>
		<form method="post" action="/network/get_host">
			HOST:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="host" id="PCHost" placeholder="hostname%" value="<?=($pcsearch) ? $pcsearch : "" ?>">
			<button type="submit" class="btn btn-primary btn-small" style="margin-top:-8px;">Поиск</button>
		</form>
		<form method="post" action="/network/get_mac">
			MAC:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="mac" type="text" id="PCMAC" placeholder="MAC: %ff:ff" value="<?=($macsearch) ? $macsearch : "" ?>">
			<button type="submit" class="btn btn-primary btn-small" style="margin-top:-8px;">Поиск</button>
		</form>
		<div id="result" style="width:99%;height:500px;overflow:auto;border:2px solid grey;"><?=$data;?></div>
	</div>

	<div class="tab-pane <?=($page === 3) ? 'active' : "fade" ?>" id="control">
		<form method="post" action="/network/show_switches">
			Показать коммутаторы в подсетях<br>
			<input type="text" name="sSwitchesRange" title="Третьи октеты сети через запятую" placeholder="Третьи октеты сети через запятую" style="width:312px;"> 
			<button type="submit" class="btn btn-primary" style="margin-top:-12px;">Показать</button>
		</form>
		<form method="post" action="/network/collect_macs">
			Опросить MAC адреса на коммутаторах в диапазоне IP и добавить их в базу данных
			<div class="input-prepend input-append">
				<span class="add-on">192.168.</span>
				<input type="text" name="macRange" placeholder="xxx.xxx" value="">
				<span class="add-on">.0/24</span>
				<button id="collectSwitches" class="btn btn-primary btn-small" style="margin-left:0px;height:30px;">Сканировать</button>
			</div>
		</form>
		<hr><br><br><br><br><br>

		<h4>Эти операции являются "разрушающими":&nbsp;&nbsp;&nbsp;&nbsp;</h4>
		<div id="" class="alert alert-danger">
			<form method="post" action="/network/collect_switches">
				<strong>Переписать данные о коммутаторах находящихся в подсети:</strong>
				<div class="input-prepend input-append">
					<span class="add-on">192.168.</span>
					<input type="text" name="netlist" maxlength=3 placeholder="Номер подсети"> 
					<span class="add-on">.0/24</span>
					<span class="btn btn-warning btn-small scanUnlocker" style="margin-left:0px;height:22px;"><i class="icon-lock icon-white"></i></span>
					<button id="reCollectSwitches" type="submit" class="btn btn-danger btn-small" style="margin-left:0px;height:30px;" disabled="disabled">Сканировать коммутаторы</button>
				</div>
			</form>
			<strong>Переписать информацию о MAC на коммутаторах в диапазоне</strong>
			<form method="post" action="/network/rescan_range">
				<div class="input-prepend input-append">
					<span class="add-on">192.168.</span>
					<input type="text" name="macRange" placeholder="192.168." value="1.">
					<span class="add-on">.0/24</span>
					<span class="btn btn-warning btn-small regetUnlocker" style="margin-left:0px;height:22px;"><i class="icon-lock icon-white"></i></span>
					<button id="reCollectMacs" class="btn btn-danger btn-small" style="margin-left:0px;height:30px;" disabled="disabled">Очистить MAC-адреса и сканировать заново</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
	$(".scanUnlocker").click( function () {
		$("#reCollectSwitches").prop("disabled", false);
		function w1() {
			$("#reCollectSwitches").prop("disabled", true);
		}
		setTimeout(w1, 2000);
	});
	$(".regetUnlocker").click( function () {
		$("#reCollectMacs").prop("disabled", false);
		function w1() {
			$("#reCollectMacs").prop("disabled", true);
		}
		setTimeout(w1, 2000);
	});
//-->
</script>
