<h4><?=$key;?> <?=$fired;?>
	<div class="btn-group pull-right" style="margin-bottom:10px;">
		<a class="btn btn-large" href="#" title="" ref="<?=$id;?>"><?=$key;?></a>
		<a class="btn btn-large dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li>
				<a href="#" class="button-order" ref="<?=$key;?>" title="��������� ������������ �������� �� ���� �������">��������� ��������</a>
			</li>
			<li>
				<a href="#" class="button-convert" ref="<?=$key;?>" title="�������� � ������ �������� �� �� ������ �������������� �� ��">��������� � ��������</a>
			</li>
			<li>
				<a href="#" title="������� ���� �������� � ��� � ���������� �� ���">��������</a>
			</li>
		</ul>
	</div>
</h4>

<?=$intinfo;?>

<table class="table table-condensed table-bordered table-hover table-licenses">
<tr>
	<th class="span8">������ ��������</th>
	<th class="span1">����</th>
	<th class="span3">��������</th>
</tr><?=$data;?>
</table>