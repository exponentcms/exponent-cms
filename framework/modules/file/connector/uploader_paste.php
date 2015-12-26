<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * Implements the paste upload feature within the CKEditor drag/drop/paste
 */

define("SCRIPT_EXP_RELATIVE", "framework/modules/file/connector/");
define("SCRIPT_FILENAME", "uploader.php");

require_once("../../../../exponent.php");
global $user;

// The returned url of the uploaded file
$url = '';
// Optional message to show to the user (file renamed, invalid file, not authenticated...)
$message = '';

if (defined('QUICK_UPLOAD_FOLDER') && QUICK_UPLOAD_FOLDER != '' && QUICK_UPLOAD_FOLDER != 0) {
    if (SITE_FILE_MANAGER == 'picker') {
        $quikFolder = QUICK_UPLOAD_FOLDER;
        $destDir = null;
    } elseif (SITE_FILE_MANAGER == 'elfinder') {
        $quikFolder = null;
        $destDir = UPLOAD_DIRECTORY_RELATIVE . QUICK_UPLOAD_FOLDER . '/';
        // create folder if non-existant
        expFile::makeDirectory($destDir);
    }
} else {
    $quikFolder = null;
    $destDir = null;
}

//extensive suitability check before doing anything with the file...
if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name']))) {
    $message = gt("No file uploaded.");
} else {
    if ($_FILES['upload']["size"] == 0) {
        $message = gt("The file is zero length.");
//            } else if (($_FILES['upload']["type"] != "image/pjpeg") AND ($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/png")) {
//                $message = gt("The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.");
    } else {
        if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
            $message = gt("You may be attempting to hack our server.");
        } else {
            // upload the file, but don't save the record yet...
            $file = expFile::fileUpload('upload', false, false, null, $destDir, intval(QUICK_UPLOAD_WIDTH));
            // since most likely this function will only get hit via flash in YUI Uploader
            // and since Flash can't pass cookies, we lose the knowledge of our $user
            // so we're passing the user's ID in as $_POST data. We then instantiate a new $user,
            // and then assign $user->id to $file->poster so we have an audit trail for the upload
            if (is_object($file)) {
                $file->poster = $user->id;
                $file->posted = $file->last_accessed = time();
                $file->save();
                if (!empty($quikFolder)) {
                    $expcat = new expCat($quikFolder);
                    $params['expCat'][0] = $expcat->id;
                    $file->update($params);
                }
                $url = $file->path_relative;
            } else {
                $message = gt('File was not uploaded!') . ' - ' . $file;
            }
        }
    }
}

$output = array(
    "uploaded" => 1,
    "fileName" => $file->filename,
    "url" => $url
);
if (!empty($message)) {
    $output = array(
        "uploaded" => 0,
        "fileName" => $file->filename,
        "url" => $url,
        "message" => $message
    );
}
$test = json_encode($output);
echo $test;
?>
