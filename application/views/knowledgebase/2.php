<!DOCTYPE HTML >
<html>
 <head>
  <title>Таблица конверсии символов кириллицы в латиницу, принятая в ЛВСМ</title>
  <meta name="Generator" content="EditPlus">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
 </head>

 <body>

<h4>Таблица конверсии символов кириллицы в латиницу, принятая в ЛВСМ</h4>
<a class="btn btn-warning" href="#gen" title="Ниасилил, дайте большую красную кнопку">К генератору</a>
<p>Имена компьютеров должны включать себя фамилию и инициалы пользователя, набранную с учётом правил транслитерации букв, приведённых ниже:</p>

А – A<br>
Б – B<br>
В – V<br>
Г – G<br>
Д – D<br>
Е – E<br>
Ж – ZH<br>
З – Z<br>
И – I<br>
Й – Y<br>
К – K<br>
Л – L<br>
М – M<br>
Н – N<br>
О – O<br>
П – P<br>
Р – R<br>
С – S<br>
Т – T<br>
У – U<br>
Ф – F<br>
Х – H<br>
Ц – C<br>
Ч – CH<br>
Ш – SH<br>
Щ – SCH<br>
Ы – Y<br>
Э – E<br>
Ю – YU<br>
Я – YA<br><br>

<p>
Инициалы пользователя, состоящие из букв «Я», «Ю», «Ё», «Й» транслитерируются буквой «Y»; 
состоящие из букв «Ш», «Щ» транслитерируются буквой «S»; 
инициалы из буквы «Ж» транслитерируются буквой «J»; 
инициалы из буквы «Ч» транслитерируются буквой «С».
</p>
<p>Во избежание конфликтов сетевых имён, на время переноса файлов и настроек пользователей на другой компьютер, компьютер-источник переименовывается добавлением в конце имени “old”.</p>
<p>В особых случаях, когда есть необходимость работы в сети двух компьютеров одного пользователя (например, основной компьютер и ноутбук), первый компьютер должен иметь имя в формате «имя_пользователя» второй компьютер «имя_пользователя2» и т.д.</p>

<blockquote>* Ныне настоятельно рекомендуется не пользоваться суффиксами "old" и "new" для временного именования компьютеров - для удобства их машинной обработки. Впрочем, пожелание это почти никем не учитывается.</blockquote>
<hr>



<form method="post" action="" class="form-horizontal">
	<fieldset>
		<legend><a name="gen">Генератор имён</a></legend>

		<div class="control-group" style="margin-bottom:4px;">
			<label class="control-label" for="sname">Фамилия</label>
			<div class="controls">
				<input type="text" id="sname" placeholder="Фамилия" value="">
			</div>
		</div>

		<div class="control-group" style="margin-bottom:4px;">
			<label class="control-label" for="name">Имя</label>
			<div class="controls">
				<input type="text" id="name" placeholder="Имя" value="">
			</div>
		</div>

		<div class="control-group" style="margin-bottom:20px;">
			<label class="control-label" for="fname">Отчество</label>
			<div class="controls">
				<input type="text" id="fname" placeholder="Отчество" value="">
			</div>
		</div>
	</fieldset>
</form>
<button class="btn btn-large btn-danger offset2" id="initGen">Сгенерировать имя</button>
<div class="well well-small" style="margin-bottom:5px;margin-top:15px;height:60px;vertical-align:middle;">
	<h2 id="f_login"></h2>
</div>

<script type="text/javascript">
<!--
	$("#initGen").click( function(){
		recode();
	});

	function recode(){
		var sname = $('#sname').val().toLowerCase(),
		name = $('#name').val().toLowerCase(),
		fname = $('#fname').val().toLowerCase(),
		output = "";
		sname_conv = [],
		fname_conv = [],
		name_conv =[],
		r = {
		'_' : '',
		'-' : '',
		' ' : '',
		'а' : 'a',
		'б' : 'b',
		'в' : 'v',
		'г' : 'g',
		'д' : 'd',
		'е' : 'e',
		'ё' : 'e',
		'ж' : 'zh',
		'з' : 'z',
		'и' : 'i',
		'й' : 'y',
		'к' : 'k',
		'л' : 'l',
		'м' : 'm',
		'н' : 'n',
		'о' : 'o',
		'п' : 'p',
		'р' : 'r',
		'с' : 's',
		'т' : 't',
		'у' : 'u',
		'ф' : 'f',
		'х' : 'h',
		'ц' : 'c',
		'ч' : 'ch',
		'ш' : 'sh',
		'щ' : 'sch',
		'ъ' : '',
		'ы' : 'y',
		'ь' : '',
		'э' : 'e',
		'ю' : 'yu',
		'я' : 'ya',
		' ' : ' '
	},
	sr = {
		'а' : 'A',
		'б' : 'B',
		'в' : 'V',
		'г' : 'G',
		'д' : 'D',
		'е' : 'E',
		'ё' : 'E',
		'ж' : 'J',
		'з' : 'Z',
		'и' : 'I',
		'й' : 'Y',
		'к' : 'K',
		'л' : 'L',
		'м' : 'M',
		'н' : 'N',
		'о' : 'O',
		'п' : 'P',
		'р' : 'R',
		'с' : 'S',
		'т' : 'T',
		'у' : 'U',
		'ф' : 'F',
		'х' : 'H',
		'ц' : 'C',
		'ч' : 'C',
		'ш' : 'S',
		'щ' : 'S',
		'ъ' : '',
		'ы' : 'Y',
		'ь' : '',
		'э' : 'E',
		'ю' : 'Y',
		'я' : 'Y'
	}
	if (sname.length && name.length && fname.length){
		for (i = 1; i < sname.length; ++i){
			sname_conv.push(r[sname.charAt(i)]);
		}

		fname_conv.push(sr[fname.charAt(0)]);
		name_conv.push(sr[name.charAt(0)]);

		$("#f_login").html( [ 
			r[sname.charAt(0)].charAt(0).toUpperCase(),
			r[sname.charAt(0)].substr(1),
			sname_conv.join(''),
			name_conv.join(''),
			fname_conv.join('')
		].join('') );
	}else{
		$("#f_login").html("Введите Фамилию, Имя и Отчество");
	}
}
//-->
</script>
 </body>
</html>
