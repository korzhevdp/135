<style type="text/css">
	#getPCsData{
		margin-right: 150px;
	}
	#systemData{
		margin-top: 60px;
	}
</style>
<h2>Стандартные отчёты</h2><hr>
<div class="tabbable"> <!-- Only required for left/right tabs -->
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab1" data-toggle="tab">Текущие конфигурации ПК</a></li>
		<li><a href="#tab2" data-toggle="tab">Руководители</a></li>
		<li><a href="#tab4" data-toggle="tab">Беспочтовые руководители</a></li>
		<li><a href="#tab3" data-toggle="tab">Бескомпьютерные</a></li>
	</ul>
	<div class="tab-content">

		<div class="tab-pane active" id="tab1">
			<div class="input-prepend">
				<span class="add-on">Компьютер</span>
				<input type="text" id="pcName" list="dl_pc">
				<span class="add-on">Категория</span>
				<select id="paramCategory">
					<option value="">Выберите категорию</option>
					<?=$cat;?>
				</select>
				<span class="add-on">Параметр</span>
				<select id="paramName"><option value="">Выберите категорию</option></select>
				<img src="http://192.168.1.35/images/ajax-loader.gif" style="width:16px;height:16px;border:none;margin-left:10px;margin-top:4px;" id="ajax-loader" class="hide" alt="">
			</div>
			<span class="btn btn-large btn-warning pull-right" id="getPCsData">Показать</span>
			<div id="output">
				
			</div>
			<br><br><hr>
		</div>
		<div class="tab-pane" id="tab2">
			<table class="table table-bordered table-condensed table-striped">
			<tr>
				<th>#</th><th>email</th><th>ФИО</th><th>Должность</th><th>Подразделение</th>
			</tr>
			<?=$cios;?>
			</table>
		</div>
		<div class="tab-pane" id="tab4">
			<table class="table table-bordered table-condensed table-striped">
			<tr>
				<th>#</th><th style="width:250px;">ФИО</th><th>Должность</th><th>Подразделение</th>
			</tr>
			<?=$emailless;?>
			</table>
		</div>
		<div class="tab-pane" id="tab3">
			<table class="table table-bordered table-condensed table-striped">
			<tr>
				<th>ФИО</th><th>Ожидаемое имя ПК</th><th>Подразделение</th>
			</tr>
			<?=$pcless;?>
			</table>
		</div>
	</div>
</div>

<datalist id="dl_pc" style="display:none;">
	<?=$pcnames;?>
</datalist>

<script type="text/javascript">
<!--


	$("#paramCategory").change(function(){
		cat = $(this).val();
		if(!cat.length){
			$("#paramName").empty().append('<option value="">Выберите категорию</option>');
			return false;
		}
		$("#ajax-loader").removeClass("hide");
		$.ajax({
			url: "/reports/selectparams",
			type: "POST",
			data: {
				cat  : cat
			},
			dataType: "html",
			cache: false,
			success: function(data){
				$("#paramName").empty().append('<option value="">Выберите категорию</option>' + data);
				//$("#paramName").attr("placeholder", "Введите параметр")
				$("#ajax-loader").addClass("hide");
			},
			error: function(data,stat,err){
				console.log([data,stat,err].join("\n"));
			}
		});
	});

	$("#getPCsData").click(function(){
		selectPC(1)
	});

	function selectPC(sortmode){
		$("#ajax-loader").removeClass("hide");
		//alert(sortmode);
		$.ajax({
			url: "/reports/selectpc",
			type: "POST",
			data: {
				host : $("#pcName").val(),
				cat  : $("#paramCategory").val(),
				par  : $("#paramName").val(),
				sort : sortmode
			},
			dataType: "html",
			cache: false,
			success: function(data){
				$("#output").empty().html(data);
				$("#ajax-loader").addClass("hide");
				$("#sortByHost").unbind().click(function(){
					selectPC(1)
				});
				$("#sortByDate").unbind().click(function(){
					selectPC(2)
				});
				$("#sortByVal").unbind().click(function(){
					selectPC(3)
				});
				$("#sortByDep").unbind().click(function(){
					selectPC(4)
				});
				$("#sortByCat").unbind().click(function(){
					selectPC(5)
				});
				$("#sortByParam").unbind().click(function(){
					selectPC(6)
				});
			},
			error: function(data, stat, err){
				console.log([data, stat, err].join("\n"));
			}
		});
	}
//-->
</script>