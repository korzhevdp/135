<tr class="<?=$mainclass;?>" title="<?=$maintitle;?>">
	<td>
		<?=$productname;?><br><?=$productkey;?><br>����� ��������: <b><?=$label;?></b> <span class="hide">/ # <?=$license;?></span>
		<div class="pull-right"><?=$properties;?></div>
	</td>
	<td style="text-align:center;vertical-align:middle;"><?=$scandate;?></td>
	<td style="vertical-align:middle;">
		<div class="btn-group">
			<?=$link;?>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li<?=$bindto;?>><a href="#" class="button-bide" title="��������� ������������ �������� ������� �� ����������� �������" ref="<?=$license;?>">������� �...</a></li>
				<li<?=$poolswitch;?>><a href="#" class="button-take" title="������������� �������� �� ���� �� �����" refid="<?=$license;?>" ref="<?=$pkshort;?>">����� �� ����...</a></li>
			</ul>
		</div>
	</td>
</tr>