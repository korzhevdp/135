<tr style="cursor:pointer" class="relShow" ref="<?=$key;?>">
	<td><?=$name;?></td>
	<td><?=$diff;?><span class="muted" title="С учётом даунгрейда"> / <?=$totalsum;?></span></td>
	<td><?=$usage_qty;?></td>
</tr>
<tr class="relrow hide" id="relrow<?=$key;?>">
	<td colspan=3 id="relation<?=$key;?>"></td>
</tr>