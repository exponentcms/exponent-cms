{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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
            {help text="Get Help with"|gettext|cat:" "|cat:("Module Settings"|gettext) module="module-settings"}
        </div>
        <h2>{if $hcview}{'Hard-coded'|gettext} {/if}{"Module Settings"|gettext}</h2>
    </div>
</div>

{control type=hidden name=container_id value=$container->id}

{if ($container->internal->mod != 'container')}
    {control type=text size=31 label="Module Title"|gettext name="moduletitle" value=$container->title caption="Module Title"|gettext required=true description='The module title is used to help the user identify this module.'|gettext focus=1}
    {control type=radiogroup columns=6 name="heading_level" label='Module Title Heading Level'|gettext items="1,2,3,4,5,6"|gettxtlist values="h1,h2,h3,h4,h5,h6" default=$config.heading_level|default:'h1'}
    {control type=radiogroup columns=6 name="item_level" label='Item Title Heading Level'|gettext items="1,2,3,4,5,6"|gettxtlist values="h1,h2,h3,h4,h5,h6" default=$config.item_level|default:'h2'}
    {if $smarty.const.INVERT_HIDE_TITLE}
        {$title_str = 'Show Module Title?'|gettext}
        {$desc_str = 'The Module Title is hidden by default.'|gettext}
    {else}
        {$title_str = 'Hide Module Title?'|gettext}
        {$desc_str = 'The Module Title is displayed by default.'|gettext}
    {/if}
    {control type="checkbox" name="hidemoduletitle" label=$title_str value=1 checked=$config.hidemoduletitle description=$desc_str}
{/if}
{control type="checkbox" name="is_private" label='Hide Module?'|gettext value=1 checked=$container->is_private description='Should this module be hidden from users without a view permission?'|gettext}
{control type=hidden id="modcntrol" name=modcntrol value=$container->internal->mod}
{if !$hcview}
    {control type=dropdown id="actions" name=actions items=$actions value=$container->action label="Content Action"|gettext}
    {control type=dropdown id="views" name=views items=$mod_views value=$container->view label="Content Display"|gettext}
    {$containerview = $container->view}
{else}
    {control type=hidden id="actions" name=actions value=$container->action}
    {control type=hidden id="views" name=views value=$container->view}
    {$containerview = $hcview}
{/if}
{group label='Display Specific Configuration Settings'|gettext}
    <div id="moduleViewConfig">
        {$themefileview="`$smarty.const.THEME_ABSOLUTE`modules/`$relative_viewpath`/configure/`$containerview`.config"}
        {$modulefileview="`$viewpath`/configure/`$containerview`.config"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {elseif file_exists($modulefileview)}
            {include file=$modulefileview}
        {else}
            <p>{'There Are No Display Specific Settings'|gettext}</p>
        {/if}
    </div>
{/group}
{if ($container->internal->mod != 'container')}
    {control type="html" name="moduledescription" label="Module Description"|gettext value=$config.moduledescription}
{/if}

{if !$hcview}
{script unique="edit-module" yui3mods="node,event,node-event-delegate,io,json-parse"}
{literal}
    YUI(EXPONENT.YUI_CONFIG).use('*', function (Y) {
        var modpicker = Y.one('#modcntrol'); // the module selection dropdown
        var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
        var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view
        var actionpicker = Y.one('#actions'); // the actions dropdown
        var viewpicker = Y.one('#views'); // the views dropdown

        // handles the action picker change
        EXPONENT.handleActionChange = function () {
//            EXPONENT.setCurAction();
            if (actionpicker.get("value") != -1) {
                EXPONENT.writeViews();
            }
        }

        //listens for a change in the action dropdown
        Y.one('#config').delegate('change', EXPONENT.handleActionChange, "#actions");

        //resets the viewpicker to the default when entering this page
        EXPONENT.resetViews = function () {
            var viewDefaultOption = Y.Node.create('<option value="0">{/literal}{"No Action Selected"|gettext}{literal}</option>');
            viewpicker.appendChild(viewDefaultOption);
        }

        var handleSuccessAction = function (ioId, o) {
            var viewpicker = Y.one('#views'); // the views dropdown
            var opts = Y.JSON.parse(o.responseText);
            viewpicker.set('innerHTML', '');
            el = Y.Node.create('<option value="0">{/literal}{"Select a View"|gettext}{literal}</option>');
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
            var sUrl = EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_module_view_config&ajax_action=1&mod={/literal}{$controller}{literal}";
            var cfg = {
                data : "view=" + viewpicker.get('value'),
                on: {
                    success: handleSuccessView,
                    failure: handleFailure
                }
            };
            var request = Y.io(sUrl, cfg);
            Y.one('#moduleViewConfig').setContent(Y.Node.create('{/literal}{loading title="Loading Form"|gettext}{literal}'));
        }

        EXPONENT.writeViews = function () {
            var actionpicker = Y.one('#actions'); // the actions dropdown
//            Y.one('#moduleViewConfig').setContent(Y.Node.create('<div class="msg-queue error" style="text-align:center"><p>{/literal}{"You Must Select a View!"|gettext}{literal}</p></div>'));
    //            viewpicker.removeAttribute('disabled');
            var uri = EXPONENT.PATH_RELATIVE + 'index.php?controller=container&action=getactionviews&ajax_action=1&mod={/literal}{$container->internal->mod}{literal}'
            var cfg = {
                data : 'act=' + actionpicker.get('value') + '&actname=' + actionpicker.get('value'),
                on: {
                    success: handleSuccessAction,
                    failure: handleFailure
                }
            };
            var request = Y.io(uri, cfg);
        }

        var handleSuccessView = function (ioId, o) {
            if (o.responseText) {
                Y.one('#moduleViewConfig').setContent(o.responseText);
                Y.one('#moduleViewConfig').all('script').each(function (n) {
                    if (!n.get('src')) {
                        eval(n.get('innerHTML'));
                    } else {
                        var url = n.get('src');
                        Y.Get.script(url);
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
//        Y.on('io:success', handleSuccess);
//        Y.on('io:failure', handleFailure);

        Y.one('#views').on('change', function (e) {
            e.halt();
            if (e.target.get('value') != 0) {
                var sUrl = EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_module_view_config&ajax_action=1&mod={/literal}{$controller}{literal}";
                var cfg = {
                    data: "view=" + e.target.get('value'),
                    on: {
                        success: handleSuccessView,
                        failure: handleFailure
                    }
                };
                var request = Y.io(sUrl, cfg);
                Y.one('#moduleViewConfig').setContent(Y.Node.create('{/literal}{loading title="Loading Form"|gettext}{literal}'));
//            } else {
//                Y.one('#moduleViewConfig').setContent(Y.Node.create('<div class="msg-queue error" style="text-align:center"><p>{/literal}{"You Must Select a View!"|gettext}{literal}</p></div>'));
            }
        });
    });
{/literal}
{/script}
{/if}