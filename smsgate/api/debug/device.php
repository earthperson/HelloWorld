<?php 
$title = 'Интерфейс для отладки SMS Gateway';
?>
<html>
<head>
<title><?php print $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://jquery-json.googlecode.com/svn/trunk/jquery.json.min.js"></script>
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
	$('#hash1').focus(function(e) {
		$.post(
			'rpc.php',
			{
				user_id: $('#user_id1').val(),
				cmd: $('#cmd1').val(),
				da: $('#da').val(),
				text: $('#text').val()
			},
			function(data) {
				$('#hash1').val(data);
			}
		);
	});

	$('#hash2').focus(function(e) {
		$.post(
			'rpc.php',
			{
				user_id: $('#user_id2').val(),
				cmd: $('#cmd2').val(),
				sms_id: $('#sms_id').val()
			},
			function(data) {
				$('#hash2').val(data);
			}
		);
	});

	$('#form1 input[type="submit"]').click(function(e) {
		e.preventDefault();
		var obj = {
				user_id: $('#user_id1').val(),
				cmd: $('#cmd1').val(),
				da: $('#da').val(),
				text: $('#text').val(),
				hash: $('#hash1').val()
			};
		$('#request').html($.toJSON(obj));
	});

	$('#form2 input[type="submit"]').click(function(e) {
		e.preventDefault();
		var obj = {
				user_id: $('#user_id2').val(),
				cmd: $('#cmd2').val(),
				sms_id: $('#sms_id').val(),
				hash: $('#hash2').val()
			};
		$('#request').html($.toJSON(obj));
	});

	$('#requestSubmit').click(function(e) {
		e.preventDefault();
		$.post(
			'../gateway.php',
			$('#request').html(),
			function(data) {
				$('#response').html(data);
			}
		);
	});
});
</script>
<h1><?php print $title; ?></h1>

<form action="" method="post" id="form1">
	<div>User ID:<input type="text" name="user_id" id="user_id1" value="1" /></div>
	<div>Command: <input type="text" name="cmd" id="cmd1" value="send_sms" /></div>
	<div>Destination address: <input type="text" name="da" id="da" value="" /></div>
	<div>Text: <textarea name="text" id="text" style="width:400px;height:200px;">Привет!</textarea></div>
	<div>Hash(<b>focus</b> to generate): <input type="text" name="hash1" id="hash1" /></div>
	<input type="submit" value="Get json encoded command send_sms" />
</form>

<form action="" method="post" id="form2">
	<div>User ID:<input type="text" name="user_id" id="user_id2" value="1" /></div>
	<div>Command: <input type="text" name="cmd" id="cmd2" value="get_sms_status" /></div>
	<div>SMS ID: <input type="text" name="sms_id" id="sms_id" value="" /></div>
	<div>Hash(<b>focus</b> to generate): <input type="text" name="hash2" id="hash2" /></div>
	<input type="submit" value="Get json encoded command get_sms_status" />
</form>

<input type="submit" id="requestSubmit" value="Send request" />

<h3>Request</h3>
<div><pre id="request"></pre></div>
<br />
<h3>Response</h3>
<div><pre id="response"></pre></div>

</body>
</html>