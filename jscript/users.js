$("#userid").on("keyup",function(){
	$.ajax({
		url: "/ajax_users/apply_filter",
		type: "POST",
		data: {
			search : $(this).val(),
			fired  : ($("#withFired").prop("checked")) ? 1 : 0
		},
		dataType: "html",
		cache: false,
		success: function(data){
			$("#userSelector").empty().append(data);
			$("#userSelector").dblclick(function() {
				$("#passedUID").val($("#userSelector").val());
				$("#userSForm").submit();
			});
			$("#userSelector option").each(function() {
				if ($(this).val() === $("#passedUID").val()) {
					$(this).prop("selected", true);
				}
				$(this).prop("selected", false);
			});
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});


$("#c_login, #c_host").click(function(){
	$("#" + $(this).attr("ref")).val(recode_field)
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
			'�' : 'a',
			'�' : 'b',
			'�' : 'v',
			'�' : 'g',
			'�' : 'd',
			'�' : 'e',
			'�' : 'e',
			'�' : 'zh',
			'�' : 'z',
			'�' : 'i',
			'�' : 'y',
			'�' : 'k',
			'�' : 'l',
			'�' : 'm',
			'�' : 'n',
			'�' : 'o',
			'�' : 'p',
			'�' : 'r',
			'�' : 's',
			'�' : 't',
			'�' : 'u',
			'�' : 'f',
			'�' : 'h',
			'�' : 'c',
			'�' : 'ch',
			'�' : 'sh',
			'�' : 'sch',
			'�' : '',
			'�' : 'y',
			'�' : '',
			'�' : 'e',
			'�' : 'yu',
			'�' : 'ya',
			' ' : ' '
		},
		sr         = {
			'�' : 'A',
			'�' : 'B',
			'�' : 'V',
			'�' : 'G',
			'�' : 'D',
			'�' : 'E',
			'�' : 'E',
			'�' : 'J',
			'�' : 'Z',
			'�' : 'I',
			'�' : 'Y',
			'�' : 'K',
			'�' : 'L',
			'�' : 'M',
			'�' : 'N',
			'�' : 'O',
			'�' : 'P',
			'�' : 'R',
			'�' : 'S',
			'�' : 'T',
			'�' : 'U',
			'�' : 'F',
			'�' : 'H',
			'�' : 'C',
			'�' : 'C',
			'�' : 'S',
			'�' : 'S',
			'�' : '',
			'�' : 'Y',
			'�' : '',
			'�' : 'E',
			'�' : 'Y',
			'�' : 'Y'
		},
		out = '��������� ����������� ��������';

	if (!sname.length || !name.length || !fname.length) {
		return false;
	}

	for (i = 1; i < sname.length; ++i) {
		if ( r[sname.charAt(i)] !== undefined ) {
			sname_conv.push(r[sname.charAt(i)]);
		}
	}

	fname_conv.push(sr[fname.charAt(0)]);
	name_conv.push(sr[name.charAt(0)]);

	return [
		r[sname.charAt(0)].charAt(0).toUpperCase(),
		r[sname.charAt(0)].substr(1),
		sname_conv.join(''),
		name_conv.join(''),
		fname_conv.join('')
	].join('');
	
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
	r['�']='a';
	r['�']='b';
	r['�']='v';
	r['�']='g';
	r['�']='d';
	r['�']='e';
	r['�']='e';
	r['�']='zh';
	r['�']='z';
	r['�']='i';
	r['�']='y';
	r['�']='k';
	r['�']='l';
	r['�']='m';
	r['�']='n';
	r['�']='o';
	r['�']='p';
	r['�']='r';
	r['�']='s';
	r['�']='t';
	r['�']='u';
	r['�']='f';
	r['�']='h';
	r['�']='c';
	r['�']='ch';
	r['�']='sh';
	r['�']='sch';
	r['�']='';
	r['�']='y';
	r['�']='';
	r['�']='e';
	r['�']='yu';
	r['�']='ya';
	r[' ']=' ';
	for (i = 0; i < sname.length; ++i){
		sname_conv.push(r[sname.charAt(i)]);
	}
	sname_conv2 = sname_conv[0].toUpperCase();
	r['�']='j';
	r['�']='c';
	r['�']='s';
	r['�']='s';
	r['�']='y';
	r['�']='y';

	fname_conv.push(r[fname.charAt(0)].toUpperCase());
	name_conv.push(r[name.charAt(0)].toUpperCase());
	output = sname_conv2 + sname_conv.join('').substr(1) + name_conv.join('') + fname_conv.join('');
	$("#f_login").val(output);
}
*/
$(".activate").click(function() {
	var id = $(this).attr('prop');
	$.ajax({
		url     : "/admin/ressubmit",
		type    : "POST",
		data    : {
			id    : id,
			num   : ($("#nm_" + id).val()    === undefined || !$("#nm_" + id).val().length)   ? 0 : $("#nm_" + id).val(),
			ip    : ($("#ip_" + id).val()    === undefined || $("#ip_" + id).val().length)    ? 0 : $("#ip_" + id).val(),
			email : ($("#email_" + id).val() === undefined || $("#email_" + id).val().length) ? 0 : $("#email_" + id).val(),
			date  : ($("#date_" + id).val()  === undefined) ? 0 : $("#date_" + id).val()
		},
		success : function(data) {
			$(".activate[prop="+ id +"]").removeClass("btn-primary").addClass("btn-success");
			$(".activate[prop="+ id +"]").html('<i class="icon-edit icon-white"></i>&nbsp;����������');
			$("#ss" + id).appendTo('#actconn');
			$("#expnum").html($('#expconn').children().length);
			$("#actnum").html($('#actconn').children().length);
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$(".hookup").click(function() {
	var id = $(this).attr('prop');
	$.ajax({
		url     : "/admin/reshookup/" + id,
		success : function(data){
			$(".hookup[prop="+ id +"]").removeClass("btn-primary").addClass("btn-success");
			$(".hookup[prop="+ id +"]").html('<i class="icon-edit icon-white"></i>&nbsp;���������');
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$(".expire").click(function() {
	var id = $(this).attr('prop');
	$.ajax({
		url: "/admin/resexpire/" + id,
		success: function(data){
			$("#ss" + id).appendTo('#expconn');
			$("#expnum").html($('#expconn').children().length);
			$("#actnum").html($('#actconn').children().length);
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
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
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$("#usermerge").click(function() {
	var values = $("#userSelector").val(); // select multiple
	$.ajax({
		url     : "/admin/usermerge",
		type    : "POST",
		data    : {
			target  : values[0],
			sources : values.slice(1)
		},
		success : function(data) {
			$("#userid").keyup();
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$(".invsaver").click(function() {
	$.ajax({
		url     : "/admin/invnumupdate",
		type    : "POST",
		data    : {
			id  : $(this).attr("ref"),
			val : $("#inv" + id).val()
		},
		success : function(data) {
			$(this).removeClass("btn-warning").addClass("btn-success");
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
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

$(".fireSw").click(function() {

	button = $(this);
	id     = $(this).attr('ref');
	$.ajax({
		url     : "/admin/switchfired",
		data    : { id : id },
		type    : "POST",
		dataType: 'script',
		success : function() {
			if (data.error === 0) {
				if ( button.hasClass("btn-info") ) {
					button.removeClass("btn-info").addClass("btn-inverse").empty().html("������(�)");
					return true;
				}
				button.removeClass("btn-inverse").addClass("btn-info").empty().html("�������");
				return true;
			}
			console.log(data.message);
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$("#curSw").click(function(){
	button = $(this);
	id = $(this).attr('ref');
	$.ajax({
		url: "/admin/switchsman/" + id,
		success: function(data){
			if (data == "1") {
				button.removeClass("btn-info").addClass("btn-warning");
				return true;
			}
			button.removeClass("btn-warning").addClass("btn-info") ;
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$("#airSw").click(function(){
	var button	= $(this),
		id		= button.attr('ref');
	$.ajax({
		url     : "/admin/switchair/" + id,
		success : function(data) {
			if (data == "1") {
				button.removeClass("btn-info").addClass("btn-warning");
				return true;
			}
			button.removeClass("btn-warning").addClass("btn-info") ;
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$("#birSw").click(function(){
	var button	= $(this),
		id		= button.attr('ref');
	$.ajax({
		url     : "/admin/switchbir/" + id,
		success : function(data) {
			if (data == "1") {
				button.removeClass("btn-info").addClass("btn-warning");
				return true;
			}
			button.removeClass("btn-warning").addClass("btn-info");
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	});
});

$("#userid").keyup();

$(".pcflowtab").dblclick(function(){
	var href = $(this).attr("href");
	window.location = href;
});

$(".pcflowtab").click(function(){
	var id = $(this).attr("ref");
	$.ajax({
		url      : "/console/pcconf/" + id,
		type     : "GET",
		dataType : "html",
		success  : function(data) {
			$("#confcontainer").html(data);
		},
		error   : function( data, stat, err) {
			console.log( [data, stat, err] );
		}
	})
});

$(".confhide").click(function() {
	$(".table-arm, #hideallarm").addClass("hide");
});

$("#inactiveToggler").click(function() {
	if ($(this).prop("checked")) {
		$(".table-licenses tr.muted").removeClass("hide");
		return false;
	}
	$(".table-licenses tr.muted").addClass("hide");
});

$(function($){
	$.datepicker.regional['ru'] = {
		closeText          : '�������',
		prevText           : '<<',
		nextText           : '>>',
		currentText        : '�������',
		monthNames         : ['������', '�������', '����', '������', '���', '����', '����', '������', '��������', '�������', '������', '�������'],
		monthNamesShort    : ['���', '���', '���', '���', '���', '���', '���', '���', '���', '���', '���', '���'],
		dayNames           : ['�����������', '�����������', '�������', '�����', '�������', '�������', '�������'],
		dayNamesShort      : ['���', '���', '���', '���', '���', '���', '���'],
		dayNamesMin        : ['��', '��', '��', '��', '��', '��', '��' ],
		weekHeader         : '���',
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
