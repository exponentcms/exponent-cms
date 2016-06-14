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

{*{css unique="edit-container" link="`$asset_path`css/add-content-bootstrap.css" corecss="admin-global"}*}

{*{/css}*}
<div class="exp-skin">
    <div class="exp-container edit">
        <div class="info-header">
            <div class="related-actions">
                {if $user->isSuperAdmin()}
                    {icon module=expModule action=manage text="Manage Active Modules"|gettext}
                {/if}
                {help text="Get Help with"|gettext|cat:" "|cat:("Adding Page Content"|gettext) module="adding-modules-to-a-page"}
            </div>
            <h2>{if $is_edit}{'Edit Module'|gettext}{else}{'Add New Content'|gettext}{/if}</h2>
        </div>
        
        {if $error}
        {message class=error text=$error}
    </div>
        {else}
            {form action=update horizontal="1"}
                {if $is_edit}
                    {control type=hidden name=id value=$container->id}
                    {control type=hidden name=existing_source value=$container->internal->src}
                {/if}
                {control type=hidden name=rank value=$container->rank}
                {control type=hidden name=rerank value=$rerank}
                {control type=hidden name=current_section value=$current_section}
                
                {control type=text size=31 horizontal="1" label="Module Title"|gettext name="title" value=$container->title caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext focus=1}

                {if $smarty.const.INVERT_HIDE_TITLE}
                    {$title_str = 'Show Module Title?'|gettext}
                    {$desc_str = 'The Module Title is hidden by default.'|gettext}
                {else}
                    {$title_str = 'Hide Module Title?'|gettext}
                    {$desc_str = 'The Module Title is displayed by default.'|gettext}
                {/if}
                {control type="checkbox" horizontal="1" name="hidemoduletitle" label=$title_str value=1 checked=$config.hidemoduletitle description=$desc_str}

                {control type="checkbox" horizontal="1" name="is_private" label='Hide Module?'|gettext value=1 checked=$container->is_private description='Should this module be hidden from users without a view permission?'|gettext}

                {control type="dropdown" horizontal="1" id="modcntrol" name=modcntrol items=$modules size=count($modules) includeblank="-- Select a Module --"|gettext label="Type of Content"|gettext disabled=1 value=$container->internal->mod}
                
                {if $is_edit}{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}{/if}

                {if $is_edit == 0}
                    <div id="recyclebin" class="control">
                        <label>{'Recycle Bin'|gettext}</label>
                        {icon name="browse-bin" class=trash disabled=1 text='Browse Recycled Content'|gettext}
                        <input type="hidden" id="existing_source" name="existing_source" value="" />
                    </div>
                {/if}

                {control type="dropdown" horizontal="1" id="actions" name=actions includeblank="-- No Module Selected --"|gettext disabled=1 label="Content Action"|gettext}

                {control type="dropdown" horizontal="1" id="views" name=views includeblank="-- No Action Selected --"|gettext disabled=1 label="Content Display"|gettext}

                {control type="buttongroup" horizontal="1" submit="Save"|gettext disabled=1 cancel="Cancel"|gettext name="buttons"}
            {/form}
        </div>

        {script unique="addmodule" jquery=1}
        {literal}
        $(document).ready(function() {
            var modpicker = $('#modcntrol'); // the module selection dropdown
            var is_edit = {/literal}{$is_edit}{literal} //are we editing?
            var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
            var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view
            var actionpicker = $('#actions'); // the actions dropdown
            var viewpicker = $('#views'); // the views dropdown
            var recyclebin = $('#browse-bin'); // the recyclebin link
            var recycledSource = $('#existing_source');

            //listens for a change in the module dropdown
            modpicker.on('change',function(e){
                EXPONENT.disableSave();
                EXPONENT.clearRecycledSource();
                if (modpicker.val()!='') {
                    //set the current module
                    EXPONENT.setCurMod();
                    //enable recycle bin
                    if (modpicker.val()!='container') {
                        EXPONENT.enableRecycleBin();
                    } else {
                        EXPONENT.disableRecycleBin();
                    }

                    EXPONENT.resetViews
                    EXPONENT.writeActions();
                }else{
                    //else, they clicked back on "select a module", so we reset everything
                    EXPONENT.disableRecycleBin();
                    EXPONENT.resetActionsViews();
                };
            });

            //listens for a change in the action dropdown
            actionpicker.on('change', function(){
                EXPONENT.disableSave();
                EXPONENT.setCurAction();
                if (actionpicker.val()!='0') {
                    EXPONENT.writeViews();
                }else{
                    EXPONENT.resetViews();
                };
            });

            // handles view picker changes
            EXPONENT.handleViewChange = function(e){
                if (viewpicker.val()!='0') {
                    EXPONENT.enableSave();
                }else{
                    EXPONENT.disableSave();
                };
            }

            //listens for a change in the view dropdown
            viewpicker.on('change', EXPONENT.handleViewChange);

            //resets both the viewpicker and actionpicker
            EXPONENT.resetActionsViews = function() {
                EXPONENT.resetActions();
                EXPONENT.resetViews();
            }

            //resets the actionpicker to the default
            EXPONENT.resetActions = function() {
                actionpicker.empty();
                //var actionDefaultOption = $('<option value="0">{/literal}{"-- No Module Selected --"|gettext}{literal}</option>');
                actionpicker.append($('<option value="0">{/literal}{"-- No Module Selected --"|gettext}{literal}</option>'));
                actionpicker.val(0);
                actionpicker.attr('disabled',1).attr('size',1);
                actionpicker.closest('div.control').addClass('disabled');
            }

            //resets the viewpicker to the default
            EXPONENT.resetViews = function() {
                viewpicker.empty();
                //var viewDefaultOption = $('<option value="0">{/literal}{"-- No Action Selected --"|gettext}{literal}</option>');
                viewpicker.append($('<option value="0">{/literal}{"-- No Action Selected --"|gettext}{literal}</option>'));
                viewpicker.val(0);
                viewpicker.attr('disabled',1).attr('size',1);
                viewpicker.closest('div.control').addClass('disabled');
                EXPONENT.disableSave();
            }

            //finds the currently selected module
            EXPONENT.setCurMod = function() {
                EXPONENT.curMod = $('#modcntrol').val();
            };
            //finds the currently selected action for the given module
            EXPONENT.setCurAction = function() {
                EXPONENT.curAction = $('#actions').val();
            };

            //enables the save button once the module/action/view is selected
            EXPONENT.enableSave = function() {
                //var svbtn = $('#buttonsSubmit')
                $('#buttonsSubmit').removeAttr('disabled').removeClass('disabled');
                // svbtn.ancestor('.buttongroup').removeClass('disabled');
            }

            //disables save button
            EXPONENT.disableSave = function() {
                $('#buttonsSubmit').attr('disabled',1).addClass('disabled');
            }

            //makes the recycle bin link clickable
            EXPONENT.enableRecycleBin = function() {
                recyclebin.on('click',EXPONENT.recyclebin);
                if ({/literal}{$user->is_acting_admin}{literal} && modpicker.val()!='container') {
                    recyclebin.removeClass('disabled');
                } else {
                    recyclebin.detach('click');
                }
            }

            //disables the recycle bin link being clickable
            EXPONENT.disableRecycleBin = function() {
                recyclebin.detach('click');
                recyclebin.addClass('disabled');
            }

            //launches the recycle bin window
            EXPONENT.recyclebin = function() {
                EXPONENT.clearRecycledSource();  //reset recycle bin status to empty
                var mod = EXPONENT.curMod;
                var url = EXPONENT.PATH_RELATIVE+"index.php?controller=recyclebin&action=show&ajax_action=1&recymod="+mod;//+"&dest="+escape(dest)+"&vmod="+vmod+"&vview="+vview;
                window.open(url,'sourcePicker','title=no,resizable=yes,toolbar=no,width=900,height=750,scrollbars=yes');
            }

            //called from the recyclebin when a trashed item is selected for use
            EXPONENT.useRecycled = function(src) {
                recycledSource.val(src)
                recyclebin.addClass('btn-success');
                $('#browse-bin > i').removeClass('fa-trash-o');
                $('#browse-bin > i').addClass('fa-check-square-o');
            }

            //removes the source from the value of the hidden variable if switching modules
            EXPONENT.clearRecycledSource = function() {
                recycledSource.val("")
                recyclebin.removeClass('btn-success');
                $('#browse-bin > i').removeClass('fa-check-square-o');
                $('#browse-bin > i').addClass('fa-trash-o');
            }

            //create the list of actions for selected module
            EXPONENT.writeActions = function() {
                actionpicker.attr('disabled',1);
                EXPONENT.resetViews();
                // var uri = EXPONENT.PATH_RELATIVE+'index.php';
                $.ajax({
                    headers: { 'X-Transaction': 'Getting Actions'},
                    url: EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getaction&ajax_action=1&mod=' + EXPONENT.curMod,
                    success: function(o){
                        var opts = $.parseJSON(o);
                        actionpicker.empty();
                        el = $('<option value="0">{/literal}{"-- Select an Action --"|gettext}{literal}</option>');
                        actionpicker.append(el);

                        $.each(opts, function( index, module ) {
                            actionpicker.append($('<option></option>').attr('value',index).text(module));
                        });

                        actionpicker.removeAttr('disabled').attr('size',actionpicker.find('option').length);
                        actionpicker.val(0);
                    }
                });
            }

            //create the list of views for the selected action
            EXPONENT.writeViews = function() {
                viewpicker.removeAttr('disabled');
                $.ajax({
                    headers: { 'X-Transaction': 'Getting Action Views'},
                    url: EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getactionviews&ajax_action=1&mod=' + EXPONENT.curMod + '&act=' + actionpicker.val() + '&actname=' + actionpicker.val(),
                    success: function(o){
                        var opts = $.parseJSON(o);
                        //console.log(opts)
                        viewpicker.empty();
                        el = $('<option value="0">{/literal}{"-- Select a View --"|gettext}{literal}</option>');
                        viewpicker.append(el);

                        $.each(opts, function( index, view ) {
                            viewpicker.append($('<option></option>').attr('value',index).text(view));
                        });

                        viewpicker.removeAttr('disabled').attr('size',viewpicker.find('option').length);

                        if (is_edit) {
                            viewpicker.val(current_view);
                            EXPONENT.handleViewChange();
                        }else{
                            viewpicker.val(0);
                        }
                    }
                });
            }

            if (!is_edit) {
                modpicker.removeAttr('disabled');
            } else {
                //set the current module
                EXPONENT.setCurMod();
            };
        });
        {/literal}
        {/script}
    {/if}
</div>