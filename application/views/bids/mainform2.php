<link rel="stylesheet" href="/css/bidsui.css">
<h3 class="muted">Заявки на информационные ресурсы
	<button type = "button" 
		class    = "btn btn-warning pull-right"
		id       = "getHelp"
		title    = "Помощь">Помощь</button>
</h3>

<div id="breadCrumbs" class="acField hide">
	<a class="stageMarker muted" href="bids">Начало</a>
	<i class="icon-play"></i>
	<span class="stageMarker" id="stage2">Данные пользователя</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted" id="stage1">Поданные заявки</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted" id="stage3">Выбор информационных ресурсов</span>
	<i class="icon-play"></i>
	<span class="stageMarker muted">Готово!</span>
</div>

<!-- поиск пользователя -->
<div id="popID"
	class="control-group acField hide"
	rel="popover"
	data-content="Введите часть Ф.И.О. в это поле, чтобы система помогла вам найти зарегистрированного пользователя.
	После этого выберите нужного пользователя в появившемся списке и либо дважды щёлкните его строчку меню, либо нажмите кнопу &quot;Показать&quot;"
	data-original-title="Поиск пользователя"
	data-trigger="manual">

	<div class="input-prepend input-append">
		<span class="add-on pre-label">Поиск</span>
		<input name="userid" ID="userid" maxlength="60"
			placeholder="Фамилия или логин / имя компьютера пользователя"
			type="text"
			value="<?= ($filter) ? iconv('UTF-8', 'Windows-1251', urldecode($filter)) : ''; ?>"
			title="Ограничьте поиск, введя буквы фамилии или сетевого имени компьютера">
		<span class="add-on post-label"><label><input type="checkbox" id="withFired" style="margin-top:3px"> Показать уволенных</label></span>
	</div>

	<select size=5 ID="userSelector" title="Список пользователей" class="long"></select>
	&nbsp;&nbsp;&nbsp;
	<button type="submit" id="searchUser" class="btn btn-large" title="Вывести информацию по выбранному пользователю">Показать</button>
</div>

<div id="userHint" class="alert alert-info acField hide">
	Выберите себя или нужного пользователя локальной сети муниципалитета.<br>Список можно сократить, введя начальные буквы фамилии.
</div>
<!-- поиск пользователя -->

<h4 id="userHeader" class="acField hide">
	<span id="fioAcknowledger">Кто-то</span><br>
	<small id="depAcknowledger">откуда-то</small>
</h4>

<div id="orderContainer" class="acField hide">
	<table class="table table-striped table-hovered table-bordered" style="margin-left: 0px;width:784px;">
		<tbody>
			<tr>
				<th>Информационный ресурс</th>
				<th style="width:100px;">Дата подачи</th>
				<th style="width:110px;">Номер заявки</th>
				<th style="width:210px;">Статус</th>
				<th style="width:25px;text-align:center;vertical-align:middle;" title="Получить копию всех заявок">
					<label for="checkAllPapers" style="cursor:pointer;"><input type="checkbox" id="checkAllPapers"></label>
				</th>
			</tr>
		</tbody>
		<tbody id="orderList"></tbody>
	</table>
</div>

<!-- плашка данных пользователя -->
<div id="userdata" class="acField hide">
	<div id="popInfo" class="control-group" rel="popover"
		data-content="Проверьте актуальность данных. Если информация устарела или отсутствует - исправьте или заполните соответствующие поля формы."
		data-original-title="Данные о пользователе"
		data-trigger="manual">
		<form method="post" action="/bidsfactory/getpapers" id="mainform" class="form-horizontal" accept-charset="utf-8">
			<input type="hidden" name="login" id= "login" value="<?= $login; ?>">
			<input type="hidden" name="uid"   id= "uid"   value="">
			<input type="hidden" name="res"   id= "res"   value="">
			<input type="hidden" name="confs" id= "confs" value="">
			<input type="hidden" name="subs"  id= "subs"  value="">

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Фамилия</span>
				<input class="traceable fio_login"
					id="sname" 
					name="sname"
					maxlength="60"
					placeholder="Фамилия пользователя"
					form="mainform"
					type="text"
					value="<?=$name_f; ?>"
					valid="rword"
					pref="2"
					title="При вводе фамилии допустимы только прописные и строчные русские буквы.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Имя</span>
				<input class="traceable fio_login" 
					id="name" 
					name="name" 
					maxlength="60"
					placeholder="Имя пользователя"
					form="mainform"
					type="text"
					value="<?=$name_i;?>"
					valid="rword" 
					pref="2"
					title="При вводе имени допустимы только прописные и строчные русские буквы.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Отчество</span>
				<input class="traceable fio_login" id="fname" name="fname" maxlength="60"
					placeholder="Отчество пользователя"
					form="mainform"
					type="text"
					value="<?=$name_o;?>"
					valid="rword" pref="2"
					title="При вводе отчества допустимы только прописные и строчные русские буквы.">
					<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Подразделение</span>
				<select class="traceable long"
					valid="nonzero"
					form="mainform"
					id="dept"
					name="dept"
					title="Выберите подразделение из предлагаемого списка. Если подразделения нет в списке, его можно будет ввести в текст документа вручную.">
					<?=$dept;?>
				</select>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Должность</span>
				<select class="traceable long"
					valid="nonzero"
					form="mainform"
					id="staff"
					name="staff"
					title="Выберите должность из предлагаемого списка. Если должности нет в списке, её можно будет ввести в текст документа вручную.">
					<?=$staff;?>
				</select>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>

			<div class="input-prepend input-append control-group">
				<span class="add-on pre-label" style="margin-left:0px;">Адрес</span>
				<select name="office"
				id="office" 
				class="traceable short"
				valid="nonzero"
				form="mainform"
				title="Выберите здание">
					<?= $location[0]; ?>
				</select>
				<span class="add-on pre-label">Кабинет</span>
				<select name="office2" 
					id="office2" 
					title="Выберите кабинет"
					form="mainform"
					class="vshort">
					<?= $location[1]; ?>
				</select>
			</div>

			<div class="input-prepend control-group">
				<span class="add-on pre-label">Телефон</span>
				<input type="text" 
					class="traceable"
					valid="num"
					pref="6"
					id="phone"
					name="phone"
					value="<?=$phone;?>"
					form="mainform"
					title="Введите рабочий телефон, если есть.">
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>
			<input type="hidden" name="esiaMailAddr" id="f_esiaMailAddr">
		</form>
	</div>
</div>
<!-- плашка данных пользователя закончилась -->

<div id="userOKButtons" class="acField hide">
	<h4>Всё правильно?</h4>
	<span class="btn btn-large" id="userDataOK">Да!</span><span class="btn btn-large" id="userDataFail">Нет</span><br><br>
	<div id="correctnessAnnot" class="alert alert-success hide">Отредактируйте приведённую выше информацию: выберите надлежащие пункты меню, исправьте иные несуразицы. Сделанные вами изменения будут учтены при выдаче заявки.</div>
</div>

<div id="copyButtons" class="acField hide">
	<h4>Что требуется получить?</h4>
		<span class="btn btn-large" id="regetOrder" style="margin-left:-30px;" disabled="disabled">Копию заявки</span>Получить копию ранее поданной заявки (отметьте нужные заявки галочками)<br>
		<span class="btn btn-large" id="newOrder" style="margin-left:-170px;">Новую заявку</span>Оформить заявку на ещё один информационный ресурс
	</dl>
</div>

<!-- описания ресурсов -->
<div id="resdata" class="acField hide" style="margin-left:0px;">
	<div id="popIR" class="control-group" rel="popover"
	data-content="В это поле введите название информационного ресурса. Специальными метками будут показаны разделы, в которых находятся совпадения."
	data-original-title="Поиск ИР по названию"
	data-trigger="manual">
		<div class="input-prepend">
			<span class="add-on pre-label">Поиск по названию:</span>
			<input id="searchIR" type="text" maxlength="60"
				title="Начните вводить название. В списке слева появятся указатели на подходящие ресурсы"
				placeholder="Введите название информационного ресурса">
		</div>
	</div>
	<div class="accordion pull-left" id="accordion" rel="popover"
				data-content="Список информационных ресурсов. Раскрывайте списки щелчком мыши и нажимайте кнопки нужных ресурсов"
				data-original-title="Выбирайте ИР здесь."
				data-placement="left"
				data-trigger="manual"
				style="margin-left: 0px;">

		<?=$this->bidsuimodel->getResourceAccordion($rlist, 11);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 10);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 12);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 13);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 1);?>
		<?//=$this->bidsuimodel->getResourceAccordion($rlist, 2);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 3);?>
		<?//=$this->bidsuimodel->getResourceAccordion($rlist, 4);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 9);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 5);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 6);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 7);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 8);?>
		<?=$this->bidsuimodel->getResourceAccordion($rlist, 0);?>
	</div>
	<ul id="selectedList" class="well well-small" >
		<center>
			<h4 class="muted" style="top:50%;bottom:50%">Выбранные информационные ресурсы<br><br>
				<small>Раскройте списки слева и щёлкайте по кнопкам ресурсов. Это добавит их в выбранные.<br>Щелчок по
					ресурсу в этом списке исключит ресурс из заявки.
				</small>
			</h4>
		</center>
	</ul>
	<button class="btn btn-block btn-primary" id="order" title="Всё будет хорошо!">Получить заявки</button>
</div>
<!--  -->

<!-- кнопки навигации -->

<div id="navButtons acField" class="well well-small pull-right hide">
	<!-- <button type="button"
			class="btn disabled pull-right<?= ($this->input->post("passedUID") || $this->input->post("userSelector")) ? "" : " hide"; ?>"
			id="regetOrder"
			title="Получить копию заявки"
			style="margin-bottom:40px;">Получить копии выбранных заявок </button>-->
	<button class="btn pull-left hide" id="back" title="Просмотреть/отредактировать данные пользователя"><i class="icon-backward"></i> К пользователю</button>
	<button class="btn pull-right btn-primary hide disabled" id="order" title="Всё будет хорошо!">Получить заявки</button>
	<button class="btn hide" id="toOrder" title="Показать список заявок"><i class="icon-backward"></i> К списку заявок</button>
	<button class="btn pull-right btn-primary" title="Перейти к выбору информационных ресурсов" id="forward">Далее<i class="icon-forward icon-white"></i></button>
	<button type="button" class="btn btn-primary span3 pull-right" id="putOrder" style="margin-bottom:100px;margin-left:10px;" title="Перейти к оформлению новых заявок">Оформить новую заявку</button>
	<button class="btn span3 pull-right<?= (!$this->input->post("passedUID") && !$this->input->post("userSelector")) ? " hide" : ""; ?>" id="reset" title="Нажать, если что-то пошло совсем не так">Начать заново</button>
</div>
<!-- кнопки навигации -->

<div id="startManual" class="alert alert-info alert-block hide" style="clear:both;">
	<span id="helpText">Помощь</span>
</div>

<table id="startScreen" class="acField" style="border-spacing:4px;border-collapse:separate;">
	<tr>
		<th colspan=2><h4 style="margin-top:80px;margin-bottom:20px;">Оформляете заявку в первый раз?</h4></th>
	</tr>
	<tr>
		<td style="vertical-align:top;border: 1px solid #D6D6D6">
			<span class="btn btn-large btn-info btn-block" id="newUser">Да,<br>будет новый пользователь сети</span>
			Будут добавлены заявки на доступ:<ol>
				<li>к локальной сети муниципалитета</li>
				<li>к справочно-правовым системам</li>
				<li>создан пользователь на файлообменном ресурсе</li>
			</ol><br>
		</td>
		<td style="vertical-align:top;border: 1px solid #D6D6D6">
			<span class="btn btn-large btn-info btn-block" id="oldUser">Нет,<br>надо добавить прав доступа</span>
			<ul>
				<li>Оформление существующим пользователям заявок на доступ к ресурсам локальной сети муниципалитета</li>
				<li>Отслеживание статусов заявок на информационные ресурсы</li>
				<li>Получение копий заявок</li>
			</ul>
		</td>
	</tr>
</table>

<div id="modalRes" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
	 aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel1">Выберите уровни доступа
			<small>щёлкая по ним</small>
		</h3>
	</div>
	<div class="modal-body">
		<div id="esiaMail">
			<h5>Адрес электронной почты для направления приглашения:</h5>
			<input type="text" id="esiaMailAddr" name="esiaMailAddr" class="short" valid="email" placeholder="Укажите адрес электронной почты">
			<span id="esiaMailAnnounce" class="hide" style="color:red">Укажите адрес электронной почты!</span><hr>
		</div>
		<div>
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader">
		</div>
		<div id="resCollection" class="hide"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" id="layerModalOk"
				title="Закончить выбор слоёв и вернуться к списку ресурсов">Готово
		</button>
	</div>
</div>

<div id="modalWF"  class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3"
	 aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel3">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label span3">Обоснование</label>
			<div class="controls">
				<textarea name="wf_reason" id="wf_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="wfModalOk">Готово</button>
	</div>
</div>

<div id="modalEmail" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel2">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label">Адрес почты</label>
			<div class="controls">
				<div class="input-append">
					<input type="text" id="email_addr" name="email_addr" form="mainform" maxlength="40" valid="email" pref="1">
					<span class="add-on">@arhcity.ru</span>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Обоснование</label>
			<div class="controls">
				<textarea name="email_reason" id="email_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="emailModalOk">Готово</button>
	</div>
</div>

<div id="modalInet"  class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body" style="text-align:center;">
		<div class="control-group">
			<label class="control-label pull-left">Обоснование</label>
			<div class="controls">
				<textarea name="inet_reason" id="inet_reason" form="mainform" rows="6" cols="8"></textarea>
				<i class="icon-ok hide"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="inetModalOk">Готово</button>
	</div>
</div>

<div id="modalPortal" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
		<h3 id="myModalLabel">Выберите раздел интернет-портала:</h3>
	</div>
	<div class="modal-body" id="portalListBody">
		Найти раздел <input type="text" id="portalSectionFilter" class="short" placeholder="Номер или название раздела"><br>
		<ul id="portalSectionList"></ul>
		Идут плановые работы по улучшению работы подачи заявок.<br>Сейчас просто нажмите кнопку <strong>"Готово"</strong>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="portalModalOk">Готово</button>
	</div>
</div>

<form class="hide" action="/bidsfactory/reget_orders" method="post" id="regetForm">
	<input type="hidden" name="resources" id="resources" value="">
</form>

<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">
<!--
	var locs = <?=$locs?>
//-->
</script>
<script type="text/javascript" src="/jscript/bidsmachine.js"></script>
