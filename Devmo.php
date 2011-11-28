<?php
// require core classes
define('DEVMO_DIR',preg_replace('=^(.+)/[^/]+$=','\1',__FILE__));
require(DEVMO_DIR."/libs/Core.php");
require(DEVMO_DIR."/libs/Exception.php");
require(DEVMO_DIR."/libs/Deprecated.php");

use \Devmo\libs\Core;
use \Devmo\libs\Logger;
use \Devmo\libs\CoreException;

/**
 * Main initializer for essentail properties
 *
 * @category Framework
 * @author Dan Wager
 * @copyright Copyright (c) 2011 Devmo
 * @version 1.0
 */
class Devmo {
	/**
	 * 
	 * Enter description here ...
	 */
	public static function run () {
		try {
			return Core::execute()->getRoot();
		} catch (CoreException $e) {
			if (Core::$debug) {
				$controller = self::getObject('Devmo.controllers.Error');
				$controller->setException($e);
				$controller->setData($e->tokens);
				return $controller->run();
			} else {
				return Core::execute('Devmo.controllers.FourOFour')->getRoot();
			}
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $path
	 */
	public static function addAppNamespace ($namespace, $path, $default=false) {
		foreach (Core::$namespaces as $k=>$v)
			Core::$namespaces[$k][$namespace] = $path;
		if ($default || Core::$namespace==null)
			Core::$namespace = $namespace;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $namespace
	 * @param unknown_type $path
	 */
	public static function addControllerNamespace ($namespace, $path) {
		Core::$paths['controllers'][$namespace] = $path;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $namespace
	 * @param unknown_type $path
	 */
	public static function addViewPath ($namespace, $path) {
		Core::$paths['views'][$namespace] = $path;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $namespace
	 * @param unknown_type $path
	 */
	public static function addLibPath ($namespace, $path) {
		Core::$paths['libs'][$namespace] = $path;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $namespace
	 * @param unknown_type $path
	 */
	public static function addDaoPath ($namespace, $path) {
		Core::$paths['daos'][$namespace] = $path;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $mapping
	 */
	public static function addMapping ($mapping) {
		Core::$mappings[] = $mapping;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $debug
	 */
	public static function setDebug ($debug=false) {
		Core::$debug = ($debug==true);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $file
	 */
	public static function setLog ($file) {
		Logger::setDefaultFile($file);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $controller
	 */
	public static function setHomeController ($controller) {
		Core::$homeController = $controller;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $controller
	 */
	public static function setRequestedController ($controller) {
		if ($controller)
			Core::$requestedController = Core::$namespace.preg_replace(array('=/=','=\.([^\.]+)$='),array('.','.controllers.\1'),$controller);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $mixed
	 */
	public static function getValue ($name, $mixed=null) {
		if (is_array($mixed))
			return isset($mixed[$name])
				? $mixed[$name]
				: false;
		if (is_object($mixed))
			return isset($mixed->{$name})
				? $mixed->{$name}
				: false;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public static function getSession ($name) {
		return self::getValue($name,$_SESSION);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public static function getGet ($name) {
		return self::getValue($name,$_GET);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public static function getPost ($name) {
		return self::getValue($name,$_POST);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public static function getRequest ($name) {
		return self::getValue($name,$_REQUEST);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public static function getServer ($name) {
		return self::getValue($name,$_SERVER);
	}
	/**
	 * Returns the current debug setting
	 *
	 * @return bool Whether debug is on or off
	 */
	public static function isDebug() {
		return Core::$debug;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $class
	 * @param unknown_type $option
	 */
	public static function getObject ($class, $option='auto') {
		return Core::getObject($class,$option);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $class
	 */
	public static function loadObject ($class) {
		return Core::getObject($class,'load');
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $obj
	 * @param unknown_type $text
	 * @param unknown_type $opt
	 */
	public static function debug ($obj, $text='DEBUG', $opt=FALSE) {
		echo "<pre>\n";
		echo "{$text}\n";
		switch ($opt) {
			default:
				print_r($obj);
				break;
			case 'fatal':
				print_r($obj);
				exit;
				break;
			case 'trace':
				debug_print_backtrace();
				print_r($obj);
				break;
			case 'obj':
				print_r($obj);
				break;
			case 'xml':
				echo $obj->asXML();
				break;
		}
		echo "\n</pre>";
	}
}

// set defaults
Devmo::setDebug(false);
Devmo::addAppNamespace('Devmo',DEVMO_DIR);
Devmo::setLog('../log/'.strtolower(Devmo::getServer('HTTP_HOST')).'.log');
