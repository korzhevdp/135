<table class="table table-bordered table-condensed table-striped <?=($fired) ? 'muted' : '';?>">
<tr class="header">
		<td class="span1">#<?=$id;?></td>
		<td class="span5">
			<i class="icon-user" title="����"></i>&nbsp;<a href="/admin/users/<?=$id;?>" target="_blank"><?=$fio;?></a>
		</td>
		<td class="span3">
			<a href="/uvmr/passport/<?=$id;?>" target="_blank">�����������</a>
		</td>
		<td class="span1">
			<?=($inet) ? '<i class=" icon-globe" title="������������ ���� ��������"></i>' : "" ?>
			&nbsp;
		</td>
		<td class="span2"></td>
	</tr>
	<tr class=text>
		<td colspan=2>
			<span class="tooltip-bottom" title="�������������"><i class="icon-home"></i>&nbsp;<?=$dn;?></span><br>
			<span class="tooltip-bottom" title="���������"><i class="icon-briefcase"></i>&nbsp;<?=$staff;?></span><br>
			<span class="tooltip-bottom" title="�����"><i class="icon-map-marker"></i>&nbsp;<?=$address;?></span><br>
			<span class="tooltip-bottom" title="�������"><i class="icon-bell"></i>&nbsp;<?=$phone;?></span><br>
			<span class="tooltip-bottom" title="������������� ����������"><i class="icon-wrench"></i>&nbsp;<?=$serviceman;?></span><br>
			<span class="tooltip-bottom" title="�������"><i class="icon-edit"></i>&nbsp;<?=$memo;?></span>
		</td>
		<td colspan=3>
			<i class="icon-envelope"></i>&nbsp;<?=$emails;?><br>
			<i class="icon-hdd"></i>&nbsp;��� ����������:<?=$login;?><br>
			<i class="icon-user"></i>&nbsp;����� ������: <?=$login;?><br>
			<i class="icon-magnet"></i>&nbsp;����� ip:
		</td>
	</tr>
</table>