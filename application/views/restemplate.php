<div id="ss<?=$id;?>" class="well well-small row-fluid span12" style="margin-left:0px;">
	
	<h4><?=$shortname;?>&nbsp;&nbsp;&nbsp;&nbsp;<?(($cat > 1) ? '<span class="btn-mini btn-danger">конфиденциальный</span>' : '');?>
		<small class="pull-right">ticket #<?=$id;?></small>
		<span class="btn btn-info btn-small infoData" ref="<?=$id;?>" title="Расширенная информация по заявке"><i class="icon-info-sign icon-white"></i></span>
	</h4>
	<div style="margin-bottom:10px;">
		<small><i class="icon-question-sign"></i>&nbsp;<?=$action;?>&nbsp;&nbsp;&nbsp;&nbsp; <i class="icon-hdd"></i>&nbsp;<?=$location;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-cog"></i>&nbsp;<?=$osa_date;?></small>
	</div>
	<?=$ipChunk;?>
	<?=$mnChunk;?>

	<div class="control-group row-fluid span8">
		<label class="control-label span3">№ заявки</label>
		<div class="controls">
			<input class="span12 input-small" ID="nm_<?=$id;?>" maxlength="20" type="text" value="<?=$docnum?>"<?=$editAllowed;?>>
		</div>
	</div>

	<div class="control-group row-fluid span8">
		<label class="control-label span3">Дата заявки</label>
		<div class="controls">
			<input class="span12 input-small wDate" id="date_<?=$id;?>" maxlength="20" type="text" value="<?=$docdate;?>"<?=$editAllowed;?>>
			
		</div>
		
	</div>

	<div class="btn-toolbar" style="margin-top:0px;">
		<div class="btn-group" style="margin-left:20px;"><?=$button1.$button2;?>
		</div>
	</div>
</div>