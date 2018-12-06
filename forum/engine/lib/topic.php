<?php
function topicLine() {
	print '
<div class="topic">Тема</div>    	    
<div class="topic">Автор</div>
<div class="topic">Добавлена</div>
<div style="clear: both;">&nbsp;</div>' . "\n";
}
function showTopics($db) {
	if($db->dbQuery("SELECT id, title, poster, CONCAT(DATE_FORMAT(created, '%k\:%i\:%s\ %e\-%b\-%Y')) AS created FROM topic")) {
		while ($row = mysql_fetch_assoc($db->dbResult)) {
			print '
<div class="line"><a href="' . $_SERVER['PHP_SELF'] . '?topicID=' . $row['id'] . '">' . $row['title'] . '</a></div>
<div class="line">' . $row['poster'] . '</div>
<div class="line">' . $row['created'] . '</div>';
			if(isset($_SESSION['admin'])) {
				print '<div class="line"><a href="' . $_SERVER['PHP_SELF'] . '?topicDel=' . $row['id'] . '">[ del ]</a></div>';
			}
print '<div style="clear: both;">&nbsp;</div>' . "\n";
		}
		return true;
	}
	else {
		return false;
	}
}
function topicForm($name) {
	print '
<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
<input type="hidden" name="poster" value="' . $name . '" />
<div><label for="topicTitle">Название темы<sup style="font-size: large;">*</sup>:</label><input type="text" name="topicTitle" id="topicTitle" /></div>
<div><label for="requestTitle">Заголовок вопроса:</label><input type="text" name="requestTitle" id="requestTitle" /></div>
<div><label for="requestBody">Обсуждаемый вопрос:</label><textarea name="requestBody" id="requestBody" cols="20" rows="20"></textarea></div>
<div><input type="submit" name="topicSbmt" value="Создать новую тему" /></div>
</form>' . "\n";
}
function topicWrite($poster, $topicTitle, $requestTitle, $requestBody, $db) {
	if(!str_empty($topicTitle)) {
		if(!$db->dbQuery("INSERT INTO topic (title, poster, created) VALUES (
		'" . addslashes(htmlspecialchars($topicTitle)) . "',
	    '" . addslashes(htmlspecialchars($poster)) . "',
	    NOW())")) {
		return false;
	    }
	}
	$topicId = mysql_insert_id($db->dbLink);
	if(!str_empty($requestTitle) && !str_empty($requestBody)) {
		if(!$db->dbQuery("INSERT INTO request (parent, title, poster, created, body) VALUES (
	'" . $topicId . "',
	'" . addslashes(htmlspecialchars($requestTitle)) . "',
	'" . addslashes(htmlspecialchars($poster)) . "',
	NOW(),
	'" . addslashes(htmlspecialchars($requestBody)) . "')")) {
		return false;
	}
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit();
}
function topicDel($topicID, $db) {
	//	if(!$db->dbQuery("DELETE topic, request, response FROM topic, request, response WHERE
	//	topic.id=$topicID and request.parent=$topicID and
	//	request.id=$requestID and response.parent=$requestID")) {
	//	    return false;
	//	}
	//	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	//	exit();
	return false;
}
?>