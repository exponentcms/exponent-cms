<?php
	/**
	 +------------------------------------------------------------------------+
	 | Pixidou - an Open Source AJAX Image Editor                      		  |
	 +------------------------------------------------------------------------+
	 | classPixidou.php                                                       |
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
	
	class Pixidou{

		private $imageFile;
		private $uploadedFile;
		private $allowedFiles = array('image/*');
		private $imageQuality = 100;
		private $destDirectory = PROCESS_DIR;
		private $processedDirectory = PROCESS_DIR;

		/**
		 * Constructor
		 *
		 * @param String $imageFile
		 */
		public function __construct($imageFile){
			set_time_limit(0);
			$this->uploadedFile = new Upload($this->destDirectory.'/'.$imageFile);
				
			$this->imageFile = $imageFile;
		}
	
		/**
		 * Resizes an image
		 *
		 * @param Int $width
		 * @param Int $height
		 */
		public function resize($width, $height){
			$this->uploadedFile->image_resize = true;
			$this->uploadedFile->image_x = $width;
			$this->uploadedFile->image_y = $height;
			$this->outputJsonData();
		}
	
		/**
		 * Flips an image
		 *
		 * @param String $orientation can be vertical/horizontal
		 */
		public function flip($orientation){
			if($orientation == 'vertical'){
				$this->uploadedFile->image_flip = 'v';
			}
			else{
				$this->uploadedFile->image_flip = 'h';
			}
			$this->outputJsonData();
		}
	
		/**
		 * Rotates an image
		 *
		 * @param Int $degrees 90/180/270
		 */
		public function rotate($degrees){
			$this->uploadedFile->image_rotate = $degrees;
			$this->outputJsonData();
		}
	
		/**
		 * Get negative of an image
		 *
		 */
		public function negative(){
			$this->uploadedFile->image_negative = true;
			$this->outputJsonData();
		}
	
		/**
		 * Tints an image
		 *
		 * @param String $color hex color
		 */
		public function tint($color){
			$this->uploadedFile->image_tint_color = '#'.$color;
			$this->outputJsonData();
		}
	
		/**
		 * Applies contrast to an image
		 *
		 * @param Int $value from -127 to 127
		 */
		public function contrast($value){
			$this->uploadedFile->image_contrast = (int)$value;
			$this->outputJsonData();
		}
	
		/**
		 * Applies brightness to an image
		 *
		 * @param Int $value from -127 to 127
		 */
		public function brightness($value){
			$this->uploadedFile->image_brightness = (int)$value;
			$this->outputJsonData();
		}
	
		/**
		 * Crops an image
		 *
		 * @param Int $width
		 * @param Int $height
		 * @param Int $xCoordinate
		 * @param Int $yCoordinate
		 */
		public function crop($width, $height, $xCoordinate, $yCoordinate){
			// get extension
			$extension = $this->uploadedFile->file_src_name_ext;
			
			// original image
			switch($extension){
				case 'jpg':
				case 'jpeg':
					$originalImage = imagecreatefromjpeg($this->destDirectory.'/'.$this->imageFile);
					break;
	
				case 'png':
					$originalImage = imagecreatefrompng($this->destDirectory.'/'.$this->imageFile);
					break;
	
				case 'gif':
					$originalImage = imagecreatefromgif($this->destDirectory.'/'.$this->imageFile);
					break;
			}
				
			// crop part
			$cropImage = imagecreatetruecolor($width, $height);
				
			// get original image width and height
			list($originalWidth, $originalHeight) = getimagesize($this->destDirectory.'/'.$this->imageFile);
				
			// do the cropping
			imagecopyresized($cropImage, $originalImage, 0, 0, $xCoordinate, $yCoordinate, $width, $height, $width, $height);
	
			//get body part of file name to generate a new one
			$bodyPart = $this->uploadedFile->file_src_name_body.'_1';
			
			while (@file_exists($this->processedDirectory.'/'.$bodyPart.'.'.$extension)) {
				$bodyPart = $bodyPart.'_1';
			}
			
			// new file name
			$newFileName = $bodyPart.'.'.$extension;
				
			switch($extension){
				case 'jpeg':
				case 'jpg':
					$result = imagejpeg($cropImage, $this->processedDirectory.'/'.$newFileName, $this->imageQuality);
					break;
	
				case 'png':
					$result = imagepng($cropImage, $this->processedDirectory.'/'.$newFileName);
					break;
	
				case 'gif':
					$result = imagegif($cropImage, $this->processedDirectory.'/'.$newFileName);
					break;
			}
				
			// output json data
			echo json_encode(array('image' => $newFileName, 'width' => $width, 'height' => $height));
		}
		
		/**
		 * Saves/Converts an image
		 *
		 * @param String $type gif/png/jpg/jpeg
		 */
		public function saveImage($type){
			// convert image
			$this->uploadedFile->image_convert = $type;
			
			// process it
			$this->uploadedFile->Process($this->processedDirectory);
			
			// check if the processing is ok
			if ($this->uploadedFile->processed) {
				// echo json data	
				echo json_encode(array('image' => $this->uploadedFile->file_dst_name));
			}
			else{
				echo json_encode(array('error' => $this->uploadedFile->error));
			}
		}

		/**
		 * Updates an image
		 */
		public function update(){
			$this->outputJsonData();
		}

		/**
		 * Outputs json encoded data to the browser
		 *
		 */
		private function outputJsonData(){
				
			$this->uploadedFile->Process($this->processedDirectory);
				
			// check if the processing is ok
			if ($this->uploadedFile->processed) {
				// get the filename
				$fileName = $this->uploadedFile->file_dst_name;
				// its width
				$width = $this->uploadedFile->image_dst_x;
				// and its height
				$height = $this->uploadedFile->image_dst_y;
				// encode everything and send to browser
				echo json_encode(array('image' => $fileName, 'width' => $width, 'height' => $height));
			}
			else{
				echo json_encode(array('error' => $this->uploadedFile->error));
			}
		}
	
		public function __destruct(){
//			$this->uploadedFile->clean();  //FIXME we have to KEEP the old file for undo, why is this even here?
		}
	
	}
?>