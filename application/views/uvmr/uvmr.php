<div class="tabbable" style="margin-bottom:60px;"> <!-- Only required for left/right tabs -->
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab1" data-toggle="tab">������������ ���� ��������</a></li>
		<li><a href="#tab2" data-toggle="tab">��������� ���������������� ��</a></li>
		<li><a href="#tab3" data-toggle="tab">�������� ���� �������</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab1">
			<h4>�����: <?=sizeof($imdsp_table['ie']);?></h4>
			<table class="table table-condensed table-hover table-striped table-bordered">
				<tr>
					<th class="span1">#</th>
					<th class="span3">���</th>
					<th class="span3">�������������</th>
					<th class="span1">��������</th>
					<th class="span1">E-mail</th>
					<th class="span3">������� ���</th>
				</tr>
				<?=implode($imdsp_table['ie'],"");?>
			</table>
		</div>
		<div class="tab-pane" id="tab2">
			<h4>�����: <?=sizeof($imdsp_table['dsp']);?></h4>
			<table class="table table-condensed table-hover table-striped table-bordered">
				<tr>
					<th class="span1">#</th>
					<th class="span3">���</th>
					<th class="span3">�������������</th>
					<th class="span1">��������</th>
					<th class="span1">E-mail</th>
					<th class="span3">������� ���</th>
				</tr>
				<?=implode($imdsp_table['dsp'],"");?>

			</table>
		</div>
		<div class="tab-pane" id="tab3">
			<h4>�����: <?=sizeof($imdsp_table['coll']);?></h4>
			<table class="table table-condensed table-hover table-striped table-bordered">
				<tr>
					<th class="span1">#</th>
					<th class="span3">���</th>
					<th class="span3">�������������</th>
					<th class="span1">��������</th>
					<th class="span1">E-mail</th>
					<th class="span3">������� ���</th>
				</tr>
				<?=implode($imdsp_table['coll'],"");?>

			</table>
		</div>
	</div>
</div>