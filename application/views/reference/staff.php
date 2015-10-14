<h2>Справочники.&nbsp;&nbsp;<small>Должности</small></h2><hr>
<form method="post" action="#" class="form-horizontal">
	<div class="control-group">
		<label for="staff" class="control-label span2">Должность</label>
		<div class="controls">
			<select name="staffSelector" id="staffSelector" class="span10">
				<?=$staff_list?>
			</select>
			<button type="submit" class="btn btn-info btn-mini" name="showStaff" value=1>Показать</button>
		</div>
	</div>

	<div class="control-group" style="margin-bottom:20px;">
		<label for="staff" class="control-label span2">Название</label>
		<div class="controls">
			<input type="text" name="staff" id="staff" class="span12" value="<?=$staff?>">
		</div>
	</div>
	<input type="hidden" name="staffToSave" value="<?=$id?>">
	<button type="button" value=1 class="btn offset6 span3" name="newStaff" id="newStaff">Создать новую</button>
	<button type="button" value=1 class="btn btn-primary span2" name="saveStaff" id="saveStaff">Сохранить</button>

</form>