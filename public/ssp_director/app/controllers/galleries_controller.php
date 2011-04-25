<?php

class GalleriesController extends AppController {
	// Models needed for this controller
	var $uses = array('Album', 'Gallery', 'Tag');
	// Helpers
	var $helpers = array('Html', 'Javascript', 'Ajax');
    var $name = 'Galleries';

	var $non_ajax_actions = array('index', 'edit', '_memberData', 'refresh');
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
	// Galleries listing
	////
	function index() {
		$filters = array();
		$params = $this->params;
		$page = 1;
		
		if ($this->RequestHandler->isAjax()) { 
			$this->set('empty', true);

			if (isset($this->data['Gallery']['search'])) {
				$search = $this->data['Gallery']['search'];
			} elseif ($this->Session->check('Gallery.search')) {
				$search = $this->Session->read('Gallery.search');
			}

			if (isset($search)) {
				if (empty($search)) {
					$this->Session->del('Gallery.search');
				} else {
					$filters[] = "(lower(Gallery.name) like '%" . low($search) . "%' OR lower(Gallery.description) like '%" . low($search) . "%')"; 
					$this->Session->write('Gallery.search', $search);
					$this->data['Gallery']['search'] = $search;
				}
			}
			
			if (isset($params['named']['page'])) {
				$page = $params['named']['page'];
				$this->Session->write('Gallery.page', $page);
			} elseif ($this->Session->check('Gallery.page')) {
				$page = $this->Session->read('Gallery.page');
			}
		} else {
			// Available imports?
			$imports = $this->Director->checkImports();
			if (empty($imports) || !$imports) {
				$this->set('imports', false);
			} else {
				$this->set('imports', $imports);
			}
			$this->Session->del('Gallery.search');
			$this->Session->del('Gallery.page');
		}
		
		if (isset($params['named']['sort'])) {
			$sort = $params['named']['sort'];
			$dir = $params['named']['direction'];
			$this->Cookie->write('Gallery.sort', "$sort $dir", true, 32536000);
		} elseif ($this->Cookie->read('Gallery.sort')) {
			$val = $this->Cookie->read('Gallery.sort');
			list($sort, $dir) = explode(' ', $val);
		}
		if (isset($sort)) {
			$this->paginate = array_merge($this->paginate, array('order' => array($sort => $dir)));
		}
		
		$this->paginate = array_merge($this->paginate, array('page' => $page));
		$this->Gallery->recursive = -1;
		$this->set('galleries', $this->paginate('Gallery', $filters));
		if ($this->RequestHandler->isAjax()) { 
			$this->render('list', 'ajax');
		}
	}
	
	////
	// Create a new gallery
	////	
	function create() {
		if ($this->Gallery->save($this->data)) {
			if ($this->data['redirect'] == 2) {
				$this->set('galleries', $this->Gallery->findAll(null, null, 'Gallery.modified_on DESC', 5));
			} else if ($this->data['redirect'] == 1) {
				$this->set('id', $this->Gallery->getLastInsertID());
			} else {
				$this->_dataForListing();
			}
		}
	}
	
	////
	// Update Gallery
	////
	function update($id) {
		$this->Gallery->id = $id;
		if ($this->Gallery->save($this->data)) {
			$this->set('gallery', $this->Gallery->read());
		}
	}
	
	////
	// Refresh gallery listing after import
	////
	function refresh() {
		$this->_dataForListing();
		$this->render('create', 'ajax');
	}
	
	////
	// Delete gallery
	////
	function delete() {
		$this->Gallery->del($this->params['form']['id']);
		$this->redirect('/galleries/index');
	}
	
	////
	// Edit gallery
	////
	function edit($id, $tab = 'summary') {
		$this->pageTitle = __('Galleries', true);
		$this->set('tab', $tab);
		
		switch($tab) {
			case('summary'):
				$this->data = $this->Gallery->find("Gallery.id = $id", null, null, 2);
				$this->set('gallery', $this->data);
				if ($this->data['Gallery']['main']) {
					$is_main = true;
				} else {
					$is_main = false;
				}
				$this->set('is_main', $is_main);
				break;
			case('albums'):
				$this->_memberData($id);
				break;
		}
		$this->Gallery->recursive = -1;
		$this->set('all_gals', $this->Gallery->findAll(null, null, 'name'));
	}
	
	////
	// Link and delink albums to galleries
	////
	function link() {
		$this->Tag->save($this->data);
		
		if ($this->Gallery->isMain($this->data['Tag']['did'])) {
			$this->Album->id = $this->data['Tag']['aid'];
			$this->Album->saveField('active', 1);
		}
		$this->_memberData($this->data['Tag']['did']);
		$this->render('refresh_edit_pane', 'ajax');
	}
	
	function delink() {
		$link = $this->Tag->read(null, $this->data['Tag']['id']);
		$id = $link['Gallery']['id'];
		$aid = $link['Album']['id'];
		$this->Tag->delete($this->data);
		
		if ($this->Gallery->isMain($id)) {
			$this->Album->id = $aid;
			$this->Album->saveField('active', 0);
		}
		
		$this->_memberData($id);
		$this->render('refresh_edit_pane', 'ajax');
	}
	
	////
	// Reset order type and refresh the album order as needed
	////
	function order_type($id) {
		$this->Gallery->id = $id;
		$this->Gallery->save($this->data);
		$this->Gallery->cacheQueries = false;
		$this->Gallery->reorder($id);
		$this->_memberData($id);
		$this->render('order_type', 'ajax');
	}
	
	////
	// Private function to refresh gallery members
	////
	function _memberData($id) {
		$this->data = $this->Gallery->find(aa('id', $id), null, null, 2);
		$this->set('gallery', $this->data);
		if (!$this->data['Gallery']['main']) {
			$this->set('is_main', false);
			$member_ids_arr = array();
			foreach ($this->data['Tag'] as $l) { 
				$member_ids_arr[] = $l['aid'];
			}
		
			// Find active albums, gallery members, and the diff
			$all_albums = $this->Album->findActive('name');
			$non_member_ids_arr = array();
			foreach ($all_albums as $a) { 
				$aid = $a['Album']['id'];
				if (!in_array($aid, $member_ids_arr)) {
					$non_member_ids_arr[] = $aid;
				}
			}
			if (empty($non_member_ids_arr)) {
				$non_members = array();
			} else {
				$non_member_ids = join(',', $non_member_ids_arr);
				$non_members = $this->Album->findAll("id IN ($non_member_ids)", null, 'name', null, 1, -1);
			}
		
			$this->set('members', $this->data['Tag']);
			$this->set('non_members', $non_members);
		} else {
			$this->set('is_main', true);
			$this->set('members', $this->data['Tag']);
		} 
	}
}

?>