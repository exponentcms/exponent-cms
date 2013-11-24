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

<div id="edithelp" class="module help edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New Help Document'|gettext}</h1>{/if}
    {form action=update record=$record}
        {control type=hidden name=id value=$record->id}
        <div id="edithelp-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Actions and Views'|gettext}</em></a></li>
                <li><a href="#tab3"><em>{'Configuration'|gettext}</em></a></li>
                <li><a href="#tab4"><em>{'Videos'|gettext}</em></a></li>
                <li><a href="#tab5"><em>{'Additional Information'|gettext}</em></a></li>
                <li><a href="#tab6"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                <h2>{'Help Document'|gettext}</h2>
                {control type=text name=title label="Title"|gettext value=$record->title}
                {control type="dropdown" name="help_version_id" label="Version"|gettext frommodel="help_version" key=id display=version order=version dir=DESC value=$record->help_version_id}
                {*{control type=textarea name=summary label="Summary"|gettext value=$record->summary}*}
                {control type=html name=body label="General Information"|gettext value=$record->body}
                {control type="dropdown" name="parent" label="Parent Help Doc"|gettext items=$parents value=$record->parent}
				{control type="dropdown" name="help_section" label="Help Section"|gettext items=$sections value=$record->loc->src default=$current_section}
            </div>
            <div id="tab2">
                 <h2>{'Actions and Views'|gettext}</h2>
                 {control type=html name=actions_views label="Actions and Views"|gettext value=$record->actions_views}
            </div>
            <div id="tab3">
                 <h2>{'Configuration'|gettext}</h2>
                 {control type=html name=configuration label="Configurations"|gettext value=$record->configuration}
            </div>
            <div id="tab4">
                <h2>{'YouTube Video Code'|gettext}</h2>
                {control type=textarea cols=80 rows=20 name=youtube_vid_code label="YouTube Video (Embed) Code"|gettext value=$record->youtube_vid_code}
            </div>
            <div id="tab5">
                 <h2>{'Additional Information'|gettext}</h2>
                 {control type=html name=additional label="Additional Info"|gettext|cat:" ("|cat:("displays in side column"|gettext)|cat:")"|gettext value=$record->additional}
            </div>
            <div id="tab6">
                 <h2>{'SEO Settings'|gettext}</h2>
                {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
                {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
                {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
            </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Help Item"|gettext}</div>
        {control type=buttongroup submit="Save Help Doc"|gettext cancel="Cancel"|gettext}
    {/form}     
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#edithelp-tabs'});
		Y.one('#edithelp-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
