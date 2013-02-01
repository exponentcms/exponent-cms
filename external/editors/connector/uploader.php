<?php
define("SCRIPT_EXP_RELATIVE","external/editors/connector/");
define("SCRIPT_FILENAME","uploader.php");

require_once("../../../exponent.php");
?>
<!DOCTYPE HTML>
<html>
	<head>
        <?php
            global $user;

            // The returned url of the uploaded file
            $url = '' ;
            // Optional message to show to the user (file renamed, invalid file, not authenticated...)
            $message = '';
            //extensive suitability check before doing anything with the file...
            if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) ) {
                $message = gt("No file uploaded.");
            } else if ($_FILES['upload']["size"] == 0) {
                $message = gt("The file is zero length.");
//            } else if (($_FILES['upload']["type"] != "image/pjpeg") AND ($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/png")) {
//                $message = gt("The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.");
            } else if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
                $message = gt("You may be attempting to hack our server.");
            } else {
                // upload the file, but don't save the record yet...
                $file = expFile::fileUpload('upload',false,false);
                // since most likely this function will only get hit via flash in YUI Uploader
                // and since Flash can't pass cookies, we lose the knowledge of our $user
                // so we're passing the user's ID in as $_POST data. We then instantiate a new $user,
                // and then assign $user->id to $file->poster so we have an audit trail for the upload
                if (is_object($file)) {
                    $file->poster = $user->id;
                    $file->posted = $file->last_accessed = time();
                    $file->save();
                    $url = $file->path_relative;
                } else {
                    $message = gt('File was not uploaded!').' - '.$file;
                }
            }

            $funcNum = $_GET['CKEditorFuncNum'] ;
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(".$funcNum.", '".$url."', '".$message."');</script>";
        ?>
    </head>
    <body>
	</body>
</html>