<h4>Инвентарный номер: <?=$inv;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Серийный номер: <?=$serial;?></h4>
<input type="hidden" name="invNum" form="invUnit" value="<?=$inv;?>">
Дата приобретения: <input type="text" form="invUnit" name="datestart" class="short withCal" value="<?=$purchase;?>" style="margin-right:30px;">
Окончание: <input type="text" form="invUnit" class="short withCal" name="dateend" value="<?=$guarantee_end;?>"><br>
Поставщик: <input type="text" name="supplier" value="<?=$supplier;?>" style="margin-left:52px;"><br>
Получатель: <select form="invUnit" name="receiver" class="long" style="margin-left:49px;height:28px;">
	<option value="0">Выберите получателя</option>
	<?=$act['users'];?>
</select>&nbsp;&nbsp;&nbsp;<strong class="muted">должен быть <?=$fio;?></strong><br>
Местонахождение: <input type="text" value="<?=$room;?>" name="room" class="short" style="margin-left:4px;margin-right:20px">
<!-- <input type="checkbox" name="syncWithReceiver" checked="checked" style="margin-top:-4px;">&nbsp;&nbsp;&nbsp;Взять из данных о получателе -->
<br>
<button type="submit" form="invUnit" class="btn btn-primary btn-small" style="margin-left:614px;">Сохранить</button>