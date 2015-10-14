<tr <?=$ismaster;?> id="i<?=$itid;?>">
	<td>
		<h3><?=$name;?></h3>
		<form method="post" action="/licenses/saveset" class="form-horizontal">
			<div class="control-group">
				<label class="control-label" style="width:200px;margin-right:5px;">Тип ключа</label>
				<div class="controls">
					<select name="keytype" style="width:300px;">
						<option value="VLK" <?=($type == "VLK") ? 'selected="selected"' : "";?>>VLK</option>
						<option value="MAK" <?=($type == "MAK") ? 'selected="selected"' : "";?>>MAK</option>
						<option value="KMS" <?=($type == "KMS") ? 'selected="selected"' : "";?>>KMS</option>
						<option value="OEM" <?=($type == "OEM") ? 'selected="selected"' : "";?>>OEM</option>
						<option value="OEI" <?=($type == "OEI") ? 'selected="selected"' : "";?>>OEI</option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" style="width:200px;margin-right:5px;">Ключ продукта</label>
				<div class="controls">
					<input type="text" style="width:300px;" name="keyvalue" value="<?=$value;?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" style="width:200px;margin-right:5px;">Количество активаций МАК</label>
				<div class="controls">
					<input type="text" style="width:300px;" name="maknum" value="<?=$qty;?>">
				</div>
			</div>
			<div class="pull-right btn-group">
				<button type="submit" class="btn btn-primary" title="Сохранить набор">Сохранить</button>
				<a href="/licenses/removeitem/<?=$itid;?>/<?=$lid;?>" class="btn btn-warning" title="Удалить запись из списков отображения (сохраняя в базе) "><i class="icon-trash"></i></a>
				<a href="/licenses/makemaster/<?=$itid;?>/<?=$lid;?>" class="btn btn-warning" title="Назначить основным программным продуктом в наборе">Сделать основным</a>
			</div>
			<input type="hidden" name="item_id" value="<?=$itid;?>">
			<input type="hidden" name="lid" value="<?=$lid;?>">
		</form>
	</td>

</tr>