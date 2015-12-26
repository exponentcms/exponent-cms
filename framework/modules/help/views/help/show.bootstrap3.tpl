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

{uniqueid assign="id"}

<div id="showhelp" class="module help show">
    <{$config.heading_level|default:'h1'}>{$doc->title}</{$config.heading_level|default:'h1'}>
    {$myloc=serialize($__loc)}
    {permissions}
    <div class="item-actions">
        {if $permissions.edit || ($permissions.create && $doc->poster == $user->id)}
            {if $myloc != $doc->location_data}
                {if $permissions.manage}
                    {icon action=merge id=$doc->id title="Merge Aggregated Content"|gettext}
                {else}
                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                {/if}
            {/if}
            {icon action=edit record=$doc}
            {icon action=copy record=$doc}
            {icon action=delete record=$doc}
        {/if}
    </div>
    {/permissions}
	<div id="showhelp-tabs-{$id}" class="">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General Overview'|gettext}</em></a></li>
			{if $doc->actions_views}
				<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Actions and Views'|gettext}</em></a></li>
			{/if}
			{if $doc->configuration}
				<li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Configuration'|gettext}</em></a></li>
			{/if}
			{if $doc->youtube_vid_code}
				<li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{'Videos'|gettext}</em></a></li>
			{/if}
			{if $doc->additional}
				<li role="presentation"><a href="#tab5" role="tab" data-toggle="tab"><em>{'Additional Information'|gettext}</em></a></li>
			{/if}
		</ul>
		<div class="tab-content bodycopy">
			<div id="tab1" role="tabpanel" class="tab-pane fade in active">
				{$doc->body|replace:"!!!version!!!":$hv}
			</div>
			{if $doc->actions_views}
				<div id="tab2" role="tabpanel" class="tab-pane fade">
					{$doc->actions_views|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->configuration}
				<div id="tab3" role="tabpanel" class="tab-pane fade">
					{$doc->configuration|replace:"!!!version!!!":$hv}
				</div>
			{/if}
			{if $doc->youtube_vid_code}
				<div id="tab4" role="tabpanel" class="tab-pane fade">
					{$doc->youtube_vid_code}
				</div>
			{/if}
			{if $doc->additional}
				<div id="tab5" role="tabpanel" class="tab-pane fade">
					{$doc->additional|replace:"!!!version!!!":$hv}
				</div>
			{/if}
		</div>
	</div>
	{*<div class="loadingdiv">{"Loading Help"|gettext}</div>*}
	{loading title="Loading Help"|gettext}
</div>
{if $children}
    {$params.parent = $doc->id}
    {showmodule controller=help action=showall view=childview source=$doc->loc->src params=$params}
{elseif $doc->parent}
    {get_object object=help param=$doc->parent assign=parent}
    <div class="item childview">
        <{$config.item_level|default:'h2'}>{'Parent Help Topic'|gettext}</{$config.item_level|default:'h2'}>
        <dl>
            <dt>
                <h3>
                    <a href={link controller=help action=show version=$parent->help_version->version title=$parent->sef_url} title="{$parent->body|summarize:"html":"para"}">{$parent->title}</a>
                </h3>
            </dt>

            <dd>
            {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $parent->poster == $user->id)}
                    {icon action=edit record=$parent}
                    {icon action=copy record=$parent}
                {/if}
                {if $permissions.delete || ($permissions.create && $parent->poster == $user->id)}
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

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}