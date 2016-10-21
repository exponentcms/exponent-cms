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

{css unique="depth" link="`$asset_path`css/depth.css"}

{/css}
{css unique="nav-manager" link="`$asset_path`css/nav-manager.css"}

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
        {if $canManage == 1}
            <div class="module-actions">
                {icon action=manage text='Manage by Menu Heirarchy'|gettext}
            </div>
        {/if}
    {/permissions}
    <div id="navmanager-tabs" class="">
   	    <ul class="nav nav-tabs" role="tablist">
           	<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Site Map'|gettext}</em></a></li>
   	        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Standalone Pages'|gettext}</em></a></li>
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
                                    {elseif (bs3() && !empty($section->glyph))}
                                        <i class="fa fa-fw {$section->glyph}"></i>
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
                                    {elseif (bs3() && !empty($section->glyph))}
                                        <i class="fa fa-fw {$section->glyph}"></i>
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
                                        <i class="fa fa-fw fa-header" title="{$section->page_title}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-header text-warning" title="{'No page title'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->description)}
                                        <i class="fa fa-fw fa-list-alt" title="{$section->description}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-list-alt text-warning" title="{'No page description'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->keywords)}
                                        <i class="fa fa-fw fa-list-ul" title="{$section->keywords}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-list-ul text-warning" title="{'No page keywords'|gettext}"></i>
                                    {/if}
                                    {if $section->public}
                                        <i class="fa fa-fw fa-users" title="{'Public'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-user-secret text-danger" title="{'Private'|gettext}"></i>
                                    {/if}
                                    {if $section->active}
                                        <i class="fa fa-fw fa-sign-out" title="{'Active'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-ban text-warning" title="{'Inactive'|gettext}"></i>
                                    {/if}
                                    {if $section->new_window}
                                        <i class="fa fa-fw fa-share-square-o text-success" title="{'Open in New Window'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-arrow-down" title="{'Open in-place'|gettext}"></i>
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
                                        {if $canManage == 1}
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
                                                {icon class=delete action=scriptaction record=$section title='Delete'|gettext onclick="deleteNode(`$section->id`,`$section->alias_type`,'`$section->name`');" text=notext title="Delete this Page"|gettext}
                                                {icon controller=users action=userperms mod=navigation int=$section->id img='userperms.png' text=notext title='Assign user permissions for this Page'|gettext}
                                                {icon controller=users action=groupperms mod=navigation int=$section->id img='groupperms.png' text=notext title='Assign group permissions for this Page'|gettext}
                                            </div>
                                        {/if}
                                    {/permissions}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>

            <div id="tab2" role="tabpanel" class="tab-pane fade">
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
                                    {elseif (bs3() && !empty($section->glyph))}
                                        <i class="fa fa-fw {$section->glyph}"></i>
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
                                    {elseif (bs3() && !empty($section->glyph))}
                                        <i class="fa fa-fw {$section->glyph}"></i>
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
                                        <i class="fa fa-fw fa-header" title="{$section->page_title}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-header text-warning" title="{'No page title'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->description)}
                                        <i class="fa fa-fw fa-list-alt" title="{$section->description}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-list-alt text-warning" title="{'No page description'|gettext}"></i>
                                    {/if}
                                    {if !empty($section->keywords)}
                                        <i class="fa fa-fw fa-list-ul" title="{$section->keywords}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-list-ul text-warning" title="{'No page keywords'|gettext}"></i>
                                    {/if}
                                    {if $section->public}
                                        <i class="fa fa-fw fa-users" title="{'Public'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-user-secret text-danger" title="{'Private'|gettext}"></i>
                                    {/if}
                                    {if $section->active}
                                        <i class="fa fa-fw fa-sign-out" title="{'Active'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-ban text-warning" title="{'Inactive'|gettext}"></i>
                                    {/if}
                                    {if $section->new_window}
                                        <i class="fa fa-fw fa-share-square-o text-success" title="{'Open in New Window'|gettext}"></i>
                                    {else}
                                        <i class="fa fa-fw fa-arrow-down" title="{'Open in-place'|gettext}"></i>
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
                                        {if $canManage == 1}
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
                                                {icon action=delete record=$section title='Delete'|gettext onclick="return confirm('"|cat:("Delete this page?"|gettext)|cat:"');" text=notext title="{'Delete this Page'|gettext}" title="Delete this Page"|gettext}
                                                {icon controller=users action=userperms mod=navigation int=$section->id img='userperms.png' text=notext title='Assign user permissions for this Page'|gettext}
                                                {icon controller=users action=groupperms mod=navigation int=$section->id img='groupperms.png' text=notext title='Assign group permissions for this Page'|gettext}
                                            </div>
                                        {/if}
                                    {/permissions}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
   	</div>
   	{loading title='Loading Pages'|gettext}
</div>

{script unique="tabload" jquery="bootstrap-dialog" bootstrap="modal,tab,transition"}
{literal}
    $('.loadingdiv').remove();

    function deleteNode(id,alias_type,name) {
        if (alias_type==0){
            var message = "{/literal}{"Deleting a content page moves it to the Standalone Page Manager, removing it from the Site Hierarchy. If there are any sub-pages to this section, those will also be moved"|gettext}{literal}";
            var yesbtn = "{/literal}{"Move to Standalone"|gettext}{literal}";
        } else {
            var message = "{/literal}{"Deleting an internal alias page or external link page permanently removes it from the system."|gettext}{literal}";
            var yesbtn = "{/literal}{"Delete Page"|gettext}{literal}";
        }
        BootstrapDialog.show({
            type: BootstrapDialog.TYPE_DANGER,
            title: "{/literal}{'Remove'|gettext}{literal} \""+name+"\" {/literal}{'from hierarchy'|gettext}{literal}",
            message: message,
            buttons: [{
                label: yesbtn,
                action: function(dialog) {
                    if (alias_type == 0)
                        window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=remove&id="+id;
                    else
                        window.location=eXp.PATH_RELATIVE+"index.php?module=navigation&action=delete&id="+id;
                }
            }, {
                label: 'No',
                action: function(dialog) {
                    dialog.close();
                }
            }]
        });
    }
{/literal}
{/script}