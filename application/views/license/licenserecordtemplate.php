<tr class="<?=$class;?>" title="<?=$annotation;?>">
	<td>
		<?=$product_name;?><br><?=$product_key;?><br>
		����� ��������: <b><?=$label;?></b>
		<span class="hide">/ # <?=$id;?></span>
		<div  class="pull-right"><?=$props;?></div>
	</td>
	<td style="text-align:center;vertical-align:middle;">
		<?=$scandate;?>
	</td>
	<td style="vertical-align:middle;">
		<div class="btn-group">
			<a class="btn btn-primary <?=$buttonClass;?>" style="width:66%" href="#" title="<?=$buttonTitle;?>" ref="<?=$id;?>">
				<?=$buttonText;?>
			</a>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li <?=$bindto;?>>
					<a href="#" class="button-bide" title="��������� ������������ �������� ������� �� ����������� �������" ref="<?=$id;?>">������� �...</a>
				</li>
				<li <?=$takefrompool;?>>
					<a href="#" class="button-take" title="������������� �������� �� ���� �� �����" refid="<?=$id;?>" ref="<?=$pkshort;?>">����� �� ����...</a>
				</li>
			</ul>
		</div>
	</td>
</tr>