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

$my_version = EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION;
if (EXPONENT_VERSION_TYPE != '') {
	$my_type = gt("Release level")." : ".EXPONENT_VERSION_TYPE.EXPONENT_VERSION_ITERATION."<br />";
} else {
	$my_type = '';
}

$script = "
// YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
// 
// });

";
expJavascript::pushToFoot(array(
    "unique"=>'admin1',
    "yui3mods"=>null,
    "content"=>$script,
 ));

if ($user->isAdmin()) {
	$expAdminMenu = array(
		'text' => '<img src="' . $this->asset_path . 'images/admintoolbar/expbar.png">',
		'classname' => 'site',
		'submenu' => array(
			'id' => 'admin',
			'itemdata' => array(
				array(
					'classname' => 'info',
					'text'=>gt('About ExponentCMS'),
					"submenu"=>array(
						'id'=>'ver',
						'itemdata'=>array(
							array(
								'classname' => 'moreinfo',
								'text'=>gt("Exponent Version")." : ".$my_version."<br />".$my_type.gt("Release date")." : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE)."<br />".gt("PHP Version")." : ".phpversion(),"disabled"=>true
							),
							array(
								'text' => gt("Report a bug"),
								'url'=>'#',
								'id'=>'reportabug-toolbar',
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
					'text'=>gt('About ExponentCMS'),
					"submenu"=>array(
						'id'=>'ver',
						'itemdata'=>array(
							array(
								'classname' => 'moreinfo',
								'text'=>gt("Exponent Version")." : ".$my_version."<br />".gt("Release level")." : ".$my_type."<br />".gt("Release date")." : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE),"disabled"=>true
							)
						)
					)
				),
			)
		)
	);
}


if ($user->isAdmin()) {
	if (SMTP_USE_PHP_MAIL){
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt("Configuration"),
			'classname' => 'config',
			'submenu' => array(
				'id' => 'configure',
				'itemdata' => array(
					array(
						'text' => gt("Configure Website"),
						'url' => makeLink(array(
							'module' => 'administration',
							'action' => 'configure_site'
						))
					),
					array(
						'text' => gt('Regenerate Search Index'),
						'classname' => 'search',
						'url' => makeLink(array(
							'module' => 'search',
							'action' => 'spider'
						))
					),
				)
			)
		);
	} else {
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt('Configuration'),
			'classname' => 'config',
			'submenu' => array(
				'id' => 'configure',
				'itemdata' => array(
					array(
						'text' => gt("Configure Website"),
						'url' => makeLink(array(
							'module' => 'administration',
							'action' => 'configure_site'
						))
					),
					array(
						'text' => gt('Test SMTP Mail Server Settings'),
						'url' => makeLink(array(
							'module' => 'administration',
							'action' => 'test_smtp'
						))
					),
					array(
						'text' => gt('Regenerate Search Index'),
						'classname' => 'search',
						'url' => makeLink(array(
							'module' => 'search',
							'action' => 'spider'
						))
					),
				)
			)
		);
	}
}

$groups = $db->selectObjects('groupmembership','member_id='.$user->id.' AND is_admin=1');

if ($user->isAdmin() || !empty($groups)) {
$expAdminMenu['submenu']['itemdata'][] = array(
    'text' => gt('User Management'),
    'classname' => 'users',
    'submenu' => array(
        'id' => 'usermanagement',
        'itemdata' => array(
            array(
                'text' => gt('User Accounts'),
                'url' => makeLink(array(
                    'controller' => 'users',
                    'action' => 'manage'
                )),
                'classname' => 'euser',
            ),
            array(
                'text' => gt('Group Accounts'),
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_groups'
                )),
                'classname' => 'egroup',
            ),
            array(
                'text' => gt('Profile Definitions'),
                'url' => makeLink(array(
                    'module' => 'users',
                    'action' => 'manage_extensions'
                )),
            ),
            array(
                'text' => gt('User Sessions'),
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
        'text' => gt('Developer Tools'),
        'classname' => 'development',
        'submenu' => array(
            'id' => 'development',
            'itemdata' => array(
                array(
                    'text' => (DEVELOPMENT)?gt('Turn Error Reporting off'):gt('Turn Error Reporting on'),
                    'classname' => (DEVELOPMENT)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'module' => 'administration',
                        'action' => 'toggle_dev'
                    ))
                ),
                array(
                    'text' => gt('Database'),
                    'submenu' => array(
                        'id' => 'database',
                        'itemdata' => array(
                            array(
                                'text' => gt('Install Tables'),
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'install_tables'
                                ))
                            ),
                             array(
                             'text'=>gt('Import Data'),
                             'url'=>makeLink(array('module'=>'importer','action'=>'list_importers')),
                             ),
                             array(
                             'text'=>gt('Export Data'),
                             'url'=>makeLink(array('module'=>'exporter','action'=>'list_exporters')),
                             ),
//                             array(
//                             'text'=>gt('Archived Modules'),
//                             'url'=>makeLink(array('module'=>'administrationmodule','action'=>'orphanedcontent')),
//                             ),
                            array(
                                'text' => gt('Optimize Database'),
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'optimize_database'
                                ))
                            ),
                            array(
                                'text' => gt('Repair Database'),
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'fix_database'
                                ))
                            ),
                            array(
                                'text' => gt('Reset Sessions Table'),
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'fix_sessions'
                                ))
                            ),
                            array(
                                'text' => gt('Remove Unused Tables'),
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
                    'text' => gt('Migration'),
                    'submenu' => array(
                        'id' => 'migration',
                        'itemdata' => array(
                            array(
                                'text' => '1 - '.gt('Configure Migration Settings'),
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'configure'
                                ))
                            ),
                            array(
                                'text' => '2 - '.gt('Migrate Users/Groups'),
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_users'
                                ))
                            ),
                            array(
                                'text' => '3 - '.gt('Migrate Pages'),
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_pages'
                                ))
                            ),
                            array(
                                'text' => '4 - '.gt('Migrate Files'),
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_files'
                                ))
                            ),
                            array(
                                'text' => '5 - '.gt('Migrate Content'),
                                'url' => makeLink(array(
                                    'module' => 'migration',
                                    'action' => 'manage_content'
                                ))
                            )
                        )
                    )
                ),
                array(
                    'text' => gt('Extensions'),
                    'submenu' => array(
                        'id' => 'extensions',
                        'itemdata' => array(
                            array(
                                'text' => gt('Upload Extension'),
                                'classname'=>'fileuploader',
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'upload_extension'
                                ))
                            ),
                            array(
                                'text' => gt('Manage Modules'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'controller' => 'expModule',
                                    'action' => 'manage'
                                ))
                            ),
                            array(
                                'text' => gt('Manage Themes'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'manage_themes'
                                )),
                            ),
							array(
								'text' => (MOBILE)?gt('Turn Mobile View off'):gt('Turn Mobile View on'),
								'classname' => (MOBILE)?'develop_on_green':'develop_off',
								'url' => makeLink(array(
									'module' => 'administration',
									'action' => 'toggle_mobile'
								)),
                            ),
                            array(
                                'text' => gt('Manage Translation'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'module' => 'administration',
                                    'action' => 'manage_lang'
                                )),
                            ),
                        )
                    )
                ),
                array(
                    'text' => gt('System Cache'),
                    'submenu' => array(
                        'id' => 'cache',
                        'itemdata' => array(
							array(
								'text' => (MINIFY)?gt('Turn Minification off'):gt('Turn Minification on'),
								'classname' => (MINIFY)?'develop_on_green':'develop_off',
								'url' => makeLink(array(
									'module' => 'administration',
									'action' => 'toggle_minify'
								))
							),
							array(
								'text' => gt('Clear Smarty Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
									'module' => 'administration',
									'action' => 'clear_smarty_cache'
								))
							),
							array(
								'text' => gt('Clear CSS/Minify Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'module' => 'administration',
									'action' => 'clear_css_cache'
								))
							),
							array(
								'text' => gt('Clear Image Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'module' => 'administration',
									'action' => 'clear_image_cache'
								))
							),
							array(
								'text' => gt('Clear RSS/Podcast Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'module' => 'administration',
									'action' => 'clear_rss_cache'
								))
							),
							array(
								'text' => gt('Clear All Caches'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'module' => 'administration',
									'action' => 'clear_all_caches'
								))
							),
	                    )
					)
				),
                array(
	                'text' => (MAINTENANCE_MODE)?gt('Turn Maintenance Mode off'):gt('Turn Maintenance Mode on'),
	                'classname' => (MAINTENANCE_MODE)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'module' => 'administration',
                        'action' => 'toggle_maintenance'
                    ))
                )
            )
        )
    );
}

return $expAdminMenu;

?>
