<?php

class UploadsController extends AppController {
	// Models needed for this controller
	var $uses = array('Album', 'Image');
    var $name = 'Uploads';
	
	////
	// Accepts file uploads
 	////
	function image($user_id, $id, $upload_type, $tags = '') {	
		// Make sure this is coming from the flash player and is a POST request
		if (strpos(strtolower(env('HTTP_USER_AGENT')), 'flash') === false || !$this->RequestHandler->isPost()) {
			exit;
		}
		
		$tags = urldecode($tags);
		if ($tags == 'null' || $tags == 'undefined') {
			$tags = '';
		}
		
		define('CUR_USER_ID', $user_id);
		
		// Make sure permissions are set correctly
		$old_mask = umask(0);
			
		// Get album
		$this->Album->id = $id;
		$album = $this->Album->read();
		$top = $this->Image->find('first', array('conditions' => "aid = $id", 'order' => 'seq DESC'));
		$next = $top['Image']['seq'] + 1;
		// Flash uploads crap out when spaces are in the name
		$file = str_replace(" ", "_", $this->params['form']['Filedata']['name']);
		$file = ereg_replace("[^A-Za-z0-9._-]", "_", $file);

		// Get image extensions so we make sure
		// a safe file is uploaded
		$ext = $this->Director->returnExt($file);
		
		// Paths
		$the_temp = $this->params['form']['Filedata']['tmp_name'];
		$path = ALBUMS . DS . $album['Album']['path'];
		
		$lg_path = $path . DS . 'lg' . DS . $file;
		$lg_temp = $lg_path . '.tmp';
		
		$tn_path = $path . DS . 'tn' . DS . $file;
		$tn_temp = $tn_path . '.tmp';
		
		$thumb_path = THUMBS . DS . 'album-' . $id . '.' . $ext;
		$thumb_temp = $thumb_path . '.tmp';
		
		$int_path = $path . DS . 'director' . DS . $file;
				
		settype($upload_type, 'integer');
		
		if (in_array($ext, a('jpg', 'jpeg', 'gif', 'png', 'mp3')) || isNotImg($file)) {
			switch($upload_type) {
				// Thumbnail
				case(2):
					if (is_uploaded_file($the_temp) && move_uploaded_file($the_temp, $tn_temp)) {
						copy($tn_temp, $tn_path);
						unlink($tn_temp);
					}
					break;
				// Audio	
				case(4):
					if (is_uploaded_file($the_temp) && $this->Director->setPerms(AUDIO)) {
						$a_tmp = AUDIO . DS . $file . '.tmp';
						move_uploaded_file($the_temp, $a_tmp);
						copy($a_tmp, AUDIO . DS . $file);
						unlink($a_tmp);
						$this->Album->saveField('audioFile', $file);
					}
					break;
				// Standard image or custom thumb
				default:
					if (is_uploaded_file($the_temp) && move_uploaded_file($the_temp, $lg_temp)) {
						copy($lg_temp, $lg_path);
						unlink($lg_temp);
						
						list($meta, $captured_on) = $this->Director->imageMetadata($lg_path);
						
						$check = $this->Image->find("aid = $id AND src = '$file'");
						if (empty($check)) {
							$this->data['Image']['src'] = $file;
							$this->data['Image']['aid'] = $id;
							$this->data['Image']['seq'] = $next;
							$this->data['Image']['filesize'] = filesize($lg_path);
							$this->data['Image']['captured_on'] = $captured_on;
							$this->data['Image']['tags'] = $tags;
							
							if ($upload_type == 3) {
								$this->data['Image']['active'] = 0;
								$this->Album->saveField('aTn', $file);
							}
							$this->Image->save($this->data);
							$image_id = $this->Image->getLastInsertId();
						} else {
							$image_id = $check['Image']['id'];
							$caches = glob(ALBUMS . DS . $check['Album']['path'] . DS . 'cache' . DS . $check['Image']['src'] . '*');
							if (!empty($caches)) {
								foreach($caches as $cache) {
									@unlink($cache);
								}
							}
							$this->Image->id = $image_id;
							$this->data['Image']['captured_on'] = $captured_on;
							$this->data['Image']['filesize'] = filesize($lg_path);
							$this->data['Image']['tags'] = $tags;
							$this->Image->save($this->data);
						}
					}
					break;
			}
		}
		
		// Reset umask
		umask($old_mask);
		clearCache(DIR_CACHE . DS . 'users.cache');
		// Exit with some empty space so onComplete always fires in flash/Mac
		exit(' ');
	}
	
	function avatar($user_id) {
		if (!is_dir(AVATARS . DS . $user_id)) {
			$this->Director->makeDir(AVATARS . DS . $user_id);
		}
		if (strpos(strtolower(env('HTTP_USER_AGENT')), 'flash') === false || !$this->RequestHandler->isPost()) {
			exit;
		}
		$oldies = glob(AVATARS . DS . $user_id . DS . 'original.*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$oldies = glob(AVATARS . DS . $user_id . DS . 'cache' . DS . '*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$ext = $this->Director->returnExt($this->params['form']['Filedata']['name']);
		$the_temp = $this->params['form']['Filedata']['tmp_name'];
		$path = AVATARS . DS . $user_id . DS . 'original.' . $ext;
		if (!is_dir(dirname($path))) {
			umask(0);
			mkdir(dirname($path), 0777);
		}
		if (in_array($ext, a('jpg', 'jpeg', 'gif', 'png'))) {
			if (is_uploaded_file($the_temp)) {
				move_uploaded_file($the_temp, $path);
			}
		}
		exit(' ');
	}
}

?>