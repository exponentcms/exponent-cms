{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="cloud-tags3"}
.module.cloud span {
	display: inline-block;
	margin: 0 0 .3em 1em;
	padding: 0;
}
.module.cloud span.tag a {
	position: relative;
	display: inline-block;
	height: 30px;
	line-height: 30px;
	padding: 0 1em 0 .75em;
	background-color: #3498db;
	border-radius: 0 3px 3px 0;
	color: #fff;
	font-size: 13px;
	text-decoration: none;
	-webkit-transition: .2s;
	transition: .2s;
    z-index: 3;
}
.module.cloud span.tag a::before {
	position: absolute;
	top: 0;
	left: -15px;
	z-index: -1;
	content: '';
	width: 30px;
	height: 30px;
	background-color: #3498db;
	border-radius: 50%;
	-webkit-transition: .2s;
	transition: .2s;
}
.module.cloud span.tag a::after {
	position: absolute;
	top: 50%;
	left: -6px;
	z-index: 2;
	display: block;
	content: '';
	width: 6px;
	height: 6px;
	margin-top: -3px;
	background-color: #fff;
	border-radius: 100%;
}
.module.cloud span.tag a:hover {
	background-color: #555;
	color: #fff;
}
.module.cloud span.tag a:hover::before {
	background-color: #555;
}
{/css}

<div class="module search cloud">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.manage}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <div class="item">
        {foreach from=$page->records item=listing}
            <span class="tag">
				{$tagt = str_replace(' ', "&#160;", $listing->title)}
			    <a href="{link controller=expTag action=show title=$listing->sef_url}" style="font-size:{if $listing->attachedcount>99}2.0{else}1.{if $listing->attachedcount<10}0{$listing->attachedcount}{else}{$listing->attachedcount}{/if}{/if}em;" title="{'View items tagged with'|gettext} '{$listing->title}'">{$tagt}</a>
            </span>
        {/foreach}
    </div>
</div>
