twitter-api-php
===============

Simple PHP Wrapper for Twitter API v1.1 calls

[![Total Downloads](https://img.shields.io/packagist/dt/j7mbo/twitter-api-php.svg)](https://packagist.org/packages/j7mbo/twitter-api-php)
[![Build Status](https://travis-ci.org/J7mbo/twitter-api-php.svg?branch=master)](https://travis-ci.org/J7mbo/twitter-api-php)
[![Version](https://badge.fury.io/gh/j7mbo%2Ftwitter-api-php.svg)](https://packagist.org/packages/j7mbo/twitter-api-php)

**[Changelog](https://github.com/J7mbo/twitter-api-php/wiki/Changelog)** ||
**[Examples](https://github.com/J7mbo/twitter-api-php/wiki/Twitter-API-PHP-Wiki)** ||
**[Wiki](https://github.com/J7mbo/twitter-api-php/wiki)**

[Instructions in StackOverflow post here](http://stackoverflow.com/questions/12916539/simplest-php-example-retrieving-user-timeline-with-twitter-api-version-1-1/15314662#15314662) with examples. This post shows you how to get your tokens and more. 
If you found it useful, please upvote / leave a comment! :)

The aim of this class is simple. You need to:

- Include the class in your PHP code
- [Create a twitter app on the twitter developer site](https://dev.twitter.com/apps/)
- Enable read/write access for your twitter app
- Grab your access tokens from the twitter developer site
- [Choose a twitter API URL to make the request to](https://dev.twitter.com/docs/api/1.1/)
- Choose either GET / POST (depending on the request) 
- Choose the fields you want to send with the request (example: `array('screen_name' => 'usernameToBlock')`)

You really can't get much simpler than that. The above bullet points are an example of how to use the class for a POST request to block a user, and at the bottom is an example of a GET request.

Installation
------------

**Normally:** If you *don't* use composer, don't worry - just include TwitterAPIExchange.php in your application.

```php
require_once('TwitterAPIExchange.php');
```

**Via Composer:**

```bash
composer require j7mbo/twitter-api-php
```

How To Use
----------

#### Set access tokens ####

```php
$settings = array(
    'oauth_access_token' => "YOUR_OAUTH_ACCESS_TOKEN",
    'oauth_access_token_secret' => "YOUR_OAUTH_ACCESS_TOKEN_SECRET",
    'consumer_key' => "YOUR_CONSUMER_KEY",
    'consumer_secret' => "YOUR_CONSUMER_SECRET"
);
```

#### Choose URL and Request Method ####

```php
$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';
```

#### Choose POST fields (or PUT fields if you're using PUT) ####

```php
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);
```

#### Perform the request! ####

```php
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();
```

GET Request Example
-------------------

Set the GET field BEFORE calling buildOauth(); and everything else is the same:

```php
$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?screen_name=J7mbo';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
```

That is it! Really simple, works great with the 1.1 API. Thanks to @lackovic10 and @rivers on SO!
