$('.modalRes').modal({
	show: 0
});

$('.button-take').click(function(){
	var id = $(this).attr('ref');
	var refid = $(this).attr('refid');
	$.ajax({
		url: "/licenses/get_related_licenses/" + id,
		dataType: "html",
		cache: false,
		success: function(data){
			$("#resCollection").html(data);
			$('#akl').val(refid);
			$(".itemsel").click(function(){
				$("#itemid").val($(this).attr('ref'));
				$("#layerModalOk").removeAttr('disabled');
			});
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
	$("#modalRes").modal('show');
});

$("#layerModalOk2").click(function(){ // назначение лицензии
	//alert([ $("#itemid2").val(), $("#akl2").val(), $("#userid2").val()].join("\n"));
	//$("#form2").submit();
	//return false;
});

$('.button-order').click(function(){
	var id = $(this).attr('ref');
	$.ajax({
		url: "/licenses/get_all_licenses/" + id,
		dataType: "html",
		cache: false,
		success: function(data){
			$("#resCollection3").html(data);
			$('#akl3').val(id);
			$(".itemsel").click(function(){
				$("#itemid3").val($(this).attr('ref'));
				$("#layerModalOk3").removeAttr('disabled');
			});
			//$("#ds32").val();
			$("#ds32").keyup(function(){
				$(".searchname").each(function(){
					if($(this).text().toLowerCase().indexOf($("#ds32").val().toLowerCase()) > (-1)){
						$("#row" + $(this).attr("ref")).removeClass('hide');
					}else{
						$("#row" + $(this).attr("ref")).addClass('hide');
					}
				});
			});
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
	$("#modalRes3").modal('show');
});

$('.button-convert').click(function(){
	var id = $(this).attr('ref');
	$("#convName").val($(this).attr('ref'));
	$.ajax({
		url: "/licenses/getpolist",
		type: "POST",
		data: { host: id },
		dataType: "html",
		success: function(data){
			$("#instList").html(data);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
	$("#convert2Lic").modal('show');
});

$("#doConv").unbind().click(function(){
	var send = [],
	name = $("#convName").val();
	$(".poCheck:checked").each(function(){
		send.push($(this).attr('refn'));
	});
	if(confirm("Добавить в лицензионное ПО на компьютере " + name + " " + send.length + " позиции?\n\n" + send.join(";\n") + ".")){
		$.ajax({
			url: "/licenses/convertsofttolicense",
			type: "POST",
			data: { 
				po: send,
				name: name
			},
			dataType: "html",
			success: function(data){
				//window.location.reload();
			},
			error: function(data,stat,err){
				$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	}
});



$('#button-addlicr').click(function(){
	$("#LicLabel").modal('show');
});

$('#button-addPOtoset').click(function(){
	var setid = $(this).attr('set'),
		qty = $("#po_num_" + setid).val();
	$.ajax({
		url: "/licenses/get_typelist/",
		dataType: "html",
		cache: false,
		success: function(data){
			$("#addsoft").html(data);
			$("#toSetSubmit").click(function(){
				types = [];
				$(".typelist:checked").each(function(){
					types.push($(this).attr('ref'));
				});
			});
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
	$("#SetLabel").modal('show');
});



$("#LicrModalSubmit").click(function(){
	$("#form3").submit();
});

$('#button-addresl').click(function(){
	//alert(1)
	$("#ReslLabel").modal('show');
});

$("#reslModalSubmit").click(function(){
	$("#form2").submit();
});

$('.button-bide').click(function(){
	var id = $(this).attr('ref');
	$.ajax({
		url: "/licenses/get_bide/" + id,
		dataType: "html",
		cache: false,
		success: function(data){
			$("#resCollection2").html(data);
			$('#akl2').val(id);
			$(".itemsel").click(function(){
				$("#itemid2").val($(this).attr('ref'));
				$("#layerModalOk2").removeAttr('disabled');
			});
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
	$("#modalRes2").modal('show');
});

$(".button-reject").click(function(){
	$("#licenseform").attr('action','/licenses/make_reject/' + $(this).attr('ref')).submit();
	return false;
});

$(".button-recall").click(function(){
	$("#licenseform").attr('action','/licenses/make_recall/' + $(this).attr('ref')).submit();
	return false;
});

$("#inactiveToggler").click(function(){
	if($(this).prop("checked")){
		//alert(1)
		$(".table-licenses tr.muted").removeClass("hide");
	}else{
		$(".table-licenses tr.muted").addClass("hide");
	}
});

