<h2>������������ ������� �� ������</h2>

<table class="table table-condensed table-bordered">
	<tr>
		<th>������������</th>
		<th>�������������� ������</th>
		<th>���������� ���</th>
		<th>��������</th>
	</tr>
	<?=$last_approved;?>
</table>

<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">

	$('#addMessage').modal({show: 0});
	$("#mesModal").click(function(){
		$('#addMessage').modal('show');
	});
	$("#ModalOk").click(function(){
		$("#form2").submit();
	});

	 $(function() {
		$( "#enddate" ).datepicker();
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
			yearSuffix: ''
		};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
	});
</script> 
