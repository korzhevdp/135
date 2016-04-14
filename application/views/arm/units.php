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
	input[type=text],
	textarea {
		width:540px;
	}
	input.short{
		width:200px;
	}
	.shortest{
		width:30px !important;
	}
	.long{
		width:554px;
		height:26px;
		padding-left: 5px;
	}
	select.short{
		width:305px;
	}
	select.vshort{
		width:99px;
	}
	select{
		height:30px;
	}

</style>
<h3>Оборудование и АРМ</h3>


<ul class="nav nav-tabs" id="mainTab">
	<li class="active"><a href="#warehouse" data-toggle="tab">Инвентарные единицы</a></li>
	<li><a href="#add" data-toggle="tab">Внесение партии оборудования</a></li>
</ul>

<div class="tab-content" id="mainTabContent">
	<div class="tab-pane active" id="warehouse">
		<div class="input-prepend control-group">
			<span class="add-on pre-label">Фильтр</span>
			<input class="long" name="invfilter" form="invData" id="invFilter" value="<?=$invfilter?>">
		</div>
		<div class="input-prepend control-group">
			<span class="add-on pre-label">Тип устройства</span>
			<input class="long" name="invfilter2" form="invData" id="invFilter2" value="<?=$invfilter2?>">
		</div>
		<div class="input-prepend control-group">
			<span class="add-on pre-label">Инвентарный номер</span>
			<select name="invnum" id="invunits" class="long" form="invData" style="height:31px;">
			</select>
		</div>
		

		<button type="button" class="btn btn-primary btn-small" id="getInvUnit" style="margin-left:614px;margin-top:15px;">Показать</button>

		<hr>
		<h4>Инвентарный номер: <span id="inv">000000000000</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Серийный номер: <span id="serial">000000000000</span></h4>
		<input type="hidden" name="invNum" value="">
		Дата приобретения: <input type="text" name="datestart" class="short withCal" id="guarantee_start" value="" style="margin-right:30px;">
		Окончание: <input type="text" class="short withCal" name="dateend" id="guarantee_end" value=""><br>
		Поставщик: <input type="text" name="supplier" id="supplier" value="" style="margin-left:52px;"><br>
		Получатель: <select name="receiver" id="receiver" class="long" style="margin-left:49px;height:28px;">
		<option value="0">Выберите получателя</option>
		<?=$users;?>
		</select>&nbsp;&nbsp;&nbsp;<strong class="muted">должен быть <span id="fio">[[Ф.И.О.]]</span></strong><br>
		Местонахождение: <input type="text" value="" name="room" id="room" class="short" style="margin-left:4px;margin-right:20px">
		<!-- <input type="checkbox" name="syncWithReceiver" checked="checked" style="margin-top:-4px;">&nbsp;&nbsp;&nbsp;Взять из данных о получателе -->
		<br>
		<button type="submit" class="btn btn-primary btn-small" id="invUnitSubmit" style="margin-left:614px;">Сохранить</button>
		<hr>
		<div id="contents" class=""></div>
		<hr>
		<?=$additional?>
	</div>
	<div class="tab-pane" id="add">
	</div>
</div>



<datalist id="typelist">
	<?=$typelist?>
</datalist>

<datalist id="invlist">
	<?//=$invlist?>
</datalist>

<datalist id="namelist">
	<?=$namelist?>
</datalist>

<form method="post" action="/arm/warehouse" id="invData">
	
</form>
<div id="ann1" style="display:none;position:absolute;height:45px;width:250px; font-size:20px;top:40px;right:50px;border:1px solid green; color:#33cc33; background-color:#DDffDD; padding-top:12px;">
	<center>Сохранено успешно</center>
</div>
<div id="ann2" style="display:none;position:absolute;height:45px;width:250px; font-size:20px;top:40px;right:50px;border:1px solid red; color:#CC3333; background-color:#FFDDDD; padding-top:12px;">
	<center>Сохранение не удалось</center>
</div>
<script type="text/javascript">
<!--
	cur_inv = '<?=$cur_inv?>';
	
	$(function() {
		$(".withCal").datepicker();
	});

/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
	$(function($){
		$.datepicker.regional['ru'] = {
			closeText: 'Закрыть',
			prevText: '&#x3c;Пред',
			nextText: 'След&#x3e;',
			currentText: 'Сегодня',
			monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
			monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
			dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
			dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
			dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
			weekHeader: 'Не',
			dateFormat: 'dd.mm.yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: '',
			changeYear: true
		};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
	});

	$(".devSaver").click(function(){
		dev = $(this).attr("dev");
		$.ajax({
			url: "/arm/dev_save",
			data: {
				devtype : $("#devtype" + dev).val(),
				devname : $("#devname" + dev).val(),
				qty     : $("#qty"     + dev).val(),
				serial  : $("#serial"  + dev).val(),
				dev     : dev
			},
			type: "POST",
			success: function(data){
				$("#ann1").fadeIn().delay(5000).fadeOut();
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter").keyup(function(){
		$("#invFilter2").val("");
		text = $(this).val();
		if(text.length < 4){
			return false
		}
		$.ajax({
			url: "/arm/get_inv_units",
			data: {
				text : text,
			},
			type: "POST",
			success: function(data){
				$("#invunits").empty().append(data);
				//$("#invunits option[value=" + cur_inv + "]").attr("selected", "selected");
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#getInvUnit").click(function(){
		if(!$("#invunits option").length){
			return false;
		}else{
			console.log($("#invunits").val());
			//return false;
		}
		$.ajax({
			url: "/arm/get_inv_unit",
			data: {
				inv: $("#invunits").val()
			},
			type: "POST",
			dataType: 'script',
			success: function(){
				$("#contents").empty().append(data.devcontent);
				$("#inv").html(data.info.inv);
				$("#serial").html(data.info.serial);
				$("#supplier").val(data.info.supplier);
				$("#guarantee_start").val(data.info.guarantee_start);
				$("#guarantee_end").val(data.info.guarantee_end);
			},
			error: function(data, stat, err){
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter2").keyup(function(){
		$("#invFilter").val("");
		text = $(this).val();
		if(text.length < 4){
			return false
		}
		$.ajax({
			url: "/arm/get_inv_units",
			data: {
				text : text,
			},
			type: "POST",
			success: function(data){
				$("#invunits").empty().append(data);
				$("#invunits option[value=" + cur_inv + "]").attr("selected", "selected");
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter").keyup();
//-->
</script>