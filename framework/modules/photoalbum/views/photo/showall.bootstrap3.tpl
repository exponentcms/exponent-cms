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

{uniqueid prepend="gallery" assign="name"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

{$rel}
<div class="module photoalbum showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add Image"|gettext}
                {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
			{/if}
            {if $permissions.delete}
                {icon class=delete action=delete_multi title="Delete Many Images"|gettext text="Delete Multiple Images"|gettext onclick='null;'}
            {/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='photo' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
                {/if}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="photo" label="Images"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list">
        {exp_include file='photolist.tpl'}
    </div>
</div>

{if $config.lightbox}
{script unique="shadowbox-`$__loc->src`" jquery='jquery.colorbox'}
{literal}
    $('a.colorbox').colorbox({
        href: $(this).href,
        ref: $(this).rel,
        photo: true,
        maxWidth: "100%",
        close:'<i class="fa fa-close" aria-label="close modal"></i>',
        previous:'<i class="fa fa-chevron-left" aria-label="previous photo"></i>',
        next:'<i class="fa fa-chevron-right" aria-label="next photo"></i>',
    });
{/literal}
{/script}
{/if}

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var photolist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'photo', 'action' => 'showall', 'src' => $params.src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=photo&action=showall&view=photolist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new photos
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                photolist_{/literal}{$name}{literal}.html(o);
                photolist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                photolist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                photolist_{/literal}{$name}{literal}.html('Unable to load content');
                photolist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        photolist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Photos'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new photos
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Photos'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // photolist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Photos"|gettext}{literal}'));
            photolist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Photos"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new photos
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Photos'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // photolist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Photos"|gettext}{literal}'));
                photolist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Photos"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
