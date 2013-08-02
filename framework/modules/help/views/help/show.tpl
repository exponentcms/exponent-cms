{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div id="showhelp" class="module help show">
    <h1>{$doc->title}</h1>
    {$myloc=serialize($__loc)}
    {permissions}
    <div class="item-actions">
        {if $permissions.edit == 1}
            {if $myloc != $doc->location_data}
                {if $permissions.manage == 1}
                    {icon action=merge id=$doc->id title="Merge Aggregated Content"|gettext}
                {else}
                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                {/if}
            {/if}
            {icon action=edit record=$doc}
            {icon action=copy record=$doc}
        {/if}
    </div>
    {/permissions}
	<div id="showhelp-tabs-{$id}" class="yui-navset exp-skin-tabview">
		<ul class="yui-nav">
			<li class="selected"><a href="#tab1"><em>{'General Overview'|gettext}</em></a></li>
			{if $doc->actions_views}
				<li><a href="#tab2"><em>{'Actions and Views'|gettext}</em></a></li>
			{/if}
			{if $doc->configuration}
				<li><a href="#tab3"><em>{'Configuration'|gettext}</em></a></li>
			{/if}
			{if $doc->youtube_vid_code}
				<li><a href="#tab4"><em>{'Videos'|gettext}</em></a></li>
			{/if}
			{if $doc->additional}
				<li><a href="#tab5"><em>{'Additional Information'|gettext}</em></a></li>
			{/if}
		</ul>
		<div class="yui-content bodycopy">
			<div id="tab1">
				{$doc->body|replace:"!!!version!!!":$hv}
			</div>
			{if $doc->actions_views}
				<div id="tab2">
					{$doc->actions_views|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->configuration}
				<div id="tab3">
					{$doc->configuration|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->youtube_vid_code}
				<div id="tab4">
					{$doc->youtube_vid_code}
				</div>
			{/if}
			{if $doc->additional}
				<div id="tab5">
					{$doc->additional|replace:"!!!version!!!":$hv}
				</div>
			{/if}
		</div>
	</div>
	<div class="loadingdiv">{"Loading Help"|gettext}</div>
</div>
{if $children}
    {$params.parent = $doc->id}
    {showmodule module=help view=childview source=$doc->loc->src params=$params}
{elseif $doc->parent}
    {get_object object=help param=$doc->parent assign=parent}
    <div class="item childview">
        <h2>{'Parent Help Topic'|gettext}</h2>
        <dl>
            <dt>
                <h3>
                    <a href={link controller=help action=show version=$parent->help_version->version title=$parent->sef_url} title="{$parent->body|summarize:"html":"para"}">{$parent->title}</a>
                </h3>
            </dt>

            <dd>
            {permissions}
            <div class="item-actions">
                {if $permissions.edit == 1}
                    {icon action=edit record=$parent}
                    {icon action=copy record=$parent}
                {/if}
                {if $permissions.delete == 1}
                    {icon action=delete record=$parent}
                {/if}
            </div>
            {/permissions}
            <div class="bodycopy">
                {*{$parent->body|summarize:"html":"paralinks"}*}
            </div>
                {$parent->body|summarize:"html":"parahtml"}
        </dl>
    </div>
{/if}

{*{script unique="editform" yui3mods=1}*}
{*{literal}*}
    {*EXPONENT.YUI3_CONFIG.modules.exptabs = {*}
        {*fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',*}
        {*requires: ['history','tabview','event-custom']*}
    {*};*}

	{*YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {*}
        {*Y.expTabs({srcNode: '#showhelp-tabs'});*}
		{*Y.one('#showhelp-tabs').removeClass('hide');*}
		{*Y.one('.loadingdiv').remove();*}
    {*});*}
{*{/literal}*}
{*{/script}*}

{script unique="`$id`" jquery="jqueryui"}
{literal}
    $('#showhelp-tabs-{/literal}{$id}{literal}').tabs().next().remove();
{/literal}
{/script}