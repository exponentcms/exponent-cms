{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

{if $config.socialfeed_display_type != 'pinboard'}
{css unique="socialfeed" lesscss="`$asset_path`less/socialfeed.less"}

{/css}
{else}
{css unique="socialfeed-notes" link="`$asset_path`css/notes.css"}

{/css}
{css unique="socialfeed-font" link="https://fonts.googleapis.com/css?family=Gloria+Hallelujah|Permanent+Marker"}

{/css}
{/if}

{if $config.socialfeed_feeds_count < 4}
    {$config.socialfeed_feeds_count = 4}
{/if}
<div class="module socialfeed showall col-xs-12 col-12">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <div class="channel-links">
        {if $config.socialfeed_facebook_use}
        <a href="https://www.facebook.com/{$config.socialfeed_facebook_page_name}" target="_blank" title="{'View Page'|gettext}"><img style="height:20px;" src="{$asset_path}images/facebook.png" alt="{'Facebook'|gettext}"></a>&nbsp;&nbsp;&nbsp;&nbsp;
        {/if}
        {if $config.socialfeed_twitter_use}
        <a href="https://twitter.com/{$config.socialfeed_twitter_username}" target="_blank" title="{'View Page'|gettext}"><img style="height:20px;" src="{$asset_path}images/twitter.png" alt="{'Twitter'|gettext}"></a>&nbsp;&nbsp;&nbsp;&nbsp;
        {/if}
        {if $config.socialfeed_instagram_use}
        <a href="https://instagram.com/{$config.socialfeed_instagram_username}" target="_blank" title="{'View Page'|gettext}"><img style="height:20px;" src="{$asset_path}images/instagram.png" alt="{'Instagram'|gettext}"></a>&nbsp;&nbsp;&nbsp;&nbsp;
        {/if}
        {if $config.socialfeed_pinterest_use}
        <a href="https://pinterest.com/{$config.socialfeed_pinterest_username}" target="_blank" title="{'View Page'|gettext}"><img style="height:20px;" src="{$asset_path}images/pinterest.png" alt="{'Pinterest'|gettext}"></a>
        {/if}
    </div>
    <div class="row pinboards items">
        {if $config.socialfeed_display_type == 'fluid'}
        <div class="shuffle">
        {/if}
        {foreach $messages as $message}
            {$style = ''}
            {$bs_style = ''}
            {$pb_style = ''}
            {if bs3()}
            {if $message@iteration > $config.socialfeed_feeds_count / 4}
                {$style = $style|cat:' hidden-xs'}
            {/if}
            {if $message@iteration > $config.socialfeed_feeds_count / 4 * 2}
                {$style = $style|cat:' hidden-sm'}
            {/if}
            {if $message@iteration > $config.socialfeed_feeds_count / 4 * 3}
                {$style = $style|cat:' hidden-md'}
            {/if}
            {else}
            {if $message@iteration > $config.socialfeed_feeds_count / 4}
                {$style = $style|cat:' d.none'}
            {/if}
            {if $message@iteration > $config.socialfeed_feeds_count / 4 * 2}
                {$style = $style|cat:' d-none d-md-block'}
            {/if}
            {if $message@iteration > $config.socialfeed_feeds_count / 4 * 3}
                {$style = $style|cat:' d-none d-lg-block'}
            {/if}
            {/if}
            {if $config.socialfeed_display_type == 'pinboard'}
                {$pb_style = $style}
            {elseif $config.socialfeed_display_type != 'fluid'}
                {$bs_style = $style}
            {/if}
            {if $config.socialfeed_display_type != 'fluid' && $config.socialfeed_display_type != 'pinboard' && ($message@first || (($message@iteration - 1) is div by ($config.socialfeed_feeds_count / 4)))}
            <div class="items col-lg-3 col-md-4 col-sm-6{$bs_style}">
            {/if}
            {if $message.sftype == 'instagram'}
                <div class="item instagram{if $config.socialfeed_display_type == 'fluid'} col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12{/if}{$pb_style}">
                    <div class="item-box">
                        {if (isset($message['post_url']) && !empty($message['post_url']))}
                            <a href="{$message['post_url']}" target="_blank" title="{'View Post'|gettext}"><img class="img-responsive" src="{$message['image_url']}" alt="{'Instagram Image'|gettext}"></a>
                        {else}
                            <img class="img-responsive" src="{$message['image_url']}" alt="{'Instagram photo'|gettext}" alt="{'Instagram Image'|gettext}">
                        {/if}
                            {if (isset($message['message']) && !empty($message['message']))} {* photo & status *}
                                <div class="fb-message post-content">
                                    <a href="{$message['post_url']}" target="_blank" title="{'View Post'|gettext}">
                                        {$message['message']}
                                    </a>
                                </div>
                            {/if}
                        <div class="post-footer">
                            <a href="https://instagram.com/{$config.socialfeed_instagram_username}" target="_blank" title="{'View Page'|gettext}">
                                <img src="{$asset_path}images/instagram.png" class="pull-right" style="width: 20px;" alt="{'Instagram'|gettext}">
                            </a>
                            <!-- Time -->
                            {if ($config.socialfeed_time_stamp && isset($message['created_stamp']) && !empty($message['created_stamp']))}
                                <span class="fb-time">
                                    {$message['created_stamp']|relative_date}
                                </span>
                            {/if}
                         </div>
                    </div>
                </div>
            {elseif $message.sftype == 'twitter'}
                <div class="item twitter{if $config.socialfeed_display_type == 'fluid'} col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12{/if}{$pb_style}">
                    <div class="item-box">
                        <div class="post-content">
                            <p>{$message['tweet']}</p>
                            {if (isset($message['tweet_url']) && !empty($message['tweet_url'])) }
                                {$message['tweet_url']}
                            {/if}
                           {if (array_key_exists('extra_links', $message))}
                                {foreach $message['extra_links'] as $extra_link}
                                    <a class="tw-links" href="{$extra_link}" target="_blank">{$extra_link}</a>
                                    {br}
                                {/foreach}
                            {/if}
                            <a class="tw-username author" href="{$message['full_username']}" target="_blank"><span>@</span>{$message['username']}</a>
                        </div>
                        <div class="post-footer">
                            <a href="https://twitter.com/{$config.socialfeed_twitter_username}" target="_blank" title="{'View Page'|gettext}">
                                <img src="{$asset_path}images/twitter.png" class="pull-right" style="width: 20px;" alt="{'Twitter'|gettext}">
                            </a>
                            {if ($config.socialfeed_time_stamp && isset($message['created_stamp']) && !empty($message['created_stamp']))}
                                <div class="tw-date">{$message['created_stamp']|relative_date}</div>
                            {/if}
                        </div>
                    </div>
                </div>
            {elseif $message.sftype == 'facebook'}
                <div class="item facebook{if $config.socialfeed_display_type == 'fluid'} col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12{/if}{$pb_style}">
                    <div class="item-box">
                        <!-- Video -->
                        {if (isset($message['video']) && !empty($message['video']))}
                            <div class="fb-video">
                                <a href="{$message['video']}" target="_blank" title="{'View Video'|gettext}">
                                    <img class="img-responsive" src="{$message['picture']}" alt="{'Facebook video'|gettext}">
                                </a>
                            </div>
                        <!-- Picture -->
                        {elseif (isset($message['picture']) && !empty($message['picture']))}
                            <div class="fb-pic">
                                <a href="{$message['photo_link']}" target="_blank" title="{'View Photos'|gettext}">
                                    <img class="img-responsive" src="{$message['picture']}" alt="{'Facebook photo'|gettext}">
                                </a>
                            </div>
                        {/if}
                        <!-- Message -->
                        {if (isset($message['message']) && !empty($message['message']))} {* photo & status *}
                            <div class="fb-message post-content">
                                <a href="{$message['full_feed_link']}" target="_blank" title="{'View Post'|gettext}">
                                    {$message['message']}
                                </a>
                                {if {$message['likes']}}
                                    <span class="btn btn-xs btn-default pull-right">
                                        <strong>
                                            <i class="{if bs4()}far fa-thumbs-up{else}fa fa-thumbs-o-up{/if}"></i>
                                            {$message['likes']}
                                        </strong>
                                    </span>
                                {/if}
                            </div>
                        {/if}
                        <!-- Full post link -->
                        {*{if (isset(message['full_feed_link']) && !empty(message['full_feed_link']))}*}
                        {*<div class="teaser-link">*}
                        {*{message['full_feed_link']}*}
                        {*</div>*}
                        {*{/if}*}
                        <div class="post-footer">
                            <a href="https://www.facebook.com/{$config.socialfeed_facebook_page_name}" target="_blank" title="{'View Page'|gettext}">
                                <img src="{$asset_path}images/facebook.png" class="pull-right" style="width: 20px;" alt="{'Facebook'|gettext}">
                            </a>
                            <!-- Time -->
                            {if ($config.socialfeed_time_stamp && isset($message['created_stamp']) && !empty($message['created_stamp']))}
                                <span class="fb-time">
                                    {$message['created_stamp']|relative_date}
                                </span>
                            {/if}
                        </div>
                    </div>
                </div>
            {elseif $message.sftype == 'pinterest'}
                <div class="item pinterest{if $config.socialfeed_display_type == 'fluid'} col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12{/if}{$pb_style}">
                    <div class="item-box">
                        {if (isset($message['post_url']) && !empty($message['post_url']))}
                            <a href="{$message['post_url']}" target="_blank" title="{'View Post'|gettext}"><img class="img-responsive" src="{$message['image_url']}" alt="{'Pinterest Image'|gettext}"></a>
                        {else}
                            <img class="img-responsive" src="{$message['image_url']}" alt="{'pinterest photo'|gettext}" alt="{'Pinterest Image'|gettext}">
                        {/if}
                        {if (isset($message['description']) && !empty($message['description']))} {* pin *}
                            <div class="fb-message post-content">
                                <a href="{$message['post_url']}" target="_blank" title="{'View Post'|gettext}">
                                    {$message['description']}
                                </a>
                            </div>
                        {/if}
                        <div class="post-footer">
                            <a href="http://pinterest.com/{$config.socialfeed_pinterest_username}" target="_blank" title="{'View Page'|gettext}">
                                <img src="{$asset_path}images/pinterest.png" class="pull-right" style="width: 20px;" alt="{'Pinterest'|gettext}">
                            </a>
                            <!-- Time -->
                            {if ($config.socialfeed_time_stamp && isset($message['created_stamp']) && !empty($message['created_stamp']))}
                                <span class="fb-time">
                                    {$message['created_stamp']|relative_date}
                                </span>
                            {/if}
                         </div>
                    </div>
                </div>
            {/if}
            {if $config.socialfeed_display_type != 'fluid' && $config.socialfeed_display_type != 'pinboard' && ($message@last || ($message@iteration is div by ($config.socialfeed_feeds_count / 4)))}
            </div>
            {/if}
        {/foreach}
        {if $config.socialfeed_display_type == 'fluid'}
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 shuffle__sizer"></div>
        {/if}
    </div>
</div>
{clear}

{if $config.socialfeed_display_type == 'fluid'}
{script unique="shuffle" jquery="jquery.shuffle.modernizr"}
{literal}
    $(window).load(function() {
        var $grid = $('.shuffle');
        var $sizer = $grid.find('.shuffle__sizer');

        $grid.shuffle({
            itemSelector: '.item', // the selector for the items in the grid
            sizer: $sizer,

//            group: 'all', // Filter group
//            speed: 250, // Transition/animation speed (milliseconds)
//            easing: 'ease-out', // css easing function to use
//            gutterWidth: 10, // a static number or function that tells the plugin how wide the gutters between columns are (in pixels)
//            columnWidth: 320, // a static number or function that returns a number which tells the plugin how wide the columns are (in pixels)
//            delimeter: null, // if your group is not json, and is comma delimeted, you could set delimeter to ','
//            buffer: 0, // useful for percentage based heights when they might not always be exactly the same (in pixels)
//            initialSort: null, // Shuffle can be initialized with a sort object. It is the same object given to the sort method
//            throttle: $.throttle || null, // By default, shuffle will try to throttle the resize event. This option will change the method it uses
//            throttleTime: 300, // How often shuffle can be called on resize (in milliseconds)
//            sequentialFadeDelay: 150, // Delay between each item that fades in when adding items
//            supported: Modernizr.csstransforms && Modernizr.csstransitions // supports transitions and transforms
        });
    });
{/literal}
{/script}
{/if}