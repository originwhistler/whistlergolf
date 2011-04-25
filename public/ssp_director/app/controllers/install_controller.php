<?php

class InstallController extends AppController
{
    var $name = 'Install';
	var $uses = array();
	var $helpers = array('Html', 'Javascript', 'Director');
	var $components = array('Director', 'Pigeon', 'Cookie', 'Session');
	var $mysqli = false;
	var $link;
	
	function beforeFilter() {
		if ($this->Session->read('Language')) {
			Configure::write('Config.language', $this->Session->read('Language'));
		}
		$this->pageTitle = __("Installing", true); 
		$this->set('config_path', ROOT . DS . 'config');
		$this->set('controller', $this);
		$this->layout = 'simple';
		
		if (version_compare(PHP_VERSION, '5.0.0', 'ge') && function_exists('mysqli_connect')) {
			if (!defined('DIR_DB_INT') || (defined('DIR_DB_INT') && DIR_DB_INT == 'mysqli')) {
				$this->mysqli = true;
			}
		}
	}
	
	////
	// Install landing page
	////
	function index() {
		$lang_folder = new Folder(ROOT . DS . 'locale');
		$langs = $lang_folder->ls(true, false);
		$actual = array();
		foreach ($langs[0] as $l) {
			if (($l != 'eng' && $l != 'SAMPLE') && file_exists(ROOT . DS . 'locale' . DS . $l . DS . 'welcome.po')) {
				$actual[] = $l;
			}
		}
		if (empty($actual)) {
			$this->Session->write('Language', 'eng');
			$this->redirect('/install/license');
			exit;
		}
		$this->set('langs', $actual);
	}
	
	function lang($l) {
		$this->Session->write('Language', $l);
		$this->redirect('/install/license');
		exit;
	}
	
	////
	// Install license
	////
	function license() {}
	
	////
	// Activation
	////
	function activate() {
		if (isset($this->data['dummy'])) {
			if ($this->Pigeon->isLocal()) {
				$this->set('local', true);
				$this->Session->write('activation', 'local');
			}
		} elseif (isset($this->data['transfer'])) {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['key'], true);
			if ($code == 0) {
				$this->Session->write('activation', $this->data['Account']['key']);
				$this->redirect('/install/database');
				exit;
			} else {
				$this->set('error', $result);
			}
		} else {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['key']);
			if ($code == 0) {
				$this->Session->write('activation', $this->data['Account']['key']);
				$this->redirect('/install/database');
				exit;
			} else {
				$this->set('error', $result);
			}
		}
	}
	
	////
	// Perform server check
	////
	function test() {
		if ($this->data) {
			$php = version_compare(PHP_VERSION, '4.3.7', 'ge');
			extension_loaded('mysql') ? $mysql = true : $mysql = extension_loaded('mysqli');
			if (ini_get('safe_mode') == false || ini_get('safe_mode') == '' || strtolower(ini_get('safe_mode')) == 'off') {
				$no_safe_mode = true;
			} else {
				$no_safe_mode = false;
			}
			if ($php && $mysql && $no_safe_mode) {
				$this->set('success', true);
			} else {
				$this->set('success', false);
				$this->set('php', $php);
				$this->set('mysql', $mysql);
				$this->set('no_safe_mode', $no_safe_mode);
			}
		} else {
			$this->redirect('/install');
			exit;
		}
	}
	
	////
	// Enter database details and create config file
	////
	function database() {
		$this->set('db_select_error', false);
		$this->set('connection_error', false);
		$this->set('conf_exists', false);
		$this->set('write_error', false);
		$filename = ROOT . DS . 'config' . DS . 'conf.php';
		
		if ($this->data) {
			if (file_exists($filename)) {
				$this->set('conf_exists', true);
			} else {
				$details = $this->data['db'];
				$server = trim($details['server']);
				$name = trim($details['name']);
				$user = trim($details['user']);
				$pass = trim($details['pass']);
				$prefix = trim($details['prefix']);
				
				if ($this->mysqli) {
					$interface = 'mysqli';
				} else {
					$interface = 'mysql';
				}
				
				$port = $socket = null;
				
				if (strpos($server, ':') !== false) {
					$bits = explode(':', $server);
					$server = $bits[0];
					if (is_numeric($bits[1])) {
						$port = $bits[1];
					} else {
						$socket = $bits[1];
					}
				}
				
				$link = @$this->_connect($server, $user, $pass, $port, $socket);
				if (!$link) {
				    $this->set('connection_error', true);
					$this->set('mysql_error', $this->_error(true));
				} elseif (@!$this->_select($details['name'])) {
					$this->set('db_select_error', true);
					$this->set('mysql_error', $this->_error());
				} else {			
					$fill = "<?php\n\n\t";
					$fill .= '$interface = \''.	$interface	."';\n\t";
					$fill .= '$host = \''.	$server	."';\n\t";
					$fill .= '$db = \''.	$name	."';\n\t";
					$fill .= '$user = \''.	$user	."';\n\t";
					$fill .= '$pass = \''.	$pass	."';\n\n\t";
					$fill .= '$pre = \''.	$prefix	."';\n\n\t";
					$fill .= '$socket = \''.	$socket	."';\n\t";
					$fill .= '$port = \''.	$port	."';\n\n";
					$fill .= '?>';
			
					$handle = fopen($filename, 'w+');

					if (fwrite($handle, $fill) == false) {
						$this->set('write_error', true);
					} else {
						$this->redirect('/install/register');
						exit;
					}
				}
			}
		} else {
			if (file_exists($filename)) {
				$this->set('conf_exists', true);
			}
		}
	}
	
	////
	// Create the first user
	////
	function register() {}
	
	////
	// Install it already!
	////
	function finish() {
		if ($this->data) {
			$socket = $port = null;
			$check = DIR_PORT;
			if (!empty($check)) {
				if (is_numeric($check)) {
					$port = $check;
				} else {
					$socket = $check;
				}
			}
			$this->_connect(DIR_DB_HOST, DIR_DB_USER, DIR_DB_PASSWORD, $port, $socket);
			$this->_select(DIR_DB);
		
			$atbl = DIR_DB_PRE . 'albums';
			$itbl = DIR_DB_PRE . 'images';
			$dtbl = DIR_DB_PRE . 'dynamic';
			$dltbl = DIR_DB_PRE . 'dynamic_links';
			$stbl = DIR_DB_PRE . 'slideshows';
			$utbl = DIR_DB_PRE . 'usrs';
			$acctbl = DIR_DB_PRE . 'account';
		
			$this->set('error', '');
			$key = md5(uniqid(rand(), true));
			$now = time() - date('Z');
			$queries = array(
					"CREATE TABLE $atbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(100), description BLOB, path VARCHAR(50), tn TINYINT(1) NOT NULL DEFAULT '0', aTn VARCHAR(150), active TINYINT(1) NOT NULL DEFAULT '0', audioFile VARCHAR(100) DEFAULT NULL, audioCap VARCHAR(200) DEFAULT NULL, displayOrder INT(4) DEFAULT '999', target TINYINT(1) NOT NULL DEFAULT '0', images_count INT NOT NULL DEFAULT 0, sort_type VARCHAR(255) NOT NULL DEFAULT 'manual', title_template VARCHAR(255), link_template TEXT, caption_template TEXT, modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), updated_by INT(11), created_by INT(11))",
					"CREATE TABLE $itbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), aid INT, title VARCHAR(255), src VARCHAR(255), caption TEXT, link TEXT, active TINYINT(1) NOT NULL DEFAULT '1', seq INT(4) NOT NULL DEFAULT '999', pause INT(4) NOT NULL DEFAULT '0', target TINYINT(1) NOT NULL DEFAULT '0', modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), updated_by INT(11), created_by INT(11), anchor VARCHAR(255) DEFAULT NULL, filesize INT(11), tags TEXT, captured_on INT(10))",
					"CREATE TABLE $utbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), usr VARCHAR(50), pwd VARCHAR(50), email VARCHAR(255), perms INT(2) NOT NULL DEFAULT '1', modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), news TINYINT(1) DEFAULT 1, help TINYINT(1) DEFAULT 1, display_name VARCHAR(255), last_seen INT(10), first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, profile TEXT, externals TEXT, anchor VARCHAR(255) DEFAULT NULL)",
					"CREATE TABLE $dtbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(100), description TEXT, modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), main TINYINT(1) DEFAULT 0, sort_type VARCHAR(255) NOT NULL DEFAULT 'manual', updated_by INT(11), created_by INT(11), tag_count INT(11) DEFAULT 0)",
					"CREATE TABLE $dltbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), did INT, aid INT, display INT DEFAULT '800')",
					"CREATE TABLE $stbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(255), url VARCHAR(255))",
					"INSERT INTO $utbl (id, usr, display_name, email, pwd, perms, created_on, modified_on) VALUES (NULL, '" . $this->data['User']['usr'] . "', '" . $this->data['User']['usr'] . "', '" . $this->data['User']['email'] . "', '" . $this->data['User']['pwd'] . "', 4, " . $now . ", " . $now . ")",
					"CREATE TABLE $acctbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), externals TINYINT(1), internals TINYINT(1), version VARCHAR(255), activation_key VARCHAR(255), last_check DATETIME, theme VARCHAR(255) DEFAULT '/app/webroot/styles/default/default.css', lang VARCHAR(255) DEFAULT 'eng', api_key VARCHAR(255), grace TINYINT(1) DEFAULT 0)",
					"INSERT INTO $acctbl (id, externals, internals, version, activation_key, last_check, lang, api_key) VALUES (NULL, 1, 1, '" . DIR_VERSION . "', '" . $this->Session->read('activation') . "', '" . date('Y-m-d H:i:s', strtotime('+2 weeks')) . "', '" . $this->Session->read('Language') . "', '" . $key . "')",
					"INSERT INTO $dtbl(name, description, main, created_on, modified_on) VALUES('All albums', 'This gallery contains all published albums.', 1, $now, $now)",
					"CREATE INDEX updated_by ON $atbl (updated_by)",
					"CREATE INDEX created_by ON $atbl (created_by)",
					"CREATE INDEX updated_by ON $dtbl (updated_by)",
					"CREATE INDEX created_by ON $dtbl (created_by)",
					"CREATE INDEX updated_by ON $itbl (updated_by)",
					"CREATE INDEX created_by ON $itbl (created_by)",
					"CREATE INDEX aid ON $itbl (aid)",
					"CREATE INDEX aid ON $dltbl (aid)",
					"CREATE INDEX did ON $dltbl (did)",
					"CREATE INDEX src ON $itbl (src)",
					"CREATE INDEX modified_on ON $itbl (modified_on)",
					"CREATE INDEX created_on ON $itbl (created_on)",
					"CREATE INDEX modified_on ON $atbl (modified_on)",
					"CREATE INDEX created_on ON $atbl (created_on)",
					"CREATE INDEX modified_on ON $dtbl (modified_on)",
					"CREATE INDEX created_on ON $dtbl (created_on)",
					"CREATE INDEX images_count ON $atbl (images_count)",
					"CREATE INDEX tag_count ON $dtbl (tag_count)",
					"CREATE INDEX main ON $dtbl (main)",
					"CREATE INDEX seq ON $itbl (seq)",
					"CREATE INDEX active ON $atbl (active)",
					"CREATE INDEX captured_on ON $itbl (captured_on)",
					"CREATE INDEX display ON $dltbl (display)",
					"CREATE INDEX usr ON $utbl (usr)"
				);

			foreach($queries as $query) {
				if (!$this->_query($query)) {
					$this->set('error', $this->_error());
					e($this->render());
					exit;
				}
			}
			$this->_clean(CACHE . DS . 'models');
		} else {
			$this->redirect('/install');
		}
	}
	
	////
	// Perform upgrade
	////
	function upgrade($step = 1) {
		define('CUR_USER_ID', $this->Session->read('User.id'));
		
		// Make sure they have the appropriate version of PHP, as 1.0.8+ now requires 4.3.2+
		if (version_compare(PHP_VERSION, '4.3.7', '>=')) {
			
			if (function_exists('set_time_limit')) {
				set_time_limit(0);
			}
			$this->set('error', false);
			$this->set('step', $step);
			
			$socket = $port = null;
			$check = DIR_PORT;
			if (!empty($check)) {
				if (is_numeric($check)) {
					$port = $check;
				} else {
					$socket = $check;
				}
			}
			
			$this->_connect(DIR_DB_HOST, DIR_DB_USER, DIR_DB_PASSWORD, $port, $socket);
			$this->_select(DIR_DB);

			$version = DIR_VERSION;
			$atbl = DIR_DB_PRE . 'albums';
			$itbl = DIR_DB_PRE . 'images';
			$dtbl = DIR_DB_PRE . 'dynamic';
			$dltbl = DIR_DB_PRE . 'dynamic_links';
			$stbl = DIR_DB_PRE . 'slideshows';
			$utbl = DIR_DB_PRE . 'usrs';
			$acctbl = DIR_DB_PRE . 'account';
			
			switch($step) {
				case(1):
					$acc = $this->_query("SELECT version FROM $acctbl WHERE version IS NOT NULL LIMIT 1");
					$row = $this->_array($acc);
					$version = $row['version'];
					$allowable = array('1.1', '1.2');
					$main = substr($version, 0, 3);
					if (!in_array($main, $allowable)) {
						$this->set('dated', true);
						$this->set('version', $version);
					} else {
						$this->set('dated', false);
					}
					$alter = $this->_query("ALTER TABLE $atbl ADD testcol VARCHAR(255)");
					if ($alter) {
						$this->_query("ALTER TABLE $atbl DROP testcol");
						$this->set('alter', false);
					} else {
						$this->set('alter', true);
					}
					break;
				case(2):
					@$this->_query("DELETE FROM $itbl WHERE aid IS NULL");
					
					// 1.2
					App::import('Model', 'Image');
					$this->Image =& new Image();
					
					@$this->_query("ALTER TABLE $acctbl ADD api_key VARCHAR(255)");
					@$this->_query("ALTER TABLE $itbl ADD filesize INT(11)");
					
					$result = $this->_query("SELECT i.*, a.path FROM $itbl AS i, $atbl AS a WHERE i.aid = a.id AND i.filesize IS NULL");
					if ($this->_rows($result) > 0) {
						while($row = $this->_array($result)) {				
							$size = filesize(ALBUMS . DS . $row['path'] . DS . 'lg' . DS . $row['src']);
							$this->_query("UPDATE $itbl SET filesize = $size WHERE id = {$row['id']}");
						}
					}
					
					$result = $this->_query("SELECT * FROM $dtbl WHERE main = 1");
					if ($this->_rows($result) == 0) {
						$now = time() - date('Z');
						$this->_query("INSERT INTO $dtbl(name, description, main, created_on, modified_on) VALUES('All albums', 'This gallery contains all published albums.', 1, $now, $now)");
						$new = $this->_query("SELECT * FROM $dtbl WHERE main = 1 LIMIT 1");
						$noob = $this->_insert_id();
						$actives = $this->_query("SELECT * FROM $atbl WHERE active = 1");
						if ($this->_rows($actives) > 0) {
							$i = 0;
							while($row = $this->_array($actives)) {				
								$this->_query("INSERT INTO $dltbl(aid, did) VALUES({$row['id']}, $noob)");
								$i++;
							}
							$this->_query("UPDATE $dtbl SET tag_count = $i WHERE id = $noob");
						}
					}
					
					$result = $this->_query("SELECT id FROM $dtbl");
					if ($this->_rows($result) > 0) {
						$ids = array();
						while($row = $this->_array($result)) {				
							$ids[] = $row['id'];
						}
						$id_str = join(',', $ids);
						$this->_query("DELETE FROM $dltbl WHERE did NOT IN ($id_str)");
					}
					
					@$this->_query("ALTER TABLE $utbl ADD last_seen INT(10)");
					@$this->_query("ALTER TABLE $utbl ADD display_name VARCHAR(255)");
					@$this->_query("UPDATE $utbl SET display_name = $utbl.usr");
					@$this->_query("ALTER TABLE $atbl ADD created_on INT(10)");
					@$this->_query("ALTER TABLE $atbl ADD modified_on INT(10)");
					@$this->_query("ALTER TABLE $itbl ADD created_on INT(10)");
					@$this->_query("ALTER TABLE $itbl ADD modified_on INT(10)");
					@$this->_query("ALTER TABLE $dtbl ADD created_on INT(10)");
					@$this->_query("ALTER TABLE $dtbl ADD modified_on INT(10)");
					@$this->_query("ALTER TABLE $utbl ADD created_on INT(10)");
					@$this->_query("ALTER TABLE $utbl ADD modified_on INT(10)");
					
					@$this->_query("ALTER TABLE $itbl ADD captured_on INT(10)");
					@$this->_query("ALTER TABLE $itbl DROP meta");
					
					@$this->_query("ALTER TABLE $utbl ADD first_name VARCHAR(255) DEFAULT NULL");
					@$this->_query("ALTER TABLE $utbl ADD last_name VARCHAR(255) DEFAULT NULL");
					@$this->_query("ALTER TABLE $utbl ADD profile TEXT");
					@$this->_query("ALTER TABLE $utbl ADD externals TEXT");
					@$this->_query("ALTER TABLE $utbl ADD anchor VARCHAR(255) DEFAULT NULL");
				
					@$this->_query("ALTER TABLE $itbl ADD tags TEXT");
					@$this->_query("ALTER TABLE $acctbl ADD grace TINYINT(1) DEFAULT 0");
					@$this->_query("ALTER TABLE $dtbl ADD tag_count INT(11) DEFAULT 0");
					
					@$this->_query("CREATE INDEX updated_by ON $atbl (updated_by)");
					@$this->_query("CREATE INDEX created_by ON $atbl (created_by)");
					@$this->_query("CREATE INDEX updated_by ON $dtbl (updated_by)");
					@$this->_query("CREATE INDEX created_by ON $dtbl (created_by)");
					@$this->_query("CREATE INDEX updated_by ON $itbl (updated_by)");
					@$this->_query("CREATE INDEX created_by ON $itbl (created_by)");
					@$this->_query("CREATE INDEX aid ON $itbl (aid)");
					@$this->_query("CREATE INDEX aid ON $dltbl (aid)");
					@$this->_query("CREATE INDEX did ON $dltbl (did)");
					
					@$this->_query("CREATE INDEX src ON $itbl (src)");
					@$this->_query("CREATE INDEX modified_on ON $itbl (modified_on)");
					@$this->_query("CREATE INDEX created_on ON $itbl (created_on)");
					@$this->_query("CREATE INDEX modified_on ON $atbl (modified_on)");
					@$this->_query("CREATE INDEX created_on ON $atbl (created_on)");
					@$this->_query("CREATE INDEX modified_on ON $dtbl (modified_on)");
					@$this->_query("CREATE INDEX created_on ON $dtbl (created_on)");
					@$this->_query("CREATE INDEX images_count ON $atbl (images_count)");
					@$this->_query("CREATE INDEX tag_count ON $dtbl (tag_count)");
					@$this->_query("CREATE INDEX main ON $dtbl (main)");
					@$this->_query("CREATE INDEX seq ON $itbl (seq)");
					@$this->_query("CREATE INDEX active ON $atbl (active)");
					@$this->_query("CREATE INDEX captured_on ON $itbl (captured_on)");
					@$this->_query("CREATE INDEX display ON $dltbl (display)");
					
					$results = $this->_query("SELECT * FROM $dtbl");
					while ($row = $this->_array($results)) {
						$r = $this->_query("SELECT COUNT(id) FROM $dltbl WHERE did = {$row['id']}");
						$irow = $this->_row($r);
						$this->_query("UPDATE $dtbl SET tag_count = {$irow[0]} WHERE id = {$row['id']}");
					}
					@$this->_query("DELETE FROM $dtbl WHERE name IS NULL");
					@$this->_query("DELETE FROM $dltbl WHERE aid IS NULL OR did IS NULL");
					
					$users = $this->_query("SELECT * FROM $utbl");

					$avatars = AVATARS;
					while($row = $this->_array($users)) {
						$path = $avatars . $row['id'];
						if (file_exists($path)) {
							$info = getimagesize($path);
							$mime = $info['mime'];
							switch($mime) {
								case 'image/jpeg':
									$original = 'original.jpg';
									break;
								case 'image/gif':
									$original = 'original.gif';
									break;
								case 'image/png':
									$original = 'original.png';
									break;
							}
							rename($path, $path . '.old');
							if (!is_dir($avatars . $row['id'])) {
								umask(0);
								mkdir($avatars . $row['id'], 0777, true);
							}
							rename($path . '.old', $path . DS . $original);
						}
					}
					
					$key = md5(uniqid(rand(), true));
					@$this->_query("UPDATE $acctbl SET api_key = '$key' WHERE api_key IS NULL OR api_key = ''");
					
					App::import('Model', 'Album');
					$this->Album =& new Album();

					App::import('Model', 'Gallery');
					$this->Gallery =& new Gallery();
				
					App::import('Model', 'User');
					$this->User =& new User();
				
					$this->Image->recursive = -1;
					$images = $this->Image->findAll(aa('created_on', null));					
					$this->Album->recursive = -1;
					$albums = $this->Album->findAll(aa('created_on', null));
					$this->Gallery->recursive = -1;
					$galleries = $this->Gallery->findAll(aa('created_on', null));
					$this->User->recursive = -1;
					$users = $this->User->findAll(aa('created_on', null));
				
					foreach($images as $image) {
						if (empty($image['Image']['created_on'])) {
							$created = strtotime($image['Image']['created']);
							$created_gmt = $created - date('Z', $created);
							$modified = strtotime($image['Image']['modified']);
							$modified_gmt = $modified - date('Z', $modified);
							$this->_query("UPDATE $itbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$image['Image']['id']}");
						}
					}
				
					foreach($albums as $album) {
						if (empty($album['Album']['created_on'])) {
							$created = strtotime($album['Album']['created']);
							$created_gmt = $created - date('Z', $created);
							$modified = strtotime($album['Album']['modified']);
							$modified_gmt = $modified - date('Z', $modified);
							$this->_query("UPDATE $atbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$album['Album']['id']}");
						}
					}
				
					foreach($galleries as $gallery) {
						if (empty($galleries['Gallery']['created_on'])) {
							$created = strtotime($gallery['Gallery']['created']);
							$created_gmt = $created - date('Z', $created);
							$modified = strtotime($gallery['Gallery']['modified']);
							$modified_gmt = $modified - date('Z', $modified);
							$this->_query("UPDATE $dtbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$gallery['Gallery']['id']}");
						}
					}
				
					foreach($users as $user) {
						if (empty($user['User']['created_on'])) {
							$created = strtotime($user['User']['created']);
							$created_gmt = $created - date('Z', $created);
							$modified = strtotime($user['User']['modified']);
							$modified_gmt = $modified - date('Z', $modified);
							$this->_query("UPDATE $utbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$user['User']['id']}");
						}	
					}
					
					break;
				
				case(3):
					if (is_callable('exif_read_data') || is_callable('iptcparse')) {
						$result = $this->_query("SELECT i.*, a.path FROM $itbl AS i, $atbl AS a WHERE i.aid = a.id AND i.captured_on IS NULL");
						if ($this->_rows($result) > 0) {
							while($row = $this->_array($result)) {	
								$path = ALBUMS . DS . $row['path'] . DS . 'lg' . DS . $row['src'];
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
									
									if (isset($captured_on) && is_numeric($captured_on)) {
										$query = "UPDATE $itbl SET captured_on = $captured_on WHERE id = {$row['id']}";
										$this->_query($query);
									}
									$meta = array();
									unset($captured_on);
								}			
							}
						}
					}
					
					@$this->_query("UPDATE $atbl SET title_template = REPLACE(title_template, '[img_name]', '[director:image filename]')");
					@$this->_query("UPDATE $atbl SET title_template = REPLACE(title_template, '[album_name]', '[director:album name]')");
					@$this->_query("UPDATE $atbl SET caption_template = REPLACE(caption_template, '[img_name]', '[director:image filename]')");
					@$this->_query("UPDATE $atbl SET caption_template = REPLACE(caption_template, '[iptc_caption]', '[iptc:caption]')");
					@$this->_query("UPDATE $itbl SET tags = REPLACE(tags, 'undefined', '') WHERE tags LIKE '%undefined%'");
					
					@$this->_query("UPDATE $atbl SET sort_type = 'file name (oldest first)' WHERE sort_type = 'file name'");
					@$this->_query("UPDATE $dtbl SET sort_type = 'album title (oldest first)' WHERE sort_type = 'album title'");
					
					break;
				
				case(4):					
					// Clean XML cache and model cache
					$this->_clean(XML_CACHE);
					$this->_clean(CACHE . DS . 'models');
					$this->_clean(CACHE . DS . 'director');
			
					$this->Cookie->del('Login');
					$this->Cookie->del('Pass');
			        $this->Session->delete('User');
			
					@$this->_query("UPDATE $acctbl SET version = '$version'");
					break;
			}
	 	} else {
			$this->set('error', true);
		}
	}
	
	////
	// Clean a directory
	////
	function _clean($dir) {
		if ($dh = @opendir($dir)) {
			while (($obj = readdir($dh))) {
		       if ($obj=='.' || $obj=='..' || $obj =='.svn') continue;
		       if (!@unlink($dir.'/'.$obj)) $this->Director->rmdirr($dir.'/'.$obj);
		   	}    
		}
	}
	
	function _connect($host, $user, $pass, $port, $socket) {
		if ($this->mysqli) {
			return $this->link = mysqli_connect($host, $user, $pass, null, $port, $socket);
		} else {
			if (!empty($port)) {
				$host = "$host:$port";
			} else if (!empty($socket)) {
				$host = "$host:$socket";
			}
			return $this->link = mysql_connect($host, $user, $pass);
		}	
	}
	
	function _error($connect = false) {
		if ($this->mysqli) {
			if ($connect) {
				return mysqli_connect_error();
			} else {
				return mysqli_error($this->link);
			}
		} else {
			return mysql_error($this->link);
		}
	}
	
	function _query($query) {
		if ($this->mysqli) {
			return mysqli_query($this->link, $query);
		} else {
			return mysql_query($query, $this->link);
		}
	}
	
	function _select($db) {
		if ($this->mysqli) {
			return mysqli_select_db($this->link, $db);
		} else {
			return mysql_select_db($db, $this->link);
		}
	}
	
	function _array($result) {
		if ($this->mysqli) {
			return mysqli_fetch_array($result);
		} else {
			return mysql_fetch_array($result);
		}
	}
	
	function _row($result) {
		if ($this->mysqli) {
			return mysqli_fetch_row($result);
		} else {
			return mysql_fetch_row($result);
		}
	}
	
	function _rows($result) {
		if ($this->mysqli) {
			return mysqli_num_rows($result);
		} else {
			return mysql_num_rows($result);
		}
	}
	
	function _insert_id() {
		if ($this->mysqli) {
			return mysqli_insert_id($this->link);
		} else {
			return mysql_insert_id();
		}
	}
}

?>