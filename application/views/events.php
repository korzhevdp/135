<style type="text/css">
	.text {
		
	}
	.date {
		width:120px;
	}
	.more {
		width:350px;
	}
	.control {
		width:28px;
		vertical-align: middle !important;
		background-color:#aaaaaa !important;
		display:table-cell;
		text-align: center;
	}
	.control a {
		padding: 10px 5px;
	}
</style>

<h4>Сообщения</h4>

<table class="table table condensed table-bordered">
<tr>
	<th class="text">Текст</th>
	<th class="more">Дополнительно</th>
	<th class="date">Дата</th>
	<th class="control">&nbsp;&nbsp;<i class="icon-remove icon-white"></i></th>
</tr>
<?=$messages;?>
</table>