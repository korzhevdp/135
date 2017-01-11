$('.modal').modal({
	show: 0
});


$(".makeEvent").click(function(){
	$(".eventItemID").val($(this).attr("prop"));
	$(".eventAnnotation").val("");
	$("#modalEvent").modal("show");
});

$(".makeBackEvent").click(function(){
	$(".eventItemID").val($(this).attr("prop"));
	$(".eventAnnotation").val("");
	$("#modalBackEvent").modal("show");
});

$(".mailEvent").click(function(){
	$(".eventAnnotation").val($(".eventAnnotation").val() + (($(".eventAnnotation").val().length) ? "\n" : "") + "Пароль почтового ящика: ")
})

$('.button-take').click(function(){
	var id = $(this).attr('ref'),
		refid = $(this).attr('refid');
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

$('.button-order').click(function() {
	var id = $(this).attr('ref');
	$.ajax({
		url      : "/licenses/get_all_licenses",
		dataType : "html",
		success  : function(data) {
			$("#resCollection3").html(data);
			$('#akl3').val(id);
			$(".itemsel").click(function(){
				$("#itemid3").val($(this).attr('ref'));
				$("#layerModalOk3").removeAttr('disabled');
			});
			$("#modalRes3").modal('show');
			$("#ds32").keyup(function(){
				var search = $("#ds32").val();
				$(".ALLItem").addClass('hide')
				$(".searchval").each(function() {
					findIn($(this).text(), search, $(this).attr("ref"));
				});
				$(".searchname").each(function() {
					findIn($(this).text(), search, $(this).attr("ref"));
				});
			});
		},
		error    : function(data,stat,err) {
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

function findIn( haystack, needle, ref ) {
	if ( haystack.toLowerCase().indexOf(needle.toLowerCase()) !== (-1) ) {
		$("#row" + ref).removeClass('hide');
	}
}

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
			error: function(data, stat, err){
			console.log([data, stat, err]);
			}
		});
	}
});



$('#button-addlicr').click(function(){
	$("#LicLabel").modal('show');
});

$("#addSoftwareType").click(function(){
	if(!$("#newTypeName").val().length){
		return false;
	}
	$.ajax({
		url      : "/licenses/addtype",
		type     : 'POST',
		data     : {
			typename : $("#newTypeName").val()
		},
		dataType : "text",
		success  : function(data) {
			getSoftwareList();
		},
		error    : function(data, stat, err){
			console.log([data, stat, err]);
		}
	});
	
});

function getSoftwareList(){
	$.ajax({
		url      : "/licenses/get_typelist",
		dataType : "html",
		success  : function(data) {
			$("#addsoft").append(data);
			$("#toSetSubmit").unbind().click(function() {
				types = [];
				$(".typelist:checked").each(function() {
					types.push($(this).attr('ref'));
				});
			});
			$("#filterSoftByName").keyup(function() {
				var string = $(this).val();
				$("#addsoft tr").each(function() {
					if ( $(this).attr("ref").toLowerCase().indexOf(string.toLowerCase()) === -1 ) {
						$(this).addClass("hide");
						return true;
					}
					$(this).removeClass("hide");
				})
			});
		},
		error    : function(data, stat, err){
			console.log([data, stat, err]);
		}
	});
}


$('#button-addPOtoset').click(function(){
	var setid = $(this).attr('set'),
		qty = $("#po_num_" + setid).val();
	getSoftwareList();
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

