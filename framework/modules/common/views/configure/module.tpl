{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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


{css unique="addmodule1" link=$smarty.const.PATH_RELATIVE|cat:'framework/modules/container/assets/css/add-content.css' corecss="admin-global"}

{/css}

<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Module Settings"|gettext) module="files"}
        </div>
        <h2>{if $hcview}{'Hard-coded'|gettext} {/if}{"Module Settings"|gettext}</h2>
    </div>
</div>

{control type=hidden name=container_id value=$container->id}
{*{control type=hidden name=existing_source value=$container->internal->src}*}
{*{control type=hidden name=rank value=$container->rank}*}
{*{control type=hidden name=src value=$loc->src}*}
{*{control type=hidden name=int value=$loc->int}*}
{*{control type=hidden name=current_section value=$current_section}*}

{if $hcview}
    {*{control type=hidden name=hcview value=1}*}
    {*{control type=text size=31 label="Module Title"|gettext name="moduletitle" value=$moduletitle caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext}*}
{else}
    {control type=text size=31 label="Module Title"|gettext name="moduletitle" value=$container->title caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext}
{/if}
{control type="checkbox" name="hidemoduletitle" label="Hide Module Title?"|gettext value=1 checked=$config.hidemoduletitle}

{if !$hcview}
{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}
{control type=dropdown id="actions" name=actions items=$actions value=$container->action label="Content Action"|gettext}
{control type=dropdown id="views" name=views items=$mod_views value=$container->view label="Content Display"|gettext}
{$containerview = $container->view}
{else}
    {$containerview = $hcview}
{/if}
{group label='View Configuration Settings'|gettext}
    <div id="moduleViewConfig">
        {$themefileview="`$smarty.const.THEME_ABSOLUTE`modules/`$relative_viewpath`/configure/`$containerview`.config"}
        {$modulefileview="`$viewpath`/configure/`$containerview`.config"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {elseif file_exists($modulefileview)}
            {include file=$modulefileview}
        {else}
            <p>{'There Are No View Specific Settings'|gettext}</p>
        {/if}
    </div>
{/group}
{control type="html" name="moduledescription" label="Module Description"|gettext value=$config.moduledescription}

{if !$hcview}
{*FIXME convert to yui3*}
{script unique="edit-module" yui3mods=1}
{literal}

    YUI(EXPONENT.YUI_CONFIG).use("node", "event", "node-event-delegate", "io", "yui2-yahoo-dom-event", "yui2-connection", "yui2-json", function (Y) {
        var YAHOO = Y.YUI2;
//        var osmv = {/literal}{$json_obj};{literal} //oldschool module views (in a JSON object)
        var modpicker = Y.one('#modcntrol'); // the module selection dropdown
        var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
        var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view
        var actionpicker = Y.one('#actions'); // the actions dropdown
        var viewpicker = Y.one('#views'); // the views dropdown

        // handles the action picker change
        EXPONENT.handleActionChange = function () {
            EXPONENT.setCurAction();
            if (actionpicker.get("value") != -1) {
                EXPONENT.writeViews();
//            } else {
//                EXPONENT.resetViews();
            }
        }

        //listens for a change in the action dropdown
        Y.one('#config').delegate('change', EXPONENT.handleActionChange, "#actions");

        // handles view picker changes
//        EXPONENT.handleViewChange = function (e) {
//            if (viewpicker.get("value") != -1) {
    //                EXPONENT.enableSave();
//            } else {
    //                EXPONENT.disableSave();
//            }
//        }

        //listens for a change in the view dropdown
//        Y.one('#config').delegate('change', EXPONENT.handleViewChange, "#views");

        //resets both the viewpicker and actionpicker
//        EXPONENT.resetActionsViews = function () {
//            EXPONENT.resetViews();
//            EXPONENT.resetActions();
//        }

        //resets the actionpicker to the default when entering this page
//        EXPONENT.resetActions = function () {
//            var actionDefaultOption = Y.Node.create('<option value="0">{/literal}{"No Module Selected"|gettext}{literal}</option>');
//            actionpicker.appendChild(actionDefaultOption);
    //            actionpicker.set('disabled',1);
    //            actionpicker.ancestor('div.control').addClass('disabled');
//        }

        //resets the viewpicker to the default when entering this page
        EXPONENT.resetViews = function () {
            var viewDefaultOption = Y.Node.create('<option value="0">{/literal}{"No Action Selected"|gettext}{literal}</option>');
            viewpicker.appendChild(viewDefaultOption);
    //            viewpicker.set('disabled',1);
    //            viewpicker.ancestor('div.control').addClass('disabled');
        }

        //finds the currently selected action for the given module
        EXPONENT.setCurAction = function () {
            var selectmenu = YAHOO.util.Dom.get('actions');
            EXPONENT.curAction = selectmenu.options[selectmenu.selectedIndex].value;
        };

        EXPONENT.writeViews = function () {
            var actionpicker = Y.one('#actions'); // the actions dropdown
//            Y.one('#moduleViewConfig').setContent(Y.Node.create('<div class="msg-queue error" style="text-align:center"><p>{/literal}{"You Must Select a View!"|gettext}{literal}</p></div>'));
    //            viewpicker.removeAttribute('disabled');
            var uri = EXPONENT.PATH_RELATIVE + 'index.php'
            YAHOO.util.Connect.asyncRequest('POST', uri,
                {success: function (o) {
                    var viewpicker = Y.one('#views'); // the views dropdown
                    var opts = YAHOO.lang.JSON.parse(o.responseText);
                    viewpicker.set('innerHTML', '');
                    el = Y.Node.create('<option value="0">{/literal}{"Select a View"|gettext}{literal}</option>');
//                    viewpicker.appendChild(el);
                    for (var view in opts) {
                        el = document.createElement('option');
                        el.appendChild(document.createTextNode(opts[view]));
                        el.setAttribute('value', view);
                        viewpicker.appendChild(el);
                    }
                    for (var view in opts) {
                        if (view == current_view) {
                            viewpicker.set('value',current_view);
                        }
                    }
    var tmp = viewpicker.get('value');
    cfg.data = "view=" + viewpicker.get('value');
    var request = Y.io(sUrl, cfg);
    Y.one('#moduleViewConfig').setContent(Y.Node.create('<div id="loadingview" class="loadingdiv" style="width:40%">{/literal}{"Loading Form"|gettext}{literal}</div>'));
//                    EXPONENT.handleViewChange();

                }}, 'module=containermodule&action=getactionviews&ajax_action=1&mod={/literal}{$container->internal->mod}{literal}&act=' + actionpicker.get('value') + '&actname=' + actionpicker.get('value')
            );
        }

        //set the current module
//        EXPONENT.curMod = '{/literal}{$container->internal->mod}{literal}';

        var cfg = {
            method: "POST",
            headers: { 'X-Transaction': 'Load File Config'},
            arguments: { 'X-Transaction': 'Load File Config'}
        };

        var sUrl = EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_module_view_config&ajax_action=1&mod={/literal}{$classname}{literal}";

        var handleSuccess = function (ioId, o) {
            Y.log(o.responseText);
            Y.log("The success handler was called.  Id: " + ioId + ".", "info", "example");

            if (o.responseText) {
                Y.one('#moduleViewConfig').setContent(o.responseText);
                Y.one('#moduleViewConfig').all('script').each(function (n) {
                    if (!n.get('src')) {
                        eval(n.get('innerHTML'));
                    } else {
                        var url = n.get('src');
                        if (url.indexOf("ckeditor")) {
                            Y.Get.script(url);
                        }
                    }
                });
                Y.one('#moduleViewConfig').all('link').each(function (n) {
                    var url = n.get('href');
                    Y.Get.css(url);
                });
            } else {
                Y.one('#moduleViewConfig #loadingview').remove();
            }
        };

        //A function handler to use for failed requests:
        var handleFailure = function (ioId, o) {
            Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "example");
        };

        //Subscribe our handlers to IO's global custom events:
        Y.on('io:success', handleSuccess);
        Y.on('io:failure', handleFailure);

        Y.one('#views').on('change', function (e) {
            e.halt();
            if (e.target.get('value') != 0) {
                cfg.data = "view=" + e.target.get('value');
                var request = Y.io(sUrl, cfg);
                Y.one('#moduleViewConfig').setContent(Y.Node.create('<div id="loadingview" class="loadingdiv" style="width:40%">{/literal}{"Loading Form"|gettext}{literal}</div>'));
            } else {
//                Y.one('#moduleViewConfig').setContent(Y.Node.create('<div class="msg-queue error" style="text-align:center"><p>{/literal}{"You Must Select a View!"|gettext}{literal}</p></div>'));
            }
        });
    });

{/literal}
{/script}
{/if}