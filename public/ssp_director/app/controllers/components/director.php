<?php
class DirectorComponent extends Object {
   	var $controller = true;
	var $uses = array('Account', 'Image');
	var $components = array('Kodak');
 
	var $dirTags = array(
			'image filename', 'album name', 'date captured', 'image number', 'image count', 'tags'
		);
			
	var $iptcTags = array(
			'credit', 'caption', 'copyright', 'title', 'category', 'keywords',
			'byline', 'byline title', 'city', 'state', 'country', 'headline',
			'source', 'contact'
		);
	
	var $exifTags = array(
			'make', 'model', 'exposure', 'exposure mode', 'iso', 'aperture',
			'focal length', 'flash simple', 'flash', 'exposure bias', 'metering mode',
			'white balance', 'title', 'comment'
		);
		
    function startup (&$controller) {
        $this->controller = &$controller;
    }

	function uploadLimit() {
		$max_upload = ini_get('upload_max_filesize');
		$post_max = ini_get('post_max_size');

		$max_upload_n = explode('m', strtolower($max_upload));
		$max_upload_n = $max_upload_n[0];
		
		$post_max_n = explode('m', strtolower($post_max));
		$post_max_n = $post_max_n[0];
		
		$max = $max_upload;
		$post_max_broken = false;
		
		if ($post_max_n < $max_upload_n) { $max = $post_max; $post_max_broken = true; }
		return array($max, $post_max_broken);
	}
	////
	// Fetch account
	////
	function fetchAccount($action = 'dummy') {
		$cache_path = DIR_CACHE . DS . 'account.cache';
		$force_actions = array('preferences', 'activate');
		if (in_array($action, $force_actions)) {
			$account = array();
		} else {
			$account = unserialize(cache($cache_path, null, '+1 day'));
		}
		if (empty($account)) {
			App::import('Model', 'Account');
			$this->Account =& new Account();
			$account = $to_cache = $this->Account->find();
			unset($to_cache['Account']['activation_key']);
			unset($to_cache['Account']['api_key']);
			cache($cache_path, serialize($to_cache));
		}
		$users = $this->fetchUsers();
		return array($account, $users);
	}
	
	function fetchUsers() {
		$cache_path_users = DIR_CACHE . DS . 'users.cache';
		$uarr = unserialize(cache($cache_path_users, null, '+1 year'));
		if (empty($uarr)) {
			App::import('Model', 'User');
			$this->User =& new User();
			App::import('Model', 'Image');
			$this->Image =& new Image();
			$this->User->recursive = -1;
			$users = $this->User->findAll();
			$uarr = array();
			foreach($users as $u) {
				$count = $this->Image->find('count', array('conditions' => 'created_by = ' . $u['User']['id'], 'recursive' => -1));
				$uarr[$u['User']['id']] = array('usr' => $u['User']['usr'], 'display_name' => $u['User']['display_name'], 'display_name_fill' => $u['User']['display_name_fill'], 'first_name' =>  $u['User']['first_name'], 'anchor' => $u['User']['anchor'], 'last_name' =>  $u['User']['last_name'], 'externals' => $u['User']['externals'], 'anchor' => $u['User']['anchor'], 'image_count' => $count, 'profile' => $u['User']['profile']);
			}
			cache($cache_path_users, serialize($uarr));
		}
		return $uarr;
	}
	
	////
	// Check email for a user
	////
	function checkMail($id) {
		App::import('Model', 'User');
		$this->User =& new User(); 
		$user = $this->User->find($id);
		return $user['User']['email'];
	}
	
	////
	// Get all slideshows
	////
	function fetchShows() {
		$cache_path = DIR_CACHE . DS . 'shows.cache';
		$shows = cache($cache_path, null, '+1 year');
		if ($shows == 'noshow') {
			$shows = array();
		} elseif (empty($shows)) {
			App::import('Model', 'Slideshow');
			$this->Slideshow =& new Slideshow();
			$shows = $this->Slideshow->findAll();
			if (empty($shows)) { 
				cache($cache_path, 'noshow');
			} else {
				cache($cache_path, serialize($shows));
			}
		} else {
			$shows = unserialize($shows);
		}
		return $shows;
	}
	
	////
	// Generate random string
	////
	function randomStr($len = 6) {
		return substr(md5(uniqid(microtime())), 0, $len);
	}
	
	////
	// Central directory creation logic
	// Creates a directory if it does not exits
 	////
	function makeDir($dir, $perms = '0777') {
		if (!is_dir($dir)) {
			umask(0);
			if (@mkdir($dir, octdec($perms))) {
				return true;
			} else {
				return false;
			}	
		} else {
			return true;
		}
	}
	
	////
	// Check for import folders
	////
	function checkImports() {
		if (is_dir(IMPORTS) && $handle = opendir(IMPORTS)) {
		    $folders = array();

		    while (false !== ($file = readdir($handle))) {
				$full_path = IMPORTS . DS . $file;
		        if (is_dir($full_path) && file_exists($full_path . DS . 'images.xml') && $file != '.' && $file != '..') {
					$folders[] = $file;
				}
		    }
		
		    closedir($handle);
			return $folders;
		} else {
			return array();
		}
	}
	
	
	////
	// Set permissions on a directory
	////
	function setPerms($dir, $perms = '0777') {
		if (!is_dir($dir)) {
			return $this->makeDir($dir);
		} elseif (is_writable($dir)) {
			return true;
		} else {
			$current_perms = substr(sprintf('%o', fileperms($dir)), -4);
			settype($current_perms, "string"); 
			if ($current_perms === $perms) {
				return true;
			} else {
				$mask = umask(0);                 
				if (@chmod($path, octdec($perms))) {
					umask($mask);
					return true;
				} else {
					umask($mask);
					return false;
				}
		    }
		}
	}
	
	////
	// Make sure album-audio and album-thumb have the correct perms
	////
	function setOtherPerms() {
		if ($this->setPerms(AUDIO)) {
			return true;
		} else {
			return false;
		}
	}
	
	////
	// Create album subdirectories
	////
	function setAlbumPerms($path) {
		if (empty($path)) {
			return false;
		} else {
			$path = ALBUMS . DS . $path;
			$lg = $path . DS . 'lg';
			$cache = $path . DS . 'cache';
		
			if ($this->setPerms($lg) && $this->setPerms($cache)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	////
	// Process permissions for album subdirectories
	////
	function createAlbumDirs($path) {
		$path = ALBUMS . DS . $path;
		$lg = $path . DS . 'lg';
		$cache = $path . DS . 'cache';
		
		if ($this->makeDir($lg) && $this->makeDir($cache)) {
			return true;
		} else {
			return false;
		}
	}
	
	////
	// Search a directory for a filename using a regular expression
	// Found in PHP docs: http://us3.php.net/manual/en/function.file-exists.php#64908
	////
	
	function regExpSearch($regExp, $dir, $regType='P', $case='') {
		$func = ( $regType == 'P' ) ? 'preg_match' : 'ereg' . $case;
		$open = opendir($dir);
		$files = array();
		while( ($file = readdir($open)) !== false ) {
			if ($func($regExp, $file) ) {
				$files[] = $file;
			}
		}
		return $files;
	}
	
	////
	// Grab the extension of of any file
	////
	function returnExt($file, $raw = false) {
		$pos = strrpos($file, '.');
		$ext = substr($file, $pos+1, strlen($file));
		if ($raw) {
			return $ext;
		} else {
			return strtolower($ext);
		}
	}

	////
	// Grab all files in a directory
	////
	function directory($dir, $filters = 'all') {
		if ($filters == 'accepted') { $filters = 'jpg,JPG,JPEG,jpeg,gif,GIF,png,PNG,swf,SWF,flv,FLV,f4v,F4V,mov,MOV,mp4,MP4,m4v,MV4,m4a,M4A,3gp,3GP,3g2,3G2'; }
		$handle = opendir($dir);
		$files = array();
		if ($filters == "all"):
			while (($file = readdir($handle))!==false):
				$files[] = $file;
			endwhile;
		endif;
		if ($filters != "all"):
			$filters = explode(",", $filters);
			while (($file = readdir($handle))!==false):
				for ($f=0; $f< sizeof($filters); $f++):
					$system = explode(".", $file);
					$count = count($system);
					if ($system[$count-1] == $filters[$f]):
						$files[] = $file;
					endif;
				endfor;
			endwhile;
		endif;
		closedir($handle);
		return $files;
	}
	
	////
	// Return string describing large thumbnail specs
	////
	function generateDesc($specs, $thumbs = false) {
		if ($thumbs) {
			$out = 'Thumbnails processed at ';
		} else {
			$out = 'Large images processed at ';
		}
		$out .= $specs['quality'] . ' quality with a sharpening factor of ' . $specs['sharpening'] . '.';
		return $out;
	}
	
	////
	// Recursive Directory Removal
	////
	function rmdirr($dir) {
	   	if (!$dh = @opendir($dir)) return;
	   	while (($obj = readdir($dh))) {
	       	if ($obj=='.' || $obj=='..') continue;
	       	$path = $dir.'/'.$obj;
			if (is_dir($path)) {
				@$this->rmdirr($path);
			} else {
				@unlink($path);
			}
	   	}
	 	closedir($dh);
	   	rmdir($dir);
	}

	////
	// Transform a string (e.g. 15MB) into an actual byte representation
	////
	function returnBytes($val) {
	   $val = trim($val);
	   $last = strtolower($val{strlen($val)-1});
	   switch($last) {
	       case 'g':
	           	$val *= 1024;
	       case 'm':
	           	$val *= 1024;
	       case 'k':
	           	$val *= 1024;
	   }
	   return $val;
	}

	////
	// Ye old autop function via PhotoMatt.net
	// This function is GPL'd (I believe) and is not covered by the Director license
	////
	function autop($pee, $br=1) {
		$pee = preg_replace("/(\r\n|\n|\r)/", "\n", $pee); // cross-platform newlines
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(\n\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
		if ($br) $pee = preg_replace('|(?<!</p>)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		return $pee;
	}
	
	function formLink($image, $album) {
		$src = $image['src'];
		
		if (isNotImg($src)) {
			return '';
		}
		$template = urldecode($album['link_template']);
		$arr = explode('__~~__', $template);
		$template = $arr[0];
		@$target = $arr[1];
		
		$file_path = ALBUMS . DS . $album['path'];
		$path = DIR_HOST . '/albums/' . $album['path'];
		
		$source = $path . '/lg/' . $src;
		$specs = getimagesize($file_path . DS . 'lg' . DS . $src);
		
		$path = DIR_HOST . '/albums/' . $album['path'];
		$template = r('[full_hr_url]', $source, $template);
		$template = r('[img_w]', $specs[0], $template);
		$template = r('[img_h]', $specs[1], $template);
		$template = r('[img_src]', $src, $template);
		$template = r('[img_title]', $image['title'], $template);
		$template = r('[img_caption]', $image['caption'], $template);
		$template = r('[album_name]', $album['name'], $template);
		
		$arr = unserialize($image['anchor']);
		if (empty($arr)) {
			$arr['x'] = $arr['y'] = 50;
		}
		
		$template = preg_replace('/\[width:(\d+),height:(\d+),crop:(\d),quality:(\d+),sharpening:(\d)\]/e', "p('$src', '{$album['path']}', \\1, \\2, \\3, \\4, \\5, {$arr['x']}, {$arr['y']})", $template);
		return array($template, $target);
	}
	
	function _date($format, $date, $tz = true) {
		setlocale(LC_TIME, explode(',', __('en_US', true)));
		if (strpos($date, '-') !== false) {
			$date = strtotime($date);
		}
		if ($tz) {
			$offset = $_COOKIE['dir_time_zone'];
			$date = $date + $offset;
		}
		return r('  ', ' ', strftime($format, $date));
	}
	
	
	function formTitle($image, $album) {
		$path = ALBUMS . DS . $album['path'] .  DS . 'lg' . DS . $image['src'];
		return $this->_form($album['title_template'], $path, $image, $album);
	}
	
	
	function formCaption($image, $album) {
		$path = ALBUMS . DS . $album['path'] .  DS . 'lg' . DS . $image['src'];
		return $this->_form($album['caption_template'], $path, $image, $album);
	}
	
	function parseMetaTags($template, $data, $empty = 'Unknown') {
		$bits = explode(':', $template);
		if ($bits[0] == 'iptc') {
			if (isset($data['IPTC'])) {
				$iptc = $data['IPTC'];
				switch($template) {
					case 'iptc:credit':
						@$tag = $iptc['2#110'];
						break;
					case 'iptc:category':
						@$tag = $iptc['2#050'];
						break;				
					case 'iptc:keywords':
						@$tag = $iptc['2#025'];
						if (is_array($tag)) {
							$tag = join(' ', $tag);
						}
						break;
					case 'iptc:byline':
						@$tag = $iptc['2#080'];
						if (is_array($tag)) {
							$tag = $tag[0];
					 	}
						if (strpos($tag, 'Picasa') !== false) {
							$tag = '';
						}
						break;
					case 'iptc:byline title':
						@$tag = $iptc['2#085'];
						break;
					case 'iptc:city':
						@$tag = $iptc['2#090'];
						break;	
					case 'iptc:state':
						@$tag = $iptc['2#095'];
						break;
					case 'iptc:country':
						@$tag = $iptc['2#101'];
						break;				
					case 'iptc:headline':
						@$tag = $iptc['2#105'];
						break;
					case 'iptc:title':
						@$tag = $iptc['2#005'];
						break;
					case 'iptc:source':
						@$tag = $iptc['2#115'];
						break;				
					case 'iptc:copyright':
						@$tag = $iptc['2#116']; 
						break;
					case 'iptc:contact':
						@$tag = $iptc['2#118'];
						break;
					case 'iptc:caption':
						@$tag = $iptc['2#120'];
						break;
				}
			}

			if (isset($tag)) {
				if (!empty($tag)) {
					if (is_array($tag)) {
						$tag = $tag[0];
				 	}
					if (function_exists('mb_detect_encoding')) {
						$encoding = mb_detect_encoding($tag);
					} else {
						$encoding = 'UTF-8';
					}
					if (is_string($tag)) {
						$tag = str_replace("\xA9", "&copy;", $tag);
						switch ($encoding) {
							case 'ASCII':
								$tag = $tag;
								break;
							case 'UTF-8':
								$tag = utf8_encode($tag);
								break;
							default:
								$tag = iconv('MacRoman', 'UTF-8', $tag);
								break;
						}
						return $tag;
					} else {
						return '';
					}
				} else {
					return $empty;
				}
			} else {
				return '';
			}
		} else {
			if (isset($data['Exif']['EXIF'])) {
			$exif = $data['Exif']['EXIF'];
			switch($template) {
				case 'exif:make':
					return @$data['Exif']['IFD0']['Make'];
					break;
				case 'exif:title':
					return @$data['Exif']['IFD0']['ImageDescription'];
					break;
				case 'exif:comment':
					return @$data['Exif']['COMPUTED']['UserComment'];
					break;
				case 'exif:model':
					return @$data['Exif']['IFD0']['Model'];
					break;
				case 'exif:exposure':
					return @$exif['ExposureTime'];
					break;
				case 'exif:iso':
					return @$exif['ISOSpeedRatings'];
					break;
				case 'exif:aperture':
					return @$this->exif_frac2dec($exif['FNumber']);
					break;
				case 'exif:focal length':
					return @$this->exif_frac2dec($exif['FocalLength']);
					break;
				case 'exif:exposure mode':
					if (isset($exif['ExposureMode'])) {
						switch($exif['ExposureMode']) {
							case 0: return 'Easy shooting'; break;
							case 1: return 'Program'; break;
							case 2: return 'Tv-priority'; break;
							case 3: return 'Av-priority'; break;
							case 4: return 'Manual'; break;
							case 5: return 'A-DEP'; break;
							default: return 'Unknown'; break;
						}
					} else {
						return 'Unknown';
					}
					break;
				case 'exif:exposure bias':
					if (isset($exif['ExposureBiasValue'])) {
						list($n, $d) = explode('/', $exif['ExposureBiasValue']);
						if (!empty($n)) {
							return $exif['ExposureBiasValue'] . ' EV';
						} else {
							return '0 EV';
						}
						return $this->exif_frac2dec($exif['ExposureBiasValue']) . ' EV';
					} else {
						return 'Unknown';
					}
					break;	
				case 'exif:metering mode':
					if (isset($exif['MeteringMode'])) {
						switch($exif['MeteringMode']) {
							case 0: return 'Unknown'; break;
							case 1: return 'Average'; break;
							case 2: return 'Center Weighted Average'; break;
							case 3: return 'Spot'; break;
							case 4: return 'Multi-Spot'; break;
							case 5: return 'Multi-Segment'; break;
							case 6: return 'Partial'; break;
							case 255: return 'Other'; break;
						}
					} else {
						return 'Unknown';
					}
					break;
				case 'exif:white balance':
					if (isset($exif['WhiteBalance'])) {
						switch($exif['WhiteBalance']) {
							case 0: return 'Auto'; break;
							case 1: return 'Sunny'; break;
							case 2: return 'Cloudy'; break;
							case 3: return 'Tungsten'; break;
							case 4: return 'Fluorescent'; break;
							case 5: return 'Flash'; break;
							case 6: return 'Custom'; break;
							case 129: return 'Manual'; break;
						}
					} else {
						return 'Unknown';
					} 
					break;
				case 'exif:flash simple':
					if (isset($exif['Flash'])) {
						if (in_array($exif['Flash'], array(0,16,24,32))) {
							return 'Flash did not fire';
						} else {
							return 'Flash fired';
						}
					} else {
						return 'Unknown';
					}
					break;
				case 'exif:flash':
					if (isset($exif['Flash'])) {
						switch($exif['Flash']) {
							case 0: return 'No Flash'; break;
							case 1: return 'Flash'; break;
							case 5: return 'Flash, strobe return light not detected'; break;
							case 7: return 'Flash, strob return light detected'; break;
							case 9: return 'Compulsory Flash'; break;
							case 13: return 'Compulsory Flash, Return light not detected'; break;
							case 16: return 'No Flash'; break;
							case 24: return 'No Flash'; break;
							case 25: return 'Flash, Auto-Mode'; break;
							case 29: return 'Flash, Auto-Mode, Return light not detected'; break;
							case 31: return 'Flash, Auto-Mode, Return light detected'; break;
							case 32: return 'No Flash'; break;
							case 65: return 'Red Eye'; break;
							case 69: return 'Red Eye, Return light not detected'; break;
							case 71: return 'Red Eye, Return light detected'; break;
							case 73: return 'Red Eye, Compulsory Flash'; break;
							case 77: return 'Red Eye, Compulsory Flash, Return light not detected'; break;
							case 79: return 'Red Eye, Compulsory Flash, Return light detected'; break;
							case 89: return 'Red Eye, Auto-Mode'; break;
							case 93: return 'Red Eye, Auto-Mode, Return light not detected'; break;
							case 95: return 'Red Eye, Auto-Mode, Return light detected'; break;
							default: return 'Unknown'; break;
						}
					} else {
						return 'Unknown';
					}
					break;
			}
			}
		}
	}
	
	function exif_frac2dec($str) {
		@list( $n, $d ) = explode( '/', $str );
		if ( !empty($d) )
			return $n / $d;
		return $str;
	}
	
	function _form($field, $path, $image, $album) {
		$set_to = str_replace('[director:image filename]', $image['src'], $field);
		$set_to = str_replace('[director:album name]', $album['name'], $set_to);
		$set_to = str_replace('[director:image number]', $image['seq'], $set_to);
		$set_to = str_replace('[director:image count]', $album['images_count'], $set_to);
		$set_to = str_replace('[director:tags]', $image['tags'], $set_to);
		
		if (strpos($field, '[director:date captured') !== false) {
			preg_match_all('/\[director:date captured(:.*?)?\]/', $field, $matches);
			for ($i = 0; $i < count($matches[0]); $i++) {
				$t = $matches[1][$i];
				if (empty($t)) {
					$t = '%m/%d/%Y %I:%M%p';
				} else {
					$t = ltrim($t, ':');
				}
				if (empty($image['captured_on'])) {
					$set_to = str_replace($matches[0][$i], '', $set_to);
				} else {
					$set_to = str_replace($matches[0][$i], $this->_date($t, $image['captured_on'], false), $set_to);
				}
			}		
		}
		list($data, $dummy) = $this->imageMetadata($path);
		foreach($this->iptcTags as $meta) {
			$value = $this->parseMetaTags("iptc:$meta", $data);
			@$set_to = str_replace("[iptc:$meta]", $value, $set_to);
		}
		
		
		foreach($this->exifTags as $meta) {
			$value = $this->parseMetaTags("exif:$meta", $data);
			$set_to = str_replace("[exif:$meta]", $value, $set_to);
		}
		
		return $set_to;
	}
	
	function imageMetadata($path) {
		$meta = array();
		$captured_on = null;
		$meta_s = null;
		
		if (!isNotImg(basename($path))) {
			$meta = array();
			if (is_callable('iptcparse')) {
				getimagesize($path, $info);
				if (!empty($info['APP13'])) {
					$meta['IPTC'] = iptcparse($info['APP13']);
				}
				if (!empty($iptc['2#055'][0]) && !empty($iptc['2#060'][0])) {
					$captured_on = strtotime($iptc['2#055'][0] . ' ' . $iptc['2#060'][0]);
				}
			}
			
			if (eregi('\.jpg|\.jpeg', basename($path)) && is_callable('exif_read_data')) {
				$exif_data = exif_read_data($path, 0, true);
				$meta['Exif'] = $exif_data;
				if (isset($meta['Exif']['EXIF']['DateTimeDigitized'])) {
					$dig = $meta['Exif']['EXIF']['DateTimeDigitized'];
					$bits = explode(' ', $dig);
					$captured_on = strtotime(str_replace(':', '-', $bits[0]) . ' ' . $bits[1]);
				}
			}
			
			// if (!empty($meta)) {
			// 	$meta_s = serialize($meta);
			// }
		}
		return array($meta, $captured_on);			
	}
}

?>