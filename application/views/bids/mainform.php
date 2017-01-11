<h2>Подача заявок на информационные ресурсы
	<button type="button" class="btn btn-warning badge-info pull-right" id="getHelp" 
			style="margin-bottom:100px;margin-left:10px;"
			title="Помощь">Помощь</button>
</h2><br>

<form method="post" action="/bids" id="userSForm">
	<div id="popID" class="control-group" rel="popover"
		 data-content="Введите Ф.И.О. в это поле, чтобы найти зарегистрированного пользователя или оставьте поле пустым для регистрации нового пользователя
		После этого выберите нужного пользователя в появившемся списке"
		 data-original-title="Поиск пользователя"
		 data-trigger="manual">
		<label class="control-label span1">Поиск</label>

		<div class="controls">
			<input class="span8" name="userid" ID="userid" maxlength="60"
				placeholder="Фамилия или логин / имя компьютера пользователя " type="text"
				value="<?= ($filter) ? iconv('UTF-8', 'Windows-1251', urldecode($filter)) : ''; ?>"
				title="Ограничьте поиск, введя буквы фамилии или сетевого имени">
			&nbsp;&nbsp;&nbsp;
			<span class="muted">Найдено <span id="foundCounter">00</span> записей</span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span1">&nbsp;</label>

		<div class="controls">
			<select size=5 class="span8" name="userSelector" ID="userSelector" title="Список пользователей"></select>
			&nbsp;&nbsp;&nbsp;
			<button type="submit" class="btn btn-primary btn-small"
					title="Вывести информацию по выбранному пользователю">Показать
			</button>
		</div>
	</div>
	<input type="hidden" id="passedUID" name="passedUID"
		   value=<?= ($this->input->post("passedUID")) ? $this->input->post("passedUID") : $this->input->post("userSelector"); ?>>
</form>

<hr>

<h3 style="border-bottom:1px solid #000099; padding-bottom:3px;margin-bottom:20px;"><span
		id="fioAcknowledger"><?= implode(array($name_f, $name_i, $name_o), " "); ?></span><br>
	<small id="depAcknowledger"></small>
</h3>

<div style="margin-bottom:20px;">
	<span class="stageMarker" id="stage1" style="margin-right:10px;">Поданные заявки</span>
	<i class="icon-play"></i>
	<span class="muted stageMarker" style="margin-left:10px;margin-right:10px;" id="stage2">Данные пользователя</span>
	<i class="icon-play"></i>
	<span class="muted stageMarker" style="margin-left:10px;margin-right:10px;" id="stage3">Выбор информационных ресурсов</span>
	<i class="icon-play"></i>
	<span class="muted" style="margin-left:10px;">Готово!</span>
</div>

<div id="orderList">
	<?= $ordersProcessed; ?>
</div>

<div id="userdata" class="hide" style="margin-bottom:10px;">
	<div class="control-group">
		<label class="control-label span2">Фамилия</label>

		<div class="controls">
			<input class="span9 traceable fio_login" id="sname" type="text" name="sname" value="<?= $name_f; ?>"
				maxlength="60" title="При вводе фамилии допустимы только прописные и строчные русские буквы."
				valid="rword" pref="2"><i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Имя</label>

		<div class="controls">
			<input class="span9 traceable fio_login" id="name" type="text" name="name" value="<?= $name_i; ?>"
				   maxlength="60" title="При вводе имени допустимы только прописные и строчные русские буквы."
				   valid="rword" pref="2"><i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Отчество</label>

		<div class="controls">
			<input class="span9 traceable fio_login" ID="fname" type="text" name="name_o" value="<?= $name_o; ?>"
				   maxlength="60" title="При вводе отчества допустимы только прописные и строчные русские буквы."
				   valid="rword" pref="3"><i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<div id="popInfo" class="control-group" rel="popover"
		 data-content="Проверьте правильность заполнения данных. Если информация устарела или отсутствует, исправьте и заполните соответствующие поля формы."
		 data-original-title="Данные о пользователе"
		 data-trigger="manual">
		<label class="control-label span2">Подразделение</label>

		<div class="controls">
			<select class="span9 traceable" valid="nonzero" id="dept" name="dept"
					title="Выберите требуемое подразделение из предлагаемого списка. Если подразделения нет в списке, его можно будет ввести позже в текст полученного документа.">
				<?= $dept; ?>
			</select><i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Должность</label>

		<div class="controls">
			<select class="span9 traceable" valid="nonzero" id="staff" name="staff"
					title="Выберите требуемую должность из предлагаемого списка. Если должности нет в списке, её можно будет ввести позже в текст полученного документа.">
				<?= $staff; ?>
			</select><i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label span2">Адрес</label>

		<div class="controls">
			<select name="office" id="office" class="span9 traceable" valid="nonzero" title="Выберите адрес размещения">
				<?= $location; ?>
			</select>
			<i class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>

	<input type="hidden" name="login" id="login" value="<?= $login ?>" maxlength="60">

	<div class="control-group">
		<label class="control-label span2">Телефон</label>

		<div class="controls">
			<input class="span9 traceable" id="phone" valid="num" pref="6" type="text" name="phone"
				value="<?= $phone ?>" maxlength="25" title="Введите рабочий телефон, если есть."><i
				class="icon-ok hide" style="margin-left:10px;"></i>
		</div>
	</div>
</div>

<script type="text/javascript" src="/jscript/jqueryui.js"></script>


<div id="resdata" class="span12 hide" style="margin-left:0px;">

	<div id="popIR" class="control-group" rel="popover"
	 data-content="В это поле введите название информационного ресурса. Специальными метками будут показаны разделы, в которых находятся совпадения."
	 data-original-title="Поиск ИР по названию"
	 data-trigger="manual">
	<label class="control-label span4" for="searchIR">Показать только содержащие:</label>

	<div class="controls">
		<input class="span8" id="searchIR" type="text" maxlength="50"
			title="Начните вводить название. В списке слева появятся указатели на подходящие ресурсы"
			placeholder="Поиск информационных ресурсов по названию">
	</div>
</div>

<hr>

<div class="accordion span6 pull-left" id="accordion" rel="popover"
			data-content="Список информационных ресурсов. Раскрывайте списки щелчком мыши и нажимайте кнопки нужных ресурсов"
			data-original-title="Выбирайте ИР здесь."
			data-placement="left"
			data-trigger="manual"
			style="margin-left: 0px;">

	<div class="accordion-group" id="domain_data">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse1" href="#collapse1">
				Заявка на подключение к сети мэрии<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse1">0</span></a>
		</div>
		<div id="collapse1" ref="1" class="accordion-body collapse in">
			<div class="accordion-inner">
				<ul class="rlist" id="group11" style="margin:0px;">
					<?= implode($rlist[11], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse2"
			   href="#collapse2">
				Интернет и электронная почта<span class="badge badge-success hide" style="margin-left:3px;"
												id="badge-collapse2">0</span></a>
		</div>
		<div id="collapse2" ref="2" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist inet-email" id="group10" style="margin:0px;">
					<?= implode($rlist[10], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion12" referer="collapse12"
			   href="#collapse12">
				Беспроводная сеть Wi-Fi<span class="badge badge-success hide" style="margin-left:3px;"
											id="badge-collapse12">0</span></a>
		</div>
		<div id="collapse12" ref="12" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group12" style="margin:0px;">
					<?= implode($rlist[12], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse4"
			   href="#collapse4">
				1C: программные продукты<span class="badge badge-success hide" style="margin-left:3px;"
											id="badge-collapse4">0</span></a>
		</div>
		<div id="collapse4" ref="4" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group1" style="margin:0px;">
					<?= implode($rlist[1], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse5"
			   href="#collapse5">
				Сервера баз данных MS SQL<span class="badge badge-success hide" style="margin-left:3px;"
											id="badge-collapse5">0</span></a>
		</div>
		<div id="collapse5" ref="5" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group2" style="margin:0px;">
					<?= implode($rlist[2], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse6"
			   href="#collapse6">
				Регистрация обращений граждан<span class="badge badge-success hide" style="margin-left:3px;"
												id="badge-collapse6">0</span></a>
		</div>
		<div id="collapse6" ref="6" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group3" style="margin:0px;">
					<?= implode($rlist[3], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group hide">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse7"
			   href="#collapse7">
				Справочно-Правовые Системы<span class="badge badge-success hide" style="margin-left:3px;"
												id="badge-collapse7">0</span></a>
		</div>
		<div id="collapse7" ref="7" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group4" style="margin:0px;">
					<?= (isset($rlist[4])) ? implode($rlist[4], "\n") : ""; ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse8"
			   href="#collapse8">
				УВСОП (Департамент здравоохранения)&nbsp;&nbsp;<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse8">0</span></a>
		</div>
		<div id="collapse8" ref="8" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group5" style="margin:0px;">
					<?= implode($rlist[5], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse9"
			   href="#collapse9">
				Автоматизированные информационные системы (ГИС)<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse9">0</span></a>
		</div>
		<div id="collapse9" ref="9" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group6" style="margin:0px;">
					<?= implode($rlist[6], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse10"
			   href="#collapse10">
				Департамент муниципального имущества<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse10">0</span></a>
		</div>
		<div id="collapse10" ref="10" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group7" style="margin:0px;">
					<?= implode($rlist[7], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse11"
			   href="#collapse11">
				Департамент образования<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse11">0</span></a>
		</div>
		<div id="collapse11" ref="11" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group8" style="margin:0px;">
					<?= implode($rlist[8], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" referer="collapse3" href="#collapse3">
				Прочие<span class="badge badge-success hide" style="margin-left:3px;" id="badge-collapse3">0</span></a>
		</div>
		<div id="collapse3" ref="3" class="accordion-body collapse">
			<div class="accordion-inner">
				<ul class="rlist" id="group0" style="margin:0px;">
					<?= implode($rlist[0], "\n"); ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<ul id="selectedList" class="span6 pull-right well well-small" style="min-height:200px;">
	<center>
		<h4 class="muted" style="top:50%;bottom:50%">Выбранные информационные ресурсы<br><br>
			<small>Раскройте списки слева и щёлкайте по кнопкам ресурсов. Это добавит их в выбранные. Щелчок по
				ресурсу в правом списке вернёт ресурс в список невыбранных.
			</small>
		</h4>
	</center>
</ul>

</div>

<button type="button"
		class="btn disabled pull-right<?= ($this->input->post("passedUID") || $this->input->post("userSelector")) ? "" : " hide"; ?>"
		id="regetOrder" title="Получить копию заявки" style="margin-bottom:40px;">Получить копии выбранных заявок
</button>

<div id="navButtons" class="span6 row-fluid well well-small pull-right hide"
	 style="vertical-align:middle;margin-top:20px;margin-bottom:40px;">
	<button class="btn span5 pull-left hide" id="back" title="Просмотреть/отредактировать данные пользователя"><i
			class="icon-backward"></i> К пользователю
	</button>
	<button class="btn span7 pull-right btn-primary hide disabled" id="order" title="Всё будет хорошо!">Получить
		заявки
	</button>
	<button class="btn span5 hide" id="toOrder" title="Показать список заявок"><i class="icon-backward"></i> К списку
		заявок
	</button>
	<button class="btn span5 pull-right btn-primary" title="Перейти к выбору информационных ресурсов" id="forward">Далее
		<i class="icon-forward icon-white"></i></button>
	<input type="hidden" id="navPage" value="1">
</div>

<div id="startManual" class="alert alert-info alert-block hide" style="clear:both;">
	<span id="helpText">Помощь</span>
</div>

<div style="clear:both;margin-bottom:100px;">
	<button type="button" class="btn btn-primary span3 pull-right" id="putOrder"
			style="margin-bottom:100px;margin-left:10px;" title="Перейти к оформлению новых заявок">Оформить новую
		заявку
	</button>
	<button
		class="btn span3 pull-right<?= (!$this->input->post("passedUID") && !$this->input->post("userSelector")) ? " hide" : ""; ?>"
		id="reset" title="Нажать, если что-то пошло совсем не так">Начать заново
	</button>
</div>

<form method="post" action="/bids/getpapers" id="mainform" class="form-horizontal">
	<input type="hidden" name="name_f" id="f_name_f" value="">
	<input type="hidden" name="name_i" id="f_name_i" value="">
	<input type="hidden" name="name_o" id="f_name_o" value="">
	<input type="hidden" name="addr1" id="f_addr1" value="">
	<input type="hidden" name="addr2" id="f_addr2" value="">
	<input type="hidden" name="esiaMailAddr" id="f_esiaMailAddr" value="">
	<input type="hidden" name="staff_id" id="f_staff" value="">
	<input type="hidden" name="dept" id="f_dept" value="">
	<input type="hidden" name="phone" id="f_phone" value="">
	<input type="hidden" name="login" id="f_login" value="<?= $login; ?>">
	<input type="hidden" name="confs" id="f_confs" value="">
	<input type="hidden" name="res" id="f_res" value="">
	<input type="hidden" name="subs" id="f_subs" value="">
	<input type="hidden" name="uid" id="f_uid" value="0">
	<input type="hidden" name="inet_reason" id="f_inet_reason" value="">
	<input type="hidden" name="wf_reason" id="f_wf_reason" value="">
	<input type="hidden" name="email_reason" id="f_email_reason" value="">
	<input type="hidden" name="email_addr" id="f_email_addr" value="">

</form>

<div id="modalRes" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
	 aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel1">Выберите уровни доступа
			<small>щёлкая по ним</small>
		</h3>
	</div>
	<div class="modal-body">
		<div id="esiaMail">
			Адрес электронной почты для приглашения <input type="text" id="esiaMailAddr" name="esiaMailAddr" placeholder="Пока не работает :)">
		</div>
		
		<div class="span12">
			<img id="gifLoader" src="/images/ajax-loader.gif" width="54" height="55" border="0" alt="loader">
		</div>
		<div id="resCollection" class="span12 hide"></div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true" title="Отказаться от выбора">Отмена</button>
		<button class="btn btn-primary" aria-hidden="true" id="layerModalOk"
				title="Закончить выбор слоёв и вернуться к списку ресурсов">Готово
		</button>
	</div>
</div>

<div id="modalWF" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3"
	 aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel3">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label span3">Обоснование</label>

			<div class="controls span8 container">
				<textarea name="wf_reason" id="wf_reason" rows="6" cols="8" style="width:100%"></textarea>
				<i class="icon-ok hide" style="margin-left:10px;"></i>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="wfModalOk">Готово</button>
	</div>
</div>

<div id="modalEmail" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
	 aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel2">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body" style="text-align:center;">
		<form method="post" action="" class="form form-horizontal span12 container">
			<div class="control-group">
				<label class="control-label span3">Адрес почты</label>

				<div class="controls span8">
					<div class="input-append span12 container" style="margin-left:0px;">
						<input type="text" class="span10" id="email_addr" maxlength="40" valid="email" pref="1"
							   style="margin-left:0px;">
						<span class="add-on">@arhcity.ru</span>
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label span3">Обоснование</label>

				<div class="controls span8 container">
					<textarea name="email_reason" id="email_reason" rows="6" cols="8" style="width:100%"></textarea>
					<i class="icon-ok hide" style="margin-left:10px;"></i>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="emailModalOk">Готово</button>
	</div>
</div>

<div id="modalInet" class="modalRes modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	 aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Заполните обязательные поля:</h3>
	</div>
	<div class="modal-body" style="text-align:center;">
		<form method="post" action="" class="form form-horizontal span12 container">
			<div class="control-group">
				<label class="control-label span3">Обоснование</label>

				<div class="controls span8">
					<textarea name="inet_reason" id="inet_reason" rows="6" cols="8" style="width:100%"></textarea>
					<i class="icon-ok hide" style="margin-left:10px;"></i>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
		<button class="btn btn-primary" id="inetModalOk">Готово</button>
	</div>
</div>


<script type="text/javascript" src="/jscript/users.js"></script>
<script type="text/javascript">
<!--

res = [];
subs = [];
confs = [];

$('.modalRes .modalEmail .modalInet').modal({
	show: 0
});

// #passedUID - индекс выбранного пользователя ( 0 - не выбран )

function checkPP() {
	var uid = $("#passedUID").val();
	if (uid != 0) {
		$("#depAcknowledger").html($("#dept :selected").html());
		$("#startManual").addClass("hide");
	}
}

// #navPage - виртуальный номер страницы ( 1 - Данные пользователя, 2 - Выбор информационных ресурсов )

var state      = 0,
	allowedRes = {
		'81' : [ 100, 101, 102 ]
	}



function displayCurrentPage() {
	var page = parseInt($("#navPage").val());
	switch (page) {
		case 1:
			$("#back").addClass("hide");
			$("#resdata").addClass("hide");
			$("#order").addClass("hide");
			$("#reset").addClass("hide");
			$("#reset").removeClass("hide");
			$("#forward").removeClass("hide");
			$("#userdata").removeClass("hide");
			$("#toOrder").removeClass("hide");
			break;
		case 2:
			$("#userdata").addClass("hide");
			$("#forward").addClass("hide");
			$("#back").removeClass("hide");
			$("#resdata").removeClass("hide");
			$("#order").removeClass("hide");
			$("#toOrder").addClass("hide");
			break;
	}
}

checkPP();

$("#forward").click(function () {
	var page = parseInt($("#navPage").val());
	$("#navPage").val(++page);
	checkPP();
	displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage3").removeClass("muted");
	$('#popInfo').popover('hide');
	state = 2;
	showHelp($("#getHelp").hasClass('active'));
});

$("#reset").click(function () {
	$("#passedUID").val(0);
	$("#userSelector").val(0);
	$("#userSForm").submit();

});

$("#order").click(function () {
	if ($(this).hasClass("disabled")) {
		return false;
	}
	if ($("#sname").val().length < 3 || $("#name").val().length < 2 || $("#fname").val().length < 3) {
		var page = parseInt($("#navPage").val());
		$("#navPage").val(--page);
		checkPP();
		displayCurrentPage();
		alert("Проверьте правильность введённых имени, фамилии и отчества пользователя");
		return false;
	}

	if ($("#dept").val() == "0") {
		var page = parseInt($("#navPage").val());
		$("#navPage").val(--page);
		checkPP();
		displayCurrentPage();
		alert("Выберите подразделение");
		return false;
	}

	if ($("#staff").val() == "0") {
		var page = parseInt($("#navPage").val());
		$("#navPage").val(--page);
		checkPP();
		displayCurrentPage();
		alert("Выберите должность");
		return false;
	}

	var lsubs = [],
		res = [],
		confs = [];

	$("#selectedList").find("li").each(function () {
		if ($(this).attr('conf') == 0) {
			res.push($(this).attr("id").split("_")[1]);
		} else {
			confs.push($(this).attr("id").split("_")[1]);
		}
	});
	if (!res.length && !confs.length) {
		alert("Информационные ресурсы не выбраны");
		return false;
	}
	for (a in subs) {
		lsubs.push(subs[a].join(","));
	}
	if ($("#userSelector").val() != "") {
		$("#f_name_f").val($("#sname").val());
		$("#f_name_i").val($("#name").val());
		$("#f_name_o").val($("#fname").val());
		$("#f_staff").val($("#staff").val());
		$("#f_addr1").val($("#office").val());
		$("#f_addr2").val($("#office2").val());
		$("#f_esiaMailAddr").val($("#esiaMailAddr").val());
		$("#f_dept").val($("#dept").val());
		$("#f_phone").val($("#phone").val());
		$("#f_confs").val(confs.join(","));
		$("#f_res").val(res.join(","));
		$("#f_subs").val(lsubs.join(","));
		$("#f_uid").val($("#passedUID").val());
		$("#mainform").submit();
	} else {
		$("#f_uid").val($("#userSelector").val());
	}
});

$("#back").click(function () {
	var page = parseInt($("#navPage").val());
	$("#navPage").val(--page);
	checkPP();
	displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
	$('#popIR, #accordion').popover('hide');
	state = 1;
	showHelp($("#getHelp").hasClass('active'));
});

$("li.reslist").click(function () {
	if ($(this).hasClass('disabled')) {
		return false;
	}
	($(this).parent().attr("id") == 'selectedList') ? removeFromList(this) : addToList(this);
	if ($("#selectedList li").size() == 0) {
		$("#order").addClass("disabled");
	}
});

function addToList(a) {
	if (parseInt($(a).attr('subs')) > 0) {
		$('#modalRes').modal('show');
		intid       = $(a).attr('id').split('_')[1];
		subs[intid] = [];
		$.ajax({
			url      : "/bids/get_subproperties/" + intid,
			type     : "POST",
			dataType : "html",
			success  : function (data) {
				$("#gifLoader").addClass('hide');
				$("#resCollection").html(data).removeClass('hide');
				$(".subspad").unbind().click(function () {
					($(this).hasClass("btn-success")) ? $(this).removeClass("btn-success") : $(this).addClass("btn-success");
					subs[intid] = [];
					$(".subspad").each(function () {
						if ($(this).hasClass("btn-success")) {
							subs[intid].push($(this).attr("ref"));
						}
					});
				});
				$("#layerModalOk").click(function () {
					if (subs[intid].length == 0) {
						$('#modalRes').modal('hide');
						return false;
					} else {
						$("#order").removeClass("disabled");
						$(a).appendTo('#selectedList');
						$(a).attr('title', 'Двойной щелчок переместит ресурс обратно');
					}
					$('#modalRes').modal('hide');
				});
			},
			error: function (data, stat, err) {
				$("#consoleContent").html([data, stat, err].join("<br>"));
			}
		});
		return false;
	}
	if ($(a).parent().hasClass("inet-email")) {
		if ($(a).attr('id').split('_')[1] == 101) {
			$('#modalInet').modal('show');
			$("#inetModalOk").click(function () {
				if ($("#inet_reason").val().length < 10) {
					alert("Обоснование необходимости подключения к сети Интернет слишком короткое.");
					return false;
				}
				$("#f_inet_reason").val($("#inet_reason").val());
				$('#modalInet').modal('hide');
				$(a).appendTo('#selectedList');
				$(a).attr('title', 'Двойной щелчок переместит ресурс обратно');
				$("#order").removeClass("disabled");
			});
		}
		if ($(a).attr('id').split('_')[1] == 100) {
			$('#modalEmail').modal('show');
			$("#emailModalOk").click(function () {
				if ($("#email_addr").val().length < 1) {
					alert("Укажите адрес электронной почты.");
					return false;
				}
				if ($("#email_reason").val().length < 10) {
					alert("Обоснование необходимости подключения электронной почты слишком короткое.");
					return false;
				}
				$("#f_email_addr").val($("#email_addr").val());
				$("#f_email_reason").val($("#email_reason").val());
				$('#modalEmail').modal('hide');
				$(a).appendTo('#selectedList');
				$(a).attr('title', 'Двойной щелчок переместит ресурс обратно');
				$("#order").removeClass("disabled");
			});
		}
		return false;
	}
	if ($(a).attr('id').split('_')[1] == 274) {
		$('#modalWF').modal('show');
		$("#wfModalOk").click(function () {
			if ($("#wf_reason").val().length < 10) {
				alert("Обоснование необходимости подключения к Интернет-ресурсам средствами беспроводной сети слишком короткое");
				return false;
			}
			$("#f_wf_reason").val($("#wf_reason").val());
			$('#modalWF').modal('hide');
			$("#order").removeClass("disabled");
			$(a).appendTo('#selectedList');
			$(a).attr('title', 'Двойной щелчок переместит ресурс обратно');
		});
		return false;
	}

	$(a).appendTo('#selectedList');
	$("#order").removeClass("disabled");
	$(a).attr('title', 'Двойной щелчок переместит ресурс обратно');
}

function removeFromList(a) {
	$(a).appendTo('#group' + $(a).attr('grp'));
	$(a).attr('title', 'Двойной щелчок добавит ресурс в список заявок');
}

$(".fio_login").keyup(function () {
	if ($("#fname").val().length > 0 && $("#name").val().length > 0 && $("#sname").val().length > 0) {
		$("#f_login").val(recode_field());
	}
})

$('.traceable').bind('change keyup', function () {
	var pref = parseInt($(this).attr('pref')),
		reg = $(this).attr('valid'),
		length = $(this).val().length,
		val = validate(reg, $(this).val());
	if (!length || !val) {
		$(this).parent().parent().removeClass('success').removeClass('warning').addClass('error');
		//$(this).siblings().last().addClass("icon-cancel").removeClass("icon-ok").removeClass("hide");
		//alert($(this).siblings().last().attr("id"));
	} else {
		if ($(this).val().length < pref) {
			$(this).parent().parent().removeClass('error').removeClass('success').addClass('warning');
			$(this).siblings().last().addClass("hide");
		} else {
			$(this).parent().parent().removeClass('error').removeClass('warning').addClass('success');
			$(this).siblings().last().removeClass("hide").addClass("icon-ok");
		}
	}
});

function validate(dt, val) {
	var r;
	//if (dt == 'email'){r = '^.+@[^\.].*\.[a-z]{2,}$';}
	if (dt == 'email') {
		r = '[^a-z\\.\\-0-9_]';
	}
	if (dt == 'text') {
		r = '[^a-z \\-"]';
	}
	if (dt == 'entext') {
		r = '[^0-9a-z \-"]';
	}
	if (dt == 'rtext') {
		r = '[^а-яёЁ\\-\\.\\, ]';
	}
	if (dt == 'rword') {
		r = '[^а-яёЁ ]';
	}
	if (dt == 'nonzero') {
		r = '^[0]$';
	}
	if (dt == 'date') {
		r = '[^0-9\\.]';
	}
	if (dt == 'weight') {
		r = '[^0-9 xхсмткг\\.]';
	}
	if (dt == 'num') {
		r = '[^0-9\\- ]';
	}
	if (dt == 'mtext') {
		r = '[^0-9 a-zа-яёЁ\\.\\,\\-"]';
	}
	if (dt == 'reqnum') {
		r = '[^0-9 \/\\бн\-]';
	}
	//console_alert(r);
	var rgEx = new RegExp(r, 'i');
	var OK = rgEx.exec(val);
	if (OK) {
		return 0;
	} else {
		return 1;
	}
}

$("#searchIR").keyup(function () {
	var text = $("#searchIR").val();
	$(".badge").html(0);
	$(".reslist").removeClass("hide");

	if (!text.length) {
		$(".badge").addClass("hide");
		return;
	}

	$(".reslist").each(function () {
		if ($(this).html().toLowerCase().indexOf(text.toLowerCase()) + 1) {
			src = $(this).parent().parent().parent().attr("ref");
			$(this).removeClass("hide");
			srf = parseInt($("#badge-collapse" + src).html());
			srf++;
			$("#badge-collapse" + src).empty().html(srf++);
		} else {
			$(this).addClass("hide");
		}
	});

	$(".badge").each(function () {
		if (parseInt($(this).html()) > 0) {
			$(this).removeClass("hide");
		} else {
			$(this).addClass("hide");
		}
	})
	//alert("press");
});

if ($("#passedUID").val() > 0) {
	$("#domain_data").addClass("hide");
}

$("#formDisplayer").click(function () {
	$("#mainform").slideToggle();
});

$("#putOrder").click(function () {
	$("#userdata, #navButtons, #toOrder").removeClass("hide");
	$("#orderList, #putOrder, #regetOrder").addClass("hide");
	$(".stageMarker").addClass("muted");
	$("#stage2").removeClass("muted");
	$('#popID').popover('hide');
	state = 1;
	showHelp($("#getHelp").hasClass('active'));
});

$("#toOrder").click(function () {
	$("#navButtons, #resdata, #toOrder, #regetOrder").addClass("hide");
	$("#orderList, #putOrder, #regetOrder").removeClass("hide");
	$("#navPage").val(1);
	checkPP();
	displayCurrentPage();
	$("#userdata").addClass("hide"); // именно после displayCurrentPage();
	$(".stageMarker").addClass("muted");
	$("#stage1").removeClass("muted");
	$('#popInfo').popover('hide');
	state = 0;
	showHelp($("#getHelp").hasClass('active'));
});

$("#checkAllPapers").click(function () {
	if ($(this).is(":checked")) {
		$(".paperChecker").attr("checked", "checked");
		$("#regetOrder").removeClass("disabled").addClass("btn-primary");
		$("#putOrder").removeClass("btn-primary");
	} else {
		$(".paperChecker").removeAttr("checked");
		$("#regetOrder").addClass("disabled").removeClass("btn-primary");
		$("#putOrder").addClass("btn-primary");
	}
});

$(".paperChecker").click(function () {
	if ($(".paperChecker:checked").size() > 0) {
		$("#regetOrder").removeClass("disabled").addClass("btn-primary");
		$("#putOrder").removeClass("btn-primary");
	} else {
		$("#regetOrder").addClass("disabled").removeClass("btn-primary");
		$("#putOrder").addClass("btn-primary");
	}
	if ($(".paperChecker:checked").size() == $(".paperChecker").size()) {
		$("#checkAllPapers").attr("checked", "checked");
	} else {
		$("#checkAllPapers").removeAttr("checked");
	}
});


$("#sname, #name, #fname").keyup(function () {
	$("#fioAcknowledger").html([$("#sname").val(), $("#name").val(), $("#fname").val()].join(" "));
});

$("#dept").change(function () {
	$("#depAcknowledger").html($("#dept option:selected").html());
});

$("#regetOrder").click(function () {
	var ids = [];
	$(".paperChecker:checked").each(function () {
		ids.push(parseInt($(this).attr("ref")));
	});
	$('<form action="/bids/reget_orders" method="post" id="regetForm"><input type="hidden" name="name_f" id="r_name_f" value=""><input type="hidden" name="name_i" id="r_name_i" value=""><input type="hidden" name="name_o" id="r_name_o" value=""><input type="hidden" name="addr1" id="r_addr1" value=""><input type="hidden" name="addr2" id="r_addr2" value=""><input type="hidden" name="staff_id" id="r_staff" value=""><input type="hidden" name="dept" id="r_dept" value=""><input type="hidden" name="phone" id="r_phone" value=""><input type="hidden" name="login" id="r_login" value="<?=$login;?>"><input type="hidden" name="uid" id="r_uid" value="0"><input type="hidden" name="resources" value="' + ids.join(",") + '"></form>').appendTo('body');
	$("#r_name_f").val($("#sname").val());
	$("#r_name_i").val($("#name").val());
	$("#r_name_o").val($("#fname").val());
	$("#r_staff").val($("#staff").val());
	$("#r_addr1").val($("#office").val());
	$("#r_addr2").val($("#office2").val());
	$("#r_dept").val($("#dept").val());
	$("#r_phone").val($("#phone").val());
	$("#r_uid").val($("#passedUID").val());
	$("#regetForm").submit().remove();
});

// Реализация интерактивной помощи
$('#getHelp').click(function () {
	$(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
	showHelp($(this).hasClass('active'));
});

function showHelp(con) {
	if (con == true) {
		switch (state) {
			case 0:
				$('#popID').popover('show');
				break;
			case 1:
				$('#popInfo').popover('show');
				break;
			case 2:
				$('#popIR, #accordion').popover('show');
		}
	}
	else {
		$('#popID, #popInfo, #popIR, #accordion').popover('hide');
	}
}

/*
 function showAll() {
 $('div').removeClass('hide');
 }
 */
//-->
</script>
