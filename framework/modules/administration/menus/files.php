<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

global $user;

//$script = "
//// YUI(EXPONENT.YUI3_CONFIG).use('node','event-custom', function(Y) {
////
//// });
//";
//
//expJavascript::pushToFoot(array(
//    "unique"=>'admin2',
//    "yui3mods"=>1,
//    "content"=>$script,
// ));

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items = array(
    array(
        'text'      => gt("File Manager"),
        'url'       => '#',
        'classname' => 'filemanager',
        'id'        => 'filemanager-toolbar',
    ),
    array(
        'text'      => gt("Upload Files"),
        'url'       => '#',
        'classname' => 'fileuploader',
        'id'        => 'fileuploader-toolbar',
    ),
);

if ($user->isSuperAdmin()) {
    $items[] = array(
        'text'      => gt('Import Files'),
        'url'       => makeLink(array('controller' => 'file', 'action' => 'import_files')),
        'classname' => 'import',
    );
    $items[] = array(
        'text'      => gt('Export Files'),
        'url'       => makeLink(array('controller' => 'file', 'action' => 'export_files')),
        'classname' => 'export',
    );
}

return array(
    'text'      => gt('Files'),
    'classname' => 'files',
    'submenu'   => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

//if ($user->isSuperAdmin()) {
//    return array(
//        'text'=>gt('Files'),
//        'classname'=>'files',
//        'submenu'=>array(
//            'id'=>'file-functions',
//            'itemdata'=>array(
//                array(
//                    'text'=>gt("File Manager"),
//                    'url'=>'#',
//                    'classname'=>'filemanager',
//                    'id'=>'filemanager-toolbar',
//                ),
//                array(
//                    'text'=>gt("Upload Files"),
//                    'url'=>'#',
//                    'classname'=>'fileuploader',
//                    'id'=>'fileuploader-toolbar',
//                ),
//                array(
//                    'text'=>gt('Import Files'),
//                    'url'=>makeLink(array('controller'=>'file','action'=>'import_files')),
//                    'classname'=>'import',
//                ),
//                array(
//                    'text'=>gt('Export Files'),
//                    'url'=>makeLink(array('controller'=>'file','action'=>'export_files')),
//                    'classname'=>'export',
//                ),
//            ),
//        )
//    );
//} else {
//    return array(
//        'text'=>gt('Files'),
//        'classname'=>'files',
//        'submenu'=>array(
//            'id'=>'file-functions',
//            'itemdata'=>array(
//                array(
//                    'text'=>gt("File Manager"),
//                    'url'=>'#',
//                    'classname'=>'filemanager',
//                    'id'=>'filemanager-toolbar',
//                ),
//                array(
//                    'text'=>gt("Upload Files"),
//                    'url'=>'#',
//                    'classname'=>'fileuploader',
//                    'id'=>'fileuploader-toolbar',
//                ),
//            ),
//        )
//    );
//}

?>
