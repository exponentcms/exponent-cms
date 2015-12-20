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

<div id="config" class="module scaffold configure exp-skin">
    <div class="form_header">
        <div class="info-header">
            <div class="related-actions">
                {help text="Get Help with"|gettext|cat:" "|cat:("module configuration"|gettext) page="module-configuration"}
            </div>
            <h2>{'Configure Settings for this'|gettext} {$title} {'Module'|gettext}</h2>
            <blockquote>{'Use this form to configure the behavior of the module.'|gettext}</blockquote>
        </div>
    </div>
    {form action=saveconfig}
        <div id="config-tabs" class="">
            <ul class="nav nav-pills">
                {foreach from=$views item=tab name=tabs}
                    <li{if $smarty.foreach.tabs.first} class="active"{/if}>
                        <a href="#tab{$smarty.foreach.tabs.iteration}" data-toggle="pill">
                            {$tab.name}
                        </a>
                    </li>
                {/foreach}
            </ul>            
            <div class="tab-content">
                {foreach from=$views item=body name=body}
                    <div id="tab{$smarty.foreach.body.iteration}" class="tab-pane fade{if $smarty.foreach.body.first} in active{/if}">
                        {include file=$body.file}
                    </div>
                {/foreach}
            </div>
        </div>
        {*<div class="loadingdiv">{"Loading Settings"|gettext}</div>*}
        {loading title="Loading Settings"|gettext}
        {control type=buttongroup submit="Save Configuration"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}