<style type="text/css">
	.pre-label{
		display:table-cell;
		width:140px !important;
		margin-bottom: 2px;
	}
	.post-label{
		display:table-cell;
		text-align:left !important;
		width:170px !important;
	}
	input[type=text],
	textarea {
		width:540px;
	}
	input.short{
		width:200px;
	}
	.shortest{
		width:30px !important;
	}
	.long{
		width:554px;
		height:26px;
		padding-left: 5px;
	}
	select.short{
		width:305px;
	}
	select.vshort{
		width:99px;
	}
	select{
		height:30px;
	}

</style>
<h3>������������ � ���</h3>

		<div class="input-prepend control-group">
			<span class="add-on pre-label">������</span>
			<input class="long" name="invfilter" form="invData" id="invFilter" value="<?=$invfilter?>">
		</div>
		<div class="input-prepend control-group">
			<span class="add-on pre-label">������ ���������</span>
			<input class="long" name="invfilter2" form="invData" id="invFilter2" value="<?=$invfilter2?>">
		</div>
		<div class="input-prepend control-group">
			<span class="add-on pre-label">����������� �����</span>
			<select name="invnum" id="invunits" class="long" form="invData" style="height:31px;">
			</select>
		</div>
		

		<button type="submit" class="btn btn-primary btn-small" form="invData" style="margin-left:614px;margin-top:15px;">��������</button>

		<hr>
		<?=$info;?>
		<form method="post" action="/arm/inv_unit_save" id="invUnit"></form>
		<hr>
		<?=$contents?>
		<hr>
		<?=$additional?>

<datalist id="typelist">
	<?=$typelist?>
</datalist>

<datalist id="invlist">
	<?//=$invlist?>
</datalist>

<datalist id="namelist">
	<?=$namelist?>
</datalist>

<form method="post" action="/arm/warehouse" id="invData">
	
</form>
<div id="ann1" style="display:none;position:absolute;height:45px;width:250px; font-size:20px;top:40px;right:50px;border:1px solid green; color:#33cc33; background-color:#DDffDD; padding-top:12px;">
	<center>��������� �������</center>
</div>
<div id="ann2" style="display:none;position:absolute;height:45px;width:250px; font-size:20px;top:40px;right:50px;border:1px solid red; color:#CC3333; background-color:#FFDDDD; padding-top:12px;">
	<center>���������� �� �������</center>
</div>
<script type="text/javascript">
<!--
	cur_inv = '<?=$cur_inv?>';
	
	$(function() {
		$(".withCal").datepicker();
	});

/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
	$(function($){
		$.datepicker.regional['ru'] = {
			closeText: '�������',
			prevText: '&#x3c;����',
			nextText: '����&#x3e;',
			currentText: '�������',
			monthNames: ['������','�������','����','������','���','����','����','������','��������','�������','������','�������'],
			monthNamesShort: ['���','���','���','���','���','���','���','���','���','���','���','���'],
			dayNames: ['�����������','�����������','�������','�����','�������','�������','�������'],
			dayNamesShort: ['���','���','���','���','���','���','���'],
			dayNamesMin: ['��','��','��','��','��','��','��'],
			weekHeader: '��',
			dateFormat: 'dd.mm.yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: '',
			changeYear: true
		};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
	});

	$(".devSaver").click(function(){
		dev = $(this).attr("dev");

		$.ajax({
			url: "/arm/dev_save",
			data: {
				devtype : $("#devtype" + dev).val(),
				devname : $("#devname" + dev).val(),
				qty     : $("#qty" + dev).val(),
				serial  : $("#serial" + dev).val(),
				dev     : dev
			},
			type: "POST",
			success: function(data){
				$("#ann1").fadeIn().delay(5000).fadeOut();
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter").keyup(function(){
		$("#invFilter2").val("");
		text = $(this).val();
		if(text.length < 4){
			return false
		}
		$.ajax({
			url: "/arm/get_inv_units",
			data: {
				text : text,
			},
			type: "POST",
			success: function(data){
				$("#invunits").empty().append(data);
				$("#invunits option[value=" + cur_inv + "]").attr("selected", "selected");
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter2").keyup(function(){
		$("#invFilter").val("");
		text = $(this).val();
		if(text.length < 4){
			return false
		}
		$.ajax({
			url: "/arm/get_inv_units",
			data: {
				text : text,
			},
			type: "POST",
			success: function(data){
				$("#invunits").empty().append(data);
				$("#invunits option[value=" + cur_inv + "]").attr("selected", "selected");
			},
			error: function(data, stat, err){
				$("#ann2").fadeIn().delay(5000).fadeOut();
				console.log([data, stat, err]);
			}
		});
	});

	$("#invFilter").keyup();
//-->
</script>