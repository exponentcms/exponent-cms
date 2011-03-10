<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file thats holds the expQueue class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2006 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expQueue
 *
 * @subpackage Core-Subsytems
 * @package Framework
 */

class expQueue {
	public $name = "";
	public function __construct($name) {
		$this->name = $name;
	}

	public function addMsg($msg) {
		expQueue::flash($this->name, $msg);
	}

	public function flush() {
		expQueue::flushQueue($this->name);
	}

	public function isEmpty() {
		expQueue::isQueueEmpty();
	}
	static function flash($name, $msg="") {
        $flash = exponent_sessions_get('flash');
    	$flash[$name] = $msg;
        exponent_sessions_set('flash', $flash);
	}

	static function flashAndFlow($name, $msg) {
    	flash($name, $msg);
    	expHistory::back();
	}

	static function flashIfNotLoggedIn($name, $msg) {
		global $user;
		if (!$user->isLoggedIn()) self::flashAndFlow($name, $msg);
	}
	
	static function isQueueEmpty($name) {
		$flash = exponent_sessions_get('flash');
		return empty($flash[$name]);	
	}

	static function flushQueue($name) {
		$flash = exponent_seesions_get('flash');
		$flash[$name] = array();
		exponent_sessions_set('flash', $flash);
	}

	static function flushAllQueues() {
		exponent_sessions_set('flash', array());
	}
}

?> 
