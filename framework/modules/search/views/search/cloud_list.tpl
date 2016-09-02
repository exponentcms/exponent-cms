{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="cloud-tags"}
.module.cloud a.ctag,
.module.cloud span.ctag {
	margin-left: 5px;
    margin-right: 5px;
}
{/css}

<div class="module search cloud list">
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
    {$taglist = explode(PHP_EOL, $config.list)}
    <div class="item no_reftagger">
        {if !empty($config.parent_tag)}
            <{$config.item_level|default:'h2'}>
                {if isset($tags_list[strtolower($config.parent_tag)]) && $tags_list[strtolower($config.parent_tag)]->count}
                    <a href="{link controller=expTag action=show title=$tags_list[strtolower($config.parent_tag)]->sef_url}" title="{'View items tagged with'|gettext} '{$config.parent_tag}'">{$config.parent_tag}</a>
                {else}
                    {$config.parent_tag}
                {/if}
            </{$config.item_level|default:'h2'}>
        {/if}
        {foreach $taglist as $tag}
            {$tagt = str_replace(' ', "&#160;", $tag)}
            {if isset($tags_list[strtolower($tag)]) && $tags_list[strtolower($tag)]->count}
                <a href="{link controller=expTag action=show title=$tags_list[strtolower($tag)]->sef_url}" class="ctag" title="{'View items tagged with'|gettext} '{$tag}'">{$tagt}</a>
            {elseif !$config.only_used}
                <span class="ctag">{$tagt}</span>
            {/if}
        {/foreach}
    </div>
</div>
