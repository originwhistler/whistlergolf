<?php

class Image extends AppModel {
    var $name = 'Image';
	var $coldSave = false;
	var $belongsTo = array('Album' =>
                           array('className'  => 'Album',
                                 'foreignKey' => 'aid'
                           )
                     );

	function beforeSave() {
		if (isset($this->data['Image']['tags'])) {
			$this->log('_' . $this->data['Image']['tags'] . '_');
			$this->data['Image']['tags'] = trim(str_replace('  ', ' ', str_replace(',', ' ', $this->data['Image']['tags'])));
		}
		return parent::beforeSave();
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave() {
		if (!$this->coldSave) {
			$this->popCache();
		}
		return true;
	}
	
	function beforeDelete() {
		if (!$this->coldSave) {
			@$this->popCache(false);
		}
		return true;
	}
	
	function popCache($save = true) {
		$id = $this->id;
		$image = $this->read();
		$api_targets = array('get_content_' . $id, 'get_users');
		$this->clearCache(array(), $api_targets);
		$album_id = $image['Album']['id'];
		$this->cacheQueries = false;
		$count = $this->findCount(aa('aid', $album_id));
		if (!$save) { $count -= 1; }
		$this->Album->id = $album_id;
		if ($this->Album->read()) {
			$data['Album']['images_count'] = $count;
			$data['Album']['modified_on'] = $this->Album->gm();
			$this->Album->save($data);
		}
	}
}

?>