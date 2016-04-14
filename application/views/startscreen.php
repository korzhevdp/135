<ul class="nav nav-tabs">
	<li class="active" title="Обработанные отделом СА заявки"><a href="#tab1" data-toggle="tab">Обработанные отделом СА заявки</a></li>
	<li title="Ожидается получение СА заявок"><a href="#tab2" data-toggle="tab">Ожидается получение СА заявок</a></li>
</ul>

<div class="tab-content" style="margin-bottom:60px;">

	<div class="tab-pane active" id="tab1">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Пользователь</th>
				<th>Информационный ресурс</th>
				<th>Обработано ОСА</th>
				<th>Действия</th>
			</tr>
			<?=$last_approved;?>
		</table>
	</div>

	<div class="tab-pane" id="tab2">
		<table class="table table-condensed table-bordered">
			<tr>
				<th>Пользователь</th>
				<th>Информационный ресурс</th>
				<th>Обработано ОСА</th>
				<th>Действия</th>
			</tr>
			<?=$awaiting;?>
		</table>
	</div>

</div>

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
