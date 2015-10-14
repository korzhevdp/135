<form method=post action="/reference/depts" class="form-horizontal">

	<div class="control-group">
		<label for="depSelector" class="control-label span2">�������������</label>
		<div class="controls">
			<select name="depSelector" id="depSelector" class="span9">
				<?=$dept_list;?>
			</select>
			<button type="submit" class="btn btn-info btn-mini" name="showDept" value=1>��������</button>
		</div>
	</div>

	<hr>

	<h3>������������� �����&nbsp;&nbsp;&nbsp;<small><?=$dn;?></small></h3>
	<div class="control-group">
		<label for="dep_qfn" class="control-label span2">����������� ������������</label>
		<div class="controls">
				<textarea name="dep_qfn" id="dep_qfn" rows="3" cols="4" class="span12" style="margin-bottom:10px;"><?=$dn?></textarea>
		</div>
	</div>
	
	<div class="control-group">
		<label for="dep_qfn" class="control-label span2">������������ (��� ������)</label>
		<div class="controls">
				<textarea name="dep_fn" id="dep_fn" rows="3" cols="4" class="span12" style="margin-bottom:10px;"><?=$dn_blank;?></textarea>
		</div>
	</div>

	<div class="control-group">
		<label for="dep_dn" class="control-label span2">������������</label>
		<div class="controls">
			<select name="dep_dn" id="dep_dn" class="span12">
			<?=$chief;?>
		</select>
		</div>
	</div>

	<div class="control-group">
		<label for="dep_zak" class="control-label span2">�����</label>
		<div class="controls">
			<input type="text" name="zakaz" id="dep_zak" class="span12" value="<?=$zakaz;?>">
		</div>
	</div>
	
	<div class="control-group">
		<label for="dep_sn" class="control-label span2">������������</label>
		<div class="controls">
			<input type="text" name="shortname" id="dep_sn" class="span12" value="<?=$alias;?>">
		</div>
	</div>

	<div class="control-group">
		<label for="dep_act" class="control-label span2">������������</label>
		<div class="controls">
			<?=$actual;?>
		</div>
	</div>
	
	<div class="control-group">
	<label for="dep_cur" class="control-label span2">�������</label>
		<div class="controls">
			<select name="curator" id="curator" class="span12">
				<?=$curator;?>
			</select>
		</div>
	</div>
	
	<div class="control-group">
	<label for="dep_parent" class="control-label span2">����������</label>
		<div class="controls">
			<select name="dep_parent" id="dep_parent" class="span12">
				<?=$parent;?>
			</select>
		</div>
	</div>
	
	<div class="control-group">
		<label for="dep_req" class="control-label span2">���������</label>
		<div class="controls">
			<textarea name="dep_req" id="dep_req" rows="3" cols="4" class="span12" style="margin-bottom:10px;"><?=$cred;?></textarea>
		</div>
	</div>

	<input type="hidden" name="depToSave" value="<?=$id;?>">

	<button type="submit" value="1" name="newDept" class="btn offset8">������� �����</button>
	<button type="submit" value="1" name="saveDept" class="btn btn-primary">���������</button>&nbsp;&nbsp;&nbsp;

</form>