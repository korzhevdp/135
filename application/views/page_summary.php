<h2>Общая информация об обстановке на фронтах</h2>

<h4>Переданные сообщения</h4>
<table class="table table-striped table-bordered table-condensed" style="width:100%">
<tr>
	<td>Сообщение</td>
	<td  style="width:70px;">Отправитель</td>
	<td style="width:50px;"><i class="icon-calendar"></i></td>
	<td style="width:50px;"><i class="icon-fast-forward"></td>
	<td style="width:30px;"><button class="btn btn-mini btn-warning"><i class="icon-bell icon-white"></button></td>
</tr>
<?=(isset($messages)) ? $messages : "";?>
</table>
<button class="btn btn-info" id="mesModal">Добавить сообщение</button>
<div id="addMessage" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">Сообщения <small>для операторов</small></h3>
	</div>
	<div class="modal-body" style="height:400px; overflow:auto;">
		<form method="post" id="form2"class="form-inline"  action="/admin/postmessage">
			<textarea name="text" rows="4" cols="5" style="width:97%;height:200px;margin-bottom:10px;" placeholder="Введите текст сообщения..."></textarea>
			 Дата контроля: <input type="text" name="enddate" id="enddate" class="input-small" placeholder="дата">
			<h5>Получатели</h5><hr>
			<?=$receivers;?>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" id="ModalOk" title="Отправить сообщение получателям">Готово</button>
	</div>
</div>
<hr>
<h4>Курируемые пользователи <small>всего:&nbsp;&nbsp;<?=$user_num;?></small></h4>

<div class="span12" style="height:200px;overflow:auto;margin-left:0px;margin-top:10px;margin-bottom:20px;border:2px solid #c6c6c6">
	<table class="table table-striped table-bordered table-condensed" style="width:100%">
		<tr>
			<th class="span4">Имя пользователя</th>
			<th class="span4">Куратор</th>
			<th class="span4">Снять с учёта</th>
		</tr>
		<?=$user_table;?>
	</table>
</div>

<div id="" class="span12" style="margin-bottom:40px;margin-left:0px;">
	<h2>Заявки по очередям&nbsp;&nbsp;<small>от начала времён</small></h2><hr>
	Подано заявок: <strong><?=$o_overall?></strong><br>
	Выполнено отделом СА: <strong><?=$o_ok;?></strong>&nbsp;&nbsp;&nbsp;
	<small class="muted">и не выполнено по разным причинам:</small>&nbsp;&nbsp;&nbsp;
	<strong><?=($o_overall - $o_ok);?></strong>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin/stuck" target="_blank">Что это за заявки? </a><br>
	Завершено кураторами: <strong><?=$o_applied?></strong><br>
	Ожидают исполнения: <strong><?=$o_toapply;?></strong><br>
</div>

 <?=$ordprocessing;?>

 <div style="height:60px;">
	
 </div>
<!-- <iframe name="aa" src="http://freehand.minigis.net" width="1300" height="700" style="margin:0px;padding:0px;margin-bottom:200px;"></iframe> -->

<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">

	$('#addMessage').modal({show: 0});
	$("#mesModal").click(function(){
		$('#addMessage').modal('show');
	});
	$("#ModalOk").click(function(){
		$("#form2").submit();
	});

	 $(function() {
		$( "#enddate" ).datepicker();
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
			yearSuffix: ''
		};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
	});
</script> 
