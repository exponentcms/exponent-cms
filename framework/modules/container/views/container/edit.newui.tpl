{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
                    <a class="managemodules" href="{link module=expModule action=manage}">{"Manage Active Modules"|gettext}</a>
                {/if}
                {help text="Get Help with"|gettext|cat:" "|cat:("Adding Page Content"|gettext) module="adding-modules-to-a-page"}
            </div>
            <h1>{if $is_edit}{'Edit Module'|gettext}{else}{'Add New Content'|gettext}{/if}</h1>
        </div>
        
        {if $error}
        <div class="msg-queue error">
            <div class="msg">{$error}</div>
        </div>
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
                
                {control type=text size=31 horizontal="1" label="Module Title"|gettext name="title" value=$container->title caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext}


                {if $smarty.const.INVERT_HIDE_TITLE}
                    {$title_str = 'Show Module Title?'|gettext}
                    {$desc_str = 'The Module Title is hidden by default.'|gettext}
                {else}
                    {$title_str = 'Hide Module Title?'|gettext}
                    {$desc_str = 'The Module Title is displayed by default.'|gettext}
                {/if}
                {control type="checkbox" horizontal="1" name="hidemoduletitle" label=$title_str value=1 checked=$config.hidemoduletitle description=$desc_str}

                {control type="checkbox" horizontal="1" name="is_private" label='Hide Module?'|gettext value=1 checked=$container->is_private description='Should this module be hidden from users without a view permission?'|gettext}

                {control type="dropdown" horizontal="1" id="modcntrol" name=modcntrol items=$modules size=count($modules) label="Type of Content"|gettext disabled=1 value=$container->internal->mod}
                
                {if $is_edit}{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}{/if}

                {if $is_edit == 0}
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">
                            {'Recycle Bin'|gettext}
                        </label>
                        <div class="col-sm-10">
                            <a id="browse-bin" href="#" class="btn disabled"><i class="fa fa-trash-o"></i> {'Browse Recycled Content'|gettext}</a>
                            <input type="hidden" id="existing_source" name="existing_source" value="" />
                        </div>
                    
                    </div>
                {/if}


                {control type="dropdown" horizontal="1" id="actions" name=actions includeblank="No Module Selected"|gettext disabled=1 label="Content Action"|gettext}

                {control type="dropdown" horizontal="1" id="views" name=views includeblank="No Action Selected"|gettext disabled=1 label="Content Display"|gettext}

                {*control type=dropdown id=ctlview name=ctlview label=" "*}

                {control type="buttongroup" horizontal="1" submit="Save"|gettext disabled=1 cancel="Cancel"|gettext name="buttons"}
            {/form}
        </div>

        {* src="$smarty.const.PATH_RELATIVE|cat:'js/ContainerSourceControl.js'" *}

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
            var recyclebinwrap = $('#recyclebin'); // the recyclebin div
            // console.log(!is_edit);

            modpicker.on('change',function(e){
                EXPONENT.disableSave();
                EXPONENT.clearRecycledSource();
                if (modpicker.val()!='') {
                    //set the current module
                    EXPONENT.setCurMod();
                    //enable recycle bin
                    if (modpicker.val()!='' && modpicker.val()!='container') {
                        EXPONENT.enableRecycleBin();
                    } else {
                        EXPONENT.disableRecycleBin();
                    }

                    //decide what to do weather it's a controller or module
                    if (EXPONENT.isController()) {
                        EXPONENT.writeActions();
                    } else {
                        EXPONENT.writeViews();
                    }
                }else{
                    //else, they clicked back on "select a module", so we reset everything
                    EXPONENT.disableRecycleBin();
                    EXPONENT.resetActionsViews();
                };
            });

            // handles the action picker change
            EXPONENT.handleActionChange = function(){
                EXPONENT.disableSave();
                EXPONENT.setCurAction();
                if (actionpicker.val()!='0') {
                    EXPONENT.writeViews();
                }else{
                    EXPONENT.resetViews();
                };
            }

            //listens for a change in the action dropdown
            actionpicker.on('change', EXPONENT.handleActionChange);

            // handles view picker changes
            EXPONENT.handleViewChange = function(e){
                if (viewpicker.val()!=-1) {
                    EXPONENT.enableSave();
                }else{
                    EXPONENT.disableSave();
                };
            }

            //listens for a change in the view dropdown
            viewpicker.on('change', EXPONENT.handleViewChange);

            //resets both the viewpicker and actionpicker
            EXPONENT.resetActionsViews = function() {
                EXPONENT.resetViews();
                EXPONENT.resetActions();
            }

            //resets the actionpicker to the default when entering this page
            EXPONENT.resetActions = function() {
                var actionDefaultOption = Y.Node.create('<option value="0">{/literal}{"No Module Selected"|gettext}{literal}</option>');
                actionpicker.appendChild(actionDefaultOption);
                actionpicker.set('disabled',1);
                actionpicker.ancestor('div.control').addClass('disabled');
            }

            //resets the viewpicker to the default when entering this page
            EXPONENT.resetViews = function() {
                // var viewDefaultOption = $('<option value="0">{/literal}{"No Action Selected"|gettext}{literal}</option>');
                // viewpicker.append(viewDefaultOption);
                // viewpicker.attr('disabled',1);
                // EXPONENT.disableSave();
            }

            //finds the currently selected module
            EXPONENT.setCurMod = function() {
                EXPONENT.curMod = $('#modcntrol').val();
            };
            //finds the currently selected action for the given module
            EXPONENT.setCurAction = function() {
                EXPONENT.curAction = $('#actions').val();
            };

            //decides if its a controller or old school module
            EXPONENT.isController = function(){
                // console.log(EXPONENT.curMod);
                if (EXPONENT.curMod.indexOf('module')!=-1) {
                    return false;
                } else {
                    return true;
                };
            }
            //enables the save button once the view is selected
            EXPONENT.enableSave = function() {
                var svbtn = $('#buttonsSubmit')
                svbtn.removeAttr('disabled').removeClass('disabled');
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

            //makes the recycle bin link clickable
            EXPONENT.disableRecycleBin = function() {
                recyclebin.detach('click');
                recyclebinwrap.addClass('disabled');
            }

            //launches the recycle bin
            EXPONENT.recyclebin = function() {
                var mod = EXPONENT.curMod;
                //Y.log(mod);
                var url = EXPONENT.PATH_RELATIVE+"index.php?controller=recyclebin&action=show&ajax_action=1&recymod="+mod;//+"&dest="+escape(dest)+"&vmod="+vmod+"&vview="+vview;
                //Y.log(url);
                window.open(url,'sourcePicker','title=no,resizable=yes,toolbar=no,width=900,height=750,scrollbars=yes');
            }

            //called from the recyclebin when a trashed item is selected for use
            EXPONENT.useRecycled = function(src) {
               var recycledSource = $('#existing_source');
               recycledSource.set('value',src)
               recyclebinwrap.addClass('using-rb');
            }

            //removes the source from the value of the hidden variable if the switch modules
            EXPONENT.clearRecycledSource = function() {
                var recycledSource = $('#existing_source');
                recycledSource.val("")
                recyclebinwrap.removeClass('using-rb');
            }

            EXPONENT.writeActions = function() {
                if (EXPONENT.isController()) {
                    actionpicker.attr('disabled',1);
                    EXPONENT.resetViews();
                    // var uri = EXPONENT.PATH_RELATIVE+'index.php';
                    $.ajax({
                        url: EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getaction&ajax_action=1&mod=' + EXPONENT.curMod,
                        success: function(o){
                            var opts = $.parseJSON(o);
                            actionpicker.empty();
                            el = $('<option value="0">{/literal}{"Select an Action"|gettext}{literal}</option>');
                            actionpicker.append(el);

                            $.each(opts, function( index, module ) {
                                actionpicker.append($('<option></option>').attr('value',index).text(module));
                            });

                            actionpicker.removeAttr('disabled').attr('size',actionpicker.find('option').length);
                        }
                    });
                } else {
                    actionpicker.attr('disabled',1).html('<option value="0">{/literal}{"No actions for this module..."|gettext}{literal}</option>');
                };
            }


            EXPONENT.writeViews = function() {
                viewpicker.removeAttr('disabled');
                if (EXPONENT.isController()) {

                    $.ajax({
                        url: EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getactionviews&ajax_action=1&mod=' + EXPONENT.curMod + '&act=' + actionpicker.val() + '&actname=' + actionpicker.val(),
                        success: function(o){
                            var opts = $.parseJSON(o);
                            console.log(opts)
                            viewpicker.empty();
                            el = $('<option value="0">{/literal}{"Select a View"|gettext}{literal}</option>');
                            viewpicker.append(el);

                            $.each(opts, function( index, view ) {
                                viewpicker.append($('<option></option>').attr('value',index).text(view));
                            });

                            viewpicker.removeAttr('disabled').attr('size',viewpicker.find('option').length);

                            if (is_edit) {
                                viewpicker.val(current_view);
                                EXPONENT.handleViewChange();
                            }
                        }
                    });
                };
            }

            if (!is_edit) {
                // console.log("this");
                modpicker.removeAttr('disabled');
            } else {
                //set the current module
                EXPONENT.setCurMod();
                if (EXPONENT.isController()) {
                    EXPONENT.writeActions();
                } else {
                    EXPONENT.writeViews();
                }
            };


        });
        {/literal}
        {/script}
    {/if}

</div>