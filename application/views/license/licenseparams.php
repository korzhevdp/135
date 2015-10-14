<h4>Информация о лицензии <a href="/licenses/statistics" class="btn btn-info pull-right" style="margin-bottom:10px;">К списку лицензий</a></h4>
<table class="table table-bordered table-condensed table-striped table-hover">
<tr>
	<td><a href="/licenses/statistics/<?=$id;?>"><b><?=$lname;?></b></a><br><?=$number;?><br><small class="muted">от <?=$issue_date;?></small></td>
	<td><?=$purchase_date;?></td>
	<td><?=$purchase_info;?><br><?=$program;?><br><small class="muted"><?=$rname;?></small></td>
	<td style="width:140px;text-align:center;vertical-align:middle;"><?=$activate;?></td>
	<td style="width:80px;text-align:center;vertical-align:middle;">
		<?=$stat1?> <?=$stat2?>
	</td>
</tr></table>
<form class="form-horizontal" method="POST" action="/licenses/save_license/<?=$lid;?>" style="margin-bottom:70px;">
	<div class="control-group">
		<label class="control-label span2" for="lnum">№ лицензии</label>
		<div class="controls span10">
			<input type="text" name="lnum" id="lnum" class="span12" placeholder="00000000" value="<?=$number;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="licr">Лицензиар</label>
		<div class="controls span10">
			<select name="licr" id="licr" class="span12">
				<option value="0"> - Выберите лицензиара - </option>
				<?=$licr;?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="dati">Дата выпуска</label>
		<div class="controls span10">
			<input type="text" id="dati" name="dati" class="span12 wDate" placeholder="00.00.0000" value="<?=$issue_date;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="datp">Дата покупки</label>
		<div class="controls span10">
			<input type="text" id="datp" name="datp" class="span12 wDate" placeholder="00.00.0000" value="<?=$purchase_date;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="resl">Реселлер</label>
		<div class="controls span10">
			<select name="resl" id="resl" class="span12">
				<option value="0"> - Выберите реселлера - </option>
				<?=$resl;?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="prog">Программа</label>
		<div class="controls span10">
			<input type="text" id="prog" name="prog" class="span12" placeholder="00000000" value="<?=$program;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="info">Информация</label>
		<div class="controls span10">
			<textarea id="info" name="info" class="span12" placeholder="данные аукциона, запроса котировок; реквизиты: муниципальных контрактов, договоров, счетов, счетов-фактур" cols="5" rows="5"><?=$purchase_info;?></textarea>
		</div>
	</div>
	<div class="control-group pull-right" style="margin-top:20px;">
		<a class="btn btn-warning" href="/licenses/addnew/<?=$lid;?>" title="Добавить отредактировать или удалить наборы ПО">Редактировать</a>&nbsp;&nbsp;
		<a class="btn btn-warning" href="/licenses/verify_license/<?=$lid;?>" title="Нажать по успешному окончанию проверки наличия на складе">Верификация</a>&nbsp;&nbsp;
		<button type="submit" class="btn btn-primary">Сохранить описание</button>
	</div>
</form>
<script type="text/javascript">
<!--
$(function($){
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '<<',
		nextText: '>>',
		currentText: 'Сегодня',
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
		dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
		dayNamesShort: ['вос', 'пон','втр','срд','чтв','пят','суб'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$(".wDate").datepicker($.datepicker.regional['ru']);
	$(".wDate").datepicker( "option", "changeYear", true);
	$("#licAct").click(function(){
		lid = $(this).attr("ref");
//		alert(lid);
		$.ajax({
			url: "/licenses/active",
			dataType: "html",
			data: {
				lid: lid
			},
			type: "POST",
			success: function(data){
				if(parseInt(data) == 0){
					$("#licAct").removeClass("btn-inverse").addClass("btn-success").html("Активировать");
				}else{
					$("#licAct").removeClass("btn-success").addClass("btn-inverse").html("Деактивировать");
				}
					//licAct
			},
			error: function(data,stat,err){
				alert("ACT error");
			}
		});
	})
});
//-->
</script>