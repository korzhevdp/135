<!DOCTYPE HTML >
<html>
 <head>
  <title>������� ��������� �������� ��������� � ��������, �������� � ����</title>
  <meta name="Generator" content="EditPlus">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
 </head>

 <body>

<h4>������� ��������� �������� ��������� � ��������, �������� � ����</h4>
<a class="btn btn-warning" href="#gen" title="��������, ����� ������� ������� ������">� ����������</a>
<p>����� ����������� ������ �������� ���� ������� � �������� ������������, ��������� � ������ ������ �������������� ����, ���������� ����:</p>

� � A<br>
� � B<br>
� � V<br>
� � G<br>
� � D<br>
� � E<br>
� � ZH<br>
� � Z<br>
� � I<br>
� � Y<br>
� � K<br>
� � L<br>
� � M<br>
� � N<br>
� � O<br>
� � P<br>
� � R<br>
� � S<br>
� � T<br>
� � U<br>
� � F<br>
� � H<br>
� � C<br>
� � CH<br>
� � SH<br>
� � SCH<br>
� � Y<br>
� � E<br>
� � YU<br>
� � YA<br><br>

<p>
�������� ������������, ��������� �� ���� �߻, �޻, ���, �ɻ ����������������� ������ �Y�; 
��������� �� ���� �ػ, �ٻ ����������������� ������ �S�; 
�������� �� ����� �ƻ ����������������� ������ �J�; 
�������� �� ����� �׻ ����������������� ������ �ѻ.
</p>
<p>�� ��������� ���������� ������� ���, �� ����� �������� ������ � �������� ������������� �� ������ ���������, ���������-�������� ����������������� ����������� � ����� ����� �old�.</p>
<p>� ������ �������, ����� ���� ������������� ������ � ���� ���� ����������� ������ ������������ (��������, �������� ��������� � �������), ������ ��������� ������ ����� ��� � ������� ����_������������� ������ ��������� ����_������������2� � �.�.</p>

<blockquote>* ���� ������������ ������������� �� ������������ ���������� "old" � "new" ��� ���������� ���������� ����������� - ��� �������� �� �������� ���������. �������, ��������� ��� ����� ����� �� �����������.</blockquote>
<hr>



<form method="post" action="" class="form-horizontal">
	<fieldset>
		<legend><a name="gen">��������� ���</a></legend>

		<div class="control-group" style="margin-bottom:4px;">
			<label class="control-label" for="sname">�������</label>
			<div class="controls">
				<input type="text" id="sname" placeholder="�������" value="">
			</div>
		</div>

		<div class="control-group" style="margin-bottom:4px;">
			<label class="control-label" for="name">���</label>
			<div class="controls">
				<input type="text" id="name" placeholder="���" value="">
			</div>
		</div>

		<div class="control-group" style="margin-bottom:20px;">
			<label class="control-label" for="fname">��������</label>
			<div class="controls">
				<input type="text" id="fname" placeholder="��������" value="">
			</div>
		</div>
	</fieldset>
</form>
<button class="btn btn-large btn-danger offset2" id="initGen">������������� ���</button>
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
		'�' : 'a',
		'�' : 'b',
		'�' : 'v',
		'�' : 'g',
		'�' : 'd',
		'�' : 'e',
		'�' : 'e',
		'�' : 'zh',
		'�' : 'z',
		'�' : 'i',
		'�' : 'y',
		'�' : 'k',
		'�' : 'l',
		'�' : 'm',
		'�' : 'n',
		'�' : 'o',
		'�' : 'p',
		'�' : 'r',
		'�' : 's',
		'�' : 't',
		'�' : 'u',
		'�' : 'f',
		'�' : 'h',
		'�' : 'c',
		'�' : 'ch',
		'�' : 'sh',
		'�' : 'sch',
		'�' : '',
		'�' : 'y',
		'�' : '',
		'�' : 'e',
		'�' : 'yu',
		'�' : 'ya',
		' ' : ' '
	},
	sr = {
		'�' : 'A',
		'�' : 'B',
		'�' : 'V',
		'�' : 'G',
		'�' : 'D',
		'�' : 'E',
		'�' : 'E',
		'�' : 'J',
		'�' : 'Z',
		'�' : 'I',
		'�' : 'Y',
		'�' : 'K',
		'�' : 'L',
		'�' : 'M',
		'�' : 'N',
		'�' : 'O',
		'�' : 'P',
		'�' : 'R',
		'�' : 'S',
		'�' : 'T',
		'�' : 'U',
		'�' : 'F',
		'�' : 'H',
		'�' : 'C',
		'�' : 'C',
		'�' : 'S',
		'�' : 'S',
		'�' : '',
		'�' : 'Y',
		'�' : '',
		'�' : 'E',
		'�' : 'Y',
		'�' : 'Y'
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
		$("#f_login").html("������� �������, ��� � ��������");
	}
}
//-->
</script>
 </body>
</html>
