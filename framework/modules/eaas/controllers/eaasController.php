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

/**
 * @subpackage Controllers
 * @package Modules
 */

class eaasController extends expController {
    //public $basemodel_name = '';
    public $useractions = array(
        'showall'=>'Install Service API'
        // 'tags'=>"Tags",
        // 'authors'=>"Authors",
        // 'dates'=>"Dates",
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
//        'module',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
//    public $tabs = array(
//        'aboutus'=>'About Us',
//        'blog'=>'Blog',
//        'photo'=>'Photos',
//        'media'=>'Media',
//        'event'=>'Events',
//        'filedownload'=>'File Downloads',
//        'news'=>'News'
//    );
    public $tabs = array();
    public $sources = array();
    protected $data = array();

    static function displayname() { return gt("Exponent as a Service"); }

    static function description() { return gt("This module allows you make service calls and return JSON for parts of Exponent"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
    static function hasSources() { return false; }  // must be explicitly added by config['add_source'] or config['aggregate']
//    static function isSearchable() { return true; }

    static function requiresConfiguration()
    {
        return true;
    }

    public function showall() {
        expHistory::set('viewable', $this->params);
        $info = array();
        $info['config'] = $this->config;
        $info['apikey'] = base64_encode(serialize($this->loc));

        assign_to_template(array('info'=>$info));
    }

    public function api() {
        if (empty($this->params['apikey'])) {
            $_REQUEST['apikey'] = true;  // set this to force an ajax reply
            $ar = new expAjaxReply(550, 'Permission Denied', 'You need an API key in order to access Exponent as a Service', null);
            $ar->send();  //FIXME this doesn't seem to work correctly in this scenario
        } else {
            $key = expUnserialize(base64_decode(urldecode($this->params['apikey'])));
            if (is_object($key) && $key->mod === "eaas") {
                preg_match('/[a-zA-Z0-9_@]*/', $key->src, $matches);
                $key->src = $matches[0];
                $cfg = new expConfig(expCore::makeLocation($key->mod, $key->src));
                $this->config = $cfg->config;
            }
            if(empty($cfg->id)) {
                $ar = new expAjaxReply(550, 'Permission Denied', 'Incorrect API key or Exponent as a Service module configuration missing', null);
                $ar->send();
            } else {
                if (!empty($this->params['get'])) {
                    $this->handleRequest();
                } else {
                    $ar = new expAjaxReply(200, 'ok', 'Your API key is working, no data requested', null);
                    $ar->send();
                }
            }
        }
    }

    private function handleRequest() {
        global $available_controllers;

        // create eaas module list
        foreach ($available_controllers as $key => $path) {
            if (strpos($key, "Controller") !== false) {
                $c = new $key();
                if ($c::canHandleEAAS()) {
                    $this->tabs[$c->baseclassname] = $c->name();
                    $this->sources[$c->baseclassname] = $c;
                }
            }
        }

        // handle about request
        if ($this->params['get'] === 'aboutus') {
            $ar = new expAjaxReply(200, 'ok', $this->aboutUs(), null);
            $ar->send();
        }

        // check for a valid module
        if (!array_key_exists($this->params['get'], $this->tabs)) {
            $ar = new expAjaxReply(400, 'Bad Request', 'No service available for your request', null);
            $ar->send();
        }

        // check for aggregate on modules with sources
        if (empty($this->config[$this->params['get'].'_aggregate']) && $this->sources[$this->params['get']]->hasSources() !== false) {
            $ar = new expAjaxReply(400, 'Bad Request', 'No modules assigned to requested service', null);
            $ar->send();
        }

        // get the data from the module
        $this->data = $this->sources[$this->params['get']]->eaasData($this->params, $this->aggregateWhereClause($this->params['get']));

        // apply the standard banner info to the response
        $this->getImageBody($this->params['get']);
        $ar = new expAjaxReply(200, 'ok', $this->data, null);
        $ar->send();
    }

    /**
     * Return the About Us data along with a quick item count by module
     * @return array
     */
    private function aboutUs() {
        if (DEVELOPMENT) {
            $counts = array();
            foreach ($this->tabs as $key => $name) {
                if (empty($this->config[$key.'_aggregate']) && $this->sources[$key]->hasSources() !== false) {
                    $counts[$key] = -1;
                } else {
                    $data = $this->sources[$key]->eaasData(null, $this->aggregateWhereClause($key));
                    $counts[$key] = count($data['records']);
                }
            }
            $this->data = $counts;
        }
        $this->data['records'] = array();  // no actual records, basically a banner
        $this->getImageBody($this->params['get']);
        return $this->data;
    }

//    private function news() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $news = new news($this->params['id']);
//            $this->data['records'] = $news;
//        } else {
//            $news = new news();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $order = isset($this->params['order']) ? $this->params['order'] : 'publish DESC';
//            $items = $news->find('all', $this->aggregateWhereClause('news'), $order, $limit);
//            $this->data['records'] = $items;
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

//    private function media() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $media = new media($this->params['id']);
//            $this->data['records'] = $media;
//        } else {
//            $media = new media();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at ASC';
//
//            $items = $media->find('all', $this->aggregateWhereClause('media'), $order, $limit);
//            $this->data['records'] = $items;
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

//    private function filedownload() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $filedownload = new filedownload($this->params['id']);
//            $this->data['records'] = $filedownload;
//        } else {
//            $filedownload = new filedownload();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at ASC';
//
//            $items = $filedownload->find('all', $this->aggregateWhereClause('filedownload'), $order, $limit);
//            $this->data['records'] = $items;
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

//    private function photo() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $photo = new photo($this->params['id']);
//            $this->data['records'] = $photo;
//        } else {
//            $photo = new photo();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $order = isset($this->params['order']) ? $this->params['order'] : 'rank';
//            $items = $photo->find('all', $this->aggregateWhereClause('photo'), $order, $limit);
//            $this->data['records'] = $items;
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

//    private function blog() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $blog = new blog($this->params['id']);
//            $this->data['records'] = $blog;
//        } else {
//            $blog = new blog();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $order = isset($this->params['order']) ? $this->params['order'] : 'publish DESC';
//            $items = $blog->find('all', $this->aggregateWhereClause('blog'), $order, $limit);
//            $this->data['records'] = $items;
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

//    private function event() {
//        $this->data = array();  // initialize
//        if (!empty($this->params['id'])) {
//            $event = new event($this->params['id']);
//            $this->data['records'] = $event;
//        } else {
//            $event = new event();
//
//            // figure out if we should limit the results
//            if (isset($this->params['limit'])) {
//                $limit = $this->params['limit'] === 'none' ? null : $this->params['limit'];
//            } else {
//                $limit = '';
//            }
//
//            $items = $event->find('upcoming', $this->aggregateWhereClause('event'), false, false);  //new 'upcoming' type of find
//            if (!empty($limit))
//                $items = array_slice($items, 0, $limit);  // limit number of items, not numberof days
//            $this->data['records'] = $items;
//        }
//
//        if (!empty($this->params['groupbydate'])&&!empty($items)) {  // aggregate by day like with regular calendar
//            $this->data['records'] = array();
//            foreach ($items as $value) {
//                $this->data['records'][date('r',$value->eventdate[0]->date)][] = $value;
//                // edebug($value);
//            }
//        }
//
//        $this->getImageBody($this->params['get']);
//        return $this->data;
//    }

    function configure() {
        global $available_controllers;

        expHistory::set('editable', $this->params);
        parent::configure();
        $order = isset($this->params['order']) ? $this->params['order'] : 'section';
        $dir = isset($this->params['dir']) ? $this->params['dir'] : '';

        $views = expTemplate::get_config_templates($this, $this->loc); // configure views found within eaas module

        // add module configuration views
        foreach ($available_controllers as $key => $path) {
            if (strpos($key, "Controller") !== false) {
                $c = new $key();
                if ($c::canHandleEAAS()) {
                    $config = $c->eaasConfig();
                    if (!empty($config)) {
                        $views[$c->baseclassname] = $c->eaasConfig();
                        $this->tabs[$c->baseclassname] = $c->name();
                    }
                }
            }
        }

        // get aggregate
        $pullable = array();
        $page = array();
        foreach ($this->tabs as $tab => $name) {
            $pullable[$tab] = expModules::listInstalledControllers($tab);
            $page[$tab] = new expPaginator(array(
                'controller'=>$tab.'Controller',
                'action' => $this->params['action'],
                'records'=>$pullable[$tab],
                'limit'=>count($pullable[$tab]),
                'order'=>$order,
                'dir'=>$dir,
                'columns'=>array(gt('Title')=>'title',gt('Page')=>'section'),
            ));
            if (@is_null($this->config[$tab . '_aggregate'])) {
                $this->config[$tab . '_aggregate'] = array();
            }

            $this->configImage($tab);  // fix attached files for proper display of file manager control
            if (!isset($this->config[$tab . '_aggregate'])) {
                $this->config[$tab . '_aggregate'] = array();
            }
        }

        assign_to_template(array(
            'config'=>$this->config, // though already assigned in controllertemplate, we need to update expFiles
            'pullable'=>$pullable,
            'page'=>$page,
            'views'=>$views
        ));
    }

    private function configImage($tab) {
        if (isset($this->config['expFile'][$tab.'_image']) && count($this->config['expFile'][$tab.'_image']) > 0) {
            if (!is_object($this->config['expFile'][$tab.'_image'][0])) {
                $ftmp[] = new expFile($this->config['expFile'][$tab . '_image'][0]);
                $this->config['expFile'][$tab . '_image'][] = $ftmp;
//            } else {
//                $this->config['expFile'][$tab . '_image'][] = $this->config['expFile'][$tab . '_image'][0];
            }
        } else {
            $this->config['expFile'][$tab.'_image'] = array();
        }
    }

    public function saveconfig() {
        foreach ($this->params['expFile'] as $expfile=>$tab) {
            foreach ($tab as $key=>$file) {
                if (!is_numeric($file)) {
                    unset($this->params['expFile'][$expfile][$key]);
                }
            }
        }
        parent::saveconfig();
   	}

    private function getImageBody($tab) {
        // create an empty 'banner' object to prevent errors in caller
        $this->data['banner']['obj'] = new stdClass();
        $this->data['banner']['obj']->url = null;
        $this->data['banner']['md5'] = null;

        if (count(@$this->config['expFile'][$tab . '_image']) > 0) {
            if (is_numeric($this->config['expFile'][$tab . '_image'][0])) {
                $img = new expFile($this->config['expFile'][$tab . '_image'][0]);
            } elseif (is_object($this->config['expFile'][$tab . '_image'][0])) {
                $img =$this->config['expFile'][$tab . '_image'][0];
            }
            if ($img) {
                $this->data['banner']['obj'] = $img;
                $this->data['banner']['md5'] = md5_file($img->path);
            }
        }
        $this->data['html'] = $this->config[$tab . '_body'];
    }

    /**
     * Only return selected modules
     *
     * @param string $type
     * @return string
     */
    function aggregateWhereClause($type='') {
        $sql = '(0';  // simply to offset the 'OR' added in loop
        if (!empty($this->config[$type . '_aggregate'])) {
            foreach ($this->config[$type . '_aggregate'] as $src) {
                $loc = expCore::makeLocation($type, $src);
                $sql .= " OR location_data ='" . serialize($loc)."'";
            }

        }
        $sql .= ')';
        $model = $this->basemodel_name;
        if (ENABLE_WORKFLOW && $this->$model->needs_approval) {
            $sql .= ' AND approved=1';
        }

        return $sql;
    }

}

?>