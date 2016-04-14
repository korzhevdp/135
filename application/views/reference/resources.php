<style type="text/css">
	label input[type=checkbox]{
		margin-top: -4px;
		margin-right:20px;
		margin-left:220px;
	}
	#tab1 label input[type=radio]{
		margin-top: 2px;
		margin-right:15px;
		margin-left:30px;
	}
	#tab1 label{
		display:inline;
		text-align:left;
	}
	#resSSub{
		margin-top:15px;
	}
	span.pre-label{
		width:200px !important;
		text-align:left !important;
	}
	#tab1 span.pre-label{
		width:330px !important;
		text-align:left !important;
	}
	select,
	input[type=text]{
		width:650px;
	}
	textarea{
		width:650px;
		height:50px;
	}
	#withInactive{
		display:inline;
	}
	#withInactive input[type=checkbox]{
		margin-top: 2px;
		margin-right:5px;
		margin-left:10px;
	}
</style>

<form method="post" id="resS" action="/reference/resources">
	<div class="input-prepend input-append">
		<span class="add-on pre-label">Ресурс</span>
		<select form="resS" id="resource" name="resource">
			<?=$list;?>
		</select>
	</div>
	<button type="submit" form="resS" id="resSSub" class="btn btn-info">Показать</button>
</form>



<ul class="nav nav-tabs" style="margin-top:40px;">
	<li class="<?=$tab1;?>" title="Формуляр информационного ресурса"><a href="#tab1" data-toggle="tab">Формуляр</a></li>
	<li class="<?=$tab2;?>" title="Представление информационного ресурса в системе"><a href="#tab2" data-toggle="tab">Описание</a></li>
	<li class="<?=$tab3;?>" title="Администраторы безопасности"><a href="#tab3" data-toggle="tab">Администраторы безопасности</a></li>
</ul>


<div class="tab-content">
	<div class="tab-pane <?=$tab1;?>" id="tab1">
		<form method="post" id="resForm" action="/reference/save_form">

			<h4><?=$name;?>&nbsp;&nbsp;&nbsp;&nbsp;
				<small>
					Формуляр информационного ресурса&nbsp;&nbsp;&nbsp;
					<?=(($active)    ? "Активен" : "Неактивен" );?>
					<?=(($in_report) ? ""        : "Нереестровый" );?>
				</small>
			</h4>
			<div class="input-prepend">
				<span class="add-on pre-label">Наименование структурного подразделения</span>
				<input type="text" form="resForm" name="f_depname" list="dl_owner" id="f_depname" value='<?=$f_depname;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Полное наименование программного продукта</span>
				<input type="text" form="resForm" name="name" id="name" value='<?=$name;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Полное наименование базы данных</span>
				<input type="text" form="resForm" name="f_dbname" id="f_dbname" value='<?=$f_dbname;?>' placeholder="Полное наименование базы данных">
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Расширенное описание</span>
				<textarea rows="6" form="resForm" cols="6" name="f_application" id="f_application" placeholder="Область применения, назначения и функциональные характеристики"><?=$f_application;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Происхождение</span>
				<label><input type="radio" form="resForm" name="f_origin" value="1" <?=($f_origin == 2) ? "" : 'checked="checked"';?>>Создано</label>
				<label><input type="radio" form="resForm" name="f_origin" value="2" <?=($f_origin == 2) ? 'checked="checked"' : "";?>>Закуплено</label>
			</div>
			
			<div class="input-prepend">
				<span class="add-on pre-label">Разработчик</span>
				<input type="text" form="resForm" name="f_developer" id="f_developer" value='<?=$f_developer;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Поставщик</span>
				<input type="text" form="resForm" name="f_reseller" id="f_reseller" value='<?=$f_reseller;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Дата приобретения</span>
				<input class="withCal" type="text" form="resForm" name="f_date" id="f_date" value="<?=$f_date;?>" placeholder="Дата приобретения">
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Дата начала эксплуатации</span>
				<input class="withCal" type="text" form="resForm" name="f_startdate" id="f_startdate" value="<?=$f_startdate;?>" placeholder="Дата начала эксплуатации">
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Дата завершения эксплуатации</span>
				<input class="withCal" type="text" form="resForm" name="f_enddate" id="f_enddate" value="<?=$f_enddate;?>" disabled placeholder="Дата завершения (приостановки) эксплуатации">
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Язык программирования</span>
				<input type="text" list="dl_lang" form="resForm" name="f_lang" id="f_lang" value='<?=$f_lang;?>'>
				<datalist id="lang_list"><?=$lang_list;?></datalist>
			</div>
			
			<div class="input-prepend">
				<span class="add-on pre-label">Местонахождение дистрибутивов</span>
				<input type="text" form="resForm" name="f_stored_at" id="f_stored_at" value='<?=$f_stored_at;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Лицензии</span>
				<textarea rows="6" form="resForm" cols="6" name="f_licenses" id="f_licenses" placeholder="Количество лицензий, номера лицензий"><?=$f_licenses;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Cопровождение</span>
				<input type="text" form="resForm" list="dl_supp" name="f_supporter" id="f_supporter" placeholder="Специалистами какого структурного подразделения мэрии города сопровождается" title="Специалистами какого структурного подразделения мэрии города сопровождается" value='<?=$f_supporter;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Пользователи</span>
				<textarea rows="6" form="resForm" cols="6" name="f_users" id="f_users" placeholder="Локальная/сетевая версия, количество пользователей"><?=$f_users;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Место установки</span>
				<input type="text" form="resForm" name="f_location" id="f_location" placeholder="на сервере, на локальных компьютерах" title="на сервере, на локальных компьютерах" value='<?=$f_location;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Системные требования</span>
				<textarea rows="6" form="resForm" cols="6" name="f_pc_prereq" id="f_pc_prereq" placeholder="Минимальные требования к ПК"><?=$f_pc_prereq;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Нормативные акты</span>
				<textarea rows="6" form="resForm" cols="6" name="f_doc" id="f_doc" placeholder="Нормативные акты о вводе в эксплуатацию и пр."><?=$f_doc;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Ретроспектива</span>
				<textarea rows="6" form="resForm" cols="6" name="f_retro" id="f_retro" placeholder="Откуда ноги растут"><?=$f_retro;?></textarea>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Периодичность обновления</span>
				<input type="text" form="resForm" name="f_datacycle" id="f_datacycle" placeholder="Периодичность обновления информации" title="Периодичность обновления информации" value='<?=$f_datacycle;?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Статус информации</span>
				<select form="resForm" name="f_category" id="f_category">
					<?=$category;?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Собственник</span>
				<select form="resForm" name="f_owner" id="f_owner">
					<?=$owner;?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Объём базы данных, Мб</span>
				<input type="text" form="resForm" name="f_dbvol" id="f_dbvol" placeholder="Объём базы данных, ориентировочно" title="Объём базы данных, мегабайт" value='<?=$f_dbvol;?>'>
			</div>
			
			<input type="hidden" form="resForm" name="resID" value="<?=$resource;?>">

			<button type="submit" form="resForm" class="btn pull-right btn-primary" name="saveMode" value="save" style="margin-right:130px">Сохранить</button>
			<button type="submit" form="resForm" class="btn pull-right" name="saveMode" value="new" style="margin-right:20px">Создать новый</button>
		</form>
		<form method="post" id="resRestart" action="/reference/res_restart">
			<? if($active) { ?>
				<button id="resFinBtn"     type="submit" form="resRestart" name="resFinish"  value="<?=$resource;?>" class="btn btn-warning pull-left">Завершить эксплуатацию</button>
			<? } else { ?>
				<button id="resRestartBtn" type="submit" form="resRestart" name="resRestart" value="<?=$resource;?>" class="btn btn-warning pull-left">Возобновить эксплуатацию</button>
			<? } ?>
		</form>

		<!-- 5. Создано/Закуплено<br>
		6. Разработчик<br>
		7. Поставщик<br>
		8. Дата приобретения<br>
		9. Дата начала эксплуатации<br>
		10. Дата прекращения (приостановки) эксплуатации<br>
		11. Причина завершения (приостановки) эксплуатации<br>
		12. Язык программирования<br>
		13. Местонахождение дистрибутивов<br>
		14. Количество лицензий, номера лицензий<br>
		15. Специалистами какого структурного подразделения мэрии города сопровождается<br>
		16. Локальная/сетевая версия, количество пользователей<br>
		17. Место установки - на сервере, на локальных компьютерах<br>
		18. Минимальные требования к ПК<br>
		19. Нормативные акты<br>
		20. Пользователи<br>
		21. Ретроспектива<br>
		22. Периодичность обновления информации<br>
		23. Статус информации, содержащейся в БД<br>
		24. Собственник<br>
		25. Объем БД, Мб -->
	</div>

	<div class="tab-pane <?=$tab3;?>" id="tab2">
		<form method="post" id="resP" action="/reference/save_resource">
			<h3><?=$name?>.&nbsp;&nbsp;&nbsp;<small>Описание</small></h3>

			<div class="input-prepend">
				<span class="add-on pre-label">Краткое наименование</span>
				<input type="text" form="resP" name="shortname" id="sn" value='<?=$shortname?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Расположение</span>
				<input type="text" form="resP" name="location" id="location" value='<?=$location?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Как подключить</span>
				<input type="text" form="resP" name="action" id="loc" value='<?=$action?>'>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Статус</span>
				<select form="resP" name="status" id="status">
					<?=$status;?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Группа</span>
				<select form="resP" name="group" id="group">
					<?=$group;?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Прикрепление к АРМ</span>
				<select form="resP" name="arm" id="arm">
					<?=$arm?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Администратор</span>
				<select multiple form="resP" size=7 name="adm" id="adm">
					<?=$admins;?>
				</select>
			</div>

			<div class="input-prepend">
				<span class="add-on pre-label">Памятка пользователю</span>
				<textarea type="text" form="resP" name="f_usermemo" id="f_usermemo" placeholder="Напоминание для пользователя по процедуре оформления заявки выводимое в справочном листе" title="Напоминание для пользователя"><?=$f_dbvol;?></textarea>
			</div>

			<div class="input-prepend">
				<label>
					<span class="add-on pre-label">Реестровый</span>
					<input type="checkbox" name="in_report" id="in_report"<?=(($in_report) ? ' checked="checked"' : '');?> style="margin-top:6px;" value="1"></label>
			</div>
			<input type="hidden" form="resP" name="resID" value="<?=$resource;?>">

			<div style="margin-top:20px;margin-bottom:60px;">
				<button type="submit" form="resP" name="saveResource" class="btn pull-right btn-primary" style="margin-right:260px" value="save">Сохранить</button>
			</div>
		</form>
	</div>

	<div class="tab-pane <?=$tab2;?>" id="tab3">
		<form method="post" id="resA" action="/reference/save_admsec">
			<h3>Администраторы безопасности.&nbsp;&nbsp;&nbsp;<small>Ресурса "<?=$name;?>"</small></h3>
			<table class="table table-condensed table-striped table-bordered container">
				<tr>
					<td>Администратор</td>
					<td>Подразделение</td>
				</tr>
				<?=$table_admsec;?>
			</table>
			<div class="input-prepend">
				<span class="add-on pre-label">Администратор БИР</span>
				<select name="as_adm" form="resA" id="as_adm">
					<option value="0">- выберите - </option>
					<?=$admsec_users;?>
				</select>
			</div>
			<div class="input-prepend">
				<span class="add-on pre-label">Подразделение</span>
				<select name="as_dept" form="resA" id="as_dept">
					<?=$owner;?>
				</select>
			</div>
			<button type="submit" form="resA" name="resID" value="<?=$resource;?>" class="btn btn-primary">Добавить администратора безопасности</button>
		</form>
	</div>
</div>


<div id="resFinModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form method="post" id="resFin" action="/reference/res_finish">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">Завершение (приостановка) эксплуатации ресурса</h3>
		</div>
		<div class="modal-body">
			Причина завершения (приостановки) эксплуатации ресурса
			<textarea name="f_endreason" form="resFin" id="f_endreason" rows="6" cols="8" style="width:97%"></textarea>
			<input type="hidden" form="resFin" name="resID" value="<?=$resource;?>">
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
			<button type="submit" class="btn btn-primary" form="resFin" id="inetModalOk">Готово</button>
		</div>
	</form>
</div>

<datalist id="dl_owner">
	<?=$dl_owner;?>
</datalist>

<datalist id="dl_supp">
	<?=$supporter_list;?>
</datalist>

<datalist id="dl_lang">
	<?=$lang_list;?>
</datalist>

<script type="text/javascript">
<!--
	$('.modal').modal({ show: 0 });

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
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false
	};
	$(".withCal").datepicker($.datepicker.regional['ru']);
	//changeMonth and changeYear
	//$(".withCal").datepicker( "option", "showWeek", true );
	$(".withCal").datepicker( "option", "changeYear", true);
	
	$("#resFinBtn").click(function(){
		$('#resFinModal').modal('show');
	});
//-->
</script>
