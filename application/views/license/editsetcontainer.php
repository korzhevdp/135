<div class="well well-small">
	<input type="text" id="po_num_<?=$id?>" class="span1 pull-left" value="<?=$max;?>"> <!-- ���������� ��������� �� �������� --> ����� �<?=$set_id;?>
	<table class="table table-bordered table-condensed table-striped table-hover">
	<tr>
		<th>��������</th>
	</tr>
		<?=(isset($items)) ? $items : "";?>
	</table>
</div>