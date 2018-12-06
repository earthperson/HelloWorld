<?php
error_reporting(E_ALL | E_STRICT);
if(isset($_COOKIE['action-status'])) {
    setcookie('action-status', '', time()-3600);
}
require_once 'config.inc.php';

mysql_connect($CONFIG['db']['host'], $CONFIG['db']['login'], $CONFIG['db']['password']) or die();
mysql_select_db($CONFIG['db']['base']) or die();
mysql_query("SET CHARACTER SET {$CONFIG['db']['charset']}");

require_once '../PLib/Common.class.php';
//require_once 'debug/index.php';
$projects = PLib_Common::serialize("SELECT * FROM gr_project ORDER BY name", "project_id");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=<?php print $CONFIG['general']['charset']?>">
<title>Яндекс.Маркет.Grabber</title>
<link href="css/redmond/jquery-ui.custom.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.custom.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var xhr;
	var lock = {
			status: [],
			set: function(obj) {
				for(var a in obj) {
					obj[a] ? $('#'+a).css({opacity: 0.2}) : $('#'+a).css({opacity: 1});
	        		this.status[a] = obj[a];
				}
			}
		}
	function ajax(p, r) {
		lock.set({'Start': true, 'Reset': true, 'Import': true, 'Export': true});
		xhr = $.post(
			"processor.php", { action: p, project: $("#project").val() },
			function(data, textStatus) {
				if(!data.error && textStatus == "success") {
					$("#progressbar").progressbar('option', 'value', data.progress);
					if($("#progressbar").progressbar('option', 'value') >= 100) {
						alert('Action completed successfully!');
						window.setTimeout(function() {$("#progressbar").progressbar('destroy'); }, 200);
						lock.set({'Start': false, 'Reset': false, 'Pause': true, 'Import': false, 'Export': false});
						return;
					}
					if(r) {
						window.setTimeout(function() { ajax(p, true); }, 200);
					}
				}
				else {
					alert(data.error);
				}
		  	}, "json"
		);
		lock.set({'Pause': false});
	}
	$("#accordion").accordion({
		collapsible: true
	});
	lock.set({'Pause': true});
	$("#Start, #Reset").click(function() {
		$("#progressbar").progressbar('destroy');
		$("#progressbar").progressbar({value: 0});
		if($(this).attr('id') == 'Start' && !lock.status.Start) {
			ajax("Start", true);
		}
		else if($(this).attr('id') == 'Reset' && !lock.status.Reset) {
			if(confirm('Do you really want to reset the data base for the project "'+$("#project option:selected").text()+'"?')) {
				ajax("Reset");
			}
			else {
				$("#progressbar").progressbar('destroy');
			}
		}
	});
	$("#Pause").click(function() {
		if(!lock.status.Pause) {
			try {
				xhr.abort();
			}
			catch(e) {}
			lock.set({'Start': false, 'Reset': false, 'Pause': true, 'Import': false, 'Export': false});
		}
	});
	$("#Import, #Export").click(function() {
		if($(this).attr('id') == 'Import' && !lock.status.Import) {
			lock.set({'Import': true});
			$('.loader').show();
			return true;
		}
		else if($(this).attr('id') == 'Export' && !lock.status.Export) {
			window.location.href = 'http://<?php print $_SERVER['HTTP_HOST']?>/db-manager.php?action=Export&project='+$("#exportProject").val();
		}
		return false;
	});
	<?php
	    if(@$_COOKIE['action-status'] == 'ok') {
	        print "alert('Action completed successfully!');";
	    }
	    elseif(@$_COOKIE['action-status'] == 'failed') {
	        print "alert('An error has been occurred!');";
	    }
    ?>
});
</script>
</head>
<body>
<h1><a href="/">Яндекс.Маркет.Grabber</a></h1>
<div id="main">
<div id="accordion">

<!-- Section 1 -->
<h3><a href="#">Import</a></h3>
<div>
<form action="db-manager.php?action=Import" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="Import">
<table cellpadding="5" cellspacing="0" border="0" width="100%">
  <tr>
    <td><label for="newProject">New project name:</label></td>
    <td><input type="text" name="new_project" id="newProject"></td>
  </tr>
  <tr>
    <td>Location of the text file:</td>
    <td><input type="file" name="data"></td>
  </tr>
  <tr>
    <td>Character set of the file:</td>
    <td>
    	<select name="charset">
          <option value="utf-8">utf-8</option>
          <option value="windows-1251" selected="selected">windows-1251</option>
        </select>
    </td>
  </tr>
  <tr>
    <td><label for="offset">Number of records to skip from start:</label></td>
    <td><input type="text" name="offset" id="offset" value="1"></td>
  </tr>
  <tr>
    <td colspan="2">
    	<input type="image" src="images/import.jpg" alt="Import" title="Import" align="middle" class="button" id="Import">
    	<img src="images/i.gif" width="20" height="1" alt="">
    	<img src="images/loader.gif" alt="" width="32" height="32" style="display:none;" class="loader" align="middle">
    </td>
  </tr>
</table>
</form>
</div>

<?php if($projects) { ?>
<!-- Section 2 -->
<h3><a href="#">Process</a></h3>
<div>
<form>
<table cellpadding="5" cellspacing="0" border="0" width="100%">
  <tr>
    <td width="25%">Project name:</td>
    <td width="75%">
    	<select id="project">
          <?php
        	foreach ($projects as $id=>$project) {
        	    print "<option value='{$id}'>" . htmlspecialchars($project['name'], ENT_QUOTES) . '</option>';
        	}
          ?>
        </select>
    </td>
  </tr>
  <tr>
    <td colspan="2"><div id="progressbar"></div></td>
  </tr>
  <tr>
    <td colspan="2">
        <img src="images/start.jpg" width="51" height="58" alt="Start" title="Start" class="button" id="Start">
        <img src="images/i.gif" width="20" height="1" alt="">
        <img src="images/pause.jpg" width="51" height="58" alt="Pause" title="Pause" class="button" id="Pause">
        <img src="images/i.gif" width="20" height="1" alt="">
        <img src="images/reset.jpg" width="51" height="58" alt="Reset" title="Reset" class="button" id="Reset">
    </td>
  </tr>
</table>  
</form>
</div>

<!-- Section 3 -->
<h3><a href="#">Export</a></h3>
<div>
<form>
<table cellpadding="5" cellspacing="0" border="0">
  <tr>
 	<td>Project name:</td>
    <td>
    	<select id="exportProject">
          <?php
        	foreach ($projects as $id=>$project) {
        	    print "<option value='{$id}'>" . htmlspecialchars($project['name'], ENT_QUOTES) . '</option>';
        	}
          ?>
        </select>
    </td>
  </tr>
  <tr>
    <td colspan="2">
        <img src="images/export.jpg" width="51" height="58" alt="Export" title="Export" class="button" id="Export">
    </td>
  </tr>
</table>  
</form>
</div>
<?php } ?>

</div> <!-- /Accordion -->

</div> <!-- /Main -->
</body>
</html>