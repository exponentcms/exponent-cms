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
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags''twitter',)
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

    public static function postStatus($params=array()) {
        if (!empty($params)) {
            // Include facebook class
            require_once(BASE . "external/facebook-php-sdk-3.2.2/src/facebook.php");

            // configuration
//            $desc = 'Facebook constantly changes their SDK and methods for communicating with Facebook. The script in this post supports the latest Facebook authentication changes that will be implemented i October 2012.';
//            $pic = 'http://blog.phpinfinite.com/wp-content/uploads/2012/11/post_to_facebook_from_php.jpg';
//            $action_name = 'Go to PHP Infinite';
//            $action_link = 'http://blog.phpinfinte.com';

            $post = new $params['model']($params['id']);

            $facebook = new Facebook(array(
                'appId' => $params['config']['app_id'],
                'secret' => $params['config']['app_secret'],
                'cookie' => true,
            ));

            $fuser = $facebook->getUser();

            // Contact Facebook and get token
            if ($fuser) {
                // you're logged in, and we'll get user acces token for posting on the wall
                try {
                    $page_info = $facebook->api("/".$params['config']['facebook_page']."?fields=access_token");
                    if (!empty($page_info['access_token'])) {
                        $attachment = array(
                            'access_token' => $page_info['access_token'],
                            'message' => expString::summarize($post->body),
                            'name' => $post->title,
                            'link' => expCore::makeLink(array('controller'=>$params['orig_controller'], 'action'=>'show','title'=>$post->sef_url)),
//                            'description' => $desc,
//                            'picture'=>$pic,
//                            'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
                        );

                        $status = $facebook->api("/".$params['config']['facebook_page']."/feed", "post", $attachment);
                    } else {
                        $status = 'No Facebook access token received';
                    }
                } catch (FacebookApiException $e) {
                    error_log($e);
                    $fuser = null;
                }
            } else {
                // you're not logged in, the application will try to log in to get a access token
                header("Location:{$facebook->getLoginUrl(array('scope' => 'photo_upload,user_status,publish_stream,user_photos,manage_pages'))}");
                $status = 'Permissions not yet set on Facebook';
                exit();
            }
            flash('message', $status);
        }
    }

}

?>