<?php

class ApiController extends AppController {

	var $name = 'Api';
	var $components = array('RequestHandler', 'Director');
	var $uses = '';
	var $helpers = array('Api', 'Director', 'Xml');
	var $version = '1.0.0';
	var $atbl, $itbl, $dtbl, $dltbl, $utbl;
	var $disableSessions = true;
	
	function beforeFilter() {
		$this->layout = 'xml';
		$this->RequestHandler->respondAs('xml');
		App::import('Model', 'Account');
		$this->Account =& new Account();
		$this->Account->recursive = -1;
		$this->account = $this->Account->find();
		$api_key = $this->account['Account']['api_key'];
		if (empty($this->data['api_key']) || ($this->data['api_key'] != $api_key)) {
			$this->_error('Invalid API key');
		}
		if (!isset($this->data['size'])) {
			$this->data['size'] = array();
		}
		
		if (!isset($this->data['user_size'])) {
			$this->data['user_size'] = array();
		}
		
		if (!isset($this->data['preview'])) {
			$this->data['preview'] = '';
		}
		
		if (!isset($this->data['only_active'])) {
			$only_active = true;
		} else {
			$only_active = $this->data['only_active'];
		}
		$this->set('active', $only_active);
		$this->set('controller', $this);
		$this->set('users', $this->Director->fetchUsers());
		$this->atbl = DIR_DB_PRE . 'albums';
		$this->itbl = DIR_DB_PRE . 'images';
		$this->dtbl = DIR_DB_PRE . 'dynamic';
		$this->dltbl = DIR_DB_PRE . 'dynamic_links';
		$this->utbl = DIR_DB_PRE . 'usrs';
		
		if (isset($this->data['invalidator'])) {
			$this->_cache_invalidator($this->data['invalidator'], $this->action);
		}
	}
	
	function _error($msg) {
		$this->set('error', $msg);
		e($this->render('error.xml'));
		exit;
	}

	function auth() {
		App::import('Model', 'User');
		$this->User =& new User();
		$user = $this->User->findByUsr($this->data['username']);
		
		if (empty($user)) {
			$this->_error('User not found');
		} else {
			if ($user['User']['pwd'] != $this->data['password']) {
				$this->_error('Password incorrect');
			} else {
				$this->set('user', $user);
				$this->render('user.xml');
			}
		}
	}
	
	////
	// APP
	////
	
	function app_version() {
		$this->set('version', $this->version);
		$this->render('app/version.xml');
	}
	
	function app_totals() {
		App::import('Model', 'Image');
		$this->Image =& new Image();
		$result = $this->Image->query("SELECT COUNT({$this->itbl}.id) as image_count, SUM(filesize) as total_size FROM {$this->itbl} WHERE src IS NOT NULL");
		$this->set('data', $result[0][0]);
		$this->render('app/totals.xml');
	}
	
	function app_limits() {
		list($max, $post_max_broken) = $this->Director->uploadLimit();
		$this->set('max', $max);
		$this->render('app/limits.xml');
	}
	
	////
	// ALBUM
	////
	
	function get_album() {
		if (!isset($this->data['album_id'])) {
			$this->_error('Required parameter \'album_id\' is missing.');
		}
		$album = $this->_cache('get_album_' . $this->data['album_id']);
		if (empty($album)) {
			App::import('Model', 'Album');
			$this->Album =& new Album();
			$this->Album->bindPreview();
			$album = $this->Album->read(null, $this->data['album_id']);
			$this->_cache('get_album_' . $this->data['album_id'], $album);
		}
		if (empty($album)) {
			$this->_error('No album found with an id of ' . $this->data['album_id']);
		}
		$this->set('album', $album);
		$this->render('album/get.xml');
	}
	
	function get_album_list() {
		$cache_key = 'get_albums_list_';
		if (isset($this->data['only_published']) && $this->data['only_published']) {
			$params = aa('Album.active', 1);
			$cache_key .= '1';
		} else {
			$params = '';
			$cache_key .= '0';
		}
		
		if (isset($this->data['list_only']) && $this->data['list_only']) {
			$list_only = true;
			$cache_key .= '_1';
		} else {
			$list_only = false;
			$cache_key .= '_0';
		}
		
		$albums = $this->_cache($cache_key);
		if (empty($albums)) {
			App::import('Model', 'Album');
			$this->Album =& new Album();
			if ($list_only) {
				$this->Album->unbindModel(array('hasMany' => array('Image', 'Tag')));
			}
			$this->Album->bindPreview();
			$albums = $this->Album->findAll($params, null, 'name');
			$this->_cache($cache_key, $albums);
		}
		$this->set('albums', $albums);
		$this->render('album/list.xml');
	}
	
	function get_associated_galleries() {
		if (!isset($this->data['album_id'])) {
			$this->_error('Required parameter \'album_id\' is missing.');
		}
		$cache_key = 'get_associated_galleries_' . $this->data['album_id'] . '_' . $this->data['exclude'];
		$galleries = $this->_cache($cache_key);
		if (empty($galleries)) {
			App::import('Model', 'Gallery');
			$this->Gallery =& new Gallery();
			$q = "SELECT did FROM {$this->dltbl} AS Tag WHERE aid = {$this->data['album_id']}";
			if (!empty($this->data['exclude']) && is_numeric($this->data['exclude'])) {
				$q .= ' AND did != ' . $this->data['exclude'];
			}
			$data = $this->Gallery->query($q);
			$ids = array();
			foreach($data as $d) {
				$ids[] = $d['Tag']['did'];
			}
			$this->Gallery->recursive = -1;
			$galleries = $this->Gallery->findAll(aa('id', $ids), null, 'name');
			$this->_cache($cache_key, $galleries);
		}
		$this->set('galleries', $galleries);
		$this->render('gallery/list.xml');
	}
		
	function create_album() {
		App::import('Model', 'Album');
		$this->Album =& new Album();
		if ($this->Album->save($this->data)) {
			// Make directories and set path
			$this->Album->id = $this->Album->getLastInsertId();
			$path = 'album-' . $this->Album->id;
			if ($this->Director->makeDir(ALBUMS . DS . $path) &&
				$this->Director->createAlbumDirs($path))
			{
				// Directories were created successfully, go ahead with new album and redirection
				$this->Album->saveField('path', $path);
			}
			$this->set('album_id', $this->Album->id);
			$this->render('album/create.xml');
		}
	}
	
	////
	// CONTENT
	////
		
	function get_content_list() {
		$sort_options = array('created_on', 'modified_on', 'captured_on', 'filename', 'random');
		$sort_order = array('DESC', 'ASC');
		
		if (isset($this->data['sort_on']) && in_array($this->data['sort_on'], $sort_options)) {
			$sort = $this->data['sort_on'];
		} else {
			$sort = 'created_on';
		}
		
		if (isset($this->data['sort_direction']) && in_array($this->data['sort_direction'], $sort_order)) {
			$sort_direction = $this->data['sort_direction'];
		} else {
			$sort_direction = 'DESC';
		}
		
		if (isset($this->data['limit']) && is_numeric($this->data['limit'])) {
			$limit = $this->data['limit'];
		} else {
			$limit = 0;
		}
		
		if (isset($this->data['only_images']) && $this->data['only_images'] == 1) {
			$only_images = 1;
		} else {
			$only_images = 0;
		}
		
		if (isset($this->data['tags']) && !empty($this->data['tags'])) {
			$tags = '_' . str_replace(',', '_', $this->data['tags']);
			$tag_query = " AND (Image.tags LIKE ";
			$tags_arr = array();
			foreach(explode(',', $this->data['tags']) as $t) {
				$tags_arr[] = "'%" . addslashes($t) ."%'";
			}
			if (isset($this->data['tags_exclusive']) && $this->data['tags_exclusive']) {
				$tag_query .= implode(' AND Image.tags LIKE ', $tags_arr) . ')';
				$tags .= '_1';
			} else {
				$tag_query .= implode(' OR Image.tags LIKE ', $tags_arr) . ')';
				$tags .= '_0';
			}
		} else {
			$tags = '';
			$tag_query = '';
		}
		
		if (isset($this->data['scope']) && isset($this->data['scope_id'])) {
			$model = $this->data['scope'];
			$model_id = $this->data['scope_id'];
			$scope_cache = '_' . $model . '_' . $model_id;
			if ($model == 'gallery') {
				App::import('Model', 'Gallery');
				$this->Gallery =& new Gallery();
				$g = $this->Gallery->find(aa('Gallery.id', $model_id));
				$album_ids = array();
				if (!empty($g['Tag'])) {
					foreach($g['Tag'] as $tag) {
						$album_ids[] = $tag['aid'];
					}
					$id_str = implode(',', $album_ids);
				}
			} else {
				$id_str = $model_id;
			}
			if (isset($id_str)) {
			 	$scope_query = " AND Album.id IN ($id_str)";
			} else {
				$scope_query = ' AND Album.id IN (0)';
			}
		} else {
			$scope_cache = '';
			$scope_query = '';
		}
		
		$cache_key = 'get_content_list_' . $sort . '_' . $sort_direction . '_' . $limit . '_' . $only_images . $tags . $scope_cache;
		
		if ($sort == 'random') {
			$cache_key .= '_' . rand(1,10);
		}
		
		$result = $this->_cache($cache_key);
		if (empty($result)) {
			App::import('Model', 'Image');
			$this->Image =& new Image();
			$q = "SELECT Image.*, Album.path FROM {$this->itbl} AS Image, {$this->atbl} AS Album WHERE Image.aid = Album.id";
			if ($only_images) {
				$q .= " AND (Image.src LIKE '%.gif' OR Image.src LIKE '%.jpg' OR Image.src LIKE '%.jpeg' OR Image.src LIKE '%.png')";
			}
			$q .= $tag_query . $scope_query;
			if ($sort == 'random') {
				$q .= ' ORDER BY RAND()';
			} else {
				$q .= " ORDER BY Image.{$sort} $sort_direction";
			}
			if ($limit !== 0) {
				$q .= ' LIMIT ' . $limit;
			}
			$result = $this->Image->query($q);
			$this->_cache($cache_key, $result);
		}
		$this->set('data', $result);
		$this->render('content/list.xml');
	}
	
	function get_content() {
		if (!isset($this->data['content_id'])) {
			$this->_error('Required parameter \'content_id\' is missing.');
		}
		$cache_key = 'get_content_' . $this->data['content_id'];
		$image = $this->_cache($cache_key);
		if (empty($image)) {
			App::import('Model', 'Image');
			$this->Image =& new Image();
			$image = $this->Image->read(null, $this->data['content_id']);
			if (empty($image)) {
				$this->_error('No content found with an id of ' . $this->data['content_id']);
			}
			$this->_cache($cache_key, $image);
		}
		$this->set('image', $image);
		$this->render('content/get.xml');
	}

	function upload() {
		$src = $_FILES['data']['name']['photo'];
		$tmp = $_FILES['data']['tmp_name']['photo'];
		$src = str_replace(" ", "_", $src);
		$src = ereg_replace("[^A-Za-z0-9._-]", "_", $src);
		
		App::import('Model', 'Image');
		$this->Image =& new Image();
		
		$album = $this->Image->Album->read(null, $this->data['Album']['id']);
		$dest = ALBUMS . DS . $album['Album']['path'] . DS . 'lg' . DS . $src;
		
		if (!allowableFile($src)) {
			$this->_error("$src is not an allowed file type.");
		}
		
		$check = $this->Image->find("aid = {$this->data['Album']['id']} AND src = '$src'");
		if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
			list($meta, $captured_on) = $this->Director->imageMetadata($dest);
			if (empty($check)) {
				$top = $this->Image->find('first', array('conditions' => "aid = {$this->data['Album']['id']}", 'order' => 'seq DESC'));
				$next = $top['Image']['seq'] + 1;
				$data['Image']['title'] = $this->data['Image']['title'];
				$data['Image']['caption'] = $this->data['Image']['caption'];
				$data['Image']['tags'] = $this->data['Image']['tags'];
				$data['Image']['src'] = $src;
				$data['Image']['aid'] = $album['Album']['id'];
				$data['Image']['seq'] = $next;
				$data['Image']['created_by'] = 	$data['Image']['updated_by'] = $this->data['User']['id'];
				$data['Image']['captured_on'] = $captured_on;
				$data['Image']['filesize'] = filesize($dest);
				$this->Image->save($data);
				$this->Image->Album->reorder($this->data['Album']['id']);
				$this->set('image_id', $this->Image->getLastInsertId());
			} else {
				$image_id = $check['Image']['id'];
				$caches = glob(ALBUMS . DS . $check['Album']['path'] . DS . 'cache' . DS . $check['Image']['src'] . '*');
				if (!empty($caches)) {
					foreach($caches as $cache) {
						@unlink($cache);
					}
				}
				$this->Image->id = $image_id;
				$data['Image']['captured_on'] = $captured_on;
				$data['Image']['filesize'] = filesize($dest);
				$data['Image']['tags'] = $this->data['Image']['tags'];;
				$this->Image->save($this->data);
				$this->set('image_id', $image_id);
			}
		}
		$this->render('content/upload.xml');
	}
	
	////
	// GALLERY
	////
	
	function get_gallery() {
		if (!isset($this->data['gallery_id'])) {
			$this->_error('Required parameter \'gallery_id\' is missing.');
		}
		$cache_key = 'get_gallery_' . $this->data['gallery_id'];
		list($gallery, $albums) = $this->_cache($cache_key);
		if (empty($gallery)) {
			App::import('Model', 'Gallery');
			$this->Gallery =& new Gallery();
			$gallery = $this->Gallery->read(null, $this->data['gallery_id']);
			if (empty($gallery)) {
				$this->_error('No gallery found with an id of ' . $this->data['gallery_id']);
			}
			$album_ids = array();
			foreach($gallery['Tag'] as $tag) {
				$album_ids[] = $tag['aid'];
			}
			if (!empty($album_ids)) {
				App::import('Model', 'Album');
				$this->Album =& new Album();
				$this->Album->bindPreview();
				$this->Album->unbindModel(array('hasMany' => array('Tags')));
				$albums = $this->Album->findAll(aa("Album.id", $album_ids), null, "FIELD(Album.id, " . join(',', $album_ids) . ")");
			} else {
				$albums = array();
			}
			$this->_cache($cache_key, array($gallery, $albums));
		}
		$this->set('gallery', $gallery);
		$this->set('albums', $albums);
		$this->render('gallery/get.xml');		
	}
	
	function get_gallery_list() {
		$cache_key = 'get_gallery_list';
		$galleries = $this->_cache($cache_key);
		if (empty($galleries)) {
			App::import('Model', 'Gallery');
			$this->Gallery =& new Gallery();
			$this->Gallery->recursive = 2;
			$galleries = $this->Gallery->findAll();
			$this->_cache($cache_key, $galleries);
		}
		$this->set('galleries', $galleries);
		$this->render('gallery/list.xml');
	}
	
	function get_users() {
		$sort = $this->data['user_sort'];
		$cache_key = 'get_users_' . $sort;
		if (isset($this->data['user_scope_model'])) {
			$model = $this->data['user_scope_model'];
			$model_id = $this->data['user_scope_id'];
			$model_all = (int) $this->data['user_scope_all'];
			$cache_key .= "_{$model}_{$model_id}_{$model_all}";
		} 
		
		$ids = $this->_cache($cache_key);
		if (empty($ids)) {
			App::import('Model', 'User');
			$this->User =& new User();
			if (isset($model)) {
				$ids = array();
				if ($model == 'gallery') {
					App::import('Model', 'Gallery');
					$this->Gallery =& new Gallery();
					$g = $this->Gallery->find(aa('Gallery.id', $model_id));
					$album_ids = array();
					if (!empty($g['Tag'])) {
						foreach($g['Tag'] as $tag) {
							$album_ids[] = $tag['aid'];
						}
						$id_str = implode(',', $album_ids);
					}
				} else {
					$id_str = $model_id;
				}
				if (isset($id_str)) {
					$q = "SELECT DISTINCT User.*, COUNT(Image.id) as cnt FROM {$this->utbl} as User, {$this->itbl} as Image WHERE User.id = Image.created_by AND Image.aid IN ($id_str) GROUP BY Image.created_by";
					$result = $this->User->query($q);
					if (!empty($result)) {
						foreach($result as $u) {
							$ids[] = array('id' => $u['User']['id'], 'last_name' => $u['User']['last_name'], 'first_name' => $u['User']['first_name'], 'usr' => $u['User']['usr'], 'count' => $u[0]['cnt']);
						}
					}
				}
			} else {
				$users = $this->User->findAll(null, null, 'last_name, first_name, usr');
				$ids = array();
				App::import('Model', 'Image');
				$this->Image =& new Image();
				foreach($users as $u) {
					if (is_array($u['User']['id'])) {
						$u['User']['id'] = $u['User']['id'][0];
					}
					$count = $this->Image->find('count', array('conditions' => 'Image.created_by = ' . $u['User']['id']));
					$ids[] = array('id' => $u['User']['id'], 'last_name' => $u['User']['last_name'], 'first_name' => $u['User']['first_name'], 'usr' => $u['User']['usr'], 'count' => $count);
				}
			}
			if (!empty($ids)) {
				foreach ($ids as $key => $row) {
					if (is_null($row['last_name'])) {
						$last_name[$key] = '';
					} else {
						$last_name[$key] = $row['last_name'];
					}
				    if (is_null($row['first_name'])) {
						$first_name[$key] = '';
					} else {
						$first_name[$key] = $row['first_name'];
					}
					$usr[$key]  = $row['usr'];
					$count_arr[$key]  = $row['count'];					
				}
				if ($this->data['user_sort'] == 'name') {
					array_multisort($last_name, SORT_ASC, $first_name, SORT_ASC, $usr, SORT_ASC, $count_arr, SORT_DESC, $ids);
				} else {
					array_multisort($count_arr, SORT_DESC, $last_name, SORT_ASC, $first_name, SORT_ASC, $usr, SORT_ASC, $ids);
				}
				$this->_cache($cache_key, $ids);
			}
		}
		$this->set('user_ids', $ids);
		$this->render('users/list.xml');
	}
	
	function _cache($key, $data = null) {
		$path = 'api' . DS . $key . '.cache';
		if (is_null($data)) {
			return unserialize(cache($path, null, '+1 year'));
		} else {
			cache($path, serialize($data));
		}
	}
	
	function _cache_invalidator($invalidator, $action) {
		$file = CACHE . 'api' . DS . 'invalidators' . DS . $action . '.cache';
		if (!is_dir(dirname($file))) {
			umask(0);
			mkdir(dirname($file), 0777, true);
		}
		if (file_exists($file)) {
			$contents = unserialize(file_get_contents($file));
		} else {
			$contents = array();
		}
		
		$path = $invalidator['path'];
		$name = $invalidator['name'];
		
		$exists = false;
		if (!empty($contents)) {
			foreach ($contents as $c) {
				if ($c['name'] == $name && $c['path'] == $path) {
					$exists = true;
				}
			}
		}	
		
		if (!$exists) {
			$contents[] = array('path' => $path, 'name' => $name);
			file_put_contents($file, serialize($contents));
		}
	}
}

?>