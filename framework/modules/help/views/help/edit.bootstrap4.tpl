{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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
        <div id="edithelp-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab2" class="nav-link" role="tab" data-toggle="tab"><em>{'Actions and Views'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab3" class="nav-link" role="tab" data-toggle="tab"><em>{'Configuration'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab4" class="nav-link" role="tab" data-toggle="tab"><em>{'Videos'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab5" class="nav-link" role="tab" data-toggle="tab"><em>{'Additional Information'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab6" class="nav-link" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
            <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                <h2>{'Help Document'|gettext}</h2>
                {control type=text name=title label="Title"|gettext value=$record->title focus=1}
                {control type="dropdown" name="help_version_id" label="Version"|gettext frommodel="help_version" key=id display=version order=version dir=DESC value=$record->help_version_id}
                {*{control type=textarea name=summary label="Summary"|gettext value=$record->summary}*}
                {control type=html name=body label="General Information"|gettext value=$record->body}
                {control type="dropdown" name="parent" label="Parent Help Doc"|gettext items=$parents value=$record->parent}
				{control type="dropdown" name="help_section" label="Help Section"|gettext items=$sections value=$record->loc->src default=$current_section}
            </div>
            <div id="tab2" role="tabpanel" class="tab-pane fade">
                 <h2>{'Actions and Views'|gettext}</h2>
                 {control type=html name=actions_views label="Actions and Views"|gettext value=$record->actions_views}
            </div>
            <div id="tab3" role="tabpanel" class="tab-pane fade">
                 <h2>{'Configuration'|gettext}</h2>
                 {control type=html name=configuration label="Configurations"|gettext value=$record->configuration}
            </div>
            <div id="tab4" role="tabpanel" class="tab-pane fade">
                <h2>{'YouTube Video Code'|gettext}</h2>
                {control type=textarea cols=80 rows=20 name=youtube_vid_code label="YouTube Video (Embed) Code"|gettext value=$record->youtube_vid_code}
            </div>
            <div id="tab5" role="tabpanel" class="tab-pane fade">
                 <h2>{'Additional Information'|gettext}</h2>
                 {control type=html name=additional label="Additional Info"|gettext|cat:" ("|cat:("displays in side column"|gettext)|cat:")"|gettext value=$record->additional}
            </div>
            <div id="tab6" role="tabpanel" class="tab-pane fade">
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
	    {*<div class="loadingdiv">{"Loading Help Item"|gettext}</div>*}
        {loading title="Loading Help Item"|gettext}
        {control type=buttongroup submit="Save Help Doc"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="tabload" jquery=1 bootstrap="tab"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}