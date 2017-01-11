<!doctype html>
<html lang="ru">
<head>
	<title>Пользователь - детали</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Сache-Control" content="no-cache">
	<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
	<!-- <link href="/bootstrap/css/bootstrap-responsive.css" rel="stylesheet"> -->
	<script type="text/javascript" src="http://api.arhcity.ru/jscript/jquery.js"></script>
	<script type="text/javascript" src="http://api.arhcity.ru/jqueryui/js/jqueryui.js"></script>
	<script type="text/javascript" src="http://api.arhcity.ru/bootstrap/js/bootstrap.js"></script>
	<link rel="stylesheet" href="http://api.arhcity.ru/jqueryui/css/jqueryui.css" />

	<style type="text/css">
			body{
				overflow-y: scroll;
			}

		.pclist, .pcused{
			display:block;
			width:80%;
		}
		.pclist:hover{
			color:#330066;
		}
		ul.pclist li{
			list-style-position:inside;
			list-style-image: url("/images/windows.png");
			text-align:center;
			float: left;
			margin-right:25px;
			cursor: pointer;
			width:145px;
			color:#666666;
			font-weight:bolder;
		}
		ul.pcused li{
			list-style-position:inside;
			list-style-image: url("/images/windows-2.png");
			text-align:center;
			float: left;
			margin-right:25px;
			cursor: pointer;
			width:145px;
			color:#666666;
			font-weight:bolder;
			-moz-user-select: none;
			-khtml-user-select: none;
			user-select: none; 
		}
	</style>
</head>

<!-- <script type="text/javascript" src="/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="/jqplotjs/plugins/jqplot.trendline.min.js" /></script>
<script type="text/javascript" src="/jqplotjs/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>  -->

<body>
	<div class="navbar navbar-fixed-top navbar-inverse" sty>
		<div class="navbar-inner"><a class="brand" href="#" style="margin-left:0px;">ML-Console v.4b</a>

			<ul class="nav pull-right">
				<li>
					<a href="/login/logout"><?=($this->session->userdata('selfname')) ? "Разлогиниться" : "";?></a></a>
				</li>
			</ul>
			<ul class="nav pull-right">
				<li>
					<a href="/kb"><i class="icon-book icon-white"></i>&nbsp;&nbsp;База знаний</a>
				</li>
				<li>
					<a href="#"><i class="icon-user icon-white"></i>&nbsp;&nbsp;<?=($this->session->userdata('selfname')) ? $this->session->userdata('selfname') : "Оформление Заявок";?></a>
				</li>
			</ul>
		</div>
	</div>

	<div class="container-fluid" id="maincontainer" style="margin-top:60px;height:100%">
		<div class="row-fluid" style="height:100%;margin-bottom:80px;">
			<div class="span2 well well-small" style="min-width:250px;">
				<?=$menu;?>
			</div>
			<div class="span8" style="min-width:600px;border-left:1px dotted #e6e6e6;border-right:1px dotted #e6e6e6;padding:10px;height:100%">
				<?=$content;?>
			</div>
		</div>
	</div>

<?=$footer;?>

<script type="text/javascript">
<!--
	$("#maincontainer").css('width',($(window).width() - 64) + 'px');
	$(".deleter").click(function(){
		var id = $(this).attr("prop");
		$.ajax({
			url: "/admin/resdelete/" + id,
			success: function(){
				$("#bt" + id).removeClass('btn-warning').empty().html("Удалена");
			},
			error: function(data,stat,err){
				console.log( data, stat, err );
			}
		});
	});

	$(".ESIAOff").click(function(){
		var id = $(this).attr("ref");
		$.ajax({
			url: "/admin/resexpire/" + id,
			success: function(){
				window.location.reload();
			},
			error: function(data,stat,err){
				console.log( data, stat, err );
			}
		});
	});

	$(".inGroupSw").click( function () {
		var id = $(this).attr("ref");
		$.ajax({
			url      : "/admin/ingroup",
			data     : {
				itemID : id
			},
			type     : "POST",
			dataType : "text",
			success  : function (data) {
				if (data === "1") {
					$(".inGroupSw[ref=" + id + "]").parent().parent().removeClass("error").removeClass("warning").addClass("success"); // местами не менять :)
					$(".inGroupSw[ref=" + id + "]").parent().html('<i class="icon-ok"></i>');
				}
			},
			error: function ( data, stat, err ) {
				console.log( data, stat, err );
			}
		});
	});

//-->
</script>
</body>
</html>