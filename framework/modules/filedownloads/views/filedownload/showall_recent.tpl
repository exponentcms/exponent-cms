{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{uniqueid assign="id"}

{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}

{if !empty($config.enable_facebook_like) || !empty($config.displayfbcomments)}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId={$config.app_id}&autoLogAppEvents=1" nonce="9wKafjYh"></script>
{/if}

{if $config.enable_tweet}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{/if}

<div class="module filedownload showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{'Recent'|gettext} {$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='filedownload' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='filedownload' text="Manage Categories"|gettext}
                {/if}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
                {/if}
           {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {$myloc=serialize($__loc)}
    {$cat="bad"}
    {foreach from=$page->records item=item name=files}
        {if $smarty.foreach.files.iteration<=$config.headcount || !$config.headcount}
            {exp_include file='filedownloaditem.tpl'}
            {$cat=$file->expCat[0]->id}
        {/if}
    {/foreach}
    {if $page->total_records > $config.headcount}
        {pagelinks paginate=$page more=1 text="More Items in"|gettext|cat:' '|cat:$moduletitle|cat:' ...'}
    {/if}
</div>

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}

    {/script}

    {script unique="filedownload-`$id`"}
    {literal}
        mejs.i18n.language('{/literal}{substr($smarty.const.LOCALE,0,2)}{literal}'); // Setting language
        $('audio,video').mediaelementplayer({
            // Do not forget to put a final slash (/)
            pluginPath: '../build/',
            iconSprite: EXPONENT.PATH_RELATIVE+'external/mediaelement/build/mejs-controls.svg',
            // this will allow the CDN to use Flash without restrictions
            // (by default, this is set as `sameDomain`)
            shimScriptAccess: 'always',
            defaultAudioWidth: 340,
            success: function(player, node) {
            // $('#' + node.id + '-mode').html('mode: ' + player.rendererName);
            },
        });
    {/literal}
    {/script}
{/if}
