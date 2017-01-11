<div class="accordion" id="accordion2" style="margin-top:10px;">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a style="text-decoration:none;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">Действующие заявки <span id="actnum" class="badge badge-success"><?=$asize;?></span></a>
		</div>
		<div id="collapseOne" class="accordion-body collapse in">
			<div class="accordion-inner" id="actconn"><?=$active;?></div>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a style="text-decoration:none;" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">Отменённые заявки <span id="expnum" class="badge badge-info"><?=$esize;?></span></a>
		</div>
		<div id="collapseThree" class="accordion-body collapse">
			<div class="accordion-inner" id="expconn"><?=$expired;?></div>
		</div>
	</div>
</div>