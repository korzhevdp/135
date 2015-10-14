<!doctype html>
<html>
	<head>
		<title> Административная консоль</title>
		<meta http-equiv="content-type" content="text/html; charset=windows-1251">
		<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
		<link href="/css/frontstyle.css" rel="stylesheet" media="screen" type="text/css">
	</head>

	<body>
		<script src="/jscript/jquery.js" type="text/javascript"></script>

		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand span2" href="/"><!-- <img src="/images/1.png" width="24" height="24" border="0" alt=""> --> Консоль</a>
				</div>
			</div>
		</div>

		<ul class="nav nav-tabs span9" style="clear:both;">
			<li><a href="#tabr1" data-toggle="tab" class="active">Авторизация</a></li>
		</ul>

		<div class="tab-content span9" style="clear:both;">
			<div id="tabr1" class="tab-pane active">
				<h1 style="margin-bottom:24px;">Авторизуйтесь. <small>Мы ценим Ваше участие</small></h1>
				<form method=post action="/login">
					<label class="span2">Имя пользователя:</label>
					<input class="span6" type="text" name="name"><br>
					<label class="span2">Пароль:</label>
					<input class="span6" type="password" name="pass"><br>
					<button type="submit" class="btn btn-primary pull-right btn-large span4 offset2">Вход</button>
				</form>
			</div>
		</div>

		<div id="reg_errors">
			<?=$errorlist;?>
		</div>

		<div id="announcer"></div>
	</body>
</html>