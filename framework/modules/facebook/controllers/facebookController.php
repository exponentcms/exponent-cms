<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @package    Modules
 */

class facebookController extends expController {
    public $useractions = array(
        'showall' => 'Facebook Like'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags')
    public $codequality = 'beta';

    static function displayname() {
        return gt("Facebook");
    }

    static function description() {
        return gt("Display Facebook Widgets");
    }

    static function author() {
        return "Dave Leffler";
    }

    public function showall() {
        global $router;

        expHistory::set('viewable', $this->params);
        if (!empty($this->config['url_type'])) {
            if ($this->config['url_type'] == 1) {
                $url = $router->current_url;
            } else {
                $url = $this->config['facebook_url'];
            }
        } else $url = URL_FULL;
        assign_to_template(array(
            'facebook_url'=>$url
        ));

    }

}

?>