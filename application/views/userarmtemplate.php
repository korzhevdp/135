<table id="tbd<?=$id;?>" class="table-arm hide table table-bordered table-condensed table-striped<?=(($active) ? '' : ' muted');?>">
	<tr>
		<td colspan=2>
			АК: <strong><?=$hostname;?></strong><small class="offset1">Дата сканирования <?=$date;?></small>
			<?=$pcmode;?>
		</td>
	</tr>
	<tr>
		<td>
			Инвентарный номер
		</td>
		<td>
			<input type="text" id="inv<?=$id;?>" value="<?=$inv_number;?>" style="margin-bottom:0px;line-height:14px;font-size:14px;height:14px;">
			<button type="button" class="btn btn-mini btn-warning invsaver" ref="<?=$id;?>">Сохранить</button>
		</td>
	</tr>
	<tr>
		<td class="span3">#</td>
		<td><?=$all_md5;?></td>
	</tr>
	<tr>
		<td class="span3">М. плата</td>
		<td><?=$mb;?></td>
	</tr>
	<tr>
		<td>Система</td>
		<td><?=$system;?></td>
	</tr>
	<tr>
		<td>BIOS</td><small></small>
		<td><?=$bios;?></td>
	</tr>
	<tr>
		<td>Процессор</td>
		<td><?=$processor;?></td>
	</tr>
	<tr>
		<td>Объём RAM</td>
		<td><?=$ram;?> МБ.</td>
	</tr>
	<tr>
		<td>Сетевая карта</td>
		<td><?=$nic;?></td>
	</tr>
	<tr>
		<td>Жёсткий диск</td>
		<td><?=$hdd;?></td>
	</tr>
	<tr>
		<td>Оптический привод</td>
		<td><?=$cdrom;?></td>
	</tr>
	<tr>
		<td>Видеокарта</td>
		<td><?=$video;?></td>
	</tr>
	<tr>
		<td>Дата сканирования</td>
		<td><?=$date;?></td>
	</tr>
</table>
<form method="post" id="invform<?=$id;?>" style="display:none" action="/admin/blockpc">
	<input type="hidden" name="invnum" value="<?=$id;?>">
	<input type="hidden" name="user" value="<?=$user_id;?>">
</form>