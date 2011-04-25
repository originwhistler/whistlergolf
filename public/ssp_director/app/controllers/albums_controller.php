<?php

class AlbumsController extends AppController {
    var $name = 'Albums';
	var $uses = array('Album', 'Image', 'Tag', 'User', 'Gallery');
	var $helpers = array('Html', 'Javascript', 'Ajax');
	
	var $non_ajax_actions = array('index', 'edit', 'reorder');
	var $paginate = array('limit' => 50, 'page' => 1, 'order' => array('name' => 'asc')); 
		
	// Only logged in users should see this controller's actions
 	function beforeFilter() {
		// Protect ajax actions
		if (!in_array($this->action, $this->non_ajax_actions)) {
			$this->verifyAjax();
		}
		// Check session
		$this->checkSession();		
	}
	
	////
	// Albums listing
	////
	function index() {
		$this->set('writable', $this->Director->setPerms(ALBUMS));
		$filters = array();
		$params = $this->params;
		$page = 1;
		
		if ($this->RequestHandler->isAjax()) { 
			$this->set('empty', true);

			if (isset($this->data['Album']['search'])) {
				$search = $this->data['Album']['search'];
			} elseif ($this->Session->check('Album.search')) {
				$search = $this->Session->read('Album.search');
			}

			if (isset($search)) {
				if (empty($search)) {
					$this->Session->del('Album.search');
				} else {
					$filters[] = "(lower(Album.name) like '%" . low($search) . "%' OR lower(Album.description) like '%" . low($search) . "%')"; 
					$this->Session->write('Album.search', $search);
					$this->data['Album']['search'] = $search;
				}
			}

			$active = 2;
			if (isset($this->data['Album']['active'])) {
				if ($this->data['Album']['active'] == 2) {
					$this->Session->del('Album.active');
				}
				$active = $this->data['Album']['active'];
			} elseif ($this->Session->check('Album.active')) {
				$active = $this->Session->read('Album.active');
				$this->data['Album']['active'] = $active;
			}

			if ($active != 2) {
				$filters[] = "Album.active = " . $active;
				$this->Session->write('Album.active', $active);
			}
			
			if (isset($params['named']['page'])) {
				$page = $params['named']['page'];
				$this->Session->write('Album.page', $page);
			} elseif ($this->Session->check('Album.page')) {
				$page = $this->Session->read('Album.page');
			}
		} else {
			$this->Session->del('Album.search');
			$this->Session->del('Album.active');
			$this->Session->del('Album.page');
			$this->set('empty', false);
		}
		
		if (isset($params['named']['sort'])) {
			$sort = $params['named']['sort'];
			$dir = $params['named']['direction'];
			$this->Cookie->write('Album.sort', "$sort $dir", true, 32536000);
		} elseif ($this->Cookie->read('Album.sort')) {
			$val = $this->Cookie->read('Album.sort');
			list($sort, $dir) = explode(' ', $val);
		}
		if (isset($sort)) {
			$this->paginate = array_merge($this->paginate, array('order' => array($sort => $dir)));
		}
		
		$this->paginate = array_merge($this->paginate, array('page' => $page));
		$this->Album->recursive = -1;
		$this->set('albums', $this->paginate('Album', $filters));
		if ($this->RequestHandler->isAjax()) { 
			$this->render('list', 'ajax');
		}
	}
	
	////
	// Create album
	////
	function create() {
		if ($this->Album->save($this->data)) {
			// Make directories and set path
			$this->Album->id = $this->Album->getLastInsertId();
			$path = 'album-' . $this->Album->id;
			if ($this->Director->makeDir(ALBUMS . DS . $path) &&
				$this->Director->createAlbumDirs($path))
			{
				// Directories were created successfully, go ahead with new album and redirection
				$this->Album->saveField('path', $path);
				
				if (isset($this->data['quick'])) {
					$this->set('all_albums', $this->Album->findAll(null, null, 'name', null, 1, -1));
				} elseif (isset($this->data['dash'])) {
					$recent = $this->Album->findAll(null, null, 'Album.modified_on DESC', 5, 1, -1);
					$this->set('albums', $recent);
				}
				
				// Render redirect via JS
				$this->set('new_id', $this->Album->id);
				$this->set('tab', 'upload');
				$this->render('after_create', 'ajax');
			} else {
				// Directory creation failed, we have a permission problem. Delete the album and notify user
				$this->Album->delete();
				$this->render('creation_failure', 'ajax');
			}
		}	
	}
	
	////
	// Album edit pane
	////
	function edit($id, $tab = 'summary', $part_id = 0) {
		$this->pageTitle = __('Albums', true);
		$this->Album->id = $id;
		$this->Album->recursive = 2;
		$this->data = $this->Album->read();
		
		switch($tab) {
			case('summary'):
				$this->set('recent_images', $this->Image->findAll(aa('aid', $id, 'Image.active', 1), null, 'Image.created_on DESC', 9));
				break;
				
			case('options'):
				$this->set("title_check", $this->Image->findAll("aid = $id AND title IS NOT NULL AND title <> ''"));
				$this->set("link_check", $this->Image->findAll("aid = $id AND Image.link IS NOT NULL AND Image.link <> ''"));
				$this->set("captions_check", $this->Image->findAll("aid = $id AND caption IS NOT NULL AND caption <> ''"));
				$templates_folder = new Folder(PLUGS . DS . 'links');
				$link_templates = $templates_folder->ls(true, false);
				$this->set('link_templates', $link_templates[1]);
				$custom_templates_folder = new Folder(CUSTOM_PLUGS . DS . 'links');
				$custom_link_templates = $custom_templates_folder->ls(true, false);
				$this->set('custom_link_templates', $custom_link_templates[0]);
				$iptcs = $this->Director->iptcTags;
				natsort($iptcs);
				$exifs = $this->Director->exifTags;
				natsort($exifs);
				$dirs = $this->Director->dirTags;
				natsort($dirs);
				$this->set('iptcs', $iptcs);
				$this->set('exifs', $exifs);
				$this->set('dirs', $dirs);
				break;
				
			case('content'):
				$this->set('images', $this->data['Image']);
				$this->set('selected_id', $part_id);
				if (function_exists('imagerotate') || $this->Kodak->gdVersion() >= 3) {
					$rotate = true;
				} else {
					$rotate = false;
				}
				$this->set('rotate', $rotate);
				break;
			
			case('audio'):
				$this->set('mp3s', $this->Director->directory(AUDIO, 'mp3,MP3'));
				break;
				
			case('upload'):
				$this->set('writable', $this->Director->setAlbumPerms($this->data['Album']['path']));
				$this->set('other_writable', $this->Director->setOtherPerms());
				// Check if any new files have been uploaded via FTP
				$files = $this->Director->directory(ALBUMS . DS . $this->data['Album']['path'] . DS . 'lg', 'accepted');
				if (count($files) > count($this->data['Image'])) {
					set_time_limit(0);
					$noobs = array();
					$n = 1;
					foreach($files as $file) {
						if (strpos($file, '___tn___') === false) {
							$this->Image->recursive = -1;
							$this->Image->coldSave = true;
							$img = $this->Image->find(aa('src', $file, 'aid', $id));
							if (empty($img)) {
								$clean = str_replace(" ", "_", $file);
								$clean = ereg_replace("[^A-Za-z0-9._-]", "_", $clean);
								$path = ALBUMS . DS . $this->data['Album']['path'] . DS . 'lg' . DS . $file;
								$clean_path = ALBUMS . DS . $this->data['Album']['path'] . DS . 'lg' . DS . $clean;
								if (rename($path, $clean_path)) {
									$path = $clean_path;
									$file = $clean;
								}
								list($meta, $captured_on) = $this->Director->imageMetadata($path);
								$new['Image']['aid'] = $id;
								$new['Image']['src'] = $file;
								$new['Image']['seq'] = $this->data['Album']['images_count'] + $n;
								$new['Image']['filesize'] = filesize($path);
								$new['Image']['meta'] = $meta;
								$new['Image']['captured_on'] = $captured_on;
								$this->Image->create();
								if ($this->Image->save($new)) {
									$noobs[] = $file;
								}
								$n++;
							}
							$this->Image->coldSave = false;
						}
					}
					$this->Album->id = $this->data['Album']['id'];
					$this->Album->reorder($id);
					$this->Album->cacheQueries = false;
					$this->data = $this->Album->read();
					$this->Album->saveField('images_count', count($this->data['Image']));
					$this->set('noobs', $noobs);
				}
				break;
		}
		
		$this->Album->recursive = -1;
		$this->set('all_albums', $this->Album->findAll(null, null, 'name'));
		$this->Album->recursive = -1;
		$this->set('other_albums', $this->Album->findAll("id <> $id", null, 'name'));
		$this->set('album', $this->data);
		$this->set('tab', $tab);
		$this->set('thumbs', !empty($this->data['Album']['thumb_specs']));
	}
	
	////
	// Update album
	////
	function update($id, $refer = '') {
		$this->Album->id = $id;
		if ($this->Album->save($this->data)) {
			$album = $this->Album->read();
			$this->set('album', $album);
		}
	}
	
	////
	// Delete an album
	////
	function delete() {
		$album = $this->Album->read(null, $this->data['Album']['id']);
		$albums = $this->Album->findAll("path = '{$album['Album']['path']}'");
		
		// Delete the album from the DB
		if ($this->Album->del($album['Album']['id'], true)) {
			if (!empty($album['Album']['path'])) {
				// Remove the directory only if no other albums use it
				if (count($albums) == 1) {
					$dir = ALBUMS . DS . $album['Album']['path'];
					$this->Director->rmdirr($dir);
				}
			}
			$this->redirect('/albums/index');
		}
	}
	
	////
	// Toggles albums active and inactive
	////
	function toggle($id) {
		$this->Album->id = $id;
		$album = $this->Album->read();
		if ($this->Album->save($this->data)) {
			if ($this->data['Album']['active']) {
				if (!$album['Album']['active']) {
					$main = $this->Gallery->find(aa('main', 1));
					$tag['Tag']['did'] = $main['Gallery']['id'];
					$tag['Tag']['aid'] = $id;
					$this->Tag->save($tag);
				}	
			} else {
				$this->Tag->deleteAll("WHERE aid = $id", false, true);
			}
		}
		$this->Album->recursive = 2;
		$album = $this->Album->read();
		$this->set('album', $album);
		$this->set('updated_by', $this->User->find(aa('id', $album['Album']['updated_by'])));
		$this->set('created_by', $this->User->find(aa('id', $album['Album']['created_by'])));
	}
	
	////
	// Reset order type and refresh the image order as needed
	////
	function order_type($id) {
		$this->Album->id = $id;
		$this->Album->save($this->data);
		$this->Album->cacheQueries = false;
		if ($this->Album->reorder($id)) {
			$this->Album->recursive = 1;
			$this->data = $this->Album->read();
			$this->set('images', $this->data['Image']);
			$this->set('album', $this->data);
			$this->set('tab', 'images');
			if (function_exists('imagerotate') || $this->Kodak->gdVersion() >= 3) {
				$rotate = true;
			} else {
				$rotate = false;
			}
			$this->set('rotate', $rotate);
			$this->Album->recursive = -1;
			$this->set('all_albums', $this->Album->findAll(null, null, 'name'));
			$this->Album->recursive = -1;
			$this->set('other_albums', $this->Album->findAll("id <> $id", null, 'name'));
			$this->render('order_type', 'ajax');
		}
	}
	
	////
	// Reorder album after image upload
	////
	function reorder($id) {
		if ($this->Album->reorder($id)) {
			$this->redirect("/albums/edit/$id/content");
			exit;
		}
	}
}

?>