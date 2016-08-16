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


<div class="form_header">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Module Settings"|gettext) module="module-settings"}
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
        {$bstrap3modulefileview="`$viewpath`/configure/`$containerview`.bootstrap3.config"}
        {$bstrapmodulefileview="`$viewpath`/configure/`$containerview`.bootstrap.config"}
        {$modulefileview="`$viewpath`/configure/`$containerview`.config"}
        {if file_exists($themefileview)}
            {include file=$themefileview}
        {elseif file_exists($bstrap3modulefileview)}
            {include file=$bstrap3modulefileview}
        {elseif file_exists($bstrapmodulefileview)}
            {include file=$bstrapmodulefileview}
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
    {script unique="edit-module" jquery=1}
    {literal}
        $(document).ready(function() {
            var modpicker = $('#modcntrol'); // the module selection dropdown
            var current_action = {/literal}{if $container->action}"{$container->action}"{else}false{/if}{literal}; //Do we have an existing action
            var current_view = {/literal}{if $container->view}"{$container->view}"{else}false{/if}{literal}; //Do we have an existing view
            var actionpicker = $('#actions'); // the actions dropdown
            var viewpicker = $('#views'); // the views dropdown

            //listens for a change in the action dropdown
            actionpicker.on('change', function() {
                viewpicker.removeAttr('disabled');
                $.ajax({
                    headers: { 'X-Transaction': 'Getting Action Views'},
                    url: EXPONENT.PATH_RELATIVE+'index.php?controller=container&action=getactionviews&ajax_action=1&mod={/literal}{$container->internal->mod}{literal}' + '&act=' + actionpicker.val() + '&actname=' + actionpicker.val(),
                    success: function(o){
                        var opts = $.parseJSON(o);
                        viewpicker.empty();
                        el = $('<option value="0">{/literal}{"Select a View"|gettext}{literal}</option>');
                        viewpicker.append(el);

                        $.each(opts, function( index, view ) {
                            viewpicker.append($('<option></option>').attr('value',index).text(view));
                        });

                        viewpicker.removeAttr('disabled').attr('size',viewpicker.find('option').length);
                        viewpicker.val(0);

                        $.ajax({
                            headers: { 'X-Transaction': 'Getting View Configs'},
                            url: EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_module_view_config&ajax_action=1&mod={/literal}{$controller}{literal}&view=" + viewpicker.val(),
                            success: handleSuccessView
                        });
                        $('#moduleViewConfig').html($('{/literal}{loading title="Loading Form"|gettext}{literal}'));
                    }
                });
            });

            //listens for a change in the views dropdown
            viewpicker.on('change', function (e) {
                if (e.target.value != 0) {
                    $.ajax({
                        headers: { 'X-Transaction': 'Getting View Configs'},
                        url: EXPONENT.PATH_RELATIVE + "index.php?controller=file&action=get_module_view_config&ajax_action=1&mod={/literal}{$controller}{literal}&view=" + e.target.value,
                        success: handleSuccessView
                    });
                    $('#moduleViewConfig').html($('{/literal}{loading title="Loading Form"|gettext}{literal}'));
                }
            });

            //display configuration form for selected view
            var handleSuccessView = function (o, ioId) {
                if (o) {
                    $('#moduleViewConfig').html(o);
                    $('#moduleViewConfig script').each(function (k, n) {
                        if (!$(n).attr('src')) {
                            eval($(n).html);
                        } else {
                            var url = $(n).attr('src');
                            $.getScript(url);
                        }
                    });
                    $('#moduleViewConfig link').each(function (k, n) {
                        $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                    });
                } else {
                    $('#moduleViewConfig #loadingview').remove();
                }
            };
        });
    {/literal}
    {/script}
{/if}