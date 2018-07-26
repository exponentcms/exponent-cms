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

{$name = glyph}
{capture assign="callback"}
{literal}
    function format{/literal}{$name}{literal}(icon, container) {
        if (!icon.id) { return icon.text; }
        var originalOption = icon.element;
        return $('<span><i class="fa-fw ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
    }
    $('#{/literal}{$name}{literal}').select2({
//        width: "100%",
        templateResult: format{/literal}{$name}{literal},
        templateSelection: format{/literal}{$name}{literal}
    });
{/literal}
{/capture}

<div class="module navigation edit_internalalias">
	<div class="form_header">
        <div class="info-header">
            <div class="related-actions">
   			    {help text="Get Help with"|gettext|cat:" "|cat:("Editing Internal Alias Pages"|gettext) module="edit-internal-page"}
            </div>
            <h2>{if $section->id}{'Edit Existing'|gettext}{else}{'Create New'|gettext}{/if} {'Internal Alias'|gettext}</h2>
            <blockquote>{'Select which internal page you want this section to link to.  If you link to another internal alias, the aliases will all be dereferenced, and the original destination used.  If you link to an external alias, then this section will point to the external aliases external web address.'|gettext}</blockquote>
        </div>
	</div>
    {form action=update}
        {control type=hidden name=id value=$section->id}
        {control type=hidden name=rank value=$section->rank}
        {control type=hidden name=alias_type value=2}
        {control type=hidden name=_validate value=1}
        {control type=hidden name=active value=1}
        <div id="configure-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-toggle="tab"><em>{'Page'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                    {control type=text name=name label="Name"|gettext value=$section->name focus=1}
                    {control type=text name=sef_name label="SEF Name"|gettext value=$section->sef_name description='If you don\'t put in an SEF Name one will be generated based on the title provided. SEF names can only contain alpha-numeric characters, hyphens and underscores.'|gettext}
                    {if $section->id == 0}
                        {control type=hidden name=parent value=$section->parent}
                    {else}
                        {control type=dropdown name=parent label="Parent Page"|gettext items=section::levelDropdownControlArray(0,0,array($section->id),($user->isAdmin() || $section->parent == 0),'manage') value=$section->parent}
                    {/if}
                    {control type="checkbox" name="new_window" label="Open in New Window"|gettext|cat:"?" checked=$section->new_window value=1 description='Should menu links for this page open in a new window/tab?'|gettext}
                    {control type="dropdown" name="internal_id" label="Page"|gettext items=section::levelDropdownControlArray(0,0,array(),false,'manage',false,false) value=$section->internal_id default=$smarty.const.SITE_DEFAULT_SECTION description='Select a page to associate this with'|gettext}
                    {control type="checkbox" name="public" label="Public"|gettext|cat:"?" checked=$section->public|default:1 value=1 description='Should this page and menu item be visible to all users regardless of permissions?'|gettext}
                    {group label='Menu Item Icon'|gettext}
                        {control type="files" name="files" label="Graphic Icon"|gettext accept="image/*" value=$section->expFile limit=1 description='Select an icon to use with this menu item'|gettext}
                        {if bs3()}
                        {control type="dropdown" name="glyph" select2=$capture label="Font Icon"|gettext items=$glyphs includeblank='No Font Icon'|gettext style="font-family: 'FontAwesome', Helvetica;" value=$section->glyph description='or Select a font icon to use with this menu item'|gettext}
                        {elseif bs4()}
                        {control type="dropdown" name="glyph" select2=$capture label="Font Icon"|gettext items=$glyphs includeblank='No Font Icon'|gettext style="font-family: 'Font Awesome 5 Free', Helvetica;" value=$section->glyph description='or Select a font icon to use with this menu item'|gettext}
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

{script unique="tabload" jquery=1 bootstrap="tab"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}