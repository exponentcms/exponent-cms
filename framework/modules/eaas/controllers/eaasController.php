<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
        'module',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
    public $add_permissions = array(
        // 'approve'=>"Approve Comments"
    );

    public $tabs = array(
        'aboutus'=>'About Us', 
        'blog'=>'Blog', 
        'photo'=>'Photos', 
        'media'=>'Media',
        'youtube'=>'YouTube Videos',  //FIXME to be removed
        'event'=>'Events', 
        'filedownload'=>'File Downloads', 
        'news'=>'News'
    );

    static function displayname() { return gt("Exponent as a Service"); }

    static function description() { return gt("This module allows you make service calls and return JSON for parts of Exponent"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
    static function hasSources() { return false; }  // must be explicitly added by config['add_source'] or config['aggregate']
//    static function isSearchable() { return true; }

    public function showall() {
        expHistory::set('viewable', $this->params);
        $info = array();
        $info['config'] = $this->config;
        $info['apikey'] = base64_encode(serialize($this->loc));

        assign_to_template(array('info'=>$info));
    }

    public function api() {

        if (empty($this->params['apikey'])) {
            $ar = new expAjaxReply(550, 'Permission Denied', 'You need an API key in order to access Exponent as a Service', null);
            $ar->send();
        } else {

            $key = expUnserialize(base64_decode(urldecode($this->params['apikey'])));

            $cfg = new expConfig($key);
            $this->config = $cfg->config;

            if(empty($cfg->id)) {
                $ar = new expAjaxReply(550, 'Permission Denied', 'Your API key is bunk. I bet you\'ll figure it out.', null);
                $ar->send();
            } else{
                if ($this->params['get']) {
                    $this->handleRequest();
                } else {
                    $ar = new expAjaxReply(200, 'ok', 'Your API key ia working', null);
                    $ar->send();
                }
            };
        }
    }

    private function handleRequest() {
        switch ($this->params['get']) {
            case 'aboutus':
                $data = $this->aboutUs();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'news':
                $data = $this->news();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'photo':
                $data = $this->photo();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'media':
                $data = $this->media();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'youtube':  //FIXME to be removed
                $data = $this->youtube();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'filedownload':
                $data = $this->filedownload();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'blog':
                $data = $this->blog();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            case 'event':
                $data = $this->event();
                $ar = new expAjaxReply(200, 'ok', $data, null);
                $ar->send();
                break;
            default:
                $ar = new expAjaxReply(400, 'Bad Request', 'no service for your request available', null);
                $ar->send();
                break;
        }
    }

    private function aboutUs() {
        $data = array();

        $img = $this->getImage($this->params['get']);

        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }


        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function news() {
        $data = array();
        if (!empty($this->params['id'])) {
            $news = new news($this->params['id']);
            $data['records'] = $news;

        } else {
            $news = new news();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       
            
            $order = isset($this->params['order']) ? $this->params['order'] : 'publish DESC';
            $items = $news->find('all', $this->aggregateWhereClause('news'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function youtube() {  //FIXME must replace with media player and then removed
        $data = array();
        if (!empty($this->params['id'])) {
            $youtube = new youtube($this->params['id']);
            $data['records'] = $youtube;

        } else {
            $youtube = new youtube();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       

            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at ASC';

            $items = $youtube->find('all', $this->aggregateWhereClause('youtube'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function media() {
        $data = array();
        if (!empty($this->params['id'])) {
            $media = new media($this->params['id']);
            $data['records'] = $media;

        } else {
            $media = new media();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }

            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at ASC';

            $items = $media->find('all', $this->aggregateWhereClause('media'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function filedownload() {
        $data = array();
        if (!empty($this->params['id'])) {
            $filedownload = new filedownload($this->params['id']);
            $data['records'] = $filedownload;

        } else {
            $filedownload = new filedownload();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       

            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at ASC';

            $items = $filedownload->find('all', $this->aggregateWhereClause('filedownload'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function photo() {
        $data = array();
        if (!empty($this->params['id'])) {
            $photo = new photo($this->params['id']);
            $data['records'] = $photo;

        } else {
            $photo = new photo();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       
            
            $order = isset($this->params['order']) ? $this->params['order'] : 'rank';
            $items = $photo->find('all', $this->aggregateWhereClause('photo'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function blog() {
        $data = array();
        if (!empty($this->params['id'])) {
            $blog = new blog($this->params['id']);
            $data['records'] = $blog;

        } else {
            $blog = new blog();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       
            
            $order = isset($this->params['order']) ? $this->params['order'] : 'publish DESC';
            $items = $blog->find('all', $this->aggregateWhereClause('blog'), $order, $limit);
            $data['records'] = $items;
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    private function event() {
        $data = array();
        if (!empty($this->params['id'])) {
            $event = new event($this->params['id']);
            $data['records'] = $event;

        } else {
            $event = new event();

            // figure out if should limit the results
            if (isset($this->params['limit'])) {
                $limit = $this->params['limit'] == 'none' ? null : $this->params['limit'];
            } else {
                $limit = '';
            }       
            
            $order = isset($this->params['order']) ? $this->params['order'] : 'created_at';
            $items = $event->find('all', $this->aggregateWhereClause('event'), $order, $limit);
            $data['records'] = $items;
        }

        if (!empty($this->params['groupbydate'])&&!empty($items)) {
            $data['records'] = array();
            foreach ($items as $value) {
                $data['records'][date('r',$value->eventdate[0]->date)][] = $value;
                // edebug($value);
            }
        }

        $img = $this->getImage($this->params['get']);
        if ($img) {
            $data['banner']['obj'] = $img;
            $data['banner']['md5'] = md5_file($img->path);
        }

        $data['html'] = $this->config[$this->params['get'].'_body'];
        return $data;
    }

    function configure() {
        expHistory::set('editable', $this->params);
        $order = isset($this->params['order']) ? $this->params['order'] : 'section';
        $dir = isset($this->params['dir']) ? $this->params['dir'] : '';
        
        $views = get_config_templates($this, $this->loc);
        $pullable = array();
        $page = array();

        foreach ($this->tabs as $tab => $name) {
            // news tab
            if ($tab!='aboutus') {
                $pullable[$tab] = expModules::listInstalledControllers($tab, $this->loc);
                $page[$tab] = new expPaginator(array(
                    'controller'=>$tab.'Controller',
                    'records'=>$pullable[$tab],
                    'limit'=>count($pullable[$tab]),
                    'order'=>$order,
                    'dir'=>$dir,
                    'columns'=>array(gt('Title')=>'title',gt('Page')=>'section'),
                ));
            }

            $this->configImage($tab);
        }
        // edebug($this->config['expFile']);

        assign_to_template(array('config'=>$this->config, 'pullable'=>$pullable, 'page'=>$page, 'views'=>$views));
    }

    private function configImage($tab) {
        if (count(@$this->config['expFile'][$tab.'_image'])>1) {
            $ftmp[] = new expFile($this->config['expFile'][$tab.'_image'][0]);
            $this->config['expFile'][$tab.'_image'] = $ftmp;
        } else {
            $this->config['expFile'][$tab.'_image'] = array();
        }
    }

    private function getImage($tab) {
        if (count(@$this->config['expFile'][$tab.'_image'])>1) {
            return new expFile($this->config['expFile'][$tab.'_image'][0]);
        } else {
            return false;
        }
    }

    function aggregateWhereClause($type='') {
        $sql = '';
        $sql .= '(';
        $sql .= "location_data ='".serialize($this->loc)."'";

        if (!empty($this->config[$type.'_aggregate'])) {
            foreach ($this->config[$type.'_aggregate'] as $src) {
                $loc = expCore::makeLocation($type, $src);
                $sql .= " OR location_data ='".serialize($loc)."'";
            }
            
            $sql .= ')';
        }       
        $model = $this->basemodel_name;
        if ($this->$model->needs_approval && ENABLE_WORKFLOW) {
            $sql .= ' AND approved=1';
        }

        return $sql;
    }
}

?>