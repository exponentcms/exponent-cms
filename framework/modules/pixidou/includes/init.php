<?php
	/**
	 +------------------------------------------------------------------------+
	 | Pixidou - an Open Source AJAX Image Editor                      		  |
	 +------------------------------------------------------------------------+
	 | init.php                                                       		  |
	 +------------------------------------------------------------------------+
	 | Copyright (c) Asvin Balloo 2008. All rights reserved. 	           	  |
	 | Version       0.1                                                      |
	 | Last modified 31/10/2008                                               |
	 | Email         asvin.balloo@gmail.com                                   |
	 | Web           http://htmlblog.net                                      |
	 +------------------------------------------------------------------------+
	 | This program is free software; you can redistribute it and/or modify   |
	 | it under the terms of the GNU General Public License version 2 as      |
	 | published by the Free Software Foundation.                             |
	 |                                                                        |
	 | This program is distributed in the hope that it will be useful,        |
	 | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
	 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
	 | GNU General Public License for more details.                           |
	 |                                                                        |
	 | You should have received a copy of the GNU General Public License      |
	 | along with this program; if not, write to the                          |
	 |   Free Software Foundation, Inc., 59 Temple Place, Suite 330,          |
	 |   Boston, MA 02111-1307 USA                                            |
	 |                                                                        |
	 +------------------------------------------------------------------------+
	 */

	require_once('includes/class.upload/class.upload.php');
	require_once('includes/classes/classPixidou.php');
	require_once('../../../exponent.php');

//	define('PROCESS_DIR', './images/');
	define('PROCESS_DIR', BASE.'tmp/pixidou/');
?>