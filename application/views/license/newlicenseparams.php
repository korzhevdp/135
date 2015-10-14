<h3>Новая лицензия <a href="/licenses/statistics" class="btn btn-info pull-right" style="margin-bottom:10px;">К списку лицензий</a></h3>
<table class="table table-bordered table-condensed table-striped table-hover">
<tr>
	<td><a href="/licenses/statistics/<?=(isset($id)) ? $id : "";?>.'"><b><?=$lname;?></b></a><br><?=$number;?><br><small class="muted">от <?=$issue_date;?></small></td>
	<td><?=$purchase_date;?></td>
	<td><?=$purchase_info;?><br><?=$program;?><br><small class="muted"><?=$rname;?></small></td>
	<td>
		<?=$stat1?> <?=$stat2?>
	</td>
</tr></table>
<form class="form-horizontal" method="POST" action="/licenses/add_new_license/<?=$lid;?>" style="margin-bottom:70px;">
	<div class="control-group">
		<label class="control-label span2" for="lnum">№ лицензии</label>
		<div class="controls span10">
			<input type="text" name="lnum" id="lnum" class="span12" placeholder="00000000" value="<?=$number;?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2" for="licr">Лицензиар</label>
		<div class="controls span10">
			<select name="licr" id="licr" class="span11">
				<option value="0"> - Выберите лицензиара - </option>
				<?=$licr;?>
			</select>
			<button type="button" class="btn" id="button-addlicr" title="Добавить лицензиара">+</button>
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
			<select name="resl" id="resl" class="span11">
				<option value="0"> - Выберите реселлера - </option>
				<?=$resl;?>
			</select>
			<button type="button" class="btn" id="button-addresl" title="Добавить реселлера">+</button>
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
		<a class="btn btn-warning" href="/licenses/verify_license/<?=$lid;?>" title="Нажать по успешному окончанию проверки наличия на складе">Верификация</a>&nbsp;&nbsp;
		<button type="submit" class="btn btn-primary">Сохранить описание</button>
	</div>
	<input type="hidden" name="license_id" value="<?=$lid;?>">
</form>
<hr>

<div style="height:20px;">
	<button id="button-addPOtoset" class="btn pull-right">Добавить набор ПО в Лицензию</button>
</div>

<div class="well well-small" style="margin-top:30px;">Состав лицензии определяется количеством и объёмом наборов.<br>Лицензия может включать в себя несколько разнородных видов ПО, например пользовательские операционные системы и серверное ПО. Сответственно, она будет состоять из одного или нескольких наборов ПО.<br>
	Набор ПО - это совокупность программных продуктов которые могут быть поставлены по одной лицензии на конкретное ПО по принципу "даунгрейда". Например, по лицензии на Windows 8.1 можно установить Windows 8, Windows 7 и Windows Vista, но не более количества, предусмотренного лицензией. Однородное программное обеспечение, на которое возможен переход по даунгрейду должны быть скомпонованы в пределах одного набора. 
	<br> В лицензиях указано новейшее ПО и несколько вариантов даунгрейда. Самое новое ПО из набора дожно быть помечено основным.<br><br> При установке из набора более старой версии ПО количество доступных инсталляций уменьшается для всего набора.<br>Набор может быть удалён только целиком. <br>
</div>

<h4>Структура лицензии</h4>

<?=(isset($sets)) ? $sets : "";?>
<!-- Плашка добавления лицензиара -->
<div id="LicLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="LicLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Добавить лицензиара ПО</h3>
	</div>
	<div class="modal-body">
		<form method="post" id="form3" action="/licenses/add_licensiar" class="form-horizontal">
			<div class="control-group">
				<label class="control-label">Название лицензиара</label>
				<div class="controls">
					<input type="text" name="licr_name" class="span12">
				</div>
			</div>
			<input type="hidden" name="redirect" value="<?=(isset($id)) ? $id : "";?>">
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" id="LicrModalSubmit" title="Назначить лицензию из пула">Готово</button>
	</div>
</div>

<!-- Плашка добавления реселлера -->
<div id="ReslLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ReslLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">Добавить реселлера ПО</h3>
	</div>
	<div class="modal-body">
		<form method="post" id="form2" action="/licenses/add_reseller" class="form-horizontal">
			<div class="control-group">
				<label class="control-label">Название реселлера</label>
				<div class="controls">
					<input type="text" name="resl_name" class="span12">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Адрес реселлера</label>
				<div class="controls">
					<input type="text" name="resl_addr" class="span12">
				</div>
			</div>
			<input type="hidden" name="redirect" value="<?=(isset($id)) ? $id : "";?>">
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" id="reslModalSubmit" title="Назначить лицензию из пула">Готово</button>
	</div>
</div>

<!-- Плашка добавления ПО в набор -->
<div id="SetLabel" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ReslLabel" aria-hidden="true">
	<form method="post" action="/licenses/addpotoset" class="form-horizontal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Добавить ПО в набор</h3>
		<div>Количество устанавливаемых копий: <input type="text" id="po_num" name="po_num" value="0"><input type="hidden" name="lid" value="<?=$id;?>"></div>
		<div>Начальный номер наклейки: <input type="text" id="startnum" name="startnum" value="0"></div>
	</div>

	<div class="modal-body">
		
		<div id="addsoft">
			список имеющихся типов Софта
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" type="submit" title="Назначить выбранные типы ПО в набор">Готово</button>
	</div>
	</form>
</div>
<script type="text/javascript" src="/jscript/lsmc.js"></script>

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
});
//-->
</script>