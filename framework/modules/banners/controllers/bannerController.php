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

class bannerController extends expController {
    public $useractions = array(
        'showall'=>'Display Banner(s)'
    );
    protected $manage_permissions = array(
        'reset' => 'Reset Stats'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'module_title',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Banners"); }
    static function description() { return gt("Display banners on your website and track 'clicks'."); }

    public function showall() {
        $banners = array();
        if (!empty($this->config['banners'])) {
            // only show banners that this module is configured to show.
            // do not show banners that have gone over their impression limit
            // do not show banners that have gone over their click limit
            // randomly grab one banner to be displayed
            // increase the impression count for this banner
            $where = 'id IN ('.implode(',', $this->config['banners']).')';
            $where .= ' AND (impression_limit > impressions || impression_limit=0)';
            $where .= ' AND (click_limit > clicks || click_limit=0)';
            $limit = isset($this->config['limit']) ? $this->config['limit'] : 1;
            $banners = $this->banner->find('all', $where , 'RAND()', $limit);
            foreach($banners as $banner) {
                $banner->increaseImpressions();
            }
        }

        // assign banner to the template and show it!
        assign_to_template(array(
            'banners'=>$banners
        ));
    }

    public function click() {
        $banner = new banner($this->params['id']);
        $banner->increaseClicks();
        redirect_to($banner->url);
    }

    public function create() {
//        global $db;
        //make sure we have companies.
//        $count = $db->countObjects('companies');
        $comp = new company();
        $count = $comp->find('count');
        if ($count < 1) {
            flash('message', gt('There are no companies created yet.  You need to create at least one company first.'));
            redirect_to(array('controller'=>'company', 'action'=>'edit'));
//            $this->edit();
        } else {
            parent::create();
        }
    }

    public function manage() {
        expHistory::set('manageable', $this->params);

        // build out a SQL query that gets all the data we need and is sortable.
        $sql  = 'SELECT b.*, c.title as companyname, f.expfiles_id as file_id ';
        $sql .= 'FROM '.DB_TABLE_PREFIX.'_banner b, '.DB_TABLE_PREFIX.'_companies c , '.DB_TABLE_PREFIX.'_content_expFiles f ';
        $sql .= 'WHERE b.companies_id = c.id AND (b.id = f.content_id AND f.content_type="banner")';

		$page = new expPaginator(array(
			'model'=>'banner',
			'sql'=>$sql,
			'order'=>'title',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
			'columns'=>array(
                gt('Title')=>'title',
                gt('Company')=>'companyname',
                gt('Impressions')=>'impressions',
                gt('Clicks')=>'clicks'
            )
        ));

		assign_to_template(array(
            'page'=>$page
        ));
    }

    public function configure() {
        $this->config['defaultbanner'] = array();
        if (!empty($this->config['defaultbanner_id'])) {
            $this->config['defaultbanner'][] = new expFile($this->config['defaultbanner_id']);
        }
	    parent::configure();
	    $banners = $this->banner->find('all', null, 'companies_id');
	    assign_to_template(array(
            'banners'=>$banners,
            'title'=>static::displayname()
        ));
	}

	public function saveconfig() {
	    $this->params['defaultbanner_id'] = isset($this->params['expFile'][0]) ? $this->params['expFile'][0] : 0;
   	    parent::saveconfig();
	}

	public function export() {
        // gather all the data
        $banners = $this->banner->find('all');
        $out = '"Banner ID","Banner Title","Banner URL","Company Name","Impression Limit","Click Limit","Impressions","Clicks"' . "\n";
        foreach ($banners as $l) {
            $out .='"'.$l->id.'","'.$l->title.'","'.$l->url.'","'.$l->company->title.'","'.$l->impression_limit.'","'.$l->click_limit.'","'.$l->impressions.'","'.$l->clicks.'"' . "\n";
        }

        // open the file
        $dir =  BASE.'tmp';
        $filename = 'banner_export' . date("m-d-Y") . '.csv';
        $fh = fopen ($dir .'/'. $filename, 'w');

        // Put all values from $out to export.csv.
        fputs($fh, $out);
        fclose($fh);

        // push the file to the user
        $export = new expFile(array('directory'=>$dir, 'filename'=>$filename));  //FIXME we are using a full path BASE instead of relative to root
        expFile::download($export);
    }

    function reset_stats() {
//        global $db;

        // reset the counters
//        $db->sql ('UPDATE '.$db->prefix.'banner SET impressions=0 WHERE 1');
        banner::resetImpressions();
//        $db->sql ('UPDATE '.$db->prefix.'banner SET clicks=0 WHERE 1');
        banner::resetClicks();

        // let the user know we did stuff.
        flash('message', gt("Banner statistics reset."));
        expHistory::back();
    }

}

?>