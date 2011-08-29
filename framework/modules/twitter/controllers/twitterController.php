<?php

/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @copyright  2004-2011 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @link       http://www.exponent-docs.org/
 */

class twitterController extends expController {
    public $basemodel_name = 'expRecord';

    public $useractions = array(
        'showall'=>'Show all'
    );
    
    public $remove_configs = array(
        'aggregretion',
        'comments',
        'files',
        'rss',
        'tags'
    );
	public $codequality = 'beta';
    
    function displayname() { return "Twitter"; }
    function description() { return "For now you can only pull your tweets. Very soon you will be able have access to the entire Twitter API"; }
    function author() { return "Jonathan Worent - OIC Group, Inc"; }
    
    public function showall() {
        if (!empty($this->config['consumer_key'])) {
            // create instance
            $twitter = new Twitter($this->config['consumer_key'], $this->config['consumer_secret']);

            // set tokens
            $twitter->setOAuthToken($this->config['oauth_token']);
            $twitter->setOAuthTokenSecret($this->config['oauth_token_secret']);

	        switch ($this->config['typestatus']) {
		        case 1:
					// get home  timeline
					$tweets = $twitter->statusesHomeTimeline(null,null,$this->config['twlimit']);
			        break;
		        case 2:
					// get friends timeline
					$tweets = $twitter->statusesFriendsTimeline(null,null,$this->config['twlimit']);
			        break;
		        case 3:
					// get public timeline
					$tweets = $twitter->statusesPublicTimeline();
			        break;
		        case 4:
					// get mentions
					$tweets = $twitter->statusesMentions(null,null,$this->config['twlimit']);
			        break;
		        default:
			        // get users timeline
			        $tweets = $twitter->statusesUserTimeline(null,null,null,null,null,$this->config['twlimit']);
	                break;
	        }

    		if ($this->config['twlimit']) $tweets = array_slice($tweets,0,$this->config['twlimit'],true);
		
    		foreach ($tweets as $key => $value) {
			    if (strpos($value['text'],'RT ') === false) {
				    $tweets[$key]['text'] = $this->twitterify($value['text']);
				    $tweets[$key]['screen_name'] = $value['user']['screen_name'];
				    $tweets[$key]['image'] = $value['user']['profile_image_url'];
				    $tweets[$key]['via'] = $value['source'];
			    } else {
				    // we're a retweet
				    $tweets[$key]['text'] = $this->twitterify(substr($value['text'],strpos($value['text'],':')+2));
				    $tweets[$key]['screen_name'] = $value['retweeted_status']['user']['screen_name'];
				    $tweets[$key]['image'] = $value['retweeted_status']['user']['profile_image_url'];
				    $tweets[$key]['via'] = $value['source'].' (RT by '.$value['user']['screen_name'].')';
			    }
			    $tweets[$key]['created_at'] = strtotime($value['created_at']); // convert to unix time
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
    
}

?>
