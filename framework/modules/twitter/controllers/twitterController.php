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

class twitterController extends expController {
    public $basemodel_name = 'expRecord';
    public $useractions = array(
        'showall' => 'Show all'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags')

    public $twitter;
    public $mytwitteracct;

    static function displayname() {
        return gt("Twitter");
    }

    static function description() {
        return gt("Display your tweets just like on Twitter");
    }

    static function author() {
        return "Jonathan Worent - OIC Group, Inc";
    }

    public function showall() {
        if (!empty($this->config['consumer_key'])) {
            // create instance
            if (expPermissions::check('manage', $this->loc)) {
                $except_handler = 'twitter_exception_admin';
            } else {
                $except_handler = 'twitter_exception';
            }
            set_exception_handler(array('twitterController', $except_handler));
            $this->twitter = new expTwitter($this->config['consumer_key'], $this->config['consumer_secret']);
            // set tokens
            $this->twitter->setOAuthToken($this->config['oauth_token']);
            $this->twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);

            $this->mytwitteracct = $this->twitter->accountVerifyCredentials();

            $tweets = $this->getTweets($this->config['typestatus']);

            if ($this->config['twlimit']) $tweets = array_slice($tweets, 0, $this->config['twlimit'], true); // not sure this is necessary??
//		    $retweets = $this->twitter->statusesRetweetedByMe(null,null,$this->config['twlimit']);

            foreach ($tweets as $key => $value) {
                $tweets[$key]['retweetedbyme'] = false;
//                $tweets[$key]['retweetedbyme'] = $value['retweeted'];
                if (strpos($value['text'], 'RT ') === false) {
                    $tweets[$key]['text'] = $this->twitterify($value['text']);
                    $tweets[$key]['screen_name'] = $value['user']['screen_name'];
                    $tweets[$key]['image'] = $value['user']['profile_image_url'];
                    $tweets[$key]['via'] = $value['source'];
                    if (!empty($this->mytwitteracct['id'])) $tweets[$key]['ours'] = ($value['user']['id'] == $this->mytwitteracct['id']) ? true : false;
                } else {
                    // we're a retweet
                    $tweets[$key]['text'] = $this->twitterify(substr($value['text'], strpos($value['text'], ':') + 2)); // strip out RT text
                    $tweets[$key]['screen_name'] = isset($value['retweeted_status']['user']['screen_name']) ? $value['retweeted_status']['user']['screen_name'] : 'Unknown';
                    $tweets[$key]['image'] = isset($value['retweeted_status']['user']['profile_image_url']) ? $value['retweeted_status']['user']['profile_image_url'] : '';
                    if (!empty($this->mytwitteracct['id']) && $value['user']['id'] == $this->mytwitteracct['id']) {
                        $tweets[$key]['via'] = $value['source'];
                        $tweets[$key]['retweetedbyme'] = true;
                    } else {
                        $tweets[$key]['via'] = $value['source'] . ' (<img src="framework/modules/twitter/assets/images/rt.png" title="retweet by" alt="RT by"/> ' . $value['user']['screen_name'] . ')';
                    }
                    if (!empty($this->mytwitteracct['id'])) $tweets[$key]['ours'] = ($value['user']['id'] == $this->mytwitteracct['id']) ? true : false;
                }
                $tweets[$key]['created_at'] = strtotime($value['created_at']); // convert to unix time
                if (!isset($value['retweeted_status'])) {
                    $tweets[$key]['retweeted_status'] = false;
                }
//		        foreach ($retweets as $rekey => $revalue) {
//			        if ($tweets[$key]['id'] == $retweets[$rekey]['retweeted_status']['id']) {
//				        $tweets[$key]['retweetedbyme'] = true;
//			            break;
//			        }
//		        }
            }

            assign_to_template(array('items' => $tweets));
            restore_exception_handler();
        }
    }

    function twitterify($ret) {
        $ret = preg_replace('/\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', "<a href=\"\\0\" class=\"twitterlink\">\\0</a>", $ret);
        $ret = preg_replace('/(^|[^\w])(@[\d\w\-]+)/', '\\1<a href="http://twitter.com/#!/$2" class=\"twitteruser\">$2</a>', $ret);
        //$ret = preg_replace('/(^|[^\w])(#[\d\w\-]+)/', '\\1<a href="http://twitter.com/#!/search/$2" class=\"twittertopic\">$2</a>' , $ret);
        $ret = preg_replace('/\s+#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $ret);
        //$ret = preg_replace("/(^| )#(\w+)/", "\\1#\\2", $ret);
        return $ret;
    }

    //     public function showallog() {
    //         //expHistory::set('viewable', $this->params);
    // $twit = new expTwitter($this->config);
    // $tweets = $twit->getUserTimeline();
    // if ($this->config['limit']) $tweets = array_slice($tweets,0,$this->config['limit'],true);
    //
    // foreach ($tweets as $key => $value) {
    //     $tweets[$key]->text = preg_replace('/\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', "<a href=\"\\0\" class=\"twitterlink\">\\0</a>", $value->text);
    //     $tweets[$key]->text = preg_replace('/(^|[^\w])(@[\d\w\-]+)/', '\\1<a href="http://twitter.com/$2" class=\"twitteruser\">$2</a>' ,$value->text);
    //             $tweets[$key]->text = preg_replace('/(^|[^\w])(#[\d\w\-]+)/', '\\1<a href="http://twitter.com/search?q=$2" class=\"twittertopic\">$2</a>' ,$value->text);
    //
    // }
    //
    //         assign_to_template(array('items'=>$tweets));
    //     }

    /**
     * Send the Tweet
     */
    public function update() {
        if (!empty($this->config['consumer_key']) && !empty($this->params['body'])) {
            // create instance
            if (expPermissions::check('manage', $this->loc)) {
                $except_handler = 'twitter_exception_admin';
            } else {
                $except_handler = 'twitter_exception';
            }
            set_exception_handler(array('twitterController', $except_handler));
            $this->twitter = new expTwitter($this->config['consumer_key'], $this->config['consumer_secret']);
            // set tokens
            $this->twitter->setOAuthToken($this->config['oauth_token']);
            $this->twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);

            $this->twitter->statusesUpdate($this->params['body']);
            restore_exception_handler();
        }
        expHistory::back();
    }

    /**
     * Retweet the Tweet
     */
    public function create_retweet() {
        if (!empty($this->config['consumer_key']) && !empty($this->params['id'])) {
            // create instance
            if (expPermissions::check('manage', $this->loc)) {
                $except_handler = 'twitter_exception_admin';
            } else {
                $except_handler = 'twitter_exception';
            }
            set_exception_handler(array('twitterController', $except_handler));
            $this->twitter = new expTwitter($this->config['consumer_key'], $this->config['consumer_secret']);
            $this->twitter->setTimeOut(60);

            // set tokens
            $this->twitter->setOAuthToken($this->config['oauth_token']);
            $this->twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);

            $this->twitter->statusesRetweet($this->params['id']);
            restore_exception_handler();
        }
        expHistory::back();
    }

    /**
     * Delete the Tweet
     */
    public function delete_tweet() {
        if (!empty($this->config['consumer_key']) && !empty($this->params['id'])) {
            // create instance
            if (expPermissions::check('manage', $this->loc)) {
                $except_handler = 'twitter_exception_admin';
            } else {
                $except_handler = 'twitter_exception';
            }
            set_exception_handler(array('twitterController', $except_handler));
            $this->twitter = new expTwitter($this->config['consumer_key'], $this->config['consumer_secret']);
            // set tokens
            $this->twitter->setOAuthToken($this->config['oauth_token']);
            $this->twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);

            $this->twitter->statusesDestroy($this->params['id']);
            restore_exception_handler();
        }
        expHistory::back();
    }

    public function getTweets($type = '') {
        if (empty($this->mytwitteracct['id']) && $type == 1) {
            $type = 4;
        }
        $cache_name = $type . '_' . substr($this->loc->src, 1);
        $cache_fname = BASE . 'tmp/rsscache/' . $cache_name . ".cache";
        $tweets = array();
        if (file_exists($cache_fname)) {
            $cache = unserialize(file_get_contents($cache_fname));
            if (!empty($cache['tweets']) && (($cache['time'] + (15 * 60)) > time())) {  // 15 min rate limit windows
                $tweets = $cache['tweets'];
            }
        }
        if (empty($tweets)) {
            switch ($type) {
                case 1: // get users timeline including retweets
                    $tweets = $this->twitter->statusesUserTimeline($this->mytwitteracct['id'], null, null, $this->config['twlimit'], null, null, null, null, true);
                    break;
                case 3: // get mentions
                    $tweets = $this->twitter->statusesMentionsTimeline($this->config['twlimit']);
                    break;
                case 5: // get retweets of me, new in v1.1
                    $tweets = $this->twitter->statusesRetweetsOfMe($this->config['twlimit']);
                    break;
                case 2: // get friends timeline deprecated v1.0
                case 4: // get public timeline deprecated v1.0
                default: // get home timeline
                    $tweets = $this->twitter->statusesHomeTimeline($this->config['twlimit']);
                    break;
            }
            // save tweets to cache
            $newcache = array();
            $newcache['tweets'] = $tweets;
            $newcache['time'] = time();
            file_put_contents($cache_fname, serialize($newcache));
        }
        return $tweets;
    }

    public static function twitter_exception(Exception $e) {
    }

    public static function twitter_exception_admin(Exception $e) {
        flash('error', 'Twitter: ' . $e->getMessage());
    }

}

?>