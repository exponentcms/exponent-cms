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

{css unique="edit-container" link="`$asset_path`css/add-content-bootstrap.css" corecss="admin-global"}

{/css}

<div class="exp-container edit{if !$error} hide{/if}">
    <div class="info-header">
        <div class="related-actions">
            {if $user->isSuperAdmin()}
                {icon class="managemodules" module=expModule action=manage text="Manage Active Modules"|gettext}
            {/if}
            {help text="Get Help with"|gettext|cat:" "|cat:("Adding Page Content"|gettext) module="adding-modules-to-a-page"}
        </div>
        <h2>{if $is_edit}{'Edit Module'|gettext}{else}{'Add New Content'|gettext}{/if}</h2>
    </div>
    
    {if $error}
    	{message class=error text=$error}
</div>
    {else}
        {form action=update}
            {if $is_edit}
                {control type=hidden name=id value=$container->id}
                {control type=hidden name=existing_source value=$container->internal->src}
            {/if}
            {control type=hidden name=rank value=$container->rank}
            {*{control type=hidden name=module value=containermodule}*}
            {*{control type=hidden name=src value=$loc->src}*}
            {*{control type=hidden name=int value=$loc->int}*}
            {control type=hidden name=rerank value=$rerank}
            {control type=hidden name=current_section value=$current_section}

            {*{control type=text size=31 label="Module Title"|gettext name="title" value=$container->title}*}
            {control type=text size=31 label="Module Title"|gettext name="title" value=$container->title caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext focus=1}

            {if $smarty.const.INVERT_HIDE_TITLE}
                {$title_str = 'Show Module Title?'|gettext}
                {$desc_str = 'The Module Title is hidden by default.'|gettext}
            {else}
                {$title_str = 'Hide Module Title?'|gettext}
                {$desc_str = 'The Module Title is displayed by default.'|gettext}
            {/if}
            {control type="checkbox" name="hidemoduletitle" label=$title_str value=1 checked=$config.hidemoduletitle description=$desc_str}

            {control type="checkbox" name="is_private" label='Hide Module?'|gettext value=1 checked=$container->is_private description='Should this module be hidden from users without a view permission?'|gettext}

            {control type=dropdown id="modcntrol" name=modcntrol items=$modules includeblank="Select a Module"|gettext label="Type of Content"|gettext disabled=1 value=$container->internal->mod}
            {if $is_edit}{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}{/if}

            {if $is_edit == 0}
                <div id="recyclebin" class="control">
                    <label>{'Recycle Bin'|gettext}</label>
                    {*<a id="browse-bin" class="btn" href="#" >{'Browse Recycled Content'|gettext}</a>*}
                    {icon name="browse-bin" class=trash action=scriptaction text='Browse Recycled Content'|gettext}
                    <input type="hidden" id="existing_source" name="existing_source" value="" />
                </div>
            {/if}

            {control type=dropdown id="actions" name=actions includeblank="No Module Selected"|gettext disabled=1 label="Content Action"|gettext}

            {control type=dropdown id="views" name=views includeblank="No Action Selected"|gettext disabled=1 label="Content Display"|gettext}

            {*control type=dropdown id=ctlview name=ctlview label=" "*}

            {control type=buttongroup submit="Save"|gettext disabled=1 cancel="Cancel"|gettext name="buttons"}
        {/form}
    </div>
    {if $is_edit}
        {*<div class="loadingdiv">{'Loading Module Configuration Form'|gettext}</div>*}
        {loading title='Loading Module Configuration Form'|gettext}
    {else}
        {*<div class="loadingdiv">{'Loading Module Creation Form'|gettext}</div>*}
        {loading title='Loading Module Creation Form'|gettext}
    {/if}
    {* src="$smarty.const.PATH_RELATIVE|cat:'js/ContainerSourceControl.js'" *}

    {script unique="addmodule" yui3mods="node,event,io,json-parse"}
    {literal}

    YUI(EXPONENT.YUI_CONFIG).use('*',function(Y){
        var modpicker = Y.one('#modcntrol'); // the module selection dropdown
        var is_edit = {/literal}{$is_edit}{literal} //are we editing?
        var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
        var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view
        var actionpicker = Y.one('#actions'); // the actions dropdown
        var viewpicker = Y.one('#views'); // the views dropdown
        var recyclebin = Y.one('#browse-bin'); // the recyclebin link
        //var recyclebinwrap = Y.one('#recyclebin'); // the recyclebin div

        recyclebin.addClass('disabled');
        // moving this func to here for now. Was in exponent.js.php, but this is the only place using it.
        EXPONENT.forms = {

            getSelectedRadio: function (formId, inputId){
                var oForm = this.grabForm(formId);
                for (var i=0; i<oForm.elements.length; i++){
                    oElement = oForm.elements[i];
                    oValue = oElement.value;

                    switch(oElement.type)
                    {
                        case 'radio':
                            if(oElement.checked && oElement.name==inputId){
                                return oValue;
                            }
                            break;
                    }
                }
                return "{/literal}{"no selected radios found"|gettext}{literal}";
            },
            setSelectedRadio: function (formId, inputId, rValue){
                var oForm = this.grabForm(formId);
                for (var i=0; i<oForm.elements.length; i++){
                    oElement = oForm.elements[i];
                    oValue = oElement.value;

                    switch(oElement.type)
                    {
                        case 'radio':
                            if(oElement.name==inputId && oElement.value==rValue){
                                oElement.checked = true;
                                return "{/literal}{"Radio"|gettext}{literal}"+" "+oElement.name+" "+"{/literal}{"set to value"|gettext}{literal}"+" "+oElement.value;
                            }
                            break;
                    }
                }
                return "{/literal}{"No value matching the one provided was found in this radio group"|gettext}{literal}";
            },
            getSelectValue: function (selectid) {
                var selectmenu = Y.one('#'+selectid);
                return selectmenu.get('value');
            },
            setSelectValue: function (selectid,setVal) {
                var selectmenu = Y.one('#'+selectid);
                return selectmenu.set('value',setVal);
            },
            grabForm: function (formId){
                var oForm;
                if(typeof formId == 'string'){
                    // Determine if the argument is a form id or a form name.
                    // Note form name usage is deprecated, but supported
                    // here for backward compatibility.
                    oForm = (document.getElementById(formId) || document.forms[formId]);
                }
                else if(typeof formId == 'object'){
                    // Treat argument as an HTML form object.
                    oForm = formId;
                }
                else{
                    return;
                }
                return oForm;
            }
        };

        //listens for a change in the module dropdown
        modpicker.on('change',function(e){
            EXPONENT.disableSave();
            EXPONENT.clearRecycledSource();
            if (modpicker.get("value")!='') {
                //set the current module
                EXPONENT.setCurMod();
                //enable recycle bin
                if (modpicker.get("value")!='' && modpicker.get("value")!='container') {
                EXPONENT.enableRecycleBin();
                } else {
                    EXPONENT.disableRecycleBin();
                }

                EXPONENT.writeActions();
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
            if (actionpicker.get("value")!='0') {
                EXPONENT.writeViews();
            }else{
                EXPONENT.resetViews();
            };
        }

        //listens for a change in the action dropdown
        actionpicker.on('change', EXPONENT.handleActionChange);

        // handles view picker changes
        EXPONENT.handleViewChange = function(e){
            if (viewpicker.get("value")!='0') {
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
            var viewDefaultOption = Y.Node.create('<option value="0">{/literal}{"No Action Selected"|gettext}{literal}</option>');
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

        //enables the save button once the view is selected
        EXPONENT.enableSave = function() {
            var svbtn = Y.one('#buttonsSubmit')
            svbtn.removeAttribute('disabled').removeClass('disabled');
            svbtn.ancestor('.buttongroup').removeClass('disabled');
        }

        //disables save button
        EXPONENT.disableSave = function() {
            Y.one('#buttonsSubmit').set('disabled',1).addClass('disabled').ancestor('.buttongroup').addClass('disabled');
        }

        //makes the recycle bin link clickable
        EXPONENT.enableRecycleBin = function() {
            recyclebin.on('click',EXPONENT.recyclebin);
            if ({/literal}{$user->is_acting_admin}{literal} && modpicker.get("value")!='container') {
                recyclebin.removeClass('disabled');
            } else {
                recyclebin.detach('click');
            }
        }

        //makes the recycle bin link clickable
        EXPONENT.disableRecycleBin = function() {
            recyclebin.detach('click');
            recyclebin.addClass('disabled');
        }

        //launches the recycle bin
        EXPONENT.recyclebin = function() {
            var mod = EXPONENT.curMod;
            var url = EXPONENT.PATH_RELATIVE+"index.php?controller=recyclebin&action=show&ajax_action=1&recymod="+mod;//+"&dest="+escape(dest)+"&vmod="+vmod+"&vview="+vview;
            window.open(url,'sourcePicker','title=no,resizable=yes,toolbar=no,width=900,height=750,scrollbars=yes');
        }

        //called from the recyclebin when a trashed item is selected for use
        EXPONENT.useRecycled = function(src) {
            var recycledSource = Y.one('#existing_source');
            recycledSource.set('value',src)
            recyclebin.addClass('btn-success');
            Y.all('#browse-bin > i').removeClass('icon-trash');
            Y.all('#browse-bin > i').addClass('icon-check');
        }

        //removes the source from the value of the hidden variable if the switch modules
        EXPONENT.clearRecycledSource = function() {
            var recycledSource = Y.one('#existing_source');
            recycledSource.set('value',"")
            recyclebin.removeClass('btn-success');
            Y.all('#browse-bin > i').addClass('icon-trash');
            Y.all('#browse-bin > i').removeClass('icon-check');
        }

        var handleSuccessAction = function(ioId, o) {
            var opts = Y.JSON.parse(o.responseText);
            actionpicker.set('innerHTML','');
            el = Y.Node.create('<option value="0">{/literal}{"Select an Action"|gettext}{literal}</option>');
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
        }

        //A function handler to use for failed requests:
        var handleFailure = function (ioId, o) {
            Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "example");
        };

        EXPONENT.writeActions = function() {
            actionpicker.set('disabled',1);
            EXPONENT.resetViews();
            var uri = EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getaction&ajax_action=1';
            var cfg = {
                data : 'mod=' + EXPONENT.curMod,
                on: {
                    success: handleSuccessAction,
                    failure: handleFailure
                }
            };
            var request = Y.io(uri, cfg);
        }

        var handleSuccessView = function(ioId, o) {
            var opts = Y.JSON.parse(o.responseText);
            viewpicker.set('innerHTML','');
            el = Y.Node.create('<option value="0">{/literal}{"Select a View"|gettext}{literal}</option>');
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
        }

        EXPONENT.writeViews = function() {
            viewpicker.removeAttribute('disabled');
            var uri = EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getactionviews&ajax_action=1'
            var cfg = {
                data : 'mod=' + EXPONENT.curMod + '&act=' + actionpicker.get('value') + '&actname=' + actionpicker.get('value'),
                on: {
                    success: handleSuccessView,
                    failure: handleFailure
                }
            };
            var request = Y.io(uri, cfg);
        }

        if (!is_edit) {
            modpicker.removeAttribute('disabled');
            modpicker.ancestor('div.control').removeClass('disabled');
        }else{
            //set the current module
            EXPONENT.setCurMod();
            EXPONENT.writeActions();
        };

        Y.one('.loadingdiv').setStyle('display','none');
        Y.one('.exp-container.hide').removeClass('hide');
    });
    {/literal}
    {/script}
{/if}
