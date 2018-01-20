{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

<div id="editfile" class="module sermonseries edit">
    {if $record->id != ""}<h1>{'Editing Sermon Series'|gettext} '{$record->title}</h1>{else}<h1>{'New Sermon Series'|gettext}'</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="editsermons-tabs"  class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">{'General'|gettext}</a></li>
                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">{'SEO'|gettext}</a></li>
                {if !$config.disable_facebook_meta}
                    <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Facebook'|gettext}</em></a></li>
                {/if}
                {if !$config.disable_twitter_meta}
                    <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{'Twitter'|gettext}</em></a></li>
                {/if}
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    {control type=text name=title label="Sermon Series Title"|gettext value=$record->title}
                    {control type=html name=body label="Description"|gettext value=$record->body}
                    {control id="preview" type="files" name="preview" label="Preview Image"|gettext subtype=preview value=$record->expFile limit="1" folder=$config.upload_folder}
                    {control type="checkbox" name="is_default" label="Default Series"|gettext|cat:"?" checked=$record->is_default value=1 description='Series used as a placeholder for unique messages not assigned to a series'|gettext}
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                    {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                    {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                    {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                </div>
                {if !$config.disable_facebook_meta}
                    <div id="tab3" role="tabpanel" class="tab-pane fade">
                        <h2>{'Facebook Meta'|gettext}</h2>
                        <blockquote>
                            {'Also used for Twitter, Pinterest, etc...'|gettext}
                        </blockquote>
                        {control type="text" name="fb[title]" label="Meta Title"|gettext value=$record->meta_fb.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="fb[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_fb.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="fb[url]" label="Meta URL"|gettext value=$record->meta_fb.url description='Canonical URL for social media if different than Canonical URL'|gettext}
                        {control type="files" name="fbimage" subtype=fbimage label="Meta Image"|gettext value=$record->meta_fb folder=$config.upload_folder limit=1 description='Image for social media (1200px x 630px or 600px x 315px, but larger than 200px x 200px)'|gettext}
                    </div>
                {/if}
                {if !$config.disable_twitter_meta}
                    <div id="tab4" role="tabpanel" class="tab-pane fade">
                        <h2>{'Twitter Meta'|gettext}</h2>
                        {control type="text" name="tw[title]" label="Meta Title"|gettext value=$record->meta_tw.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="tw[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_tw.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="tw[twsite]" label="Twitter Account"|gettext value=$record->meta_tw.twsite description='Must include @'|gettext}
                        {control type="files" name="twimage" subtype=twimage label="Meta Image"|gettext value=$record->meta_tw folder=$config.upload_folder limit=1 description='Image for social media (120px x 120px minimum)'|gettext}
                    </div>
                {/if}
            </div>
        </div>
        {loading title="Loading Sermon Series"|gettext}
        {control type=buttongroup submit="Save Sermon series"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}