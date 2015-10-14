<h3>Операторы.&nbsp;&nbsp;&nbsp;<small>Права доступа</small></h3>

<form method="post" action="#" class="form-horizontal">

<table class="table table-bordered table-condensed table-hover table-container">
<tr>
	<th class="span6">Логин</th>
	<th class="span2">Ранг</th>
	<th class="span1"></th>
</tr>
<?=$admin_list;?>
</table>

<hr>

<h3>Оператор.&nbsp;&nbsp;&nbsp;<small>Панель редактирования</small></h3>
<div class="well well-small span12 container-fluid" style="margin-left:0px;margin-bottom:80px;">

	<div class="control-group">
		<label for="login" class="control-label span2">Пользователь ЛВСМ</label>
		<div class="controls">
			<select name="candidate" id="candidate" class="span10">
				<?=$candidates;?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label for="login" class="control-label span2">Логин</label>
		<div class="controls">
			<input type="text" name="login" id="login" class="span3" placeholder="Введите новый логин" value="<?=$user;?>">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="description" id="login" class="span7" placeholder="Введите описание" value="<?=$description;?>">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">&nbsp;&nbsp;</label>
		<div class="controls">
			<label class="checkbox inline"><?=$rank;?>Администратор</label>
		</div>
	</div>

	<div class="control-group">
		<label for="fn" class="control-label span2">Новый пароль</label>
		<div class="controls">
			<input type="password" name="newpassword" id="newpassword" class="span10" value="">
			<button type="submit" class="btn btn-info btn-small" value="1" name="newPass">Задать</button>
		</div>
	</div>

	<div class="control-group">
		<label for="followers" class="control-label span2">Руководитель</label>
		<div class="controls">
			<select name="supervisor" id="supervisor" class="span10">
				<?=$sups?>
			</select>
		</div>
	</div>
	<input type="hidden" name="userToSave" value="<?=$this->input->post("showUser")?>"><br>
	<button type="submit" class="btn offset5" value="1" name="newAdmin">Создать нового пользователя</button>
	<button type="submit" class="btn btn-primary" value="1" name="saveAdmin">Сохранить</button>
</div>
</form>