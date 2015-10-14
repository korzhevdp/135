<h2>Пул лицензий&nbsp;<small>статистика использования</small></h2><hr>
<a href="/licenses/addnew" class="btn btn-warning pull-right" style="margin-bottom:20px;">Добавить лицензию</a>
<table class="table table-condensed table-bordered table-hover" style="margin-bottom:80px;">
<tr>
	<th>#</th>
	<th>Лицензиар</th>
	<th>Дата приобретения</th>
	<th>Информация</th>
	<th>Примечания</th>
</tr>
<?=$general;?>
</table>

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