<h3>Лицензии на персональных компьютерах&nbsp;&nbsp;&nbsp;<small></small></h3>
<form method="post" class="form-horizontal" action="">
	<div class="control-group">
		<label class="control-label span2">Подразделение</label>
		<div class="controls">
			<select name="dep_id" class="span10">
				<?=$depts;?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label span2">Пользователи</label>
		<div class="controls">
			<select name="userid" class="span10">
				<?=$users;?>
			</select>
			<button type="submit" class="btn btn-mini btn-info" style="margin-left:10px;">Показать</button>
		</div>
	</div>
</form>
<label class="checkbox" style="cursor:pointer;"><input type="checkbox" id="inactiveToggler">&nbsp;&nbsp;&nbsp;Показывать неактивные лицензии</label>

<?=$licenselist;?>

<!-- Плашка взятия лицензий из пула по признаку совпадения ключа-->
<div id="modalRes" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel1">Взять из пула лицензию <small>из следующих возможных</small></h3>
	</div>
	<div class="modal-body">
		<div id="resCollection" class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader" style="margin-left:45%">
		</div>
	</div>
	<div class="modal-footer">
		<form method="post" id="getform" action="/licenses/takeitem">
			<input type="hidden" name="itemid" id="itemid">
			<input type="hidden" name="akl" id="akl">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk" title="Взять лицензию из пула лицензий и передать этому пользователю" disabled>Готово</button>
		</form>
	</div>
</div>

<!-- Плашка связывания -->
<div id="modalRes2" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">Заполнить данными <small> из назначенных вручную лицензий</small></h3>
	</div>
	<div class="modal-body">
		<div id="resCollection2" class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader" style="margin-left:45%">
		</div>
	</div>
	<div class="modal-footer">
		<form method="post" id="form2" action="/licenses/bideitem">
			<input type="hidden" name="itemid" id="itemid2">
			<input type="hidden" name="akl" id="akl2">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid2" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk2" title="Заполнить данные лицензии" disabled>Готово</button>
		</form>
	</div>
</div>

<!-- Плашка назначения произвольных лицензий -->
<div id="modalRes3" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Назначить произвольную лицензию <small>из пула</small></h3>
		Отобрать только: <input id="ds32" size="44" type="text">
	</div>
	<div class="modal-body">
			<table class="table table-condensed table-bordered">
			<tbody><tr>
				<th></th>
				<th class="span9">Лицензия</th>
				<th class="span2">Остаток</th>
			</tr></tbody>
			<tbody id="resCollection3"></tbody>
		</table>
	</div>
	<div class="modal-footer">
		<form method="post" id="form3" action="/licenses/orderitem">
			<input type="hidden" name="itemid" id="itemid3">
			<input type="hidden" name="akl" id="akl3">
			<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
			<input type="hidden" name="userid" id="userid3" value="<?=$userid?>">
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
			<button class="btn btn-primary" aria-hidden="true" id="layerModalOk3" title="Назначить лицензию из пула" disabled>Готово</button>
		</form>
	</div>
</div>

<div id="convert2Lic" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="convert2Lic" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Перевести в лицензию <small>софт установленный на ПК</small></h3>
	</div>
	<form method="post" action="/licenses/convert">
		<div class="modal-body">
		<input type="hidden" id="convName">
		<table class="table table-condensed table-bordered table-hover">
		<tbody>
			<tr>
				<td>/</td>
				<td>Программное обеспечение</td>
			</tr>
		</tbody>

		<tbody id="instList">
		
		</tbody>
		</table>
		</div>
	</form>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
				<button class="btn btn-primary" aria-hidden="true" id="doConv" title="Перевести в лицензию">Перевести в лицензию</button>
		</div>

</div>

<form method="post" id="licenseform" action="dummyurl" style="margin-bottom:80px;">
	<input type="hidden" name="dep_id" id="dep_id" value="<?=$dep_id?>">
	<input type="hidden" name="userid" value="<?=$userid;?>">
</form>
<script type="text/javascript" src="/jscript/lsmc.js"></script>