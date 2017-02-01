<tr class="<?=$class;?>" title="<?=$annotation;?>">
	<td>
		<?=$product_name;?><br><?=$product_key;?><br>
		Номер наклейки: <b><?=$label;?></b>
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
					<a href="#" class="button-bide" title="Заполнить обнаруженную лицензию данными из назначенной вручную" ref="<?=$id;?>">Связать с...</a>
				</li>
				<li <?=$takefrompool;?>>
					<a href="#" class="button-take" title="Задействовать лицензию из пула по ключу" refid="<?=$id;?>" ref="<?=$pkshort;?>">Взять из пула...</a>
				</li>
			</ul>
		</div>
	</td>
</tr>