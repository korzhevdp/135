<style type="text/css">
	#loctable{
		margin-bottom:80px;margin-left:0px;
	}
	#saveName{
		margin-left:20px;
	}

	#address{
		width:600px;
	}
	#location{
		width:615px;
	}
</style>
<h2>��������� �����&nbsp;&nbsp;&nbsp;<small>��������</small></h2><hr>

<form method="post" action="/reference/locations" class="form-horizontal">

	<div class="control-group" >
		<label for="address" class="control-label">������ ������</label>
		<div class="controls">
		<select name="location" id="location">
			<?=$locations;?>
		</select>
			&nbsp;&nbsp;&nbsp;
			<button type="submit" class="btn btn-info btn-mini" value="showLocation">��������</button>
		</div>
	</div>

	<div class="control-group" style="margin-bottom:20px;">
		<label for="address" class="control-label">������������</label>
		<div class="controls">
			<input type="text" name="fullname" id="address" value="<?=$address?>">&nbsp;&nbsp;&nbsp;
		</div>
		<button type="submit" class="btn btn-primary pull-right" name="saveName" id="saveName" value=1>���������</button>
		<button type="submit" class="btn pull-right" name="addNewName" id="addNewName"value=1>��������</button>

	</div>
	


	<hr>
	<h4>���������&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary btn-mini pull-right" name="addSubLocation" value=1>��������</button></h4>
	<table class="table table-bordered table-striped" id="loctable">
	<tr>
		<td>�����</td>
		<td>����</td>
		<td>��������</td>
	</tr>
	<?=$locations_table;?>
	</table>
	<input type="hidden" name="locationIDToSave" value="<?=$id;?>">
</form>