<div class="control-group row-fluid span8 input-prepend">
	<label class="control-label span3">IP</label>
	<div class="controls">
		<span class="add-on" style="width:30%;font-weight:bold;">192.168.</span>
		<input class="span8 input-small" id="ip_<?=$id;?>" maxlength="20" type="text" value="<?=((isset($pid6)) ? $pid6 : "")?>"<?=$editAllowed;?>>
	</div>
</div>