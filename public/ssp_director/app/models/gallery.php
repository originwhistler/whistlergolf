<?php

class Gallery extends AppModel {
    var $name = 'Gallery';
	var $useTable = 'dynamic';

	var $hasMany = array('Tag' => 
						array('className'  => 'Tag',
							  'foreignKey' => 'did',
							  'dependent'  => true,
							  'order'      => 'display'
						)
	               );

	function afterFind($result) {
		if (!isset($result[0]['Gallery'])) { return $result; }
		for($i = 0; $i < count($result); $i++) {
			$description = $result[$i]['Gallery']['description'];
			if (empty($description)) {
				$result[$i]['Gallery']['description_clean'] = __('This gallery does not have a description.', true);
			} else {
				$result[$i]['Gallery']['description_clean'] = $description;
			}
		}
		return $result;
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave() {
		$this->popCache();
		return true;
	}
	
	function beforeDelete() {
		$this->popCache();
		return true;
	}
	
	function popCache() {
		$id = $this->id;
		$targets = array("images_gid_{$id}", "images_gallery_{$id}");
		$api_targets = array('get_gallery_list', 'get_gallery_' . $id);
		$apis = glob(CACHE . 'api' . DS . 'get_associated_*');
		foreach($apis as $a) {
			if (!is_dir($a)) {
				$api_targets[] = basename($a);
			}
		}
		$this->clearCache($targets, $api_targets);
	}
	
	function isMain($id) {
		$this->id = $id;
		$gallery = $this->read();
		return $gallery['Gallery']['main'];
	}
	
	////
	// Reorder based on preset
	////
	function reorder($id) {
		// On really large galleries, this might take a while
		if (function_exists('set_time_limit')) {
			set_time_limit(0);
		}
		$this->id = $id;
		$this->recursive = 2;
		$gallery = $this->read();
		$order = $gallery['Gallery']['sort_type'];
		App::import('Model', 'Tag');
		$this->Tag =& new Tag();
		if ($order != 'manual') {
			$ids = array();
			switch($order) {
				case('album title (newest first)'):
				case('album title (oldest first)'):
					$albums = $gallery['Tag'];
					$names = array();
					foreach($albums as $a) {
						$names[] = $a['Album']['name'] . '__~~__' . $a['id'];
					}
					natcasesort($names);
					if (strpos($order, 'newest') !== false) {
						$names = array_reverse($names);
					}
					$names = array_values($names);
					for($i = 0; $i < count($names); $i++) {
						$bits = explode('__~~__', $names[$i]);
						$this->Tag->id = $bits[1];
						$this->Tag->saveField('display', $i+1);
					}
					break;
				default:
					preg_match('/(date|modified) \((.*)\)/', $order, $matches);
					$data = $matches[1];
					$order = $matches[2];
					if ($data == 'date') {
						$sql = '`Album`.created_on';
					} else {
						$sql = '`Album`.modified_on';
					}
					if ($order == 'newest first') { $sql .= ' DESC'; }
					$albums = $gallery['Tag'];
					$aids = array();
					foreach($albums as $a) {
						if (is_numeric($a['Album']['id'])) {
							$aids[] = $a['Album']['id']; 
						}
					}
					$aids = join(',', $aids);
					$conditions = "`Album`.id IN ($aids) AND `Tag`.did = $id";
					$new_albums = $this->Tag->findAll($conditions, null, $sql);
					$i = 1;
					foreach($new_albums as $album) {
						$this->log($album);
						if ($album['Tag']['display'] != $i) {
							$this->Tag->query("UPDATE " . DIR_DB_PRE . "dynamic_links SET display = $i WHERE did = $id AND aid = {$album['Album']['id']}");
						}
						$i++;
					}
					break;
			}
		}
		return true;
	}
}

?>