<?php
class Tree {
	public $ok = true;
	private $db;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	public function get_stat() {
		$db_tree = $this->db->select("SELECT id AS ARRAY_KEY, parent_id AS PARENT_KEY, name, type FROM journal ORDER BY id LIMIT 500");
		$objects = array();
		foreach ($db_tree as $id => $node) {
			$objects[] = $this->make_jso($id, $node, !empty($node['childNodes']));
		}
		return '[
					{
						attributes: {id: "node_0", rel: "folder"},
						data: { title: "Root / Корень", icon: "/img/drive.png" },
						' . ($objects ? 'children: [' . implode(',', $objects) . ']' : '') . '
					}
				]';
	}
	
	public function get_nodes($parent_id = 0) {
		$nodes = $this->db->select("SELECT id AS ARRAY_KEY, name, type FROM journal WHERE parent_id = ?d ORDER BY id", (int)$parent_id);
		$objects = array();
		foreach ($nodes as $id => $node) {
			$objects[] = $this->make_jso($id, $node, $this->db->selectCell("SELECT COUNT(*) FROM journal WHERE parent_id = ?d", $id));
		}
		return '[' . ($objects ? implode(',', $objects) : '') . ']';
	}
	
	public function create($parent_id, $name) {
		$this->db->query("INSERT INTO
		            	  	  journal
		            	  SET
		            	  	  parent_id = ?d,
		            		  file = ?,
		            		  type = 'folder',
		            		  name = ?",
	            		  (int)$parent_id,
	            		  md5(uniqid(rand(), true)),
	            		  $name
        );
	}
	
	public function delete($id) {
		$rec = $this->db->selectRow("SELECT * FROM journal WHERE id = ?d", (int)$id);
		if($rec['type'] == 'file') {
			$f = "{$_SERVER['DOCUMENT_ROOT']}/var/{$rec['file']}";
			if(is_file($f)) {
				@unlink($f);
			}
		}
		$this->db->query("DELETE FROM journal WHERE id = ?d LIMIT 1", (int)$id);
	}
	
	public function rename($id, $new_name) {
		$this->db->query("UPDATE journal SET name = ? WHERE id = ?d LIMIT 1", $new_name, (int)$id);
	}
	
	private function make_jso($id, $node, $closed) {
		$object = array(
					'attributes' => array(
										'id' => "node_{$id}",
										'rel' => $node['type']
									),
					'data' => $node['name']
				  );
		if($closed) {
			$object['state'] = 'closed';
		}
		return json_encode($object);
	}
}
?>