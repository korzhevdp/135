<tr class="<?=$mainclass;?>" title="<?=$maintitle;?>">
	<td>
		<?=$productname;?><br><?=$productkey;?><br>Номер наклейки: <b><?=$label;?></b> <span class="hide">/ # <?=$license;?></span>
		<div class="pull-right"><?=$properties;?></div>
	</td>
	<td style="text-align:center;vertical-align:middle;"><?=$scandate;?></td>
	<td style="vertical-align:middle;">
		<div class="btn-group">
			<?=$link;?>
			<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li<?=$bindto;?>><a href="#" class="button-bide" title="Заполнить обнаруженную лицензию данными из назначенной вручную" ref="<?=$license;?>">Связать с...</a></li>
				<li<?=$poolswitch;?>><a href="#" class="button-take" title="Задействовать лицензию из пула по ключу" refid="<?=$license;?>" ref="<?=$pkshort;?>">Взять из пула...</a></li>
			</ul>
		</div>
	</td>
</tr>