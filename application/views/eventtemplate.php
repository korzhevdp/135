<td class="text">
	Пользователь: <a href="/admin/users/<?=$uid;?>" target="_blank"><?=$fio;?></a><br>
	Рекомендуемые мероприятия: <ul><?=implode($actions, "");?></ul>
</td>
<td class="more">
	Комментарий: <?=$comment;?> <br><small>Источник: заявка на ресурс "<?=$shortname?>" </small>
</td>