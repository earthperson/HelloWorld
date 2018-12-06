<?php
function responseLine1($topicId) {
	print '
<div style="margin-bottom: 10px;"><a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '">Вернуться к темам форума</a>&nbsp;&nbsp;&nbsp;<a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?topicID=' . $topicId  . '">Вернуться к списку вопросов</a></div>' . "\n";
}
function responseLine2() {
	print '
<div class="topic">Ответ</div>    	    
<div class="topic">Автор</div>
<div class="topic">Добавлен</div>
<div style="clear: both;">&nbsp;</div>' . "\n";
}
function showResponse($topicID, $requestID, $db) {
	if($db->dbQuery("SELECT response.id, response.parent, response.poster, CONCAT(DATE_FORMAT(response.created, '%k\:%i\:%s\ %e\-%b\-%Y')) AS created, response.body FROM request, response WHERE request.id=$requestID and response.parent=$requestID")) {
		while ($row = mysql_fetch_assoc($db->dbResult)) {
			print '
<div class="line">' . $row['body'] . '</div>
<div class="line">' . $row['poster'] . '</div>
<div class="line">' . $row['created'] . '</div>';
			if(isset($_SESSION['admin'])) {
				print '<div class="line"><a href="' . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID . '&requestID=' . $requestID . '&responseDel=' . $row['id'] . '">[ del ]</a></div>';
			}
print '<div style="clear: both;">&nbsp;</div>' . "\n";
		}
		return true;
	}
	else {
		return false;
	}
}
function responseForm($topicID, $requestID, $name) {
	print '
<form action="' . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID . '&requestID=' . $requestID . '" method="post">
<input type="hidden" name="poster" value="' . $name . '" />
<input type="hidden" name="responseParent" value="' . $requestID . '" />
<div><label for="responseBody">Ответ<sup style="font-size: large;">*</sup>:</label><textarea name="responseBody" id="responseBody" cols="20" rows="20"></textarea></div>
<div><input type="submit" name="responseSbmt" value="Ответить" /></div>
</form>' . "\n";
}
function responseWrite($poster, $requestID, $responseParent, $responseBody, $db) {
	if(!str_empty($responseBody)) {
		if(!$db->dbQuery("INSERT INTO response (parent, poster, created, body) VALUES (
	'" . $responseParent . "',
	'" . addslashes(htmlspecialchars($poster)) . "',
	NOW(),
	'" . addslashes(htmlspecialchars($responseBody)) . "')")) {
		return false;
	}
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?topicID=' . $requestID . '&requestID=' . $responseParent);
	exit();
}
function responseDel($topicID, $requestID, $responseID, $db) {
	if(!$db->dbQuery("DELETE FROM response WHERE id=$responseID")) {
		return false;
	}
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?topicID=' . $topicID . '&requestID=' . $requestID);
	exit();
}
?>