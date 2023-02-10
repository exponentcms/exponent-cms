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

{css unique="depth" link="`$asset_path`css/depth.css"}

{/css}
{css unique="nav-manager" link="`$asset_path`css/nav-manager.css" corecss="panels"}

{/css}

<div class="module navigation manager-hierarchy expanding manage-site-map">
    <div class="form_header">
   		<div class="info-header">
   			<div class="related-actions">
   				{help text="Get Help with"|gettext|cat:" "|cat:("Managing Pages"|gettext) module="manage-sitemap"}
   			</div>
   			<h2>{'Manage Site Map'|gettext}</h2>
   		</div>
   	</div>
    {permissions}
        {if $user->isAdmin()}
            <div class="module-actions">
                {icon action=manage text='Manage by Menu Heirarchy'|gettext}
                {if $user->isSystemAdmin()}  {* only the real super admin can create/change other super admins *}
                    {icon class=manage action=buildSiteMap text='Generate Sitemap'|gettext}
                {/if}
            </div>
        {/if}
    {/permissions}
    <div id="navmanager-tabs" class="">
   	    <ul class="nav nav-tabs" role="tablist">
           	<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Site Map'|gettext}</em></a></li>
            {if $canManageStandalones}<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Standalone Pages'|gettext}</em></a></li>{/if}
            {if $smarty.const.HANDLE_PAGE_REDIRECTION}<li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Page Redirection'|gettext}</em></a></li>{/if}
   	    </ul>
   	    <div class="tab-content">
           	<div id="tab1" role="tabpanel" class="tab-pane fade in active">
                <table class="table table-responsive table-striped table-condensed">
                    <thead>
                        <th>
                            {'Page Name'|gettext}
                        </th>
                        <th class="hidden-xs">
                            {'SEF Name'|gettext}
                        </th>
                        <th class="hidden-xs">
                            {'Sub-Theme'|gettext}
                        </th>
                        <th>
                            {'Status'|gettext}
                        </th>
                        <th>
                        </th>
                    </thead>
                    <tbody>
                        {foreach from=$sections item=section}
                            <tr>
                                <td width="33%">
                            {$parent=0}

                            {foreach from=$sections item=iSection}
                                {if $iSection->parents[0] == $section->id }
                                    {$parent=1}
                                {/if}
                            {/foreach}
                            {if $section->active == 1}
                                {* active page *}
                                {if  $section->id == $current->id }
                                    {* current page *}
                                    {if $parent == 1 }
                                        {$class="parent current"}
                                    {else}
                                        {if $section->depth != 0 }
                                            {$class="child current"}
                                        {else}
                                            {$class="current"}
                                        {/if}
                                    {/if}
                                {else}
                                    {* not the current page *}
                                    {if $parent == 1 }
                                        {$class="parent"}
                                    {else}
                                        {if $section->depth != 0 }
                                            {$class="child"}
                                        {/if}
                                    {/if}
                                {/if}
                            {else}
                                {* in-active page *}
                                {$class="inactive"}
                            {/if}

                            {$headerlevel=$section->depth}

                            {if $section->active == 1}
                                {* active page *}
                                <a href="{$section->link}" class="navlink depth{$headerlevel}"title="{$section->page_title}" {if $section->new_window} target="_blank"{/if}>
                                    {$image = 0}
                                    {if (!empty($section->expFile[0]->id))}
                                        {img h=16 w=16 zc=1 file_id=$section->expFile[0]->id return=1}
                                        {$image = 1}
                                    {elseif (!empty($section->glyph))}
                                        <i class="icon-fixed-width {$section->glyph}"></i>
                                        {$image = 1}
                                    {/if}
                                    {if ($image && !empty($section->glyph_only))}
                                    {else}
                                        {$section->name}
                                    {/if}
                                </a>
                            {else}
                                {* in-active page *}
                                <span class="inactive depth{$headerlevel}" title="{$section->page_title}">
                                    {$image = 0}
                                    {if (!empty($section->expFile[0]->id))}
                                        {img h=16 w=16 zc=1 file_id=$section->expFile[0]->id return=1 class='img_left'}
                                        {$image = 1}
                                    {elseif (!empty($section->glyph))}
                                        <i class="icon-fixed-width {$section->glyph}"></i>
                                        {$image = 1}
                                    {/if}
                                    {if ($image && !empty($section->glyph_only))}
                                    {else}
                                        {$section->name}
                                    {/if}
                                </span>
                            {/if}

                                </td>
                                <td class="hidden-xs">
                                    {$section->sef_name}
                                </td>
                                <td class="hidden-xs">
                                    {if empty($section->subtheme)}
                                        <em>{'Default'|gettext}</em>
                                    {else}
                                        {$section->subtheme}
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($section->page_title)}
                                        <i class="icon-fixed-width icon-text-width" title="{$section->page_title}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-text-width text-warning" title="{'No page title'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->description)}
                                        <i class="icon-fixed-width icon-list-alt" title="{$section->description}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-list-alt text-warning" title="{'No page description'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->keywords)}
                                        <i class="icon-fixed-width icon-list-ul" title="{$section->keywords}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-list-ul text-warning" title="{'No page keywords'|gettext}"></i>
                                    {/if}
                                    {if $section->public}
                                        <i class="icon-fixed-width icon-group" title="{'Public'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-user text-danger" title="{'Private'|gettext}"></i>
                                    {/if}
                                    {if $section->active}
                                        <i class="icon-fixed-width icon-signout" title="{'Active'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-ban-circle text-warning" title="{'Inactive'|gettext}"></i>
                                    {/if}
                                    {if !$section->noindex}
                                        <i class="icon-fixed-width icon-check" title="{'Index'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-remove-circle text-warning" title="{'No Index'|gettext}"></i>
                                    {/if}
                                    {if $section->new_window}
                                        <i class="icon-fixed-width icon-share text-success" title="{'Open in New Window'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-arrow-down" title="{'Open in-place'|gettext}"></i>
                                    {/if}
                                    {if $section->alias_type == 0}
                                        <span class="addpage" title="{'Normal page'|gettext}"></span>
                                    {elseif $section->alias_type == 1}
                                        <span class="addextpage" title="{'External page'|gettext}"></span>
                                    {elseif $section->alias_type == 2}
                                        <span class="addintpage" title="{'Internal Alias'|gettext}"></span>
                                    {elseif $section->alias_type == 3}
                                        <span class="addfreeform" title="{'Free-form page'|gettext}"></span>
                                    {/if}
                                </td>
                                <td>
                                    {permissions}
                                        {if $user->isAdmin()}
                                            <div class="item-actions">
                                                {if $section->alias_type == 0}
                                                    {icon class=edit action=edit_contentpage record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 1}
                                                    {icon class=edit action=edit_externalalias record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 2}
                                                    {icon class=edit action=edit_internalalias record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 3}
                                                    {icon class=edit action=edit_freeform record=$section text=notext title="Edit this Page"|gettext}
                                                {/if}
                                                {icon class=delete action=scriptaction record=$section title='Remove this Page'|gettext onclick="EXPONENT.deleteNode(`$section->id`,`$section->alias_type`,'`$section->name`');" text=notext}
                                                {if !$smarty.const.SIMPLE_PERMISSIONS}
                                                    {icon controller=users action=userperms mod=navigation int=$section->id img='userperms.png' text=notext title='Assign user permissions for this Page'|gettext}
                                                    {icon controller=users action=groupperms mod=navigation int=$section->id img='groupperms.png' text=notext title='Assign group permissions for this Page'|gettext}
                                                {/if}
                                            </div>
                                        {/if}
                                    {/permissions}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {if $canManageStandalones}
            <div id="tab2" role="tabpanel" class="tab-pane fade">
                {icon class="add" action=edit_contentpage parent=-1 text='Create a New Standalone Page'|gettext}
                <table class="table table-responsive table-striped table-condensed">
                    <thead>
                        <th>
                            {'Page Name'|gettext}
                        </th>
                        <th class="hidden-xs">
                            {'SEF Name'|gettext}
                        </th>
                        <th class="hidden-xs">
                            {'Sub-Theme'|gettext}
                        </th>
                        <th>
                            {'Status'|gettext}
                        </th>
                        <th>
                        </th>
                    </thead>
                    <tbody>
                        {foreach from=$sasections item=section}
                            <tr>
                                <td width="33%">
                            {$parent=0}

                            {foreach from=$sections item=iSection}
                                {if $iSection->parents[0] == $section->id }
                                    {$parent=1}
                                {/if}
                            {/foreach}

                            {if $section->active == 1}
                                {* active page *}
                                {if  $section->id == $current->id }
                                    {* current page *}
                                    {$class="current"}
                                {else}
                                    {* not the current page *}
                                {/if}
                            {else}
                                {* in-active page *}
                                {$class="inactive"}
                            {/if}

                            {if $section->active == 1}
                                {* active page *}
                                <a href="{link section=$section->id}" class="navlink"title="{$section->page_title}" {if $section->new_window} target="_blank"{/if}>
                                    {$image = 0}
                                    {if (!empty($section->expFile[0]->id))}
                                        {img h=16 w=16 zc=1 file_id=$section->expFile[0]->id return=1 class='img_left'}
                                        {$image = 1}
                                    {elseif (!empty($section->glyph))}
                                        <i class="icon-fixed-width {$section->glyph}"></i>
                                        {$image = 1}
                                    {/if}
                                    {if ($image && !empty($section->glyph_only))}
                                    {else}
                                        {$section->name}
                                    {/if}
                                </a>
                            {else}
                                {* in-active page *}
                                <span class="inactive" title="{$section->page_title}">
                                    {$image = 0}
                                    {if (!empty($section->expFile[0]->id))}
                                        {img h=16 w=16 zc=1 file_id=$section->expFile[0]->id return=1 class='img_left'}
                                        {$image = 1}
                                    {elseif (!empty($section->glyph))}
                                        <i class="icon-fixed-width {$section->glyph}"></i>
                                        {$image = 1}
                                    {/if}
                                    {if ($image && !empty($section->glyph_only))}
                                    {else}
                                        {$section->name}
                                    {/if}
                                </span>
                            {/if}

                                </td>
                                <td class="hidden-xs">
                                    {$section->sef_name}
                                </td>
                                <td class="hidden-xs">
                                    {if empty($section->subtheme)}
                                        <em>{'Default'|gettext}</em>
                                    {else}
                                        {$section->subtheme}
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($section->page_title)}
                                        <i class="icon-fixed-width icon-text-width" title="{$section->page_title}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-text-width text-warning" title="{'No page title'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->description)}
                                        <i class="icon-fixed-width icon-list-alt" title="{$section->description}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-list-alt text-warning" title="{'No page description'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->keywords)}
                                        <i class="icon-fixed-width icon-list-ul" title="{$section->keywords}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-list-ul text-warning" title="{'No page keywords'|gettext}"></i>
                                    {/if}
                                    {if $section->public}
                                        <i class="icon-fixed-width icon-group" title="{'Public'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-user text-danger" title="{'Private'|gettext}"></i>
                                    {/if}
                                    {if $section->active}
                                        <i class="icon-fixed-width icon-signout" title="{'Active'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-ban-circle text-warning" title="{'Inactive'|gettext}"></i>
                                    {/if}
                                    {if !$section->noindex}
                                        <i class="icon-fixed-width icon-check" title="{'Index'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-remove-circle text-warning" title="{'No Index'|gettext}"></i>
                                    {/if}
                                    {if $section->new_window}
                                        <i class="icon-fixed-width icon-share text-success" title="{'Open in New Window'|gettext}"></i>
                                    {else}
                                        <i class="icon-fixed-width icon-arrow-down" title="{'Open in-place'|gettext}"></i>
                                    {/if}
                                    {if $section->alias_type == 0}
                                        <span class="addpage" title="{'Normal page'|gettext}"></span>
                                    {elseif $section->alias_type == 1}
                                        <span class="addextpage" title="{'External page'|gettext}"></span>
                                    {elseif $section->alias_type == 2}
                                        <span class="addintpage" title="{'Internal Alias'|gettext}"></span>
                                    {elseif $section->alias_type == 3}
                                        <span class="addfreeform" title="{'Free-form page'|gettext}"></span>
                                    {/if}
                                </td>
                                <td>
                                    {permissions}
                                        {if $user->isAdmin()}
                                            <div class="item-actions">
                                                {if $section->alias_type == 0}
                                                    {icon class=edit action=edit_contentpage record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 1}
                                                    {icon class=edit action=edit_externalalias record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 2}
                                                    {icon class=edit action=edit_internalalias record=$section text=notext title="Edit this Page"|gettext}
                                                {elseif $section->alias_type == 3}
                                                    {icon class=edit action=edit_freeform record=$section text=notext title="Edit this Page"|gettext}
                                                {/if}
                                                {icon action=delete record=$section title='Delete this Page'|gettext onclick="return confirm('"|cat:("Delete this page?"|gettext)|cat:"');" text=notext}
                                                {if !$smarty.const.SIMPLE_PERMISSIONS}
                                                    {icon controller=users action=userperms mod=navigation int=$section->id img='userperms.png' text=notext title='Assign user permissions for this Page'|gettext}
                                                    {icon controller=users action=groupperms mod=navigation int=$section->id img='groupperms.png' text=notext title='Assign group permissions for this Page'|gettext}
                                                {/if}
                                            </div>
                                        {/if}
                                    {/permissions}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {/if}
            {if $smarty.const.HANDLE_PAGE_REDIRECTION}<div id="tab3" role="tabpanel" class="tab-pane fade">{exp_include file="manage_redirection.tpl"}</div>{/if}
        </div>
   	</div>
   	{loading title='Loading Pages'|gettext}
</div>

{script unique="tabload" yui3mods="node,yui2-yahoo-dom-event,yui2-container"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.one('.loadingdiv').remove();
        var YAHOO = Y.YUI2;
        EXPONENT.deleteNode = function (id,alias_type,name){
            var handleYes = function() {
                this.hide();
                   if (alias_type == 0)
                       window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=remove&id="+id;
                   else
                       window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=delete&id="+id;
            };
            var handleNo = function() {
                this.hide();
            };
               if (alias_type == 0) {
                var message = "{/literal}{"Removing a content page moves it to the Standalone Page Manager, removing it from the Site Hierarchy. If there are any sub-pages to this section, those will also be moved"|gettext}{literal}";
                   var yesbtn = "{/literal}{"Move to Standalone"|gettext}{literal}";
               } else {
                   var message = "{/literal}{"Removing an internal alias page or external link page permanently removes it from the system."|gettext}{literal}";
                   var yesbtn = "{/literal}{"Delete Page"|gettext}{literal}";
               }

            // Instantiate the Dialog
            var delpage = new YAHOO.widget.SimpleDialog("simpledialog1",
                                            { width: "400px",
                                                fixedcenter: true,
                                                visible: false,
                                                modal: true,
                                                draggable: false,
                                                close: true,
                                                text: message,
                                                icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                constraintoviewport: true,
                                                buttons: [ { text:yesbtn, handler:handleYes, isDefault:true },
                                                    { text:"{/literal}{"Cancel"|gettext}{literal}",  handler:handleNo } ]
                                            } );
            delpage.setHeader("Remove \""+name+"\" from hierarchy");

            // Render the Dialog
            delpage.render(document.body);
            delpage.show();
        }
    });
{/literal}
{/script}