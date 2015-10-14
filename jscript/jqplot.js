	$(document).ready(function(){
		var vals = {<?=$graph;?>};

		function display_graph( vals, a ){
			window['plot' + a] = $.jqplot('ordchart' + a, [vals], {
				title: 'Поданные заявки по месяцам года',
				series: [{renderer:$.jqplot.BarRenderer}],
				axesDefaults: {
					tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
					tickOptions: {
						angle: -30,
						fontSize: '10pt'
					}
				},
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer
					}
				}
			});
			eval('plot' + a).replot();
		}

		for(a in vals){
			window['plot' + a] = new Object;
			display_graph( vals[a] , a);
		}


		var data = [<?=$servicemendata;?>];
		if(data.length){
			$.jqplot ('chart2', [data],{
				title: 'Распределение пользователей ЛВСМ по специалистам',
				seriesDefaults: {
					renderer: $.jqplot.PieRenderer,
					rendererOptions: {
						showDataLabels: true
					}
				},
				legend: { show:true, location: 'w' }
				}
			);
		}

		$(".tabber").click(function(){
			key = $(this).attr("key");
			//alert(key);
			eval('plot' + key).replot();
		});

		$(".serv_complete").click(function(){
			id = $(this).attr("ref");
			$.ajax({
				url: "/admin/reshookup/" + id,
				success: function(data){
					//$(this).parent().parent().parent().parent().addClass("hide");
					$("#btn" + id).removeClass("btn-warning").addClass("btn-success");
				},
				error: function(data,stat,err){
					$("#consoleContent").html([data,stat,err].join("<br>"));
				}
			});
			return false;
		});

		$(".serv_decline").click(function(){
			id = $(this).attr("ref");
			//return false;
			$.ajax({
				url: "/admin/resexpiredandapplied/" + id,
				success: function(data){
					$(this).parent().parent().parent().parent().addClass("muted");
					$("#btn" + id).removeClass("btn-warning");
				},
				error: function(data,stat,err){
					$("#consoleContent").html([data,stat,err].join("<br>"));
				}
			});
			return false;
		});
	});