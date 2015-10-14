<TABLE style="width:200mm;height:140mm;margin-left:10mm;margin-top:5mm;table-layout:fixed;">
	<TR>
		<TD style="width:80mm;font-size:10pt;text-align:center;font-weight:bold;height:8mm;margin-bottom:7mm;">
			<IMG SRC="http://192.168.1.8/users-r2/images/gerbbw.png" style="width:53px;height:68px;border:none;margin-bottom:3mm;" alt=""><br>
			<?=$top_header;?>
		</TD>
	</TR>
	<TR>
		<TD style="width:80mm;font-size:11pt;text-align:center;font-weight:bold;height:16mm;margin-bottom:7mm;">
		<?=$org;?>
		</TD>
	</TR>
	<TR>
		<TD style="width:80mm;height:30mm;font-size:11pt;text-align:center;vertical-align:bottom;">
		<?=$cred;?><br><br>
		<TABLE style="width:80mm;height:10mm;padding:0mm;border-collapse:collapse;border-spacing:0mm;table-layout:fixed;">
			<TR>
				<TD style="width:8mm;border-bottom:1px solid #000000;text-align:center;font-size:10pt;line-height:10pt;">&nbsp;</TD>
				<TD style="width:27mm;border-bottom:1px solid #000000;text-align:center;font-size:10pt;line-height:10pt;">&nbsp;<?=$ddate;?></TD>
				<TD style="width:10mm;text-align:center;font-size:10pt;line-height:10pt;">№</TD>
				<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-size:10pt;line-height:10pt;">&nbsp;<?=$dnum;?></TD>
			</TR>
			<TR>
				<TD style="width:10mm;font-size:10pt;">на&nbsp;№</TD>
				<TD style="width:27mm;border-bottom:1px solid #000000;font-size:10pt;">&nbsp;</TD>
				<TD style="width:10mm;text-align:center;font-size:10pt;">от</TD>
				<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-size:10pt;">&nbsp;</TD>
			</TR>
		</TABLE>
		</TD>
		<TD rowspan=3 style="width:20mm;vertical-align:top;">&nbsp;</TD>
		<TD rowspan=3 style="width:65mm;vertical-align:top;font-size:14pt;padding-top:-15mm;">
			Начальнику управления<BR> информационных ресурсов <BR>и систем мэрии города<BR><BR>
			<SPAN style="font-size:14pt;font-weight:bold;">А.Ю. Глебову</SPAN>
		</TD>
	</TR>
</TABLE>

<TABLE style="width:175mm;margin-left:10mm;padding:0mm;margin-top:15mm;margin-bottom:10mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
<TR>
	<TD style="width:100%;text-align:center;font-weight:bold;font-size:14pt;">ЗАЯВКА</TD>
</TR>
</TABLE>

<TABLE style="width:175mm;margin-left:10mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
	<TR style="height:0mm;">
		<TD style="width:50mm;"></TD>
		<TD style="width:65mm;"></TD>
		<TD style="width:15mm;"></TD>
		<TD style="width:25mm;"></TD>
	</TR>
	<TR>
		<TD colspan=4>Прошу зарегистрировать в локальной вычислительной сети мэрии города пользователя:</TD>
	</TR>
	<TR>
		<TD colspan=4 style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;<?=implode(array($name_f, $name_i, $name_o), " ");?> <?=$staff;?></TD>
	</TR>
	<TR>
		<TD colspan=4 style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Фамилия, имя, отчество, должность сотрудника)</SUP></TD>
	</TR>
	<TR>
		<TD>идентификатор&nbsp;пользователя:</TD>
		<TD colspan=3 style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$login;?></TD>
	</TR>
	<TR>
		<TD></TD>
		<TD colspan=3 style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(латинскими буквами)</SUP></TD>
	</TR>
	<TR>
		<TD colspan=4>Автоматизированное рабочее место пользователя располагается по адресу:</TD>
	</TR>
	<TR>
		<TD colspan=2 style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$address;?>&nbsp;</TD>
		<TD>телефон:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$phone;?>&nbsp;</TD>
	</TR>
</TABLE>



<TABLE style="width:175mm;margin-left:10mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
	<TR style="height:0mm;">
		<TD style="width:60mm;"></TD>
		<TD style="width:95mm;"></TD>
		<TD style="width:3mm;"></TD>
		<TD style="width:3mm;"></TD>
		<TD style="width:8mm;"></TD>
	</TR>
	<TR>
		<TD colspan=5>Автоматизированное рабочее место пользователя оснащено:</TD>
	</TR>
	<TR>
		<TD>Аппаратный комплекс:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
	<TR>
		<TD></TD>
		<TD colspan=4 style="text-align:center;font-size:10pt;"><SUP>Pentium I – 200 MHz/ 32 Mb /HDD -  1 Mb / FDD  / Сетевая плата</SUP></TD>
	</TR>
	<TR>
		<TD>Монитор:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
	<TR>
		<TD>Клавиатура:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
	<TR>
		<TD>Манипулятор типа "Мышь":</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
	<TR>
		<TD>Модем:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
	<TR>
		<TD>Принтер:</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp; </TD>
		<TD></TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>шт.</TD>
	</TR>
</TABLE>

<TABLE style="width:175mm;margin-left:10mm;margin-top:5mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
	<TR style="height:0mm;">
		<TD style="width:68mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:50mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
	</TR>
	<TR>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$otv_dl;?>&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$fio;?>&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Pуководитель органа мэрии, МУП или У)</SUP></TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
</TABLE>

<TABLE style="width:155mm;margin-left:10mm;margin-top:5mm;margin-bottom:15mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
	<TR>
		<TD>Отделу сетевого администрирования предоставить доступ к локальной вычислительной<br> сети мэрии города.</TD>
	</TR>
</TABLE>

<TABLE style="width:175mm;margin-left:10mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;">
	<TR style="height:0mm;">
		<TD style="width:75mm;" rowspan=3>Начальник управления информационных ресурсов и систем мэрии города</TD>
		<TD style="width:50mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
	</TR>
	<TR>
		<TD style="width:45mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">А.Ю. Глебов</TD>
	</TR>
	<TR height=30>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
</TABLE>

<TABLE style="width:165mm;margin-left:10mm;margin-top:10mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:14pt;">
	<TR>
		<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:65mm;text-align:center;font-weight:bold;"></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Исполнитель)</SUP></TD>
		<TD></TD>
		<TD></TD>
	</TR>
	<TR>
		<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:75mm;text-align:center;font-weight:bold;"></TD>
		<TD style="width:63mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Телефон)</SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Отметка об исполнении заявки)</SUP></TD>
	</TR>
	<TR>
		<TD colspan=3 style="font-size:8pt;vertical-align:top;"><SUP><?=$zakaz;?></SUP></TD>
	</TR>
</TABLE>