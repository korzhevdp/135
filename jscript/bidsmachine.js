var state	   = 0,
	res		   = [],
	subs	   = [],
	confs	   = [],
	bmode	   = 'new',
	controller = 'bids',
	allowedRes = {
		/*
		идентификатор подразделения departments : [массив идентификаторов разрешённых ресурсов]
		*/
		'81' : [ 100, 101, 102 ]
	};

//########################################################
//# rev 3 JS code
//########################################################

function disableUnallowedRes() {
	var dept = ( udt === undefined || !udt || udt.dept === undefined) ? $("#dept").val() : udt.dept;
	if (allowedRes[dept] === undefined){
		$('.reslist').removeClass("toDisabled disabled").prop('disabled', false);
		$('.reslist').each(function (){
			$(this).addClass( ($(this).attr("conf") == "0") ? "btn-info" : "btn-warning" );
		});
		return false;
	}
	$('.reslist').addClass("toDisabled");
	$("#selectedList .reslist").click();
	for ( a in allowedRes[dept] ) {
		if (allowedRes[dept].hasOwnProperty(a)) {
			$('.reslist[id=r_' + allowedRes[dept][a] + ']').removeClass("toDisabled")
		}
	}
	$(".toDisabled").removeClass("btn-info btn-warning").addClass("disabled").prop('disabled', true).removeClass("toDisabled");
}

function getUserData() {
	uid = $("#userSelector").val();
	if ( uid != null ) {
		$.ajax({
			url: "/" + controller + "/getuserdata",
			type: "POST",
			data: {
				uid: uid
			},
			dataType: "script",
			success: function () {
				bldg = (udt.bldg == "0") ? udt.office : udt.bldg ; 
				$("#sname").val(udt.name_f);
				$("#name" ).val(udt.name_i);
				$("#fname").val(udt.name_o);
				$("#dept" ).val(udt.dept);
				$("#staff").val(udt.staff);
				$("#esiaMailAddr, #f_esiaMailAddr").val(udt.email);
				$("#office").val(bldg);
				if ( locs[parseInt(bldg)] !== undefined ) {
					$("#office2").empty().append(locs[parseInt(bldg)].join("\n")).val(udt.office);
				} else {
					$("#office2").empty();
				}
				$("#phone").val(udt.phone);
				$("#uid").val(uid);
				$("#login").val(udt.login);
				$(".traceable").parent().removeClass("warning error success");
				disableUnallowedRes();
			},
			error: function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
	}
	$("#userdata, #popID, #userOKButtons, #breadCrumbs").removeClass("hide");
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
}

function addToList(item) {
	var resID = parseInt($(item).attr('id').split('_')[1], 10);
	if (parseInt($(item).attr('subs')) > 0) {
		subs[resID] = [];
		$("#esiaMail").addClass("hide");
		if (resID === 286) {
			$("#esiaMail").removeClass("hide");
		}
		$.ajax({
			url      : "/" + controller + "/get_subproperties/" + resID,
			type     : "POST",
			dataType : "html",
			success  : function (data) {
				$("#resCollection").html(data).removeClass('hide');
				$("#gifLoader").addClass('hide');
				$(".subspad").click(function () {
					($(this).hasClass("btn-success")) ? $(this).removeClass("btn-success") : $(this).addClass("btn-success");
					subs[resID] = [];
					$(".subspad").each(function () {
						if ($(this).hasClass("btn-success")) {
							subs[resID].push($(this).attr("ref"));
						}
					});
				});
				$("#layerModalOk").click(function () {
					if (!$("#esiaMail").hasClass("hide") && !$("#esiaMailAddr").val().length) {
						$("#esiaMailAnnounce").removeClass("hide");
						return false;
					}
					if (subs[resID].length) {
						$("#f_esiaMailAddr").val($("#esiaMailAddr").val());
						$("#order").removeClass("disabled");
						$(item).appendTo('#selectedList');
						$(item).attr('title', 'Двойной щелчок переместит ресурс обратно');
					}
					$('#modalRes').modal('hide');
				});
				$('#modalRes').modal('show');
			},
			error     : function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
		return false;
	}

	$(item).attr('title', 'Щёлкните, чтобы переместить ресурс обратно');

	if (resID === 101) {
		$('#modalInet').modal('show');
		$("#inetModalOk").unbind().click(function () {
			if ($("#inet_reason").val().length < 10) {
				alert("Обоснование необходимости подключения к сети Интернет слишком короткое.");
				return false;
			}
			$(item).appendTo('#selectedList');
			$("#order").removeClass("disabled");
			$("#f_inet_reason").val($("#inet_reason").val());
			$('#modalInet').modal('hide');
		});
		return false;
	}
	
	if (resID === 100) {
		$('#modalEmail').modal('show');
		$("#emailModalOk").unbind().click(function () {
			if ($("#email_addr").val().length < 1) {
				alert("Укажите адрес электронной почты.");
				return false;
			}
			if ($("#email_reason").val().length < 10) {
				alert("Обоснование необходимости подключения электронной почты слишком короткое.");
				return false;
			}
			$(item).appendTo('#selectedList');
			$("#order").removeClass("disabled");
			$("#f_email_addr").val($("#email_addr").val());
			$("#f_email_reason").val($("#email_reason").val());
			$('#modalEmail').modal('hide');
		});
		return false;
	}
	
	if (resID === 274) {
		$('#modalWF').modal('show');
		$("#wfModalOk").unbind().click(function () {
			if ($("#wf_reason").val().length < 10) {
				alert("Обоснование необходимости подключения к Интернет-ресурсам средствами беспроводной сети слишком короткое");
				return false;
			}
			$(item).appendTo('#selectedList');
			$("#order").removeClass("disabled");
			$("#f_wf_reason").val($("#wf_reason").val());
			$('#modalWF').modal('hide');
		});
		return false;
	}
	
	if (resID === 13)  {
		$('#modalPortal').modal('show');
		$("#portalModalOk").unbind().click(function () {
			$('#modalPortal').modal('hide');
		});
		$(item).appendTo('#selectedList');
		$("#order").removeClass("disabled");
		return false;
	}

	$(item).appendTo('#selectedList');
	$("#order").removeClass("disabled");
}

function removeFromList(item) {
	$(item).appendTo('#group' + $(item).attr('grp'));
	$(item).attr('title', 'Щелчок добавит ресурс в список заявок');
}

function validate(dt, val) {
	var r,
		library = {
			email   : '^([0-9a-zA-Z\.\-_]+)@(.*)\.([a-zA-Z]{2,})$',
			text    : '[^a-z \\-"]',
			entext  : '[^0-9a-z \-"]',
			rtext   : '[^а-яёЁ\\-\\.\\, ]',
			rword   : '[^а-яёЁ ]',
			nonzero : '^[0]$',
			date    : '[^0-9\\.]',
			weight  : '[^0-9 xхсмткг\\.]',
			num     : '[^0-9\\- ]',
			mtext   : '[^0-9 a-zа-яёЁ\\.\\,\\-"]',
			reqnum  : '[^0-9 \/\\бн\-]'
		},
		rgEx = new RegExp(library[dt], 'i'),
		OK = rgEx.exec(val);
	if (OK) {
		return 0;
	}
	return 1;
}

function showHelp(con) {
	if (con === true) {
		switch (state) {
			case 0:
				$('#popID').popover('show');
				break;
			case 1:
				$('#popInfo').popover('show');
				break;
			case 2:
				$('#popIR, #accordion').popover('show');
		}
		return true;
	}
	$('#popID, #popInfo, #popIR, #accordion').popover('hide');
}

$('.modalRes .modalEmail .modalInet').modal({ show: 0 });

$("#searchUser, #stage2").click(function() {
	getUserData();
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
});

$("#userSelector").dblclick(function() {
	getUserData();
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
});

$("#oldUser").click(function() {
	bmode = "old";
	$(".acField").fadeOut(100);
	$("#popID, #userHint, #breadCrumbs").fadeIn(700);
});

$("#newUser").click( function() {
	bmode = "new";
	$(".acField").fadeOut(100);
	$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
	$("#r_102").appendTo('#selectedList').removeClass("btn-info").addClass("btn-success");
	$.ajax({
		url      : "/" + controller + "/resetUID",
		type     : "GET",
		dataType : "text",
		success  : function () {},
		error    : function ( data, stat, err ) {
			$("#consoleContent").html([data, stat, err].join("<br>"));
		}
	});
});

$("#office").change(function() {
	$("#office2").empty().append(locs[parseInt($(this).val())].join("\n"));
});

$("#userDataOK, #stage1").unbind().click(function() {
	uid = $("#userSelector").val();
	$.ajax({
		url      : "/" + controller + "/getuserresources",
		type     : "POST",
		data     : {
			uid  : uid
		},
		dataType : "html",
		success  : function (data) {
			$(".acField").fadeOut(100);
			$("#userHeader, #breadCrumbs, #orderContainer, #userHeader, #copyButtons").fadeIn(700);
			$("#fioAcknowledger").html([ $("#sname").val(), $("#name").val(), $("#fname").val() ].join("\n"));
			$("#depAcknowledger").html( $("#dept option:selected").text() );
			$(".stageMarker").addClass("muted");
			$("#stage1").removeClass("muted");
			if ( bmode === "old" ) {
				$("#orderList").html(data);
			} else {
				$("#orderList").html('<h5 class="muted">&nbsp;&nbsp;&nbsp;<i class="icon-exclamation-sign"></i>&nbsp;&nbsp;Заявка на доступ к сети будет добавлена автоматически</h5><hr>');
			}
			// actions
			$(".paperChecker").unbind().click(function () {
				if ( $(".paperChecker:checked").size() > 0 ) {
					$("#regetOrder").removeClass("disabled").removeAttr("disabled").addClass("btn-primary");
					$("#putOrder").removeClass("btn-primary");
				} else {
					$("#regetOrder").addClass("disabled").attr('disabled', 'disabled').removeClass("btn-primary");
					$("#putOrder").addClass("btn-primary");
				}
				if ($(".paperChecker:checked").size() == $(".paperChecker").size()) {
					$("#checkAllPapers").attr("checked", "checked");
				} else {
					$("#checkAllPapers").removeAttr("checked");
				}
			});
			$("#checkAllPapers").unbind().click(function () {
				$(".paperChecker").prop('checked', $("#checkAllPapers").prop('checked'));
			});
		},
		error: function (data, stat, err) {
			$("#consoleContent").html([data, stat, err].join("<br>"));
		}
	});
});

$("#userDataFail").click(function() {
	$("#correctnessAnnot").fadeIn(1000).delay(15000).fadeOut(1000);
});

$("#newOrder, #stage3").click(function() {
	$(".acField").fadeOut(100);
	$("#resdata, #breadCrumbs, #userHeader").fadeIn(700);
	$(".stageMarker").addClass("muted");
	$("#stage3").removeClass("muted");
});

$("#order").click(function () {
	var lsubs = [],
		res   = [],
		confs = [];
	if ($(this).hasClass("disabled")) {
		return false;
	}

	if ($("#sname").val().length < 3 || $("#name").val().length < 2 || $("#fname").val().length < 3) {
		alert("Проверьте правильность введённых имени, фамилии и отчества пользователя");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	if ($("#dept").val() == "0") {
		alert("Выберите подразделение");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	if ($("#staff").val() == "0") {
		alert("Выберите должность");
		$(".acField").fadeOut(100);
		$("#breadCrumbs, #userdata, #userOKButtons").fadeIn(700);
		return false;
	}

	$("#selectedList").find("li").each(function () {
		res.push($(this).attr("id").split("_")[1]);
	});
	if (!res.length && !confs.length) {
		alert("Информационные ресурсы не выбраны");
		return false;
	}
	for (a in subs) {
		lsubs.push(subs[a].join(","));
	}

	if ($("#userSelector").val() != "") {
		$("#login").val(recode_field);
		$( "#res" ).val(res.join(","));
		$("#subs" ).val(lsubs.join(","));
		$("#mainform").submit();
	} else {
		$("#f_uid").val($("#userSelector").val());
	}
});

$("li.reslist").click(function () {
	if($(this).attr("id") == "r_102"){
		return false;
	}
	if ($(this).parent().attr("id") === 'selectedList' ) {
		//console.log('remove');
		removeFromList(this);
	} else {
		//console.log('add');
		addToList(this);
	}
	if (!$("#selectedList li").size()) {
		$("#order").addClass("disabled");
	}
});

$("#portalSectionFilter").keyup(function() {
	if ( $(this).val().length < 4 ){
		return false;
	}
	search = $(this).val();
	$.ajax({
		url: "/" + controller + "/getwebportalsection",
		type: "POST",
		data: {
			search: search
		},
		dataType: "html",
		success: function (data) {
			$("#portalSectionList").empty().append(data);
		},
		error: function (data, stat, err) {
			console.log( [data, stat, err].join("<br>") );
		}
	});
});

$('.traceable').bind('change keyup', function () {
	var pref   = parseInt($(this).attr('pref')),
		reg    = $(this).attr('valid'),
		length = $(this).val().length,
		val    = validate(reg, $(this).val());
	if (!length || !val) {
		$(this).parent().removeClass('success').removeClass('warning').addClass('error');
	} else {
		if ($(this).val().length < pref) {
			$(this).parent().removeClass('error').removeClass('success').addClass('warning');
		} else {
			$(this).parent().removeClass('error').removeClass('warning').addClass('success');
		}
	}
});

$("#searchIR").keyup(function () {
	var text = $("#searchIR").val();
	$(".badge").html(0);
	$(".reslist").removeClass("hide");

	if (!text.length) {
		$(".badge").addClass("hide");
		return true;
	}

	$(".reslist").each(function () {
		if ($(this).html().toLowerCase().indexOf(text.toLowerCase()) + 1) {
			src = $(this).parent().parent().parent().attr("ref");
			$(this).removeClass("hide");
			srf = parseInt($("#badge-collapse" + src).html());
			srf++;
			$("#badge-collapse" + src).empty().html(srf++);
			return true;
		}
		$(this).addClass("hide");
	});

	$(".badge").each(function () {
		if (parseInt($(this).html()) > 0) {
			$(this).removeClass("hide");
			return true;
		}
		$(this).addClass("hide");
	})
});

$("#sname, #name, #fname").keyup(function () {
	$("#fioAcknowledger").html([ $("#sname").val(), $("#name").val(), $("#fname").val() ].join(" "));
});

$("#dept").change(function () {
	disableUnallowedRes();
	$("#depAcknowledger").html($("#dept option:selected").html());
});

$("#regetOrder").click(function () {
	if ( $(this).prop("disabled") ) {
		return false;
	}
	var ids = [];
	$(".paperChecker:checked").each(function () {
		ids.push(parseInt($(this).attr("ref")));
	});
	//alert(ids);
	$("#r_name_f").val($("#sname").val());
	$("#r_name_i").val($("#name").val());
	$("#r_name_o").val($("#fname").val());
	$("#r_staff").val($("#staff").val());
	$("#r_addr1").val($("#office").val());
	$("#r_addr2").val($("#office2").val());
	$("#r_dept").val($("#dept").val());
	$("#r_phone").val($("#phone").val());
	$("#r_uid").val($("#passedUID").val());
	$("#resources").val(ids.join(","));
	$("#regetForm").submit();
	//console.log(ids.join(","));
});

// Реализация интерактивной помощи
$('#getHelp').click(function () {
	$(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
	showHelp($(this).hasClass('active'));
});
