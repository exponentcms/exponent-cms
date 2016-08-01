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

/**
 * @subpackage Controllers
 * @package Modules
 */

class migrationController extends expController {
    protected $add_permissions = array(
        'analyze'=>'Analyze Data',
        'migrate'=>'Migrate Data'
    );

    // this is a list of modules that we can convert to exp2 type modules.
    public $new_modules = array(
//        'addressbookmodule'=>'address',
        'imagegallerymodule'=>'photo',
        'linklistmodule'=>'links',
        'newsmodule'=>'news',
        'slideshowmodule'=>'photo',
        'snippetmodule'=>'snippet',
        'swfmodule'=>'text',
        'textmodule'=>'text',
        'resourcesmodule'=>'filedownload',
        'rotatormodule'=>'text',
        'faqmodule'=>'faq',
        'headlinemodule'=>'text',
        'linkmodule'=>'links',
        'weblogmodule'=>'blog',
        'listingmodule'=>'portfolio',
        'youtubemodule'=>'media',
        'mediaplayermodule'=>'media',
        'bannermodule'=>'banner',
        'feedlistmodule'=>'rss',
        'simplepollmodule'=>'simplePoll',
        'navigationmodule'=>'navigation',
        'calendarmodule'=>'event',
        'formmodule'=>'forms',
        'contactmodule'=>'forms',  // this module is converted to a functionally similar form
        'containermodule'=>'container',
    );

    // these are modules that have either been deprecated or have no content to migrate
    // Not sure we need to note deprecated modules...
    public $deprecated_modules = array(
        'administrationmodule',
//        'containermodule',    // not really deprecated, but must be in this list to skip processing?
//        'navigationmodule',   // views are still used, so modules need to be imported?
        'loginmodule',
        'searchmodule',  
        'imagemanagermodule',
        'imageworkshopmodule',
         'inboxmodule',
        'rssmodule',
// the following 0.97/98 modules were added to this list
        'articlemodule',
        'bbmodule',
        'pagemodule',
        'previewmodule',
        'tasklistmodule',
        'wizardmodule',
// other older or user-contributed modules we don't want to deal with
        'addressbookmodule',    // moved to deprecated list since this is NOT the type of address we use in 2.x
        'cataloguemodule',
        'codemapmodule',
        'extendedlistingmodule',
        'googlemapmodule',
        'greekingmodule',
        'guestbookmodule',
        'keywordmodule',
        'sharedcoremodule',
        'svgallerymodule',
        'uiswitchermodule',
        'filemanagermodule',
    );

	/**
	 * name of module
	 * @return string
	 */
    static function displayname() { return gt("Content Migration Controller"); }

	/**
	 * description of module
	 * @return string
	 */
    static function description() { return gt("Use this module to pull Exponent 1 style content from your old site."); }

	/**
	 * if module has associated sources
	 * @return bool
	 */
    static function hasSources() { return false; }

	/**
	 * if module has associated content
	 * @return bool
	 */
    static function hasContent() { return false; }

	/**
	 * gather info about all pages in old site for user selection
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function manage_pages() {
        global $db;

        expHistory::set('manageable', $this->params);
        $old_db = $this->connect();
        $pages = $old_db->selectObjects('section','id > 1');
        foreach($pages as $page) {
			if ($db->selectObject('section',"id='".$page->id."'")) {
				$page->exists = true;
			} else {
				$page->exists = false;
			}
		}
        assign_to_template(array(
            'pages'=>$pages
        ));
    }

	/**
	 * copy selected pages over from old site
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function migrate_pages() {
        global $db;

		$del_pages = '';
        if (isset($this->params['wipe_pages'])) {
            $db->delete('section',"id > '1'");
			$del_pages = ' '.gt('after clearing database of pages');
		}
        $successful = 0;
        $failed     = 0;
        $old_db = $this->connect();
		if (!empty($this->params['pages'])) {
			foreach($this->params['pages'] as $pageid) {
				$page = $old_db->selectObject('section', 'id='.$pageid);
				// make sure the SEF name is valid
				global $router;
				if (empty($page->sef_name)) {
					if (isset($page->name)) {
						$page->sef_name = $router->encode($page->name);
					} else {
						$page->sef_name = $router->encode('Untitled');
					}
				}
				$dupe = $db->selectValue('section', 'sef_name', 'sef_name="'.$page->sef_name.'"');
				if (!empty($dupe)) {
					list($u, $s) = explode(' ',microtime());
                    $page->sef_name .= '-'.$s.'-'.$u;
				}
//                $page->sef_name = $page->sef_name;
//                unset($page->sef_name);
				$ret = $db->insertObject($page, 'section');
				if (empty($ret)) {
					$failed++;
				} else {
					$successful++;
				}
			}
		}
		if (!empty($this->params['rep_pages'])) {
			foreach($this->params['rep_pages'] as $pageid) {
				$db->delete('section','id='.$pageid);
				$page = $old_db->selectObject('section', 'id='.$pageid);
				// make sure the SEF name is valid
				global $router;
				if (empty($page->sef_name)) {
					if (isset($page->name)) {
						$page->sef_name = $router->encode($page->name);
					} else {
						$page->sef_name = $router->encode('Untitled');
					}
				}
				$dupe = $db->selectValue('section', 'sef_name', 'sef_name="'.$page->sef_name.'"');
				if (!empty($dupe)) {
					list($u, $s) = explode(' ',microtime());
                    $page->sef_name .= '-'.$s.'-'.$u;
				}
//                $page->sef_name = $page->sef_name;
//                unset($page->sef_name);
				$ret = $db->insertObject($page, 'section');
				if (empty($ret)) {
					$failed++;
				} else {
					$successful++;
				}
			}
		}

		if (isset($this->params['copy_permissions'])) {
			$db->delete('userpermission',"module = 'navigation' AND source = ''");
			$db->delete('grouppermission',"module = 'navigation' AND source = ''");
			
			$users = $db->selectObjects('user','id > 1');
			foreach($users as $user) {
				$pages = $old_db->selectObjects('userpermission',"uid='".$user->id."' AND module = 'navigationmodule' AND source = ''");
				foreach($pages as $page) {
					if ($db->selectObject('section','id = '.$page->internal)) {
						 if ($page->permission != 'administrate') {
                             $page->module = 'navigation';
							 $db->insertObject($page,'userpermission');
						 }
					}
				}
			}		
			$groups = $db->selectObjects('group','1');
			foreach($groups as $group) {
				$pages = $old_db->selectObjects('grouppermission',"gid='".$group->id."' AND module = 'navigationmodule' AND source = ''");
				foreach($pages as $page) {
					if ($db->selectObject('section','id = '.$page->internal)) {
						 if ($page->permission != 'administrate') {
                             $page->module = 'navigation';
							 $db->insertObject($page,'grouppermission');
						 }
					}
				}
			}		
		}

        flash('message', $successful.' '.gt('pages were imported from').' '.$this->config['database'].$del_pages);
        if ($failed > 0) {
            flash('error', $failed.' '.gt('pages could not be imported from').' '.$this->config['database'].' '.gt('This is usually because a page with the same ID already exists in the database you importing to.'));
        }

        expSession::clearCurrentUserSessionCache();
        expHistory::back();
    }

	/**
	 * gather info about all files in old site for user selection
	 * @return void
	 */
    public function manage_files() {
        expHistory::set('manageable', $this->params);
        $old_db = $this->connect();
        $files = $old_db->selectObjects('file');
        assign_to_template(array(
            'count'=>count($files)
        ));
    }

	/**
	 * copy selected file information (not the files themselves) over from old site
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function migrate_files() {
        global $db;

        expHistory::set('manageable', $this->params);
        $old_db = $this->connect();
        $db->delete('expFiles');

        //import the files
        $oldfiles = $old_db->selectObjects('file');
        foreach ($oldfiles as $oldfile) {
            unset(
                $oldfile->name,
                $oldfile->collection_id
            );
            $file = $oldfile;
            $file->directory = $file->directory."/";
            $db->insertObject($file,'expFiles');
			$oldfile->exists = file_exists(BASE.$oldfile->directory."/".$oldfile->filename);
		}
        assign_to_template(array(
            'files'=>$oldfiles,
            'count'=>count($oldfiles)
        ));
    }

	/**
	 * gather info about all modules in old site for user selection
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function manage_content() {
        //global $db;
        //$containers = $db->selectObjects('container', 'external="N;"');
        //eDebug($containers);
        $old_db = $this->connect();

        $sql  = 'SELECT *, COUNT(module) as count FROM '.$this->config['prefix'].'_sectionref WHERE is_original=1 GROUP BY module';
        $modules = $old_db->selectObjectsBySql($sql);
		for ($i = 0, $iMax = count($modules); $i < $iMax; $i++) {
            if (array_key_exists($modules[$i]->module, $this->new_modules)) {
                $newmod = expModules::getController($this->new_modules[$modules[$i]->module]);
//                $newmod = $this->new_modules[$modules[$i]->module];
                $modules[$i]->action = '<span style="color:green;">'.gt('Converting content to').' '.$newmod->displayname()."</span>";
//                $modules[$i]->action = '<span style="color:green;">'.gt('Converting content to').' '.$newmod::displayname()."</span>";  //TODO this doesn't work w/ php 5.2
            } elseif (in_array($modules[$i]->module, $this->deprecated_modules)) {
                // $modules[$i]->action = '<span style="color:red;">This module is deprecated and will not be migrated.</span>';
                $modules[$i]->notmigrating = 1;
//            } elseif (in_array($modules[$i]->module, $this->needs_written)) {
//                $modules[$i]->action = '<span style="color:orange;">'.gt('Still needs migration script written').'</span>';
            } else {
                $modules[$i]->action = gt('Migrating as is.');
            }
        }
        //eDebug($modules);

        assign_to_template(array(
            'modules'=>$modules
        ));
    }

	/**
	 * copy selected modules and their contents over from old site
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function migrate_content() {
        global $db;

        $old_db = $this->connect();
        if (isset($this->params['wipe_content'])) {
            $db->delete('sectionref');
            $db->delete('container');
            $db->delete('text');
            $db->delete('snippet');
            $db->delete('links');
            $db->delete('news');
//            $db->delete('filedownloads');
            $db->delete('filedownload');
            $db->delete('photo');
            $db->delete('headline');
            $db->delete('blog');
//            $db->delete('faqs');
            $db->delete('faq');
            $db->delete('portfolio');
            $db->delete('media');
            $db->delete('banner');
            $db->delete('companies');
            $db->delete('addresses');
            $db->delete('content_expComments');
            $db->delete('content_expFiles');
            $db->delete('content_expSimpleNote');
            $db->delete('content_expTags');
            $db->delete('content_expCats');
            $db->delete('expComments');
            $db->delete('expConfigs', 'id>1');  // don't delete migration config
//            $db->delete('expFiles');			// deleted and rebuilt during (previous) file migration
            $db->delete('expeAlerts');
            $db->delete('expeAlerts_subscribers');
            $db->delete('expeAlerts_temp');
            $db->delete('expSimpleNote');
            $db->delete('expRss');
            $db->delete('expCats');
            $db->delete('calendar');
            $db->delete('eventdate');
            $db->delete('calendarmodule_config');
            $db->delete('calendar_external');
            $db->delete('calendar_reminder_address');
            $db->delete('event');
            $db->delete('poll_question');
            $db->delete('poll_answer');
            $db->delete('poll_timeblock');
            $db->delete('simplepollmodule_config');
            $db->delete('simplepoll_question');
            $db->delete('simplepoll_answer');
            $db->delete('simplepoll_timeblock');
            $db->delete('formbuilder_address');
            $db->delete('formbuilder_control');
            $db->delete('formbuilder_form');
            $db->delete('formbuilder_report');
            $db->delete('forms');
            $db->delete('forms_control');
            @$this->msg['clearedcontent']++;
        }
		
		if (!empty($this->params['replace'])) {
			foreach($this->params['replace'] as $replace) {
				switch ($replace) {
				    case 'containermodule':
					    $db->delete('container');
						break;
					case 'textmodule':
					case 'rotatormodule':
					case 'swfmodule':
						$db->delete('text');
						break;
					case 'snippetmodule':
						$db->delete('snippet');
						break;
					case 'linklistmodule':
					case 'linkmodule':
						$db->delete('links');
						break;
					case 'newsmodule':
						$db->delete('news');
						break;
					case 'resourcesmodule':
//						$db->delete('filedownloads');
                        $db->delete('filedownload');
						break;
					case 'imagegallerymodule':
					case 'slideshowmodule':
						$db->delete('photo');
						break;
					case 'headlinemodule':
						$db->delete('headline');
						break;
					case 'weblogmodule':
						$db->delete('blog');
						$db->delete('expComments');
						$db->delete('content_expComments');
						break;
					case 'faqmodule':
						$db->delete('faq');
						break;
					case 'listingmodule':
						$db->delete('portfolio');
						break;
					case 'calendarmodule':
						$db->delete('calendar');
						$db->delete('eventdate');
						$db->delete('calendarmodule_config');
                        $db->delete('calendar_external');
                        $db->delete('calendar_reminder_address');
                        $db->delete('event');
						break;
					case 'simplepollmodule':
						$db->delete('poll_question');
						$db->delete('poll_answer');
						$db->delete('poll_timeblock');
						$db->delete('simplepollmodule_config');
                        $db->delete('simplepoll_question');
                        $db->delete('simplepoll_answer');
                        $db->delete('simplepoll_timeblock');
						break;
					case 'formmodule':
						$db->delete('formbuilder_address');
						$db->delete('formbuilder_control');
						$db->delete('formbuilder_form');
						$db->delete('formbuilder_report');
                        $db->delete('forms');
                        $db->delete('forms_control');
						break;
					case 'youtubemodule':
					case 'mediaplayermodule':
						$db->delete('media');
						break;
					case 'bannermodule':
						$db->delete('banner');
						$db->delete('companies');
						break;
					case 'addressmodule':
						$db->delete('addresses');
						break;
				}
			}
		}

        //pull the sectionref data for selected modules
		if (empty($this->params['migrate'])) {
			$where = '1';
		} else {
			$where = '';
			foreach ($this->params['migrate'] as $key) {
				if (!empty($where)) {$where .= " or";}
				$where .= " module='".$key."'";
			}
		}

        // pull the sectionref data for selected modules
        $secref = $old_db->selectObjects('sectionref',$where);
        if (empty($this->params['migrate'])) $this->params['migrate'] = array();
        foreach ($secref as $sr) {
            // convert hard coded modules which are only found in sectionref
            if (array_key_exists($sr->module, $this->new_modules) && ($sr->refcount==1000)) {
	            $iloc = expCore::makeLocation($sr->module,$sr->source,$sr->internal);
                $tmp = new stdClass();
	            $tmp->module = '';
//                $this->convert($iloc,$iloc->mod,1);
                $this->convert($iloc,$tmp,1);  // convert the hard-coded module

                // convert the source to new exp controller
                $sr->module = $this->new_modules[$sr->module];
            }

            // copy over and convert sectionrefs
            if (!in_array($sr->module, $this->deprecated_modules)) {
                // if the module is not in the deprecation list, we're hitting here
                if (!$db->selectObject('sectionref',"source='".$sr->source."'")) {
					if (array_key_exists($sr->module, $this->new_modules)) {
						// convert the source to new exp controller
						$sr->module = $this->new_modules[$sr->module];
					}
                    $db->insertObject($sr, 'sectionref');
                    @$this->msg['sectionref']++;
                }
            }
        }

        //pull over all the top level containers
        $containers = $old_db->selectObjects('container', 'external="N;"');
        foreach ($containers as $cont) {
            $oldint = expUnserialize($cont->internal);
            $newint = expCore::makeLocation('container',$oldint->src);
            if (!$db->selectObject('container',"internal='".serialize($newint)."'")) {
                unset($cont->id);
                $cont->internal = serialize($newint);
                $cont->action = 'showall';
                if ($cont->view == 'Default') {
                    $cont->view = 'showall';
                } else {
                    $cont->view = 'showall_'.$cont->view;
                }
                $cont->view_data = null;
                $db->insertObject($cont, 'container');
                @$this->msg['container']++;
            }
        }
        // echo "Imported containermodules<br>";

        // // this will pull all the old modules.  if we have a exp2 equivalent module
        // // we will convert it to the new type of module before pulling.
        $cwhere = ' and (';
        $i=0;
        foreach ($this->params['migrate'] as $key) {
            $cwhere .= ($i==0) ? "" : " or ";
            $cwhere .= "internal like '%".$key."%'";
            $i=1;
        }
        $cwhere .= ")";
        $modules = $old_db->selectObjects('container', 'external != "N;"'.$cwhere.' ORDER BY "rank"');
        foreach($modules as $module) {
            $iloc = expUnserialize($module->internal);
            if (array_key_exists($iloc->mod, $this->new_modules)) {
                // convert new modules added via container
                unset(
                    $module->internal,
                    $module->action
                );
//                unset($module->view);
                $this->convert($iloc, $module);
//            } else if (!in_array($iloc->mod, $this->deprecated_modules)) {
//                // add old school modules not in the deprecation list
////				if ($iloc->mod == 'calendarmodule' && $module->view == 'Upcoming Events - Summary') {
////					$module->view = 'Upcoming Events - Headlines';
////				}
//				$linked = $this->pulldata($iloc, $module);
//				if ($linked) {
//					$newmodule['i_mod'] = $iloc->mod;
//					$newmodule['modcntrol'] = $iloc->mod;
//					$newmodule['rank'] = $module->rank;
//					$newmodule['views'] = $module->view;
//					$newmodule['title'] = $module->title;
//					$newmodule['actions'] = '';
//                    $section = $old_db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."' AND is_original='0'");
//                    $_POST['current_section'] = empty($section->section) ? 1 : $section->section;
//					$module = container::update($newmodule,$module,expUnserialize($module->external));
////                    if ($iloc->mod == 'calendarmodule') {
////                        $config = $old_db->selectObject('calendarmodule_config', "location_data='".serialize($iloc)."'");
////                        $config->id = '';
////                        $config->enable_categories = 1;
////                        $config->enable_tags = 0;
////                        $config->location_data = $module->internal;
////                        $config->aggregate = serialize(Array($iloc->src));
////                        $db->insertObject($config, 'calendarmodule_config');
////                    }
//				}
//				$res = $db->insertObject($module, 'container');
//				if ($res) { @$this->msg['container']++; }
            }
        }

		if (isset($this->params['copy_permissions'])) {
			$db->delete('userpermission',"module != 'navigation'");
			$db->delete('grouppermission',"module != 'navigation'");

			$users = $db->selectObjects('user','id > 1');
			foreach($users as $user) {
				$containers = $old_db->selectObjects('userpermission',"uid='".$user->id."' AND source != ''");
				foreach($containers as $item) {
                    $loc = expCore::makeLocation($item->module,$item->source);
					if (array_key_exists($item->module, $this->new_modules)) {
						$loc->mod = $this->new_modules[$item->module];
						$item->module = $this->new_modules[$item->module];
                        $item = $this->convert_permission($item);
                    }
					if ($item && $db->selectObject('container',"internal = '".serialize($loc)."'")) {
						$db->insertObject($item,'userpermission');
						if ($item->permission == 'edit') {  // if they had edit permission, we'll also give them create permission
							$item->permission = 'create';
							@$db->insertObject($item,'userpermission');
						}
					}
				}
			}
			$groups = $db->selectObjects('group','1');
			foreach($groups as $group) {
				$containers = $old_db->selectObjects('grouppermission',"gid='".$group->id."' AND source != ''");
				foreach($containers as $item) {
                    $loc = expCore::makeLocation($loc->mod = $item->module,$item->source);
					if (array_key_exists($item->module, $this->new_modules)) {
						$loc->mod = $this->new_modules[$item->module];
						$item->module = $this->new_modules[$item->module];
						$item = $this->convert_permission($item);
					}
					if ($item && $db->selectObject('container',"internal = '".serialize($loc)."'")) {
						$db->insertObject($item,'grouppermission');
						if ($item->permission == 'edit') {  // if they had edit permission, we'll also give them create permission
							$item->permission = 'create';
							@$db->insertObject($item,'grouppermission');
						}
					}
				}
			}
		}

        // migrate the active controller list (modstate)
        $activemods = $old_db->selectObjects('modstate',1);
        foreach($activemods as $mod) {
            if (array_key_exists($mod->module, $this->new_modules)) {
                $mod->module = $this->new_modules[$mod->module];
            }
            if (array_key_exists($mod->module, $this->new_modules) || !in_array($mod->module, $this->deprecated_modules)) {
//                $mod->path = '';
//                $mod->user_runnable = 1;
//                $mod->controller = 1;
//                $mod->os_module = 1;
//                $mod->name = '';
//                $mod->author = '';
//                $mod->description = '';
//                $mod->codequality = '';
                if ($db->selectObject('modstate',"module='".$mod->module."'")) {
                    $db->updateObject($mod,'modstate',null,'module');
                } else {
                    $db->insertObject($mod,'modstate');
                }
            }
        }

		searchController::spider();
        expSession::clearCurrentUserSessionCache();
        assign_to_template(array(
            'msg'=>@$this->msg
        ));
    }

	/**
	 * gather info about all users/groups in old site for user selection
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
	public function manage_users() {
        global $db;

        expHistory::set('manageable', $this->params);
        $old_db = $this->connect();
        $users = $old_db->selectObjects('user','id > 1');
        foreach($users as $user) {
			if ($db->selectObject('user',"id='".$user->id."'")) {
				$user->exists = true;
			} else {
				$user->exists = false;
			}
		}

        $groups = $old_db->selectObjects('group');
        foreach($groups as $group) {
			if ($db->selectObject('group',"id='".$group->id."'")) {
				$group->exists = true;
			} else {
				$group->exists = false;
			}
		}
		assign_to_template(array(
            'users'=>$users,
            'groups'=>$groups
        ));
    }

	/**
	 * copy selected users/groups over from old site
	 * @var \mysqli_database $db the exponent database object
	 * @return void
	 */
    public function migrate_users() {
        global $db;

		if (isset($this->params['wipe_groups'])) {
			$db->delete('group');
			$db->delete('groupmembership');
		}
		if (isset($this->params['wipe_users'])) {
			$db->delete('user','id > 1');
		}
        $old_db = $this->connect();
//		print_r("<pre>");
//		print_r($old_db->selectAndJoinObjects('', '', 'group', 'groupmembership','id', 'group_id', 'name = "Editors"', ''));

        $gsuccessful = 0;
        $gfailed     = 0;
		if (!empty($this->params['groups'])) {
			foreach($this->params['groups'] as $groupid) {
				$group = $old_db->selectObject('group', 'id='.$groupid);
				$ret = $db->insertObject($group, 'group');
				if (empty($ret)) {
					$gfailed++;
				} else {
					$gsuccessful++;
				}				
			}
		}
		if (!empty($this->params['rep_groups'])) {
			foreach($this->params['rep_groups'] as $groupid) {
				$db->delete('group','id='.$groupid);
				$group = $old_db->selectObject('group', 'id='.$groupid);
				$ret = $db->insertObject($group, 'group');
				if (empty($ret)) {
					$gfailed++;
				} else {
					$gsuccessful++;
				}				
			}
		}
		
        $successful = 0;
        $failed     = 0;
		if (!empty($this->params['users'])) {
			foreach($this->params['users'] as $userid) {
				$user = $old_db->selectObject('user', 'id='.$userid);
				$ret = $db->insertObject($user, 'user');
				if (empty($ret)) {
					$failed++;
				} else {
					$successful++;
				}				
			}
		}
		if (!empty($this->params['rep_users'])) {
			foreach($this->params['rep_users'] as $userid) {
				$db->delete('user','id='.$userid);
				$user = $old_db->selectObject('user', 'id='.$userid);
				$ret = $db->insertObject($user, 'user');
				if (empty($ret)) {
					$failed++;
				} else {
					$successful++;
				}				
			}
		}
	    $users = new stdClass();
	    $groups = new stdClass();
		if (!empty($this->params['groups']) && !empty($this->params['rep_groups'])) {
			$groups = array_merge($this->params['groups'],$this->params['rep_groups']);
		} elseif (!empty($this->params['groups'])) {
			$groups = $this->params['groups'];
		}  elseif (!empty($this->params['rep_groups']))  {
			$groups = $this->params['rep_groups'];
		}
		if (!empty($this->params['users']) && !empty($this->params['rep_users'])) {
			$users = array_merge($this->params['users'],$this->params['rep_users']);
		} elseif (!empty($this->params['users'])) {
			$users = $this->params['users'];
		}  elseif (!empty($this->params['rep_users']))  {
			$users = $this->params['rep_users'];
		}
		if (!empty($groups) && !empty($users)) {
			foreach($groups as $groupid) {
				$groupmembers = $old_db->selectObjects('groupmembership', 'group_id='.$groupid);
				foreach($groupmembers as $userid) {
					if (in_array($userid->member_id,$users)) {
						$db->insertObject($userid, 'groupmembership');
					}
				}
			}
		}
		
        flash('message', $successful.' '.gt('users and').' '.$gsuccessful.' '.gt('groups were imported from').' '.$this->config['database']);
        if ($failed > 0 || $gfailed > 0) {
			$msg = '';
			if ($failed > 0) {
				$msg = $failed.' users ';
			}
			if ($gfailed > 0) {
				if ($msg != '') { $msg .= ' and ';}
				$msg .= $gfailed.' groups ';
			}
            flash('error', $msg.' '.gt('could not be imported from').' '.$this->config['database'].' '.gt('This is usually because a user with the username or group with that name already exists in the database you importing to.'));
        }
        expSession::clearCurrentUserSessionCache();
        expHistory::back();
    }

	/**
	 * main routine to convert old school module data into new controller format
	 * @var \mysqli_database $db the exponent database object
	 * @param  $iloc
	 * @param  $module
	 * @param int $hc
	 * @return
	 */
    private function convert($iloc, $module, $hc=0) {
        if (!in_array($iloc->mod, $this->params['migrate'])) return $module;
        global $db;
        $old_db = $this->connect();
		$linked = false;
	    $loc = new stdClass();
        $newconfig = new expConfig();
        if ((!empty($module->is_existing) && $module->is_existing)) {
            $linked = true;
        }

        switch ($iloc->mod) {
            case 'textmodule':
				@$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = clone($iloc);
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'textmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'textmodule';
                $textitems = $old_db->selectObjects('textitem', "location_data='".serialize($iloc)."'");
                if ($textitems) {
                    foreach ($textitems as $ti) {
                        $text = new text();
                        $loc = expUnserialize($ti->location_data);
                        $loc->mod = "text";
                        $text->location_data = serialize($loc);
                        $text->body = $ti->text;
                        $text->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                    }
                }
				break;
            case 'rotatormodule':
                $module->action = 'showRandom';
                $module->view = 'showRandom';

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'rotatormodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'rotatormodule';
                $textitems = $old_db->selectObjects('rotator_item', "location_data='".serialize($iloc)."'");
                if ($textitems) {
                    foreach ($textitems as $ti) {
                        $text = new text();
                        $loc = expUnserialize($ti->location_data);
                        $loc->mod = "text";
                        $text->location_data = serialize($loc);
                        $text->body = $ti->text;
                        $text->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                    }
                }
				break;
            case 'snippetmodule':
				$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "snippet";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'snippetmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'snippetmodule';
                $textitems = $old_db->selectObjects('textitem', "location_data='".serialize($iloc)."'");
                if ($textitems) {
                    foreach ($textitems as $ti) {
                        $text = new snippet();
                        $loc = expUnserialize($ti->location_data);
                        $loc->mod = "snippet";
                        $text->location_data = serialize($loc);
                        $text->body = $ti->text;
                        // if the item exists in the current db, we won't save it
                        $te = $text->find('first',"location_data='".$text->location_data."'");
                        if (empty($te)) {
                            $text->save();
                            @$this->msg['migrated'][$iloc->mod]['count']++;
                            @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        }
                    }
                }
				break;
            case 'linklistmodule':
				switch ($module->view) {
					case 'Quick Links':
						@$module->view = "showall_quicklinks";
						break;
					default:
						@$module->view = 'showall';
						break;
				}

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "links";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'linklistmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'linklistmodule';
                $links = $old_db->selectArrays('linklist_link', "location_data='".serialize($iloc)."'");
				if ($links) {
					foreach ($links as $link) {
						$lnk = new links();
						$loc = expUnserialize($link['location_data']);
						$loc->mod = "links";
						$lnk->title = (!empty($link['name'])) ? $link['name'] : 'Untitled';
						$lnk->body = $link['description'];
						$lnk->new_window = $link['opennew'];
						$lnk->url = (!empty($link['url'])) ? $link['url'] : '#';
						$lnk->rank = $link['rank']+1;
						$lnk->poster = 1;
						$lnk->editor = 1;
						$lnk->location_data = serialize($loc);
						$lnk->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            case 'linkmodule':  // user mod, not widely distributed
				switch ($module->view) {
					case 'Summary':
						@$module->view = "showall_quicklinks";
						break;
					default:
						@$module->view = 'showall';
						break;
				}

                $oldconfig = $old_db->selectObject('linkmodule_config', "location_data='".serialize($iloc)."'");
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_rss == 1) {
                        $newconfig->config['enable_rss'] = true;
                        $newconfig->config['advertise'] = true;
                        $newconfig->config['feed_title'] = $oldconfig->feed_title;
                        $newconfig->config['feed_desc'] = $oldconfig->feed_desc;
                        $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                        $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                    }
                    if (!empty($oldconfig->orderhow)) {
                        if ($oldconfig->orderby == 'name') $newconfig->config['order'] = 'title';
                        switch ($oldconfig->orderhow) {
                            case '1':
                                $newconfig->config['order'] .= ' DESC';
                                break;
                            case '2':
                                $newconfig->config['order'] = 'rank';
                                break;
                        }
                    }
                    if ($oldconfig->enable_categories == 1) {
                        $newconfig->config['usecategories'] = true;
                    }
                }

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "links";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'linkmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'linkmodule';
                $links = $old_db->selectArrays('link', "location_data='".serialize($iloc)."'");
				if ($links) {
					foreach ($links as $link) {
						$lnk = new links();
						$loc = expUnserialize($link['location_data']);
						$loc->mod = "links";
						$lnk->title = (!empty($link['name'])) ? $link['name'] : 'Untitled';
						$lnk->body = $link['description'];
						$lnk->new_window = $link['opennew'];
						$lnk->url = (!empty($link['url'])) ? $link['url'] : '#';
						$lnk->rank = $link['rank']+1;
						$lnk->poster = 1;
						$lnk->editor = 1;
						$lnk->location_data = serialize($loc);
						$lnk->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($oldconfig) && $oldconfig->enable_categories == 1 && $link['category_id']) {
                            $params = null;
                            $oldcat = $old_db->selectObject('category','id = '.$link['category_id']);
                            $cat = new expCat($oldcat->name);
                            if (empty($cat->id)) {
                                $cat->title = $oldcat->name;
                                $cat->color = $oldcat->color;
                                $catloc = expUnserialize($oldcat->location_data);
                                if (array_key_exists($catloc->mod, $this->new_modules)) {
                                    $mod = expModules::getModuleName($this->new_modules[$catloc->mod]);
                                    $cat->module = $mod;
                                }
                                $cat->save();
                                $cat->rank = $oldcat->rank + 1;
                                $cat->update();
                            }
                            $params['expCat'][] = $cat->id;
                            $lnk->update($params);
                        }
					}
				}
				break;
            case 'swfmodule':
				$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'swfmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'swfmodule';
                $swfitems = $old_db->selectObjects('swfitem', "location_data='".serialize($iloc)."'");
				if ($swfitems) {
					foreach ($swfitems as $ti) {
						$text = new text();
						$file = new expFile($ti->swf_id);
						$loc = expUnserialize($ti->location_data);
						$loc->mod = "text";
						$text->location_data = serialize($loc);
						$text->title = $ti->name;
						$swfcode = '
							<p>
							 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" height="'.$ti->height.'" width="'.$ti->width.'">
								 <param name="bgcolor" value="'.$ti->bgcolor.'" />
									'.($ti->transparentbg?"<param name=\"wmode\" value=\"transparent\" />":"").'
								 <param name="quality" value="high" />
								 <param name="movie" value="'.$file->path_relative.'" />
								 <embed bgcolor= "'.$ti->bgcolor.'" pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'.$file->path_relative.'" type="application/x-shockwave-flash" height="'.$ti->height.'" width="'.$ti->width.'"'.($ti->transparentbg?" wmode=\"transparent\"":"").'>
								 </embed>
							 </object>
							</p>
						';
						$text->body = $swfcode;
						$text->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            case 'newsmodule':
                $only_featured = false;
                $usebody = 0;
				switch ($module->view) {
                    case 'Featured News':
                        $only_featured = true;
                        $module->view = 'showall';
                        break;
					case 'Headlines':
                        $usebody = 2;
						$module->view = 'showall_headlines';
						break;
					case 'Summary':
                    case 'Default':
                        $usebody = 1;
					default:
						$module->view = 'showall';
						break;
				}

                $oldconfig = $old_db->selectObject('newsmodule_config', "location_data='".serialize($iloc)."'");
                $oldviewconfig = expUnserialize($old_db->selectValue('container','view_data', "internal='".serialize($iloc)."'"));
                $ploc = clone($iloc);
                $ploc->mod = "news";
                // fudge a config to get attached files to appear
                $newconfig->config = expUnserialize('a:14:{s:9:"feedmaker";s:0:"";s:11:"filedisplay";s:7:"Gallery";s:6:"ffloat";s:4:"Left";s:6:"fwidth";s:3:"120";s:7:"fmargin";s:1:"5";s:7:"piwidth";s:3:"100";s:5:"thumb";s:3:"100";s:7:"spacing";s:2:"10";s:10:"floatthumb";s:8:"No Float";s:6:"tclass";s:0:"";s:5:"limit";s:0:"";s:9:"pagelinks";s:14:"Top and Bottom";s:10:"feed_title";s:0:"";s:9:"feed_desc";s:0:"";}');
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_rss == 1) {
                        $newconfig->config['enable_rss'] = true;
                        $newconfig->config['advertise'] = true;
                        $newconfig->config['feed_title'] = $oldconfig->feed_title;
                        $newconfig->config['feed_desc'] = $oldconfig->feed_desc;
                        $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                        $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                    }
                    if (!empty($oldconfig->item_limit)) {
                        $newconfig->config['limit'] = $oldconfig->item_limit;
                        $newconfig->config['multipageonly'] = true;
                    }
                    if (!empty($oldconfig->sortfield)) {
                        switch ($oldconfig->sortfield) {
                            case 'publish':
                                $newconfig->config['order'] = 'publish';
                                break;
                            case 'edited':
                                $newconfig->config['order'] = 'edited_at';
                                break;
                            case 'posted':
                            default:
                                $newconfig->config['order'] = 'created_at';
                                break;
                        }
                        if ($oldconfig->sortorder == 'DESC') {
                            $newconfig->config['order'] .= ' DESC';
                        }
                    }
                    if (!empty($oldconfig->aggregate) && $oldconfig->aggregate != 'a:0:{}') {
                        $merged = expUnserialize($oldconfig->aggregate);
                        foreach ($merged as $merge) {
                            $newconfig->config['aggregate'][] = $merge;
                        }
                    }
                    if (!empty($oldconfig->pull_rss) && $oldconfig->pull_rss) {
                        $pulled = expUnserialize($oldconfig->rss_feed);
                        foreach ($pulled as $pull) {
                            $newconfig->config['pull_rss'][] = $pull;
                        }
                    }
                }
                if ($usebody) {
                    $newconfig->config['usebody'] = $usebody;
                }
                if (!empty($oldviewconfig['num_items'])) {
                    $newconfig->config['limit'] = $oldviewconfig['num_items'];
//                    $newconfig->config['pagelinks'] = "Don't show page links";
                }
                $only_featured = empty($oldviewconfig['featured_only']) ? 0 : 1;
                if ($only_featured) {
                    $newconfig->config['only_featured'] = true;
                }

				//check to see if it's already pulled in (circumvent !is_original)
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'newsmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'newsmodule';
                $newsitems = $old_db->selectArrays('newsitem', "location_data='".serialize($iloc)."'");
                if ($newsitems) {
                    foreach ($newsitems as $ni) {
                        unset($ni['id']);
                        $news = new news($ni);
                        $loc = expUnserialize($ni['location_data']);
                        $loc->mod = "news";
                        $news->location_data = serialize($loc);
                        $news->title = (!empty($ni['title'])) ? $ni['title'] : gt('Untitled');
                        $news->body = (!empty($ni['body'])) ? $ni['body'] : gt('(empty)');
                        $news->save();
						// default is to create with current time
                        $news->created_at = $ni['posted'];
                        $news->migrated_at = $ni['edited'];
                        $news->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($ni['file_id'])) {
                            $file = new expFile($ni['file_id']);
                            $news->attachItem($file,'');
                        }
                        if (isset($oldconfig->enable_tags) && $oldconfig->enable_tags = true) {
	                        $params = null;;
							$oldtags = expUnserialize($ni['tags']);
                            if (!empty($oldtags)) {
                                foreach ($oldtags as $oldtag){
                                    $tagtitle = strtolower(trim($old_db->selectValue('tags','name','id = '.$oldtag)));
                                    $tag = new expTag($tagtitle);
    //								$tag->title = $old_db->selectValue('tags','name','id = '.$oldtag);
                                    if (empty($tag->id))
                                        $tag->update(array('title'=>$tagtitle));
                                    $params['expTag'][] = $tag->id;
                                }
                            }
                            $news->update($params);
                        }
                    }
                }
				break;
            case 'resourcesmodule':
				switch ($module->view) {
					case 'One Click Download - Descriptive':
						$module->view = 'showall_headlines';
						break;
                    case 'Recent':
                        $module->view = 'showall_recent';
                        $newconfig->config['usebody'] = 2;
                        break;
					default:
						$module->view = 'showall';
						break;
				}

                $oldconfig = $old_db->selectObject('resourcesmodule_config', "location_data='".serialize($iloc)."'");
                $oldviewconfig = expUnserialize($old_db->selectValue('container','view_data', "internal='".serialize($iloc)."'"));
                $ploc = clone($iloc);
                $ploc->mod = "filedownload";
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_categories == 1 && $module->view != 'showall_recent') {
                        $newconfig->config['usecategories'] = true;
                    }
                    if (!empty($oldconfig->description)) {
                        $newconfig->config['moduledescription'] = $oldconfig->description;
                    }
                    if (isset($oldconfig->enable_rss)) {
                        $dorss = $oldconfig->enable_rss;
                    } elseif (isset($oldconfig->enable_podcasting)) {
                        $dorss = $oldconfig->enable_podcasting;
                    } else {
                        $dorss = false;
                    }
                    if ($dorss) {
                        $newconfig->config['enable_rss'] = true;
                        $newconfig->config['advertise'] = true;
                        $newconfig->config['feed_title'] = $oldconfig->feed_title;
                        $newconfig->config['feed_desc'] = $oldconfig->feed_desc;
                        $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                        $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                    }
                    if (!empty($oldconfig->orderhow)) {
                        switch ($oldconfig->orderby) {
                            case 'edited':
                                $newconfig->config['order'] = 'edited_at';
                                break;
                            case 'downloads':
                                $newconfig->config['order'] = 'downloads';
                                break;
                            case 'name':
                                $newconfig->config['order'] = 'title';
                                break;
                            case 'posted':
                            default:
                                $newconfig->config['order'] = 'created_at';
                                break;
                        }
                        switch ($oldconfig->orderhow) {
                            case '2':
                                $newconfig->config['order'] = 'rank';
                            break;
                            case '1':
                                $newconfig->config['order'] .= ' DESC';
                            break;
                        }
                    }
                }
                if (!empty($oldviewconfig['num_posts'])) {
                    $newconfig->config['limit'] = $oldviewconfig['num_posts'];
//                    $newconfig->config['pagelinks'] = "Don't show page links";
                }
                $newconfig->config['usebody'] = 2;
                if (!empty($oldviewconfig['show_descriptions'])) {
                    $newconfig->config['show_info'] = $oldviewconfig['show_descriptions'] ? 1 : 0;
                    if ($oldviewconfig['show_descriptions']) {
                        $newconfig->config['usebody'] = 0;
                    }
                }
                $newconfig->config['quick_download'] = !empty($oldviewconfig['direct_download']) ? $oldviewconfig['direct_download'] : 0;
                $newconfig->config['show_icon'] = !empty($oldviewconfig['show_icons']) ? $oldviewconfig['show_icons'] : 0;
                $newconfig->config['show_player'] = !empty($oldviewconfig['show_player']) ? $oldviewconfig['show_player'] : 0;

				//check to see if it's already pulled in (circumvent !is_original)
//				if ($db->countObjects('filedownloads', "location_data='".serialize($ploc)."'")) {
                if ($db->countObjects('filedownload', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'resourcesmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'resourcesmodule';
                $resourceitems = $old_db->selectArrays('resourceitem', "location_data='".serialize($iloc)."'");
				if ($resourceitems) {
					foreach ($resourceitems as $ri) {
						unset($ri['id']);
						$filedownload = new filedownload($ri);
						$loc = expUnserialize($ri['location_data']);
						$loc->mod = "filedownload";
						$filedownload->title = (!empty($ri['name'])) ? $ri['name'] : 'Untitled';
						$filedownload->body = $ri['description'];
						$filedownload->downloads = $ri['num_downloads'];
						$filedownload->location_data = serialize($loc);
						if (!empty($ri['file_id'])) {
							$filedownload->save();
							@$this->msg['migrated'][$iloc->mod]['count']++;
							@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
							$file = new expFile($ri['file_id']);
							$filedownload->attachItem($file,'downloadable');
							// default is to create with current time						
							$filedownload->created_at = $ri['posted'];
							$filedownload->migrated_at = $ri['edited'];
                            $filedownload->publish = $ri['posted'];
							$filedownload->update();
                            if (!empty($oldconfig) && $oldconfig->enable_categories == 1 && $ri['category_id']) {
                                $params = null;
                                $oldcat = $old_db->selectObject('category','id = '.$ri['category_id']);
                                $cat = new expCat($oldcat->name);
                                if (empty($cat->id)) {
                                    $cat->title = $oldcat->name;
                                    $cat->color = $oldcat->color;
                                    $catloc = expUnserialize($oldcat->location_data);
                                    if (array_key_exists($catloc->mod, $this->new_modules)) {
                                        $mod = expModules::getModuleName($this->new_modules[$catloc->mod]);
                                        $cat->module = $mod;
                                    }
                                    $cat->save();
                                    $cat->rank = $oldcat->rank +1;
                                    $cat->update();
                                }
                                $params['expCat'][] = $cat->id;
                                $filedownload->update($params);
                            }
						}
					}
				}
				break;
            case 'imagegallerymodule':
				switch ($module->view) {
					case 'Slideshow':
						$module->action = 'slideshow';
						$module->view = 'slideshow';
						break;
					default:
						$module->view = 'showall';
						break;
				}

                $oldviewconfig = expUnserialize($old_db->selectValue('container','view_data', "internal='".serialize($iloc)."'"));
                $newconfig->config['usecategories'] = true;
                $newconfig->config['multipageonly'] = true;
                $newconfig->config['speed'] = empty($oldviewconfig['delay']) ? 0: $oldviewconfig['delay']/1000;
                $newconfig->config['pa_show_controls'] = empty($oldviewconfig['controller']) ? 0 : 1;

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "photo";
				if ($db->countObjects('photo', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'imagegallerymodule';
//					$linked = true;
					break;
				}

//				$iloc->mod = 'imagegallerymodule';
                $galleries = $old_db->selectArrays('imagegallery_gallery', "location_data='".serialize($iloc)."'");
				if ($galleries) {
					foreach ($galleries as $gallery) {
                        $params = null;;
                        $cat = new expCat($gallery['name']);
                        if (empty($cat->id)) {
                            $cat->title = $gallery['name'];
                            $cat->rank = $gallery['galleryorder']+1;
                            $cat->module = 'photo';
                            $cat->update();
                        }
                        $params['expCat'][] = $cat->id;
						$gis = $old_db->selectArrays('imagegallery_image', "gallery_id='".$gallery['id']."'");
						foreach ($gis as $gi) {
							$photo = new photo();
							$loc = expUnserialize($gallery['location_data']);
							$loc->mod = "photo";
							$photo->title = (!empty($gi['name'])) ? $gi['name'] : 'Untitled';
							$photo->body = $gi['description'];
							$photo->alt = !empty($gi['alt']) ? $gi['alt'] : $photo->title;
							$photo->location_data = serialize($loc);
							if (!empty($gi['file_id'])) {
								$photo->save();
								@$this->msg['migrated'][$iloc->mod]['count']++;
								@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
								$file = new expFile($gi['file_id']);
								$photo->attachItem($file,'');
								$photo->created_at = $gi['posted'];
								$photo->migrated_at = $gi['posted'];
								$photo->update(array("validate"=>false));								
                                $photo->update($params);  // save gallery name as category
							}
						}
					}
                    // pick up some module config settings based on last gallery
                    $newconfig->config['pa_showall_thumbbox'] = $gallery['box_size'];
                    $newconfig->config['pa_showall_enlarged'] = $gallery['pop_size'];
                    $newconfig->config['limit'] = $gallery['perpage'];
				}
				break;
            case 'slideshowmodule':
                $module->action = 'slideshow';
                $module->view = 'slideshow';

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "photo";
				if ($db->countObjects('photo', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'slideshowmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'slideshowmodule';
                $gis = $old_db->selectArrays('slideshow_slide', "location_data='".serialize($iloc)."'");
                if ($gis) {
                    foreach ($gis as $gi) {
                        $photo = new photo();
                        $loc->mod = "photo";
                        $loc->src = $iloc->src;
                        $loc->int = $iloc->int;
                        $photo->title = (!empty($gi['name'])) ? $gi['name'] : 'Untitled';
                        $photo->body = $gi['description'];
                        $photo->alt = !empty($gi['alt']) ? $gi['alt'] : $photo->title;
                        $photo->location_data = serialize($loc);
                        $te = $photo->find('first',"location_data='".$photo->location_data."'");
                        if (empty($te)) {
                            if (!empty($gi['file_id'])) {
                                $photo->save();
                                @$this->msg['migrated'][$iloc->mod]['count']++;
                                @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                                $file = new expFile($gi['file_id']);
                                $photo->attachItem($file,'');
                                $photo->update(array("validate"=>false));
                            }
                        }
                    }
                }
				break;
            case 'headlinemodule':
                $module->view = 'showall_headline';

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'headlinemodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'headlinemodule';
                $headlines = $old_db->selectObjects('headline', "location_data='".serialize($iloc)."'");
                if ($headlines) {
                    foreach ($headlines as $hl) {
                        $headline = new text();
                        $loc = expUnserialize($hl->location_data);
                        $loc->mod = "text";
                        $headline->location_data = serialize($loc);
                        $headline->title = $hl->headline;
                        $headline->poster = 1;
//                        $headline->created_at = time();
//                        $headline->migrated_at = time();
                        $headline->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                    }
                }
				break;
            case 'weblogmodule':
                $usebody = 0;
				switch ($module->view) {
					case 'By Author':
						$module->action = 'authors';
						$module->view = 'authors';
						break;
					case 'By Tag':
						$module->action = 'tags';
						$module->view = 'tags_list';
						break;
					case 'Monthly':
						$module->action = 'dates';
						$module->view = 'dates';
						break;
                    case 'Summary':
                        $usebody = 2;
						$module->view = 'showall';
						break;
                    case 'Default':
                        $usebody = 1;
					default:
						$module->view = 'showall';
						break;
				}

                $oldconfig = $old_db->selectObject('weblogmodule_config', "location_data='".serialize($iloc)."'");
                $oldviewconfig = expUnserialize($old_db->selectValue('container','view_data', "internal='".serialize($iloc)."'"));
                $ploc = clone($iloc);
                $ploc->mod = "blog";
                $newconfig->config['add_source'] = '1';
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_rss == 1) {
                        $newconfig->config['enable_rss'] = true;
                        $newconfig->config['advertise'] = true;
                        $newconfig->config['feed_title'] = $oldconfig->feed_title;
                        $newconfig->config['feed_desc'] = $oldconfig->feed_desc;
                        $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                        $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                    }
                    if (!empty($oldconfig->items_per_page)) {
                        $newconfig->config['limit'] = $oldconfig->items_per_page;
                        $newconfig->config['multipageonly'] = true;
                    }
                    if (!empty($oldviewconfig['num_posts'])) {
                        $newconfig->config['limit'] = $oldviewconfig['num_posts'];
    //                    $newconfig->config['pagelinks'] = "Don't show page links";
                    }
                    if (!empty($oldconfig->allow_comments)) {
                        $newconfig->config['usescomments'] = !$oldconfig->allow_comments;
                    }
                    if (!empty($oldconfig->aggregate) && $oldconfig->aggregate != 'a:0:{}') {
                        $merged = expUnserialize($oldconfig->aggregate);
                        foreach ($merged as $merge) {
                            $newconfig->config['aggregate'][] = $merge;
                        }
                    }
                }
                if ($usebody) {
                    $newconfig->config['usebody'] = $usebody;
                }

                //check to see if it's already pulled in (circumvent !is_original)
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'weblogmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'weblogmodule';
                $blogitems = $old_db->selectArrays('weblog_post', "location_data='".serialize($iloc)."'");
                if ($blogitems) {
                    foreach ($blogitems as $bi) {
                        unset($bi['id']);
                        $post = new blog($bi);
                        $loc = expUnserialize($bi['location_data']);
                        $loc->mod = "blog";
                        $post->location_data = serialize($loc);
                        $post->title = (!empty($bi['title'])) ? $bi['title'] : gt('Untitled');
                        $post->body = (!empty($bi['body'])) ? $bi['body'] : gt('(empty)');
                        $post->save();
						// default is to create with current time						
                        $post->created_at = $bi['posted'];
                        $post->migrated_at = $bi['edited'];
                        $post->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
						// this next section is moot since there are no attachments to blogs
                        // if (!empty($bi['file_id'])) {
                            // $file = new expFile($bi['file_id']);
                            // $post->attachItem($file,'downloadable');
                        // }

                        if (isset($oldconfig->enable_tags) && $oldconfig->enable_tags = true) {
	                        $params = null;;
							$oldtags = expUnserialize($bi['tags']);
							foreach ($oldtags as $oldtag){
								$tagtitle = strtolower(trim($old_db->selectValue('tags','name','id = '.$oldtag)));
								$tag = new expTag($tagtitle);
//								$tag->title = $old_db->selectValue('tags','name','id = '.$oldtag);
								if (empty($tag->id))
                                    $tag->update(array('title'=>$tagtitle));
								$params['expTag'][] = $tag->id;
							}
							$post->update($params);
                        }

						$comments = $old_db->selectArrays('weblog_comment', "parent_id='".$post->id."'");
						foreach($comments as $comment) {
							unset($comment['id']);
							$newcomment = new expComment($comment);
							$newcomment->created_at = $comment['posted'];
							$newcomment->migrated_at = $comment['edited'];
                            $newcomment->publish = $comment['posted'];
							$newcomment->update();
							// attach the comment to the blog post it belongs to
//                            $obj = new stdClass();
//							$obj->content_type = 'blog';
//							$obj->content_id = $post->id;
//							$obj->expcomments_id = $newcomment->id;
//							if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
//							$db->insertObject($obj, $newcomment->attachable_table);
                            $newcomment->attachComment('blog', $post->id);
						}
                    }
                }
				break;
            case 'faqmodule':
				$module->view = 'showall';

                $oldconfig = $old_db->selectObject('faqmodule_config', "location_data='".serialize($iloc)."'");
                if (!empty($oldconfig) && $oldconfig->enable_categories == 1) {
                    $newconfig->config['usecategories'] = true;
                }
                $newconfig->config['use_toc'] = true;

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "faq";
//				if ($db->countObjects('faqs', "location_data='".serialize($ploc)."'")) {
                if ($db->countObjects('faq', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'faqmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'faqmodule';
                $faqs = $old_db->selectArrays('faq', "location_data='".serialize($iloc)."'");
                if ($faqs) {
                    foreach ($faqs as $fqi) {
                        unset($fqi['id']);
                        $faq = new faq($fqi);
                        $loc = expUnserialize($fqi['location_data']);
                        $loc->mod = "faq";
                        $faq->location_data = serialize($loc);
                        $faq->question = (!empty($fqi['question'])) ? $fqi['question'] : 'Untitled?';
                        $faq->answer = $fqi['answer'];
                        $faq->rank = $fqi['rank']+1;
                        $faq->include_in_faq = 1;
                        $faq->submitter_name = 'Unknown';
                        $faq->submitter_email = 'address@website.com';
                        $faq->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($oldconfig) && $oldconfig->enable_categories == 1 && $fqi['category_id']) {
                            $params = null;
                            $oldcat = $old_db->selectObject('category','id = '.$fqi['category_id']);
                            $cat = new expCat($oldcat->name);
                            if (empty($cat->id)) {
                                $cat->title = $oldcat->name;
                                $cat->color = $oldcat->color;
                                $catloc = expUnserialize($oldcat->location_data);
                                if (array_key_exists($catloc->mod, $this->new_modules)) {
                                    $mod = expModules::getModuleName($this->new_modules[$catloc->mod]);
                                    $cat->module = $mod;
                                }
                                $cat->save();
                                $cat->rank = $oldcat->rank + 1;
                                $cat->update();
                            }
                            $params['expCat'][] = $cat->id;
                            $faq->update($params);
                        }
                    }
                }
				break;
            case 'listingmodule':
                $usebody = 0;
				switch ($module->view) {
					case 'Simple':
                        $module->view = 'showall_simple_list';
                        $usebody = 2;
						break;
                    case 'Default':
                        $usebody = 1;
                    case 'Full':
                        $module->view = 'showall';
					default:
						break;
				}

                $oldconfig = $old_db->selectObject('listingmodule_config', "location_data='".serialize($iloc)."'");
                // fudge a config to get attached files to appear
                $newconfig->config = expUnserialize('a:11:{s:11:"filedisplay";s:7:"Gallery";s:6:"ffloat";s:4:"Left";s:6:"fwidth";s:3:"120";s:7:"fmargin";s:1:"5";s:7:"piwidth";s:3:"100";s:5:"thumb";s:3:"100";s:7:"spacing";s:2:"10";s:10:"floatthumb";s:8:"No Float";s:6:"tclass";s:0:"";s:5:"limit";s:0:"";s:9:"pagelinks";s:14:"Top and Bottom";}');
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_categories == 1) {
                        $newconfig->config['usecategories'] = true;
                    }
                    if (!empty($oldconfig->items_perpage)) {
                        $newconfig->config['limit'] = $oldconfig->items_perpage;
                        $newconfig->config['multipageonly'] = true;
                    }
                    if (!empty($oldconfig->orderhow)) {
                        if ($oldconfig->orderby == 'name') $newconfig->config['order'] = 'title';
                        switch ($oldconfig->orderhow) {
                            case '1':
                                $newconfig->config['order'] .= ' DESC';
                                break;
                            case '2':
                                $newconfig->config['order'] = 'rank';
                                break;
                        }
                    }
                    if (!empty($oldconfig->description)) {
                        $newconfig->config['moduledescription'] = $oldconfig->description;
                    }
                }
                if ($usebody) {
                    $newconfig->config['usebody'] = $usebody;
                }

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "portfolio";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'listingmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'listingmodule';
                $listingitems = $old_db->selectArrays('listing', "location_data='".serialize($iloc)."'");
                if ($listingitems) {
                    foreach ($listingitems as $li) {
                        unset($li['id']);
                        $listing = new portfolio($li);
						$listing->title = (!empty($li['name'])) ? $li['name'] : 'Untitled?';
                        $loc = expUnserialize($li['location_data']);
                        $loc->mod = "portfolio";
                        $listing->location_data = serialize($loc);
                        $listing->featured = true;
                        $listing->poster = 1;
                        $listing->body = "<p>".$li['summary']."</p>".$li['body'];
                        $listing->save();
						// default is to create with current time						
//                        $listing->created_at = time();
//                        $listing->migrated_at = time();
//                        $listing->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($li['file_id'])) {
							$file = new expFile($li['file_id']);
							$listing->attachItem($file,'');
						}
                        if (!empty($oldconfig) && $oldconfig->enable_categories == 1 && $li['category_id']) {
                            $params = null;
                            $oldcat = $old_db->selectObject('category','id = '.$li['category_id']);
                            $cat = new expCat($oldcat->name);
                            if (empty($cat->id)) {
                                $cat->title = $oldcat->name;
                                $cat->color = $oldcat->color;
                                $catloc = expUnserialize($oldcat->location_data);
                                if (array_key_exists($catloc->mod, $this->new_modules)) {
                                    $mod = expModules::getModuleName($this->new_modules[$catloc->mod]);
                                    $cat->module = $mod;
                                }
                                $cat->save();
                                $cat->rank = $oldcat->rank + 1;
                                $cat->update();
                            }
                            $params['expCat'][] = $cat->id;
                            $listing->update($params);
                        }
                    }
                }
				break;
            case 'youtubemodule':  //must convert to media player
				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "media";
				if ($db->countObjects('media', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'youtubemodule';
//					$linked = true;
					break;
				}

//				$iloc->mod = 'youtubemodule';
                $videos = $old_db->selectArrays('youtube', "location_data='".serialize($iloc)."'");
				if ($videos) {
					foreach ($videos as $vi) {
						unset ($vi['id']);
						$video = new media($vi);
						$loc = expUnserialize($vi['location_data']);
						$loc->mod = "media";
						$video->title = $vi['name'];
						if (empty($video->title)) { $video->title = 'Untitled'; }
						$video->location_data = serialize($loc);
                        $video->body = $vi['description'];
//						$yt = explode("watch?v=",$vi['url']);
//						if (empty($yt[1])) {
//							break;
//						} else {
//							$ytid = $yt[1];
//						}
//						unset ($video->url);
//						$video->embed_code = '<iframe title="YouTube video player" width="'.$vi['width'].'" height="'.$vi['height'].'" src="http://www.youtube.com/embed/'.$ytid.'" frameborder="0" allowfullscreen></iframe>';
						$video->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            case 'mediaplayermodule':  // must convert media player
				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "media";
				if ($db->countObjects('media', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'mediaplayermodule';
//					$linked = true;
					break;
				}

//				$iloc->mod = 'mediaplayermodule';
                $movies = $old_db->selectArrays('mediaitem', "location_data='".serialize($iloc)."'");
				if ($movies) {
					foreach ($movies as $mi) {
						unset ($mi['id']);
						$movie = new media($mi);
						$loc = expUnserialize($mi['location_data']);
						$loc->mod = "media";
						$movie->title = $mi['name'];
						if (empty($movie->title)) { $movie->title = 'Untitled'; }
                        $movie->body = $mi['description'];
						unset (
                            $mi['bgcolor'],
                            $mi['alignment'],
                            $mi['loop_media'],
                            $mi['auto_rewind'],
                            $mi['autoplay'],
                            $mi['hide_controls']
                        );
						$movie->location_data = serialize($loc);
						$movie->poster = 1;
						$movie->rank = 1;
						if (!empty($mi['media_id'])) {
							$movie->save();
							@$this->msg['migrated'][$iloc->mod]['count']++;
							@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
							$file = new expFile($mi['media_id']);
							$movie->attachItem($file,'files');
							if (!empty($mi['alt_image_id'])) {
								$file = new expFile($mi['alt_image_id']);
								$movie->attachItem($file,'splash');
							}
						}
					}
				}
				break;
            case 'bannermodule':
				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "banner";
				if ($db->countObjects('banner', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'bannermodule';
//					$linked = true;
					break;
				}

//				$iloc->mod = 'bannermodule';
                $banners = $old_db->selectArrays('banner_ad', "location_data='".serialize($iloc)."'");
				if ($banners) {
					foreach ($banners as $bi) {
						$oldclicks = $old_db->selectObjects('banner_click', "ad_id='".$bi['id']."'");
						$oldcompany = $old_db->selectObject('banner_affiliate', "id='".$bi['affiliate_id']."'");
						unset ($bi['id']);
						$banner = new banner($bi);
						$loc = expUnserialize($bi['location_data']);
						$loc->mod = "banner";
						$banner->title = $bi['name'];
						$banner->url = (!empty($bi['url'])) ? $bi['url'] : '#';
						if (empty($banner->title)) { $banner->title = 'Untitled'; }
						$banner->location_data = serialize($loc);
						$newcompany = $db->selectObject('companies', "title='".$oldcompany->name."'");
						if ($newcompany == null) {
							$newcompany = new company();
							$newcompany->title = (!empty($oldcompany->name)) ? $oldcompany->name : 'Untitled';
							$newcompany->body = $oldcompany->contact_info;
							$newcompany->location_data = $banner->location_data;
							$newcompany->save();
						}						
						$banner->companies_id = $newcompany->id;
						$banner->clicks = 0;
						foreach($oldclicks as $click) {
							$banner->clicks += $click->clicks;
						}
                        if (!empty($bi['file_id'])) {
                            $file = new expFile($bi['file_id']);
                            $banner->attachItem($file,'');
                        }
						$banner->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
//            case 'addressbookmodule':  // user mod, not widely distributed
//
//				@$module->view = 'myaddressbook';
//				@$module->action = 'myaddressbook';
//
//				//check to see if it's already pulled in (circumvent !is_original)
//				// $ploc = $iloc;
//				// $ploc->mod = "addresses";
//				// if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
//					// $iloc->mod = 'addressbookmodule';
//					// $linked = true;
//					// break;
//				// }
//
////                $iloc->mod = 'addressbookmodule';
//                $addresses = $old_db->selectArrays('addressbook_contact', "location_data='".serialize($iloc)."'");
//				if ($addresses) {
//					foreach ($addresses as $address) {
////						unset($address['id']);
//						$addr = new address();
//						$addr->user_id = 1;
//						$addr->is_default = 1;
//						$addr->is_billing = 1;
//						$addr->is_shipping = 1;
//						$addr->firstname = (!empty($address['firstname'])) ? $address['firstname'] : 'blank';
//						$addr->lastname = (!empty($address['lastname'])) ? $address['lastname'] : 'blank';
//						$addr->address1 = (!empty($address['address1'])) ? $address['address1'] : 'blank';
//						$addr->city = (!empty($address['city'])) ? $address['city'] : 'blank';
//						$address['state'] = (!empty($address['state'])) ? $address['state'] : 'CA';
//						$state = $db->selectObject('geo_region', 'code="'.strtoupper($address['state']).'"');
//						$addr->state = empty($state->id) ? 0 : $state->id;
//						$addr->zip = (!empty($address['zip'])) ? $address['zip'] : '99999';
//						$addr->phone = (!empty($address['phone'])) ? $address['phone'] : '800-555-1212';
//						$addr->email = (!empty($address['email'])) ? $address['email'] : 'address@website.com';
//						$addr->organization = $address['business'];
//						$addr->phone2 = $address['cell'];
//						$addr->save();
//						@$this->msg['migrated'][$iloc->mod]['count']++;
//						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
//					}
//				}
//				break;
            case 'feedlistmodule':
				@$module->view = 'showall';

//                $iloc->mod = 'feedlistmodule';
                $feedlist = $old_db->selectObject('feedlistmodule_config', "location_data='".serialize($iloc)."'");
                if ($feedlist->enable_rss == 1) {
					$loc = expUnserialize($feedlist->location_data);
					$loc->mod = "rss";
					$newconfig->config['enable_rss'] = true;
                    $newconfig->config['advertise'] = true;
					$newconfig->config['feed_title'] = $feedlist->feed_title;
					$newconfig->config['feed_desc'] = $feedlist->feed_desc;
					$newconfig->config['rss_limit'] = isset($feedlist->rss_limit) ? $feedlist->rss_limit : 24;
					$newconfig->config['rss_cachetime'] = isset($feedlist->rss_cachetime) ? $feedlist->rss_cachetime : 1440;
					$newconfig->location_data = $loc;
//					$newconfig->save();
					@$this->msg['migrated'][$iloc->mod]['count']++;
					@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                }
				break;
            case 'simplepollmodule':  // added v2.0.9
                $oldconfig = $old_db->selectObject('simplepollmodule_config', "location_data='".serialize($iloc)."'");
                if (!empty($oldconfig)) {
                    if (!empty($oldconfig->thank_you_message)) {
                        $newconfig->config['thank_you_message'] = 'Thank you for voting.';
                    }
                    if (!empty($oldconfig->already_voted_message)) {
                        $newconfig->config['already_voted_message'] = 'You have already voted in this poll.';
                    }
                    if (!empty($oldconfig->voting_closed_message)) {
                        $newconfig->config['voting_closed_message'] = 'Voting has been closed for this poll.';
                    }
                    if (!empty($oldconfig->anonymous_timeout)) {
                        $newconfig->config['anonymous_timeout'] = '5';
                    }
                }

				//check to see if it's already pulled in (circumvent !is_original)
                $ploc = clone($iloc);
				$ploc->mod = "simplePoll";
				if ($db->countObjects('simplepoll_question', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'simplepollmodule';
//					$linked = true;
					break;
				}

//				$iloc->mod = 'simplepollmodule';
                $oldquestions = $old_db->selectArrays('poll_question', "location_data='".serialize($iloc)."'");
				if ($oldquestions) {
					foreach ($oldquestions as $qi) {
						$oldanswers = $old_db->selectArrays('poll_answer', "question_id='".$qi['id']."'");
						$oldblocks = $old_db->selectArrays('poll_timeblock', "question_id='".$qi['id']."'");
						unset ($qi['id']);
                        $active = $qi['is_active'];
                        unset ($qi['is_active']);
						$question = new simplepoll_question($qi);
						$loc = expUnserialize($qi['location_data']);
						$loc->mod = "simplePoll";
                        $question->active = $active;
						if (empty($question->question)) { $question->question = 'Untitled'; }
                        $question->location_data = serialize($loc);
                        $question->save();

                        foreach ($oldanswers as $oi) {
                            unset (
                                $oi['id'],
                                $oi['question_id']
                            );
                            $newanswer = new simplepoll_answer($oi);
                            $newanswer->simplepoll_question_id = $question->id;
//                            $question->simplepoll_answer[] = $newanswer;
                            $newanswer->update();
                        }
//                        $question->update();

						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            case 'navigationmodule':  // added v2.0.9
                if (!empty($module->view)) {
                    if ($module->view == 'Breadcrumb') {
                        @$module->view = 'breadcrumb';
                        @$module->action = 'breadcrumb';
                    } else {
                        @$module->view = 'showall_'.$module->view;
                    }
                    @$this->msg['migrated'][$iloc->mod]['count']++;
                    @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                }
				break;
            case 'calendarmodule':  // added v2.1.0
                if ($module->view == 'Default') {
                    @$module->view = 'showall';
                } elseif ($module->view == 'Upcoming Events - Summary') {
                    $module->view = 'showall_Upcoming Events - Headlines';
                } else {
                    @$module->view = 'showall_'.$module->view;
                }
                $oldconfig = $old_db->selectObject('calendarmodule_config', "location_data='".serialize($iloc)."'");
                if (!empty($oldconfig)) {
                    if ($oldconfig->enable_ical == 1) {
                        $newconfig->config['enable_ical'] = true;
                        $newconfig->config['feed_title'] = $oldconfig->feed_title;
                        $newconfig->config['rss_limit'] = isset($oldconfig->rss_limit) ? $oldconfig->rss_limit : 24;
                        $newconfig->config['rss_cachetime'] = isset($oldconfig->rss_cachetime) ? $oldconfig->rss_cachetime : 1440;
                    }
                    if (!empty($oldconfig->hidemoduletitle)) {
                        $newconfig->config['hidemoduletitle'] = $oldconfig->hidemoduletitle;
                    }
                    if (!empty($oldconfig->moduledescription)) {
                        $newconfig->config['moduledescription'] = $oldconfig->moduledescription;
                    }
                    if (!empty($oldconfig->aggregate) && $oldconfig->aggregate != 'a:0:{}') {
                        $merged = expUnserialize($oldconfig->aggregate);
                        foreach ($merged as $merge) {
                            $newconfig->config['aggregate'][] = $merge;
                        }
                    }
                    if (!empty($oldconfig->enable_feedback)) {
                        $newconfig->config['enable_feedback'] = $oldconfig->enable_feedback;
                    }
                    if (!empty($oldconfig->email_title_reminder)) {
                        $newconfig->config['email_title_reminder'] = $oldconfig->email_title_reminder;
                    }
                    if (!empty($oldconfig->email_from_reminder)) {
                        $newconfig->config['email_from_reminder'] = $oldconfig->email_from_reminder;
                    }
                    if (!empty($oldconfig->email_address_reminder)) {
                        $newconfig->config['email_address_reminder'] = $oldconfig->email_address_reminder;
                    }
                    if (!empty($oldconfig->email_reply_reminder)) {
                        $newconfig->config['email_reply_reminder'] = $oldconfig->email_reply_reminder;
                    }
                    if (!empty($oldconfig->email_showdetail)) {
                        $newconfig->config['email_showdetail'] = $oldconfig->email_showdetail;
                    }
                    if (!empty($oldconfig->email_signature)) {
                        $newconfig->config['email_signature'] = $oldconfig->email_signature;
                    }
                    if (empty($oldconfig->enable_tags)) {
                        $newconfig->config['disabletags'] = true;
                    }
                    if (!empty($oldconfig->enable_categories)) {
                        $newconfig->config['usecategories'] = $oldconfig->enable_categories;
                    }

                    // we have to pull in external addresses for reminders
                    $addrs = $old_db->selectObjects('calendar_reminder_address',"calendar_id=".$oldconfig->id);
                    foreach ($addrs as $addr) {
                        if (!empty($addr->user_id)) {
                            $newconfig->config['users'][] = $addr->user_id;
                        } elseif (!empty($addr->group_id)) {
                            $newconfig->config['groups'][] = $addr->group_id;
                        } elseif (!empty($addr->email)) {
                            $newconfig->config['addresses'][] = $addr->email;
                        }
                    }
                }

                //check to see if it's already pulled in (circumvent !is_original)
				$ploc = clone($iloc);
				$ploc->mod = "event";
				if ($db->countObjects('event', "location_data='".serialize($ploc)."'")) {
//					$iloc->mod = 'calendarmodule';
//					$linked = true;
					break;
				}

//                $iloc->mod = 'calendarmodule';
                // convert each eventdate
                $eds = $old_db->selectObjects('eventdate',"1");
                foreach ($eds as $ed) {
                    $cloc = expUnserialize($ed->location_data);
                    $cloc->mod = 'event';
                    $ed->location_data = serialize($cloc);
                    $db->insertObject($ed,'eventdate');
                }

                // convert each calendar to an event
                $cals = $old_db->selectObjects('calendar',"1");
                foreach ($cals as $cal) {
                    unset($cal->approved);
                    $cat = $cal->category_id;
                    unset($cal->category_id);
                    $tags = $cal->tags;
                    unset(
                        $cal->tags,
                        $cal->file_id
                    );
                    $loc = expUnserialize($cal->location_data);
                    $loc->mod = "event";
                    $cal->location_data = serialize($loc);
                    $cal->created_at = $cal->posted;
                    unset($cal->posted);
                    $cal->edited_at = $cal->edited;
                    unset($cal->edited);
                    $db->insertObject($cal,'event');

                    $ev = new event($cal->id);
                    $ev->save();
                    if (!empty($oldconfig->enable_tags)) {
                        $params = null;;
                        $oldtags = expUnserialize($tags);
                        if (!empty($oldtags)) {
                            foreach ($oldtags as $oldtag){
                                $tagtitle = strtolower(trim($old_db->selectValue('tags','name','id = '.$oldtag)));
                                $tag = new expTag($tagtitle);
//								$tag->title = $old_db->selectValue('tags','name','id = '.$oldtag);
                                if (empty($tag->id))
                                    $tag->update(array('title'=>$tagtitle));
                                $params['expTag'][] = $tag->id;
                            }
                        }
                        $ev->update($params);
                    }
                    if (!empty($oldconfig->enable_categories) && $cat) {
                        $params = null;
                        $oldcat = $old_db->selectObject('category','id = '.$cat);
                        $cat = new expCat($oldcat->name);
                        if (empty($cat->id)) {
                            $cat->title = $oldcat->name;
                            $cat->color = $oldcat->color;
                            $catloc = expUnserialize($oldcat->location_data);
                            if (array_key_exists($catloc->mod, $this->new_modules)) {
                                $mod = expModules::getModuleName($this->new_modules[$catloc->mod]);
                                $cat->module = $mod;
                            }
                            $cat->save();
                            $cat->rank = $oldcat->rank +1;
                            $cat->update();
                        }
                        $params['expCat'][] = $cat->id;
                        $ev->update($params);
                    }
                }
                @$this->msg['migrated'][$iloc->mod]['count']++;
                @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                break;
            case 'contactmodule':  // v2.1.1 now converted to a forms 2.0 module
				$module->view = "enterdata";
                $module->action = "enterdata";

//                $iloc->mod = 'contactmodule';
                $contactform = $old_db->selectObject('contactmodule_config', "location_data='".serialize($iloc)."'");
				if ($contactform) {
                    // for forms 2.0 we create a site form (form & report consolidated)
                    $newform = new forms();
                    $newform->title = 'Contact Form';
                    $newform->is_saved = false;
                    $newform->table_name = '';
                    $newform->description = '';
                    $newform->response = $contactform->final_message;
                    $newform->update();

                    // now add the controls to the site form
					$control = new stdClass();
					$control->name = 'name';
					$control->caption = 'Your Name';
					$control->forms_id = $newform->id;
					$control->data = 'O:11:"textcontrol":14:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:9:"Your Name";s:11:"placeholder";s:8:"John Doe";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:4:"name";s:11:"description";s:22:"Please enter your name";}';
					$control->rank = 1;
					$control->is_readonly = 0;
					$control->is_static = 0;
					$db->insertObject($control, 'forms_control');
					$control->name = 'email';
					$control->caption = 'Your Email';
					$control->data = 'O:11:"textcontrol":14:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:10:"Your Email";s:11:"placeholder";s:18:"johndoe@mailer.org";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:5:"email";s:11:"description";s:31:"Please enter your email address";}';
					$control->rank = 2;
					$db->insertObject($control, 'forms_control');
					$control->name = 'subject';
					$control->caption = 'Subject';
					$control->data = 'O:11:"textcontrol":14:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:7:"Subject";s:11:"placeholder";s:22:"Subject line for email";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:7:"subject";s:11:"description";s:21:"Enter a quick summary";}';
					$control->rank = 3;
					$db->insertObject($control, 'forms_control');
					$control->name = 'message';
					$control->caption = 'Message';
					$control->data = 'O:17:"texteditorcontrol":13:{s:4:"cols";i:60;s:4:"rows";i:8;s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:0;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:8:"maxchars";i:0;s:10:"identifier";s:7:"message";s:7:"caption";s:7:"Message";s:11:"description";s:33:"Enter the content of your message";}';
					$control->rank = 4;
					$db->insertObject($control, 'forms_control');

                    //  and then an expConfig to link to that site form with config settings
                    $newconfig->config['forms_id'] = $newform->id;
                    $newconfig->config['title'] = 'Send us an e-mail';
                    $newconfig->config['description'] = '';
                    $newconfig->config['is_email'] = true;
                    if (!empty($contactform->subject)) {
                        $newconfig->config['report_name'] = $contactform->subject;
                        $newconfig->config['subject'] = $contactform->subject;
                    }
                    if (!empty($contactform->final_message)) $newconfig->config['response'] = $contactform->final_message;
                    $newconfig->config['submitbtn'] = 'Send Message';
                    $newconfig->config['resetbtn'] = 'Reset';

                    // we have to pull in addresses for emails
                    $addrs = $old_db->selectObjects('contact_contact', "location_data='".serialize($iloc)."'");
                    foreach ($addrs as $addr) {
                        if (!empty($addr->user_id)) {
                            $newconfig->config['user_list'][] = $addr->user_id;
                        } elseif (!empty($addr->email)) {
                            $newconfig->config['address_list'][] = $addr->email;
                        }
                    }

					@$this->msg['migrated'][$iloc->mod]['count']++;
					@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
				}
				break;
            case 'formmodule':  // convert to forms module
                $module->view = "enterdata";
                $module->action = "enterdata";

                // new form update
                $oldform = $old_db->selectObject('formbuilder_form', "location_data='".serialize($iloc)."'");
                $oldreport = $old_db->selectObject('formbuilder_report', "location_data='".serialize($iloc)."'");

                if (!empty($oldform->id)) {
                    $newform = new forms();
                    $newform->title = $oldform->name;
                    $newform->is_saved = $oldform->is_saved;
                    $newform->table_name = $oldform->table_name;
                    if (empty($newform->title) && !empty($newform->table_name)) $newform->title = implode(' ',explode('_',$newform->table_name));
                    $newform->description = $oldform->description;
                    $newform->response = $oldform->response;
                    $newform->report_name = $oldreport->name;
                    $newform->report_desc = $oldreport->description;
                    $newform->report_def = $oldreport->text;
                    $newform->column_names_list = $oldreport->column_names;
                    $newform->update();

                     // copy & convert each formbuilder_control to a forms_control
                    $fcs = $old_db->selectObjects('formbuilder_control',"form_id=".$oldform->id);
                    foreach ($fcs as $fc) {
                        $fc->forms_id = $newform->id;
                        unset (
                            $fc->id,
                            $fc->form_id
                        );
                        $db->insertObject($fc,'forms_control');
                    }

                    // import form saved data
                    if ($oldform->is_saved) {
                        $newform->updateTable();  // creates the table in database
                        $records = $old_db->selectObjects('formbuilder_'.$oldform->table_name, 1);
                        foreach($records as $record) {
                            //FIXME do we want to add a forms_id field?
//                            $db->insertObject($record, 'forms_'.$oldform->table_name);
                            $oldform->insertRecord($record);
                        }
                    }

                    // convert the form & report configs to an expConfig object for this module
                    $newconfig = new expConfig();
                    $newconfig->config['forms_id'] = $newform->id;
                    if (!empty($oldform->name)) $newconfig->config['title'] = $oldform->name;
                    if (!empty($oldform->description)) $newconfig->config['description'] = $oldform->description;
                    if (!empty($oldform->response)) $newconfig->config['response'] = $oldform->response;
                    if (!empty($oldform->is_email)) $newconfig->config['is_email'] = $oldform->is_email;
                    if (!empty($oldform->select_email)) $newconfig->config['select_email'] = $oldform->select_email;
                    if (!empty($oldform->submitbtn)) $newconfig->config['submitbtn'] = $oldform->submitbtn;
                    if (!empty($oldform->resetbtn)) $newconfig->config['resetbtn'] = $oldform->resetbtn;
                    if (!empty($oldform->style)) $newconfig->config['style'] = $oldform->style;
                    if (!empty($oldform->subject)) $newconfig->config['subject'] = $oldform->subject;
                    if (!empty($oldform->is_auto_respond)) $newconfig->config['is_auto_respond'] = $oldform->is_auto_respond;
                    if (!empty($oldform->auto_respond_subject)) $newconfig->config['auto_respond_subject'] = $oldform->auto_respond_subject;
                    if (!empty($oldform->auto_respond_body)) $newconfig->config['auto_respond_body'] = $oldform->auto_respond_body;
                    if (!empty($oldreport->name)) $newconfig->config['report_name'] = $oldreport->name;
                    if (!empty($oldreport->description)) $newconfig->config['report_desc'] = $oldreport->description;
                    if (!empty($oldreport->text)) $newconfig->config['report_def'] = $oldreport->text;
                    if (!empty($oldreport->column_names)) $newconfig->config['column_names_list'] = explode('|!|',$oldreport->column_names);

                    // we have to pull in addresses for emails
                    $addrs = $old_db->selectObjects('formbuilder_address',"form_id=".$oldform->id);
                    foreach ($addrs as $addr) {
                        if (!empty($addr->user_id)) {
                            $newconfig->config['user_list'][] = $addr->user_id;
                        } elseif (!empty($addr->group_id)) {
                            $newconfig->config['group_list'][] = $addr->group_id;
                        } elseif (!empty($addr->email)) {
                            $newconfig->config['address_list'][] = $addr->email;
                        }
                    }

                    // now save/attach the expConfig
                    if ($newconfig->config != null) {
                        $newconfig->location_data = expCore::makeLocation($this->new_modules[$iloc->mod],$iloc->src);
                    }
                }

                @$this->msg['migrated'][$iloc->mod]['count']++;
                @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                break;
            case 'containermodule':
                if (!$hc) {
                    $module->action = 'showall';
                    if ($module->view == 'Default') {
                        @$module->view = 'showall';
                    } else {
                        @$module->view = 'showall_'.$module->view;
                    }
                    @$this->msg['migrated'][$iloc->mod]['count']++;
                    @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                }
				break;
            default:
                @$this->msg['noconverter'][$iloc->mod]++;
				break;
		}
        // quick check for non hard coded modules
        // We add a container if they're not hard coded.
        (!$hc) ? $this->add_container($iloc,$module,$linked,$newconfig) : "";

        return $module;
    }

//	/**
//	 * pull over extra/related data required for old school modules
//	 * @var \mysqli_database $db the exponent database object
//	 * @param  $iloc
//	 * @param  $module
//	 * @return bool
//	 */
//    private function pulldata($iloc, $module) {
//        global $db;
//        $old_db = $this->connect();
//		$linked = false;
//        if ((!empty($module->is_existing) && $module->is_existing)) {
//            $linked = true;
//        }
//
//        switch ($iloc->mod) {
////            case 'calendarmodule':
////				if ($db->countObjects('calendar', "location_data='".serialize($iloc)."'")) {
////					$linked = true;
////					break;
////				}
////                $events = $old_db->selectObjects('eventdate', "location_data='".serialize($iloc)."'");
////                foreach($events as $event) {
////                    $res = $db->insertObject($event, 'eventdate');
////					if ($res) { @$this->msg['migrated'][$iloc->mod]['count']++; }
////                }
////                $cals = $old_db->selectObjects('calendar', "location_data='".serialize($iloc)."'");
////                foreach($cals as $cal) {
////                    unset($cal->allow_registration);
////                    unset($cal->registration_limit);
////                    unset($cal->registration_allow_multiple);
////                    unset($cal->registration_cutoff);
////                    unset($cal->registration_price);
////                    unset($cal->registration_count);
////                    $db->insertObject($cal, 'calendar');
////                }
////                $configs = $old_db->selectObjects('calendarmodule_config', "location_data='".serialize($iloc)."'");
////                foreach ($configs as $config) {
////                    $reminders = $old_db->selectObjects('calendar_reminder_address', "calendar_id='".$config->id."'");
////					$config->id = '';
////					$config->enable_categories = 0;
////					$config->enable_tags = 0;
////                    $db->insertObject($config, 'calendarmodule_config');
////                    foreach($reminders as $reminder) {
////                        $reminder->calendar_id = $config->id;
////                        $db->insertObject($reminder, 'calendar_reminder_address');
////                    }
////                }
////				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
////				break;
////            case 'simplepollmodule':
////				if ($db->countObjects('poll_question', "location_data='".serialize($iloc)."'")) {
////					break;
////				}
////                $questions = $old_db->selectObjects('poll_question', "location_data='".serialize($iloc)."'");
////                foreach($questions as $question) {
////                    $db->insertObject($question, 'poll_question');
////					$answers = $old_db->selectObjects('poll_answer', "question_id='".$question->id."'");
////					foreach($answers as $answer) {
////						$db->insertObject($answer, 'poll_answer');
////					}
////					$timeblocks = $old_db->selectObjects('poll_timeblock', "question_id='".$question->id."'");
////					foreach($timeblocks as $timeblock) {
////						$db->insertObject($timeblock, 'poll_timeblock');
////					}
////					@$this->msg['migrated'][$iloc->mod]['count']++;
////                }
////                $configs = $old_db->selectObjects('simplepollmodule_config', "location_data='".serialize($iloc)."'");
////                foreach ($configs as $config) {
////                    $db->insertObject($config, 'simplepollmodule_config');
////                }
////				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
////				break;
////            case 'formmodule':
////				if ($db->countObjects('formbuilder_form', "location_data='".serialize($iloc)."'")) {
////					break;
////				}
////                $form = $old_db->selectObject('formbuilder_form', "location_data='".serialize($iloc)."'");
////				$oldformid = $form->id;
////				unset($form->id);
////                $form->id = $db->insertObject($form, 'formbuilder_form');
////				@$this->msg['migrated'][$iloc->mod]['count']++;
////				$addresses = $old_db->selectObjects('formbuilder_address', "form_id='".$oldformid."'");
////                foreach($addresses as $address) {
////					unset($address->id);
////					$address->form_id = $form->id;
////                    $db->insertObject($address, 'formbuilder_address');
////				}
////				$controls = $old_db->selectObjects('formbuilder_control', "form_id='".$oldformid."'");
////                foreach($controls as $control) {
////					unset($control->id);
////					$control->form_id = $form->id;
////                    $db->insertObject($control, 'formbuilder_control');
////				}
////				$reports = $old_db->selectObjects('formbuilder_report', "form_id='".$oldformid."'");
////                foreach($reports as $report) {
////					unset($report->id);
////					$report->form_id = $form->id;
////                    $db->insertObject($report, 'formbuilder_report');
////				}
////				if (isset($form->table_name)) {
////					if (isset($this->params['wipe_content'])) {
////						$db->delete('formbuilder_'.$form->table_name);
////					}
////					formbuilder_form::updateTable($form);
////					$records = $old_db->selectObjects('formbuilder_'.$form->table_name, 1);
////					foreach($records as $record) {
////						$db->insertObject($record, 'formbuilder_'.$form->table_name);
////					}
////				}
////				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
////				break;
//        }
//        return $linked;
//    }

    /**
     * used to create containers, expConfigs, and expRss for new modules
     * @param $iloc
     * @param $m
     * @param bool $linked
     * @param $newconfig
     * @var \mysqli_database $db the exponent database object
     * @return void
     */
	private function add_container($iloc,$m,$linked=false,$newconfig) {
//        global $db;

        // first the container
//        $old_db = $this->connect();
//        $section = $old_db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."' AND is_original='0'");
//        unset($m->id);
//        $oldext = expUnserialize($m->external);
//        $m->external = serialize(expCore::makeLocation('container',$oldext->src));
////		if ($iloc->mod != 'contactmodule') {
//			$iloc->mod = $this->new_modules[$iloc->mod];
////			$m->internal = (isset($m->internal) && strstr($m->internal,"Controller")) ? $m->internal : serialize($iloc);
//            $m->internal = serialize($iloc);
//			$m->action = isset($m->action) ? $m->action : 'showall';
//			$m->view = isset($m->view) ? $m->view : 'showall';
//			if ($m->view == "Default") {
//				$m->view = 'showall';
//			}
//		} else {  // must be an old school contactmodule
//			$iloc->mod = $this->new_modules[$iloc->mod];
//			$m->internal = serialize($iloc);
//		}

        $params = get_object_vars($m);
        unset($params['id']);
        $old_db = $this->connect();
        $section = $old_db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."' AND is_original='0'");
        $params['current_section'] = empty($section->section) ? 1 : $section->section;
        $oldext = expUnserialize($params['external']);
        $params['external'] = serialize(expCore::makeLocation('container',$oldext->src));
        $iloc->mod = $this->new_modules[$iloc->mod];
        $params['modcntrol'] = $iloc->mod;
        $params['internal'] = serialize($iloc);
        $params['rank'] = $params['rank']+1;
        $params['action'] = !empty($params['action']) ? $params['action'] : 'showall';
        $params['view'] = !empty($params['view']) ? $params['view'] : 'showall';
        if ($params['view'] == "Default") {
            $params['view'] = 'showall';
        }

        $m = new container();
        if (!$linked) {
            $params['existing_source'] = $iloc->src;
        }
        $m->update($params);
		if ($linked) {
//			$newmodule['i_mod'] = $iloc->mod;
//			$newmodule['modcntrol'] = $iloc->mod;
//			$newmodule['rank'] = $m->rank;
//			$newmodule['views'] = $m->view;
//			$newmodule['title'] = $m->title;
//			$newmodule['actions'] = $m->action;
//			$_POST['current_section'] = empty($section->section) ? 1 : $section->section;
//			$m = container::update($newmodule,$m,expUnserialize($m->external));
//            $params = array();
//            $params['rank'] = $newmod['rank'];
//            $params['view'] = $newmod['view'];
//            $params['title'] = $newmod['title'];
//            $params['action'] = $newmod['action'];
//            $params['is_private'] = $newmod['is_private'];
            $newconfig->config['aggregate'][] = $iloc->src;
            if ($iloc->mod == 'blog') {
                $newconfig->config['add_source'] = 1;  //  we need to make our blog aggregation discrete
            }
        }
//        $db->insertObject($m, 'container');

        // now save the expConfig
        if (!empty($newconfig->config['enable_rss']) && $newconfig->config['enable_rss'] == true) {
            $newrss = new expRss();
            $newrss->enable_rss = $newconfig->config['enable_rss'];
            $newrss->advertise = $newconfig->config['enable_rss'];
            $newrss->title = $newconfig->config['feed_title'];
//            $newrss->sef_url = expCore::makeSefUrl($newrss->title,'expRss');
			$newrss->sef_url = $this->makeSefUrl($newrss->title);
            $newrss->feed_desc = $newconfig->config['feed_desc'];
            $newrss->rss_limit = $newconfig->config['rss_limit'];
            $newrss->rss_cachetime = $newconfig->config['rss_cachetime'];
        }
        if ($newconfig->config != null) {
//            $newmodinternal = expUnserialize($m->internal);
//            $newmod = expModules::getModuleName($newmodinternal->mod);
//            $newmodinternal->mod = $newmod;
            $newconfig->location_data = expUnserialize($m->internal);
            $newconfig->save();
        }

        // and save the expRss table
        if (!empty($newrss->enable_rss)) {
            $newmodinternal = expUnserialize($m->internal);
            $newrss->module = $newmodinternal->mod;
            $newrss->src = $newmodinternal->src;
            $newrss->save();
        }
    }

	/**
	 * module customized function to circumvent going to previous page
	 * @return void
	 */
	function saveconfig() {
        
        // unset some unneeded params
        unset(
            $this->params['module'],
            $this->params['controller'],
            $this->params['src'],
            $this->params['int'],
            $this->params['id'],
            $this->params['action'],
            $this->params['PHPSESSID'],
            $this->params['__utma'],
            $this->params['__utmb'],
            $this->params['__utmc'],
            $this->params['__utmz'],
            $this->params['__utmt'],
            $this->params['__utmli'],
            $this->params['__cfduid']
        );
        
        // setup and save the config
        $config = new expConfig($this->loc);
        $config->update(array('config'=>$this->params));
		// update our object config
		$this->config = expUnserialize($config->config);
//        flash('message', 'Migration Configuration Saved');
//        expHistory::back();
        $this->connect();  // now make sure the parameters work

		if (isset($this->params['fix_database'])) $this->fix_database();
        //NOTE we need to push the button.css file to head for coolwater theme?
        expCSS::pushToHead(array(
//      		    "unique"=>"button",
      		    "corecss"=>"button",
      		    ));
		echo '<h2>'.gt('Migration Configuration Saved').'</h2><br />';
		echo '<p>'.gt('We\'ve successfully connected to the Old database').'</p><br />';
        if (bs()) {
            $btn_class = 'btn btn-default';
        } else {
            $btn_class = "awesome " . BTN_SIZE . " " . BTN_COLOR;
        };
		echo "<a class=\"".$btn_class."\" href=\"".expCore::makeLink(array('controller'=>'migration','action'=>'manage_users'))."\">".gt('Next Step -> Migrate Users & Groups')."</a>";
    }
	
	/**
	 * connect to old site's database
	 *
	 * @return mysqli_database
	 */
    private function connect() {
        // check for required info...then make the DB connection.
        if (
            empty($this->config['username']) ||
            empty($this->config['password']) ||
            empty($this->config['database']) ||
            empty($this->config['server']) ||
            empty($this->config['prefix']) ||
            empty($this->config['port'])
        ) {
            flash('error', gt('You are missing some required database connection information.  Please enter DB information.'));
            redirect_to (array('controller'=>'migration', 'action'=>'configure'));
//            $this->configure();
        }

       $database = expDatabase::connect($this->config['username'],$this->config['password'],$this->config['server'].':'.$this->config['port'],$this->config['database']);

       if (empty($database->havedb)) {
           flash('error', gt('An error was encountered trying to connect to the database you specified. Please check your DB config.'));
           redirect_to (array('controller'=>'migration', 'action'=>'configure'));
//           $this->configure();
       }

       $database->prefix = $this->config['prefix']. '_';;
       return $database;
    }

	/**
	 * several things that may clear up problems in the old database and do a better job of migrating data
	 * @return void
	 */
	private function fix_database() {
		// let's test the connection
		$old_db = $this->connect();
		
		print_r("<h2>".gt('We\'re connected to the Old Database!')."</h2><br><br><h3>".gt('Running several checks and fixes on the old database')."<br>".gt('to enhance Migration.')."</h3><br>");

		print_r("<pre>");
	// upgrade sectionref's that have lost their originals
		print_r("<strong>".gt('Searching for sectionrefs that have lost their originals')."</strong><br><br>");
		$sectionrefs = $old_db->selectObjects('sectionref',"is_original=0");
		print_r("Found: ".count($sectionrefs)." copies (not originals)<br>");
		foreach ($sectionrefs as $sectionref) {
			if ($old_db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
			// There is no original for this sectionref so change it to the original
//				$sectionref->is_original = 1;
				$old_db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");
	
		print_r("<pre>");
	// upgrade sectionref's that point to missing sections (pages)
		print_r("<strong>".gt('Searching for sectionrefs pointing to missing sections/pages')." <br>".gt('to fix for the Recycle Bin')."</strong><br><br>");
		$sectionrefs = $old_db->selectObjects('sectionref',"refcount!=0");
		foreach ($sectionrefs as $sectionref) {
			if ($old_db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$old_db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");

	}

	/**
	 * Take an old school permission and convert it to a newmodule permission
	 *
	 * @param $item
	 * @return mixed
	 */
	private function convert_permission($item) {
		if ($item == null) return null;
		switch ($item->permission) {
		    case 'administrate':
			    $item->permission = 'manage';
				break;
			case 'post':
			case 'create_slide':
			case 'add':
			case 'add_item':
            case 'add_module':
				$item->permission = 'create';
				break;
			case 'edit_item':
			case 'edit_slide':
            case 'edit_module':
				$item->permission = 'edit';
				break;
			case 'delete_item':
			case 'delete_slide':
            case 'delete_module':
				$item->permission = 'delete';
				break;
			case 'order':
			case 'import':
            case 'orders_modules':
				$item->permission = 'configure';
				break;
			case 'view_unpublished':
				$item->permission = 'show_unpublished';
				break;
            case 'approve_comments':
                $item->permission = 'approve';
                break;
			case 'manage_categories':
			case 'manage_approval':
			case 'approve':
			case 'can_download':
			case 'comment':
			case 'edit_comments':
			case 'delete_comments':
			case 'view_private':
                $item = null;
				break;
			case 'create':
			case 'configure':
			case 'delete':
			case 'edit':
			case 'manage':
			case 'spider':
			case 'view':
			default:
				break;
		}
		return $item;
	}

}

?>