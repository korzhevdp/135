<h4><?=$ref;?>
	<div class="btn-group pull-right" style="margin-bottom:10px;">
		<a class="btn btn-large" href="#" title="" ref="<?=$pcref;?>"><?=$ref;?></a>
		<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li><a href="#" class="button-order" ref="<?=$ref;?>" title="Назначить произвольную лицензию из пула вручную">Назначить лицензию</a></li>
			<li><a href="#" class="button-convert" ref="<?=$ref;?>" title="Добавить в список лицензий ПО из списка установленного на ПК">Перевести в лицензию</a></li>
			<li><a href="#" title="Возврат всех лицензий в пул и исключение из АРМ">Списание</a></li>
		</ul>
	</div>
</h4>
<?=$info;?>
<table class="table table-condensed table-bordered table-hover table-licenses">
	<tr>
	<th class="span8">Детали лицензий</th>
	<th class="span1">Дата</th>
	<th class="span3">Действия</th>
</tr>
<?=$data;?>
</table>