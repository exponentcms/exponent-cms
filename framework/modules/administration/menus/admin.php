<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
global $user, $db;

$i18n = exponent_lang_loadFile('modules/administrationmodule/tasks/coretasks.php');

$my_version = EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION;
$my_type = EXPONENT_VERSION_TYPE.EXPONENT_VERSION_ITERATION;

$expAdminMenu = array(
    'text' => '<img src="' . $this->asset_path . 'images/admintoolbar/expbar.png">',
    'classname' => 'site',
    'submenu' => array(
        'id' => 'admin',
        'itemdata' => array(
    		array(
    			'classname'=>'info',
    			'text'=>'About ExponentCMS',
    			"submenu"=>array(
    				'id'=>'ver',
    				'itemdata'=>array(
    					array('classname'=>'moreinfo','text'=>"Exponent Version : ".$my_version."<br />Release level : ".$my_type."<br />Release date : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE),"disabled"=>true)
    				)
    			)
    		),
        )
    )
);


if ($user->isAdmin()) {
$expAdminMenu['submenu']['itemdata'][] = array(
    'text' => $i18n['configuration'],
    'classname' => 'config',
    'submenu' => array(
        'id' => 'configure',
        'itemdata' => array(
            array(
                'text' => "Configure Website",
                'url' => makeLink(array(
                    'module' => 'administration',
                    'action' => 'configure_site'
                ))
            ),
            array(
                'text' => 'Regenerate Search Index',
                'url' => makeLink(array(
                    'module' => 'search',
                    'action' => 'spider'
                ))
            ),
            array(
                'text' => expLang::gettext("Configure Editor Toolbar"),
                'url' => makeLink(array(
                    'module' => 'expHTMLEditor',
                    'action' => 'manage'
                ))
            )
        )
    )
);

}

$groups = $db->selectObjects('groupmembership','member_id='.$user->id.' AND is_admin=1');

if ($user->isAdmin() || !empty($groups)) {
$expAdminMenu['submenu']['itemdata'][] = array(
    'text' => $i18n['user_management'],
    'classname' => 'users',
    'submenu' => array(
        'id' => 'usermanagement',
        'itemdata' => array(
            array(
                'text' => $i18n['user_accounts'],
                'url' => makeLink(array(
                    'controller' => 'users',
                    'action' => 'manage'
                )),
                'icon' => ICON_RELATIVE . "userperms.png"
            ),
            array(
                'text' => $i18n['group_accounts'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_groups'
                )),
                'icon' => ICON_RELATIVE . "groupperms.png"
            ),
            array(
                'text' => $i18n['profile_definitions'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_extensions'
                )),
                'icon' => ICON_RELATIVE . "groupperms.png"
            ),
            array(
                'text' => $i18n['user_sessions'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_sessions'
                )),
                'icon' => ICON_RELATIVE . "groupperms.png"
            )
        )
    )
);

}


if ($user->isSuperAdmin()) {
$expAdminMenu['submenu']['itemdata'][] = array(
        'text' => 'Developer Tools',
        'classname' => 'development',
        'submenu' => array(
            'id' => 'development',
            'itemdata' => array(
                array(
                    'text' => (DEVELOPMENT)?expLang::gettext('Turn error reporting off'):expLang::gettext('Turn error reporting on'),
                    'url' => makeLink(array(
                        'module' => 'administrationmodule',
                        'action' => 'toggle_dev'
                    ))
                ),
                array(
                    'text' => (MINIFY)?expLang::gettext('Turn minification off'):expLang::gettext('Turn minification on'),
                    'url' => makeLink(array(
                        'module' => 'administration',
                        'action' => 'toggle_minify'
                    ))
                ),
                array(
                    'text' => $i18n['database'],
                    'submenu' => array(
                        'id' => 'database',
                        'itemdata' => array(
                            array(
                                'text' => $i18n['install_tables'],
                                'url' => makeLink(array(
                                    'module' => 'administrationmodule',
                                    'action' => 'installtables'
                                ))
                            ),
                            // array(
                            // 'text'=>$i18n['import_data'],
                            // 'url'=>makeLink(array('module'=>'importer','action'=>'list_importers')),
                            // ),
                            // array(
                            // 'text'=>$i18n['export_data'],
                            // 'url'=>makeLink(array('module'=>'exporter','action'=>'list_exporters')),
                            // ),
                            // array(
                            // 'text'=>$i18n['archived_modules'],
                            // 'url'=>makeLink(array('module'=>'administrationmodule','action'=>'orphanedcontent')),
                            // ),
                            array(
                                'text' => $i18n['optimize_database'],
                                'url' => makeLink(array(
                                    'module' => 'administrationmodule',
                                    'action' => 'optimizedatabase'
                                ))
                            ),
                            array(
                                'text' => 'Remove Unused Tables',
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'manage_unused_tables'
                                ))
                            )
                        )
                    )
                ),
                
                array(
                    'text' => 'Migration',
                    'submenu' => array(
                        'id' => 'migration',
                        'itemdata' => array(
                            array(
                                'text' => '1-Configure Migration Settings',
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'configure'
                                ))
                            ),
                            array(
                                'text' => '2-Migrate Users/Groups',
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_users'
                                ))
                            ),
                            array(
                                'text' => '3-Migrate Pages',
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_pages'
                                ))
                            ),
                            array(
                                'text' => '4-Migrate Files',
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_files'
                                ))
                            ),
                            array(
                                'text' => '5-Migrate Content',
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_content'
                                ))
                            )
                        )
                    )
                ),
                array(
                    'text' => expLang::gettext('Extensions'),
                    'submenu' => array(
                        'id' => 'extensions',
                        'itemdata' => array(
                            array(
                                'text' => $i18n['upload_extension'],
                                'url' => makeLink(array(
                                    'module' => 'administrationmodule',
                                    'action' => 'upload_extension'
                                ))
                            ),
                            array(
                                'text' => expLang::gettext('Manage Modules'),
                                'url' => makeLink(array(
                                    'controller' => 'expModule',
                                    'action' => 'manage'
                                ))
                            ),
                            array(
                                'text' => expLang::gettext('Manage Themes'),
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'manage_themes'
                                ))
                            ),
                        )
                    )
                ),
                array(
                    'text' => $i18n['clear_smarty'],
                    'url' => makeLink(array(
                        'module' => 'administrationmodule',
                        'action' => 'clear_smarty_cache'
                    ))
                ),
                array(
                    'text' => $i18n['toggle_maint'],
                    'url' => makeLink(array(
                        'module' => 'administrationmodule',
                        'action' => 'toggle_maintenance'
                    ))
                )
            )
        )
    );
}

return $expAdminMenu;

?>
