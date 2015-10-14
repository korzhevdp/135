<style type="text/css">
	.relShow {
		cursor: pointer
	}

	.relShow :nth-child(2),
	.relShow :nth-child(3) {
		text-align:center;
		vertical-align:center;
	}

	#useData{
		width:640px;
	}

	#useData div.modal-header{
		cursor: move;
		background-color: #d6d6d6;
	}

	#usagetable{
		height:460px;
	}
</style>

<table class="table table condensed table-bordered">
	<tr>
		<th>Название ПО</th>
		<th>Лицензии VLK</th>
		<th>Используются</th>
	</tr>
	<?=$content;?>
</table>

<div class="modal hide" id="useData">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><i class="icon-remove"></i></button>
		<h4>ПО установлено у:</h4>
	</div>
	<div class="modal-body" id="usagetable"></div>
</div>

<script type="text/javascript">
<!--
	$(".modal").modal({show: 0});
	$(".relShow").click(function(){
		var id = $(this).attr("ref");
		$.ajax({
			url: "/licenses/related_lic_get",
			data: {id: id},
			type: 'POST',
			dataType: 'html',
			success: function(data){
				$(".relrow").addClass('hide');
				$("#relation" + id).empty().html(data);
				$("#relrow" + id).removeClass("hide");
				
				$(".useCheck").click(function(){
					var id = $(this).attr("ref");
					$.ajax({
						url: "/licenses/related_pk_get",
						data: { pk: id },
						type: 'POST',
						dataType: 'html',
						success: function(data){
							//alert(data);
							$("#usagetable").html(data);
							$("#useData").modal('show');
						},
						error: function(data,stat,err){
							//$("#consoleContent").html([data,stat,err].join("<br>"));
						}
					});
				});

			},
			error: function(data,stat,err){
				//$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	});


//-->
</script>