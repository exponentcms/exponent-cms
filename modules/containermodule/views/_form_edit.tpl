{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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


{css unique="addmodule1" link="`$smarty.const.PATH_RELATIVE`framework/modules/container/assets/css/add-content.css" corecss="admin-global"}

{/css}

<div class="containermodule edit {if !$error}hide{/if}">
    <div class="info-header">
        <div class="related-actions">
            {if $user->is_admin}
                <a class="managemodules" href="{link module=expModule action=manage}">{"Manage Active Modules"|gettext}</a>
            {/if}
            {help text="Get Help with Adding Content" module="how-to-add-modules-to-a-page"}
        </div>
        <h1>{if $is_edit}Edit Module{else}Add New Content{/if}</h1>
    </div>
    
    {if $error}
    <div class="msg-queue error">
    	<div class="msg">{$error}</div>
    </div>
</div>
    {else}

    {form action=save}
    {if $is_edit}
        {control type=hidden name=id value=$container->id}
        {control type=hidden name=existing_source value=$container->internal->src}
    {/if}
    {control type=hidden name=rank value=$container->rank}
    {control type=hidden name=module value=containermodule}
    {control type=hidden name=src value=$loc->src}
    {control type=hidden name=int value=$loc->int}
    {control type=hidden name=rerank value=$rerank}
    {control type=hidden name=current_section value=$current_section}

    {control type=text size=31 label="Module Title" name=title value=$container->title}

    {control type=dropdown id="modcntrol" name=modcntrol items=$modules includeblank="Select a Module" label="Type of Content" disabled=1 value=$container->internal->mod}
    {if $is_edit}{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}{/if}

    {if $is_edit == 0}
    <div id="recyclebin" class="control disabled">
        <label>Recycle Bin</label>
        <a id="browse-bin" href="#" >Browse Recycled Content</a>
        <input type="hidden" id="existing_source" name="existing_source" value="" />
    </div>
    {/if}

    {control type=dropdown id="actions" name=actions includeblank="No Module Selected" disabled=1 label="Content Action"}

    {control type=dropdown id="views" name=views includeblank="No Action Selected" disabled=1 label="Content Display"}
    
    {*control type=dropdown id=ctlview name=ctlview label=" "*}
    
    {control type=buttongroup submit="Save" disabled=1 cancel="Cancel" name="buttons"}
    {/form}
</div>
<div class="loadingdiv">Loading Content Creation Form</div>
{* src="`$smarty.const.PATH_RELATIVE`js/ContainerSourceControl.js" *}

{script unique="addmodule" yui2mods="connection,json"}
{literal}



YUI(EXPONENT.YUI_CONFIG).use("node","event",function(Y){
    var osmv = {/literal}{$json_obj};{literal} //oldschool module views (in a JSON object)
    var modpicker = Y.one('#modcntrol'); // the module selection dropdown
    var is_edit = {/literal}{$is_edit}{literal} //are we editing?
    var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
    var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view 
    var actionpicker = Y.one('#actions'); // the actions dropdown
    var viewpicker = Y.one('#views'); // the views dropdown
    var recyclebin = Y.one('#browse-bin'); // the recyclebin link
    var recyclebinwrap = Y.one('#recyclebin'); // the recyclebin link
    
    //listens for a change in the module dropdown
    modpicker.on('change',function(e){
        EXPONENT.disableSave();
        EXPONENT.clearRecycledSource();
        if (modpicker.get("value")!=-1) {
            //set the current module
            EXPONENT.setCurMod();
            //enable recycle bin
            EXPONENT.enableRecycleBin();
            
            //decide what to do weather it's a controller or module
            if (EXPONENT.isController()) {
                EXPONENT.writeActions();
            } else {
                EXPONENT.writeViews();
            }
        }else{
            //else, they clicked back on "select a module", so we reset everything
            EXPONENT.diableRecycleBin();
			EXPONENT.resetActionsViews();
        };
    });
    
    // handles the action picker change
    EXPONENT.handleActionChange = function(){
        EXPONENT.disableSave();
        EXPONENT.setCurAction();
        if (actionpicker.get("value")!=-1) {
            EXPONENT.writeViews();
        }else{
            EXPONENT.resetViews();
        };
    }
    
    //listens for a change in the action dropdown
    actionpicker.on('change', EXPONENT.handleActionChange);
    
    // handles view picker changes
    EXPONENT.handleViewChange = function(e){
        if (viewpicker.get("value")!=-1) {
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
        var actionDefaultOption = Y.Node.create('<option value="0">No Module Selected</option>');
        actionpicker.appendChild(actionDefaultOption);
        actionpicker.set('disabled',1);
        actionpicker.ancestor('div.control').addClass('disabled');
    }

    //resets the viewpicker to the default when entering this page
    EXPONENT.resetViews = function() {
        var viewDefaultOption = Y.Node.create('<option value="0">No Action Selected</option>');
        viewpicker.appendChild(viewDefaultOption);
        viewpicker.set('disabled',1);
        viewpicker.ancestor('div.control').addClass('disabled');
        EXPONENT.disableSave();
    }

    //finds the currently selected module
    EXPONENT.setCurMod = function() {
        EXPONENT.curMod = EXPONENT.forms.getSelectValue('modcntrol');
    };
    //finds the currently selected action for the given module
    EXPONENT.setCurAction = function() {
        EXPONENT.curAction = EXPONENT.forms.getSelectValue('actions');
    };

    //decides if its a controller or old school module
    EXPONENT.isController = function(){
        if (EXPONENT.curMod.indexOf('Controller')!=-1) {
            return true;
        } else {
            return false;
        };
    }
    //enables the save button once the view is selected
    EXPONENT.enableSave = function() {
        var svbtn = Y.one('#buttonsSubmit')
        svbtn.removeAttribute('disabled');
        svbtn.ancestor('.buttongroup').removeClass('disabled');
    }
    
    //disables save button
    EXPONENT.disableSave = function() {
        Y.one('#buttonsSubmit').set('disabled',1).ancestor('.buttongroup').addClass('disabled');
    }
    
    
    //makes the recycle bin link clickable
    EXPONENT.enableRecycleBin = function() {
        recyclebin.on('click',EXPONENT.recyclebin);
        recyclebinwrap.removeClass('disabled');
    }
    
    //makes the recycle bin link clickable
    EXPONENT.diableRecycleBin = function() {
        recyclebin.detach('click');
        recyclebinwrap.addClass('disabled');
    }
    
    //launches the recycle bin
    EXPONENT.recyclebin = function() {
        var mod = EXPONENT.curMod;
        //console.debug(mod);
        var url = EXPONENT.URL_FULL+"index.php?controller=recyclebin&action=show&ajax_action=1&recymod="+mod;//+"&dest="+escape(dest)+"&vmod="+vmod+"&vview="+vview;
        //console.debug(url);
        window.open(url,'sourcePicker','title=no,resizable=yes,toolbar=no,width=900,height=750,scrollbars=yes');
    }

    //called from the recyclebin one a trashed item is selected for use
    EXPONENT.useRecycled = function(src) {
       var recycledSource = Y.one('#existing_source');
       recycledSource.set('value',src)
       recyclebinwrap.addClass('using-rb');
    }

    //removes the source from the value of the hidden variable if the switch modules
    EXPONENT.clearRecycledSource = function() {
        var recycledSource = Y.one('#existing_source');
        recycledSource.set('value',"")
        recyclebinwrap.removeClass('using-rb');
    }

    EXPONENT.writeActions = function() {
        if (EXPONENT.isController()) {
            actionpicker.set('disabled',1);
            EXPONENT.resetViews();
            var uri = EXPONENT.URL_FULL+'index.php';
            YAHOO.util.Connect.asyncRequest('POST', uri, 
                {success: function(o) {
                    var opts = YAHOO.lang.JSON.parse(o.responseText);
                    actionpicker.set('innerHTML','');
                    el = Y.Node.create('<option value="0">Select an Action</option>');
                    actionpicker.appendChild(el);

                    for(var action in opts) {
                        el = document.createElement('option');
                        el.appendChild(document.createTextNode(opts[action]));
                        el.setAttribute('value', action);
                        actionpicker.appendChild(el);
                    }
                    actionpicker.removeAttribute('disabled');
                    actionpicker.ancestor('div.control').removeClass('disabled');
                    if (is_edit) {
                        EXPONENT.forms.setSelectValue(actionpicker.get("id"),current_action);
                        EXPONENT.handleActionChange();
                    }
                }}, 'module=containermodule&action=getaction&ajax_action=1&mod=' + EXPONENT.curMod
            );
        } else {
            actionpicker.set('disabled',1).set('innerHTML','<option value="0">No actions for this module...</option>');
        };
    }

    EXPONENT.writeViews = function() {
        viewpicker.removeAttribute('disabled');
        if (EXPONENT.isController()) {
            var uri = EXPONENT.URL_FULL+'index.php'
            YAHOO.util.Connect.asyncRequest('POST', uri,
                {success: function(o) {
                    var opts = YAHOO.lang.JSON.parse(o.responseText);
                    viewpicker.set('innerHTML','');
                    el = Y.Node.create('<option value="0">Select a View</option>');
                    viewpicker.appendChild(el);
                    for(var view in opts) {
                            el = document.createElement('option');
                            el.appendChild(document.createTextNode(opts[view]));
                            el.setAttribute('value', view);
                            viewpicker.appendChild(el);
                    }
                    viewpicker.removeAttribute('disabled');
                    viewpicker.ancestor('div.control').removeClass('disabled');
                    if (is_edit) {
                        EXPONENT.forms.setSelectValue(viewpicker.get("id"),current_view);
                        EXPONENT.handleViewChange();
                    }
                    
                }}, 'module=containermodule&action=getactionviews&ajax_action=1&mod=' + EXPONENT.curMod + '&act=' + actionpicker.get('value') + '&actname=' + actionpicker.get('value')
            );
            
        } else {
            //set the actions drop to something a little more informational
            var nas = Y.Node.create('<option value="0">No actions for this module...</option>');
            actionpicker.appendChild(nas);
            actionpicker.ancestor('div.control').addClass('disabled');
            actionpicker.set('disabled',1);
            
            //load up the views dropdown with the legacy views for the oldschool mods
            viewpicker.set('innerHTML','');
            el = Y.Node.create('<option value="0">Select a View</option>');
            viewpicker.appendChild(el);
            for(var view in osmv[EXPONENT.curMod].views) {
                el = document.createElement('option');
                el.appendChild(document.createTextNode(view));
                el.setAttribute('value', view);
                viewpicker.appendChild(el);
            }
            viewpicker.ancestor('div.control').removeClass('disabled');
            viewpicker.removeAttribute('disabled');
            if (is_edit) {
                EXPONENT.forms.setSelectValue(viewpicker.get("id"),current_view);
                EXPONENT.handleViewChange();
            }
        };
    }

    if (!is_edit) {
        modpicker.removeAttribute('disabled');
        modpicker.ancestor('div.control').removeClass('disabled');
    }else{
        //set the current module
        EXPONENT.setCurMod();
        if (EXPONENT.isController()) {
            EXPONENT.writeActions();
        } else {
            EXPONENT.writeViews();
        }
    };
    
    Y.one('.loadingdiv').setStyle('display','none');
    Y.one('.containermodule.hide').removeClass('hide');
    
});


{/literal}
{/script}
{/if}