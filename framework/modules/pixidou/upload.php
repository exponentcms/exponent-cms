<?php
	/**
	 +------------------------------------------------------------------------+
	 | Pixidou - an Open Source AJAX Image Editor                      		  |
	 +------------------------------------------------------------------------+
	 | upload.php	                                                          |
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
	
	// our object
	$uploadedImage = new Upload($_FILES['uploadImage']);
	
	if ($uploadedImage->uploaded) {
		// only images are allowed
		$uploadedImage->allowed = array('image/*');
		
		// jpg quality
		$uploadedImage->jpeg_quality = 100;
		
		// process the whole thing in the PROCESS_DIR check init.php
		$uploadedImage->Process(PROCESS_DIR);
		
		if ($uploadedImage->processed) {
			// get all the uploaded file details
			$fileName = $uploadedImage->file_dst_name;
			$width = $uploadedImage->image_src_x;
			$height = $uploadedImage->image_src_y;
			
			// output json data
			echo json_encode(array('image' => $fileName, 'width' => $width, 'height' => $height));
		}
		else{
			echo json_encode(array('error' => $uploadedImage->error));
		}
		$uploadedImage->clean();
	}
?>