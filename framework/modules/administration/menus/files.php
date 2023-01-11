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

if (!defined('EXPONENT'))
    exit('');

global $user;

if ($user->globalPerm('hide_files_menu'))
    return false;

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items = array(
    array(
        'text'      => gt("File Manager"),
        'icon'      => 'fa-archive',
        'icon5'      => 'fas fa-archive',
        'iconbs'      => 'bi-archive',
        'classname' => 'filemanager',
        'url'       => '#',
        'id'        => 'filemanager-toolbar',
    ),
);
if (!$user->globalPerm('prevent_uploads') && SITE_FILE_MANAGER != 'elfinder') {
    $items[] = array(
        'text'      => gt("Upload Files"),
        'icon'      => 'fa-upload',
        'icon5'      => 'fas fa-upload',
        'iconbs'      => 'bi-upload',
        'classname' => 'fileuploader',
        'url'       => '#',
        'id'        => 'fileuploader-toolbar',
    );
}
if ($user->isSuperAdmin()) {
    $items[] = array(
        'text'      => gt('Import Files'),
        'icon'      => 'fa-sign-in',
        'icon5'      => 'fas fa-sign-in-alt',
        'iconbs'      => 'bi-upload',
        'classname' => 'import',
        'url'       => makeLink(
            array(
                'controller' => 'file',
                'action' => 'import_files'
            )
        ),
    );
    $items[] = array(
        'text'      => gt('Export Files'),
        'icon'      => 'fa-sign-out',
        'icon5'      => 'fas fa-sign-out-alt',
        'iconbs'      => 'bi-download',
        'classname' => 'export',
        'url'       => makeLink(
            array(
                'controller' => 'file',
                'action' => 'export_files'
            )
        ),
    );
}

return array(
    'text'      => gt('Files'),
    'icon' => 'fa-camera-retro',
    'icon5' => 'fas fa-camera-retro',
    'iconbs' => 'bi-camera',
    'classname' => 'files',
    'submenu'   => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

?>
