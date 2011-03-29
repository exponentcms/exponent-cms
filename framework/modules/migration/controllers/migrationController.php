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

class migrationController extends expController {
    //public $basemodel_name = '';
    protected $permissions = array('manage'=>'Manage', 'analyze'=>'Analyze Data', 'migrate'=>'Migrate Data','configure'=>'Configure');
    //public $useractions = array('showall'=>'Show all');
	public $useractions = array();
	public $codequality = 'beta';

    // this is a list of modules that we can convert to exp2 type modules.
    public $new_modules = array(
//        'addressbookmodule'=>'addressController',  // NOT WRITTEN YET???
        'imagegallerymodule'=>'photosController',
        'linklistmodule'=>'linksController',
        'newsmodule'=>'newsController',
        'slideshowmodule'=>'photosController',
        'snippetmodule'=>'snippetController',
        'swfmodule'=>'textController',
        'textmodule'=>'textController',
        'resourcesmodule'=>'filedownloadController',
        'rotatormodule'=>'textController',
// the following "scripts" were added by Dave Leffler
        'faqmodule'=>'faqController',
        'headlinemodule'=>'headlineController',
        'linkmodule'=>'linksController',
        'weblogmodule'=>'blogController',
        'listingmodule'=>'portfolioController',
		'contactmodule'=>'formmodule',  // this module is converted to a functionally similar old school formmodule
        'youtubemodule'=>'youtubeController',
    );

    // these are modules that have either been deprecated or have no content to migrate
    // Not sure we need to note deprecated modules...
    public $deprecated_modules = array(
        'administrationmodule',
//        'containermodule',  // not really deprecated, but must be in this list to skip processing?
//        'navigationmodule',  // views are still used, so modules need to be imported?
        'imagemanagermodule',
        'imageworkshopmodule',
        'inboxmodule',
        'loginmodule',
        'rssmodule',
        'searchmodule',
// the following 0.97/98 modules were added to this list by Dave Leffler
//   based on lack of info showing they will exist in 2.0
        'articlemodule',
        'bbmodule',
        'pagemodule',
        'previewmodule',
        'tasklistmodule',
        'wizardmodule',
// other older or user-contributed modules we don't want to deal with
        'cataloguemodule',
        'codemapmodule',
        'extendedlistingmodule',
        'feedlistmodule',
        'googlemapmodule',
        'greekingmodule',
        'guestbookmodule',
        'keywordmodule',
        'sharedcoremodule',
        'svgallerymodule',
    );

    public $needs_written = array(
		'addressbookmodule',  // listed above, but no script written yet?
        'bannermodule',  // to bannerController?
        'mediaplayermodule',  // to flowplayerController?
//        'categories',  // no controller and not in old school ???
//        'tags',	 // no controller and not in old school ???
    );

    // public $old_school = array(  // psuedo-variable isn't used, list of old school modules still in code base
        // 'calendarmodule',
        // 'formmodule',
        // 'navigationmodule',
        // 'simplepollmodule',
    // );

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Content Migration Controller"; }
    function description() { return "Use this module to pull Exponent 1 style content from your old site."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
    function hasContent() { return false; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }

	// gather info about all pages in old site for user selection
    public function manage_pages() {
        global $db;

        expHistory::set('managable', $this->params);
        $old_db = $this->connect();
        $pages = $old_db->selectObjects('section','id > 1');
        foreach($pages as $page) {
			if ($db->selectObject('section',"id='".$page->id."'")) {
				$page->exists = true;
			} else {
				$page->exists = false;
			}
		}
        assign_to_template(array('pages'=>$pages));
    }

	// copy selected pages over from old site
    public function migrate_pages() {
        global $db;

		$del_pages = '';
        if (isset($this->params['wipe_pages'])) {
            print_r($db->delete('section',"id > '1'"));
			$del_pages = ' after clearing database of pages';
		}
        $successful = 0;
        $failed     = 0;
        $old_db = $this->connect();
		if (!empty($this->params['pages'])) {
			foreach($this->params['pages'] as $pageid) {
				$page = $old_db->selectObject('section', 'id='.$pageid);
				$ret = $db->insertObject($page, 'section');
				if (empty($ret)) {
					$failed += 1;
				} else {
					$successful += 1;
				}
			}
		}
		if (!empty($this->params['rep_pages'])) {
			foreach($this->params['rep_pages'] as $pageid) {
				$db->delete('section','id='.$pageid);
				$page = $old_db->selectObject('section', 'id='.$pageid);
				$ret = $db->insertObject($page, 'section');
				if (empty($ret)) {
					$failed += 1;
				} else {
					$successful += 1;
				}
			}
			}

        flash ('message', $successful.' pages were imported from '.$this->config['database'].$del_pages = '');
        if ($failed > 0) {
            flash('error', $failed.' pages could not be imported from '.$this->config['database'].' This is usually because a page with the same ID already exists in the database you importing to.');
        }

        expSession::clearUserCache();
        expHistory::back();
    }

	// gather info about all files in old site for user selection
    public function manage_files() {
        expHistory::set('managable', $this->params);
        $old_db = $this->connect();
        $files = $old_db->selectObjects('file');
        assign_to_template(array('files'=>$files));
    }

	// copy selected file information (not the files themselves) over from old site
    public function migrate_files() {
        global $db;

        echo "<ol>";
        $old_db = $this->connect();
        $db->delete('expFiles');
        echo "<li class=\"mig-msg\">
            Emptied expFiles table before file import.
        </li>";

        // // pull the sectionref data
        // $secref = $old_db->selectObjects('sectionref');
        // foreach ($secref as $sr) {
        //     if (array_key_exists($sr->module, $this->new_modules)) {
        //         $sr->module = $this->new_modules[$sr->module];
        //         $db->insertObject($sr, 'sectionref');
        //     } elseif (in_array($sr->module, $this->deprecated_modules)) {
        //         // do nothing...we don't want this module
        //     } else {
        //         $db->insertObject($sr, 'sectionref');
        //     }
        // }
        //
        echo "<li class=\"mig-msg\">
            Importing files
        </li>";

        //import the files
        $oldfiles = $old_db->selectObjects('file');
        foreach ($oldfiles as $oldfile) {
            unset($oldfile->name);
            unset($oldfile->collection_id);
            $file = $oldfile;
            $file->directory = $file->directory."/";
            $db->insertObject($file,'expFiles');
        }

        echo "<li class=\"mig-msg\">
            ".count($oldfiles)." files imported.
        </li>";
        echo "<li class=\"mig-msg\">
            Done! You should now have all files from your previous system listed in your file manager.
        </li>";
        echo "<li class=\"mig-msg\">
            HOWEVER, you must manually copy the 'files' directory over to this installation.
        </li>";
        echo "</ol>";
    }

	// gather info about all modules in old site for user selection
    public function manage_content() {
        global $db;
        //$containers = $db->selectObjects('container', 'external="N;"');
        //eDebug($containers);
        $old_db = $this->connect();

        $sql  = 'SELECT *, COUNT(module) as count FROM '.$this->config['prefix'].'_sectionref WHERE is_original=1 GROUP BY module';
        $modules = $old_db->selectObjectsBySql($sql);
        for($i=0; $i<count($modules); $i++) {
            if (array_key_exists($modules[$i]->module, $this->new_modules)) {
                $newmod = new $this->new_modules[$modules[$i]->module]();
                $modules[$i]->action = '<span style="color:green;">Converting content to '.$newmod->displayname()."</span>";
            } elseif (in_array($modules[$i]->module, $this->deprecated_modules)) {
                // $modules[$i]->action = '<span style="color:red;">This module is deprecated and will not be migrated.</span>';
                $modules[$i]->notmigrating = 1;
            } elseif (in_array($modules[$i]->module, $this->needs_written)) {
                $modules[$i]->action = '<span style="color:orange;">Still needs migration script written</span>';
            } else {
                $modules[$i]->action = 'Migrating as is.';
            }
        }
        //eDebug($modules);

        assign_to_template(array('modules'=>$modules));
    }

	// copy selected modules and their contents over from old site
    public function migrate_content() {
        global $db;

        $old_db = $this->connect();
        if (isset($this->params['wipe_content'])) {
            $db->delete('sectionref');
            $db->delete('locationref');
            $db->delete('container');
            $db->delete('text');
            $db->delete('snippet');
            $db->delete('links');
            $db->delete('news');
            $db->delete('filedownloads');
            $db->delete('photo');
            $db->delete('headline');
            $db->delete('blog');
            $db->delete('faqs');
            $db->delete('portfolio');
            $db->delete('youtube');
            $db->delete('content_expComments');
            $db->delete('content_expFiles');
            $db->delete('content_expSimpleNote');
            $db->delete('content_expTags');
            $db->delete('expComments');
            $db->delete('expSimpleNote');
            $db->delete('expTags');
//            $db->delete('expConfigs');
//            $db->delete('expFiles');
//            $db->delete('expRSS');
            $db->delete('calendar');
            $db->delete('eventdate');
            $db->delete('calendarmodule_config');
            $db->delete('poll_question');
            $db->delete('poll_answer');
            $db->delete('poll_timeblock');
            $db->delete('simplepollmodule_config');
            $db->delete('formbuilder_address');
            $db->delete('formbuilder_control');
            $db->delete('formbuilder_form');
            $db->delete('formbuilder_report');
            @$this->msg['clearedcontent']++;
        }
		
		if (!empty($this->params['replace'])) {
			if (in_array('containermodule',$this->params['replace'])) {
				$db->delete('container');
			}
			if (in_array('textmodule',$this->params['replace'])) {
				$db->delete('text');
			}
			if (in_array('rotatormodule',$this->params['replace'])) {
				$db->delete('text');
			}
			if (in_array('snippetmodule',$this->params['replace'])) {
				$db->delete('snippet');
			}
			if (in_array('linklistmodule',$this->params['replace'])) {
				$db->delete('links');
			}
			if (in_array('linkmodule',$this->params['replace'])) {
				$db->delete('links');
			}
			if (in_array('swfmodule',$this->params['replace'])) {
				$db->delete('text');
			}
			if (in_array('newsmodule',$this->params['replace'])) {
				$db->delete('news');
			}
			if (in_array('resourcesmodule',$this->params['replace'])) {
				$db->delete('filedownload');
			}
			if (in_array('imagegallerymodule',$this->params['replace'])) {
				$db->delete('photo');
			}
			if (in_array('slideshowmodule',$this->params['replace'])) {
				$db->delete('photo');
			}
			if (in_array('headlinemodule',$this->params['replace'])) {
				$db->delete('headline');
			}
			if (in_array('weblogmodule',$this->params['replace'])) {
				$db->delete('blog');
				$db->delete('expComments');
				$db->delete('content_expComments');
			}
			if (in_array('faqmodule',$this->params['replace'])) {
				$db->delete('faq');
			}
			if (in_array('listingmodule',$this->params['replace'])) {
				$db->delete('portfolio');
			}
			if (in_array('calendarmodule',$this->params['replace'])) {
				$db->delete('calendar');
				$db->delete('eventdate');
				$db->delete('calendarmodule_config');
			}
			if (in_array('simplepollmodule',$this->params['replace'])) {
				$db->delete('poll_question');
				$db->delete('poll_answer');
				$db->delete('poll_timeblock');
				$db->delete('simplepollmodule_config');
			}
			if (in_array('formmodule',$this->params['replace'])) {
				$db->delete('formbuilder_address');
				$db->delete('formbuilder_control');
				$db->delete('formbuilder_form');
				$db->delete('formbuilder_report');
			}
			if (in_array('youtubemodule',$this->params['replace'])) {
				$db->delete('youtube');
			}
		}

        //pull the locationref data for selected modules
		if (empty($this->params['migrate'])) {
			$where = '1';
		} else {
			$where = '';
			foreach ($this->params['migrate'] as $key=>$var) {
				if (!empty($where)) {$where .= " or";}
				$where .= " module='".$key."'";
			}
		}

        $locref = $old_db->selectObjects('locationref',$where);
        foreach ($locref as $lr) {
            if (array_key_exists($lr->module, $this->new_modules)) {
                $lr->module = $this->new_modules[$lr->module];
            }

            if (!in_array($lr->module, $this->deprecated_modules)) {
                if (!$db->selectObject('locationref',"source='".$lr->source."'")) {
                    $db->insertObject($lr, 'locationref');
                    @$this->msg['locationref']++;
                }
            }
        }

        // pull the sectionref data for selected modules
        $secref = $old_db->selectObjects('sectionref',$where);
        foreach ($secref as $sr) {
            // hard coded modules
            if (array_key_exists($sr->module, $this->new_modules) && ($sr->refcount==1000)) {
                $iloc->mod = $sr->module;
                $iloc->src = $sr->source;
                $iloc->int = $sr->internal;
                $this->convert($iloc,$iloc->mod,1);

                // convert the source to new exp controller
                $sr->module = $this->new_modules[$sr->module];
            }

            if (!in_array($sr->module, $this->deprecated_modules)) {
                // if the module is not in the depecation list, we're hitting here
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
            if (!$db->selectObject('container',"internal='".$cont->internal."'")) {
                $db->insertObject($cont, 'container');
                @$this->msg['container']++;
            }
        }

        // echo "Imported containermodules<br>";
        //
        // // this will pull all the old modules.  if we have a exp2 equivalent module
        // // we will convert it to the new type of module before pulling.
        $cwhere = ' and (';
        $i=0;
        foreach ($this->params['migrate'] as $key=>$var) {
            $cwhere .= ($i==0) ? "" : " or ";
            $cwhere .= "internal like '%".$key."%'";
            $i=1;
        }
        $cwhere .= ")";
        $modules = $old_db->selectObjects('container', 'external != "N;"'.$cwhere);
        foreach($modules as $module) {
            $iloc = expUnserialize($module->internal);
            if (array_key_exists($iloc->mod, $this->new_modules)) {
                // convert new modules added via container
                unset($module->internal);
                unset($module->action);
//                unset($module->view);
                $this->convert($iloc, $module);
            } else if (!in_array($iloc->mod, $this->deprecated_modules)) {
                // add old school modules not in the deprecation list
				if ($iloc->mod == 'calendarmodule' && $module->view == 'Upcoming Events - Summary') {
					$module->view = 'Upcoming Events - Headlines';
				}
				$res = $db->insertObject($module, 'container');
				if ($res) { @$this->msg['container']++; }
                $this->pulldata($iloc, $module);
            }
        }
		searchController::spider();
        expSession::clearUserCache();
        assign_to_template(array('msg'=>@$this->msg));
    }

	// gather info about all users/groups in old site for user selection
	public function manage_users() {
        global $db;

        expHistory::set('managable', $this->params);
        $old_db = $this->connect();
        $users = $old_db->selectObjects('user','id > 1');
        foreach($users as $user) {
			if ($db->selectObject('user',"id='".$user->id."'")) {
				$user->exists = true;
			} else {
				$user->exists = false;
			}
		}
		assign_to_template(array('users'=>$users));

        $groups = $old_db->selectObjects('group');
        foreach($groups as $group) {
			if ($db->selectObject('group',"id='".$group->id."'")) {
				$group->exists = true;
			} else {
				$group->exists = false;
			}
		}
		assign_to_template(array('groups'=>$groups));
    }

	// copy selected users/groups over from old site
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
					$gfailed += 1;
				} else {
					$gsuccessful += 1;
				}				
			}
		}
		if (!empty($this->params['rep_groups'])) {
			foreach($this->params['rep_groups'] as $groupid) {
				$db->delete('group','id='.$groupid);
				$group = $old_db->selectObject('group', 'id='.$groupid);
				$ret = $db->insertObject($group, 'group');
				if (empty($ret)) {
					$gfailed += 1;
				} else {
					$gsuccessful += 1;
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
					$failed += 1;
				} else {
					$successful += 1;
				}				
			}
		}
		if (!empty($this->params['rep_users'])) {
			foreach($this->params['rep_users'] as $userid) {
				$db->delete('user','id='.$userid);
				$user = $old_db->selectObject('user', 'id='.$userid);
				$ret = $db->insertObject($user, 'user');
				if (empty($ret)) {
					$failed += 1;
				} else {
					$successful += 1;
				}				
			}
		}
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
		
        flash ('message', $successful.' users and '.$gsuccessful.' groups were imported from '.$this->config['database']);
        if ($failed > 0 || $gfailed > 0) {
			$msg = '';
			if ($failed > 0) {
				$msg = $failed.' users ';
			}
			if ($gfailed > 0) {
				if ($msg != '') { $msg .= ' and ';}
				$msg .= $gfailed.' groups ';
			}
            flash('error', $msg.' could not be imported from '.$this->config['database'].' This is usually because a user with the username or group with that name already exists in the database you importing to.');
        }
        expSession::clearUserCache();
        expHistory::back();
    }

	// main routine to convert old school module data into new controller format
    private function convert($iloc, $module, $hc=0) {
        if (!array_key_exists($iloc->mod, $this->params['migrate'])) return $module;
        global $db;
        $old_db = $this->connect();

        switch ($iloc->mod) {
            case 'textmodule':

				@$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'textmodule';
					break;
				}

                $iloc->mod = 'textmodule';
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
				$ploc = $iloc;
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'rotatormodule';
					break;
				}

                $iloc->mod = 'rotatormodule';
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
				$ploc = $iloc;
				$ploc->mod = "snippet";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'snippetmodule';
					break;
				}

                $iloc->mod = 'snippetmodule';
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
				$ploc = $iloc;
				$ploc->mod = "links";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'linklistmodule';
					break;
				}

                $iloc->mod = 'linklistmodule';
                $links = $old_db->selectArrays('linklist_link', "location_data='".serialize($iloc)."'");
				if ($links) {
					foreach ($links as $link) {
						$lnk = new links();
						$loc = expUnserialize($link['location_data']);
						$loc->mod = "links";
						$lnk->title = $link['name'];
						$lnk->body = $link['description'];
						$lnk->new_window = $link['opennew'];
						$lnk->url = $link['url'];
						$lnk->rank = $link['rank'];
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

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "links";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'linkmodule';
					break;
				}

                $iloc->mod = 'linkmodule';
                $links = $old_db->selectArrays('link', "location_data='".serialize($iloc)."'");
				if ($links) {
					foreach ($links as $link) {
						$lnk = new links();
						$loc = expUnserialize($link['location_data']);
						$loc->mod = "links";
						$lnk->title = $link['name'];
						$lnk->body = $link['description'];
						$lnk->new_window = $link['opennew'];
						$lnk->url = $link['url'];
						$lnk->rank = $link['rank'];
						$lnk->poster = 1;
						$lnk->editor = 1;
						$lnk->location_data = serialize($loc);
						$lnk->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            case 'swfmodule':

				$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "text";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'swfmodule';
					break;
				}

                $iloc->mod = 'swfmodule';
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

				switch ($module->view) {
					case 'Headlines':
						$module->view = 'showall_headlines';
						break;
					case 'Summary':
						$module->view = 'showall_summary';
						break;
					default:
						$module->view = 'showall';
						break;
				}

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "news";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'newsmodule';
					break;
				}

                $iloc->mod = 'newsmodule';
                $newsitems = $old_db->selectArrays('newsitem', "location_data='".serialize($iloc)."'");
                if ($newsitems) {
                    foreach ($newsitems as $ni) {
                        unset($ni['id']);
                        $news = new news($ni);
                        $loc = expUnserialize($ni['location_data']);
                        $loc->mod = "news";
                        $news->location_data = serialize($loc);
                        $news->save();
						// default is to create with current time
                        $news->created_at = $ni['posted'];
                        $news->edited_at = $ni['edited'];
                        $news->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($ni['file_id'])) {
                            $oldfile = $old_db->selectArray('file', 'id='.$ni['file_id']);
                            $file = new expFile($oldfile);
                            $news->attachitem($file,'downloadable');
                        }
                    }
                }
				break;
            case 'resourcesmodule':

				switch ($module->view) {
					case 'One Click Download - Descriptive':
						$module->view = 'showall_quick_download_with_description';
						break;
					default:
						$module->view = 'showall';
						break;
				}

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "filedownload";
				if ($db->countObjects('filedownloads', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'resourcesmodule';
					break;
				}

                $iloc->mod = 'resourcesmodule';
                $resourceitems = $old_db->selectArrays('resourceitem', "location_data='".serialize($iloc)."'");
				if ($resourceitems) {
					foreach ($resourceitems as $ri) {
						unset($ri['id']);
						$filedownload = new filedownload($ri);
						$loc = expUnserialize($ri['location_data']);
						$loc->mod = "filedownload";
						$filedownload->title = $ri['name'];
						$filedownload->body = $ri['description'];
						$filedownload->downloads = $ri['num_downloads'];
						$filedownload->location_data = serialize($loc);
						if (!empty($ri['file_id'])) {
							$filedownload->save();
							@$this->msg['migrated'][$iloc->mod]['count']++;
							@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
							$oldfile = $old_db->selectArray('file', 'id='.$ri['file_id']);
							$file = new expFile($oldfile);
							$filedownload->attachitem($file,'downloadable');
							// default is to create with current time						
							$filedownload->created_at = $ri['posted'];
							$filedownload->edited_at = $ri['edited'];
							$filedownload->update();
						}
					}
				}
				break;
            case 'imagegallerymodule':

				switch ($module->view) {
					case 'Slideshow':
						$module->action = 'slideshow';
						$module->view = 'showall';
						break;
					default:
						$module->view = 'showall';
						break;
				}

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "photos";
				if ($db->countObjects('photo', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'imagegallerymodule';
					break;
				}

				$iloc->mod = 'imagegallerymodule';
                $galleries = $old_db->selectArrays('imagegallery_gallery', "location_data='".serialize($iloc)."'");
					if ($galleries) {
					foreach ($galleries as $gallery) {
						$gis = $old_db->selectArrays('imagegallery_image', "gallery_id='".$gallery['id']."'");
						//eDebug($gis,1);
						foreach ($gis as $gi) {
							$photo = new photo();
							//$loc = expUnserialize($gi['location_data']);
							$loc = expUnserialize($gallery['location_data']);
							$loc->mod = "photos";
							$photo->title = $gi['name'];
							if (empty($photo->title)) { $photo->title = 'Untitled'; }
							$photo->body = $gi['description'];
							$photo->alt = $gi['alt'];
							$photo->location_data = serialize($loc);
							if (!empty($gi['file_id'])) {
								$photo->save();
								@$this->msg['migrated'][$iloc->mod]['count']++;
								@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
								$file = new expFile($gi['file_id']);
								$photo->attachitem($file,'');
								$photo->created_at = $gi['posted'];
								$photo->edited_at = $gi['posted'];
								$photo->update();								
							}
						}
					}
				}
				break;
            case 'slideshowmodule':

                $module->action = 'slideshow';
                $module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "photos";
				if ($db->countObjects('photo', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'slideshowmodule';
					break;
				}

                $iloc->mod = 'slideshowmodule';
                $galleries = $old_db->selectArrays('imagegallery_gallery', "location_data='".serialize($iloc)."'");
				if ($galleries) {
					foreach ($galleries as $gallery) {
						$gis = $old_db->selectArrays('imagegallery_image', "gallery_id='".$gallery['id']."'");
						//eDebug($gis,1);
						foreach ($gis as $gi) {
							$photo = new photo();
							//$loc = expUnserialize($gi['location_data']);
							$loc = expUnserialize($gallery['location_data']);
							$loc->mod = "photos";
							$photo->title = $gi['name'];
							if (empty($photo->title)) { $photo->title = 'Untitled'; }
							$photo->body = $gi['description'];
							$photo->alt = $gi['alt'];
							$photo->location_data = serialize($loc);
							$te = $photo->find('first',"location_data='".$photo->location_data."'");
							if (empty($te)) {
								if (!empty($gi['file_id'])) {
									$photo->save();
									@$this->msg['migrated'][$iloc->mod]['count']++;
									@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
									$file = new expFile($gi['file_id']);
									$photo->attachitem($file,'');
									$photo->created_at = $gi['posted'];
									$photo->edited_at = $gi['posted'];
									$photo->update();								
								}
							}

						}
					}
				}
				break;
            case 'headlinemodule':

                $module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "headline";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'headlinemodule';
					break;
				}

                $iloc->mod = 'headlinemodule';
                $headlines = $old_db->selectObjects('headline', "location_data='".serialize($iloc)."'");
                if ($headlines) {
                    foreach ($headlines as $hl) {
                        $headline = new headline();
                        $loc = expUnserialize($hl->location_data);
                        $loc->mod = "headline";
                        $headline->location_data = serialize($loc);
                        $headline->title = $hl->headline;
                        $headline->poster = 1;
//                        $headline->created_at = time();
//                        $headline->edited_at = time();
                        $headline->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                    }
                }
				break;
            case 'weblogmodule':

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
					default:
						$module->view = 'showall';
						break;
				}

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "blog";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'weblogmodule';
					break;
				}

                $iloc->mod = 'weblogmodule';
                $blogitems = $old_db->selectArrays('weblog_post', "location_data='".serialize($iloc)."'");
                if ($blogitems) {
                    foreach ($blogitems as $bi) {
                        unset($bi['id']);
                        $post = new blog($bi);
                        $loc = expUnserialize($bi['location_data']);
                        $loc->mod = "blog";
                        $post->location_data = serialize($loc);
                        $post->save();
						// default is to create with current time						
                        $post->created_at = $bi['posted'];
                        $post->edited_at = $bi['edited'];
                        $post->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($bi['file_id'])) {
                            $oldfile = $old_db->selectArray('file', 'id='.$bi['file_id']);
                            $file = new expFile($oldfile);
                            $post->attachitem($file,'downloadable');
                        }
						$comments = $old_db->selectObjects('weblog_comment', "location_data='".serialize($iloc)."'");
						foreach($comments as $comment) {
							$newcomment = new expComments($comment);
							$newcomment->created_at = $comment['posted'];
							$newcomment->edited_at = $comment['edited'];
							$post->attachitem($newcomment,'');
						}
                    }
                }
				break;
            case 'faqmodule':

				$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "faq";
				if ($db->countObjects('faqs', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'faqmodule';
					break;
				}

                $iloc->mod = 'faqmodule';
                $faqs = $old_db->selectArrays('faq', "location_data='".serialize($iloc)."'");
                if ($faqs) {
                    foreach ($faqs as $fqi) {
                        unset($fqi['id']);
                        $faq = new faq($fqi);
                        $loc = expUnserialize($fqi['location_data']);
                        $loc->mod = "faq";
                        $faq->location_data = serialize($loc);
                        $faq->question = $fqi['question'];
                        $faq->answer = $fqi['answer'];
                        $faq->rank = $fqi['rank'];
                        $faq->include_in_faq = 1;
                        $faq->save();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                    }
                }
				break;
            case 'listingmodule':

				$module->view = 'showall';

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "portfolio";
				if ($db->countObjects($ploc->mod, "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'listingmodule';
					break;
				}

                $iloc->mod = 'listingmodule';
                $listingitems = $old_db->selectArrays('listing', "location_data='".serialize($iloc)."'");
                if ($listingitems) {
                    foreach ($listingitems as $li) {
                        unset($li['id']);
                        $listing = new portfolio($li);
						$listing->title = $li['name'];
                        $loc = expUnserialize($li['location_data']);
                        $loc->mod = "portfolio";
                        $listing->location_data = serialize($loc);
                        $listing->featured = true;
                        $listing->poster = 1;
                        $listing->body = "<p>".$li['summary']."</p>".$li['body'];
                        $listing->save();
						// default is to create with current time						
                        $listing->created_at = time();
                        $listing->edited_at = time();
                        $listing->update();
                        @$this->msg['migrated'][$iloc->mod]['count']++;
                        @$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
                        if (!empty($li['file_id'])) {
							$file = new expFile($li['file_id']);
							$listing->attachitem($file,'');
                        }
                    }
                }
				break;
            case 'contactmodule':  // convert to an old school form

				$module->view == "Default";

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "formmodule";
				if ($db->countObjects('formbuilder_form', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'contactmodule';
					break;
				}

                $iloc->mod = 'contactmodule';
                $contactform = $old_db->selectObject('contactmodule_config', "location_data='".serialize($iloc)."'");
				if ($contactform) {
					$loc = expUnserialize($contactform->location_data);
					$loc->mod = 'formmodule';
					$contactform->location_data = serialize($loc);
	//				$replyto_address = $contactform->replyto_address;
					unset($contactform->replyto_address);
	//				$from_address = $contactform->from_address;
					unset($contactform->from_address);
	//				$from_name = $contactform->from_name;
					unset($contactform->from_name);
					unset($contactform->use_captcha);
					$contactform->name = 'Send us an e-mail';
					$contactform->description = '';
					$contactform->response = $contactform->final_message;
					unset($contactform->final_message);
					$contactform->table_name ='';
					$contactform->is_email = true;
					$contactform->is_saved = false;
					$contactform->submitbtn = 'Send Message';
					$contactform->resetbtn = 'Reset';
					unset($contactform->id);
					$contactform->id = $db->insertObject($contactform, 'formbuilder_form');

					$addresses = $old_db->selectObjects('contact_contact', "location_data='".serialize($iloc)."'");
					foreach($addresses as $address) {
						unset($address->addressbook_contact_id);
						unset($address->contact_info);
						unset($address->location_data);
						$address->form_id = $contactform->id;
						$db->insertObject($address, 'formbuilder_address');
					}

					$report->name = $contactform->subject;
					$report->location_data = $contactform->location_data;
					$report->form_id = $contactform->id;
					$db->insertObject($report, 'formbuilder_report');
					// now add the controls to the form
					$control->name = 'name';
					$control->caption = 'Your Name';
					$control->form_id = $contactform->id;
					$control->data = 'O:11:"textcontrol":12:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:9:"Your Name";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:4:"name";}';
					$control->rank = 0;
					$control->is_readonly = 0;
					$control->is_static = 0;
					$db->insertObject($control, 'formbuilder_control');
					$control->name = 'email';
					$control->caption = 'Your Email';
					$control->data = 'O:11:"textcontrol":12:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:18:"Your Email Address";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:5:"email";}';
					$control->rank = 1;
					$db->insertObject($control, 'formbuilder_control');
					$control->name = 'subject';
					$control->caption = 'Subject';
					$control->data = 'O:11:"textcontrol":12:{s:4:"size";i:0;s:9:"maxlength";i:0;s:7:"caption";s:7:"Subject";s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:1;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:6:"filter";s:0:"";s:10:"identifier";s:7:"subject";}';
					$control->rank = 2;
					$db->insertObject($control, 'formbuilder_control');
					$control->name = 'message';
					$control->caption = 'Message';
					$control->data = 'O:17:"texteditorcontrol":12:{s:4:"cols";i:60;s:4:"rows";i:8;s:9:"accesskey";s:0:"";s:7:"default";s:0:"";s:8:"disabled";b:0;s:8:"required";b:0;s:8:"tabindex";i:-1;s:7:"inError";i:0;s:4:"type";s:4:"text";s:8:"maxchars";i:0;s:10:"identifier";s:7:"message";s:7:"caption";s:7:"Message";}';
					$control->rank = 3;
					$db->insertObject($control, 'formbuilder_control');

					@$this->msg['migrated'][$iloc->mod]['count']++;
					@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
				}
				break;
            case 'youtubemodule':

				//check to see if it's already pulled in (circumvent !is_original)
				$ploc = $iloc;
				$ploc->mod = "youtube";
				if ($db->countObjects('youtube', "location_data='".serialize($ploc)."'")) {
					$iloc->mod = 'youtubemodule';
					break;
				}

				$iloc->mod = 'youtubemodule';
                $videos = $old_db->selectArrays('youtube', "location_data='".serialize($iloc)."'");
				if ($videos) {
					foreach ($videos as $vi) {
						unset ($vi['id']);
						$video = new youtube($vi);
						//$loc = expUnserialize($vi['location_data']);
						$loc = expUnserialize($vi['location_data']);
						$loc->mod = "youtube";
						$video->title = $vi['name'];
						if (empty($video->title)) { $video->title = 'Untitled'; }
						$video->location_data = serialize($loc);
						$yt = explode("watch?v=",$vi['url']);
						if (empty($yt[1])) {
							break;
						} else {
							$ytid = $yt[1];			
						}
						unset ($video->url);
						$video->embed_code = '<iframe title="YouTube video player" width="'.$vi['width'].'" height="'.$vi['height'].'" src="http://www.youtube.com/embed/'.$ytid.'" frameborder="0" allowfullscreen></iframe>';
						$video->save();
						@$this->msg['migrated'][$iloc->mod]['count']++;
						@$this->msg['migrated'][$iloc->mod]['name'] = $this->new_modules[$iloc->mod];
					}
				}
				break;
            default:
                @$this->msg['noconverter'][$iloc->mod]++;
				break;
		}
        // quick check for non hard coded modules
        // We add a container if they're not hard coded.
        (!$hc) ? $this->add_container($iloc,$module) : "";

        return $module;
    }

	// pull over extra/related data required for old school modules
    private function pulldata($iloc, $module) {
        global $db;
        $old_db = $this->connect();

        switch ($iloc->mod) {
            case 'calendarmodule':
				if ($db->countObjects('calendar', "location_data='".serialize($iloc)."'")) {
					break;
				}
                $events = $old_db->selectObjects('eventdate', "location_data='".serialize($iloc)."'");
                foreach($events as $event) {
                    $res = $db->insertObject($event, 'eventdate');
					if ($res) { @$this->msg['migrated'][$iloc->mod]['count']++; }
                }
                $cals = $old_db->selectObjects('calendar', "location_data='".serialize($iloc)."'");
                foreach($cals as $cal) {
                    unset($cal->allow_registration);
                    unset($cal->registration_limit);
                    unset($cal->registration_allow_multiple);
                    unset($cal->registration_cutoff);
                    unset($cal->registration_price);
                    unset($cal->registration_count);
                    $db->insertObject($cal, 'calendar');
                }
                $configs = $old_db->selectObjects('calendarmodule_config', "location_data='".serialize($iloc)."'");
                foreach ($configs as $config) {
					$config->enable_categories = 0;
					$config->enable_tags = 0;
                    // unset($config->enable_ical);
                    // unset($config->rss_limit);
                    // unset($config->rss_cachetime);
                    // unset($config->reminder_notify);
                    // unset($config->email_title_reminder);
                    // unset($config->email_from_reminder);
                    // unset($config->email_address_reminder);
                    // unset($config->email_reply_reminder);
                    // unset($config->email_showdetail);
                    // unset($config->email_signature);
                    $db->insertObject($config, 'calendarmodule_config');
                }
				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
				break;
            case 'simplepollmodule':
				if ($db->countObjects('poll_question', "location_data='".serialize($iloc)."'")) {
					break;
				}
                $questions = $old_db->selectObjects('poll_question', "location_data='".serialize($iloc)."'");
                foreach($questions as $question) {
                    $db->insertObject($question, 'poll_question');
					$answers = $old_db->selectObjects('poll_answer', "question_id='".$question->id."'");
					foreach($answers as $answer) {
						$db->insertObject($answer, 'poll_answer');
					}
					$timeblocks = $old_db->selectObjects('poll_timeblock', "question_id='".$question->id."'");
					foreach($timeblocks as $timeblock) {
						$db->insertObject($timeblock, 'poll_timeblock');
					}
					@$this->msg['migrated'][$iloc->mod]['count']++;
                }
                $configs = $old_db->selectObjects('simplepollmodule_config', "location_data='".serialize($iloc)."'");
                foreach ($configs as $config) {
                    $db->insertObject($config, 'simplepollmodule_config');
                }
				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
				break;
            case 'formmodule':
				if ($db->countObjects('formbuilder_form', "location_data='".serialize($iloc)."'")) {
					break;
				}
                $form = $old_db->selectObject('formbuilder_form', "location_data='".serialize($iloc)."'");
				$oldformid = $form->id;
				unset($form->id);
                $form->id = $db->insertObject($form, 'formbuilder_form');
				@$this->msg['migrated'][$iloc->mod]['count']++;
				$addresses = $old_db->selectObjects('formbuilder_address', "form_id='".$oldformid."'");
                foreach($addresses as $address) {
					unset($address->id);
					$address->form_id = $form->id;
                    $db->insertObject($address, 'formbuilder_address');
				}
				$controls = $old_db->selectObjects('formbuilder_control', "form_id='".$oldformid."'");
                foreach($controls as $control) {
					unset($control->id);
					$control->form_id = $form->id;
                    $db->insertObject($control, 'formbuilder_control');
				}
				$reports = $old_db->selectObjects('formbuilder_report', "form_id='".$oldformid."'");
                foreach($reports as $report) {
					unset($report->id);
					$report->form_id = $form->id;
                    $db->insertObject($report, 'formbuilder_report');
				}
				if (isset($form->table_name)) {
					if (isset($this->params['wipe_content'])) {
						$db->delete('formbuilder_'.$form->table_name);
					}
					formbuilder_form::updateTable($form);
					$records = $old_db->selectObjects('formbuilder_'.$form->table_name, 1);
					foreach($records as $record) {
						$db->insertObject($record, 'formbuilder_'.$form->table_name);
					}
				}
				@$this->msg['migrated'][$iloc->mod]['name'] = $iloc->mod;
				break;
        }
    }

	// used to create containers for new modules
    private function add_container($iloc,$m) {
        global $db;
		if ($iloc->mod != 'contactmodule') {
			$iloc->mod = $this->new_modules[$iloc->mod];
			$m->internal = (isset($m->internal) && strstr($m->internal,"Controller")) ? $m->internal : serialize($iloc);
			$m->action = isset($m->action) ? $m->action : 'showall';
			$m->view = isset($m->view) ? $m->view : 'showall';
			if ($m->view == "Default") {
				$m->view = 'showall';
			}
		} else {  // must be old school contactmodule
			$iloc->mod = $this->new_modules[$iloc->mod];
			$m->internal = serialize($iloc);
		}
        $db->insertObject($m, 'container');
    }

	// connect to old site's database
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
            flash ('error', 'You are missing some required database connectin information.  Please enter DB information.');
            redirect_to (array('controller'=>'migration', 'action'=>'configure'));
        }

       $database = exponent_database_connect($this->config['username'],$this->config['password'],$this->config['server'].':'.$this->config['port'],$this->config['database']);

       if (empty($database->havedb)) {
            flash ('error', 'An error was encountered trying to connect to the database you specified. Please check your DB config.');
            redirect_to (array('controller'=>'migration', 'action'=>'configure'));
       }

       $database->prefix = $this->config['prefix']. '_';;
       return $database;
    }
}

?>