<table id="tbd<?=$id;?>" class="table-arm hide table table-bordered table-condensed table-striped<?=(($active) ? '' : ' muted');?>">
	<tr>
		<td colspan=2>
			��: <strong><?=$hostname;?></strong><small class="offset1">���� ������������ <?=$date;?></small>
			<?=$pcmode;?>
		</td>
	</tr>
	<tr>
		<td>
			����������� �����
		</td>
		<td>
			<input type="text" id="inv<?=$id;?>" value="<?=$inv_number;?>" style="margin-bottom:0px;line-height:14px;font-size:14px;height:14px;">
			<button type="button" class="btn btn-mini btn-warning invsaver" ref="<?=$id;?>">���������</button>
		</td>
	</tr>
	<tr>
		<td class="span3">#</td>
		<td><?=$all_md5;?></td>
	</tr>
	<tr>
		<td class="span3">�. �����</td>
		<td><?=$mb;?></td>
	</tr>
	<tr>
		<td>�������</td>
		<td><?=$system;?></td>
	</tr>
	<tr>
		<td>BIOS</td><small></small>
		<td><?=$bios;?></td>
	</tr>
	<tr>
		<td>���������</td>
		<td><?=$processor;?></td>
	</tr>
	<tr>
		<td>����� RAM</td>
		<td><?=$ram;?> ��.</td>
	</tr>
	<tr>
		<td>������� �����</td>
		<td><?=$nic;?></td>
	</tr>
	<tr>
		<td>Ƹ����� ����</td>
		<td><?=$hdd;?></td>
	</tr>
	<tr>
		<td>���������� ������</td>
		<td><?=$cdrom;?></td>
	</tr>
	<tr>
		<td>����������</td>
		<td><?=$video;?></td>
	</tr>
	<tr>
		<td>���� ������������</td>
		<td><?=$date;?></td>
	</tr>
</table>
<form method="post" id="invform<?=$id;?>" style="display:none" action="/admin/blockpc">
	<input type="hidden" name="invnum" value="<?=$id;?>">
	<input type="hidden" name="user" value="<?=$user_id;?>">
</form>