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

{$name = glyph}
{capture assign="callback"}
{literal}
    function format{/literal}{$name}{literal}(icon, container) {
        if (!icon.id) { return icon.text; }
        var originalOption = icon.element;
        return $('<span><i class="fa fa-fw ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
    }
    $('#{/literal}{$name}{literal}').select2({
//        width: "100%",
        templateResult: format{/literal}{$name}{literal},
        templateSelection: format{/literal}{$name}{literal}
    });
{/literal}
{/capture}

<div class="module navigation edit_freeform">
	<div class="form_header">
        <div class="info-header">
            <div class="related-actions">
   			    {help text="Get Help with"|gettext|cat:" "|cat:("Editing Free-form Menu Item"|gettext) module="edit-freeform-menu"}
            </div>
            <h2>{if $section->id}{'Edit Existing'|gettext}{else}{'Create New'|gettext}{/if} {'Free-form Menu Item'|gettext}</h2>
            <blockquote><strong>{'This page/menu-item type is only functional on some navigation views such as the Mega view where it will appear without its children!'|gettext}</strong>{br}
             {'On other views it will appear as an inactive menu item with its children.'|gettext}
             {'Use it to embed a module/container within a top-level menu item.'|gettext}</blockquote>
        </div>
    </div>
    {form action=update}
        {control type=hidden name=id value=$section->id}
        {control type=hidden name=rank value=$section->rank}
        {control type=hidden name=parent value=0}
        {control type=hidden name=alias_type value=3}
        {control type=hidden name=_validate value=1}
        {control type=hidden name=active value=0}
        <div id="configure-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Page'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    {control type=text name=name label="Name"|gettext value=$section->name focus=1}
                    {control type=text name=sef_name label="SEF Name"|gettext value=$section->sef_name description='If you don\'t put in an SEF Name one will be generated based on the title provided. SEF names can only contain alpha-numeric characters, hyphens and underscores.'|gettext}
                    {control type=number min=0 max=4 name="internal_id" label="Width in Columns"|gettext value=$section->internal_id default=3 description="Enter 0 to 4"|gettext description='The width of this top-level dropdown area, 0 or empty means a full-width single column'|gettext}
                    {*{control type="dropdown" name="external_link" label="Dropdown Alignment"|gettext items="Left,Right"|gettxtlist values="left,right" value=$section->external_link}*}
                    {control type="radiogroup" name="external_link" label="Dropdown Alignment"|gettext items="Left,Right"|gettxtlist values="left,right" value=$section->external_link|default:"left"}
                    {control type="checkbox" name="public" label="Public"|gettext|cat:"?" checked=$section->public|default:1 value=1 description='Should this page and menu item be visible to all users regardless of permissions?'|gettext}
                    {group label='Menu Item Icon'|gettext}
                        {control type="files" name="files" label="Graphic Icon"|gettext accept="image/*" value=$section->expFile limit=1 description='Select an icon to use with this menu item'|gettext}
                        {if bs3()}
                        {control type="dropdown" name="glyph" select2=$callback label="Font Icon"|gettext items=$glyphs includeblank='No Font Icon'|gettext style="font-family: 'FontAwesome', Helvetica;" value=$section->glyph description='or Select a font icon to use with this menu item'|gettext}
                        {elseif bs4()}
                        {control type="dropdown" name="glyph" select2=$callback label="Font Icon"|gettext items=$glyphs includeblank='No Font Icon'|gettext style="font-family: 'Font Awesome 5 Free', Helvetica;" value=$section->glyph description='or Select a font icon to use with this menu item'|gettext}
                        {/if}
                        {control type="checkbox" name="glyph_only" label="Display Icon Alone"|gettext checked=$section->glyph_only value=1 description='Should the menu only display the icon without the page name?'|gettext}
                    {/group}
                </div>
            </div>
        </div>
        {*<div class="loadingdiv">{'Loading Pages'|gettext}</div>*}
        {loading title='Loading Pages'|gettext}
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}