	<table style="width:200mm;height:140mm;margin-left:10mm;margin-top:5mm;table-layout:fixed;">
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
						<TD style="width:35mm;border-bottom:1px solid #000000;text-align:center;font-size:10pt;line-height:10pt;"><?=$dnum;?></TD>
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
		<TD rowspan=3 style="width:65mm;vertical-align:middle;font-size:14pt;padding-top:-15mm">
			Начальнику управления<BR> информационных ресурсов<BR>и систем мэрии города<BR><BR>
			<SPAN style="font-size:14pt;font-weight:bold;">А.Ю. Глебову</SPAN><BR>
		</TD>
	</TR>
	</TABLE>


	<table style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;margin-left:10mm;margin-top:10mm;margin-bottom:20mm;">
		<tr>
			<td><div style="width:100%;text-align:center;font-weight:bold;font-size:14pt;">ЗАЯВКА</div></td>
		</tr>
	</table>



	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
	<TR>
		<TD style="width:50mm;"></TD>
		<TD style="width:65mm;"></TD>
		<TD style="width:17mm;"></TD>
		<TD style="width:35mm;"></TD>
	</TR>
	<TR>
		<TD colspan=4>Прошу пользователю локальной вычислительной сети мэрии:</TD>
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



	<TABLE style="width:185mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
	<TR>
		<TD style="width:175mm;">
			<?=$inetaction;?> <BR> <?=$mailaction;?> 
		</TD>
	</TR>
	</TABLE>


	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;margin-top:5mm;">
	<TR>
		<TD style="width:65mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$otv_dl;?>&nbsp;</TD>
		<TD style="width:10mm;">&nbsp;</TD>
		<TD style="width:50mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:5mm;">&nbsp;</TD>
		<TD style="width:40mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;"><?=$fio;?>&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Руководитель органа мэрии, МУП или У)</SUP></TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD>&nbsp;</TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
	</TABLE>



	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:14pt;border-collapse:collapse;margin-left:10mm">
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
	</TABLE>

	<div style="margin-left:10mm;margin-top:3mm;font-size:12pt;font-weight:bold;">Согласовано:</div>
	
	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm">
	<TR>
		<TD style="width:75mm;" rowspan=3>Начальник управления<br>информационных ресурсов и систем мэрии города</TD>
		<TD style="width:40mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
	</TR>
	<TR>
		<TD style="width:50mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">А.Ю. Глебов</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
	</TABLE>

	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm">
	<TR>
		<TD style="width:75mm;" rowspan=3>Администратор безопасности информации</TD>
		<TD style="width:50mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
	</TR>
	<TR>
		<TD style="width:50mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">Г.А. Титов</TD>
	</TR>
	<TR height=30>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
	</TABLE>

	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;">
	<TR>
		<TD style="width:175mm;"><b>Принятое решение: </b>Отделу сетевого администрирования управления информационных ресурсов и систем мэрии города <?=$decision;?>.</TD>
	</TR>
	</TABLE>

	<TABLE style="width:165mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:12pt;border-collapse:collapse;margin-left:10mm;margin-top:5mm">
	<TR>
		<TD style="width:75mm;" rowspan=3>Заместитель мэра города -<BR>руководитель аппарата</TD>
		<TD style="width:50mm;"></TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;"></TD>
	</TR>
	<TR>
		<TD style="width:50mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:5mm;"></TD>
		<TD style="width:40mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">В.С. Гармашов</TD>
	</TR>
	<TR height=30>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Подпись)</SUP></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(И.О. Фамилия)</SUP></TD>
	</TR>
	</TABLE>




	<TABLE style="width:175mm;padding:0mm;border-spacing:0mm;table-layout:fixed;font-size:14pt;border-collapse:collapse;margin-left:10mm;">
	<TR>
		<TD style="width:35mm;text-align:center;font-weight:bold;">&nbsp;</TD>
		<TD style="width:75mm;text-align:center;font-weight:bold;"></TD>
		<TD style="width:60mm;border-bottom:1px solid #000000;text-align:center;font-weight:bold;">&nbsp;</TD>
	</TR>
	<TR>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"></TD>
		<TD></TD>
		<TD style="text-align:center;font-size:10pt;vertical-align:top;"><SUP>(Отметка об исполнении заявки)</SUP></TD>
	</TR>
	</TABLE>
