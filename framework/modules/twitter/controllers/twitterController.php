<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @package Framework
 */

class twitterController extends expController {
    public $basemodel_name = 'expRecord';
    public $useractions = array(
        'showall'=>'Show all'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation', 'categories','comments','ealerts','files','module_title','pagination', 'rss','tags')

    function displayname() { return "Twitter"; }
    function description() { return "Display your tweets just like on Twitter"; }
    function author() { return "Jonathan Worent - OIC Group, Inc"; }
    
    public function showall() {
        if (!empty($this->config['consumer_key'])) {
            // create instance
            $twitter = new Twitter($this->config['consumer_key'], $this->config['consumer_secret']);

            // set tokens
            $twitter->setOAuthToken($this->config['oauth_token']);
            $twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);
            $mytwitteracct =$twitter->accountVerifyCredentials();

	        switch ($this->config['typestatus']) {
		        case 1:  // get users timeline including retweets
			        $tweets = $twitter->statusesUserTimeline(null,null,null,null,$this->config['twlimit'],null,null,true);
			        break;
		        case 2:  // get friends timeline
					$tweets = $twitter->statusesFriendsTimeline(null,null,$this->config['twlimit']);
			        break;
		        case 3:  // get mentions
					$tweets = $twitter->statusesMentions(null,null,$this->config['twlimit']);
			        break;
		        case 4:  // get public timeline
					$tweets = $twitter->statusesPublicTimeline();
			        break;
		        default:  // get home timeline
                    $tweets = $twitter->statusesHomeTimeline(null,null,$this->config['twlimit']);
	                break;
	        }

    		if ($this->config['twlimit']) $tweets = array_slice($tweets,0,$this->config['twlimit'],true);
		    $retweets = $twitter->statusesRetweetedByMe(null,null,$this->config['twlimit']);

    		foreach ($tweets as $key => $value) {
			    $tweets[$key]['retweetedbyme'] = false;
			    if (strpos($value['text'],'RT ') === false) {
				    $tweets[$key]['text'] = $this->twitterify($value['text']);
				    $tweets[$key]['screen_name'] = $value['user']['screen_name'];
				    $tweets[$key]['image'] = $value['user']['profile_image_url'];
				    $tweets[$key]['via'] = $value['source'];
				    $tweets[$key]['ours'] = ($value['user']['id'] == $mytwitteracct['id']) ? true : false;
			    } else {
				    // we're a retweet
				    $tweets[$key]['text'] = $this->twitterify(substr($value['text'],strpos($value['text'],':')+2));  // strip out RT text
				    $tweets[$key]['screen_name'] = isset($value['retweeted_status']['user']['screen_name']) ? $value['retweeted_status']['user']['screen_name'] : 'Unknown';
				    $tweets[$key]['image'] = isset($value['retweeted_status']['user']['profile_image_url']) ? $value['retweeted_status']['user']['profile_image_url'] : '';
				    if ($value['user']['id'] == $mytwitteracct['id']) {
					    $tweets[$key]['via'] = $value['source'];
					    $tweets[$key]['retweetedbyme'] = true;
				    } else {
					    $tweets[$key]['via'] = $value['source'].' (<img src="framework/modules/twitter/assets/images/rt.png" title="retweet by" alt="RT by"/> '.$value['user']['screen_name'].')';
				    }
				    $tweets[$key]['ours'] = ($value['user']['id'] == $mytwitteracct['id']) ? true : false;
			    }
//			    $tweets[$key]['created_at'] = strtotime($value['created_at']); // convert to unix time
			    $tweets[$key]['created_at'] = expDateTime::relativeDate(strtotime($value['created_at'])); // convert to unix time
		        if (!isset($value['retweeted_status'])) {
			        $tweets[$key]['retweeted_status'] = false;
		        }
		        foreach ($retweets as $rekey => $revalue) {
			        if ($tweets[$key]['id'] == $retweets[$rekey]['retweeted_status']['id']) {
				        $tweets[$key]['retweetedbyme'] = true;
			            break;
			        }
		        }
    		}

            assign_to_template(array('items'=>$tweets));
        }
    }
    
    function twitterify($ret) {
        $ret = preg_replace('/\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', "<a href=\"\\0\" class=\"twitterlink\">\\0</a>", $ret);
        $ret = preg_replace('/(^|[^\w])(@[\d\w\-]+)/', '\\1<a href="http://twitter.com/#!/$2" class=\"twitteruser\">$2</a>', $ret);
        //$ret = preg_replace('/(^|[^\w])(#[\d\w\-]+)/', '\\1<a href="http://twitter.com/#!/search/$2" class=\"twittertopic\">$2</a>' , $ret);
		$ret = preg_replace('/\s+#(\w+)/',' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>',$ret);
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
		    $twitter = new Twitter($this->config['consumer_key'], $this->config['consumer_secret']);
		    // set tokens
		    $twitter->setOAuthToken($this->config['oauth_token']);
		    $twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);
			$twitter->statusesUpdate($this->params['body']);
		}
		expHistory::back();
	}

	/**
	 * Retweet the Tweet
	 */
	public function create_retweet() {
		if (!empty($this->config['consumer_key']) && !empty($this->params['id'])) {
		    // create instance
		    $twitter = new Twitter($this->config['consumer_key'], $this->config['consumer_secret']);
		    // set tokens
		    $twitter->setOAuthToken($this->config['oauth_token']);
		    $twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);
			$twitter->statusesRetweet($this->params['id']);
		}
		expHistory::back();
	}

	/**
	 * Delete the Tweet
	 */
	public function delete_retweet() {
		if (!empty($this->config['consumer_key']) && !empty($this->params['id'])) {
		    // create instance
		    $twitter = new Twitter($this->config['consumer_key'], $this->config['consumer_secret']);
		    // set tokens
		    $twitter->setOAuthToken($this->config['oauth_token']);
		    $twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);
			$twitter->statusesDestroy($this->params['id']);
		}
		expHistory::back();
	}

}

?>
