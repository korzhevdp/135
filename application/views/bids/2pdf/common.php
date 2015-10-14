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

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;margin-top:10mm;">
<TR>
	<TD colspan=4 style="width:100%;text-align:center;font-weight:bold;font-size:14pt;">ЗАЯВКА</TD>
</TR>
<TR>
	<TD colspan=4>Прошу пользователю локальной вычислительной сети мэрии города:</TD>
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
	<TD colspan=2 style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;width:65mm;"><?=$fulladdress;?>&nbsp;</TD>
	<TD style="width:15mm;text-align:center;">телефон:</TD>
	<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;width:25mm;"><?=$phone;?>&nbsp;</TD>
</TR>
</TABLE>

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm">
<TR>
	<TD>предоставить доступ к следующим информационным ресурсам, не содержащим секретной и конфиденциальной информации:</TD>
</TR>
</TABLE>


<TABLE style="width:175mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;border-spacing:0px;">
	<TR>
		<TD style="width:5mm;border:1px solid #000000;border-bottom:none;">&nbsp;</TD>
		<TD style="text-align:center;border:1px solid #000000;width:82.1mm;border-bottom:none"><B>Название ресурса</B></TD>
		<TD style="text-align:center;border:1px solid #000000;width:84mm;border-bottom:none;"><B>Требуемые права доступа к ресурсу</B></TD>
	</TR>
</TABLE>

<TABLE style="width:175mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;border-spacing:0px;">
	<TR>
		<TD style="border:1px solid #000000;width:5mm;border-top:none;border-bottom:none;">&nbsp;</TD>
		<TD style="border:1px solid #000000;text-align:center;padding:3px;width:80mm;font-size:11pt;border-top:none;border-bottom:none;">&nbsp;</TD>
		<TD style="border:1px solid #000000;text-align:center;padding:3px;width:18mm;font-size:11pt;border-left:none;border-bottom:none;">администри<BR>рование</TD>
		<TD style="border:1px solid #000000;text-align:center;padding:3px;width:18mm;font-size:11pt;border-left:none;border-bottom:none;">полный доступ</TD>
		<TD style="border:1px solid #000000;text-align:center;padding:3px;width:18mm;font-size:11pt;border-left:none;border-bottom:none;">изменение</TD>
		<TD style="border:1px solid #000000;text-align:center;padding:3px;width:17mm;font-size:11pt;border-left:none;border-bottom:none;">чтение</TD>
	</TR>
</TABLE>

<?=$res_container;?>

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;margin-top:5mm;">
<TR>
	<TD style="width:75mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$otv_dl;?>&nbsp;</TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:40mm;text-align:center;font-size:10pt;vertical-align:top;border-bottom:1px solid #000000;">&nbsp;</TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:45mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$fio;?>&nbsp;</TD>
</TR>
<TR>
	<TD style="width:75mm;text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Ответственное должностное лицо)</SUP></TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:40mm;text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:45mm;text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
</TR>
</TABLE>

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
<TR>
	<TD style="width:40mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:28mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	<TD style="width:5mm;">&nbsp;</TD>
	<TD style="width:100mm;text-align:center;font-weight:bold;">&nbsp;</TD>
</TR>
<TR>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(исполнитель)</SUP></TD>
	<TD>&nbsp;</TD>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(телефон)</SUP></TD>
	<TD>&nbsp;</TD>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP></SUP></TD>
</TR>
</TABLE>

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
<TR height=0>
	<TD style="width:75mm;"></TD>
	<TD style="width:5mm;"></TD>
	<TD style="width:40mm;"></TD>
	<TD style="width:5mm;"></TD>
	<TD style="width:45mm;"></TD>
</TR>
<TR>
	<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$owner_staff?>&nbsp;</TD>
	<TD>&nbsp;</TD>
	<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	<TD>&nbsp;</TD>
	<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$r_owner;?>&nbsp;</TD>
</TR>
<TR>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(владелец информационного ресурса)</SUP></TD>
	<TD>&nbsp;</TD>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
	<TD>&nbsp;</TD>
	<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
</TR>
</TABLE>

<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
	<TR height=0 >
		<TD style="width:75mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:45mm;"></TD>
	</TR>
	<TR>
		<TD style="text-align:center;vertical-align:bottom;">Администратор безопасности</TD>
		<TD>&nbsp;</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD>&nbsp;</TD>
		<TD style="border-bottom:1px solid #000000;text-align:center;font-weight:bold;">Г.А. Титов</TD>
	</TR>
	<TR>
		<TD style="text-align:center;vertical-align:top;">информационных ресурсов</TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
</TABLE>


<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
	<TR>
		<TD style="width:35mm;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:70mm;text-align:center;font-weight:bold;"></TD>
		<TD style="width:67mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP></SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Отметка об исполнении заявки)</SUP></TD>
	</TR>
	<TR>
		<TD colspan=3 style="font-size:8pt;vertical-align:top;"><SUP><?=$zakaz;?></SUP></TD>
	</TR>
</TABLE>
