<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');
if (!$user->isAdmin()) return false;

$i18n = exponent_lang_loadFile('modules/administrationmodule/tasks/coretasks.php');

return array(
    'text'=>'<img src="'.$this->asset_path.'images/admintoolbar/expbar.png">',
    'classname'=>'site',
    'submenu'=>array(
        'id'=>'admin',
        'itemdata'=>array(
            array(
                'text'=>$i18n['configuration'],
                'classname'=>'config',
                'submenu'=>array(
                    'id'=>'configure',
                    'itemdata'=>array(
                        array(
                            'text'=>"Configure Site",
			                'url'=>makeLink(array('module'=>'administration','action'=>'configure_site')),
                        ),
                        array(
                            'text'=>$i18n['upload_extension'],
    		                'url'=>makeLink(array('module'=>'administrationmodule','action'=>'upload_extension')),
                        ),
                        array(
                            'text'=>expLang::gettext('Manage Active Modules'),
			                'url'=>makeLink(array('controller'=>'expModule','action'=>'manage')),
                        ),
                        array(
                            'text'=>$i18n['manage_themes'],
			                'url'=>makeLink(array('module'=>'administrationmodule','action'=>'managethemes')),
                        ),
                        array(
                            'text'=>'Spider Site',
			                'url'=>makeLink(array('module'=>'search','action'=>'spider')),
                        ),
                        array(
                            'text'=>expLang::gettext("Configure Editor Toolbar"),
			                'url'=>makeLink(array('module'=>'expHTMLEditor','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>$i18n['user_management'],
                'classname'=>'users',
                'submenu'=>array(
                    'id'=>'usermanagement',
                    'itemdata'=>array(
                        array(
                            'text'=>$i18n['user_accounts'],
    		                'url'=>makeLink(array('controller'=>'users','action'=>'manage')),
    		                'icon'=>ICON_RELATIVE."userperms.png"
                        ),
                        array(
                            'text'=>$i18n['group_accounts'],
    		                'url'=>makeLink(array('module'=>'users','action'=>'manage_groups')),
    		                'icon'=>ICON_RELATIVE."groupperms.png"
                        ),
                        array(
                            'text'=>$i18n['profile_definitions'],
    		                'url'=>makeLink(array('module'=>'users','action'=>'manage_extensions')),
    		                'icon'=>ICON_RELATIVE."groupperms.png"
                        ),
                        array(
                            'text'=>$i18n['user_sessions'],
    		                'url'=>makeLink(array('module'=>'users','action'=>'manage_sessions')),
    		                'icon'=>ICON_RELATIVE."groupperms.png"
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>'Developer Tools',
                'classname'=>'development',
                'submenu'=>array(
                    'id'=>'development',
                    'itemdata'=>array(
                        array(
                            'text'=>$i18n['database'],
                            'submenu'=>array(
                                'id'=>'database',
                                'itemdata'=>array(
                                    array(
                                        'text'=>$i18n['install_tables'],
                                        'url'=>makeLink(array('module'=>'administrationmodule','action'=>'installtables')),
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
                                        'text'=>$i18n['optimize_database'],
                                        'url'=>makeLink(array('module'=>'administrationmodule','action'=>'optimizedatabase')),
                                    ),
                                    array(
                                        'text'=>'Remove Unused Tables',
                                        'url'=>makeLink(array('controller'=>'administration','action'=>'manage_unused_tables')),
                                    ),
                                )
                            )
                        ),
                        
                        array(
                            'text'=>'Migration',
                            'submenu'=>array(
                                'id'=>'migration',
                                'itemdata'=>array(
                                    array(
                                        'text'=>'Configure Migration Settings',
            			                'url'=>makeLink(array('module'=>'migration','action'=>'configure')),
                                    ),
                                    array(
                                        'text'=>'Migrate Pages',
            			                'url'=>makeLink(array('module'=>'migration','action'=>'manage_pages')),
                                    ),
                                    array(
                                        'text'=>'Migrate Files',
            			                'url'=>makeLink(array('module'=>'migration','action'=>'manage_files')),
                                    ),
                                    array(
                                        'text'=>'Migrate Content',
            			                'url'=>makeLink(array('module'=>'migration','action'=>'manage_content')),
                                    ),
                                    array(
                                        'text'=>'Migrate Users/Groups',
            			                'url'=>makeLink(array('module'=>'migration','action'=>'manage_users')),
                                    ),
                                )
                            )
                        ),
                        array(
                            'text'=>$i18n['toggle_dev'],
			                'url'=>makeLink(array('module'=>'administrationmodule','action'=>'toggle_dev')),
                        ),
                        array(
                            'text'=>"Toggle Minify",
			                'url'=>makeLink(array('module'=>'administration','action'=>'toggle_minify')),
                        ),
                        array(
                            'text'=>$i18n['clear_smarty'],
			                'url'=>makeLink(array('module'=>'administrationmodule','action'=>'clear_smarty_cache')),
                        ),
                        array(
                            'text'=>$i18n['toggle_maint'],
			                'url'=>makeLink(array('module'=>'administrationmodule','action'=>'toggle_maintenance')),
                        ),
                    ),                        
                ),
            ),
        ),
     )
);

?>
