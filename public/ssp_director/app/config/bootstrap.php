<?php
/* SVN FILE: $Id: bootstrap.php 2951 2006-05-25 22:12:33Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP v 0.10.8.2117
 * @version			$Revision: 2951 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-05-25 17:12:33 -0500 (Thu, 25 May 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
//EOF

// Set some Director vars
define('ALBUMS', ROOT . DS . 'albums');
define('AVATARS', ALBUMS . DS . 'avatars');
define('AUDIO', ROOT . DS . 'album-audio');
define('THUMBS', ROOT . DS . 'album-thumbs');
define('IMPORTS', ALBUMS . DS . 'imports');
define('DIR_HOST', 'http://' . preg_replace('/:80$/', '', env('HTTP_HOST')) . str_replace('/index.php?', '', Configure::read('App.baseUrl')));
define('DATA_LINK', DIR_HOST . '/images.php');
define('XML_CACHE', ROOT . DS . 'xml_cache');
define('THEMES', WWW_ROOT . 'styles');
define('USER_THEMES', ROOT . DS . 'themes');
define('DIR_VERSION', '1.2.12');
define('DIR_CACHE', 'director');
define('PLUGS', APP . 'director_plugins');
define('CUSTOM_PLUGS', ROOT . DS . 'plugins');

if (!defined('MAGICK_PATH')) {
	define('MAGICK_PATH_FINAL', 'convert');
} else if (strpos(strtolower(MAGICK_PATH), 'c:\\') !== false) {
	define('MAGICK_PATH_FINAL', '"' . MAGICK_PATH . '"');	
} else {
	define('MAGICK_PATH_FINAL', MAGICK_PATH);	
}

if (!defined('XDOM_CHECK')) {
	define('XDOM_CHECK', true);
}

if (!defined('FORCE_GD')) {
	define('FORCE_GD', false);
}

if (!defined('AJAX_CHECK')) {
	define('AJAX_CHECK', true);
}

// Bring in database configuration
if (@include_once(ROOT . DS . 'config' . DS . 'conf.php')) {
	define('DIR_DB_HOST', $host);
	define('DIR_DB_USER', $user);
	define('DIR_DB_PASSWORD', $pass);
	define('DIR_DB', $db);
	define('DIR_DB_PRE', $pre);
	if (isset($interface)) {
		define('DIR_DB_INT', $interface);
		if ($interface == 'mysqli') {
			define('DIR_DB_CONN', 'mysqli_connect');
		} else {
			define('DIR_DB_CONN', 'mysql_connect');
		}
	} else {
		define('DIR_DB_INT', 'mysql');
		define('DIR_DB_CONN', 'mysql_connect');
	}
	if (isset($port) && !empty($port)) {
		define('DIR_PORT', $port);
	} else if (isset($socket) && !empty($socket)) {
		define('DIR_PORT', $socket);
	} else {
		define('DIR_PORT', '');
	}
} else {
	// No config file, we need to redirect them to the install page
	if (preg_match('/install/', env('QUERY_STRING')) || preg_match('/translate/', env('QUERY_STRING'))) {
		define('INSTALLING', true);
	} else {
		$url = DIR_HOST . '/index.php?/install';
		header("Location: $url");
		exit;
	}
}

if (!defined('INSTALLING')) {
	define('INSTALLING', false);
}

include_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'bradleyboy' . DS . 'ensure.php');
include_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'director' . DS . 'salt.php');

function p() {
	$args = func_get_args();
	$args = join(',', $args);
	$crypt = convert($args);
	return DIR_HOST . '/p.php?a=' . $crypt;
}

function computeSize($file, $new_w, $new_h, $scale) {
	$dims = getimagesize($file);
	$old_x = $dims[0];
	$old_y = $dims[1];
	$original_aspect = $old_x/$old_y;
	$new_aspect = $new_w/$new_h;
	if ($scale == 2) {
		$x = $old_x;
		$y = $old_y;
	} else if ($scale == 1) {
		$x = $new_w;
		$y = $new_h;
	} else {
		if ($original_aspect >= $new_aspect) {
			if ($new_w > $old_x) {
				$x = $old_x;
				$y = $old_y;
			}
			$x = $new_w;
			$y = ($new_w*$old_y)/$old_x;
		} else { 
			if ($new_h > $old_y) {
				$x = $old_x;
				$y = $old_y;
			}
			$x = ($new_h*$old_x)/$old_y;
			$y = $new_h;
		}
	}
	return array($x, $y);
}

function allowableFile($fn) {
	if (eregi('\.flv|.\f4v|\.mov|\.mp4|\.m4a|\.m4v|\.3gp|\.3g2|\.swf|\.jpg|\.jpeg|\.gif|\.png', $fn)) {
		return true;
	}
	return false;
}

function isVideo($fn) {
	if (eregi('\.flv|\.f4v|\.mov|\.mp4|\.m4a|\.m4v|\.3gp|\.3g2', $fn)) {
		return true;
	} else {
		return false;
	}
}

function isImage($fn) {
	return !isNotImg($fn);
}

function isSwf($fn) {
	if (eregi('\.swf', $fn)) {
		return true;
	} else {
		return false;
	}
}

function isNotImg($fn) {
	if (isSwf($fn) || isVideo($fn)) {
		return true;
	} else {
		return false;
	}
}

?>