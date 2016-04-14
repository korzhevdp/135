<h2>��������� ��� �����&nbsp;&nbsp;&nbsp;&nbsp;<small>������ � ������������</small></h2>
<hr>

<ul class="nav nav-tabs">
	<li <?=($page === 1) ? 'class="active"' : "" ?>><a href="#switch"  data-toggle="tab">�����������</a></li>
	<li <?=($page === 2) ? 'class="active"' : "" ?>><a href="#user"    data-toggle="tab">������������</a></li>
	<li <?=($page === 3) ? 'class="active"' : "" ?>><a href="#control" data-toggle="tab">����������</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane <?=($page === 1) ? 'active' : "" ?>" id="switch">
		����� ����������� ������������
	</div>

	<div class="tab-pane <?=($page === 2) ? 'active' : "fade" ?>" id="user">
		����� �����������<br>
		HOST:&nbsp;<input type="text" id="PCHost" placeholder="hostname%" value="<?=($pcsearch) ? $pcsearch : "" ?>"> <a id="searchPC" href="/network/get_host/<?=($pcsearch) ? $pcsearch : "" ?>" class="btn btn-primary btn-small" style="margin-top:-8px;">�����</a><br>
		MAC:&nbsp;&nbsp;<input type="text" id="PCMAC" placeholder="MAC: %ff:ff" value="<?=($macsearch) ? $macsearch : "" ?>"> <a id="searchMAC" href="/network/get_mac/<?=($macsearch) ? $macsearch : "" ?>" class="btn btn-primary btn-small" style="margin-top:-8px;">�����������</a>
	</div>

	<div class="tab-pane <?=($page === 3) ? 'active' : "fade" ?>" id="control">
		������ ����������<br>
		<form method="post" action="/network/show_switches">
			�������� ����������� � ��������&nbsp;&nbsp;<input type="text" name="sSwitchesRange"> <button type="submit" class="btn btn-primary btn-small">�����������</button>
		</form>

		<hr>
		<strong>������� ���������� � ����</strong><br>
		����������� � ������� 192.168.<input type="text" id="switchesRange" maxlength=3 placeholder="����� �������">.0/24 <button id="collectSwitches" class="btn btn-primary btn-small" >�����������</button><br>

		MAC ������ �� ������������ � ��������� IP <input type="text" id="macRange" placeholder="192.168..." value="192.168."> <button id="collectMAC" class="btn btn-primary btn-small">�����������</button>

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
				// �������� ���������� � �������
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
				// �������� ���������� � �������
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