<?php

class Album extends AppModel {
    var $name = 'Album';
	var $components = array('Director');

	var $hasMany = array('Image' =>
	              		array('className'  => 'Image',
	                      	  'foreignKey' => 'aid',
							  'dependent'  => true,
							  'order' 	   => 'seq, src'	
	                	),
						'Tag' => 
						array('className'  => 'Tag',
							  'foreignKey' => 'aid',
							  'dependent'  => true
						)
	               );

	function bindPreview() {
		$hasOne = array('Preview' =>
							array(	'className' 	=> 'Image',
								  	'foreignKey' 	=> 'aid',
								   	'conditions' 	=> 'Preview.src = Album.aTn'));
		$this->bindModel(array('hasOne' => $hasOne));
	}
	
	function beforeFind($queryData) {
		if (is_array($queryData['conditions'])) {
			$queryData['conditions'][] = "Album.name <> ''";
		} else {
			if (!empty($queryData['conditions'])) {
				$queryData['conditions'] .= " AND ";
			}
			$queryData['conditions'] .= "Album.name <> ''";
		}
		return $queryData;
	}
	
	function afterFind($result) {
		if (!isset($result[0]['Album'])) { return $result; }
		for($i = 0; $i < count($result); $i++) {
			$description = $result[$i]['Album']['description'];
			if (empty($description)) {
				$result[$i]['Album']['description_clean'] = __('This album does not have a description.', true);
			} else {
				$result[$i]['Album']['description_clean'] = $description;
			}
		}
		return $result;
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave() {
		App::import('Model', 'Tag');
		$this->Tag =& new Tag();
		$tags = $this->Tag->findAll(aa('aid', $this->id));
		if (!empty($tags)) {
			foreach($tags as $tag) {
				$this->Tag->Gallery->reorder($tag['Gallery']['id']);
			}
		}
		$this->popCache();
		return true;
	}
	
	function beforeDelete() {
		$this->popCache();
		return true;
	}
	
	function popCache() {
		$id = $this->id;
		$album = $this->read();
		$targets = array("images_album_{$id}", "images_album_.*,{$id}_");
		$api_targets = array("get_album_{$id}", 'get_albums_list_0_0', 'get_albums_list_0_1', 'get_albums_list_1_0', 'get_albums_list_1_1');
		if (!empty($album['Tag'])) {	
			$api_targets[] = 'get_gallery_list';
			foreach ($album['Tag'] as $tag) {
				$targets[] = 'images_gid_' . $tag['did'];
				$targets[] = 'images_gallery_' . $tag['did'];
				$api_targets[] = 'get_gallery_' . $tag['did'];
			}
		}
		$this->clearCache($targets, $api_targets);
	}
	
	////
	// Find all active albums
 	////
	function findActive($order = 'name') {
		$this->unbindModel(array('hasMany' => array('Image', 'Tags')));
		return $this->findAll("active = 1", null, $order);
	}
	
	////
	// Quickly return images in array
	////
	function returnImages($id) {
		$this->id = $id;
		$album = $this->read();
		return $album['Image'];
	}
	
	////
	// Reorder based on preset
	////
	function reorder($id, $manual = false) {
		// On really large albums, this might take a while
		if (function_exists('set_time_limit')) {
			set_time_limit(0);
		}
		$this->id = $id;
		$this->recursive = -1;
		$album = $this->read();
		$order = $album['Album']['sort_type'];
		$this->Image->coldSave = true;
		switch($order) {
			case('manual'):
				if ($manual) {
					$this->Image->recursive = -1;
					$images = $this->Image->find('all', array('conditions' => "aid = $id", 'order' => 'seq'));
					$i = 0;
					foreach($images as $image) {
						$this->Image->id = $image['Image']['id'];
						$this->Image->saveField('seq', $i+1);
						if ($image['Image']['active']) {
							$i++;
						}
					}
				}
				break;
			case('file name (oldest first)'):
			case('file name (newest first)'):
				$images = $this->Image->findAll(aa('aid', $id));
				$files = array();
				foreach($images as $i) {
					$files[] = $i['Image']['src'];
				}
				natcasesort($files);
				if (strpos($order, 'newest') !== false) {
					$files = array_reverse($files);
				}
				$files = array_values($files);
				$seq = 0;
				for($i = 0; $i < count($files); $i++) {
					$temp = $this->Image->find(aa('src', $files[$i], 'aid', $id));
					$this->Image->id = $temp['Image']['id'];
					$this->Image->saveField('seq', $seq+1);
					if ($temp['Image']['active']) {
						$seq++;
					}
				}
				break;
			default:
				preg_match('/(date|captured) \((.*)\)/', $order, $matches);
				$data = $matches[1];
				$order = $matches[2];
				if ($data == 'date') {
					$sql = '`Image`.created_on';
				} else {
					$sql = '`Image`.captured_on';
				}
				if ($order == 'newest first') { $sql .= ' DESC'; }
				$images = $this->Image->findAll(aa('aid', $id), null, $sql);
				$seq = 0;
				for($i = 0; $i < count($images); $i++) {
					$this->Image->id = $images[$i]['Image']['id'];
					$this->Image->saveField('seq', $seq+1);
					if ($images[$i]['Image']['active']) {
						$seq++;
					}
				}
				break;
		}
		$this->Image->coldSave = false;
		return true;
	}
}

?>