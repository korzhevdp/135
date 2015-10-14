<div class="well well-small">
	<input type="text" id="po_num_<?=$id?>" class="span1 pull-left" value="<?=$max;?>"> <!-- Количество установок по лицензии --> Набор №<?=$set_id;?>
	<table class="table table-bordered table-condensed table-striped table-hover">
	<tr>
		<th>Описание</th>
	</tr>
		<?=(isset($items)) ? $items : "";?>
	</table>
</div>