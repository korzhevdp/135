<h2>Выборки из базы данных.&nbsp;&nbsp;<small>Данные о пользователях, АРМ и пр.</small></h2><hr>
<form method=post action="/console/search" class="form-horizontal">

	<div class="control-group">
		<label class="control-label span2">Ф.И.О / Хостнейм</label>
		<div class="controls">
			<input type="text" class="span12" maxlength=160 name="name" id="name" value="<?=($this->input->post("name")) ? $this->input->post("name") : "";?>">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Подразделение</label>
		<div class="controls">
			<select name="dep_id" id="dep_id" class="span12">
				<option value=0>Выберите подразделение</option>
				<?=$depts;?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Адрес, кабинет</label>
		<div class="controls">
			<select id="office" name="office" class="span12">
				<option value=0>Выберите адрес</option>
				<?=$location;?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Должность</label>
		<div class="controls">
			<select class="span12" id="staff_id" name="staff_id">
				<option value=0>Выберите должность</option>
				<?=$staffs; ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Адрес IP</label>
		<div class="controls">
			<div class="input-prepend span12">
				<span class="add-on" style="width:15.75%;height:20px;"><b>192.168.</b></span>
				<input type="text" style="margin-left:0px;" maxlength=20 class="span10" name="ip" id="ip" value="<?=($this->input->post("ip")) ? $this->input->post("ip") : "";?>">
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Интернет</label>
		<div class="controls">
			<?=$inet;?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Уволенные</label>
		<div class="controls">
			<?=$fired;?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">E-mail</label>
		<div class="controls">
			<input type="text" maxlength=60 class="span12" name="email" id="email"  value="<?=($this->input->post("email")) ? $this->input->post("email") : "";?>">
		</div>
	</div>

	<fieldset style="margin-bottom:20px;">
		<legend>Список подключенных к ресурсам</legend>
		<div class="control-group">
			<label class="control-label span2">&nbsp;&nbsp;&nbsp;</label>
			<div class="controls">
				<select name="res" id="res" class="span12">
					<option value=0>Выберите информационный ресурс</option>
					<?=$res; ?>
				</select>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="to">
	<button type="submit" class="btn btn-primary pull-right">Отправить запрос</button>
</form>
<script type="text/javascript" src="/jscript/users.js"></script>