<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

global $user, $db;

$my_version = gt("Exponent Version")." : ".EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR.".".EXPONENT_VERSION_REVISION."<br />";
if (EXPONENT_VERSION_TYPE != '') {
	$my_type = gt("Release level")." : ".EXPONENT_VERSION_TYPE.EXPONENT_VERSION_ITERATION."<br />";
} else {
	$my_type = '';
}
$my_releasedate = gt("Release date")." : ".date("F-d-Y",EXPONENT_VERSION_BUILDDATE);

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
								'text'=>$my_version.$my_type.$my_releasedate."<br />".gt("PHP Version")." : ".phpversion(),"disabled"=>true
							),
                            array(
                                'text' => gt("Exponent Documentation"),
                                'url'=>'#',
                                'id'=>'docs-toolbar',
                                'classname' => 'docs',
                            ),
                            array(
                                'text' => gt("Discuss Exponent"),
                                'url'=>'#',
                                'id'=>'forums-toolbar',
                                'classname' => 'forums',
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
								'text'=>$my_version.$my_type.$my_releasedate,"disabled"=>true
							),
                            array(
                                'text' => gt("Exponent Documentation"),
                                'url'=>'#',
                                'id'=>'docs-toolbar',
                                'classname' => 'docs',
                            ),
                            array(
                                'text' => gt("Discuss Exponent"),
                                'url'=>'#',
                                'id'=>'forums-toolbar',
                                'classname' => 'forums',
                            )
						)
					)
				),
			)
		)
	);
}

if ($user->isAdmin()) {
	if (SMTP_USE_PHP_MAIL) {
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt("Site Configuration"),
			'classname' => 'config',
			'submenu' => array(
				'id' => 'configure',
				'itemdata' => array(
					array(
						'text' => gt("Configure Website"),
						'url' => makeLink(array(
							'controller' => 'administration',
							'action' => 'configure_site'
						))
					),
				)
			)
		);
	} else {
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt('Site Configuration'),
			'classname' => 'config',
			'submenu' => array(
				'id' => 'configure',
				'itemdata' => array(
					array(
						'text' => gt("Configure Website"),
						'url' => makeLink(array(
							'controller' => 'administration',
							'action' => 'configure_site'
						))
					),
					array(
						'text' => gt('Test SMTP Mail Server Settings'),
						'url' => makeLink(array(
							'controller' => 'administration',
							'action' => 'test_smtp'
						))
					),
				)
			)
		);
	}
}

if ($user->isAdmin()) {
	if (SMTP_USE_PHP_MAIL) {
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt("Site Management"),
			'classname' => 'manage',
			'submenu' => array(
				'id' => 'manage',
				'itemdata' => array(
                    array(
                        'text' => gt('Manage Site Comments'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expComment',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('Manage Site Tags'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expTag',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('Manage Site Categories'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expCat',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('View Top Searches'),
                        'classname' => 'search',
                        'url' => makeLink(array(
                            'controller' => 'search',
                            'action' => 'topSearchReport'
                        ))
                    ),
                    array(
                        'text' => gt('View Search Queries'),
                        'classname' => 'search',
                        'url' => makeLink(array(
                            'controller' => 'search',
                            'action' => 'searchQueryReport'
                        ))
                    ),
					array(
						'text' => gt('Regenerate Search Index'),
						'classname' => 'search',
						'url' => makeLink(array(
							'controller' => 'search',
							'action' => 'spider'
						))
					),
				)
			)
		);
	} else {
		$expAdminMenu['submenu']['itemdata'][] = array(
			'text' => gt('Site Management'),
			'classname' => 'manage',
			'submenu' => array(
				'id' => 'manage',
				'itemdata' => array(
                    array(
                        'text' => gt('Manage Site Comments'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expComment',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('Manage Site Tags'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expTag',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('Manage Site Categories'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expCat',
                            'action' => 'manage'
                        ))
                    ),
					array(
                        'text' => gt('Manage Definable Fields'),
                        'classname' => 'manage',
                        'url' => makeLink(array(
                            'controller' => 'expDefinableField',
                            'action' => 'manage'
                        ))
                    ),
                    array(
                        'text' => gt('View Top Searches'),
                        'classname' => 'search',
                        'url' => makeLink(array(
                            'controller' => 'search',
                            'action' => 'topSearchReport'
                        ))
                    ),
                    array(
                        'text' => gt('View Search Queries'),
                        'classname' => 'search',
                        'url' => makeLink(array(
                            'controller' => 'search',
                            'action' => 'searchQueryReport'
                        ))
                    ),
					array(
						'text' => gt('Regenerate Search Index'),
						'classname' => 'search',
						'url' => makeLink(array(
							'controller' => 'search',
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
                        'controller' => 'users',
                        'action' => 'manage_groups'
                    )),
                    'classname' => 'egroup',
                ),
                array(
                    'text' => gt('Profile Definitions'),
                    'url' => makeLink(array(
                        'controller' => 'users',
                        'action' => 'manage_extensions'
                    )),
                ),
                array(
                    'text' => gt('User Sessions'),
                    'url' => makeLink(array(
                        'controller' => 'users',
                        'action' => 'manage_sessions'
                    )),
                )
            )
        )
    );
}

if ($user->isSuperAdmin()) {
    $tmp= count($expAdminMenu['submenu']['itemdata']);
    $expAdminMenu['submenu']['itemdata'][count($expAdminMenu['submenu']['itemdata'])-1]['submenu']['itemdata'][] = array(
        'text' => gt('Mass Mailer'),
        'url' => makeLink(array(
            'controller' => 'administration',
            'action' => 'mass_mail'
        )),
        'classname' => 'email',
    );
	$expAdminMenu['submenu']['itemdata'][] = array(
        'text' => gt('Super-Admin Tools'),
        'classname' => 'development',
        'submenu' => array(
            'id' => 'development',
            'itemdata' => array(
                array(
                    'text' => (DEVELOPMENT)?gt('Turn Error Reporting off'):gt('Turn Error Reporting on'),
                    'classname' => (DEVELOPMENT)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'controller' => 'administration',
                        'action' => 'toggle_dev'
                    ))
                ),
                array(
                    'text' => (LOGGER)?gt('Turn YUI Log Display off'):gt('Turn YUI Log Display on'),
                    'classname' => (LOGGER)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'controller' => 'administration',
                        'action' => 'toggle_log'
                    ))
                ),
                array(
                    'text' => gt('Database'),
                    'submenu' => array(
                        'id' => 'database',
                        'itemdata' => array(
                            array(
                                'text'=>gt('Import Data'),
                                'url'=>makeLink(array('controller'=>'importer','action'=>'list_importers')),
                            ),
                            array(
                                'text'=>gt('Export Data'),
                                'url'=>makeLink(array('controller'=>'exporter','action'=>'list_exporters')),
                            ),
                            array(
                                'text' => gt('Update Tables'),
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'install_tables'
                                ))
                            ),
                            array(
                                'text' => gt("Manage Database"),
                                'url'=>'#',
                                'id'=>'manage-db',
                                'classname' => 'manage',
                            ),
                            array(
                                'text' => gt('Optimize Database'),
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'fix_optimize_database'
                                ))
                            ),
                            array(
                                'text' => gt('Repair Database'),
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'fix_database'
                                ))
                            ),
                            array(
                                'text' => gt('Fix Table Names'),
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'fix_tables'
                                ))
                            ),
                            array(
                                'text' => gt('Reset Sessions Table'),
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'fix_sessions'
                                ))
                            ),
                            array(
                                'text' => gt('Remove Unneeded Table Columns'),
                                'classname' => 'remove',
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'delete_unused_columns'
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
                    'text' => gt('Migrate 0.9x Site'),
                    'url' => makeLink(array(
                        'controller' => 'migration',
                        'action' => 'configure'
                    ))
                ),
                array(
                    'text' => gt('Extensions'),
                    'submenu' => array(
                        'id' => 'extensions',
                        'itemdata' => array(
                            array(
                                'text' => gt('Install Extension'),
                                'classname'=>'fileuploader',
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'install_extension'
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
                                'text' => gt('Manage Translations'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'manage_lang'
                                )),
                            ),
                            array(
                                'text' => gt('Manage Themes'),
                                'classname' => 'manage',
                                'url' => makeLink(array(
                                    'controller' => 'administration',
                                    'action' => 'manage_themes'
                                )),
                            ),
							array(
								'text' => (MOBILE)?gt('Turn Mobile View off'):gt('Turn Mobile View on'),
								'classname' => (MOBILE)?'develop_on_green':'develop_off',
								'url' => makeLink(array(
									'controller' => 'administration',
									'action' => 'togglemobile'
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
									'controller' => 'administration',
									'action' => 'toggle_minify'
								))
							),
							array(
								'text' => gt('Clear Smarty Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
									'controller' => 'administration',
									'action' => 'clear_smarty_cache'
								))
							),
							array(
								'text' => gt('Clear CSS/Minify Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'controller' => 'administration',
									'action' => 'clear_css_cache'
								))
							),
							array(
								'text' => gt('Clear Image Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'controller' => 'administration',
									'action' => 'clear_image_cache'
								))
							),
							array(
								'text' => gt('Clear RSS/Podcast Cache'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'controller' => 'administration',
									'action' => 'clear_rss_cache'
								))
							),
							array(
								'text' => gt('Clear All Caches'),
								'classname' => 'remove',
								'url' => makeLink(array(
								    'controller' => 'administration',
									'action' => 'clear_all_caches'
								))
							),
	                    )
					)
				),
                array(
                    'text' => gt('View Recycle Bin'),
                    'classname' => 'manage',
                    'url' => makeLink(array(
                        'controller' => 'recyclebin',
                        'action' => 'showall'
                    ))
                ),
                array(
	                'text' => (MAINTENANCE_MODE)?gt('Turn Maintenance Mode off'):gt('Turn Maintenance Mode on'),
	                'classname' => (MAINTENANCE_MODE)?'develop_on_red':'develop_off',
                    'url' => makeLink(array(
                        'controller' => 'administration',
                        'action' => 'toggle_maintenance'
                    ))
                ),
                array(
	                'text' => gt('Check for updated version'),
                    'url' => makeLink(array(
                        'controller' => 'administration',
                        'action' => 'manage_version'
                    ))
                )
            )
        )
    );
}

return $expAdminMenu;

?>
