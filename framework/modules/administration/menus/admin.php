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

if (!defined('EXPONENT'))
    exit('');

global $user, $db;

if ($user->globalPerm('hide_exp_menu'))
    return false;

$my_version = gt("Exponent Version") . " : " . expVersion::getVersion(true, false, false) . "<br />";
if (EXPONENT_VERSION_TYPE != '') {
    $my_type = gt("Release level") . " : " . EXPONENT_VERSION_TYPE . EXPONENT_VERSION_ITERATION . "<br />";
} else {
    $my_type = '';
}
$my_releasedate = gt("Release date") . " : " . date("F-d-Y", EXPONENT_VERSION_BUILDDATE);

if (bs3()) {
    $admin_text = 'Admin';
    $admin_icon = 'fa-star';
} else {
    $admin_text = '<img src="' . $this->asset_path . 'images/admintoolbar/expbar.png" alt="' . gt('Exponent') . '">';
    $admin_icon = '';
}

if ($user->isAdmin()) {
    $expAdminMenu = array(
        'text'      => $admin_text,
        'icon'      => $admin_icon,
        'classname' => 'site',
        'submenu'   => array(
            'id'       => 'admin',
            'itemdata' => array(
                array(
                    'classname' => 'info',
                    'icon'      => 'fa-info-circle',
                    'text'      => gt('About ExponentCMS'),
                    "submenu"   => array(
                        'id'       => 'ver',
                        'itemdata' => array(
                            array(
                                'classname' => 'moreinfo',
                                'info'      => '1',
                                'text'      => $my_version . $my_type . $my_releasedate . "<br />" .
                                    gt("PHP Version") . " : " . phpversion() . "<br />" .
                                    gt("Max Upload") . " : " . expCore::maxUploadSize(),
                                "disabled"  => true
                            ),
                            array(
                                'text'      => gt("Exponent Documentation"),
                                'icon'      => 'fa-book',
                                'classname' => 'docs',
                                'url'       => '#',
                                'id'        => 'docs-toolbar',
                            ),
                            array(
                                'text'      => gt("Discuss Exponent"),
                                'icon'      => 'fa-comments',
                                'classname' => 'forums',
                                'url'       => '#',
                                'id'        => 'forums-toolbar',
                            ),
                            array(
                                'text'      => gt("Report a bug"),
                                'icon'      => 'fa-bug',
                                'classname' => 'reportbug',
                                'url'       => '#',
                                'id'        => 'reportabug-toolbar',
                            )
                        )
                    )
                ),
            )
        )
    );

    $expAdminMenu['submenu']['itemdata'][] = array(
        'text'      => gt("Configure Website"),
        'icon'      => 'fa-gears',
        'classname' => 'configure',
        'url'       => makeLink(
            array(
                'controller' => 'administration',
                'action'     => 'configure_site'
            )
        )
    );
} else {
    $expAdminMenu = array(
        'text'      => $admin_text,
        'icon'      => $admin_icon,
        'classname' => 'site',
        'submenu'   => array(
            'id'       => 'admin',
            'itemdata' => array(
                array(
                    'classname' => 'info',
                    'text'      => gt('About ExponentCMS'),
                    "submenu"   => array(
                        'id'       => 'ver',
                        'itemdata' => array(
                            array(
                                'classname' => 'moreinfo',
                                'text'      => $my_version . $my_type . $my_releasedate,
                                "disabled"  => true
                            ),
                            array(
                                'text'      => gt("Exponent Documentation"),
                                'icon'      => 'fa-book',
                                'classname' => 'docs',
                                'url'       => '#',
                                'id'        => 'docs-toolbar',
                            ),
                            array(
                                'text'      => gt("Discuss Exponent"),
                                'icon'      => 'fa-comments',
                                'classname' => 'forums',
                                'url'       => '#',
                                'id'        => 'forums-toolbar',
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
        'text'      => gt("Site Management"),
        'icon'      => 'fa-cog',
        'classname' => 'manage',
        'submenu'   => array(
            'id'       => 'manage',
            'itemdata' => array(
                array(
                    'text'      => gt('Manage Site Comments'),
                    'icon'      => 'fa-comment',
                    'classname' => 'manage',
                    'url'       => makeLink(
                        array(
                            'controller' => 'expComment',
                            'action'     => 'manage'
                        )
                    )
                ),
                array(
                    'text'      => gt('Manage Site Tags'),
                    'icon'      => 'fa-tags',
                    'classname' => 'manage',
                    'url'       => makeLink(
                        array(
                            'controller' => 'expTag',
                            'action'     => 'manage'
                        )
                    )
                ),
                array(
                    'text'      => gt('Manage Site Categories'),
                    'icon'      => 'fa-sitemap',
                    'classname' => 'manage',
                    'url'       => makeLink(
                        array(
                            'controller' => 'expCat',
                            'action'     => 'manage'
                        )
                    )
                ),
            )
        )
    );

    if (SITE_FILE_MANAGER == 'picker') {
        $expAdminMenu['submenu']['itemdata'][count(
            $expAdminMenu['submenu']['itemdata']
        ) - 1]['submenu']['itemdata'][] = array(
            'text'      => gt('Manage File Folders'),
            'icon'      => 'fa-folder-open',
            'classname' => 'manage',
            'url'       => makeLink(
                array(
                    'controller' => 'expCat',
                    'action'     => 'manage',
                    'model'      => 'file'
                )
            )
        );
    }

    $expAdminMenu['submenu']['itemdata'][count(
        $expAdminMenu['submenu']['itemdata']
    ) - 1]['submenu']['itemdata'] = array_merge(
        $expAdminMenu['submenu']['itemdata'][count($expAdminMenu['submenu']['itemdata']) - 1]['submenu']['itemdata'],
        array(
            array(
                'text'      => gt('Manage Site Forms'),
                'icon'      => 'fa-list-alt',
                'classname' => 'manage',
                'url'       => makeLink(
                    array(
                        'controller' => 'forms',
                        'action'     => 'manage',
                    )
                )
            ),
            array(
                'text'      => gt('View Top Searches'),
                'icon'      => 'fa-signal',
                'classname' => 'search',
                'url'       => makeLink(
                    array(
                        'controller' => 'search',
                        'action'     => 'topSearchReport'
                    )
                )
            ),
            array(
                'text'      => gt('View Search Queries'),
                'icon'      => 'fa-search',
                'classname' => 'search',
                'url'       => makeLink(
                    array(
                        'controller' => 'search',
                        'action'     => 'searchQueryReport'
                    )
                )
            ),
            array(
                'text'      => gt('Regenerate Search Index'),
                'icon'      => 'fa-search-plus',
                'classname' => 'search',
                'url'       => makeLink(
                    array(
                        'controller' => 'search',
                        'action'     => 'spider'
                    )
                )
            ),
        )
    );
}

$groups = $db->selectObjects('groupmembership', 'member_id=' . $user->id . ' AND is_admin=1');
//FIXME should a group admin get the entire User Management menu?
if ($user->isAdmin() || !empty($groups)) {
    $expAdminMenu['submenu']['itemdata'][] = array(
        'text'      => gt('User Management'),
        'icon'      => 'fa-group',
        'classname' => 'users',
        'submenu'   => array(
            'id'       => 'usermanagement',
            'itemdata' => array(
                array(
                    'text'      => gt('User Accounts'),
                    'icon'      => 'fa-user',
                    'classname' => 'euser',
                    'url'       => makeLink(
                        array(
                            'controller' => 'users',
                            'action'     => 'manage'
                        )
                    ),
                ),
                array(
                    'text'      => gt('Group Accounts'),
                    'icon'      => 'fa-group',
                    'classname' => 'egroup',
                    'url'       => makeLink(
                        array(
                            'controller' => 'users',
                            'action'     => 'manage_groups'
                        )
                    ),
                ),
                array(
                    'text' => gt('User Profile Extensions'),
                    'icon' => 'fa-magic',
                    'url'  => makeLink(
                        array(
                            'controller' => 'users',
                            'action'     => 'manage_extensions'
                        )
                    ),
                ),
                array(
                    'text' => gt('User Sessions'),
                    'icon' => 'fa-group',
                    'url'  => makeLink(
                        array(
                            'controller' => 'users',
                            'action'     => 'manage_sessions'
                        )
                    ),
                ),
                array(
                    'text'      => gt('Import Users'),
                    'icon'      => 'fa-upload',
                    'classname' => 'import',
                    'url'       => makeLink(
                        array(
                            'controller' => 'users',
                            'action'     => 'import'
                        )
                    ),
                )
            )
        )
    );
}

if ($user->isSuperAdmin()) {
    $tmp = count($expAdminMenu['submenu']['itemdata']);
    if (USE_LDAP && function_exists('ldap_connect')) {
        $expAdminMenu['submenu']['itemdata'][count(
            $expAdminMenu['submenu']['itemdata']
        ) - 1]['submenu']['itemdata'][] = array(
            'text' => gt('Sync LDAP Users'),
            'icon' => 'fa-rotate-left',
            'url'  => makeLink(
                array(
                    'controller' => 'users',
                    'action'     => 'sync_LDAPUsers'
                )
            ),
        );
    }
    $expAdminMenu['submenu']['itemdata'][count(
        $expAdminMenu['submenu']['itemdata']
    ) - 1]['submenu']['itemdata'][] = array(
        'text'      => gt('Mass Mailer'),
        'icon'      => 'fa-envelope',
        'url'       => makeLink(
            array(
                'controller' => 'administration',
                'action'     => 'mass_mail'
            )
        ),
        'classname' => 'email',
    );
    $expAdminMenu['submenu']['itemdata'][] = array(
        'text'      => gt('Super-Admin Tools'),
        'icon'      => 'fa-asterisk',
        'classname' => 'development',
        'submenu'   => array(
            'id'       => 'development',
            'itemdata' => array(
                array(
                    'text'      => (DEVELOPMENT) ? gt('Turn Error Reporting off') : gt('Turn Error Reporting on'),
                    'icon'      => (DEVELOPMENT) ? 'fa-list text-danger' : 'fa-list',
                    'classname' => (DEVELOPMENT) ? 'develop_on_red' : 'develop_off',
                    'url'       => makeLink(
                        array(
                            'controller' => 'administration',
                            'action'     => 'toggle_dev'
                        )
                    )
                ),
                array(
                    'text'      => (LOGGER) ? gt('Turn Logger off') : gt('Turn Logger on'),
                    'icon'      => (LOGGER) ? 'fa-indent text-danger' : 'fa-indent',
                    'classname' => (LOGGER) ? 'develop_on_red' : 'develop_off',
                    'url'       => makeLink(
                        array(
                            'controller' => 'administration',
                            'action'     => 'toggle_log'
                        )
                    )
                ),
                array(
                    'text'    => gt('Database'),
                    'icon'    => 'fa-briefcase',
                    'submenu' => array(
                        'id'       => 'database',
                        'itemdata' => array(
                            array(
                                'text'      => gt("Manage Database"),
                                'icon'      => 'fa-cog',
                                'classname' => 'manage',
                                'url'       => '#',
                                'id'        => 'manage-db',
                            ),
                            array(
                                'text'      => gt('Restore Database'),
                                'icon'      => 'fa-download',
                                'classname' => 'import',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'file',
                                        'action' => 'import_eql'
                                    )
                                ),
                            ),
                            array(
                                'text'      => gt('Backup Database'),
                                'icon'      => 'fa-upload',
                                'classname' => 'export',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'file',
                                        'action' => 'export_eql'
                                    )
                                ),
                            ),
                            array(
                                'text'      => gt("Import/Export Data"),
                                'icon'      => 'fa-exchange',
                                'classname' => 'import',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'importexport',
                                        'action' => 'manage'
                                    )
                                ),
                            ),
                            array(
                                'text' => gt('Update Tables'),
                                'icon' => 'fa-toggle-up',
                                'url'  => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'install_tables'
                                    )
                                )
                            ),
                            array(
                                'text' => gt('Optimize Database'),
                                'icon' => 'fa-wrench',
                                'url'  => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'fix_optimize_database'
                                    )
                                )
                            ),
                            array(
                                'text' => gt('Repair Database'),
                                'icon' => 'fa-wrench',
                                'url'  => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'fix_database'
                                    )
                                )
                            ),
                            array(
                                'text' => gt('Fix Table Names'),
                                'icon' => 'fa-wrench',
                                'url'  => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'fix_tables'
                                    )
                                )
                            ),
//                            array(
//                                'text' => gt('Reset Sessions Table'),
//                                'url'  => makeLink(array(
//                                    'controller' => 'administration',
//                                    'action'     => 'fix_sessions'
//                                ))
//                            ),
                            array(
                                'text'      => gt('Remove Unneeded Table Columns'),
                                'icon'      => 'fa-wrench',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'delete_unused_columns'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Remove Unused Tables'),
                                'icon'      => 'fa-wrench',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'manage_unused_tables'
                                    )
                                )
                            )
                        )
                    )
                ),
                array(
                    'text' => gt('Migrate 0.9x Site'),
                    'icon' => 'fa-angle-double-right',
                    'url'  => makeLink(
                        array(
                            'controller' => 'migration',
                            'action'     => 'configure'
                        )
                    )
                ),
                array(
                    'text'    => gt('Extensions'),
                    'icon'    => 'fa-magic',
                    'submenu' => array(
                        'id'       => 'extensions',
                        'itemdata' => array(
                            array(
                                'text'      => gt('Install Extension'),
                                'icon'      => 'fa-upload',
                                'classname' => 'fileuploader',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'install_extension'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Manage Modules'),
                                'icon'      => 'fa-cog',
                                'classname' => 'manage',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'expModule',
                                        'action'     => 'manage'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Manage Translations'),
                                'icon'      => 'fa-cog',
                                'classname' => 'manage',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'manage_lang'
                                    )
                                ),
                            ),
                            array(
                                'text'      => gt('Manage Themes'),
                                'icon'      => 'fa-cog',
                                'classname' => 'manage',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'manage_themes'
                                    )
                                ),
                            ),
                            array(
                                'text'      => (MOBILE) ? gt('Turn Mobile View off') : gt('Turn Mobile View on'),
                                'icon'      => (MOBILE) ? 'fa-tablet text-success' : 'fa-tablet',
                                'classname' => (MOBILE) ? 'develop_on_green' : 'develop_off',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'togglemobile'
                                    )
                                ),
                            ),
                            array(
                                'text' => gt('Run Upgrade Scripts'),
                                'icon' => 'fa-share',
                                'url'  => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'install_upgrades'
                                    )
                                ),
                            ),
                        )
                    )
                ),
                array(
                    'text'    => gt('System Cache'),
                    'icon'    => 'fa-save',
                    'submenu' => array(
                        'id'       => 'cache',
                        'itemdata' => array(
                            array(
                                'text'      => (MINIFY) ? gt('Turn Minification off') : gt('Turn Minification on'),
                                'icon'      => (MINIFY) ? 'fa-paperclip text-success' : 'fa-paperclip',
                                'classname' => (MINIFY) ? 'develop_on_green' : 'develop_off',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'toggle_minify'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Clear Smarty Cache'),
                                'icon'      => 'fa-ban',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'clear_smarty_cache'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Clear CSS/Minify Cache'),
                                'icon'      => 'fa-ban',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'clear_css_cache'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Clear Image Cache'),
                                'icon'      => 'fa-ban',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'clear_image_cache'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Clear RSS/Podcast Cache'),
                                'icon'      => 'fa-ban',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'clear_rss_cache'
                                    )
                                )
                            ),
                            array(
                                'text'      => gt('Clear All Caches'),
                                'icon'      => 'fa-ban',
                                'classname' => 'remove',
                                'url'       => makeLink(
                                    array(
                                        'controller' => 'administration',
                                        'action'     => 'clear_all_caches'
                                    )
                                )
                            ),
                        )
                    )
                ),
                array(
                    'text'      => gt('View Recycle Bin'),
                    'icon'      => 'fa-trash-o',
                    'classname' => 'manage',
                    'url'       => makeLink(
                        array(
                            'controller' => 'recyclebin',
                            'action'     => 'showall'
                        )
                    )
                ),
                array(
                    'text'      => (ENABLE_WORKFLOW) ? gt('Turn Workflow off') : gt('Turn Workflow on'),
                    'icon'      => (ENABLE_WORKFLOW) ? 'fa-shield text-success' : 'fa-shield',
                    'classname' => (ENABLE_WORKFLOW) ? 'develop_on_green' : 'develop_off',
                    'url'       => makeLink(
                        array(
                            'controller' => 'administration',
                            'action'     => 'toggle_workflow'
                        )
                    ),
                    'id'        => 'workflow-toggle',
                ),
                array(
                    'text'      => (MAINTENANCE_MODE) ? gt('Turn Maintenance Mode off') : gt(
                        'Turn Maintenance Mode on'
                    ),
                    'icon'      => (MAINTENANCE_MODE) ? 'fa-warning text-danger' : 'fa-warning',
                    'classname' => (MAINTENANCE_MODE) ? 'develop_on_red' : 'develop_off',
                    'url'       => makeLink(
                        array(
                            'controller' => 'administration',
                            'action'     => 'toggle_maintenance'
                        )
                    )
                ),
                array(
                    'text' => gt('Check for updated version'),
                    'icon' => 'fa-toggle-up',
                    'url'  => makeLink(
                        array(
                            'controller' => 'administration',
                            'action'     => 'manage_version'
                        )
                    )
                )
            )
        )
    );

    if (!SMTP_USE_PHP_MAIL) {
        $expAdminMenu['submenu']['itemdata'][count(
            $expAdminMenu['submenu']['itemdata']
        ) - 1]['submenu']['itemdata'][] = array(
            'text'      => gt('Test SMTP Mail Server Settings'),
            'icon'      => 'fa-check',
            'classname' => 'configure',
            'url'       => makeLink(
                array(
                    'controller' => 'administration',
                    'action'     => 'test_smtp'
                )
            ),
        );
    }

}

return $expAdminMenu;

?>
