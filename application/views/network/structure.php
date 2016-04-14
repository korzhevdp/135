<h2>Структура ЛВС мэрии&nbsp;&nbsp;&nbsp;&nbsp;<small>Отчёты и визуализация</small></h2>
<hr>

<ul class="nav nav-tabs">
	<li <?=($page === 1) ? 'class="active"' : "" ?>><a href="#switch"  data-toggle="tab">Коммутаторы</a></li>
	<li <?=($page === 2) ? 'class="active"' : "" ?>><a href="#user"    data-toggle="tab">Пользователи</a></li>
	<li <?=($page === 3) ? 'class="active"' : "" ?>><a href="#control" data-toggle="tab">Управление</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane <?=($page === 1) ? 'active' : "" ?>" id="switch">
		Схемы подключения коммутаторов
	</div>

	<div class="tab-pane <?=($page === 2) ? 'active' : "fade" ?>" id="user">
		Поиск компьютеров<br>
		HOST:&nbsp;<input type="text" id="PCHost" placeholder="hostname%" value="<?=($pcsearch) ? $pcsearch : "" ?>"> <a id="searchPC" href="/network/get_host/<?=($pcsearch) ? $pcsearch : "" ?>" class="btn btn-primary btn-small" style="margin-top:-8px;">Поиск</a><br>
		MAC:&nbsp;&nbsp;<input type="text" id="PCMAC" placeholder="MAC: %ff:ff" value="<?=($macsearch) ? $macsearch : "" ?>"> <a id="searchMAC" href="/network/get_mac/<?=($macsearch) ? $macsearch : "" ?>" class="btn btn-primary btn-small" style="margin-top:-8px;">Сканировать</a>
	</div>

	<div class="tab-pane <?=($page === 3) ? 'active' : "fade" ?>" id="control">
		Органы управления<br>
		<form method="post" action="/network/show_switches">
			Показать коммутаторы в подсетях&nbsp;&nbsp;<input type="text" name="sSwitchesRange"> <button type="submit" class="btn btn-primary btn-small">Сканировать</button>
		</form>

		<hr>
		<strong>Собрать информацию в базу</strong><br>
		Коммутаторы в подсети 192.168.<input type="text" id="switchesRange" maxlength=3 placeholder="Номер подсети">.0/24 <button id="collectSwitches" class="btn btn-primary btn-small" >Сканировать</button><br>

		MAC адреса на коммутаторах в диапазоне IP <input type="text" id="macRange" placeholder="192.168..." value="192.168."> <button id="collectMAC" class="btn btn-primary btn-small">Сканировать</button>

	</div>
</div>
<div id="result" style="width:100%;height:500px;overflow:auto;border:2px solid grey;"><?=$data;?></div>

<script type="text/javascript">
<!--
	$("#PCHost").keyup(function() {
		var string = $(this).val();
		if (!string.length) {
			return false;
		}
		$("#searchPC").attr("href", "/network/get_host/" + string);
	});

	$("#PCMAC").keyup(function() {
		var string = $(this).val();
		if (!string.length) {
			return false;
		}
		$("#searchMAC").attr("href", "/network/get_mac/" + string);
	});
	/*/
	$("#scanPC").click(function(){
		$.ajax({
			url: "network/get_host",
			data: {
				host: $("#PCHost").val()
			},
			type: "POST",
			dataType: "html",
			//crossDomain: true,
			success: function (data) {
				$("#result").html(data);
			},
			error: function (data, stat, err) {
				console.log([data, stat, err].join("<br>"));
			}
		});
	});
	*/
	$("#scanMAC").click(function(){
		$.ajax({
			url: "network/get_mac",
			data : {
				mac:  $("#PCMAC").val()
			},
			type: "POST",
			dataType: "html",
			//crossDomain: true,
			success: function (data) {
				$("#result").html(data);
			},
			error: function (data, stat, err) {
				console.log([data, stat, err].join("<br>"));
			}
		});
	});

	$("#collectSwitches").click(function(){
		var result = $.ajax({
			url: "network/collect_switches/",
			type: "POST",
			data: {
				netlist: $("#switchesRange").val()
			},
			dataType: "html",
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// прогресс скачивания с сервера
				xhr.addEventListener("progress", function(evt){
					alert("z");
				}, false);
				return xhr;
			},
			//crossDomain: true,
			success: function (data) {
				$("#result").html(data);
			},
			error: function (data, stat, err) {
				console.log([data, stat, err].join("<br>"));
			}
		});
	});
	
	$("#collectMAC").click(function(){
		$.ajax({
			url: "network/collect_macs/",
			type: "POST",
			data: {
				switchesRange: $("#macRange").val()
			},
			dataType: "html",
			xhr: function() {
				var xhr = new window.XMLHttpRequest();
				// прогресс скачивания с сервера
				xhr.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {
						console.log("progress");
					}
				}, false);
				return xhr;
			},
			//crossDomain: true,
			success: function (data) {
				$("#result").html(data);
			},
			error: function (data, stat, err) {
				console.log([data, stat, err].join("<br>"));
			}
		});
	});
//-->
</script>