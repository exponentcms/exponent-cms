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

$script = "
    var reportbugwindow = function (){
        win = window.open('http://exponentcms.lighthouseapp.com/projects/61783-exponent-cms/tickets/new');
        if (!win) {
            //Catch the popup blocker
            alert(\"Your popup blocker has prevented the file manager from opening\");
        }
    }

    YAHOO.util.Event.on('reportabug','click',reportbugwindow);
";
exponent_javascript_toFoot('zreportabug', '', null, $script);

if ($user->isAdmin()) {
	$expAdminMenu = array(
		'text' => '<img src="' . $this->asset_path . 'images/admintoolbar/expbar.png">',
		'classname' => 'site',
		'submenu' => array(
			'id' => 'admin',
			'itemdata' => array(
				array(
					'classname' => 'info',
					'text'=>'About ExponentCMS',
					"submenu"=>array(
						'id'=>'ver',
						'itemdata'=>array(
							array(
								'classname' => 'moreinfo',
								'text'=>"Exponent Version : ".$my_version."<br />Release level : ".$my_type."<br />Release date : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE)."<br />PHP Version : ".phpversion(),"disabled"=>true
							),
							array(
								'text' => "Report a bug",
								'url'=>'#',
								'id'=>'reportabug',
								'classname' => 'reportbug',
							)
						)
					)
				),
			)
		)
	);
} else {
	$expAdminMenu = array(
		'text' => '<img src="' . $this->asset_path . 'images/admintoolbar/expbar.png">',
		'classname' => 'site',
		'submenu' => array(
			'id' => 'admin',
			'itemdata' => array(
				array(
					'classname' => 'info',
					'text'=>'About ExponentCMS',
					"submenu"=>array(
						'id'=>'ver',
						'itemdata'=>array(
							array(
								'classname' => 'moreinfo',
								'text'=>"Exponent Version : ".$my_version."<br />Release level : ".$my_type."<br />Release date : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE)."<br />PHP Version : ".phpversion(),"disabled"=>true
							)
						)
					)
				),
			)
		)
	);
}


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
                'classname' => 'search',
                'url' => makeLink(array(
                    'module' => 'search',
                    'action' => 'spider'
                ))
            ),
/*            array(
                'text' => expLang::gettext("Configure Editor Toolbar"),
                'url' => makeLink(array(
                    'module' => 'expHTMLEditor',
                    'action' => 'manage'
                ))
            ) */
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
                'classname' => 'euser',
            ),
            array(
                'text' => $i18n['group_accounts'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_groups'
                )),
                'classname' => 'egroup',
            ),
            array(
                'text' => $i18n['profile_definitions'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_extensions'
                )),
            ),
            array(
                'text' => $i18n['user_sessions'],
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_sessions'
                )),
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
                    'text' => (DEVELOPMENT)?expLang::gettext('Turn Error Reporting off'):expLang::gettext('Turn Error Reporting on'),
                    'classname' => (DEVELOPMENT)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'module' => 'administrationmodule',
                        'action' => 'toggle_dev'
                    ))
                ),
                array(
                    'text' => (MINIFY)?expLang::gettext('Turn Minification off'):expLang::gettext('Turn Minification on'),
                    'classname' => (MINIFY)?'develop_on_green':'develop_off',
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
                                'classname' => 'remove',
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
                                'classname'=>'fileuploader',
                                'url' => makeLink(array(
                                    'module' => 'administrationmodule',
                                    'action' => 'upload_extension'
                                ))
                            ),
                            array(
                                'text' => expLang::gettext('Manage Modules'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'controller' => 'expModule',
                                    'action' => 'manage'
                                ))
                            ),
                            array(
                                'text' => expLang::gettext('Manage Themes'),
                                'classname' => 'manage',
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
	                'classname' => 'remove',
                    'url' => makeLink(array(
                        'module' => 'administrationmodule',
                        'action' => 'clear_smarty_cache'
                    ))
                ),
                array(
	                'text' => (MAINTENANCE_MODE)?expLang::gettext('Turn Maintenance Mode off'):expLang::gettext('Turn Maintenance Mode on'),
	                'classname' => (MAINTENANCE_MODE)?'develop_on_red':'develop_off',
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
