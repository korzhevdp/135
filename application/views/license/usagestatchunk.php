<table class="table table-bordered table-condensed table-striped table-hover">
	<tr>
		<td>Лицензиар</td>
		<td>Номер лицензии</td>
		<td>Поставщик</td>
		<td>Количество лицензий</td>
		<td>Остаток</td>
	</tr>
	<?=$tableDirect;?>
	<tr><th colspan=3></th><th colspan=2>Всего прямых:&nbsp;&nbsp;<?=$TDir;?></th></tr>
	<?=$tableDown;?>
	<tr><th colspan=3></th><th colspan=2>Всего даунгрейд:&nbsp;&nbsp;<?=$TDown;?></th></tr>
</table>