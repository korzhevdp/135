<div class="navbar navbar-fixed-bottom" >
	<div class="navbar-inner"><a class="brand"  style="margin-left:0px;" href="#">ML-Console v.4b</a>
		<ul class="nav pull-right">
			<li class="active">
				<a href="#">
				<i class="icon-hdd"></i>&nbsp;<?php echo $this->benchmark->memory_usage();?>
				&nbsp;&nbsp;
				<i class="icon-time"></i>&nbsp;<?php echo $this->benchmark->elapsed_time();?>
				</a>
			</li>
		</ul>
	</div>
</div>