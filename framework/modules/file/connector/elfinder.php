<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/**
 * Implements elFinder as the 'connector' referenced by 'url' param in elfinder.tpl
 */

require_once("../../../../exponent.php");
if (empty($user->id))
    exit();

if (DEVELOPMENT) {
    set_time_limit(0);
} // just in case it too long, not recommended for production

ini_set('max_file_uploads', FM_SIMLIMIT); // allow uploading up to FM_SIMLIMIT files at once

// needed for case insensitive search to work, due to broken UTF-8 support in PHP
//ini_set('mbstring.internal_encoding', 'UTF-8');
//ini_set('mbstring.func_overload', 2);

// elFinder autoload
//require './autoload.php'; //note in Exp we load manually
include BASE . 'external/elFinder/php/elFinderConnector.class.php';
include BASE . 'external/elFinder/php/elFinder.class.php';
include BASE . 'external/elFinder/php/editors/editor.php';
//include BASE . 'external/elFinder/php/editors/ZohoOffice/editor.php';
//include BASE . 'external/elFinder/php/editors/ZipArchive/editor.php';
//include BASE . 'external/elFinder/php/editors/OnlineConvert/editor.php';

include BASE . 'external/elFinder/php/elFinderPlugin.php';
//include BASE . 'external/elFinder/php/libs/GdBmp.php';  // will also autoload if needed
//include BASE . 'external/elFinder/php/plugins/AutoResize/plugin.php'; // plugins will autoload if needed
//include BASE . 'external/elFinder/php/plugins/AutoRotate/plugin.php';
//include BASE . 'external/elFinder/php/plugins/Normalizer/plugin.php';
//include BASE . 'external/elFinder/php/plugins/Sanitizer/plugin.php';
//include BASE . 'external/elFinder/php/plugins/Watermark/plugin.php';
//include BASE . 'external/elFinder/php/elFinderSessionInterface.php'; // will also autoload if needed
//include BASE . 'external/elFinder/php/elFinderSession.php'; // will also autoload if needed

include BASE . 'framework/modules/file/connector/elFinderExponent.class.php'; // our custom elFinder object

include BASE . 'external/elFinder/php/elFinderVolumeDriver.class.php';
include BASE . 'external/elFinder/php/elFinderVolumeLocalFileSystem.class.php';
include BASE . 'external/elFinder/php/elFinderVolumeTrash.class.php';
include BASE . 'framework/modules/file/connector/elFinderVolumeExponent.class.php'; // our custom elFinder volume driver

define('ELFINDER_IMG_PARENT_URL', PATH_RELATIVE . 'external/elFinder/');

function debug($o)
{
    echo '<pre>';
    print_r($o);
}

/**
 * example logger function
 * Demonstrate how to work with elFinder event api
 *
 * @param  string   $cmd      command name
 * @param  array    $result   command result
 * @param  array    $args     command arguments from client
 * @param  elFinder $elfinder elFinder instance
 *
 * @return void|true
 * @author Troex Nevelin
 **/
function logger($cmd, $result, $args, $elfinder)
{
    if (DEVELOPMENT && LOGGER) {
        $log = sprintf("[%s] %s: %s \n", date('r'), strtoupper($cmd), var_export($result, true));
        $logfile = BASE . 'tmp/elfinder.log';
        $dir = dirname($logfile);
        if (!is_dir($dir) && !mkdir($dir, octdec(DIR_DEFAULT_MODE_STR + 0))) {
            return;
        }
        if (($fp = fopen($logfile, 'ab'))) {
            fwrite($fp, $log);
            fclose($fp);
        }
        return;

//        // alternative logging method
//        foreach ($result as $key => $value) {
//            if (empty($value)) {
//                continue;
//            }
//            $data = array();
//            if (in_array($key, array('error', 'warning'))) {
//                array_push($data, implode(' ', $value));
//            } else {
//                if (is_array($value)) { // changes made to files
//                    foreach ($value as $file) {
//                        $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
//                        array_push($data, $filepath);
//                    }
//                } else { // other value (ex. header)
//                    array_push($data, $value);
//                }
//            }
//            $log .= sprintf(' %s(%s)', $key, implode(', ', $data));
//        }
//        $log .= "\n";
//
//        $logfile = BASE . 'tmp/elfinder.log';
//        $dir = dirname($logfile);
//        if (!is_dir($dir) && !mkdir($dir)) {
//            return;
//        }
//        if (($fp = fopen($logfile, 'a'))) {
//            fwrite($fp, $log);
//            fclose($fp);
//        }
    }
}

/**
 * example logger class
 * Demonstrate how to work with elFinder event api.
 *
 * @package elFinder
 * @author  Dmitry (dio) Levashov
 *
 * @package    elFinder
 * @subpackage Utility
 **/
class elFinderSimpleLogger
{

    /**
     * Log file path
     *
     * @var string
     **/
    protected $file = '';

    /**
     * constructor
     *
     * @param $path
     *
     * @author Dmitry (dio) Levashov
     */
    public function __construct($path)
    {
        $this->file = $path;
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, octdec(DIR_DEFAULT_MODE_STR + 0));
        }
    }

    /**
     * Create log record
     *
     * @param  string   $cmd      command name
     * @param  array    $result   command result
     * @param  array    $args     command arguments from client
     * @param  elFinder $elfinder elFinder instance
     *
     * @return void|true
     * @author Dmitry (dio) Levashov
     **/
    public function log($cmd, $result, $args, $elfinder)
    {
        if (DEVELOPMENT && LOGGER) {
            $log = $cmd . ' [' . date('d.m H:s') . "]\n";

            if (!empty($result['error'])) {
                $log .= "\tERROR: " . implode(' ', $result['error']) . "\n";
            }

            if (!empty($result['warning'])) {
                $log .= "\tWARNING: " . implode(' ', $result['warning']) . "\n";
            }

            if (!empty($result['removed'])) {
                foreach ($result['removed'] as $file) {
                    // removed file contain additional field "realpath"
                    $log .= "\tREMOVED: " . $file['realpath'] . "\n";
                }
            }

            if (!empty($result['added'])) {
                foreach ($result['added'] as $file) {
                    $log .= "\tADDED: " . $elfinder->realpath($file['hash']) . "\n";
                }
            }

            if (!empty($result['changed'])) {
                foreach ($result['changed'] as $file) {
                    $log .= "\tCHANGED: " . $elfinder->realpath($file['hash']) . "\n";
                }
            }

            $this->write($log);
            $this->write(var_export($result, true), true);
        }
    }

    /**
     * Write log into file
     *
     * @param  string $log log record
     * @param bool $eol
     *
     * @return void
     * @author Dmitry (dio) Levashov
     */
    protected function write($log, $eol=false)
    {
        if ($eol)
            $eol = "\n";
        if (($fp = @fopen($this->file, 'ab'))) {
            fwrite($fp, $log . $eol);
            fclose($fp);
        }
    }

} // END class
//$logger = new elFinderSimpleLogger(BASE.'tmp/elfinder.log');

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string    $attr    attribute name (read|write|locked|hidden)
 * @param  string    $path    absolute file path
 * @param  string    $data    value of volume option `accessControlData`
 * @param  object    $volume  elFinder volume driver object
 * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
 * @param  string    $relpath file path relative to volume root directory started with directory separator
 * @return bool|null
 */
function access($attr, $path, $data, $volume, $isDir, $relpath) {
    $basename = basename($path);
   	return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
   			 && strlen($relpath) !== 1           // but with out volume root
   		? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
   		:  null;                                 // else elFinder decide it itself
}

/**
 * example accessControl class
 *
 * @author Dmitry (dio) Levashov
 *
 * @package    elFinder
 * @subpackage Utility
 **/
class elFinderTestACL
{

    /**
     * make dotfiles not readable, not writable, hidden and locked
     *
     * @param  string               $attr   attribute name (read|write|locked|hidden)
     * @param  string               $path   file path. Attention! This is path relative to volume root directory started with directory separator.
     * @param  mixed                $data   data which seted in 'accessControlData' elFinder option
     * @param  elFinderVolumeDriver $volume volume driver
     *
     * @return bool
     * @author Dmitry (dio) Levashov
     **/
    public function fsAccess($attr, $path, $data, $volume)
    {

        if ($volume->name() == 'localfilesystem') {
            return strpos(basename($path), '.') === 0
                ? !($attr == 'read' || $attr == 'write')
                : $attr == 'read' || $attr == 'write';
        }

        return true;
    }

} // END class
//$acl = new elFinderTestACL();

/**
 * example acceptedName function
 * @param $name
 *
 * @return bool
 */
function validName($name)
{
    return strpos($name, '.') !== 0;
}

$opts = array(
    'locale' => LOCALE . '.' . LANG_CHARSET,
    'bind'   => array(
        // '*' => 'logger',
        'mkdir mkfile rename duplicate upload rm paste' => 'logger', // use function style logger
//        'mkdir mkfile rename duplicate upload rm paste' => array($logger, 'log'), // use class style logger
//        'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
//            'Plugin.Normalizer.cmdPreprocess',
//            'Plugin.Sanitizer.cmdPreprocess',
//        ),
//        'ls' => array(
//            'Plugin.Normalizer.cmdPostprocess',
//            'Plugin.Sanitizer.cmdPostprocess',
//        ),
//        'archive.pre ls.pre mkdir.pre mkfile.pre rename.pre upload.pre' => array(
//            'Plugin.Normalizer.cmdPreprocess',
//            'Plugin.Sanitizer.cmdPreprocess'
//        ),
        'upload.presave'  => array(
//            'Plugin.AutoRotate.onUpLoadPreSave',
            'Plugin.AutoResize.onUpLoadPreSave',
//            'Plugin.Watermark.onUpLoadPreSave',
//            'Plugin.Normalizer.onUpLoadPreSave',
//            'Plugin.Sanitizer.onUpLoadPreSave',
        ),
    ),
    // global plugin configure (optional)
    'plugin' => array(
        'AutoResize' => array(
            'enable'     => UPLOAD_WIDTH,     // For control by volume driver
            'maxWidth'   => UPLOAD_WIDTH,
            'maxHeight'  => UPLOAD_WIDTH,
            'quality'    => THUMB_QUALITY,    // JPEG image save quality
//            'preserveExif'   => false,        // Preserve EXIF data (Imagick only)
//            'forceEffect'    => false,        // For change quality or make progressive JPEG of small images
            'targetType' => IMG_GIF | IMG_JPG | IMG_PNG | IMG_WBMP, // Target image formats ( bit-field )
//            'offDropWith'    => null,         // To disable it if it is dropped with pressing the meta key
//        			                            // Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
//        			                            // In case of using any key, specify it as an array
//            'onDropWith'     => null          // Disabled by default. To enable it if it is dropped with pressing the meta key
//                                              // Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
//                                              // In case of using any key, specify it as an array

        ),
//        'AutoRotate' => array(
//            'enable'         => true,       // For control by volume driver
//            'quality'        => 95,          // JPEG image save quality
//            'offDropWith'    => null        // To disable it if it is dropped with pressing the meta key
     			                              // Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
     			                              // In case of using any key, specify it as an array
//        )
//        'Normalizer' => array(
//            'enable' => true,
//            'nfc'    => true,
//            'nfkc'   => true,
//			  'umlauts'   => false,
//            'lowercase' => false,
//            'convmap'   => array()
//        ),
//       'Sanitizer' => array(
//           'enable' => true,
//           'targets'  => array('\\','/',':','*','?','"','<','>','|'), // target chars
//           'replace'  => '_'    // replace to this
//           'pathAllows' => array('/') // Characters allowed in path name of characters in `targets` array
//        ),
//        'Watermark' => array(
//            'enable'         => true,       // For control by volume driver
//            'source'         => 'logo.png', // Path to Water mark image
//            'ratio'          => 0.2,        // Ratio to original image (ratio > 0 and ratio <= 1)
//            'position'       => 'RB',       // Position L(eft)/C(enter)/R(ight) and T(op)/M(edium)/B(ottom)
//            'marginX'        => 5,          // Margin horizontal pixel
//            'marginY'        => 5,          // Margin vertical pixel
//            'quality'        => THUMB_QUALITY,         // JPEG image save quality
//            'transparency'   => 70,         // Water mark image transparency ( other than PNG )
//            'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
//            'targetMinPixel' => 200         // Target image minimum pixel size
//			  'interlace'      => IMG_GIF|IMG_JPG, // Set interlacebit image formats ( bit-field )
//            'offDropWith'    => null        // To disable it if it is dropped with pressing the meta key
     			                              // Alt: 8, Ctrl: 4, Meta: 2, Shift: 1 - sum of each value
     			                              // In case of using any key, specify it as an array
//        ),
    ),
    'debug'  => DEVELOPMENT,
//	'netVolumesSessionKey' => 'netVolumes',
    'callbackWindowURL' => makeLink(array('controller'=>'file','action'=>'picker','ajax_action'=>1)),

    'roots'  => array(
        array(
            'id'              => 'exp2',
            'driver'          => 'Exponent',
            'path'            => BASE . 'files/',
            'URL'             => URL_FULL . 'files/',
            'trashHash'       => 'tt1_Lw',                     // elFinder's hash of trash folder
            'winHashFix'      => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            'dirMode'         => octdec(DIR_DEFAULT_MODE_STR + 0),    // new dirs mode (default 0755)
            'fileMode'        => octdec(FILE_DEFAULT_MODE_STR + 0),   // new files mode (default 0644)
            'detectDirIcon'   => '.foldericon.png',       // File to be detected as a folder icon image (elFinder >= 2.1.10) e.g. '.favicon.png'
            'keepTimestamp'   => array('copy', 'move'),   // Keep timestamp at inner filesystem (elFinder >= 2.1.12) It allowed 'copy', 'move' and 'upload'.
            // 'treeDeep'        => 3,
            'checkSubfolders' => -1,
            'alias'           => 'files',
            'disabled'        => array('netmount'),
//            'maxArcFilesSize' => 100,
            'accessControl'   => 'access',
            // 'accessControl' => array($acl, 'fsAccess'),
            // 'accessControlData' => array('uid' => 1),
            'uploadDeny'      => array('all'),                // NO Mimetype allowed to upload
            'uploadAllow'     => array(
                'application/arj',
                'application/excel',
                'application/gnutar',
                'application/mspowerpoint',
                'application/msword',
                'application/octet-stream',
                'application/onenote',
                'application/pdf',
                'application/plain',
                'application/postscript',
                'application/powerpoint',
                'application/rar',
                'application/rtf',
                'application/vnd.ms-excel',
                'application/vnd.ms-excel.addin.macroEnabled.12',
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'application/vnd.ms-excel.sheet.macroEnabled.12',
                'application/vnd.ms-excel.template.macroEnabled.12',
                'application/vnd.ms-office',
                'application/vnd.ms-officetheme',
                'application/vnd.ms-powerpoint',
                'application/vnd.ms-powerpoint.addin.macroEnabled.12',
                'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'application/vnd.ms-powerpoint.slide.macroEnabled.12',
                'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
                'application/vnd.ms-powerpoint.template.macroEnabled.12',
                'application/vnd.ms-word',
                'application/vnd.ms-word.document.macroEnabled.12',
                'application/vnd.ms-word.template.macroEnabled.12',
                'application/vnd.oasis.opendocument.chart',
                'application/vnd.oasis.opendocument.database',
                'application/vnd.oasis.opendocument.formula',
                'application/vnd.oasis.opendocument.graphics',
                'application/vnd.oasis.opendocument.graphics-template',
                'application/vnd.oasis.opendocument.image',
                'application/vnd.oasis.opendocument.presentation',
                'application/vnd.oasis.opendocument.presentation-template',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.spreadsheet-template',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.text-master',
                'application/vnd.oasis.opendocument.text-template',
                'application/vnd.oasis.opendocument.text-web',
                'application/vnd.openofficeorg.extension',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.presentationml.slide',
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'application/vnd.openxmlformats-officedocument.presentationml.template',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'application/vocaltec-media-file',
                'application/wordperfect',
                'application/x-bittorrent',
                'application/x-bzip',
                'application/x-bzip2',
                'application/x-compressed',
                'application/x-excel',
                'application/x-gzip',
                'application/x-latex',
                'application/x-midi',
                'application/xml',
                'application/x-msexcel',
                'application/x-rar',
                'application/x-rar-compressed',
                'application/x-rtf',
                'application/x-shockwave-flash',
                'application/x-sit',
                'application/x-stuffit',
                'application/x-troff-msvideo',
                'application/x-zip',
                'application/x-zip-compressed',
                'application/zip',
                'audio',
                'image',
                'multipart/x-gzip',
                'multipart/x-zip',
                'text/plain',
                'text/rtf',
                'text/richtext',
                'text/xml',
                'video',
                'text/csv'
            ),
//            'uploadDeny'      => array(
//                'application/x-shockwave-flash'
//            ),
//            'uploadOrder'     => 'allow,deny',
            'uploadOrder'     => 'deny,allow',
            'uploadOverwrite' => true,
//            'uploadMaxSize'   => '128m',
//             'copyOverwrite'  => false,
            'copyJoin'        => true,
//            'mimeDetect'      => 'internal',
            'mimeDetect'      => 'mime_content_type',
//            'tmpPath'         => BASE . 'tmp',
            'tmbCrop'         => false,
//            'imgLib'          => 'gd',  // 'auto' doesn't seem to work on some servers
            'tmbPath'         => BASE . 'tmp' . DIRECTORY_SEPARATOR . 'elfinder',
            'tmbURL'          => URL_FULL . 'tmp/elfinder/',
            'tmbPathMode'     => octdec(DIR_DEFAULT_MODE_STR + 0),
            'tmbBgColor'      => 'transparent',
            'tmbSize'         => FM_THUMB_SIZE,
            'quarantine'      => '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'elfinder' . DIRECTORY_SEPARATOR . '.quarantine',
            'acceptedName'    => '/^[^\.].*$/',
            // 'acceptedName'    => '/^[\W]*$/',
            // 'acceptedName'    => 'validName',
            'utf8fix'         => false,
//            'statOwner'       => true,
//            'attributes'      => array(
//                array(
//                    'pattern' => '/^\/\./', // dot files are hidden, we use 'access' for this feature
//                    'read'    => false,
//                    'write'   => false,
//                    'hidden'  => true,
//                    'locked'  => true
//                )
//            )
            'attributes' => array(
                 array(
                     'pattern' => '/^./', // files in root are NOT locked and hiddend by default for Exponent
//                     'read'    => true,
//                     'write'   => false,
                     'hidden'  => false,
                     'locked'  => false
                 )
               )
        ),
        // Trash volume
        array(
            'id'              => 't1',
            'driver'          => 'Trash',
            'path'            => BASE . 'files/.trash/',
            'URL'             => URL_FULL . 'files/.trash/',
            'tmbPath'         => BASE . 'tmp' . DIRECTORY_SEPARATOR . 'elfinder',
            'tmbURL'          => URL_FULL . 'tmp/elfinder/',
            'winHashFix'      => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            'uploadDeny'      => array('all'),                // Recommend the same settings as the original volume that uses the trash
            'uploadAllow'     => array(
                'application/arj',
                'application/excel',
                'application/gnutar',
                'application/mspowerpoint',
                'application/msword',
                'application/octet-stream',
                'application/onenote',
                'application/pdf',
                'application/plain',
                'application/postscript',
                'application/powerpoint',
                'application/rar',
                'application/rtf',
                'application/vnd.ms-excel',
                'application/vnd.ms-excel.addin.macroEnabled.12',
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'application/vnd.ms-excel.sheet.macroEnabled.12',
                'application/vnd.ms-excel.template.macroEnabled.12',
                'application/vnd.ms-office',
                'application/vnd.ms-officetheme',
                'application/vnd.ms-powerpoint',
                'application/vnd.ms-powerpoint.addin.macroEnabled.12',
                'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
                'application/vnd.ms-powerpoint.slide.macroEnabled.12',
                'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
                'application/vnd.ms-powerpoint.template.macroEnabled.12',
                'application/vnd.ms-word',
                'application/vnd.ms-word.document.macroEnabled.12',
                'application/vnd.ms-word.template.macroEnabled.12',
                'application/vnd.oasis.opendocument.chart',
                'application/vnd.oasis.opendocument.database',
                'application/vnd.oasis.opendocument.formula',
                'application/vnd.oasis.opendocument.graphics',
                'application/vnd.oasis.opendocument.graphics-template',
                'application/vnd.oasis.opendocument.image',
                'application/vnd.oasis.opendocument.presentation',
                'application/vnd.oasis.opendocument.presentation-template',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.spreadsheet-template',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.text-master',
                'application/vnd.oasis.opendocument.text-template',
                'application/vnd.oasis.opendocument.text-web',
                'application/vnd.openofficeorg.extension',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.presentationml.slide',
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                'application/vnd.openxmlformats-officedocument.presentationml.template',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'application/vocaltec-media-file',
                'application/wordperfect',
                'application/x-bittorrent',
                'application/x-bzip',
                'application/x-bzip2',
                'application/x-compressed',
                'application/x-excel',
                'application/x-gzip',
                'application/x-latex',
                'application/x-midi',
                'application/xml',
                'application/x-msexcel',
                'application/x-rar',
                'application/x-rar-compressed',
                'application/x-rtf',
                'application/x-shockwave-flash',
                'application/x-sit',
                'application/x-stuffit',
                'application/x-troff-msvideo',
                'application/x-zip',
                'application/x-zip-compressed',
                'application/zip',
                'audio',
                'image',
                'multipart/x-gzip',
                'multipart/x-zip',
                'text/plain',
                'text/rtf',
                'text/richtext',
                'text/xml',
                'video',
                'text/csv'
            ),
            'uploadOrder'   => array('deny', 'allow'),      // Same as above
            'accessControl' => 'access',
        ),

    ),
);

//header('Access-Control-Allow-Origin: *');
$connector = new elFinderConnector(new elFinderExponent($opts));
$connector->run();
