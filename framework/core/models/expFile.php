<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the self::getImageInfo class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/** @define "BASE" "../../.." */

 /**
  * Class to handle files at the File System Level and updating
  * the record for each file.
  *
  * expFile is an extension of expRecord because File information is stored
  * in the database for future access and retrival. This class also handles
  * and and all File System handling as well: copy, move, delete, upload,
  * and importing of data in preperation of data importation. Upload and
  * import via child classes.
  *
 * @subpackage Core-Datatypes
 * @package Framework
  *
  */
class expFile extends expRecord {

// ==========================================================
// Class Constants

	/*
	 * The definition of this constant lets other parts of the subsystem know
	 * that the Image Subsystem has been included for use.
	 */
	const SYS_IMAGE = 1;
	const IMAGE_ERR_NOGD = '';
	const IMAGE_ERR_NOTSUPPORTED ='_unknown';
	const IMAGE_ERR_FILENOTFOUND = '_notfound';
	const IMAGE_ERR_PERMISSIONDENIED = '_denied';
	

// ===========================================================
// File Access Control Values

   /**
    * Mode to use for reading from files
    * @constant string FILE_MODE_READ
    *
    * @access private
    * @PHPUnit Not Defined
    *
    */
    const FILE_MODE_READ = 'rb';

   /**
    * Mode to use for truncating files, then writing
    * @constant string FILE_MODE_WRITE
    *
    * @access private
    * @PHPUnit Not Defined
    *
    */
    const FILE_MODE_WRITE = 'wb';

   /**
    * Mode to use for appending to files
    * @constant string FILE_MODE_APPEND
    *
    * @access private
    * @PHPUnit Not Defined
    *
    */
    const FILE_MODE_APPEND = 'ab';

   /**
    * Use this when a shared (read) lock is required.
    * This is a "relabel" of the PHP 'LOCK_SH' constant
    * @constant string FILE_LOCK_SHARED
    *
    * @access private
    * @PHPUnit Not Defined
    *
    */
    const FILE_LOCK_SHARED = LOCK_SH;

   /**
    * Use this when an exclusive (write) lock is required
    * This is a "relabel" of the PHP 'LOCK_EX' constant
    * @constant string FILE_LOCK_EXCLUSIVE
    *
    * @access private
    * @PHPUnit Not Defined
    *
    */
    const FILE_LOCK_EXCLUSIVE = LOCK_EX;


  // ==========================================================
  // Class Properties and their default values

   /**
    * Database Table Name to store File info
    *
    * @public
    * @property string $table   Database Table Name
    *
    */
	public $table = 'expFiles';
	protected $attachable_table = 'content_expFiles';

    protected $attachable_item_types = array(
        //'content_expFiles'=>'expFile', 
        //'content_expTags'=>'expTag', 
        //'content_expComments'=>'expComment',
        //'content_expSimpleNote'=>'expSimpleNote',
    );
    
   /**
    * Relative OS System File path to where $filename is [to be] located
    *
    * @protected
    * @property string $directory  Relative OS System File path
    *
    */
    public $directory = null;

   /**
    * File Name of File to process
    *
    * @public
    * @property string $filename  Name of File to process
    *
    */
    public $filename = null;

   /**
    * Size of File, in Bytes
    *
    * @protected
    * @property string $filesize   Size of File, in Bytes.
    *
    */
    public $filesize = null;

   /**
    * Mime Type of File.
    *
    * @public 
    * @property string $mimetype   File MIME Type
    *
    */
    public $mimetype = null;

   /**
    * Image width in pixels.
    *
    * @public
    * @property string $image_width   Image width in pixels
    *
    */
    public $image_width = null;

   /**
    * Image height in pixels.
    *
    * @public
    * @property string $image_height   Image height in pixels
    *
    */
    public $image_height = null;

   /**
    * Is this file an image.
    # Defaultes to FALSE
    *
    * @public
    * @property string $is_image   Is this file an image
    *
    */
    public $is_image = false;


   /**
    * Determines if this file can be overwritten.
    * Also if it can be "moved" or "renamed" over
    * Default set to FALSE, no it can't
    *
    * @protected boolean
    * @property boolean $fileOverWrite Determines if this file be overwritten
    *
    * @access protected
    * @since 1.1
    */
    protected $fileOverWrite = false;



   /**
    * Web based Path for current File
    *
    * @public
    * @property string $url   Web based Path
    *
    */
	public $url = null;

   /**
    * Full File System Path for current File. Also used to in FILE Record
    *
    * @public
    * @property string $path   Full File System Path
    *
    */
    public $path = null;

   /**
    * Relative File System Path for current File
    *
    * @public
    * @property string $path_relative   Relative File System Path
    *
    */
    public $path_relative = null;



// ==========================================================
// Class Methods

	/**
	 * Class constructor to create a File Class either from a database
	 * record or from the File System.
	 *
	 * Class will either: a) load an existing File Record
	 *                    b) modify an existing File Record
	 *                    c) create a new File Record
	 *
	 * This will also handle any File System handling that is needed: copy,
	 * move, create, delete, read and write.
	 *
	 * @access public
	 *
	 * @uses expRecord::__construct
	 *
	 * @PHPUnit Not Defined
	 *
	 * @param mixed $params  - If an INT is given, this assumes that it needs to
	 *                         load an exisiting File Record.
	 *                       - If an ARRAY is given, this assumes that the elements
	 *                         of the array are values to the File table that need
	 *                         to be modifiy or other processing.
	 *                       - If NULL is given, an empty File Object is created
	 *
	 * @param bool $get_assoc
	 * @param bool $get_attached
	 * @return \expFile Object@throws void
	 *
	 */
	public function __construct($params = array(), $get_assoc = false, $get_attached = false) {
        // Set 'directory' as the default FILE location
        // This will be redefined if a FILE record is loaded
        // or a path is given to the Class
        //eDebug($params,true);
        $this->directory = UPLOAD_DIRECTORY_RELATIVE;
        // This will pull properties for class properties based upon
        // expRecord table definition
		parent::__construct($params, $get_assoc, $get_attached);

        // If the 'directory' is the same as the default path then a given,
        // or derived, filename can be added to pathing settings
        //if ( $this->directory == UPLOAD_DIRECTORY_RELATIVE ) {
        if (!stristr($this->directory, BASE)) {
             // Place system level web root
            $this->url = URL_FULL . $this->directory . $this->filename;

            // Place system level OS root
            $this->path = BASE.$this->directory . $this->filename;

            // Place system OS relative path
            $this->path_relative  = PATH_RELATIVE.$this->directory.$this->filename;
	    } else {
            // Otherwise, the URL is not set since we can't use it, niether is
            // RELATIVE, as 'directory' must be an absolute path in this instance
            // Place system level OS root
            $relpath = str_replace(BASE, '', $this->directory);
            $this->path = $this->directory . $this->filename;
            $this->url = URL_FULL .$relpath . $this->filename;
            $this->path_relative  = $relpath . $this->filename;
        }

        // If a file location was given, not derived from the database,
        // basic file information is needed
        if (empty($this->id) && !empty($this->filename)) {
            // File info
            $_fileInfo = self::getImageInfo($this->path);
            // Assign info back to class
            $this->is_image      = $_fileInfo['is_image'];
            $this->filesize      = $_fileInfo['fileSize'];
            if ( $_fileInfo['is_image'] ) {
                $this->mimetype      = $_fileInfo['mime'];
                $this->image_width   = $_fileInfo[0];
                $this->image_height  = $_fileInfo[1];
            }
        }
	}


// =========================================================================
// Static Methods


	/**
	 * File UPLOAD that also inserts File info into datbase.
	 *
	 * File UPLOAD is a straight forward uploader and processer. It can accept
	 * filename and destination directory overrides as well. It has an additional
	 * pair of flags that allow for an upload NOT to be inserted into the database
	 * (default to INSERT) and if it previous file, with the same name, should be
	 * overwritten (default to NO overwrite)
	 *
	 * @static
	 * @access public
	 *
	 * @uses class|method|global|variable description
	 * @requires class_name
	 *
	 * @PHPUnit Not Defined|Implement|Completed
	 *
	 * @param string $_postName  The name of the _FILE upload array
	 * @param bool|string $_force Force the uploaded to overwrite existing file of same name
	 * @param bool|string $_save Save file info to database, defaults to TRUE
	 * @param string $_destFile  Override the uploaded file name
	 * @param string $_destDir   Override the default FILE UPLOAD location
	 *
	 * @return object $_objFile expFile Object
	 * @return object $errMsg   Error message if something failed@throws void
	 *
	 * @TODO Have file upload overwrite make sure not to duplicate its record in the DB
	 *
	 */
    public static function fileUpload( $_postName = null,
                                       $_force    = false,
                                       $_save     = true,
                                       $_destFile = null,
                                       $_destDir  = null
                                      ) {

//        if (!defined('SYS_FILES')) include_once(BASE.'framework/core/subsystems-1/files.php');
//        include_once(BASE.'framework/core/subsystems-1/files.php');

        // Make sure something was sent first off...
        if ( ( !isset($_SERVER['CONTENT_TYPE'] )) ||
             ( strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== 0) )
        {
            return 'bad upload form';
        }

        //check for errors
		switch($_FILES[$_postName]['error']) {
			case UPLOAD_ERR_OK:
				// Everything looks good.  Continue with the update.
				break;
			case UPLOAD_ERR_INI_SIZE:
			case images:
				// This is a tricky one to catch.  If the file is too large for
                // POST, then the script won't even run.
				// But if its between post_max_size and upload_max_filesize,
                // we will get here.
				return 'file_too_large';
			case UPLOAD_ERR_PARTIAL:
				return 'partial_file';
			case UPLOAD_ERR_NO_FILE:
				return 'no_file_uploaded';
			default:
				return 'unknown';
				break;
		}
        

        // If $_destDir is not defined, use the default Files directory
        $_destDir = ( $_destDir == null ) ? UPLOAD_DIRECTORY : $_destDir;

		// If $_destFile is defined, use that name as an override for the
        // uploaded file name
		$_destFile = ($_destFile == null) ? self::fixFileName( $_FILES[$_postName]['name']) : $_destFile;

        // Fix the filename, so that we don't have funky characters screwing
        // with our attempt to create the destination file.
        // $_destFile = self::fixFileName( $_FILES[$_postName]['name']);
        // eDebug($_destFile,1);
		
        // Build destination fille path for future use
        $_destFullPath = $_destDir . $_destFile;
		
		//if the file exists and we don't want to overwrite it, create a new one			
		if (file_exists($_destFullPath) && $_force == false) {
			$_destFile = self::resolveDuplicateFilename($_destFullPath);
            $_destFullPath = $_destDir . $_destFile;
		}
        
		//Check to see if the directory exists.  If not, create the directory structure.
        // if (!file_exists(BASE . $_destDir)) {
        //  self::makeDirectory(BASE . $_destDir);
        // }    

		// Move the temporary uploaded file into the destination directory,
        // and change the name.
               
        move_uploaded_file($_FILES[$_postName]['tmp_name'], $_destFullPath);

        if (file_exists($_destFullPath)) {
            $__oldumask = umask(0);
            chmod($_destFullPath,FILE_DEFAULT_MODE);
            umask($__oldumask);
        } else {
			return 'could not move';
		}
		
		// At this point, we are good to go.
		
        // Create a new expFile Object for further processing
        $_fileParams = array ('filename'  => $_destFile);
        $_objFile = new expFile ( $_fileParams);

        // Insert new File Record
        if ( $_save === true ) {
            $_objFile->save();
        }
		return $_objFile;
    }


	/**
	 * Performs a system level check on the file and retrieves its size
	 *
	 * @static
	 * @access public
	 *
	 * @uses function filesize()    Built-in PHP method
	 *
	 * @PHPUnit Not Defined|Implement|Completed
	 *
	 * @param bool|string $_path Full path to file to pull info from
	 * @return int $_fileSize    Size of file in bytes
	 * @throws void
	 *
	 */

     public static function fileSize($_path = false)
     {
         if ( $_path )
             $_fileSize = filesize( $_path );
         else
             $_fileSize = 0;

         return $_fileSize;
     }


     /**
      * check for duplicate files and returns a file name that's not already in the system
      *
      * @static
      * @access public
      *
      * @uses function filesize()    Built-in PHP method
      *
      * @PHPUnit Not Defined|Implement|Completed
      *
      * @param string $filepath    direct path of the file to check againsts 
      * @return int $newFileName   Name of the file that isn't a duplicate
      * @throws void
      *
      */
      public static function resolveDuplicateFilename($filepath) {
          $extention = strrchr($filepath, "."); // grab the file extention by looking for the last dot in the string
          $filnameWoExt = str_replace($extention,"",str_replace("/","",strrchr($filepath, "/"))); // filename sans extention
          $pathToFile = str_replace($filnameWoExt.$extention,"",$filepath); // path sans filename
          
          $i = "";
          while (file_exists($pathToFile.$filnameWoExt.$inc.$extention)) {
              $i++;
              $inc = "-".$i;
          }
          
          //we'll just return the new filename assuming we've 
          //already got the path we want on the other side
          return $filnameWoExt.$inc.$extention;
      }


   /**
    * prompts the user to download a file
    *
    * @static
    * @access public
    *
    * @uses function download()    Built-in PHP method
    *
    * @PHPUnit Not Defined|Implement|Completed
    *
    * @param string $file       Full path to file to download
    * @return void
    * @throws void
    *
    */
	public static function download($file) {
		// we are expecting an int val as a file ID or the whole file object.
        // If all we get is the ID then we'll instantiate a new file object.
        // If that object doesn't have it's id property set or the file doesn't
        // actually exist then we can assume its not a valid file object and
        // return false.
		if (!is_object($file)) $file = new expFile($file); 
		//if (empty($file->id) || !file_exists($file->path)) return false;
        if (!file_exists($file->path)) return false;
        
		// NO buffering from here on out or things break unexpectedly. - RAM					
		ob_end_clean();		

		// This code was lifted from phpMyAdmin, but this is Open Source, right?
		// 'application/octet-stream' is the registered IANA type but
		// MSIE and Opera seems to prefer 'application/octetstream'
		// It seems that other headers I've added make IE prefer octet-stream again. - RAM

		$mimetype = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : $file->mimetype;

		header('Content-Type: ' . $mimetype);		
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		//header("Content-length: ".filesize($file->path));  // for some reason the webserver cant run stat on the files and this breaks.
		header('Content-Transfer-Encoding: binary');
		header('Content-Encoding:');
		header('Content-Disposition: attachment; filename="' . $file->filename . '";');
		// IE need specific headers
		if (EXPONENT_USER_BROWSER == 'IE') {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Vary: User-Agent');
		} else {
			header('Pragma: no-cache');
		}
		
		//Read the file out directly
		readfile($file->path);
		exit();
	}

   /**
    * Replace anything but alphanumeric characters with an UNDERSCORE
    *
    * @static
    * @access public
    *
    * @uses function preg_replace   built-in PHP Function
    *
    * @PHPUnit Not Defined|Implement|Completed
    *
    * @param string  $name   File name to 'fix'
    * @return string $name   the correct filename
    * @throws void
    *
    */
    public static function fixFileName($name) {
        return preg_replace('/[^A-Za-z0-9\.]/','_',$name);
    }


// ==========================================================
// Class Image Processing Methods
// @TODO  This collection of methods need to be placed in their own Class

	/**
	 * Return size and mimetype information about an image file,
	 * given its path/filename.  This is a wrapper around the
	 * built-in PHP 'getimagesize' function, to make all implementations
	 * work identically.
	 *
	 * @static
	 * @access public
	 *
	 * @uses function getimagesize()   Built-in PHP function
	 *
	 * @PHPUnit Not Defined|Implement|Completed
	 *
	 * @param bool|string $_path Full path to file to pull info from

	 *
	 * @return array  $_sizeinfo      An array of Image File info
	 * @return array $error message  Error message@throws void
	 *
	 */
	public static function getImageInfo($_path = false) {
        
        $_path = __realpath($_path);

		if (!file_exists($_path)) return self::IMAGE_ERR_FILENOTFOUND;
		if (!is_readable($_path)) return self::IMAGE_ERR_PERMISSIONDENIED;

        if ( $_sizeinfo = @getimagesize($_path) ) {
            $_sizeinfo['is_image'] = true;

    		if (!isset($_sizeinfo['mime'])) {
    			// In case this implementation of getimagesize doesn't discover
                // the mime type
    			$_types = array(
				'jpg'=>'image/jpeg',
				'jpeg'=>'image/jpeg',
				'gif'=>'image/gif',
				'png'=>'image/png'
			);

                $_fileData = pathinfo($_path);
                $_sizeinfo['mime'] = $_types[$_fileData['extension']];
			}
        } else {
            $_sizeinfo['is_image'] = false;
		}

        $_sizeinfo['fileSize'] = self::fileSize($_path);

		return $_sizeinfo;
	}


	/* exdoc
	 * Create an image resource handle (from GD) for a given filename.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * At this point, the user should have called self::getImageInfo on the filename
	 * and verified that the file does indeed exist, and is readable.  A safeguard check
	 * is in place, however.
	 *
	 * @param string $filename The path/filename of the image.
	 * @node Subsystems:expFile
	 */
	public static function openImageFile($filename) {
		if (!EXPONENT_HAS_GD) return null;

		$sizeinfo = @getimagesize($filename);
		$info = gd_info();

		if ($sizeinfo['mime'] == 'image/jpeg' && $info['JPG Support'] == true) {
			$img = imagecreatefromjpeg($filename);
		} else if ($sizeinfo['mime'] == 'image/png' && $info['PNG Support'] == true) {
			$img = imagecreatefrompng($filename);
		} else if ($sizeinfo['mime'] == 'image/gif' && $info['GIF Read Support'] == true) {
			$img = imagecreatefromgif($filename);
		} else {
			// Either we have an unknown image type, or an unsupported image type.
			return IMAGE_ERR_NOTSUPPORTED;
		}

		if (function_exists('imagesavealpha')) {
			imagealphablending($img, false);
			imagesavealpha($img, true);
		}
		return $img;
	}

	/* exdoc
	 * Create a new blank image resource, with the specified width and height.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param integer $w Width of the image resource to create (in pixels)
	 * @param integer $h Height of the image resource to create (in pixels)
	 * @node Subsystems:Image
	 */
	public static function imageCreate($w,$h) {
		if (!EXPONENT_HAS_GD) {
			return null;
		}
		$info = gd_info();
		if (strpos($info['GD Version'],'2.0') !== false) {
			$img = imagecreatetruecolor($w,$h);

			if (function_exists('imagesavealpha')) {
				imagealphablending($img, false);
				imagesavealpha($img, true);
			}

			return $img;
		} else {
			return imagecreate($w,$h);
		}
	}
	/* exdoc
	 * Create a new blank image resource, with the specified width and height.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param integer $w Width of the image resource to create (in pixels)
	 * @param integer $h Height of the image resource to create (in pixels)
	 * @node Subsystems:Image
	 */
	function copyToDirectory($destination) {
	    //eDebug($this,true);
        copy($this->path,$destination.$this->filename);
	}

	public static function imageCopyresized($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
		if (!EXPONENT_HAS_GD) {
			return null;
		}
		$info = gd_info();
		if (strpos($info['GD Version'],'2.0') !== false) {
			return imagecopyresampled($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		} else {
			return imagecopyresized($dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		}
	}

	/* exdoc
	 * Proportionally scale an image by a specific percentage
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param decimal $scale The scaling factor, as a decimal (i.e. 0.5 for 50%)
	 * @node Subsystems:Image
	 */
	public static function imageScaleByPercent($filename,$scale) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		if ($scale == 1) {
			return $original;
		}

		$w = $scale * $sizeinfo[0];
		$h = $scale * $sizeinfo[1];

		$thumb = self::imageCreate($w,$h);
		if (!$thumb) return null;
		self::imageCopyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

		return $thumb;
	}

	/* exdoc
	 * Proportionally scale an image to a given width. Height adjusts accordingly.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param integer $width The desired width of the scaled image, in pixels.
	 * @node Subsystems:Image
	 */
	public static function imageScaleToWidth($filename,$width) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}
		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $sizeinfo;
		}

		if ($width == $sizeinfo[0]) {
			return $original;
		}

		$w = $width;
		$h = ($width / $sizeinfo[0]) * $sizeinfo[1];

		$thumb = self::imageCreate($w,$h);
		if (!$thumb) return null;
		self::imageCopyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

		return $thumb;
	}

	/* exdoc
	 * Proportionally scale an image to a given height.  Width adjusts accordingly.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param integer $height The desired height of the scaled image, in pixels.
	 * @node Subsystems:Image
	 */
	public static function imageScaleToHeight($filename,$height) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		if ($height == $sizeinfo[1]) {
			return $original;
		}

		$w = ($height / $sizeinfo[1]) * $sizeinfo[0];
		$h = $height;

		$thumb = self::imageCreate($w,$h);
		if (!$thumb) return null;
		self::imageCopyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

		return $thumb;
	}

	/* exdoc
	 * Proportionally scale an image to fit within the given width / height.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param integer $width The maximum width of the scaled image, in pixels.
	 * @param integer $height The maximum height of the scaled image, in pixels.
	 * @node Subsystems:Image
	 */
	public static function imageScaleToConstraint($filename,$width,$height) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		if ($width >= $sizeinfo[0] && $height >= $sizeinfo[1]) {
			return $original;
		}

		$w = $width;
		$h = ($width / $sizeinfo[0]) * $sizeinfo[1];

		if ($h > $height) { // height is outside
			$w = ($height / $sizeinfo[1]) * $sizeinfo[0];
			$h = $height;
		}

		$thumb = self::imageCreate($w,$h);
		if (!$thumb) return null;
		self::imageCopyresized($thumb,$original,0,0,0,0,$w,$h,$sizeinfo[0],$sizeinfo[1]);

		return $thumb;
	}
	/* exdoc
	 * Scale an image to a square keeping the image aspect ratio.
	 * If the image is smaller in either dimension than request square side original is returned.
	 * Image is first cropped to a square of length smaller of width or height and then resized.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param integer $size The desired side length of the scaled image, in pixels.
	 * @node Subsystems:Image
	 */
	public static function imageScaleToSquare($filename,$side) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		if ($side >= $sizeinfo[0] || $side >= $sizeinfo[1]) {
			return $original;
		}

		/* The defaults will serve in case the image is a square */
		$src_x = 0;
		$src_y = 0;
		$width = $sizeinfo[0];
		$height = $sizeinfo[1];

		/*if width greater than height, we crop the image left and right */
		if ($sizeinfo[0] > $sizeinfo[1]) {
			$width=$sizeinfo[1];
			$height=$sizeinfo[1];
			$src_x=round(($sizeinfo[0]-$width)/2,0);
		}
		else
		{
		/*if height greater than width, we crop the image top and bottom */
			$height=$sizeinfo[0];
			$width=$sizeinfo[0];
			$src_y=round(($sizeinfo[1]-$height)/2,0);
		}

		$w = $side;
		$h = $side;

		$thumb = self::imageCreate($w,$h);
		if (!$thumb) return null;
	   self::imageCopyresized($thumb,$original,0,0,$src_x,$src_y,$w,$h,$width,$height);

		return $thumb;
	}

	/* exdoc
	 * Scale an image to a given width and height, without regard to aspect ratio.
	 * This is a wrapper around various GD functions, to provide Exponent
	 * programmers a single point of entry.  It also handles situations where
	 * there is no GD support compiled into the server.  (In this case, null is returned).
	 *
	 * @param string $filename The path/filename of the image to scale.
	 * @param integer $width The desired width of the scaled image, in pixels.
	 * @param integer $height The desired height of the scaled image, in pixels.
	 * @node Subsystems:Image
	 */
	public static function imageScaleManually($filename,$width,$height) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		if ($width == $sizeinfo[0] && $height == $sizeinfo[1]) {
			return $original;
		}

		$thumb = self::imageCreate($width,$height);
		if (!$thumb) return null;
		self::imageCopyresized($thumb,$original,0,0,0,0,$width,$height,$sizeinfo[0],$sizeinfo[1]);

		return $thumb;
	}

	public static function imageRotate($filename,$degrees) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		$color = imagecolorclosesthwb($original,255,255,255);

		return imagerotate($original,$degrees,$color);
	}

	public static function imageFlip($filename,$is_horizontal) {
		$sizeinfo = self::getImageInfo($filename);
		if (!is_array($sizeinfo)) {
			return $sizeinfo;
		}

		$original = self::openImageFile($filename,$sizeinfo);
		if (!is_resource($original)) {
			return $original;
		}

		// Horizontal - invert y coords
		// Vertical - invert x coords

		$w = $sizeinfo[0];
		$h = $sizeinfo[1];
		$new = self::imageCreate($w,$h);

		if ($is_horizontal) {
			// Copy column by column
			//$dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
			for ($i = 0; $i < $w; $i++) {
				imagecopy($new,$original, // DESTINATION, SOURCE
				$i,0,		// dst_X, dst_Y
				$w-$i-1,0,	// src_X,src_Y
				1,$h);		//src_W, src_H
			}
		} else {
			// Copy row by row.
			//$dest,$src,$dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
			for ($i = 0; $i < $h; $i++) {
				imagecopy($new,$original, // DESTINATION, SOURCE
				0,$i,		// dst_X, dst_Y
				0,$h-$i-1,	// src_X,src_Y
				#$w,1,		// dst_W, dst_H
				$w,1);		//src_W, src_H
			}
		}
		return $new;
	}

	/* exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node Undocumented
	 */
	public static function imageOutput($img, $sizeinfo, $filename=null,  $quality=75) {
		header('Content-type: ' . $sizeinfo['mime']);
		if ($sizeinfo['mime'] == 'image/jpeg') {
			($filename != null) ? imagejpeg($img, $filename, $quality) : imagejpeg($img, null, $quality);
		} else if ($sizeinfo['mime'] == 'image/png') {
			($filename != null) ? imagepng($img,$filename) : imagepng($img);
		} else if ($sizeinfo['mime'] == 'image/gif') {
			($filename != null) ? imagepng($img,$filename) : imagepng($img);
		}
	}

	/* exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node Undocumented
	 */
	public static function imageCaptcha($w,$h,$string) {
		$img = self::imageCreate($w,$h);
		if ($img) {
			// We were able to create an image.
			$bg = 		imagecolorallocate($img,250,255,225);
			imagefill($img,0,0,$bg);
			#echo $bg;
			$colors = array();
			for ($i = 0; $i < strlen($string) && $i < 10; $i++) {
				$colors[$i] = imagecolorallocate($img,rand(50,150),rand(50,150),rand(50,150));
			}
			$px_per_char = floor($w / (strlen($string)+1));
			for ($i = 0; $i < strlen($string); $i++) {
				imagestring($img,rand(4,6),$px_per_char * ($i+1) + rand(-5,5),rand(0,$h / 2),$string{$i},$colors[($i % 10)]);
			}

			// Need this to be 'configurable'
			for ($i = 0; $i < strlen($string) / 2 && $i < 10; $i++) {
				$c = imagecolorallocate($img,rand(150,250),rand(150,250),rand(150,250));
				imageline($img,rand(0,$w / 4),rand(5, $h-5),rand(3*$w / 4,$w),rand(0,$h),$c);
			}

			//imagestring($img,6,0,0,$string,$color);
			return $img;
		} else {
			return null;
		}
	}
    
    
    
    static function recurse_copy($src,$dst) {
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                } 
                else { 
                    if(!copy($src . '/' . $file,$dst . '/' . $file)){
                        return false;
                    };
                } 
            } 
        } 
        closedir($dir); 
        return true;
    }

	/**
	 * Recursively removes all files in a given directory, and all
	 * the files and directories underneath it.
	 * Optionally can skip dotfiles
	 *
	 * @param string $dir directory to work with
	 * @param bool $dot_files should dotfiles be removed?
	 * @return array
	 */
	public static function removeFilesInDirectory($dir, $dot_files = false) {
		$results['removed'] = array();
		$results['not_removed'] = array();

		$files = scandir($dir);
		array_shift($files);    // remove '.' from array
		array_shift($files);    // remove '..' from array
		foreach ($files as $file) {
			if ($dot_files || substr($file, 0, 1) != '.') {  // don't remove dot files
				$file = $dir . '/' . $file;
				if (is_dir($file)) {
					self::removeFilesInDirectory($file);
					rmdir($file);
				} else {
					if (is_writeable($file) && !is_dir($file)) {
						unlink($file);
						$results['removed'][] = $file;
					} else {
						$results['not_removed'][] = $file;
					}
				}
			}
		}

	/*	old routine
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				$filepath = $dir.'/'.$file;
				if (substr($file,0,1) != '.') {
					if (is_writeable($filepath) && !is_dir($filepath)) {
						unlink($filepath);
						$results['removed'][] = $filepath;
					} else {
						$results['not_removed'][] = $filepath;
					}
				}
			}
		}*/

		return $results;
	}

	/* exdoc
	 * This method creates a directory and all of its parent directories, if they do not exist,
	 * emulating the behavior of the -p option to mkdir on UNIX systems.  Returns
	 * a SYS_FILES_* constant, indicating its status.
	 *
	 * @param string $dir The directory to create.  This path must be relative to BASE
	 * @node Subsystems:Files
	 */
	public static function makeDirectory($dir,$mode=null,$is_full=false) {
		$__oldumask = umask(0);
		$parentdir = ($is_full ? "/" : BASE); // we will add to parentdir with each directory
		foreach (explode("/",$dir) as $part) {
			if ($part != "" && !is_dir($parentdir.$part)) {
				// No parent directory.  Create it.
				if (is_file($parentdir.$part)) return SYS_FILES_FOUNDFILE;
				if (expUtil::isReallyWritable($parentdir)) {
					if ($mode == null) $mode = DIR_DEFAULT_MODE+0;
					mkdir($parentdir.$part,$mode);
					chmod($parentdir.$part,$mode);
				} else return SYS_FILES_NOTWRITABLE;
			}
			$parentdir .= $part."/";
		}
		umask($__oldumask);
		return SYS_FILES_SUCCESS;
	}

	/**
	 * Recursively removes the given directory, and all
	 * of the files and directories underneath it.
	 *
	 * @param string $dir The path of the directory to remove
	 * @node Subsystems:Files
	 * @param string $dir directory to work with
	 * @return int
	 */
	public static function removeDirectory($dir) {
		if (strpos($dir,BASE) != 0) $dir = BASE.$dir;
		$dh = opendir($dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && is_dir("$dir/$file")) {
					if (self::removeDirectory("$dir/$file") == SYS_FILES_NOTDELETABLE) return SYS_FILES_NOTDELETABLE;
				} else if (is_file("$dir/$file") || is_link(is_file("$dir/$file"))) {
					unlink("$dir/$file");
					if (file_exists("$dir/$file")) {
						return SYS_FILES_NOTDELETABLE;
					}
				}
				else if ($file != "." && $file != "..") {
					echo "BAD STUFF HAPPENED<br />";
					echo "--------Don't know what to do with $dir/$file<br />";
					echo "<xmp>";
					print_r(stat("$dir/$file"));
					echo filetype("$dir/$file");
					echo "</xmp>";
				}
			}
		}
		rmdir($dir);
	}

	public static function fixName($name) {
		return preg_replace('/[^A-Za-z0-9\.]/','_',$name);
	}

	/* exdoc
	* Move an uploaded temporary file to a more permanent home inside of the Exponent files/ directory.
	* This function takes into account the default file modes specified in the site configuration.
	 * @param string $tmp_name The temporary path of the uploaded file.
	 * @param string $dest The full path to the destination file (including the destination filename).
	 * @node Subsystems:Files
	 */
	public static function moveUploadedFile($tmp_name,$dest) {
		move_uploaded_file($tmp_name,$dest);
		if (file_exists($dest)) {
			$__oldumask = umask(0);
			chmod($dest,FILE_DEFAULT_MODE);
			umask($__oldumask);
		}
	}

	/* exdoc
	 * Checks to see if the upload destination file exists.  This is to prevent
	 * accidentally uploading over the top of another file.
	 * Returns true if the file already exists, and false if it does not.
	 *
	 * @param string $dir The directory to contain the existing directory.
	 * @param string $name The name of the file control used to upload the
	 *  file.  The files subsystem will look to the $_FILES array
	 *  to get the filename of the uploaded file.
	 * @node Subsystems:Files
	 */
	public static function uploadDestinationFileExists($dir,$name) {
		return (file_exists(BASE.$dir."/".self::fixName($_FILES[$name]['name'])));
	}

	/* exdoc
	 * Lists files and directories under a given parent directory. Returns an
	 * associative, flat array of files and directories.  The key is the full file
	 * or directory name, and the value is the file or directory name.
	 *
	 * @param string $dir The path of the directory to look at.
	 * @param boolean $recurse A boolean dictating whether to descend into subdirectories
	 * 	recursviely, and list files and subdirectories.
	 * @param string $ext An optional file extension.  If specified, only files ending with
	 * 	that file extension will show up in the list.  Directories are not affected.
	 * @param array $exclude_dirs An array of directory names to exclude.  These names are
	 * 	path-independent.  Specifying "dir" will ignore all directories and
	 * 	sub-directories named "dir", regardless of their parent.
	 * @node Subsystems:Files
	 */
	public static function listFlat($dir, $recurse = false, $ext=null, $exclude_dirs = array(), $relative = "") {
		$files = array();
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (is_dir("$dir/$file") && !in_array($file,$exclude_dirs) && $recurse && $file != "." && $file != ".." && $file != "CVS") {
					$files = array_merge($files,self::listFlat("$dir/$file",$recurse,$ext,$exclude_dirs,$relative));
				}
				if (is_file("$dir/$file") && ($ext == null || substr($file,-1*strlen($ext),strlen($ext)) == $ext)) {
					$files[str_replace($relative,"","$dir/$file")] = $file;
				}
			}
		}
		return $files;
	}

	/* exdoc
	 * Looks at the filesystem strucutre surrounding the destination
	 * and determines if the web server can create a new file there.
	 * Returns one of the following:
	 *	<br>SYS_FILES_NOTWRITABLE - unable to create files in destination
	 *	<br>SYS_FILES_SUCCESS - A file or directory can be created in destination
	 *	<br>SYS_FILES_FOUNDFILE - Found destination to be a file, not a directory
	 *
	 * @param string $dest Path to the directory to check
	 * @node Subsystems:Files
	 */
	public static function canCreate($dest) {
		if (substr($dest,0,1) == '/') $dest = str_replace(BASE,'',$dest);
		$parts = explode('/',$dest);
		$working = BASE;
		for ($i = 0; $i < count($parts); $i++) {
			if ($parts[$i] != '') {
				if (!file_exists($working.$parts[$i])) {
					return (expUtil::isReallyWritable($working) ? SYS_FILES_SUCCESS : SYS_FILES_NOTWRITABLE);
				}
				$working .= $parts[$i].'/';
			}
		}
		// If we got this far, then the file we are asking about already exists.
		// Check to see if we can overrwrite this file.
		// First however, we need to strip off the '/' that was added a few lines up as the last part of the for loop.
		$working = substr($working,0,-1);

		if (!expUtil::isReallyWritable($working)) {
			return SYS_FILES_NOTWRITABLE;
		} else {
			if (is_file($working)) {
				return SYS_FILES_FOUNDFILE;
			} else {
				return SYS_FILES_FOUNDDIR;
			}
		}
	}

	/* exdoc
	 * Copies just the directory structure (including subdirectories) of a given directory.
	 * Any files in the source directory are ignore, and duplicate copies are made (no symlinks).
	 *
	 * @param string $src The directory to copy structure from.  This must be a full path.
	 * @param string $dest The directory to create duplicate structure in.  If this directory is not empty,
	 * 	you may run into some problems, because of file/directory conflicts.
	 * @param $exclude_dirs An array of directory names to exclude.  These names are
	 * 	path-independent.  Specifying "dir" will ignore all directories and
	 * 	sub-directories named "dir", regardless of their parent.
	 * @node Subsystems:Files
	 */
	public static function copyDirectoryStructure($src,$dest,$exclude_dirs = array()) {
		$__oldumask = umask(0);
		if (!file_exists($dest)) mkdir($dest,fileperms($src));
		$dh = opendir($src);
		while (($file = readdir($dh)) !== false) {
			if (is_dir("$src/$file") && !in_array($file,$exclude_dirs) && substr($file,0,1) != "." && $file != "CVS") {
				if (!file_exists("$dest/$file")) mkdir("$dest/$file",fileperms("$src/$file"));
				if (is_dir("$dest/$file")) {
					self::copyDirectoryStructure("$src/$file","$dest/$file");
				}
			}
		}
		umask($__oldumask);
	}

}
?>
