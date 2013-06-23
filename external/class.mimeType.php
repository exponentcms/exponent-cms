<?php
/* Modified: 7/15/2009      
 * Licence:  GNU - http://www.gnu.org/copyleft/gpl.txt
 * Copyleft: Dustin Hoffman, 2009
 * Contact:  dustin.hoffman@breefield.com
 */

/* NOTE
 * The basic content / structure of this class was retrieved from php.net
 * It can be found at:
 * http://us.php.net/manual/en/function.mime-content-type.php#87856
 */
 
/* mimeType
 * For figuring out what mimetype your files are!
 */
class mimeType {

    /* getMimeType
     * Get the mimtype from a filename / extension
     * @param string $filename The path to a file
     */
    public function getMimeType($filename) {
        /* Store an array of commom mimetypes */
        $types = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        
        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'ogg'  => 'audio/ogg',
        'f4v'  => 'video/mp4',
        'mp4'  => 'video/mp4',
        'ogv'  => 'video/ogg',
        '3gp'  => 'video/3gpp',
        'webm' => 'video/webm',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet');
        
        /* Get the file extension,
         * FYI: this is *really* hax.
         */
        $extension = strtolower(array_pop(explode('.',$filename)));
        if(function_exists('finfo_open')) {
            /* If we don't have to guess, do it the right way */
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } elseif(array_key_exists($extension, $types)) {
            /* If we can *guess* the mimetype based on the filename, do that */
            return $types[$extension];
        } else {
            /* Otherwise, let the browser guess */
            return 'application/octet-stream';
        }
    }
}
?>