<tr <?=$ismaster;?> id="i<?=$itid;?>">
	<td>
		<div class="btn-group">
			<button class="btn btn-success btn-mini" title="'<?=$name;?>" style="width:180px;overflow:hidden;"><?=$name;?></button>
			<button class="btn btn-info btn-mini" style="width:40px;"><?=$type;?></button>
			<button class="btn dropdown-toggle btn-mini" data-toggle="dropdown">
			<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			<li><a href="/licenses/removeitem/<?=$itid;?>/<?=$lid;?>" title="Удалить запись из списков отображения (сохраняя в базе) ">Удалить из списков</a></li>
			<li><a href="/licenses/makemaster/<?=$itid;?>/<?=$lid;?>" title="Назначить основным программным продуктом в наборе">Сделать основным</a></li>
			</ul>
		</div>
	</td>
	<td><?=$value;?></td>
	<td><?=$max;?></td>
	<td><?=$cnt;?></td>
	<td><?=$remainder;?></td>
</tr>