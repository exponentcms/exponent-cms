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
 * @package    Modules
 */

// needed for Facebook SDK v4
use Facebook\Facebook;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;


class socialfeedController extends expController
{
    public $useractions = array(
        'showall' => 'Social Feeds'
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

    private $session = null;
//    private $display_all_posts = false;
    private $post_types = array();
    private $cache_limit = 120;
    private $display_time = false;
    private $display_pic = false;
    private $display_video = false;
    private $facebook_hash_tag = false;

    public function __construct($src=null, $params=array()) {
        parent::__construct($src, $params);
//        $this->display_all_posts = !empty($this->config['socialfeed_facebook_all_types']);
//        $this->post_types = $this->config['socialfeed_facebook_post_type'];
        if (!empty($this->config)) {
            foreach ($this->config['socialfeed_facebook_post_type'] as $key=>$value) {
                if ($value)
                    $this->post_types[] = $key;
            }
            if (!empty($this->config['socialfeed_cache_refresh']))
                $this->cache_limit = $this->config['socialfeed_cache_refresh'];
            $this->display_time = !empty($this->config['socialfeed_time_stamp']);
            $this->display_pic = !empty($this->config['socialfeed_facebook_display_pic']);
            $this->display_video = !empty($this->config['socialfeed_facebook_display_video']);
            $this->facebook_hash_tag = !empty($this->config['socialfeed_facebook_hashtag']);
        }
    }

    public static function displayname()
    {
        return gt("Social Feeds");
    }

    public static function description()
    {
        return gt("Display Aggregated Social Feeds");
    }

    public static function author()
    {
        return "Dave Leffler";
    }

    private function sortDESC($a,$b) {
        $aval = $a['created_stamp'];
        $bval = $b['created_stamp'];

        return ($aval > $bval ? -1 : 1);
    }

    public function showall()
    {
        expHistory::set('viewable', $this->params);
        $fb_messages = $tw_messages = $ig_messages = $pi_messages = array();
        if (!empty($this->config['socialfeed_facebook_use']))
            $fb_messages = $this->socialfeed_facebook_posts();
        if (!empty($this->config['socialfeed_twitter_use']))
            $tw_messages = $this->socialfeed_twitter_posts();
        if (!empty($this->config['socialfeed_instagram_use']))
            $ig_messages = $this->socialfeed_instagram_posts();
        if (!empty($this->config['socialfeed_pinterest_use']))
            $pi_messages = $this->socialfeed_pinterest_posts();
        $messages = array_merge_recursive($fb_messages, $tw_messages, $ig_messages, $pi_messages);
        usort($messages, array($this,'sortDESC'));  // resort entire list by date
        $messages = array_slice($messages, 0, $this->config['socialfeed_feeds_count']);
        assign_to_template(
            array(
                'messages' => $messages,
            )
        );
    }

    /**
     * Returns HTML with feeds in required format.
     *
     * @param string $page_name Array with parameters for this action: depends on the trigger.
     * @return array
     */
    private function get_facebook_feed($page_name = '')
    {
        global $user, $router;

        $selected_type_value = $config = $message_feed = array();
        $display_time = $display_pic = $display_video = $selected_type = $selected_type_key = $fb_type = $selected_type_value = '';
        $config['app_id'] = $this->config['socialfeed_facebook_app_id'];
        $config['secret'] = $this->config['socialfeed_facebook_secret_key'];

        // PHP SDK 4.x.
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {  // facebook library requires php v5.4.x
            flash('error', gt('Social Feed Facebook support requires PHP v5.4 or later'));
            return array();
        }
        require_once(BASE . "external/facebook-php-sdk-4.0.23/autoload.php"); //v4
//        require_once(BASE . "external/facebook-php-sdk-v4-5.2.0/src/Facebook/autoload.php");
//        require_once(BASE . "external/php-graph-sdk-5.3.1/src/Facebook/autoload.php");
        FacebookSession::setDefaultApplication($config['app_id'], $config['secret']); //v4
//        $fb = new Facebook\Facebook(array(
//            'app_id'     => $config['app_id'],
//            'app_secret' => $config['secret'],
//            'default_graph_version' => 'v2.5',
//        ));
        if (isset($config['app_id']) && !empty($config['app_id']) && isset($config['secret']) && !empty($config['secret'])) {
            $this->session = FacebookSession::newAppSession();  //v4
//            $helper = $fb->getCanvasHelper();//begin v5
//            $permissions = array('user_posts'); // optional
//            try {
//            	if (isset($_SESSION['facebook_access_token'])) {
//                    $accessToken = $_SESSION['facebook_access_token'];
//                    eLog('a1: '.$accessToken);
//                } elseif (isset($_REQUEST['code'])) {
//                    $accessToken = $_REQUEST['code'];
//                    // Save the access token to a session and redirect
//                    $_SESSION['facebook_access_token'] = (string) $accessToken;
//                    eLog('a2: '.$accessToken);
//                } else {
//                    $accessToken = $helper->getAccessToken();
//                    eLog('a3: '.$accessToken);
//            	}
//            } catch(Facebook\Exceptions\FacebookResponseException $e) {
//             	// When Graph returns an error
//                eLog('Graph returned an error: ' . $e->getMessage());
//              	exit;
//            } catch(Facebook\Exceptions\FacebookSDKException $e) {
//             	// When validation fails or other local issues
//            	eLog('Facebook SDK returned an error: ' . $e->getMessage());
//              	exit;
//            }
//            eLog('b: '.$accessToken);
//            if (isset($accessToken)) {
//                eLog('b1: '.$_SESSION['facebook_access_token']);
//            	if (isset($_SESSION['facebook_access_token'])) {
//            		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
//                    eLog('b2: '.$_SESSION['facebook_access_token']);
//            	} else {
//                    eLog('b3: '.$_SESSION['facebook_access_token']);
//            		$_SESSION['facebook_access_token'] = (string) $accessToken;
//
//            	  	// OAuth 2.0 client handler
//            		$oAuth2Client = $fb->getOAuth2Client();
//
//            		// Exchanges a short-lived access token for a long-lived one
//            		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
//
//            		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
//
//            		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
//                    eLog('b3a: '.$_SESSION['facebook_access_token']);
//            	}//end v5

                try {  //v4
                    if (empty($this->config['socialfeed_facebook_apiv24']))
                        $request = new FacebookRequest($this->session, 'GET', '/' . $page_name . '/posts'); //v4
                    else // facebook api v2.4+ requires specific request for fields
                        $request = new FacebookRequest($this->session, 'GET', '/' . $page_name . '/posts?fields=type,message,likes,link,created_time,source,object_id,actions'); //v4 new api v2.4+
                    $response = $request->execute(); //v4
                    $graph_object = $response->getGraphObject(); //v4
                    $facebook_values = $graph_object->asArray(); //v4
                } catch (Exception $e) {  //v4
                    return array();  //v4
                }  //v4

//                // validating the access token begin v5
//               	try {
//               		$request = $fb->get('me');
//               	} catch(Facebook\Exceptions\FacebookResponseException $e) {
//               		// When Graph returns an error
//                    eLog('1Facebook SDK returned an error: ' . $e->getCode() . ' - ' . $e->getMessage());
//               		if ($e->getCode() == 190) {
//               			unset($_SESSION['facebook_access_token']);
//               			$helper = $fb->getRedirectLoginHelper();
//               			$loginUrl = $helper->getLoginUrl($router->current_url, $permissions);
//               			echo "t<script>window.top.location.href='".$loginUrl."'</script>";
//               			exit;
//               		}
//               	} catch(Facebook\Exceptions\FacebookSDKException $e) {
//               		// When validation fails or other local issues
//                    eLog('Facebook SDK returned an error: ' . $e->getMessage());
//               		exit;
//               	}
//
//                // getting all posts published by user
//               	try {
//               		$posts_request = $fb->get('/' . $page_name . '/posts');
//               	} catch(Facebook\Exceptions\FacebookResponseException $e) {
//               		// When Graph returns an error
//                    eLog('Graph returned an error: ' . $e->getMessage());
//               		exit;
//               	} catch(Facebook\Exceptions\FacebookSDKException $e) {
//               		// When validation fails or other local issues
//                    eLog('Facebook SDK returned an error: ' . $e->getMessage());
//               		exit;
//               	}
//
//                $facebook_values = array();
//               	$posts_response = $posts_request->getGraphEdge();
//               	if($fb->next($posts_response)) {
//               		$response_array = $posts_response->asArray();
//                    $facebook_values = array_merge($facebook_values, $response_array);
//               		while ($posts_response = $fb->next($posts_response)) {
//               			$response_array = $posts_response->asArray();
//                        $facebook_values = array_merge($facebook_values, $response_array);
//               		}
////               		print_r($facebook_values);
//               	} else {
//                    $facebook_values = $posts_request->getGraphEdge()->asArray();
////               		print_r($facebook_values);
//               	}//end v5

                if ($facebook_values) {
                    foreach ($facebook_values['data'] as $facebook_value) {
                        $i = 0;
//                        if (array_key_exists($facebook_value->type, $this->post_types)) {
                        if (in_array($facebook_value->type, $this->post_types)) {
                            $msg = $this->parse_facebook_data($facebook_value);
                            if ($msg !== null) {
                                $message_feed[] = $msg;
                                $i++;
                                if ($i >= $this->config['socialfeed_feeds_count']) {
                                    break;
                                }
                            }
                        }
                    }
                    // check to see if we need another page of posts to meet our quota
                    if (count($message_feed) < $this->config['socialfeed_feeds_count']) {  // do we need more posts?
                        $i = 0;
                        $next_page = json_decode(file_get_contents($facebook_values['paging']->next));
                        foreach ($next_page->data as $next_facebook_value) {
                            if (array_key_exists($next_facebook_value->type, $this->post_types)) {
                                $msg = $this->parse_facebook_data($next_facebook_value);
                                if ($msg !== null) {
                                    $message_feed[] = $msg;
                                    $i++;
                                    if ($i >= $this->config['socialfeed_feeds_count']) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    foreach ($message_feed as $key => $message) {
                        $message_feed[$key]['sftype'] = 'facebook';  // add feed type
                    }
                    return $message_feed;
                }
//            // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
//            } else {//begin v5
//                eLog('no access token');
//            	$helper = $fb->getRedirectLoginHelper();
//            	$loginUrl = $helper->getLoginUrl($router->current_url, $permissions);
//            	echo "b<script>window.top.location.href='".$loginUrl."'</script>";
//            }//end v5
        } else {
            if ($user->isAdmin()) {
                flash('warning', gt('Please provide your credentials for the Facebook login'));
            } else {
                flash('error', gt('Please login to provide Facebook App values'));
            }
        }
    }

    /**
     * Render posts from the Facebook feed
     */
    private function parse_facebook_data($facebook_entry) {
        $ids = explode('_',$facebook_entry->id);
        if ($facebook_entry->type == 'photo') {
            if ($this->display_pic == 1) {
//                $message_feed['picture'] = $facebook_entry->picture;
                $message_feed['picture'] = 'https://graph.facebook.com/'.$facebook_entry->object_id.'/picture?type=normal';
            }
        }
        if ($facebook_entry->type == 'video') {
            if ($this->display_video == 1) {
                $message_feed['video'] = $facebook_entry->source;
            }
        }
        if (isset($facebook_entry->message) && !empty($facebook_entry->message)) {
            $message_feed['message'] = substr(
                $facebook_entry->message,
                0,
                $this->config['socialfeed_trim_length']
            );
        }
        if ($facebook_entry->type == 'event') {
            try {
                $request = new FacebookRequest($this->session, 'GET', $ids[1]); //v4
                $response = $request->execute(); //v4
                $event_object = $response->getGraphObject(); //v4
                $event_value = $event_object->asArray(); //v4
                $formatted_start = new DateTime($event_value['start_time']);
                $event_timestamp = $formatted_start->format(  // note needs to be created date
//                    $this->config['socialfeed_facebook_time_format']
                    'U'
                );
                // we don't display past events
                if ($event_timestamp < time()) {
                    return null;
                }
            } catch (Exception $e) {
                return null;
            }

            $message_feed['message'] = '<strong>'.$event_value['name'] . '</strong><br>' . substr(
                    $event_value['description'],
                    0,
                    $this->config['socialfeed_trim_length']
                );
            if ($this->display_pic == 1) {
                $message_feed['picture'] = 'https://graph.facebook.com/'.$event_value['id'].'/picture?type=large';
            }
//                $formatted_start = new DateTime($event_value['start_time']);
            $message_feed['message'] .= '<br><em>(' . $formatted_start->format(
                    (!empty($this->config['socialfeed_facebook_time_format']) ? $this->config['socialfeed_facebook_time_format'] : 'F j, Y @ g:i a')
            ) . ')</em>';
            if ($this->display_time == 1) {
                $formatted_date = new DateTime($facebook_entry->created_time);
                $message_feed['created_stamp'] = $formatted_date->format(
//                    $this->config['socialfeed_facebook_time_format']
                    'U'
                );
            }
        } else {
            if ($this->display_time == 1) {
                $formatted_date = new DateTime($facebook_entry->created_time);
                $message_feed['created_stamp'] = $formatted_date->format(
//                    $this->config['socialfeed_facebook_time_format']
                    'U'
                );
            }
        }
        if (!empty($facebook_entry->actions[0]->link)) {
//                $message_feed['full_feed_link'] = '<a href="' . $facebook_entry->link . '" target="_blank">' . htmlspecialchars(
//                        '',
//                        ENT_QUOTES,
//                        'UTF-8'
//                    ) . '</a>';
            $message_feed['action_link'] = $facebook_entry->actions[0]->link;  // like link on event; share link on photo
//        } elseif (!empty($facebook_entry->link)) {
        }
        if (!empty($facebook_entry->link)) {
            $message_feed['photo_link'] = $facebook_entry->link;  // photo link
//        } else {
//            $message_feed['full_feed_link'] = 'https://graph.facebook.com/'.$facebook_entry->id;
        }
//        if (empty($message_feed['full_feed_link']))  //fixme we won't get here?
            $message_feed['full_feed_link'] = 'https://www.facebook.com/'.$this->config['socialfeed_facebook_page_name'].'/posts/'.$ids[1];
        $message_feed['likes'] = @count($facebook_entry->likes->data);

        return $message_feed;
    }

    /**
     * Uses socialfeed_facebook_posts() for fetching Facebook feeds.
     */
    private function socialfeed_facebook_posts()
    {
        $cache_name = 'facebook_' . substr($this->loc->src, 1);
        $cache_fname = BASE . 'tmp/rsscache/' . $cache_name . ".cache";
        $facebook = array();
        if (file_exists($cache_fname)) {
            $cache = unserialize(file_get_contents($cache_fname));
            if (!empty($cache['facebook']) && (($cache['time'] + ($this->cache_limit * 60)) > time())) {  //  rate limit in minutes
                $facebook = $cache['facebook'];
            }
        }
        if (empty($facebook)) {
            $facebook = $this->get_facebook_feed($this->config['socialfeed_facebook_page_name']);

            // save new statuses to cache
            $newcache = array();
            $newcache['facebook'] = $facebook;
            $newcache['time'] = time();
            file_put_contents($cache_fname, serialize($newcache));
        }
        return $facebook;
    }

    /**
     * Fetch Twitter tweets
     *
     * @return array
     */
    private function socialfeed_twitter_posts()
    {
        global $user;

//        if ($_SERVER['REMOTE_ADDR'] != '::1') {
            $twitter_tweets = array();
            $display_time = $display_media = '';

            $settings = array(
                'oauth_access_token' => $this->config['socialfeed_twitter_access_token'],
                'oauth_access_token_secret' => $this->config['socialfeed_twitter_access_token_secret'],
                'consumer_key' => $this->config['socialfeed_twitter_consumer_key'],
                'consumer_secret' => $this->config['socialfeed_twitter_consumer_secret'],
            );

            if (count(array_filter($settings)) == count($settings)) {
                $cache_name = 'twitter_' . substr($this->loc->src, 1);
                $cache_fname = BASE . 'tmp/rsscache/' . $cache_name . ".cache";
                if (file_exists($cache_fname)) {
                    $cache = unserialize(file_get_contents($cache_fname));
                    if (!empty($cache['twitter']) && (($cache['time'] + ($this->cache_limit * 60)) > time())) {  // rate limit in minutes
                        $twitter_tweets = $cache['twitter'];
                    }
                }
                if (empty($twitter_tweets)) {
                    $tweets_count = $this->config['socialfeed_feeds_count'];
                    $twitter_username = $this->config['socialfeed_twitter_username'];
                    $display_time = $this->config['socialfeed_time_stamp'];
    //                $display_date_twitter_style = $this->config['socialfeed_twitter_time_ago'];
                    $twitter_hash_tag = $this->config['socialfeed_twitter_hashtag'];
    //                $teaser_text = $this->config['socialfeed_twitter_teaser_text'];

                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $request_method = "GET";
                    $getfield = '?screen_name=' . $twitter_username . '&count=' . $tweets_count;

                    // Loading TwitterAPIExchange.
                    require_once(BASE . "external/twitter-api-php/TwitterAPIExchange.php");
                    $twitter = new TwitterAPIExchange($settings);
                    $twitter_values = json_decode(
                        $twitter->setGetfield($getfield)->buildOauth($url, $request_method)->performRequest(),
                        $assoc = true
                    );

                    if (isset($twitter_values) && !empty($twitter_values)) {
                        if (array_key_exists('errors', $twitter_values)) {
                            if ($twitter_values["errors"][0]["message"] != "") {
                                eLog(
                                    "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $twitter_values[errors][0]["message"] . "</em></p>"
                                );
                                exit();
                            }
                        }
                        foreach ($twitter_values as $key => $twitter_value) {
                            $twitter_tweets[$key]['username'] = $twitter_value['user']['screen_name'];
                            $twitter_tweets[$key]['full_username'] = 'http://twitter.com/' . $twitter_value['user']['screen_name'];
                            preg_match_all(
                                '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
                                $twitter_value['text'],
                                $extra_links
                            );
                            foreach ($extra_links[0] as $extra_link) {
                                $twitter_tweets[$key]['extra_links'][] = $extra_link;
                            }
                            if (isset($twitter_value['text'])) {
                                $twitter_tweets[$key]['tweet'] = substr(
                                    rtrim($twitter_value['text'], $extra_link),
                                    0,
                                    $this->config['socialfeed_trim_length']
                                );
                            }
    //                        if (isset($teaser_text) && !empty($teaser_text)) {
                            if (array_key_exists('media', $twitter_value['entities'])) {
                                $twitter_tweets[$key]['tweet_url'] = '<a href="' . $twitter_value['entities']['media'][0]['url'] . '" target="_blank">' . htmlspecialchars(
                                        '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) . '</a>';
                            }
    //                        }
                            if ($display_time == 1) {
                                $formatted_twitter_date = new DateTime($twitter_value['created_at']);
    //                            if ($display_date_twitter_style == 1) {
    //                                $twitter_tweets[$key]['twitter_date'] = $this->time_elapsed_string(
    //                                    $formatted_twitter_date->format($this->config['socialfeed_twitter_time_format'])
    //                                );
    //                            } else {
                                $twitter_tweets[$key]['created_stamp'] = $formatted_twitter_date->format(
    //                                    $this->config['socialfeed_twitter_time_format']
                                    'U'
                                );
    //                            }
                            }
                            if ($twitter_hash_tag == 1) {
                                $twitter_tweets[$key]['tweet'] = preg_replace_callback(
                                    '/#(\\w+)|@(\\w+)/',
                                    function ($hash) {
                                        if ($hash[0][0] == '#') {
                                            return '<a href="' . 'https:twitter.com/hashtag/' . $hash[1] . '" target="_blank">' . $hash[0] . '</a>';
                                        }
                                        if ($hash[0][0] == '@') {
                                            return '<a href="' . 'https:twitter.com/' . $hash[2] . '" target="_blank">' . $hash[0] . '</a>';
                                        }
                                    },
                                    $twitter_tweets[$key]['tweet']
                                );
                            }
                            $twitter_tweets[$key]['sftype'] = 'twitter';  // add feed type
                        }
                        // exit();

                        // save new tweets to cache
                        $newcache = array();
                        $newcache['twitter'] = $twitter_tweets;
                        $newcache['time'] = time();
                        file_put_contents($cache_fname, serialize($newcache));
                    }
                }
                return $twitter_tweets;
            } else {
                if (in_array('administrator', array_values($user->roles))) {
                    flash('error',
                        gt('Please provide the Twitter App values')
                    );
                } else {
                    flash('status',
                        gt('Please login to provide Twitter App values')
                    );
                }
            }
//        } elseif ($_SERVER['REMOTE_ADDR'] == '::1') {
//            flash('warning', gt('Please try installing twitter on live server'));
//        }
    }

    /**
     * Fetch instagram pictures
     *
     * @return array
     */
    private function socialfeed_instagram_posts()
    {
        $i = 0;
        $images = $pic = array();
        $cache_name = 'instagram_' . substr($this->loc->src, 1);
        $cache_fname = BASE . 'tmp/rsscache/' . $cache_name . ".cache";
        if (file_exists($cache_fname)) {
            $cache = unserialize(file_get_contents($cache_fname));
            if (!empty($cache['instagram']) && (($cache['time'] + ($this->cache_limit * 60)) > time())) {  // rate limit in minutes
                $images = $cache['instagram'];
            }
        }
        if (empty($images)) {
            $instagram_post_link = $this->config['socialfeed_instagram_post_link'];
            $resolution = $this->config['socialfeed_instagram_picture_resolution'];
    //        $access_token = $this->config['socialfeed_instagram_access_token'];
    //        $url = "https://api.instagram.com/v1/users/self/media/recent/?access_token=" . $this->config['socialfeed_instagram_access_token'] . '&count=' . $this->config['socialfeed_instagram_picture_count'];
    //        $request = expCore::loadData($url);
    //
    //        // Loading Instagram API.
    //        require_once(BASE . "external/Instagram-PHP-API/src/Instagram.php");
    ////        use MetzWeb\Instagram\Instagram;
    //
    //        // initialize class
    //        $instagram = new MetzWeb\Instagram\Instagram(array(
    //            'apiKey' => '067f1c9e7a3342118d1c8d975ff52393',
    //            'apiSecret' => '13846109502641e2b51e7701fbceb16e',
    //            'apiCallback' => $this->config['socialfeed_instagram_redirect_uri'] // must point to success.php
    //        ));
    //
    //        // receive OAuth code parameter
    //        $code = $_GET['code'];
    //    $code = '4bbf4fd53fa04265b4a579d2f6ec0e68';
    //        // check whether the user has granted access
    //        if (isset($code)) {
    //            // receive OAuth token object
    //            $data = $instagram->getOAuthToken($code);
    //            $username = $data->user->username;
    //            // store user access token
    //            $instagram->setAccessToken($data);
    //            // now you have access to all authenticated user methods
    //            $request = $instagram->getUserMedia();
    //        } else {
    //            // check whether an error occurred
    //            if (isset($_GET['error'])) {
    //                echo 'An error occurred: ' . $_GET['error_description'];
    //            }
    //        }

            $request = json_decode(file_get_contents('https://www.instagram.com/'.$this->config['socialfeed_instagram_username'].'/media/'));
    //        if (isset($access_token) && !empty($access_token)) {
    //            if ($request->status_message != 'BAD REQUEST') {
    //                $json_response = json_decode($request->data);
    //                foreach ($json_response['data'] as $key => $response_data) {
                    foreach ($request->items as $key => $response_data) {
                        if ($instagram_post_link == 1) {
                            $images[$key]['post_url'] = $response_data->link;
                        }
                        $images[$key]['created_stamp'] = $response_data->created_time;
    //                    if ($response_data->caption->text) {
    //                        $images[$key]['message'] = $response_data->caption->text;
    //                    }
                        if ($response_data->comments->count) {
                            $images[$key]['message'] = $response_data->comments->data[0]->text;
                        }
                        $images[$key]['sftype'] = 'instagram';  // add feed type
                        $pic[] = $response_data->images;
                        $i++;
                        if ($i == $this->config['socialfeed_feeds_count']) {
                            break;
                        }
                    }

                    foreach ($pic as $key => $image) {
                        $images[$key]['image_url'] = $image->$resolution->url;
                    }

                    // save new pictures to cache
                    $newcache = array();
                    $newcache['instagram'] = $images;
                    $newcache['time'] = time();
                    file_put_contents($cache_fname, serialize($newcache));
                }
                return $images;
//            } else {
//                flash('error', gt('The access_token provided is invalid.'));
//            }
//        } else {
//            flash(
//                gt('warning', 'Please fill in the required details')
//            );
//        }
    }

    /**
     * Fetch pinterest pins
     *
     * @return array
     */
    private function socialfeed_pinterest_posts()
    {
        $pin_images = array();
        $cache_name = 'pinterest_' . substr($this->loc->src, 1);
        $cache_fname = BASE . 'tmp/rsscache/' . $cache_name . ".cache";
        if (file_exists($cache_fname)) {
            $cache = unserialize(file_get_contents($cache_fname));
            if (!empty($cache['pinterest']) && (($cache['time'] + ($this->cache_limit * 60)) > time())) {  // rate limit in minutes
                $pin_images = $cache['pinterest'];
            }
        }
        if (empty($pin_images)) {
            $user_id = $this->config['socialfeed_pinterest_username'];
            $board_id = $this->config['socialfeed_pinterest_boardname'];
            $pinterest_pull = $user_id . '/feed.rss';
            $desc_padding = 8;
            if (!empty($board_id)) {
                $pinterest_pull = $user_id . '/' . $board_id . '.rss';
                $desc_padding = 4;
            }
            $rss = new DOMDocument();
            $rss->load('http://pinterest.com/'.$pinterest_pull);
    //        $result = file_get_contents('http://pinterest.com/'.$pinterest_pull.'/feed.rss');
    //        $result_pins = simplexml_load_string($result);

            $pins = $rss->getElementsByTagName('item');
            $i = 0;
            foreach ($pins as $pin) {
    //        foreach($result_pin->channel->item as $poin) {
                $item_description = $pin->getElementsByTagName('description')->item(0)->nodeValue;
                $regex = "/<img((?:[^<>])*)>/";
                preg_match($regex, $item_description, $matches);

                // Old img tag
                $mh_old = $matches[0];

                // Get the img URL, it's needed for the button code
                $mh_img_url = preg_replace( '/^.*src="/' , '' , $mh_old );
                $mh_img_url = preg_replace( '/".*$/' , '' , $mh_img_url );

                $desc_offset = strripos($item_description, "</a>", -1) + $desc_padding;

                $formatted_twitter_date = new DateTime($pin->getElementsByTagName('pubDate')->item(0)->nodeValue);
                $pin_date = $formatted_twitter_date->format(
                    'U'
                );
                $item = array (
                    'title' => $pin->getElementsByTagName('title')->item(0)->nodeValue,
    //                'title' => $pin->title,
    //                'image' => $pin->getElementsByTagName('image')->item(0)->nodeValue,
                    'post_url' => $pin->getElementsByTagName('link')->item(0)->nodeValue,
                    'created_stamp' => $pin_date,
                    'description' => substr($item_description, $desc_offset),
                    'image_url' => $mh_img_url,
                    'sftype' => 'pinterest'  // add feed type
                );
                array_push($pin_images, $item);
                $i++;
                if ($i == $this->config['socialfeed_feeds_count']) {
                    break;
                }
            }
            // save new pins to cache
            $newcache = array();
            $newcache['pinterest'] = $pin_images;
            $newcache['time'] = time();
            file_put_contents($cache_fname, serialize($newcache));
        }
        return $pin_images;
    }

}

?>
