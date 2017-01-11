<tr class="ALLItem" id="row<?=$id;?>">
	<td style="text-align:center;vertical-align:middle;">
		<input type="radio" id="item<?=$id;?>" name="itm" class="itemsel" ref="<?=$id;?>">
	</td>
	<td style="vertical-align:middle;">
		<label for="item<?=$id;?>" style="cursor:pointer;">
			<div class="searchname" ref="<?=$id;?>"><?=$name;?></div>
			<?=$licensiar;?>, ëèö. ¹ <?=$number;?> <b class="hide">Item: <?=$id;?></b><br>
			<div class="searchval" ref="<?=$id;?>"><?=$value;?></div>
		</label>
	</td>
	<td style="text-align:center;vertical-align:middle;">
		<label for="item<?=$id;?>" style="cursor:pointer;">
			<span class="btn-primary btn-small pull-right"><?=($master) ? "ÏĞßÌ" : "ÄÃĞÄ"?></span>
		</label>
	</td>
	<td style="text-align:center;vertical-align:middle;">
		<label for="item<?=$id;?>" style="cursor:pointer;"><?=$max;?></label>
	</td>
</tr>