{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="newpage" corecss="forms"}

{/css}

<div class="navigationmodule form-editContentPage"> 
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Editing Content Pages"|gettext) module="edit-content-page"}
        </div>
		<h1>{if $is_edit == 1}{'Edit Existing Content Page'|gettext}{else}{'Create New Content Page'|gettext}{/if}</h1>
	</div>
    <p>{if $is_edit == 1}{'Use the form below to change the details of this content page.'|gettext}{else}{'Use the form below to enter the information about your new content page.'|gettext}{/if}</p>
    {$form_html}
{script unique="configure" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
        var tabview = new Y.TabView({srcNode:'#configure-tabs'});
        tabview.render();
        Y.one('#configure-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
</div>
