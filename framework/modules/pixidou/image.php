<?php
	/**
	 +------------------------------------------------------------------------+
	 | Pixidou - an Open Source AJAX Image Editor                      		  |
	 +------------------------------------------------------------------------+
	 | image.php	                                                          |
	 +------------------------------------------------------------------------+
	 | Copyright (c) Asvin Balloo 2008. All rights reserved. 	              |
	 | Version       0.1                                                      |
	 | Last modified 29/10/2008                                               |
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

	require_once('includes/init.php');

	$action = $_GET['action'];
	$image = $_GET['image'];
	
	$pixidou = new Pixidou($image);
	
	switch($action){
		case 'resize':
			$height = (int)$_GET['height'];
			$width = (int)$_GET['width'];
			
			$pixidou->resize($width, $height);
			break;
		
		case 'flip':
			$direction = $_GET['direction'];
			
			$pixidou->flip($direction);
			break;
			
		case 'rotate':
			$degrees = (int)$_GET['degrees'];
			
			$pixidou->rotate($degrees);
			break;
			
		case 'negative':
			$pixidou->negative();
			break;
			
		case 'tint':
			$color = $_GET['color'];
			
			$pixidou->tint($color);
			break;
			
		case 'contrast':
			$value = $_GET['value'];

			$pixidou->contrast($value);
			break;
		
		case 'brightness':
			$value = $_GET['value'];

			$pixidou->brightness($value);
			break;
			
		case 'crop':
			$width = $_GET['width'];
			$height = $_GET['height'];
			$startX = $_GET['cropStartX'];
			$startY = $_GET['cropStartY'];
			
			$pixidou->crop($width, $height, $startX, $startY);
			break;
		
		case 'save':
			$type = $_GET['type'];
			
			$pixidou->saveImage($type);
			break;
	}
?>