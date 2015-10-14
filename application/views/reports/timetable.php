<style type="text/css">
	.markAll{
		color: #cc0000;
		cursor:pointer;
	}
	.holiday{
		color: #ffff33;
		font-weight: bolder;
		cursor:pointer;
		background-color: #ff6699 !important
	}
	.dayCell{
		vertical-align:middle !important;
		text-align:center !important;
		cursor:pointer;
	}
	.dcSelected{
		background-color: #66cc00 !important
	}
</style>
<h2>Табель учёта рабочего времени</h2><hr>
<center><h4><?=$nav;?></h4></center>
<?=$table;?>


<a href="<?=$linktoword?>" type="button" class="pull-right btn btn-primary btn-large"><i class="icon-file icon-white"></i>&nbsp;&nbsp;&nbsp;Отчёт в Word</a>

<div id="statSetter" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="statSetter" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close clear" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">Установка статуса <small> на выбранные дни</small></h3>
	</div>
	<div class="modal-body">
		Для работника <strong><span id="uidRange">(ФИО)</span></strong><br>в период: <strong><span id="dateRange">(дата)</span></strong><br><br>
		установить статус:<br>
			<span style="width:200px;" class="statusW btn btn-success clear" ref="1" data-dismiss="modal">Я – явка&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;
			<span style="width:200px;" class="statusW btn btn-success clear" ref="4" data-dismiss="modal">К – командировка</span><hr>
			<span style="width:200px;" class="statusW btn btn-primary clear" ref="2" data-dismiss="modal">В – выходной</span>&nbsp;&nbsp;&nbsp;
			<span style="width:200px;" class="statusW btn btn-primary clear" ref="3" data-dismiss="modal">П – праздник&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><hr>
			<span style="width:200px;" class="statusW btn btn-warning clear" ref="5" data-dismiss="modal">О – отпуск&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;
			<span style="width:200px;" class="statusW btn btn-warning clear" ref="6" data-dismiss="modal">Б – больничный&nbsp;&nbsp;&nbsp;</span><br>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary clear" data-dismiss="modal" aria-hidden="true">Закрыть</button>
	</div>
</div>

<script type="text/javascript">
<!--
	var sc  = 0,
		uid = 0,
		dh  = "",
		row = 0;
	$('.modal').modal({ show: 0 });
	dhs = [];
	uids = [];
	uidns = [];

	$(".dayCell").unbind().click(function(){
		dhs = [];
		uids = [];
		uidns = [];
		if(sc){
			if(parseInt(dh) != parseInt($(this).attr("dh")) && parseInt(row) != parseInt($(this).attr("row"))){
				alert("Выбран некорректный диапазон.\nВыберите ячейки из одного столбца или одной строки");
				return false;
			}
			//назначение статуса группе лиц
			if(dh == $(this).attr("dh")){
				lrow = parseInt($(this).attr("row"));
				if(lrow < row){
					ws = row;
					row = lrow;
					lrow = ws;
				}
				$(".dayCell").each(function(){
					if(parseInt($(this).attr("row")) >= row && parseInt($(this).attr("row")) <= lrow && $(this).attr("dh") == dh){
						//alert("row: " + $(this).attr("row") + "\nfirstrow: " + row +  "\nlastrow: " + lrow)
						$(this).addClass("dcSelected");
						uids.push($(this).attr("uid"));
						uidns.push($("#uid" + $(this).attr("uid")).html());
					}
				});
				$("#uidRange").html(uidns.join(", "));
				$("#dateRange").html([  dh.substr(6,2) ,  dh.substr(4,2),  dh.substr(0,4)].join("."));
				//alert("Выбран 1 день, люди разные - назначение статуса группе лиц" + row + ' -+- ' + $(this).attr("row"));
				$("#statSetter").modal("show");
			}
			//назначение статуса одному человеку на период;
			if(uid == $(this).attr("uid")){
				dhl = parseInt($(this).attr("dh"));
				sd  = [  dh.substr(6,2) ,  dh.substr(4,2),  dh.substr(0,4)].join(".");
				ed  = [ dhl.toString().substr(6,2) , dhl.toString().substr(4,2), dhl.toString().substr(0,4)].join(".");
				$("#uidRange").html($("#uid" + uid).html());
				$("#dateRange").html([sd, ed].join(" - "));
				if(dhl < dh){
					ws = dh;
					dh = dhl;
					dhl = ws;
				}
				$(".dayCell").each(function(){
					if(parseInt($(this).attr("dh")) >= parseInt(dh) && parseInt($(this).attr("dh")) <= dhl && $(this).attr("uid") == uid){
						$(this).addClass("dcSelected");
						dhs.push($(this).attr("dh"));
					}
				});

				//alert("Выбран 1 человек, дни разные - назначение статуса одному человеку на период");
				//alert(dhs.join(" - "))
				$("#statSetter").modal("show");
			}
			sc = 0;
			//$(".dayCell").removeClass("dcSelected");

		}else{
			if($(this).hasClass("dcSelected")){
				$(this).removeClass("dcSelected");
			}else{
				$(this).addClass("dcSelected");
				uid = $(this).attr("uid");
				dh  = $(this).attr("dh");
				row = parseInt($(this).attr("row"));
				sc++;
			}
		}
	});

	$(".statusW").click(function(){
		var stat = $(this).attr('ref');
		$.ajax({
			url: "/reports/insert_wkt_data",
			data: { 
				dh: dhs,
				uid: uid,
				stat: stat,
				uids: uids,
				dhc: dh
			},
			type: "POST",
			dataType: "html",
			success: function(){
				window.location.reload();
			},
			error: function(data,stat,err){
				alert([data,stat,err].join("<br>"));
			}
		});
	});

	$(".clear").click(function(){
		$(".dayCell").removeClass("dcSelected");
	});
//-->
</script>