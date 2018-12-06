<?php
function requestLine() {
	print '
<div style="margin-bottom: 10px;"><a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '">Вернуться к темам форума</a></div>
<div class="topic">Обсуждаемый вопрос</div>    	    
<div class="topic">Автор</div>
<div class="topic">Добавлен</div>
<div style="clear: both;">&nbsp;</div>' . "\n";
}
function showRequest($topicID, $db) {
	if($db->dbQuery("SELECT request.id, request.title, request.poster, CONCAT(DATE_FORMAT(request.created, '%k\:%i\:%s\ %e\-%b\-%Y')) AS created FROM request, topic WHERE topic.id=$topicID and request.parent=$topicID")) {
		while ($row = mysql_fetch_assoc($db->dbResult)) {
			print '
<div class="line"><a href="' . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID . '&requestID=' . $row['id'] . '">' . $row['title'] . '</a></div>
<div class="line">' . $row['poster'] . '</div>
<div class="line">' . $row['created'] . '</div>';
			if(isset($_SESSION['admin'])) {
				print '<div class="line"><a href="' . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID . '&requestDel=' . $row['id'] . '">[ del ]</a></div>';
			}
print '<div style="clear: both;">&nbsp;</div>' . "\n";
		}
		return true;
	}
	else {
		return false;
	}
}
function showRequestBody($id, $db) {
	if($db->dbQuery("SELECT title, body FROM request WHERE id=$id LIMIT 1")) {
		$row = mysql_fetch_assoc($db->dbResult);
		print '<div style="margin-bottom: 2px;"><b style="color: navy;">Текущий заголовок вопроса: </b>' . $row['title'] . '<br /><b style="color: navy;">Текущий обсуждаемый вопрос: </b>' . $row['body'] . '</div>' . "\n";
		return true;
	}
	else {
		return false;
	}
}
function requestForm($id, $name) {
	print '
<form action="' . $_SERVER['PHP_SELF'] . '?topicID=' . $id . '" method="post">
<input type="hidden" name="poster" value="' . $name . '" />
<input type="hidden" name="requestParent" value="' . $id . '" />
<div><label for="requestTitle">Заголовок вопроса<sup style="font-size: large;">*</sup>:</label><input type="text" name="requestTitle" id="requestTitle" /></div>
<div><label for="requestBody">Обсуждаемый вопрос<sup style="font-size: large;">*</sup>:</label><textarea name="requestBody" id="requestBody" cols="20" rows="20"></textarea></div>
<div><input type="submit" name="requestSbmt" value="Создать новый вопрос" /></div>
</form>' . "\n";
}
function requestWrite($poster, $requestParent, $requestTitle, $requestBody, $db) {
	if(!str_empty($requestTitle) && !str_empty($requestBody)) {
		if(!$db->dbQuery("INSERT INTO request (parent, title, poster, created, body) VALUES (
	'" . $requestParent . "',
	'" . addslashes(htmlspecialchars($requestTitle)) . "',
	'" . addslashes(htmlspecialchars($poster)) . "',
	NOW(),
	'" . addslashes(htmlspecialchars($requestBody)) . "')")) {
		return false;
	}
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?topicID=' . $requestParent);
	exit();
}
function requestDel($topicID, $requestID, $db) {
	if(!$db->dbQuery("DELETE request, response FROM request, response WHERE 
	request.id=$requestID and response.parent=$requestID")) {
		return false;
	}
	// если нет ответов
	if(!$db->dbQuery("DELETE FROM request WHERE request.id=$requestID")) {
		return false;
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID);
	exit();
}
?>