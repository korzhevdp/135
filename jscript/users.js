$("#userid").on("keyup",function(){
	$.ajax({
		url: "/ajax_users/apply_filter/",
		type: "POST",
		data: {
			search : $(this).val(),
			fired  : ($("#withFired").prop("checked")) ? 1 : 0
		},
		dataType: "html",
		cache: false,
		success: function(data){
			//alert(data);
			$("#userSelector").empty().append(data);
			$('#foundCounter').html($('#userSelector').find('option').size());
			$("#userSelector").dblclick(function(){
				$("#passedUID").val($("#userSelector").val());
				$("#userSForm").submit();
			});
			$("#userSelector").find("option").each(function(){
				if($(this).val() == $("#passedUID").val()){
					$(this).attr("selected","selected");
				}else{
					$(this).removeAttr("selected");
				}
			});
		},
		error: function(data,stat,err){
			//alert([data,stat,err].join("<br>"));
		}
	});
});


/*
$("#office").change(function(){
	var id = $(this).val();
	if(id != "0"){
		$.ajax({
			url: "/admin/roomsget/" + id,
			cache: false,
			success: function(data){
				$("#office2").empty().append(data);
			},
			error: function(data,stat,err){
				//$("#consoleContent").html([data,stat,err].join("<br>"));
			}
		});
	}else{
		$("#office2").empty().append("<option value=0>Выберите помещение</option>");
	}
});
*/

$("#c_login, #c_host").click(function(){
	id = $(this).attr("ref");
	$("#" + id).val(recode_field)
});

function recode_field(){
	var sname      = $('#sname').val().toLowerCase().trim(),
		name       = $( '#name').val().toLowerCase().trim(),
		fname      = $('#fname').val().toLowerCase().trim(),
		output     = "",
		sname_conv = [],
		fname_conv = [],
		name_conv  = [],
		r          = {
			'_' : '',
			'-' : '',
			' ' : '',
			'а' : 'a',
			'б' : 'b',
			'в' : 'v',
			'г' : 'g',
			'д' : 'd',
			'е' : 'e',
			'ё' : 'e',
			'ж' : 'zh',
			'з' : 'z',
			'и' : 'i',
			'й' : 'y',
			'к' : 'k',
			'л' : 'l',
			'м' : 'm',
			'н' : 'n',
			'о' : 'o',
			'п' : 'p',
			'р' : 'r',
			'с' : 's',
			'т' : 't',
			'у' : 'u',
			'ф' : 'f',
			'х' : 'h',
			'ц' : 'c',
			'ч' : 'ch',
			'ш' : 'sh',
			'щ' : 'sch',
			'ъ' : '',
			'ы' : 'y',
			'ь' : '',
			'э' : 'e',
			'ю' : 'yu',
			'я' : 'ya',
			' ' : ' '
		},
		sr         = {
			'а' : 'A',
			'б' : 'B',
			'в' : 'V',
			'г' : 'G',
			'д' : 'D',
			'е' : 'E',
			'ё' : 'E',
			'ж' : 'J',
			'з' : 'Z',
			'и' : 'I',
			'й' : 'Y',
			'к' : 'K',
			'л' : 'L',
			'м' : 'M',
			'н' : 'N',
			'о' : 'O',
			'п' : 'P',
			'р' : 'R',
			'с' : 'S',
			'т' : 'T',
			'у' : 'U',
			'ф' : 'F',
			'х' : 'H',
			'ц' : 'C',
			'ч' : 'C',
			'ш' : 'S',
			'щ' : 'S',
			'ъ' : '',
			'ы' : 'Y',
			'ь' : '',
			'э' : 'E',
			'ю' : 'Y',
			'я' : 'Y'
		},
		out = 'Конверсия закончилась неудачей';

	if (sname.length && name.length && fname.length){
		for (i = 1; i < sname.length; ++i){
			sname_conv.push(r[sname.charAt(i)]);
		}

		fname_conv.push(sr[fname.charAt(0)]);
		name_conv.push(sr[name.charAt(0)]);

		out = [ 
			r[sname.charAt(0)].charAt(0).toUpperCase(),
			r[sname.charAt(0)].substr(1),
			sname_conv.join(''),
			name_conv.join(''),
			fname_conv.join('')
		].join('');
		return out;
	}
	//alert(out);
	return false;
}


/*
function recode(){
	var sname = $('#sname').val().toLowerCase(),
	name = $('#name').val().toLowerCase(),
	fname = $('#fname').val().toLowerCase(),
	output = "";
	sname_conv = [],
	fname_conv = [],
	name_conv =[],
	r = [];

	r['_']='';
	r['-']='';
	r[' ']='';
	r['а']='a';
	r['б']='b';
	r['в']='v';
	r['г']='g';
	r['д']='d';
	r['е']='e';
	r['ё']='e';
	r['ж']='zh';
	r['з']='z';
	r['и']='i';
	r['й']='y';
	r['к']='k';
	r['л']='l';
	r['м']='m';
	r['н']='n';
	r['о']='o';
	r['п']='p';
	r['р']='r';
	r['с']='s';
	r['т']='t';
	r['у']='u';
	r['ф']='f';
	r['х']='h';
	r['ц']='c';
	r['ч']='ch';
	r['ш']='sh';
	r['щ']='sch';
	r['ъ']='';
	r['ы']='y';
	r['ь']='';
	r['э']='e';
	r['ю']='yu';
	r['я']='ya';
	r[' ']=' ';
	for (i = 0; i < sname.length; ++i){
		sname_conv.push(r[sname.charAt(i)]);
	}
	sname_conv2 = sname_conv[0].toUpperCase();
	r['ж']='j';
	r['ч']='c';
	r['ш']='s';
	r['щ']='s';
	r['ю']='y';
	r['я']='y';

	fname_conv.push(r[fname.charAt(0)].toUpperCase());
	name_conv.push(r[name.charAt(0)].toUpperCase());
	output = sname_conv2 + sname_conv.join('').substr(1) + name_conv.join('') + fname_conv.join('');
	$("#f_login").val(output);
}
*/
$(".activate").click(function(){
	var id = $(this).attr('prop');
	$.ajax({
		url: "/admin/ressubmit/",
		type: "POST",
		data: {
			id: id,
			num:   (typeof $("#nm_" + id).val()    != 'undefined' && $("#nm_" + id).val().length) ? $("#nm_" + id).val() : 0,
			date:  (typeof $("#date_" + id).val()  != 'undefined') ? $("#date_" + id).val() : 0,
			ip:    (typeof $("#ip_" + id).val()    != 'undefined' && $("#ip_" + id).val().length) ? $("#ip_" + id).val() : 0,
			email: (typeof $("#email_" + id).val() != 'undefined' && $("#email_" + id).val().length) ? $("#email_" + id).val() : 0
		},
		success: function(data){
			$(".activate[prop="+ id +"]").removeClass("btn-primary").addClass("btn-success");
			$(".activate[prop="+ id +"]").html('<i class="icon-edit icon-white"></i>&nbsp;Подключена');
			$("#ss" + id).appendTo('#actconn');
			$("#expnum").html($('#expconn').children().length);
			$("#actnum").html($('#actconn').children().length);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$(".hookup").click(function(){
	var id = $(this).attr('prop');
	$.ajax({
		url: "/admin/reshookup/" + id,
		success: function(data){
			$(".hookup[prop="+ id +"]").removeClass("btn-primary").addClass("btn-success");
			$(".hookup[prop="+ id +"]").html('<i class="icon-edit icon-white"></i>&nbsp;Исполнена');
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$(".expire").click(function(){
	var id = $(this).attr('prop');
	$.ajax({
		url: "/admin/resexpire/" + id,
		success: function(data){
			$("#ss" + id).appendTo('#expconn');
			$("#expnum").html($('#expconn').children().length);
			$("#actnum").html($('#actconn').children().length);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$(".delete").click(function(){
	var id = $(this).attr('prop');
	$.ajax({
		url: "/admin/resdelete/" + id,
		success: function(data){
			$("#ss" + id).remove();
			$("#expnum").html($('#expconn').children().length);
			$("#actnum").html($('#actconn').children().length);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$("#usermerge").click(function(){
	vals = $("#userSelector").val();
	//alert ("/admin/usermerge/" + vals[0] + "/" + vals.slice(1).join("_"));
	//return false;
	$.ajax({
		url: "/admin/usermerge/" + vals[0] + "/" + vals.slice(1).join("_"),
		success: function(data){
	//		alert(11);
			$("#userid").keyup();
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$(".invsaver").click(function() {
	var id = $(this).attr("ref"),
		val = $("#inv" + id).val();
	$.ajax({
		url: "/admin/invnumupdate/" + id + "/" + val,
		success: function(data){
			$(this).removeClass("btn-warning").addClass("btn-success");
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$(".armselector").click(function(){
	id = $(this).attr("act");
	//alert(id);
	$(".table-arm").addClass("hide");
	$("#hideallarm").removeClass("hide");
	$("#tbd" + id).removeClass("hide");
});

$("#hideallarm").click(function(){
	$(".table-arm").addClass("hide");
	$(this).addClass("hide");
});

$("#hideallarm").click(function(){
	$(".table-arm").addClass("hide");
	$(this).addClass("hide");
});

$(".fireSw").click(function(){
	button = $(this);
	id = button.attr('ref');
	$.ajax({
		url: "/admin/switchfired/" + id,
		success: function(data){
			(button.hasClass("btn-info")) ? button.removeClass("btn-info").addClass("btn-inverse").empty().html("Уволен(а)") : button.removeClass("btn-inverse").addClass("btn-info").empty().html("Уволить");
			//$(this).empty().html(data);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$("#curSw").click(function(){
	button = $(this);
	id = $(this).attr('ref');
	$.ajax({
		url: "/admin/switchsman/" + id,
		success: function(data){
			(data == "1") ? button.removeClass("btn-info").addClass("btn-warning") : button.removeClass("btn-warning").addClass("btn-info") ;
			//$(this).empty().html(data);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$("#airSw").click(function(){
	button = $(this);
	id = $(this).attr('ref');
	$.ajax({
		url: "/admin/switchair/" + id,
		success: function(data){
			(data == "1") ? button.removeClass("btn-info").addClass("btn-warning") : button.removeClass("btn-warning").addClass("btn-info") ;
			//$(this).empty().html(data);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});

$("#birSw").click(function(){
	button = $(this);
	id = $(this).attr('ref');
	$.ajax({
		url: "/admin/switchbir/" + id,
		success: function(data){
			(data == "1") ? button.removeClass("btn-info").addClass("btn-warning") : button.removeClass("btn-warning").addClass("btn-info") ;
			//$(this).empty().html(data);
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});
/*
$(".invsaver").click(function() {
	var id = $(this).attr("ref"),
		val = $("#inv" + id).val();
	$.ajax({
		url: "/admin/removeFromARM/" + id + "/" + val,
		success: function(data){
			$(this).removeClass("btn-warning").addClass("btn-success");
		},
		error: function(data,stat,err){
			$("#consoleContent").html([data,stat,err].join("<br>"));
		}
	});
});
*/
/*
$("#host, #login").keyup(function(){
	$("#t_host").val($("#host").val());
	$("#t_login").val($("#login").val());
});
*/
$("#userid").keyup();
//$("#office").change();

$(".pcflowtab").dblclick(function(){
	var href = $(this).attr("href");
	window.location = href;
});

$(".pcflowtab").click(function(){
	var id = $(this).attr("ref");
	$.ajax({
		url: "/console/pcconf/" + id,
		type: "GET",
		dataType: "html",
		success: function(data){
			$("#confcontainer").html(data);
		}
	})
});

$(".confhide").click(function(){
	$(".table-arm, #hideallarm").addClass("hide");
});

$("#inactiveToggler").click(function(){
	if($(this).prop("checked")){
		$(".table-licenses tr.muted").removeClass("hide");
	}else{
		$(".table-licenses tr.muted").addClass("hide");
	}
	
});

$(function($){
	$.datepicker.regional['ru'] = {
		closeText          : 'Закрыть',
		prevText           : '<<',
		nextText           : '>>',
		currentText        : 'Сегодня',
		monthNames         : ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort    : ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
		dayNames           : ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
		dayNamesShort      : ['вос', 'пон', 'втр', 'срд', 'чтв', 'пят', 'суб'],
		dayNamesMin        : ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб' ],
		weekHeader         : 'Нед',
		dateFormat         : 'dd.mm.yy',
		firstDay           : 1,
		isRTL              : false,
		showMonthAfterYear : false,
		yearSuffix         : ''
	};
	$(".wDate").datepicker($.datepicker.regional['ru']);
	$(".wDate").datepicker( "option", "changeYear", true);
});

$("#pcFilter").keyup(function(){
	$("li.armselector").addClass("hide");
	$("li.armselector").each(function(){
		if($(this).attr("title").toLowerCase().indexOf($("#pcFilter").val().toLowerCase()) !== -1){
			$(this).removeClass("hide");
		}
	})

});
