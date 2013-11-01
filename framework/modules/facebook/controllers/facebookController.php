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
//                'fileUpload' => true     // this is important !
            ));
            $accesstoken = $facebook->getAccessToken();
            if (!empty($accesstoken)) {
//            $user = $facebook->getUser();
//            if (!empty($user)) {
                try {
                    $attachment = array(
                        'access_token' => $accesstoken,
                        'message' => expString::summarize($post->body),
                        'name' => $post->title,
                        'link' => expCore::makeLink(array('controller'=>$params['orig_controller'], 'action'=>'show','title'=>$post->sef_url)),
//                        'description' => $desc,
//                        'picture'=>$pic,
//                        'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
                    );
                    $status = $facebook->api("/".$params['config']['facebook_page']."/feed", "post", $attachment);
                    if (!empty($status)) $status = gt('New Facebook Status posted');
                    flash('message', $status);
                } catch (FacebookApiException $e) {
//                    header("Location:{$facebook->getLoginUrl(array('scope' => 'photo_upload,user_status,publish_stream,user_photos,manage_pages'))}");
                    $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". $params['config']['app_id'] . "&redirect_uri=" . urlencode(URL_FULL) . "&scope=publish_stream,offline_access,publish_actions,user_photos,photo_upload,user_status,manage_pages,create_event". "&state=" . $_SESSION['fb_state'];
                    echo("<script> window.location.href='" . $dialog_url . "'</script>");
                    error_log($e);
                    flash('error', $e->getMessage());
                }
            } else {
                // you're not logged in, the application will try to log in to get a access token
//                header("Location:{$facebook->getLoginUrl(array('scope' => 'photo_upload,user_status,publish_stream,user_photos,manage_pages'))}");
                $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". $params['config']['app_id'] . "&redirect_uri=" . urlencode(URL_FULL) . "&scope=publish_stream,offline_access,publish_actions,user_photos,photo_upload,user_status,manage_pages,create_event". "&state=" . $_SESSION['fb_state'];
                echo("<script> window.location.href='" . $dialog_url . "'</script>");
                $status = gt('Permissions were not yet set on your Facebook page, please try again');
                flash('error', $status);
            }
        }
    }

    public static function postEvent($params=array()) {
        if (!empty($params)) {
            // Include facebook class
            require_once(BASE . "external/facebook-php-sdk-3.2.2/src/facebook.php");

            // configuration
//            $desc = 'Facebook constantly changes their SDK and methods for communicating with Facebook. The script in this post supports the latest Facebook authentication changes that will be implemented i October 2012.';
//            $pic = 'http://blog.phpinfinite.com/wp-content/uploads/2012/11/post_to_facebook_from_php.jpg';
//            $action_name = 'Go to PHP Infinite';
//            $action_link = 'http://blog.phpinfinte.com';

            $eventdate = new eventdate($params['id']);
            $eventdate->event = new event($eventdate->event_id);

            $facebook = new Facebook(array(
                'appId' => $params['config']['app_id'],
                'secret' => $params['config']['app_secret'],
                'cookie' => true,
//                'fileUpload' => true     // this is important !
            ));
            $accesstoken = $facebook->getAccessToken();
            if (!empty($accesstoken)) {
//            $user = $facebook->getUser();
//            if (!empty($user)) {
                try {
                    $attachment = array(
                        'access_token' => $accesstoken,
                        "name"=>$eventdate->event->title,
                        "description"=>expString::summarize($eventdate->event->body) . ' ' . expCore::makeLink(array('controller'=>$params['orig_controller'], 'action'=>'show','date_id'=>$eventdate->id)),
                        "start_time"=>date('c',$eventdate->date + $eventdate->event->eventstart),
//                        "end_time"=>date('c',$eventdate->date + $eventdate->event->eventend),
//                        "location"=>$location,
//                        'description' => $desc,
//                        'picture'=>$pic,
//                        'actions' => json_encode(array('name' => $action_name,'link' => $action_link))
                    );
                    $result = $facebook->api("/".$params['config']['facebook_page']."/events", "post", $attachment);
                    $facebookEventId = $result['id'];
                    $status = gt('New Facebook Event posted') . ' - ' . $facebookEventId;
                    flash('message', $status);
                } catch (FacebookApiException $e) {
//                    header("Location:{$facebook->getLoginUrl(array('scope' => 'photo_upload,user_status,publish_stream,user_photos,manage_pages,create_event'))}");
                    $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". $params['config']['app_id'] . "&redirect_uri=" . urlencode(URL_FULL) . "&scope=publish_stream,offline_access,publish_actions,user_photos,photo_upload,user_status,manage_pages,create_event". "&state=" . $_SESSION['fb_state'];
                    echo("<script> window.location.href='" . $dialog_url . "'</script>");
                    error_log($e);
                    flash('error', $e->getMessage());
                }
            } else {
                // you're not logged in, the application will try to log in to get a access token
//                header("Location:{$facebook->getLoginUrl(array('scope' => 'photo_upload,user_status,publish_stream,user_photos,manage_pages,create_event'))}");
                $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=". $params['config']['app_id'] . "&redirect_uri=" . urlencode(URL_FULL) . "&scope=publish_stream,offline_access,publish_actions,user_photos,photo_upload,user_status,manage_pages,create_event". "&state=" . $_SESSION['fb_state'];
                echo("<script> window.location.href='" . $dialog_url . "'</script>");
                $status = gt('Permissions were not yet set on your Facebook page, please try again');
                flash('error', $status);
            }
        }
    }

}

?>