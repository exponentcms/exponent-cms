<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

if ($user->globalPerm('hide_files_menu')) return array();

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
);
if (!$user->globalPerm('prevent_uploads') && SITE_FILE_MANAGER != 'elfinder') {
    $items[] = array(
        'text'      => gt("Upload Files"),
        'url'       => '#',
        'classname' => 'fileuploader',
        'id'        => 'fileuploader-toolbar',
    );
}
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

?>
