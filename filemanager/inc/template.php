<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php print $CONFIG['general']['charset']; ?>" />
<title><?php print plain_text($TPLD['title']); ?></title>
<link rel="stylesheet" href="/style.css" type="text/css" />
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jstree/jquery.tree.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var rpc_url = "/inc/rpc.php";
	var creation = {
			parent_id: null
	}
	$("#tree").tree({
		data : { 
			type : "json",
			async : true,
			opts : {
				method : "POST",
				url : rpc_url,
			}
		},
		opened : ["node_0"],
		/*
		rules : {
			valid_children: "root"
		},
		*/
		types : {
			"default" : {
				clickable	: true,
				renameable	: true,
				deletable	: true,
				creatable	: true,
				draggable	: false,
				max_children	: -1,
				max_depth	: -1,
				valid_children	: "all",

				icon : {
					image : false,
					position : false
				}
			},
			/*
			"root" : {
				renameable	: false,
				deletable : false,
				draggable : false,
			    valid_children : [ "folder", "file" ],
				icon : {
					image : "/img/drive.png"
			    }
			},
			*/
			"folder" : {
				renameable	: function (NODE, TREE_OBJ) {
					if(TREE_OBJ.get_node(NODE).attr('id') == "node_0") {
						return false;
					}
					else {
						return true;
					}
				},
				deletable : function (NODE, TREE_OBJ) {
					if(TREE_OBJ.get_node(NODE).attr('id') == "node_0") {
						return false;
					}
					else {
						return true;
					}
				},
				valid_children : [ "folder", "file" ]
			},
			"file" : {
				valid_children : "none",
				max_children : 0,
				max_depth :0,
				icon : { 
					image : "/img/file.png"
				}
			}
		},
		callback : { 
			beforedata : function (NODE, TREE_OBJ) { 
				if(NODE == false) {
					TREE_OBJ.settings.data.opts.static = <?php print $TPLD['stat_tree'] ?>; 
				}
				else {
					TREE_OBJ.settings.data.opts.static = false; 
					var id = TREE_OBJ.get_node(NODE).attr('id');
					TREE_OBJ.settings.data.opts.url = rpc_url + '?parent_id=' + id.substr(5) + '&action=get_nodes';
				}
			},

			oncreate : function (NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
				creation.parent_id = TREE_OBJ.parent(NODE).attr('id').substr(5);
			},

			ondelete : function (NODE, TREE_OBJ, RB) {
				xhr = $.post(
					rpc_url,
					{
						action: "delete",
						id: TREE_OBJ.get_node(NODE).attr('id').substr(5)
					},
					function(data, textStatus) {
						if(textStatus != "success") {
							alert(textStatus);
						}
						window.location.reload();
					}, "json"
				);
			},
			
			onrename : function (NODE, TREE_OBJ, RB) {
				// creation
				if(creation.parent_id !== null) {
					xhr = $.post(
						rpc_url,
						{
							action: "create",
							parent_id: creation.parent_id,
							name: TREE_OBJ.get_text(NODE)
						},
						function(data, textStatus) {
							if(textStatus != "success") {
								alert(textStatus);
							}
							creation.parent_id = null;
							window.location.reload();
						}, "json"
					);
				}
				// renaming
				else {
					xhr = $.post(
						rpc_url,
						{
							action: "rename",
							id: TREE_OBJ.get_node(NODE).attr('id').substr(5),
							new_name: TREE_OBJ.get_text(NODE)
						},
						function(data, textStatus) {
							if(textStatus != "success") {
								alert(textStatus);
							}
						}, "json"
					);
				}
			}
		}
	});
});

function upload(n) {
	var t = $.tree.focused().get_type(n);
	if(t != 'file') {
		var obj = $('input[type="file"]');
		if(obj.css('display') == 'none') {
			obj.show();
			obj.after('<input type="hidden" name="parent_id" value="' +  $.tree.focused().get_node(n).attr('id').substr(5) + '" />');
			return false;
		}
	}
	else {
		alert('Unsupported for ' + t);
		return false;
	}
}

function download(n) {
	var t = $.tree.focused().get_type(n);
	if(t == 'file') {
		window.location.href = 'http://<?php print $_SERVER['HTTP_HOST'] ?>/inc/download.php?id=' + $.tree.focused().get_node(n).attr('id').substr(5);
	}
	else {
		alert('Unsupported for ' + t);
	}
}
</script>
</head>
<body>
<h1 class="page-title"><?php print plain_text($TPLD['title']); ?></h1>
<table cellpadding="10" cellspacing="0" border="1" class="box">
  <tr>
  	<th>Folders</th>
  	<th>Actions</th>
  </tr>
  <tr>
    <td id="tree" valign="top">&nbsp;</td>
    <td id="controls" valign="top">
    	<input type="button" onclick="var t = $.tree.focused(); if(t.selected) t.create(); else alert('Select a node first');" value="Create folder" />
    	<input type="button" onclick="var t = $.tree.focused(); if(t.selected) t.remove(); else alert('Select a node first');" value="Delete selected" />
    	<input type="button" onclick="var t = $.tree.focused(); if(t.selected) t.rename(); else alert('Select a node first');" value="Rename selected" />
    	<form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    		<div><input type="hidden" name="action" value="upload" /></div>
    		<div><input type="file" name="file" style="display: none;" /></div>
    		<div><input type="submit" onclick="var t = $.tree.focused(); if(t.selected) return upload(t.selected); else alert('Select a node first'); return false;" value="Upload" /></div>
    	</form>
    	<input type="button" onclick="var t = $.tree.focused(); if(t.selected) download(t.selected); else alert('Select a node first');" value="Download" />
    </td>
  </tr>
</table>
</body>
</html>